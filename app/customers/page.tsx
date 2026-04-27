import Link from "next/link";
import { sql } from "@vercel/postgres";
import AppShell from "@/components/AppShell";
import { deactivateCustomer } from "@/app/actions";
import { requireUser } from "@/lib/auth";

export default async function CustomersPage() {
  const user = await requireUser();
  const { rows } = await sql`
    SELECT customers.*, rooms.number AS room_number
    FROM customers
    LEFT JOIN occupancies ON occupancies.customer_id = customers.id AND occupancies.ended_at IS NULL
    LEFT JOIN rooms ON rooms.id = occupancies.room_id
    ORDER BY customers.created_at DESC
  `;
  return (
    <AppShell user={user} title="Pelanggan" subtitle="Data penghuni dan kamar aktif" action={<Link className="btn btn-primary" href="/customers/new">Tambah Pelanggan</Link>}>
      <div className="card table-wrap">
        <table>
          <thead><tr><th>Nama</th><th>Telepon</th><th>Kamar</th><th>Status</th><th className="actions">Aksi</th></tr></thead>
          <tbody>
            {rows.map((customer: any) => (
              <tr key={customer.id}>
                <td><strong>{customer.name}</strong></td>
                <td>{customer.phone ?? "-"}</td>
                <td>{customer.room_number ?? "-"}</td>
                <td><span className={`badge ${customer.status === "active" ? "success" : ""}`}>{customer.status}</span></td>
                <td className="actions">
                  <Link className="btn btn-small" href={`/customers/${customer.id}`}>Edit</Link>
                  {customer.status === "active" ? <form action={deactivateCustomer} style={{ display: "inline" }}><input type="hidden" name="id" value={customer.id} /><button className="btn btn-small">Nonaktifkan</button></form> : null}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </AppShell>
  );
}
