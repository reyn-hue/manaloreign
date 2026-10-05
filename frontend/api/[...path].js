module.exports = async function apiProxy(req, res) {
  const backendValue = process.env.BACKEND_URL;
  if (!backendValue) {
    res.status(500).json({ success: false, error: "BACKEND_URL is not configured." });
    return;
  }

  let backend;
  try {
    backend = new URL(backendValue);
  } catch {
    res.status(500).json({ success: false, error: "BACKEND_URL must be a valid URL." });
    return;
  }

  if ((backend.protocol !== "https:" && process.env.NODE_ENV === "production")
    || backend.username
    || backend.password) {
    res.status(500).json({ success: false, error: "Use an HTTPS BACKEND_URL without embedded credentials in production." });
    return;
  }

  const incomingUrl = new URL(req.url, "https://vercel.invalid");
  const target = new URL(`${incomingUrl.pathname}${incomingUrl.search}`, backend);
  const headers = new Headers();

  for (const header of ["accept", "content-type", "cookie", "user-agent", "x-forwarded-for"]) {
    const value = req.headers[header];
    if (value) headers.set(header, Array.isArray(value) ? value.join(", ") : value);
  }

  let body;
  if (req.method !== "GET" && req.method !== "HEAD" && req.body !== undefined) {
    body = typeof req.body === "string" || Buffer.isBuffer(req.body)
      ? req.body
      : JSON.stringify(req.body);
  }

  try {
    const upstream = await fetch(target, {
      method: req.method,
      headers,
      body,
      redirect: "manual",
    });

    res.status(upstream.status);
    const contentType = upstream.headers.get("content-type");
    if (contentType) res.setHeader("content-type", contentType);

    const setCookies = typeof upstream.headers.getSetCookie === "function"
      ? upstream.headers.getSetCookie()
      : [];
    if (setCookies.length) res.setHeader("set-cookie", setCookies);
    else if (upstream.headers.get("set-cookie")) {
      res.setHeader("set-cookie", upstream.headers.get("set-cookie"));
    }

    res.setHeader("cache-control", "no-store");
    res.send(Buffer.from(await upstream.arrayBuffer()));
  } catch (error) {
    console.error("Backend proxy request failed:", error);
    res.status(502).json({ success: false, error: "The backend service could not be reached." });
  }
};
