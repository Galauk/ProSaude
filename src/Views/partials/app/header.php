<header>

    <?php if (isset($_GET['erro'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Erro!</strong> <?php echo htmlspecialchars($_GET['erro']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <nav class="navbar navbar-dark bg-primary">

        <div class="container-fluid">

            <button class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#sidebar">

                <span class="navbar-toggler-icon"></span>

            </button>

            <span class="navbar-brand mb-0 h1">
                ProSaúde
            </span>

        </div>

    </nav>
</header>
