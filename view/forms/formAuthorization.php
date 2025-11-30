<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="container text-center title-site">
    <h1>To-do-listAgressive</h1>
    <img src="/To-do-listAgressive/view/resources/image/devil.png" alt="#" height="100" width="100">
</div>

<div class="container text-center form-authorization">
    <h1>Log in to your account</h1>
    <div class="container">
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
                <div class="row">
                    <button type="submit" class="btn btn-outline-dark">Log in</button>
                    <a href="/To-do-listAgressive/view/forms/formRegistration.php">Don't you have an account yet?</a>
                </div>
            </div>

        </form>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>