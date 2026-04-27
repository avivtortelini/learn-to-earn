import type { Metadata } from "next";
import type { ReactNode } from "react";
import "./globals.css";

export const metadata: Metadata = {
  title: "Manajemen Kost",
  description: "Sistem informasi manajemen kamar kost"
};

export const dynamic = "force-dynamic";
export const runtime = "nodejs";

export default function RootLayout({ children }: { children: ReactNode }) {
  return (
    <html lang="id">
      <body>{children}</body>
    </html>
  );
}
