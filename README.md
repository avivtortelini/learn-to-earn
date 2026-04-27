# Sistem Informasi Manajemen Kamar Kost

Next.js App Router application for managing kost rooms, customers, occupancy, monthly bills, payment proof uploads, expenses, finance reports, and internal users.

## Stack

- Next.js + React Server Components
- Vercel Postgres via `@vercel/postgres`
- Vercel Blob via `@vercel/blob` for payment proof files
- Cookie-based internal sessions with `owner` and `receptionist` roles

## Local Setup

```bash
npm install
cp .env.example .env.local
npm run dev
```

Set `POSTGRES_URL` to a Vercel Postgres-compatible connection string. Set `BLOB_READ_WRITE_TOKEN` from Vercel Blob before uploading payment proof files.

Open `/setup` once to create tables and seed initial data.

## Seeded Accounts

- Owner: `pemilik@kost.local` / `password`
- Receptionist: `receptionist@kost.local` / `password`

## Vercel Setup

1. Create/import the project on Vercel.
2. Add a Postgres database from Vercel Storage or Marketplace and connect it to the project.
3. Add a Vercel Blob store and expose `BLOB_READ_WRITE_TOKEN`.
4. Deploy, then visit `/setup` once.

The app reads database credentials from `POSTGRES_URL`. Payment proof uploads use Vercel Blob and require `BLOB_READ_WRITE_TOKEN`.
