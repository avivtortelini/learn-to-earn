import { sql } from "@vercel/postgres";
import AppShell from "@/components/AppShell";
import { requireUser } from "@/lib/auth";
import CustomerForm from "../CustomerForm";

export default async function EditCustomerPage({ params }: { params: Promise<{ id: string }> }) {
  const user = await requireUser();
  const { id } = await params;
  const customer = await sql`
    SELECT customers.*, occupancies.room_id, occupancies.started_at
    FROM customers
    LEFT JOIN occupancies ON occupancies.customer_id = customers.id AND occupancies.ended_at IS NULL
    WHERE customers.id = ${Number(id)}
    LIMIT 1
  `;
  const rooms = await sql`
    SELECT rooms.*, occupancies.customer_id
    FROM rooms LEFT JOIN occupancies ON occupancies.room_id = rooms.id AND occupancies.ended_at IS NULL
    ORDER BY rooms.number
  `;
  return <AppShell user={user} title="Edit Pelanggan"><CustomerForm customer={customer.rows[0]} rooms={rooms.rows} /></AppShell>;
}
