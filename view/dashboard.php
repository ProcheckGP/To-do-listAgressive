<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /To-do-listAgressive/view/forms/formAuthorization.php");
    exit();
}
?>

<?php require __DIR__ . '/layout/header.php'; ?>

<body>
    <section>
        <div class="row" style="height: 100vh;">
            <div class="col-2 left-half">
                <div class="menu-top">
                    здесь разные пункты будут
                </div>
                <div class="menu-bottom">
                    <h4><?php echo $_SESSION['username']; ?></h4>
                    <a href="/To-do-listAgressive/router.php?action=logout">Выйти</a>
                </div>
            </div>
            <div class="col-10 right-half">
                <h1>Основной контент</h1>
            </div>
        </div>
    </section>
</body>

<?php require __DIR__ . '/layout/footer.php'; ?>