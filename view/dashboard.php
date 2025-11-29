<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /To-do-listAgressive/view/forms/formAuthorization.php");
    exit();
}
?>

<?php require __DIR__ . '/layout/header.php'; ?>

<h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
<p>You are successfully logged in.</p>

<?php require __DIR__ . '/layout/footer.php'; ?>