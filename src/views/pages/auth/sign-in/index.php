<main class="main">
    <section class="signin">
        <!-- Title -->
        <h1 class="signin__title">Sign In</h1>
        <!-- Form -->
        <form id="signin-form" class="form" action="/api/auth/sign-in" method="POST">
            <!-- Email -->
            <div class="form__group">
                <label for="email" id="email-label" class="form__label">
                    Email
                </label>
                <input
                    id="email"
                    name="email"
                    type="text"
                    placeholder="Enter your email"
                    class="input" />
                <p id="email-message" class="form__error-message"></p>
            </div>

            <!-- Password -->
            <div class="form__group">
                <label for="password" id="password-label" class="form__label">
                    Password
                </label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="Enter your password"
                    class="input" />
                <p id="password-message" class="form__error-message"></p>
            </div>
            <button id="signin-button" type="submit" class="button button--default">
                Sign In
            </button>
        </form>

        <!-- Sign up -->
        <p class="signin__register-prompt">
            Don't have an account?
            <a href="/auth/register.php" class="anchor-link">
                Register
            </a>
        </p>
    </section>
</main>