import { sql } from "@vercel/postgres";
import AppShell from "@/components/AppShell";
import { requireRole } from "@/lib/auth";
import { money } from "@/lib/format";
import type { ReactNode } from "react";

export default async function ReportsPage({ searchParams }: { searchParams: Promise<{ period?: string }> }) {
  const user = await requireRole("owner");
  const params = await searchParams;
  const period = `${params.period ?? new Date().toISOString().slice(0, 7)}-01`;
  const summary = await sql<{ income: number; expenses: number; arrears: number }>`
    SELECT
      COALESCE((SELECT SUM(amount)::int FROM payments WHERE status = 'verified' AND paid_at >= date_trunc('month', ${period}::date) AND paid_at < date_trunc('month', ${period}::date) + interval '1 month'), 0) AS income,
      COALESCE((SELECT SUM(amount)::int FROM expenses WHERE spent_at >= date_trunc('month', ${period}::date) AND spent_at < date_trunc('month', ${period}::date) + interval '1 month'), 0) AS expenses,
      COALESCE((SELECT SUM(amount)::int FROM bills WHERE status IN ('unpaid', 'pending') AND period <= ${period}::date), 0) AS arrears
  `;
  const s = summary.rows[0];
  const payments = await sql`
    SELECT payments.*, customers.name AS customer_name, rooms.number AS room_number
    FROM payments JOIN bills ON bills.id = payments.bill_id JOIN customers ON customers.id = bills.customer_id JOIN rooms ON rooms.id = bills.room_id
    WHERE payments.status = 'verified' AND paid_at >= date_trunc('month', ${period}::date) AND paid_at < date_trunc('month', ${period}::date) + interval '1 month'
    ORDER BY paid_at DESC
  `;
  const expenses = await sql`SELECT * FROM expenses WHERE spent_at >= date_trunc('month', ${period}::date) AND spent_at < date_trunc('month', ${period}::date) + interval '1 month' ORDER BY spent_at DESC`;

  return (
    <AppShell user={user} title="Laporan Keuangan" subtitle="Pemasukan, pengeluaran, dan tunggakan">
      <form className="toolbar"><input type="month" name="period" defaultValue={period.slice(0, 7)} /><button className="btn">Tampilkan</button></form>
      <div className="grid">
        <Stat label="Pemasukan" value={money(s.income)} />
        <Stat label="Pengeluaran" value={money(s.expenses)} />
        <Stat label="Saldo Bersih" value={money(s.income - s.expenses)} />
        <Stat label="Tunggakan" value={money(s.arrears)} />
      </div>
      <div className="card" style={{ marginTop: 18 }}>
        <div className="section-title">Pemasukan Terverifikasi</div>
        <table><thead><tr><th>Tanggal</th><th>Pelanggan</th><th>Kamar</th><th>Nominal</th></tr></thead><tbody>{payments.rows.map((p: any) => <tr key={p.id}><td>{String(p.paid_at).slice(0, 10)}</td><td>{p.customer_name}</td><td>{p.room_number}</td><td>{money(p.amount)}</td></tr>)}</tbody></table>
      </div>
      <div className="card" style={{ marginTop: 18 }}>
        <div className="section-title">Pengeluaran</div>
        <table><thead><tr><th>Tanggal</th><th>Kategori</th><th>Deskripsi</th><th>Nominal</th></tr></thead><tbody>{expenses.rows.map((e: any) => <tr key={e.id}><td>{String(e.spent_at).slice(0, 10)}</td><td>{e.category}</td><td>{e.description ?? "-"}</td><td>{money(e.amount)}</td></tr>)}</tbody></table>
      </div>
    </AppShell>
  );
}

function Stat({ label, value }: { label: string; value: ReactNode }) {
  return <div className="card"><div className="card-body"><div className="stat-label">{label}</div><div className="stat-value">{value}</div></div></div>;
}
