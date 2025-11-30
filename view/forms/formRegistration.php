<?php
session_start();
?>
<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="container text-center title-site">
    <h1>To-do-listAgressive</h1>
    <img src="/To-do-listAgressive/view/resources/image/devil.png" alt="#" height="100" width="100">
</div>

<div class="container text-center form-registration">
    <h1>Register your account</h1>

    <form method="POST" action="/To-do-listAgressive/router.php?action=register">
        <div class="container">
            <div class="form-group">
                <label for="username">Enter your username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="email">Enter your email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Enter your password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <br>
            <div class="form-group">
                <div class="row">
                    <button type="submit" class="btn btn-outline-dark">Register</button>
                    <a href="/To-do-listAgressive/view/forms/formAuthorization.php">Do you already have an account?</a>
                </div>
            </div>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>