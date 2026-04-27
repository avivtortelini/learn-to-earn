import { saveCustomer } from "@/app/actions";

export default function CustomerForm({ customer, rooms }: { customer?: any; rooms: any[] }) {
  return (
    <form action={saveCustomer} className="card">
      <div className="card-body form-grid">
        <input type="hidden" name="id" value={customer?.id ?? ""} />
        <div className="field"><label>Nama</label><input name="name" defaultValue={customer?.name ?? ""} required /></div>
        <div className="field"><label>No Identitas</label><input name="identity_number" defaultValue={customer?.identity_number ?? ""} /></div>
        <div className="field"><label>Telepon</label><input name="phone" defaultValue={customer?.phone ?? ""} /></div>
        <div className="field">
          <label>Status</label>
          <select name="status" defaultValue={customer?.status ?? "active"}>
            <option value="active">Aktif</option>
            <option value="inactive">Nonaktif</option>
          </select>
        </div>
        <div className="field">
          <label>Kamar Aktif</label>
          <select name="room_id" defaultValue={customer?.room_id ?? ""}>
            <option value="">Tanpa kamar</option>
            {rooms.map((room) => (
              <option key={room.id} value={room.id} disabled={room.customer_id && room.customer_id !== customer?.id}>
                {room.number} - {room.monthly_price} {room.customer_id ? "(terisi)" : ""}
              </option>
            ))}
          </select>
        </div>
        <div className="field"><label>Tanggal Masuk</label><input name="started_at" type="date" defaultValue={customer?.started_at ?? new Date().toISOString().slice(0, 10)} /></div>
        <div className="field full"><label>Alamat</label><textarea name="address" defaultValue={customer?.address ?? ""} /></div>
        <div className="field full"><button className="btn btn-primary">Simpan</button></div>
      </div>
    </form>
  );
}
