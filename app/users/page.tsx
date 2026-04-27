import Link from "next/link";
import { sql } from "@vercel/postgres";
import AppShell from "@/components/AppShell";
import { requireRole } from "@/lib/auth";

export default async function UsersPage() {
  const user = await requireRole("owner");
  const { rows } = await sql`SELECT id, name, email, role FROM users ORDER BY name`;
  return (
    <AppShell user={user} title="User" subtitle="Akun receptionist dan pemilik" action={<Link className="btn btn-primary" href="/users/new">Tambah User</Link>}>
      <div className="card table-wrap"><table><thead><tr><th>Nama</th><th>Email</th><th>Role</th><th className="actions">Aksi</th></tr></thead><tbody>{rows.map((u: any) => <tr key={u.id}><td>{u.name}</td><td>{u.email}</td><td><span className="badge">{u.role}</span></td><td className="actions"><Link className="btn btn-small" href={`/users/${u.id}`}>Edit</Link></td></tr>)}</tbody></table></div>
    </AppShell>
  );
}
