<?php
// Se não for uma requisição POST, redireciona para o formulário
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit;
}

// Conexão com o banco de dados (container Docker MySQL)
$host = 'localhost';
$db   = 'login_system';
$user = 'root';
$pass = 'root';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("<div class='container' style='color:#721c24; background:#f8d7da; padding:20px; border-radius:8px;'>
            <h2>❌ Erro de conexão</h2>
            <p>" . htmlspecialchars($e->getMessage()) . "</p>
            <p>Verifique se o container Docker está rodando:</p>
            <pre>docker ps</pre>
         </div>");
}

// Captura os dados do formulário (sem nenhum tratamento!)
$usuario = $_POST['usuario'] ?? '';
$senha   = $_POST['senha'] ?? '';

// CONSULTA VULNERÁVEL (NUNCA USE ISSO EM PRODUÇÃO!)
$sql = "SELECT * FROM usuarios WHERE usuario = '$usuario' AND senha = '$senha'";

// Debug: exibe a consulta gerada (como comentário HTML)
echo "<!-- SQL: " . htmlspecialchars($sql) . " -->";

try {
    $stmt = $pdo->query($sql);
    $result = $stmt->fetchAll();
} catch (PDOException $e) {
    die("<div class='container' style='color:#721c24; background:#f8d7da; padding:20px; border-radius:8px;'>
            <h2>⚠️ Erro na consulta SQL</h2>
            <p><strong>Query:</strong> " . htmlspecialchars($sql) . "</p>
            <p><strong>Erro:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
         </div>");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado do Login</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>

<body>
    <div class="container">
        <div class="login-box result-box">
            <?php if (count($result) > 0): ?>
                <h2 style="color: #28a745;">✅ Login bem-sucedido!</h2>
                <p>Bem-vindo, <strong><?= htmlspecialchars($result[0]['usuario']) ?></strong>.</p>
                <p><small>Dados do usuário logado:</small></p>
                <pre><?= htmlspecialchars(print_r($result[0], true)) ?></pre>
            <?php else: ?>
                <h2 style="color: #dc3545;">❌ Falha no login</h2>
                <p>Usuário ou senha inválidos.</p>
                <p><a href="index.html">← Tentar novamente</a></p>
            <?php endif; ?>

            <hr>
            <details>
                <summary>🔍 Ver consulta SQL executada</summary>
                <pre><code><?= htmlspecialchars($sql) ?></code></pre>
            </details>
            <div class="warning" style="margin-top: 20px;">
                ⚠️ Esta página é intencionalmente vulnerável a SQL Injection.
            </div>
            <footer class="footer">
                <a href="https://github.com/Eliel-DM/Mysql-Injection-Lab" target="_blank" rel="noopener noreferrer">📚 Repositório oficial no GitHub</a>
            </footer>
        </div>
    </div>
</body>

</html>