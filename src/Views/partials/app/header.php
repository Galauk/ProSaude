<header>

    <?php if (isset($_GET['erro'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Erro!</strong> <?php echo htmlspecialchars($_GET['erro']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <nav class="navbar navbar-dark bg-primary">

        <div class="row">
            <div class="col">
                <div class="container-fluid">
                    <span class="navbar-brand mb-0 h1">
                        ProSaúde
                    </span>
                </div>
                
            </div>
            <div class="d-flex align-items-center col">
                <span class="me-2">Sessão:</span>
                <span id="contador-sessao" class="badge bg-success">
                    15:00
                </span>
            </div>

        </div>

    </nav>
</header>
