<header class="navbar sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <div class="offcanvas-header">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-6 text-white" href="#">ProSaúde</a>
        <ul class="navbar-nav flex-row d-md-none">
            <li class="nav-item text-nowrap">
                <button class="nav-link px-3 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSearch" aria-controls="navbarSearch" aria-expanded="false" aria-label="Toggle search"> <svg class="bi" aria-hidden="true"><use xlink:href="#search"></use></svg> </button> </li> <li class="nav-item text-nowrap"> <button class="nav-link px-3 text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation"> 
                    <svg class="bi" aria-hidden="true">
                        <use xlink:href="#list"></use>
                    </svg> 
                </button> 
            </li> 
        </ul>
        <div id="navbarSearch" class="navbar-search w-100 collapse"> 
            <input class="form-control w-100 rounded-0 border-0" type="text" placeholder="Search" aria-label="Search"> 
        </div>
    </div>
    <div class="text-white me-3">

        Bem-vindo,
        <?= htmlspecialchars(
            $_SESSION['usuario']
        ) ?>

        |
        <a class="nav-link text-white" href="/logout">
            Sair
        </a>

    </div>

</header>
    <?php if (isset($_GET['erro'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Erro!</strong> <?php echo htmlspecialchars($_GET['erro']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
