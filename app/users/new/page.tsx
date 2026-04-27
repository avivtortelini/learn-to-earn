import AppShell from "@/components/AppShell";
import { requireRole } from "@/lib/auth";
import UserForm from "../UserForm";

export default async function NewUserPage() {
  const user = await requireRole("owner");
  return <AppShell user={user} title="Tambah User"><UserForm /></AppShell>;
}
