import { saveUser } from "@/app/actions";

export default function UserForm({ editedUser }: { editedUser?: any }) {
  return (
    <form action={saveUser} className="card"><div className="card-body form-grid">
      <input type="hidden" name="id" value={editedUser?.id ?? ""} />
      <div className="field"><label>Nama</label><input name="name" defaultValue={editedUser?.name ?? ""} required /></div>
      <div className="field"><label>Email</label><input name="email" type="email" defaultValue={editedUser?.email ?? ""} required /></div>
      <div className="field"><label>Role</label><select name="role" defaultValue={editedUser?.role ?? "receptionist"}><option value="owner">Pemilik</option><option value="receptionist">Receptionist</option></select></div>
      <div className="field"><label>Password</label><input name="password" type="password" required={!editedUser} /></div>
      <div className="field full"><button className="btn btn-primary">Simpan</button></div>
    </div></form>
  );
}
