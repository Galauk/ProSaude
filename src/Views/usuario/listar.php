<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <h1><?php echo $title ?? 'Usuários'; ?></h1>
            
            <?php if (isset($_GET['sucesso'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Sucesso!</strong> <?php echo htmlspecialchars($_GET['sucesso']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['erro'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Erro!</strong> <?php echo htmlspecialchars($_GET['erro']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <a href="/prosaude/usuarios/criar" class="btn btn-primary mb-3">
                Adicionar Usuário
            </a>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($usuarios)) { ?>
                            <?php foreach ($usuarios as $usuario) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($usuario->getId()); ?></td>
                                    <td><?php echo htmlspecialchars($usuario->getNome()); ?></td>
                                    <td><?php echo htmlspecialchars($usuario->getEmail()); ?></td>
                                    <td>
                                        <a href="/prosaude/usuarios/<?php echo $usuario->getId(); ?>" class="btn btn-sm btn-info">Visualizar</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php }else{ ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">Nenhum usuário cadastrado</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
