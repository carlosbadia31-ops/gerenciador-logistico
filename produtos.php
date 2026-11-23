<?php
require 'includes/funcoes.php';
$produtos = lerDados('produtos');

// Excluir
if (isset($_GET['excluir'])) {
    salvarDados('produtos', removerPorId($produtos, $_GET['excluir']));
    header('Location: produtos.php');
    exit;
}

// Salvar (novo ou edição)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produto = [
        'id' => (int) $_POST['id'],
        'nome' => trim($_POST['nome']),
        'peso' => numero($_POST['peso']),
        'valor' => numero($_POST['valor']),
    ];
    salvarDados('produtos', salvarItem($produtos, $produto));
    header('Location: produtos.php?ok=1');
    exit;
}

// Editar: os dados voltam para o formulário
$editando = isset($_GET['editar']) ? buscarPorId($produtos, $_GET['editar']) : null;

$titulo = 'Produtos';
require 'includes/cabecalho.php';
?>
<h1 class="h3 mb-3">Produtos</h1>

<div class="card mb-4">
    <div class="card-body">
        <h2 class="h5"><?= $editando ? 'Editar produto' : 'Novo produto' ?></h2>
        <form method="post" class="row g-3">
            <input type="hidden" name="id" value="<?= $editando['id'] ?? 0 ?>">
            <div class="col-md-6">
                <label class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control" required value="<?= e($editando['nome'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Peso (kg)</label>
                <input type="text" inputmode="decimal" name="peso" class="form-control" required value="<?= e($editando['peso'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Valor (R$)</label>
                <input type="text" inputmode="decimal" name="valor" class="form-control" required value="<?= e($editando['valor'] ?? '') ?>">
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Salvar</button>
                <?php if ($editando): ?>
                    <a href="produtos.php" class="btn btn-outline-secondary">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover bg-white align-middle">
        <thead class="table-primary">
            <tr><th>#</th><th>Nome</th><th>Peso</th><th>Valor</th><th class="text-end">Ações</th></tr>
        </thead>
        <tbody>
            <?php foreach ($produtos as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><?= e($p['nome']) ?></td>
                    <td><?= peso($p['peso']) ?></td>
                    <td><?= moeda($p['valor']) ?></td>
                    <td class="text-end">
                        <a href="?editar=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                        <a href="?excluir=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Excluir este produto?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$produtos): ?>
                <tr><td colspan="5" class="text-center text-muted">Nenhum produto cadastrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php require 'includes/rodape.php'; ?>