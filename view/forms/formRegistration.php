<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="container text-center title-site">
    <h1>To-do-listAgressive</h1>
    <img src="/To-do-listAgressive/view/resources/image/devil.png" alt="#" height="100" width="100">
</div>

<div class="container text-center form-registration">
    <h1>Register your account</h1>

    <form method="POST" action="/To-do-listAgressive/router.php?action=register" id="registerForm">
        <div class="container">
            <div class="form-group">
                <label for="username">Enter your username</label>
                <input type="text" class="form-control" id="username" name="username">
                <div class="invalid-feedback" id="usernameError"></div>
            </div>

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
                    <button type="submit" class="btn btn-outline-dark">Register</button>
                    <a href="/To-do-listAgressive/view/forms/formAuthorization.php">Do you already have an account?</a>
                </div>
            </div>
        </div>
    </form>
</div>

<div id="errorContainer" class="container alert-container"></div>

<script>
    document.getElementById('registerForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const username = document.getElementById('username').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value.trim();
        const errorContainer = document.getElementById('errorContainer');
        let isValid = true;


        document.getElementById('usernameError').textContent = '';
        document.getElementById('emailError').textContent = '';
        document.getElementById('passwordError').textContent = '';
        document.getElementById('username').classList.remove('is-invalid');
        document.getElementById('email').classList.remove('is-invalid');
        document.getElementById('password').classList.remove('is-invalid');
        errorContainer.innerHTML = '';

        if (!username) {
            document.getElementById('usernameError').textContent = 'Username is required';
            document.getElementById('username').classList.add('is-invalid');
            isValid = false;
        } else if (username.length < 3) {
            document.getElementById('usernameError').textContent = 'Username must be at least 3 characters long';
            document.getElementById('username').classList.add('is-invalid');
            isValid = false;
        } else if (username.length > 20) {
            document.getElementById('usernameError').textContent = 'Username must be less than 20 characters';
            document.getElementById('username').classList.add('is-invalid');
            isValid = false;
        }

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
        } else if (password.length < 8) {
            document.getElementById('passwordError').textContent = 'Password must be at least 8 characters long';
            document.getElementById('password').classList.add('is-invalid');
            isValid = false;
        } else if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(password)) {
            document.getElementById('passwordError').textContent = 'Password must contain at least one uppercase letter, one lowercase letter and one number';
            document.getElementById('password').classList.add('is-invalid');
            isValid = false;
        }

        if (!isValid) {
            errorContainer.innerHTML = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Registration failed!</strong> Please fix the errors in the form.
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