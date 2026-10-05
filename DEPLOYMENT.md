# Render and Vercel deployment

This project separates the PHP API (Render) from the static product-management
frontend (Vercel). The frontend talks to the backend through a same-origin
Vercel serverless proxy so the PHP session cookie stays first-party.

## Render backend

1. Push the backend source and `render.yaml` to the `backend` branch of
   `reyn-hue/manalo-reign-lavalust`.
2. In Render, create a Blueprint from that repository and select the `backend`
   branch. The blueprint builds the root `Dockerfile`.
3. Provide `DB_HOST`, `DB_USER`, `DB_PASSWORD`, and `DB_NAME` for an externally
   hosted MySQL database. Use the database and credentials from a trusted
   provider; Render does not provision MySQL with this blueprint. The schema
   uses MySQL features and must be created before the app can serve data.
4. After the service is deployed, open its Shell and run:

   ```sh
   php console/cli.php migration run
   ```

   Do not run the web migration routes in production; they are disabled when
   `APP_ENV=production`.
5. Copy the deployed service URL (for example, `https://your-service.onrender.com`).

The blueprint generates `APP_KEY` and enables secure session cookies. Keep all
database credentials and generated secrets in Render's environment settings,
never in source control.

## Vercel frontend

1. Import the same repository into Vercel and select the `frontend` branch.
2. Set the project Root Directory to `frontend`; no build command or output
   directory is needed.
3. Add the environment variable `BACKEND_URL` with the Render service URL,
   without a trailing slash. Redeploy after saving it.

The Vercel project serves `frontend/index.html` and forwards `/api/*` requests
to the Render service. The UI supports account signup/login and product
create/read/update/delete.

## Database and service notes

- Keep the Render web service and MySQL database on compatible network
  allowlists; the database must accept connections from Render.
- The Render free web service may sleep when idle. The first request after
  idle can take longer.
- PHP file-backed sessions are stored on the service instance. Redeploying or
  restarting the service can sign users out.
- Do not add `.env` to Git; `.env.example` is the safe template.
