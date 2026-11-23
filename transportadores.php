<?php
require 'includes/funcoes.php';
$transportadores = lerDados('transportadores');

// Excluir
if (isset($_GET['excluir'])) {
    salvarDados('transportadores', removerPorId($transportadores, $_GET['excluir']));
    header('Location: transportadores.php');
    exit;
}

// Salvar (novo ou edição)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transportador = [
        'id' => (int) $_POST['id'],
        'nome' => trim($_POST['nome']),
        'documento' => trim($_POST['documento']),
        'telefone' => trim($_POST['telefone']),
        'veiculo' => trim($_POST['veiculo']),
    ];
    salvarDados('transportadores', salvarItem($transportadores, $transportador));
    header('Location: transportadores.php?ok=1');
    exit;
}

// Editar
$editando = isset($_GET['editar']) ? buscarPorId($transportadores, $_GET['editar']) : null;

$titulo = 'Transportadores';
require 'includes/cabecalho.php';
?>
<h1 class="h3 mb-3">Transportadores</h1>

<div class="card mb-4">
    <div class="card-body">
        <h2 class="h5"><?= $editando ? 'Editar transportador' : 'Novo transportador' ?></h2>
        <form method="post" class="row g-3">
            <input type="hidden" name="id" value="<?= $editando['id'] ?? 0 ?>">
            <div class="col-md-5">
                <label class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control" required value="<?= e($editando['nome'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">CPF ou CNPJ</label>
                <input type="text" name="documento" class="form-control" required value="<?= e($editando['documento'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Telefone</label>
                <input type="text" name="telefone" class="form-control" value="<?= e($editando['telefone'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Veículo (modelo e placa)</label>
                <input type="text" name="veiculo" class="form-control" required value="<?= e($editando['veiculo'] ?? '') ?>">
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Salvar</button>
                <?php if ($editando): ?>
                    <a href="transportadores.php" class="btn btn-outline-secondary">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover bg-white align-middle">
        <thead class="table-primary">
            <tr><th>#</th><th>Nome</th><th>CPF/CNPJ</th><th>Telefone</th><th>Veículo</th><th class="text-end">Ações</th></tr>
        </thead>
        <tbody>
            <?php foreach ($transportadores as $t): ?>
                <tr>
                    <td><?= $t['id'] ?></td>
                    <td><?= e($t['nome']) ?></td>
                    <td><?= e($t['documento']) ?></td>
                    <td><?= e($t['telefone']) ?></td>
                    <td><?= e($t['veiculo']) ?></td>
                    <td class="text-end">
                        <a href="?editar=<?= $t['id'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                        <a href="?excluir=<?= $t['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Excluir este transportador?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$transportadores): ?>
                <tr><td colspan="6" class="text-center text-muted">Nenhum transportador cadastrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php require 'includes/rodape.php'; ?>