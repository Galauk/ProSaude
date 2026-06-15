<header>
    <ul class="nav justify-content-center">
        <li class="nav-item"><a class="nav-link" href="<?= url('/') ?>">Início</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= url('/sobre') ?>">Sobre</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= url('/login') ?>">Login</a></li>
    </ul>
</header>

<?php if (isset($_GET['erro'])){ ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Erro!</strong> <?php echo htmlspecialchars($_GET['erro']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>
