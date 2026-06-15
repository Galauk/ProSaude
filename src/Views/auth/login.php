<div class="d-flex align-items-center py-4 bg-body-tertiary">
    <div class="form-signin  m-auto">
    <h1 class="h3 mb-3 fw-normal">Login</h1>
    <?= isset($erro) ? "<p style='color: red;'>$erro</p>" : "" ?>
        <form action="/autenticar" method="POST">
            <div class="form-floating">
                <input class="form-control" type="text" name="login" id="login" required>
                <label class="form-label">Login:</label>
            </div>
            <div class="form-floating mb-3">
                <input class="form-control" type="password" name="senha" id="senha" required>
                <label class="form-label" for="senha">Senha:</label>
            </div>
            <button class="btn btn-primary w-100 py-2" type="submit">Entrar</button>
        </form>
    </div>
</div>