<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create account</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: grid;
            place-items: center;
            margin: 0;
            padding: 1.25rem;
            color: #1f2937;
            background: linear-gradient(135deg, #eff6ff, #f8fafc 55%, #eef2ff);
            font-family: Arial, sans-serif;
        }

        .auth-card {
            width: min(100%, 420px);
            padding: 2.25rem;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            box-shadow: 0 18px 45px rgb(15 23 42 / 10%);
        }

        .eyebrow {
            margin: 0 0 0.5rem;
            color: #2563eb;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        h1 {
            margin: 0;
            font-size: 1.8rem;
        }

        .intro {
            margin: 0.6rem 0 1.7rem;
            color: #6b7280;
            line-height: 1.5;
        }

        form {
            display: grid;
            gap: 1rem;
        }

        label {
            display: grid;
            gap: 0.45rem;
            font-size: 0.92rem;
            font-weight: 600;
        }

        input {
            width: 100%;
            padding: 0.78rem 0.85rem;
            color: #111827;
            background: #fff;
            border: 1px solid #d1d5db;
            border-radius: 7px;
            font: inherit;
        }

        input:focus {
            border-color: #2563eb;
            outline: 3px solid rgb(37 99 235 / 15%);
        }

        button {
            margin-top: 0.35rem;
            padding: 0.82rem 1rem;
            color: #fff;
            background: #2563eb;
            border: 0;
            border-radius: 7px;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
        }

        button:hover {
            background: #1d4ed8;
        }

        .auth-footer {
            margin: 1.5rem 0 0;
            color: #6b7280;
            font-size: 0.92rem;
            text-align: center;
        }

        a {
            color: #2563eb;
            font-weight: 600;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .auth-card {
                padding: 1.6rem;
            }
        }
    </style>
</head>
<body>
    <main class="auth-card">
        <p class="eyebrow">Welcome</p>
        <h1>Create your account</h1>
        <p class="intro">Sign up to manage your products.</p>

        <form method="POST" action="/signup">
            <label>
                Username
                <input type="text" name="username" autocomplete="username" maxlength="100" required>
            </label>

            <label>
                Email
                <input type="email" name="email" autocomplete="email" required>
            </label>

            <label>
                Password
                <input type="password" name="password" autocomplete="new-password" required>
            </label>

            <button type="submit">Create account</button>
        </form>

        <p class="auth-footer">Already have an account? <a href="/login">Log in</a></p>
    </main>
    <script src="/js/frontend.js"></script>

</body>
</html>
