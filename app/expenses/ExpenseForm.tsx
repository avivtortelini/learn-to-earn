import { saveExpense } from "@/app/actions";

export default function ExpenseForm({ expense }: { expense?: any }) {
  return (
    <form action={saveExpense} className="card"><div className="card-body form-grid">
      <input type="hidden" name="id" value={expense?.id ?? ""} />
      <div className="field"><label>Tanggal</label><input name="spent_at" type="date" defaultValue={expense?.spent_at ? String(expense.spent_at).slice(0, 10) : new Date().toISOString().slice(0, 10)} required /></div>
      <div className="field"><label>Kategori</label><input name="category" defaultValue={expense?.category ?? ""} required /></div>
      <div className="field"><label>Nominal</label><input name="amount" type="number" defaultValue={expense?.amount ?? ""} required /></div>
      <div className="field full"><label>Deskripsi</label><textarea name="description" defaultValue={expense?.description ?? ""} /></div>
      <div className="field full"><button className="btn btn-primary">Simpan</button></div>
    </div></form>
  );
}
