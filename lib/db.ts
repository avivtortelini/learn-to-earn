import { sql } from "@vercel/postgres";
import { hashPassword } from "./security";

export type Role = "owner" | "receptionist";
export type User = { id: number; name: string; email: string; role: Role };

export async function ensureSchema() {
  await sql`
    CREATE TABLE IF NOT EXISTS users (
      id SERIAL PRIMARY KEY,
      name TEXT NOT NULL,
      email TEXT NOT NULL UNIQUE,
      password_hash TEXT NOT NULL,
      role TEXT NOT NULL CHECK (role IN ('owner', 'receptionist')),
      created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
    )
  `;
  await sql`
    CREATE TABLE IF NOT EXISTS rooms (
      id SERIAL PRIMARY KEY,
      number TEXT NOT NULL UNIQUE,
      monthly_price INTEGER NOT NULL CHECK (monthly_price > 0),
      created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
    )
  `;
  await sql`
    CREATE TABLE IF NOT EXISTS customers (
      id SERIAL PRIMARY KEY,
      name TEXT NOT NULL,
      identity_number TEXT,
      phone TEXT,
      address TEXT,
      status TEXT NOT NULL DEFAULT 'active' CHECK (status IN ('active', 'inactive')),
      created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
    )
  `;
  await sql`
    CREATE TABLE IF NOT EXISTS occupancies (
      id SERIAL PRIMARY KEY,
      room_id INTEGER NOT NULL REFERENCES rooms(id) ON DELETE CASCADE,
      customer_id INTEGER NOT NULL REFERENCES customers(id) ON DELETE CASCADE,
      started_at DATE NOT NULL,
      ended_at DATE,
      created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
    )
  `;
  await sql`
    CREATE TABLE IF NOT EXISTS bills (
      id SERIAL PRIMARY KEY,
      customer_id INTEGER NOT NULL REFERENCES customers(id) ON DELETE CASCADE,
      room_id INTEGER NOT NULL REFERENCES rooms(id) ON DELETE CASCADE,
      period DATE NOT NULL,
      due_date DATE NOT NULL,
      amount INTEGER NOT NULL CHECK (amount > 0),
      status TEXT NOT NULL DEFAULT 'unpaid' CHECK (status IN ('unpaid', 'pending', 'paid')),
      notes TEXT,
      created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
      UNIQUE(customer_id, period)
    )
  `;
  await sql`
    CREATE TABLE IF NOT EXISTS payments (
      id SERIAL PRIMARY KEY,
      bill_id INTEGER NOT NULL REFERENCES bills(id) ON DELETE CASCADE,
      amount INTEGER NOT NULL CHECK (amount > 0),
      paid_at DATE NOT NULL,
      proof_url TEXT NOT NULL,
      status TEXT NOT NULL DEFAULT 'pending' CHECK (status IN ('pending', 'verified')),
      verified_by INTEGER REFERENCES users(id) ON DELETE SET NULL,
      verified_at TIMESTAMPTZ,
      notes TEXT,
      created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
    )
  `;
  await sql`
    CREATE TABLE IF NOT EXISTS expenses (
      id SERIAL PRIMARY KEY,
      category TEXT NOT NULL,
      description TEXT,
      amount INTEGER NOT NULL CHECK (amount > 0),
      spent_at DATE NOT NULL,
      created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
    )
  `;
  await sql`
    CREATE TABLE IF NOT EXISTS sessions (
      token_hash TEXT PRIMARY KEY,
      user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
      expires_at TIMESTAMPTZ NOT NULL,
      created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
    )
  `;
}

export async function seedInitialData() {
  const ownerHash = await hashPassword("password");
  const receptionistHash = await hashPassword("password");

  await sql`
    INSERT INTO users (name, email, password_hash, role)
    VALUES ('Pemilik Kost', 'pemilik@kost.local', ${ownerHash}, 'owner')
    ON CONFLICT (email) DO NOTHING
  `;
  await sql`
    INSERT INTO users (name, email, password_hash, role)
    VALUES ('Receptionist Kost', 'receptionist@kost.local', ${receptionistHash}, 'receptionist')
    ON CONFLICT (email) DO NOTHING
  `;

  for (let i = 1; i <= 10; i++) {
    await sql`
      INSERT INTO rooms (number, monthly_price)
      VALUES (${`A${String(i).padStart(2, "0")}`}, 750000)
      ON CONFLICT (number) DO NOTHING
    `;
  }
}

export async function setupDatabase() {
  await ensureSchema();
  await seedInitialData();
}
