import { initializeDatabase } from "@/app/actions";

export default function SetupPage() {
  return (
    <main className="login-page">
      <form action={initializeDatabase} className="card login-card">
        <div className="card-body stack">
          <h1 className="title">Setup Database</h1>
          <p className="muted">Buat tabel Postgres dan seed akun awal. Jalankan sekali setelah environment Vercel database tersambung.</p>
          <button className="btn btn-primary">Initialize</button>
        </div>
      </form>
    </main>
  );
}
