import { loginAction } from "@/app/actions";
import { currentUser } from "@/lib/auth";
import { redirect } from "next/navigation";

export default async function LoginPage({ searchParams }: { searchParams: Promise<{ error?: string }> }) {
  if (await currentUser()) redirect("/");
  const params = await searchParams;
  return (
    <main className="login-page">
      <form action={loginAction} className="card login-card">
        <div className="card-body stack">
          <div>
            <h1 className="title">Manajemen Kost</h1>
            <p className="muted">Masuk sebagai receptionist atau pemilik.</p>
          </div>
          {params.error ? <div className="badge danger">Email atau password tidak sesuai</div> : null}
          <div className="field">
            <label>Email</label>
            <input name="email" type="email" required autoFocus />
          </div>
          <div className="field">
            <label>Password</label>
            <input name="password" type="password" required />
          </div>
          <button className="btn btn-primary">Login</button>
          <small className="muted">Seeder: pemilik@kost.local / receptionist@kost.local, password: password</small>
        </div>
      </form>
    </main>
  );
}
