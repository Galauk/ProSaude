<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">

    <title><?= $title ?? 'Sistema' ?></title>

    <link rel="stylesheet" href="<?= asset('css/app.css'); ?>">
    <link rel="stylesheet" href="<?= asset('css/bootstrap.min.css'); ?>">
</head>

<body>

<?php require __DIR__ .
    '/../partials/app/header.php'; ?>

<?php require __DIR__ .
    '/../partials/app/sidebar.php'; ?>

<main>

    <?= $content ?>

</main>

<?php require __DIR__ .
    '/../partials/app/footer.php'; ?>

<script src="<?= asset('js/app.js'); ?>"></script>
<script src="<?= asset('js/bootstrap.min.js'); ?>"></script>

<?php if (isset($_SESSION['usuario'])): ?>
    <script src="<?= asset('js/session-timeout.js'); ?>"></script>
<?php endif; ?>

</body>

</html>