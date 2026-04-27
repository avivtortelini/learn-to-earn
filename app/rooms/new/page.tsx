import AppShell from "@/components/AppShell";
import { requireUser } from "@/lib/auth";
import RoomForm from "../RoomForm";

export default async function NewRoomPage() {
  const user = await requireUser();
  return <AppShell user={user} title="Tambah Kamar"><RoomForm /></AppShell>;
}
