import { sql } from "@vercel/postgres";
import AppShell from "@/components/AppShell";
import { requireRole } from "@/lib/auth";
import UserForm from "../UserForm";

export default async function EditUserPage({ params }: { params: Promise<{ id: string }> }) {
  const user = await requireRole("owner");
  const { id } = await params;
  const { rows } = await sql`SELECT id, name, email, role FROM users WHERE id = ${Number(id)} LIMIT 1`;
  return <AppShell user={user} title="Edit User"><UserForm editedUser={rows[0]} /></AppShell>;
}
