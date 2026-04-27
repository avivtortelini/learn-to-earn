import crypto from "crypto";

const iterations = 120000;
const keyLength = 64;
const digest = "sha512";

export async function hashPassword(password: string) {
  const salt = crypto.randomBytes(16).toString("hex");
  const hash = await pbkdf2(password, salt);
  return `pbkdf2$${iterations}$${salt}$${hash}`;
}

export async function verifyPassword(password: string, stored: string) {
  const [scheme, rawIterations, salt, hash] = stored.split("$");
  if (scheme !== "pbkdf2" || !rawIterations || !salt || !hash) return false;
  const candidate = await pbkdf2(password, salt, Number(rawIterations));
  return crypto.timingSafeEqual(Buffer.from(candidate, "hex"), Buffer.from(hash, "hex"));
}

export function hashToken(token: string) {
  return crypto.createHash("sha256").update(token).digest("hex");
}

export function randomToken() {
  return crypto.randomBytes(32).toString("hex");
}

function pbkdf2(password: string, salt: string, count = iterations): Promise<string> {
  return new Promise((resolve, reject) => {
    crypto.pbkdf2(password, salt, count, keyLength, digest, (error, derivedKey) => {
      if (error) reject(error);
      else resolve(derivedKey.toString("hex"));
    });
  });
}
