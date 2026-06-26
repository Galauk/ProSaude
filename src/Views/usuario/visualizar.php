<?php 
if(!isset($usuario)){
    die();
}

?>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3>Visualizar Usuário</h3>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <label class="col-3 fw-bold">
                    Nome
                </label>
                <div class="col">
                    <?= $usuario->getNome() ?>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-3 fw-bold">
                    Login
                </label>
                <div class="col">
                    <?= $usuario->getLogin() ?>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-3 fw-bold">
                    E-mail
                </label>
                <div class="col">
                    <?= $usuario->getEmail() ?>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-3 fw-bold">
                    Perfil
                </label>
                <div class="col">
                    <?= $usuario->getPerfil() ?>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-3 fw-bold">
                    Status
                </label>
                <div class="col">
                    <?php if ($usuario->isAtivo()): ?>
                        <span class="badge bg-success">
                            Ativo
                        </span>
                    <?php else: ?>
                        <span class="badge bg-danger">
                            Inativo
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <a
                href="/prosaude/usuarios/editar/<?= $usuario->getId() ?>"
                class="btn btn-primary"
            >
                Editar
            </a>
            <a
                href="/prosaude/usuarios"
                class="btn btn-secondary"
            >
                Voltar
            </a>
        </div>

    </div>

</div>