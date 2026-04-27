import { sql } from "@vercel/postgres";
import AppShell from "@/components/AppShell";
import { createBill } from "@/app/actions";
import { requireUser } from "@/lib/auth";

export default async function NewBillPage() {
  const user = await requireUser();
  const customers = await sql`
    SELECT customers.id, customers.name, rooms.number AS room_number
    FROM customers
    JOIN occupancies ON occupancies.customer_id = customers.id AND occupancies.ended_at IS NULL
    JOIN rooms ON rooms.id = occupancies.room_id
    WHERE customers.status = 'active'
    ORDER BY customers.name
  `;
  return (
    <AppShell user={user} title="Tambah Tagihan">
      <form action={createBill} className="card"><div className="card-body form-grid">
        <div className="field"><label>Pelanggan</label><select name="customer_id" required>{customers.rows.map((c: any) => <option key={c.id} value={c.id}>{c.name} - {c.room_number}</option>)}</select></div>
        <div className="field"><label>Periode</label><input name="period" type="month" defaultValue={new Date().toISOString().slice(0, 7)} required /></div>
        <div className="field"><label>Jatuh Tempo</label><input name="due_date" type="date" required /></div>
        <div className="field full"><label>Catatan</label><textarea name="notes" /></div>
        <div className="field full"><button className="btn btn-primary">Simpan</button></div>
      </div></form>
    </AppShell>
  );
}
