<?php

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
    </head>
    <body>
        <h1>Login</h1>
        <form action="?rota=autenticar" method="POST">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>
            <br>
            <label for="senha">Senha:</label>
            <input type="password" name="senha" id="senha" required>
            <br>
            <button type="submit">Entrar</button>
        </form>
    </body>
</html>