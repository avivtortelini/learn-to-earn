import { sql } from "@vercel/postgres";
import AppShell from "@/components/AppShell";
import { requireUser } from "@/lib/auth";
import ExpenseForm from "../ExpenseForm";

export default async function EditExpensePage({ params }: { params: Promise<{ id: string }> }) {
  const user = await requireUser();
  const { id } = await params;
  const { rows } = await sql`SELECT * FROM expenses WHERE id = ${Number(id)} LIMIT 1`;
  return <AppShell user={user} title="Edit Pengeluaran"><ExpenseForm expense={rows[0]} /></AppShell>;
}
