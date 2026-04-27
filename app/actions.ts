"use server";

import { put } from "@vercel/blob";
import { sql } from "@vercel/postgres";
import { revalidatePath } from "next/cache";
import { redirect } from "next/navigation";
import { createSession, destroySession, requireRole, requireUser } from "@/lib/auth";
import { setupDatabase } from "@/lib/db";
import { firstDayOfMonth } from "@/lib/format";
import { hashPassword, verifyPassword } from "@/lib/security";

function text(formData: FormData, key: string) {
  return String(formData.get(key) ?? "").trim();
}

function int(formData: FormData, key: string) {
  const value = Number(formData.get(key));
  if (!Number.isFinite(value) || value <= 0) throw new Error(`${key} tidak valid`);
  return value;
}

export async function initializeDatabase() {
  await setupDatabase();
  redirect("/login");
}

export async function loginAction(formData: FormData) {
  await setupDatabase();
  const email = text(formData, "email");
  const password = text(formData, "password");
  const { rows } = await sql<{ id: number; password_hash: string }>`
    SELECT id, password_hash FROM users WHERE email = ${email} LIMIT 1
  `;
  const user = rows[0];
  if (!user || !(await verifyPassword(password, user.password_hash))) {
    redirect("/login?error=1");
  }
  await createSession(user.id);
  redirect("/");
}

export async function logoutAction() {
  await destroySession();
  redirect("/login");
}

export async function saveRoom(formData: FormData) {
  await requireUser();
  const id = text(formData, "id");
  const number = text(formData, "number");
  const monthlyPrice = int(formData, "monthly_price");
  if (id) {
    await sql`UPDATE rooms SET number = ${number}, monthly_price = ${monthlyPrice} WHERE id = ${Number(id)}`;
  } else {
    await sql`INSERT INTO rooms (number, monthly_price) VALUES (${number}, ${monthlyPrice})`;
  }
  revalidatePath("/rooms");
  redirect("/rooms");
}

export async function deleteRoom(formData: FormData) {
  await requireUser();
  const id = int(formData, "id");
  const occupied = await sql`SELECT id FROM occupancies WHERE room_id = ${id} AND ended_at IS NULL LIMIT 1`;
  if (occupied.rowCount) redirect("/rooms?error=occupied");
  await sql`DELETE FROM rooms WHERE id = ${id}`;
  revalidatePath("/rooms");
}

export async function saveCustomer(formData: FormData) {
  await requireUser();
  const id = text(formData, "id");
  const roomId = text(formData, "room_id");
  const status = text(formData, "status") || "active";
  const payload = {
    name: text(formData, "name"),
    identity: text(formData, "identity_number") || null,
    phone: text(formData, "phone") || null,
    address: text(formData, "address") || null,
    startedAt: text(formData, "started_at") || new Date().toISOString().slice(0, 10)
  };

  let customerId = Number(id);
  if (id) {
    await sql`
      UPDATE customers
      SET name = ${payload.name}, identity_number = ${payload.identity}, phone = ${payload.phone},
          address = ${payload.address}, status = ${status}
      WHERE id = ${customerId}
    `;
    const active = await sql`SELECT id, room_id FROM occupancies WHERE customer_id = ${customerId} AND ended_at IS NULL LIMIT 1`;
    if (status === "inactive" || !roomId) {
      await sql`UPDATE occupancies SET ended_at = CURRENT_DATE WHERE customer_id = ${customerId} AND ended_at IS NULL`;
    } else if (!active.rows[0] || active.rows[0].room_id !== Number(roomId)) {
      const roomTaken = await sql`SELECT id FROM occupancies WHERE room_id = ${Number(roomId)} AND ended_at IS NULL LIMIT 1`;
      if (roomTaken.rowCount) throw new Error("Kamar sudah terisi");
      await sql`UPDATE occupancies SET ended_at = CURRENT_DATE WHERE customer_id = ${customerId} AND ended_at IS NULL`;
      await sql`INSERT INTO occupancies (customer_id, room_id, started_at) VALUES (${customerId}, ${Number(roomId)}, ${payload.startedAt})`;
    }
  } else {
    const inserted = await sql<{ id: number }>`
      INSERT INTO customers (name, identity_number, phone, address, status)
      VALUES (${payload.name}, ${payload.identity}, ${payload.phone}, ${payload.address}, ${status})
      RETURNING id
    `;
    customerId = inserted.rows[0].id;
    if (roomId && status === "active") {
      const roomTaken = await sql`SELECT id FROM occupancies WHERE room_id = ${Number(roomId)} AND ended_at IS NULL LIMIT 1`;
      if (roomTaken.rowCount) throw new Error("Kamar sudah terisi");
      await sql`INSERT INTO occupancies (customer_id, room_id, started_at) VALUES (${customerId}, ${Number(roomId)}, ${payload.startedAt})`;
    }
  }
  revalidatePath("/customers");
  redirect("/customers");
}

export async function deactivateCustomer(formData: FormData) {
  await requireUser();
  const id = int(formData, "id");
  await sql`UPDATE customers SET status = 'inactive' WHERE id = ${id}`;
  await sql`UPDATE occupancies SET ended_at = CURRENT_DATE WHERE customer_id = ${id} AND ended_at IS NULL`;
  revalidatePath("/customers");
}

