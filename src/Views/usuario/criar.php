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

                <button type="submit" class="btn btn-success">Salvar Usuário</button>
            </div>
    
</form>