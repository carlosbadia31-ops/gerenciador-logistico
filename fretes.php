<?php
require 'includes/funcoes.php';
$fretes = lerDados('fretes');
$produtos = lerDados('produtos');
$enderecos = lerDados('enderecos');
$transportadores = lerDados('transportadores');
$erro = '';

// Excluir
if (isset($_GET['excluir'])) {
    salvarDados('fretes', removerPorId($fretes, $_GET['excluir']));
    header('Location: fretes.php');
    exit;
}

// Gerar frete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Junta os produtos de todas as linhas do formulário
    $itens = [];
    foreach ($_POST['produto_id'] ?? [] as $i => $produtoId) {
        $quantidade = (int) ($_POST['quantidade'][$i] ?? 0);
        if ($produtoId !== '' && $quantidade > 0) {
            $itens[] = ['produto_id' => (int) $produtoId, 'quantidade' => $quantidade];
        }
    }

    if (!$itens || empty($_POST['transportador_id']) || empty($_POST['origem_id']) || empty($_POST['destino_id'])) {
        $erro = 'Escolha o transportador, a origem, o destino e pelo menos um produto.';
    } else {
        $frete = [
            'id' => 0,
            'data' => $_POST['data'] ?: date('Y-m-d'),
            'transportador_id' => (int) $_POST['transportador_id'],
            'origem_id' => (int) $_POST['origem_id'],
            'destino_id' => (int) $_POST['destino_id'],
            'itens' => $itens,
        ];
        salvarDados('fretes', salvarItem($fretes, $frete));
        header('Location: fretes.php?ok=1');
        exit;
    }
}

$origens = array_filter($enderecos, fn($end) => $end['tipo'] === 'Origem');
$destinos = array_filter($enderecos, fn($end) => $end['tipo'] === 'Destino');

$titulo = 'Fretes';
$script = 'js/frete.js';
require 'includes/cabecalho.php';
?>
<h1 class="h3 mb-3">Fretes</h1>

<?php if ($erro): ?>
    <div class="alert alert-danger"><?= e($erro) ?></div>
<?php endif; ?>
<?php if (!$produtos || !$origens || !$destinos || !$transportadores): ?>
    <div class="alert alert-warning">Para gerar um frete, cadastre antes pelo menos 1 produto, 1 endereço de origem, 1 de destino e 1 transportador.</div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-body">
        <h2 class="h5">Novo frete</h2>
        <form method="post">
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label">Data</label>
                    <input type="date" name="data" class="form-control" required value="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Transportador</label>
                    <select name="transportador_id" class="form-select" required>
                        <option value="">Escolha</option>
                        <?php foreach ($transportadores as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= e($t['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Origem</label>
                    <select name="origem_id" class="form-select" required>
                        <option value="">Escolha</option>
                        <?php foreach ($origens as $end): ?>
                            <option value="<?= $end['id'] ?>"><?= e(descreverEndereco($end)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Destino</label>
                    <select name="destino_id" class="form-select" required>
                        <option value="">Escolha</option>
                        <?php foreach ($destinos as $end): ?>
                            <option value="<?= $end['id'] ?>"><?= e(descreverEndereco($end)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <h3 class="h6">Produtos do frete</h3>
            <div id="itens">
                <div class="row g-2 mb-2 linha-produto">
                    <div class="col-7 col-md-8">
                        <select name="produto_id[]" class="form-select" required>
                            <option value="">Escolha o produto</option>
                            <?php foreach ($produtos as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= e($p['nome']) ?> (<?= peso($p['peso']) ?> - <?= moeda($p['valor']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-3 col-md-2">
                        <input type="number" name="quantidade[]" class="form-control" min="1" value="1" required>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-outline-danger w-100 remover-produto">Remover</button>
                    </div>
                </div>
            </div>
            <button type="button" id="adicionar-produto" class="btn btn-outline-primary btn-sm mb-3">+ Adicionar produto</button>
            <div><button class="btn btn-primary">Gerar frete</button></div>
        </form>
    </div>
</div>

<h2 class="h5">Fretes gerados</h2>
<?php if (!$fretes): ?>
    <p class="text-muted">Nenhum frete gerado ainda.</p>
<?php endif; ?>
<?php foreach (array_reverse($fretes) as $frete): $d = detalharFrete($frete); ?>
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between flex-wrap gap-2">
                <div><strong>Frete #<?= $d['id'] ?></strong> &middot; <?= dataBr($d['data']) ?> &middot; <?= e($d['transportador']) ?></div>
                <a href="?excluir=<?= $d['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Excluir este frete?')">Excluir</a>
            </div>
            <div class="text-muted small mb-2"><?= e($d['origem']) ?> → <?= e($d['destino']) ?></div>
            <ul class="mb-2">
                <?php foreach ($d['itens'] as $item): ?>
                    <li><?= $item['quantidade'] ?>x <?= e($item['nome']) ?> &middot; <?= peso($item['peso']) ?> &middot; <?= moeda($item['valor']) ?></li>
                <?php endforeach; ?>
            </ul>
            <strong>Peso total:</strong> <?= peso($d['peso_total']) ?> &nbsp; <strong>Valor total:</strong> <?= moeda($d['valor_total']) ?>
        </div>
    </div>
<?php endforeach; ?>
<?php require 'includes/rodape.php'; ?>