<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create account</title>
    <style>
        :root {
            --page-bg: #f3f4f6;
            --card-bg: #f7f9fb;
            --field-bg: #eff3f9;
            --field-border: #d6dce7;
            --field-border-focus: #4d6fe8;
            --text: #1f2430;
            --muted: #6a7280;
            --primary: #2e5ce6;
            --primary-dark: #254bc9;
            --danger: #c42727;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: grid;
            place-items: center;
            margin: 0;
            padding: 32px 18px;
            color: var(--text);
            background: var(--page-bg);
            font-family: "Segoe UI", Arial, sans-serif;
        }

        .auth-card {
            width: min(100%, 540px);
            padding: 26px 36px 32px;
            background: rgba(255, 255, 255, 0.5);
            border: 1px solid #dfe3eb;
            border-radius: 20px;
            box-shadow: 0 10px 28px rgba(29, 41, 57, 0.05);
        }

        .eyebrow {
            margin: 0 0 18px;
            color: var(--primary);
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        h1 {
            margin: 0;
            font-size: clamp(2.25rem, 3vw, 3.2rem);
            line-height: 1.07;
            letter-spacing: -0.06em;
            font-weight: 800;
        }

        .intro {
            max-width: 440px;
            margin: 12px 0 26px;
            color: var(--muted);
            font-size: 19px;
            line-height: 1.4;
        }

        form {
            display: grid;
            gap: 18px;
        }

        .field {
            display: grid;
            gap: 10px;
            font-size: 18px;
            font-weight: 700;
        }

        .field span {
            color: #1c2432;
        }

        .password-wrap {
            position: relative;
        }

        input {
            width: 100%;
            border: 1px solid var(--field-border);
            border-radius: 10px;
            padding: 15px 16px;
            background: var(--field-bg);
            color: var(--text);
            font: inherit;
            font-weight: 500;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
        }

        input:focus {
            border-color: var(--field-border-focus);
            background: #f4f7ff;
            box-shadow: 0 0 0 3px rgba(77, 111, 232, 0.12);
            outline: none;
        }

        .password-wrap input {
            padding-right: 46px;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 12px;
            display: grid;
            place-items: center;
            width: 28px;
            height: 28px;
            border: 0;
            background: transparent;
            color: #4a5364;
            transform: translateY(-50%);
            cursor: pointer;
            padding: 0;
        }

        .password-toggle svg {
            width: 20px;
            height: 20px;
        }

        .error-text {
            min-height: 0;
            margin: -4px 0 0;
            color: var(--danger);
            font-size: 16px;
            font-weight: 600;
            letter-spacing: -0.02em;
        }

        .primary-button {
            margin-top: 2px;
            border: 0;
            border-radius: 12px;
            padding: 18px 16px;
            background: linear-gradient(180deg, var(--primary), var(--primary-dark));
            color: #fff;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.03em;
            cursor: pointer;
            transition: transform 0.12s ease, filter 0.12s ease;
        }

        .primary-button:hover {
            filter: brightness(1.02);
        }

        .primary-button:active {
            transform: translateY(1px);
        }

        .auth-footer {
            margin: 22px 0 0;
            color: var(--muted);
            font-size: 18px;
            text-align: center;
        }

        a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 700;
        }

        a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .auth-card {
                padding: 22px 22px 24px;
            }

            .intro,
            .auth-footer,
            .field {
                font-size: 16px;
            }

            .primary-button {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <main class="auth-card" aria-labelledby="signup-heading">
        <p class="eyebrow">GET STARTED</p>
        <h1 id="signup-heading">Create account</h1>
        <p class="intro">Create an account to manage your product catalog.</p>

        <form method="POST" action="/signup" novalidate>
            <label class="field">
                <span>Your name</span>
                <input type="text" name="username" autocomplete="username" maxlength="100" placeholder="Enter your name" required>
            </label>

            <label class="field">
                <span>Email address</span>
                <input type="email" name="email" autocomplete="email" placeholder="Enter your email" required>
            </label>

            <label class="field">
                <span>Password</span>
                <div class="password-wrap">
                    <input id="signup-password" type="password" name="password" autocomplete="new-password" placeholder="Create a password" required>
                    <button class="password-toggle" type="button" aria-label="Show password" aria-pressed="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"></path>
                            <circle cx="12" cy="12" r="3.25"></circle>
                        </svg>
                    </button>
                </div>
            </label>

            <div class="error-text" aria-live="polite"></div>

            <button class="primary-button" type="submit">Create account</button>
        </form>

        <p class="auth-footer">Already have an account? <a href="/login">Sign in</a></p>
    </main>

    <script>
        const passwordInput = document.getElementById('signup-password');
        const toggleButton = document.querySelector('.password-toggle');

        if (passwordInput && toggleButton) {
            toggleButton.addEventListener('click', () => {
                const shouldShow = passwordInput.type === 'password';
                passwordInput.type = shouldShow ? 'text' : 'password';
                toggleButton.setAttribute('aria-pressed', String(shouldShow));
                toggleButton.setAttribute('aria-label', shouldShow ? 'Hide password' : 'Show password');
            });
        }
    </script>
</body>
</html>
