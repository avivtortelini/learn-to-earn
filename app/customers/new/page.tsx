import { sql } from "@vercel/postgres";
import AppShell from "@/components/AppShell";
import { requireUser } from "@/lib/auth";
import CustomerForm from "../CustomerForm";

export default async function NewCustomerPage() {
  const user = await requireUser();
  const { rows } = await sql`
    SELECT rooms.*, occupancies.customer_id
    FROM rooms LEFT JOIN occupancies ON occupancies.room_id = rooms.id AND occupancies.ended_at IS NULL
    ORDER BY rooms.number
  `;
  return <AppShell user={user} title="Tambah Pelanggan"><CustomerForm rooms={rows} /></AppShell>;
}
