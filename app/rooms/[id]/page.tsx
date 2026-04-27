import { sql } from "@vercel/postgres";
import AppShell from "@/components/AppShell";
import { requireUser } from "@/lib/auth";
import RoomForm from "../RoomForm";

export default async function EditRoomPage({ params }: { params: Promise<{ id: string }> }) {
  const user = await requireUser();
  const { id } = await params;
  const { rows } = await sql`SELECT * FROM rooms WHERE id = ${Number(id)} LIMIT 1`;
  return <AppShell user={user} title="Edit Kamar"><RoomForm room={rows[0]} /></AppShell>;
}
