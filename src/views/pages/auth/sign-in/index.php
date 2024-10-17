<main class="main">
    <section class="signin">
        <!-- Title -->
        <h1 class="signin__title">Sign In</h1>
        <!-- Form -->
        <form class="form" action="/auth/sign-in" method="POST">
            <!-- Email -->
            <div class="form__group">
                <label for="email" class="form__label 
                    <?php if (isset($errorFields) && isset($errorFields['email'])):  ?>
                        <?= htmlspecialchars($errorFields['email'][0] ? 'form__error-message' : '') ?>
                    <?php endif; ?>
                ">
                    Email
                </label>

                <input
                    name="email"
                    type="text"
                    placeholder="Enter your email"
                    value="<?= htmlspecialchars($fields['email'] ?? '') ?>"
                    class="input" />

                <p class="form__error-message">
                    <?php if (isset($errorFields) && isset($errorFields['email'])):  ?>
                        <?= htmlspecialchars($errorFields['email'][0] ?? '') ?>
                    <?php endif; ?>
                </p>
            </div>

            <!-- Password -->
            <div class="form__group">
                <label for="password" class="form__label 
                    <?php if (isset($errorFields) && isset($errorFields['password'])):  ?>
                        <?= htmlspecialchars($errorFields['password'][0] ? 'form__error-message' : '') ?>
                    <?php endif; ?>
                ">
                    Password
                </label>
                <input
                    name="password"
                    type="password"
                    placeholder="Enter your password"
                    value="<?= htmlspecialchars($fields['password'] ?? '') ?>"
                    class="input" />
                <p class="form__error-message">
                    <?php if (isset($errorFields) && isset($errorFields['password'])):  ?>
                        <?= htmlspecialchars($errorFields['password'][0] ?? '') ?>
                    <?php endif; ?>
                </p>
            </div>
            <button type="submit" class="button button--default-size button--default-color">
                Sign In
            </button>
        </form>

        <!-- Sign up -->
        <p class="signin__register-prompt">
            Don't have an account?
            <a href="/auth/sign-up" class="anchor-link">
                Register
            </a>
        </p>
    </section>
</main>