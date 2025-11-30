<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="container text-center form-authorization">
    <h1>Log in to your account</h1>
    <form method="POST" action="/To-do-listAgressive/router.php?action=login">
        <div class="form-group">
            <label for="email">Enter your email</label>
            <input type="email" class="form-control" id="email" name="email">
        </div>
        <div class="form-group">
            <label for="password">Enter your password</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <br>
        <div class="form-group">
            <button type="submit" class="form-control">Log in</button>
            <a href="/To-do-listAgressive/view/forms/formRegistration.php">Don't you have an account yet?</a>
        </div>

    </form>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>