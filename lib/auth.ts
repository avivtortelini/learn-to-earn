import { cookies } from "next/headers";
import { redirect } from "next/navigation";
import { sql } from "@vercel/postgres";
import type { Role, User } from "./db";
import { hashToken, randomToken } from "./security";

const cookieName = "kost_session";

export async function createSession(userId: number) {
  const token = randomToken();
  const tokenHash = hashToken(token);
  const expiresAt = new Date(Date.now() + 1000 * 60 * 60 * 24 * 7);

  await sql`
    INSERT INTO sessions (token_hash, user_id, expires_at)
    VALUES (${tokenHash}, ${userId}, ${expiresAt.toISOString()})
  `;

  (await cookies()).set(cookieName, token, {
    httpOnly: true,
    sameSite: "lax",
    secure: process.env.NODE_ENV === "production",
    expires: expiresAt,
    path: "/"
  });
}

export async function destroySession() {
  const token = (await cookies()).get(cookieName)?.value;
  if (token) {
    await sql`DELETE FROM sessions WHERE token_hash = ${hashToken(token)}`;
  }
  (await cookies()).delete(cookieName);
}

export async function currentUser(): Promise<User | null> {
  const token = (await cookies()).get(cookieName)?.value;
  if (!token) return null;

  const { rows } = await sql<User>`
    SELECT users.id, users.name, users.email, users.role
    FROM sessions
    JOIN users ON users.id = sessions.user_id
    WHERE sessions.token_hash = ${hashToken(token)}
      AND sessions.expires_at > NOW()
    LIMIT 1
  `;

  return rows[0] ?? null;
}

export async function requireUser() {
  const user = await currentUser();
  if (!user) redirect("/login");
  return user;
}

export async function requireRole(...roles: Role[]) {
  const user = await requireUser();
  if (!roles.includes(user.role)) redirect("/");
  return user;
}
