import { saveRoom } from "@/app/actions";

export default function RoomForm({ room }: { room?: any }) {
  return (
    <form action={saveRoom} className="card">
      <div className="card-body form-grid">
        <input type="hidden" name="id" value={room?.id ?? ""} />
        <div className="field">
          <label>Nomor Kamar</label>
          <input name="number" defaultValue={room?.number ?? ""} required />
        </div>
        <div className="field">
          <label>Harga Bulanan</label>
          <input name="monthly_price" type="number" min="1" defaultValue={room?.monthly_price ?? ""} required />
        </div>
        <div className="field full"><button className="btn btn-primary">Simpan</button></div>
      </div>
    </form>
  );
}
