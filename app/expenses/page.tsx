import Link from "next/link";
import { sql } from "@vercel/postgres";
import AppShell from "@/components/AppShell";
import { deleteExpense } from "@/app/actions";
import { requireUser } from "@/lib/auth";
import { money } from "@/lib/format";

export default async function ExpensesPage() {
  const user = await requireUser();
  const { rows } = await sql`SELECT * FROM expenses ORDER BY spent_at DESC, created_at DESC`;
  return (
    <AppShell user={user} title="Pengeluaran" subtitle="Biaya operasional kost" action={<Link className="btn btn-primary" href="/expenses/new">Tambah Pengeluaran</Link>}>
      <div className="card table-wrap">
        <table>
          <thead><tr><th>Tanggal</th><th>Kategori</th><th>Deskripsi</th><th>Nominal</th><th className="actions">Aksi</th></tr></thead>
          <tbody>{rows.map((expense: any) => <tr key={expense.id}><td>{String(expense.spent_at).slice(0, 10)}</td><td>{expense.category}</td><td>{expense.description ?? "-"}</td><td>{money(expense.amount)}</td><td className="actions"><Link className="btn btn-small" href={`/expenses/${expense.id}`}>Edit</Link><form action={deleteExpense} style={{ display: "inline" }}><input type="hidden" name="id" value={expense.id} /><button className="btn btn-small btn-danger">Hapus</button></form></td></tr>)}</tbody>
        </table>
      </div>
    </AppShell>
  );
}
