<?php
require 'includes/funcoes.php';

$cards = [
    ['Produtos', count(lerDados('produtos')), 'produtos.php'],
    ['Endereços', count(lerDados('enderecos')), 'enderecos.php'],
    ['Transportadores', count(lerDados('transportadores')), 'transportadores.php'],
    ['Fretes', count(lerDados('fretes')), 'fretes.php'],
];
$ultimos = array_slice(array_reverse(lerDados('fretes')), 0, 5);

$titulo = 'Início';
require 'includes/cabecalho.php';
?>
<h1 class="h3 mb-4">Resumo dos cadastros</h1>
<div class="row g-3 mb-4">
    <?php foreach ($cards as [$nome, $total, $link]): ?>
        <div class="col-6 col-md-3">
            <a href="<?= $link ?>" class="card card-resumo text-decoration-none h-100">
                <div class="card-body text-center">
                    <div class="display-6 fw-bold text-primary"><?= $total ?></div>
                    <div class="text-secondary"><?= $nome ?></div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<h2 class="h5">Últimos fretes</h2>
<?php if (!$ultimos): ?>
    <p class="text-muted">Nenhum frete gerado ainda.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped bg-white align-middle">
            <thead class="table-primary">
                <tr><th>#</th><th>Data</th><th>Transportador</th><th>Origem → Destino</th><th>Peso</th><th>Valor</th></tr>
            </thead>
            <tbody>
                <?php foreach ($ultimos as $frete): $d = detalharFrete($frete); ?>
                    <tr>
                        <td><?= $d['id'] ?></td>
                        <td><?= dataBr($d['data']) ?></td>
                        <td><?= e($d['transportador']) ?></td>
                        <td><?= e($d['origem']) ?> → <?= e($d['destino']) ?></td>
                        <td><?= peso($d['peso_total']) ?></td>
                        <td><?= moeda($d['valor_total']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php require 'includes/rodape.php'; ?>