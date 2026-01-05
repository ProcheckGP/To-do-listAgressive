<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="container text-center title-site">
    <h1>To-do-listAgressive</h1>
    <img src="/To-do-listAgressive/view/resources/image/devil.png" alt="#" height="100" width="100">
</div>

<div class="container text-center form-authorization">
    <h1>Log in to your account</h1>
    <div class="container">
        <form method="POST" action="/To-do-listAgressive/router.php?action=login" id="loginForm">
            <div class="form-group">
                <label for="email">Enter your email</label>
                <input type="email" class="form-control" id="email" name="email">
                <div class="invalid-feedback" id="emailError"></div>
            </div>
            <div class="form-group">
                <label for="password">Enter your password</label>
                <input type="password" class="form-control" id="password" name="password">
                <div class="invalid-feedback" id="passwordError"></div>
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

<div id="errorContainer" class="container alert-container"></div>

<script>
    document.getElementById('loginForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value.trim();
        const errorContainer = document.getElementById('errorContainer');
        let isValid = true;


        document.getElementById('emailError').textContent = '';
        document.getElementById('passwordError').textContent = '';
        document.getElementById('email').classList.remove('is-invalid');
        document.getElementById('password').classList.remove('is-invalid');
        errorContainer.innerHTML = '';

        if (!email) {
            document.getElementById('emailError').textContent = 'Email is required';
            document.getElementById('email').classList.add('is-invalid');
            isValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            document.getElementById('emailError').textContent = 'Please enter a valid email address';
            document.getElementById('email').classList.add('is-invalid');
            isValid = false;
        }

        if (!password) {
            document.getElementById('passwordError').textContent = 'Password is required';
            document.getElementById('password').classList.add('is-invalid');
            isValid = false;
        } else if (password.length < 6) {
            document.getElementById('passwordError').textContent = 'Password must be at least 6 characters long';
            document.getElementById('password').classList.add('is-invalid');
            isValid = false;
        }

        if (!isValid) {
            errorContainer.innerHTML = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Login failed!</strong> Please fix the errors in the form.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

            errorContainer.scrollIntoView({
                behavior: 'smooth'
            });
            return false;
        }

        this.submit();
    });
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>