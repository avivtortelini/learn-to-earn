import Link from "next/link";
import { logoutAction } from "@/app/actions";
import type { User } from "@/lib/db";
import type { ReactNode } from "react";

export default function AppShell({
  user,
  title,
  subtitle,
  action,
  children
}: {
  user: User;
  title: string;
  subtitle?: string;
  action?: ReactNode;
  children: ReactNode;
}) {
  const nav = [
    ["/", "Dashboard"],
    ["/rooms", "Kamar"],
    ["/customers", "Pelanggan"],
    ["/bills", "Tagihan"],
    ["/payments", "Pembayaran"],
    ["/expenses", "Pengeluaran"],
    ...(user.role === "owner" ? [["/reports", "Laporan"], ["/users", "User"]] : [])
  ];

  return (
    <div className="app-shell">
      <aside className="sidebar">
        <div className="brand">Manajemen Kost</div>
        <nav className="nav">
          {nav.map(([href, label]) => (
            <Link key={href} href={href}>{label}</Link>
          ))}
        </nav>
        <form action={logoutAction} className="footer-user">
          <div>{user.name}</div>
          <div className="muted">{user.role}</div>
          <button className="btn" style={{ marginTop: 10, width: "100%" }}>Logout</button>
        </form>
      </aside>
      <main className="main">
        <div className="topbar">
          <div>
            <h1 className="title">{title}</h1>
            {subtitle ? <div className="subtitle">{subtitle}</div> : null}
          </div>
          {action}
        </div>
        {children}
      </main>
    </div>
  );
}
