import AppShell from "@/components/AppShell";
import { generateBills } from "@/app/actions";
import { requireUser } from "@/lib/auth";

export default async function GenerateBillsPage() {
  const user = await requireUser();
  return (
    <AppShell user={user} title="Generate Tagihan Bulanan">
      <form action={generateBills} className="card"><div className="card-body form-grid">
        <div className="field"><label>Periode</label><input name="period" type="month" defaultValue={new Date().toISOString().slice(0, 7)} required /></div>
        <div className="field"><label>Jatuh Tempo</label><input name="due_date" type="date" required /></div>
        <div className="field full"><button className="btn btn-primary">Generate</button></div>
      </div></form>
    </AppShell>
  );
}
