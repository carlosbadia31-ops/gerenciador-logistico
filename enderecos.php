<?php
require 'includes/funcoes.php';
$enderecos = lerDados('enderecos');

// Excluir
if (isset($_GET['excluir'])) {
    salvarDados('enderecos', removerPorId($enderecos, $_GET['excluir']));
    header('Location: enderecos.php');
    exit;
}

// Salvar (novo ou edição)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $endereco = [
        'id' => (int) $_POST['id'],
        'tipo' => $_POST['tipo'] === 'Destino' ? 'Destino' : 'Origem',
        'nome' => trim($_POST['nome']),
        'logradouro' => trim($_POST['logradouro']),
        'cidade' => trim($_POST['cidade']),
        'uf' => strtoupper(trim($_POST['uf'])),
        'cep' => trim($_POST['cep']),
    ];
    salvarDados('enderecos', salvarItem($enderecos, $endereco));
    header('Location: enderecos.php?ok=1');
    exit;
}

// Editar
$editando = isset($_GET['editar']) ? buscarPorId($enderecos, $_GET['editar']) : null;

$titulo = 'Endereços';
require 'includes/cabecalho.php';
?>
<h1 class="h3 mb-3">Endereços</h1>

<div class="card mb-4">
    <div class="card-body">
        <h2 class="h5"><?= $editando ? 'Editar endereço' : 'Novo endereço' ?></h2>
        <form method="post" class="row g-3">
            <input type="hidden" name="id" value="<?= $editando['id'] ?? 0 ?>">
            <div class="col-md-3">
                <label class="form-label">Tipo</label>
                <select name="tipo" class="form-select">
                    <option value="Origem">Origem</option>
                    <option value="Destino" <?= ($editando['tipo'] ?? '') === 'Destino' ? 'selected' : '' ?>>Destino</option>
                </select>
            </div>
            <div class="col-md-9">
                <label class="form-label">Nome do local</label>
                <input type="text" name="nome" class="form-control" required value="<?= e($editando['nome'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Logradouro e número</label>
                <input type="text" name="logradouro" class="form-control" required value="<?= e($editando['logradouro'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Cidade</label>
                <input type="text" name="cidade" class="form-control" required value="<?= e($editando['cidade'] ?? '') ?>">
            </div>
            <div class="col-md-1">
                <label class="form-label">UF</label>
                <input type="text" name="uf" maxlength="2" class="form-control" required value="<?= e($editando['uf'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">CEP</label>
                <input type="text" name="cep" class="form-control" value="<?= e($editando['cep'] ?? '') ?>">
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Salvar</button>
                <?php if ($editando): ?>
                    <a href="enderecos.php" class="btn btn-outline-secondary">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover bg-white align-middle">
        <thead class="table-primary">
            <tr><th>#</th><th>Tipo</th><th>Nome</th><th>Logradouro</th><th>Cidade/UF</th><th>CEP</th><th class="text-end">Ações</th></tr>
        </thead>
        <tbody>
            <?php foreach ($enderecos as $end): ?>
                <tr>
                    <td><?= $end['id'] ?></td>
                    <td><span class="badge <?= $end['tipo'] === 'Origem' ? 'bg-success' : 'bg-warning text-dark' ?>"><?= $end['tipo'] ?></span></td>
                    <td><?= e($end['nome']) ?></td>
                    <td><?= e($end['logradouro']) ?></td>
                    <td><?= e($end['cidade']) ?>/<?= e($end['uf']) ?></td>
                    <td><?= e($end['cep']) ?></td>
                    <td class="text-end">
                        <a href="?editar=<?= $end['id'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                        <a href="?excluir=<?= $end['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Excluir este endereço?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$enderecos): ?>
                <tr><td colspan="7" class="text-center text-muted">Nenhum endereço cadastrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php require 'includes/rodape.php'; ?>