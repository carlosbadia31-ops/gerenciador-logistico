<?php
$paginaAtual = basename($_SERVER['PHP_SELF']);
$menu = [
    'index.php' => 'Início',
    'produtos.php' => 'Produtos',
    'enderecos.php' => 'Endereços',
    'transportadores.php' => 'Transportadores',
    'fretes.php' => 'Fretes',
    'relatorio.php' => 'Relatório',
];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo ?? 'Início') ?> - Gerenciador Logístico</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
<nav class="navbar navbar-expand navbar-dark bg-primary mb-4">
    <div class="container flex-wrap">
        <a class="navbar-brand fw-bold" href="index.php">Gerenciador Logístico</a>
        <ul class="navbar-nav flex-wrap">
            <?php foreach ($menu as $link => $nome): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $paginaAtual === $link ? 'active fw-semibold' : '' ?>" href="<?= $link ?>"><?= $nome ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>
<main class="container pb-5">
<?php if (isset($_GET['ok'])): ?>
    <div class="alert alert-success">Dados salvos com sucesso.</div>
<?php endif; ?>