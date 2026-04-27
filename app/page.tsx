import { sql } from "@vercel/postgres";
import AppShell from "@/components/AppShell";
import { requireUser } from "@/lib/auth";
import { money } from "@/lib/format";
import type { ReactNode } from "react";

export default async function DashboardPage() {
  const user = await requireUser();
  const stats = await sql<{
    total_rooms: number;
    occupied_rooms: number;
    unpaid_bills: number;
    income: number;
    expenses: number;
  }>`
    SELECT
      (SELECT COUNT(*)::int FROM rooms) AS total_rooms,
      (SELECT COUNT(*)::int FROM occupancies WHERE ended_at IS NULL) AS occupied_rooms,
      (SELECT COUNT(*)::int FROM bills WHERE status IN ('unpaid', 'pending')) AS unpaid_bills,
      COALESCE((SELECT SUM(amount)::int FROM payments WHERE status = 'verified' AND paid_at >= date_trunc('month', CURRENT_DATE)), 0) AS income,
      COALESCE((SELECT SUM(amount)::int FROM expenses WHERE spent_at >= date_trunc('month', CURRENT_DATE)), 0) AS expenses
  `;
  const row = stats.rows[0];
  const recent = await sql`
    SELECT payments.*, customers.name AS customer_name, rooms.number AS room_number
    FROM payments
    JOIN bills ON bills.id = payments.bill_id
    JOIN customers ON customers.id = bills.customer_id
    JOIN rooms ON rooms.id = bills.room_id
    ORDER BY payments.created_at DESC
    LIMIT 5
  `;

  return (
    <AppShell user={user} title="Dashboard" subtitle="Ringkasan operasional bulan ini">
      <div className="grid">
        <Stat label="Total Kamar" value={row.total_rooms} />
        <Stat label="Kamar Terisi" value={row.occupied_rooms} />
        <Stat label="Kamar Kosong" value={row.total_rooms - row.occupied_rooms} />
        <Stat label="Tagihan Belum Lunas" value={row.unpaid_bills} />
      </div>
      <div className="grid two" style={{ marginTop: 16 }}>
        <Stat label="Pemasukan Bulan Ini" value={money(row.income)} />
        <Stat label="Pengeluaran Bulan Ini" value={money(row.expenses)} />
      </div>
      <div className="card" style={{ marginTop: 18 }}>
        <div className="section-title">Pembayaran Terbaru</div>
        <div className="table-wrap">
          <table>
            <thead><tr><th>Pelanggan</th><th>Kamar</th><th>Nominal</th><th>Status</th><th>Tanggal</th></tr></thead>
            <tbody>
              {recent.rows.map((payment: any) => (
                <tr key={payment.id}>
                  <td>{payment.customer_name}</td>
                  <td>{payment.room_number}</td>
                  <td>{money(payment.amount)}</td>
                  <td><span className={`badge ${payment.status === "verified" ? "success" : "warning"}`}>{payment.status}</span></td>
                  <td>{String(payment.paid_at).slice(0, 10)}</td>
                </tr>
              ))}
              {recent.rows.length === 0 ? <tr><td colSpan={5} className="muted">Belum ada pembayaran.</td></tr> : null}
            </tbody>
          </table>
        </div>
      </div>
    </AppShell>
  );
}

function Stat({ label, value }: { label: string; value: ReactNode }) {
  return <div className="card"><div className="card-body"><div className="stat-label">{label}</div><div className="stat-value">{value}</div></div></div>;
}
