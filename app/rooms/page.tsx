import Link from "next/link";
import { sql } from "@vercel/postgres";
import AppShell from "@/components/AppShell";
import { deleteRoom } from "@/app/actions";
import { requireUser } from "@/lib/auth";
import { money } from "@/lib/format";

export default async function RoomsPage() {
  const user = await requireUser();
  const { rows } = await sql`
    SELECT rooms.*, customers.name AS customer_name
    FROM rooms
    LEFT JOIN occupancies ON occupancies.room_id = rooms.id AND occupancies.ended_at IS NULL
    LEFT JOIN customers ON customers.id = occupancies.customer_id
    ORDER BY rooms.number
  `;
  return (
    <AppShell user={user} title="Kamar" subtitle="Data kamar dan status keterisian" action={<Link className="btn btn-primary" href="/rooms/new">Tambah Kamar</Link>}>
      <div className="card table-wrap">
        <table>
          <thead><tr><th>Nomor</th><th>Harga Bulanan</th><th>Status</th><th>Penghuni</th><th className="actions">Aksi</th></tr></thead>
          <tbody>
            {rows.map((room: any) => (
              <tr key={room.id}>
                <td><strong>{room.number}</strong></td>
                <td>{money(room.monthly_price)}</td>
                <td><span className={`badge ${room.customer_name ? "success" : ""}`}>{room.customer_name ? "Terisi" : "Kosong"}</span></td>
                <td>{room.customer_name ?? "-"}</td>
                <td className="actions">
                  <Link className="btn btn-small" href={`/rooms/${room.id}`}>Edit</Link>
                  <form action={deleteRoom} style={{ display: "inline" }}><input type="hidden" name="id" value={room.id} /><button className="btn btn-small btn-danger">Hapus</button></form>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </AppShell>
  );
}
