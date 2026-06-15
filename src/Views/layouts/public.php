<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">

    <title><?= $title ?? 'Pro Saude' ?></title>

    <link rel="stylesheet" href="<?= asset('css/public.css'); ?>">
    <link rel="stylesheet" href="<?= asset('css/bootstrap.min.css'); ?>">
</head>

<body>

<?php require __DIR__ .
    '/../partials/public/header.php'; ?>

<main>

    <?= $content ?>

</main>

<?php require __DIR__ .
    '/../partials/public/footer.php'; ?>

<script src="<?= asset('js/bootstrap.min.js'); ?>"></script>

</body>

</html>