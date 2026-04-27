import { sql } from "@vercel/postgres";
import AppShell from "@/components/AppShell";
import { updateBill } from "@/app/actions";
import { requireUser } from "@/lib/auth";

export default async function EditBillPage({ params }: { params: Promise<{ id: string }> }) {
  const user = await requireUser();
  const { id } = await params;
  const { rows } = await sql`SELECT * FROM bills WHERE id = ${Number(id)} LIMIT 1`;
  const bill: any = rows[0];
  return (
    <AppShell user={user} title="Edit Tagihan">
      <form action={updateBill} className="card"><div className="card-body form-grid">
        <input type="hidden" name="id" value={bill.id} />
        <div className="field"><label>Nominal</label><input name="amount" type="number" defaultValue={bill.amount} required /></div>
        <div className="field"><label>Jatuh Tempo</label><input name="due_date" type="date" defaultValue={String(bill.due_date).slice(0, 10)} required /></div>
        <div className="field full"><label>Catatan</label><textarea name="notes" defaultValue={bill.notes ?? ""} /></div>
        <div className="field full"><button className="btn btn-primary">Simpan</button></div>
      </div></form>
    </AppShell>
  );
}
