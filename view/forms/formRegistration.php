<?php
session_start();
?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="container text-center">
    <h1>Register your account</h1>

    <form method="POST" action="/To-do-listAgressive/index.php?action=register">
        <div class="container">
            <div>
                <label for="username">Enter your username</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div>
                <label for="email">Enter your email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div>
                <label for="password">Enter your password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div>
                <div>
                    <button type="submit">Register</button>
                    <a href="/To-do-listAgressive/view/forms/formAuthorization.php">Do you already have an account?</a>
                </div>
            </div>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>