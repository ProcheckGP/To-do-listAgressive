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
        <div class="row">
            <div class="col-2 left-half">
                <div class="menu-top">
                    здесь разные пункты будут
                </div>
                <div class="menu-bottom">
                    <div class="row">
                        <div class="col-8 text-center">
                            <img src="/To-do-listAgressive/view/resources/image/user.png" alt="" height="40px">
                            <p><?php echo $_SESSION['username']; ?></p>
                        </div>
                        <div class="col-4">
                            <a href="/To-do-listAgressive/router.php?action=logout">Выйти</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-10 right-half">
                <h1>Основной контент</h1>
            </div>
        </div>
    </section>
</body>