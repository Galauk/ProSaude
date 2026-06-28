<?php
if(!isset($perfilUsuario)){
    die();
}
?>
<form method="POST" action="/prosaude/usuarios">
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h3>Criar Novo Usuário</h3>
            </div>
            <div class="card-body">
                <?php if (isset($_GET['erro'])): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($_GET['erro']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="nome" name="nome" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="login" class="form-label">Login</label>
                    <input type="text" class="form-control" id="login" name="login" required>
                </div>
                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="senha" name="senha" required>
                </div>
                <div class="mb-3">
                    <label for="perfil" class="form-label">Perfil</label>
                    <select class="form-select" id="perfil" name="perfil" required>
                        <?php foreach ($perfilUsuario as $perfil): ?>
                            <option value="<?php echo $perfil->value; ?>">
                                <?php echo $perfil->name; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="ativo" class="form-label">Ativo</label>
                    <select class="form-select" id="ativo" name="ativo" required>
                        <option value="1">Sim</option>
                        <option value="0">Não</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                    <input type="date" class="form-control" id="data_nascimento" name="data_nascimento">
                </div>
                <div class="row">
                    <div class="col">
                        <a href="/prosaude/usuarios" class="btn btn-secondary">Cancelar</a>
                    </div>
                    <div class="col text-end">
                        <button type="submit" class="btn btn-success">Salvar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>