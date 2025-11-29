<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="container text-center">
    <h1>Log in to your account</h1>
    <form method="POST" action="/To-do-listAgressive/index.php?action=login">
        <div class="container">
            <div>
                <label for="email">Enter your email</label>
                <input type="email" id="email" name="email">
            </div>

            <div>
                <label for="password">Enter your password</label>
                <input type="password" id="password" name="password">
            </div>
            <div>
                <div>
                    <button type="submit">Log in</button>
                    <a href="/To-do-listAgressive/view/forms/formRegistration.php">Don't you have an account yet?</a>
                </div>
            </div>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>