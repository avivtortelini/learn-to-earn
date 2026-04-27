import { sql } from "@vercel/postgres";
import AppShell from "@/components/AppShell";
import { uploadPayment } from "@/app/actions";
import { requireUser } from "@/lib/auth";

export default async function NewPaymentPage({ searchParams }: { searchParams: Promise<{ bill?: string }> }) {
  const user = await requireUser();
  const { bill } = await searchParams;
  const { rows } = await sql`
    SELECT bills.*, customers.name AS customer_name, rooms.number AS room_number
    FROM bills JOIN customers ON customers.id = bills.customer_id JOIN rooms ON rooms.id = bills.room_id
    WHERE bills.id = ${Number(bill)}
    LIMIT 1
  `;
  const row: any = rows[0];
  return (
    <AppShell user={user} title="Upload Bukti Bayar" subtitle={row ? `${row.customer_name} - ${row.room_number}` : ""}>
      <form action={uploadPayment} className="card"><div className="card-body form-grid">
        <input type="hidden" name="bill_id" value={row?.id ?? ""} />
        <div className="field"><label>Nominal</label><input name="amount" type="number" defaultValue={row?.amount ?? ""} required /></div>
        <div className="field"><label>Tanggal Bayar</label><input name="paid_at" type="date" defaultValue={new Date().toISOString().slice(0, 10)} required /></div>
        <div className="field"><label>Bukti Bayar</label><input name="proof" type="file" accept=".jpg,.jpeg,.png,.pdf" required /></div>
        <div className="field full"><label>Catatan</label><textarea name="notes" /></div>
        <div className="field full"><button className="btn btn-primary">Upload</button></div>
      </div></form>
    </AppShell>
  );
}
