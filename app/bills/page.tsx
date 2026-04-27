import Link from "next/link";
import { sql } from "@vercel/postgres";
import AppShell from "@/components/AppShell";
import { requireUser } from "@/lib/auth";
import { money } from "@/lib/format";

export default async function BillsPage() {
  const user = await requireUser();
  const { rows } = await sql`
    SELECT bills.*, customers.name AS customer_name, rooms.number AS room_number
    FROM bills JOIN customers ON customers.id = bills.customer_id JOIN rooms ON rooms.id = bills.room_id
    ORDER BY bills.period DESC, bills.created_at DESC
  `;
  return (
    <AppShell user={user} title="Tagihan" subtitle="Tagihan bulanan penghuni" action={<div className="toolbar"><Link className="btn" href="/bills/generate">Generate Bulanan</Link><Link className="btn btn-primary" href="/bills/new">Tambah Tagihan</Link></div>}>
      <div className="card table-wrap">
        <table>
          <thead><tr><th>Periode</th><th>Pelanggan</th><th>Kamar</th><th>Nominal</th><th>Status</th><th className="actions">Aksi</th></tr></thead>
          <tbody>
            {rows.map((bill: any) => (
              <tr key={bill.id}>
                <td>{String(bill.period).slice(0, 7)}</td><td>{bill.customer_name}</td><td>{bill.room_number}</td><td>{money(bill.amount)}</td>
                <td><span className={`badge ${bill.status === "paid" ? "success" : bill.status === "pending" ? "warning" : "danger"}`}>{bill.status}</span></td>
                <td className="actions"><Link className="btn btn-small" href={`/bills/${bill.id}`}>Edit</Link>{bill.status !== "paid" ? <Link className="btn btn-small btn-success" href={`/payments/new?bill=${bill.id}`}>Upload Bukti</Link> : null}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </AppShell>
  );
}