export async function createBill(formData: FormData) {
  await requireUser();
  const customerId = int(formData, "customer_id");
  const period = firstDayOfMonth(text(formData, "period"));
  const dueDate = text(formData, "due_date");
  const notes = text(formData, "notes") || null;
  const active = await sql<{ room_id: number; monthly_price: number }>`
    SELECT occupancies.room_id, rooms.monthly_price
    FROM occupancies JOIN rooms ON rooms.id = occupancies.room_id
    WHERE occupancies.customer_id = ${customerId} AND occupancies.ended_at IS NULL
    LIMIT 1
  `;
  if (!active.rows[0]) throw new Error("Pelanggan belum punya kamar aktif");
  await sql`
    INSERT INTO bills (customer_id, room_id, period, due_date, amount, notes)
    VALUES (${customerId}, ${active.rows[0].room_id}, ${period}, ${dueDate}, ${active.rows[0].monthly_price}, ${notes})
    ON CONFLICT (customer_id, period) DO NOTHING
  `;
  revalidatePath("/bills");
  redirect("/bills");
}

export async function generateBills(formData: FormData) {
  await requireUser();
  const period = firstDayOfMonth(text(formData, "period"));
  const dueDate = text(formData, "due_date");
  await sql`
    INSERT INTO bills (customer_id, room_id, period, due_date, amount)
    SELECT customers.id, occupancies.room_id, ${period}, ${dueDate}, rooms.monthly_price
    FROM customers
    JOIN occupancies ON occupancies.customer_id = customers.id AND occupancies.ended_at IS NULL
    JOIN rooms ON rooms.id = occupancies.room_id
    WHERE customers.status = 'active'
    ON CONFLICT (customer_id, period) DO NOTHING
  `;
  revalidatePath("/bills");
  redirect("/bills");
}

export async function updateBill(formData: FormData) {
  await requireUser();
  await sql`
    UPDATE bills SET amount = ${int(formData, "amount")}, due_date = ${text(formData, "due_date")},
      notes = ${text(formData, "notes") || null}
    WHERE id = ${int(formData, "id")}
  `;
  revalidatePath("/bills");
  redirect("/bills");
}

export async function uploadPayment(formData: FormData) {
  await requireUser();
  const billId = int(formData, "bill_id");
  const file = formData.get("proof");
  if (!(file instanceof File) || file.size === 0) throw new Error("Bukti bayar wajib diunggah");
  if (!["image/jpeg", "image/png", "application/pdf"].includes(file.type)) throw new Error("Bukti harus JPG, PNG, atau PDF");
  if (file.size > 2 * 1024 * 1024) throw new Error("Ukuran bukti maksimal 2MB");
  const blob = await put(`payment-proofs/${Date.now()}-${file.name}`, file, { access: "public", addRandomSuffix: true });
  await sql`
    INSERT INTO payments (bill_id, amount, paid_at, proof_url, notes)
    VALUES (${billId}, ${int(formData, "amount")}, ${text(formData, "paid_at")}, ${blob.url}, ${text(formData, "notes") || null})
  `;
  await sql`UPDATE bills SET status = 'pending' WHERE id = ${billId}`;
  revalidatePath("/payments");
  redirect("/payments");
}

export async function verifyPayment(formData: FormData) {
  const user = await requireUser();
  const id = int(formData, "id");
  const payment = await sql<{ bill_id: number }>`SELECT bill_id FROM payments WHERE id = ${id} LIMIT 1`;
  if (!payment.rows[0]) return;
  await sql`UPDATE payments SET status = 'verified', verified_by = ${user.id}, verified_at = NOW() WHERE id = ${id}`;
  await sql`UPDATE bills SET status = 'paid' WHERE id = ${payment.rows[0].bill_id}`;
  revalidatePath("/payments");
  revalidatePath("/bills");
}

export async function saveExpense(formData: FormData) {
  await requireUser();
  const id = text(formData, "id");
  if (id) {
    await sql`
      UPDATE expenses SET category = ${text(formData, "category")}, description = ${text(formData, "description") || null},
        amount = ${int(formData, "amount")}, spent_at = ${text(formData, "spent_at")}
      WHERE id = ${Number(id)}
    `;
  } else {
    await sql`
      INSERT INTO expenses (category, description, amount, spent_at)
      VALUES (${text(formData, "category")}, ${text(formData, "description") || null}, ${int(formData, "amount")}, ${text(formData, "spent_at")})
    `;
  }
  revalidatePath("/expenses");
  redirect("/expenses");
}

export async function deleteExpense(formData: FormData) {
  await requireUser();
  await sql`DELETE FROM expenses WHERE id = ${int(formData, "id")}`;
  revalidatePath("/expenses");
}

export async function saveUser(formData: FormData) {
  await requireRole("owner");
  const id = text(formData, "id");
  const password = text(formData, "password");
  if (id) {
    await sql`UPDATE users SET name = ${text(formData, "name")}, email = ${text(formData, "email")}, role = ${text(formData, "role")} WHERE id = ${Number(id)}`;
    if (password) await sql`UPDATE users SET password_hash = ${await hashPassword(password)} WHERE id = ${Number(id)}`;
  } else {
    await sql`
      INSERT INTO users (name, email, role, password_hash)
      VALUES (${text(formData, "name")}, ${text(formData, "email")}, ${text(formData, "role")}, ${await hashPassword(password)})
    `;
  }
  revalidatePath("/users");
  redirect("/users");
}
