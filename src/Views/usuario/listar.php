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
            
            <a href="#" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#adicionarUsuarioModal">
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
                                    <td><?php echo htmlspecialchars($usuario->id); ?></td>
                                    <td><?php echo htmlspecialchars($usuario->nome); ?></td>
                                    <td><?php echo htmlspecialchars($usuario->email); ?></td>
                                    <td>
                                        <a href="/prosaude/usuarios/<?php echo $usuario->id; ?>" class="btn btn-sm btn-info">Visualizar</a>
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

<!-- Modal para Adicionar Usuário -->
<div class="modal fade" id="adicionarUsuarioModal" tabindex="-1" aria-labelledby="adicionarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adicionarUsuarioLabel">Adicionar Novo Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="/prosaude/usuarios">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="id" class="form-label">ID</label>
                        <input type="text" class="form-control" id="id" name="id" required>
                    </div>
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>