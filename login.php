<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - BioForest</title>
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .login-container { max-width: 400px; margin: 100px auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); text-align: center; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; background: #004a8d; color: white; padding: 10px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body style="background-color: #f4f7f6;">
    <div class="login-container">
        <img src="embrapii.svg" alt="Logo" style="width: 150px; margin-bottom: 20px;">
        <h2>Acesso Restrito</h2>
        <form action="autenticar.php" method="POST">
            <input type="text" name="usuario" placeholder="Usuário" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit">Entrar no Painel</button>
        </form>
        <button type="button" onclick="window.location.href='https://bioforest.com.br'" style="background: #64748b; margin-top: 5px;">Voltar para o Site</button>
    </div>
</body>
</html>