export function money(value: number | string) {
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    maximumFractionDigits: 0
  }).format(Number(value || 0));
}

export function dateInput(value = new Date()) {
  const date = typeof value === "string" ? new Date(value) : value;
  return date.toISOString().slice(0, 10);
}

export function monthInput(value = new Date()) {
  const date = typeof value === "string" ? new Date(value) : value;
  return date.toISOString().slice(0, 7);
}

export function firstDayOfMonth(month: string) {
  return `${month}-01`;
}
