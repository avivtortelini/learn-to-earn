import AppShell from "@/components/AppShell";
import { requireUser } from "@/lib/auth";
import ExpenseForm from "../ExpenseForm";

export default async function NewExpensePage() {
  const user = await requireUser();
  return <AppShell user={user} title="Tambah Pengeluaran"><ExpenseForm /></AppShell>;
}
