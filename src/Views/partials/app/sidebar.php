<nav id="sidebar"
     class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">

    <div class="position-sticky pt-3">

        <ul class="nav flex-column">

            <li class="nav-item">
                <a class="nav-link" href="/prosaude/dashboard">
                    <i class="fa-solid fa-gauge"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#usuariosMenu" role="button">
                    <span><i class="fa-solid fa-users me-2"></i>
                    Cadastros
                    </span>
                     <i class="fa-solid fa-chevron-down menu-arrow"></i>
                </a>
                <div class="collapse" id="usuariosMenu">
                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                        <li>
                            <a class="nav-link ms-3" href="/prosaude/usuarios">
                                Usuários
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/prosaude/logout">
                    Sair
                </a>
            </li>
        </ul>
    </div>
</nav>