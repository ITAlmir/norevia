export function siteUrl(path = "") {
  const base = (import.meta.env.VITE_APP_URL || "").replace(/\/+$/, "");
  const p = String(path || "").startsWith("/") ? path : `/${path}`;
  return `${base}${p}`.replace(/\/+$/, "");
}

export function stripHtml(html = "") {
  return String(html).replace(/<[^>]*>?/gm, "").replace(/\s+/g, " ").trim();
}

export function clamp(text = "", max = 160) {
  const t = String(text || "").trim();
  if (t.length <= max) return t;
  return t.slice(0, max - 1).trim() + "…";
}

export function makeMeta({
  title,
  description,
  path,
  image,
  type = "website",
}) {
  const url = siteUrl(path);
  const desc = clamp(stripHtml(description || ""), 160);

  return {
    title,
    description: desc,
    canonical: url,
    og: {
      "og:title": title,
      "og:description": desc,
      "og:type": type,
      "og:url": url,
      ...(image ? { "og:image": image } : {}),
    },
    twitter: {
      "twitter:card": image ? "summary_large_image" : "summary",
      "twitter:title": title,
      "twitter:description": desc,
      ...(image ? { "twitter:image": image } : {}),
    },
  };
}
