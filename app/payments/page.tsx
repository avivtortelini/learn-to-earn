import { sql } from "@vercel/postgres";
import AppShell from "@/components/AppShell";
import { verifyPayment } from "@/app/actions";
import { requireUser } from "@/lib/auth";
import { money } from "@/lib/format";

export default async function PaymentsPage() {
  const user = await requireUser();
  const { rows } = await sql`
    SELECT payments.*, customers.name AS customer_name, rooms.number AS room_number
    FROM payments
    JOIN bills ON bills.id = payments.bill_id
    JOIN customers ON customers.id = bills.customer_id
    JOIN rooms ON rooms.id = bills.room_id
    ORDER BY payments.created_at DESC
  `;
  return (
    <AppShell user={user} title="Pembayaran" subtitle="Bukti bayar dan verifikasi">
      <div className="card table-wrap">
        <table>
          <thead><tr><th>Tanggal</th><th>Pelanggan</th><th>Kamar</th><th>Nominal</th><th>Bukti</th><th>Status</th><th className="actions">Aksi</th></tr></thead>
          <tbody>
            {rows.map((payment: any) => (
              <tr key={payment.id}>
                <td>{String(payment.paid_at).slice(0, 10)}</td><td>{payment.customer_name}</td><td>{payment.room_number}</td><td>{money(payment.amount)}</td>
                <td><a className="btn btn-small" href={payment.proof_url} target="_blank">Lihat</a></td>
                <td><span className={`badge ${payment.status === "verified" ? "success" : "warning"}`}>{payment.status}</span></td>
                <td className="actions">{payment.status === "pending" ? <form action={verifyPayment}><input type="hidden" name="id" value={payment.id} /><button className="btn btn-small btn-success">Verifikasi</button></form> : null}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </AppShell>
  );
}
