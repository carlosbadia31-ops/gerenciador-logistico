<?php
require 'includes/funcoes.php';
$dia = $_GET['dia'] ?? date('Y-m-d');

// Filtra os fretes do dia escolhido
$doDia = [];
foreach (lerDados('fretes') as $frete) {
    if ($frete['data'] === $dia) {
        $doDia[] = detalharFrete($frete);
    }
}
$pesoDia = array_sum(array_column($doDia, 'peso_total'));
$valorDia = array_sum(array_column($doDia, 'valor_total'));

// Gera o arquivo para baixar (CSV, abre no Excel)
if (isset($_GET['baixar'])) {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="relatorio-fretes-' . $dia . '.csv"');
    $saida = fopen('php://output', 'w');
    fwrite($saida, "\xEF\xBB\xBF"); // para os acentos aparecerem certos no Excel
    fputcsv($saida, ['Frete', 'Data', 'Transportador', 'Origem', 'Destino', 'Produtos', 'Peso total (kg)', 'Valor total (R$)'], ';', '"', '\\');
    foreach ($doDia as $d) {
        $lista = [];
        foreach ($d['itens'] as $item) {
            $lista[] = $item['quantidade'] . 'x ' . $item['nome'];
        }
        fputcsv($saida, [
            $d['id'], dataBr($d['data']), $d['transportador'], $d['origem'], $d['destino'],
            implode(', ', $lista),
            number_format($d['peso_total'], 2, ',', ''),
            number_format($d['valor_total'], 2, ',', ''),
        ], ';', '"', '\\');
    }
    fputcsv($saida, ['', '', '', '', '', 'TOTAL DO DIA', number_format($pesoDia, 2, ',', ''), number_format($valorDia, 2, ',', '')], ';', '"', '\\');
    fclose($saida);
    exit;
}

$titulo = 'Relatório';
require 'includes/cabecalho.php';
?>
<h1 class="h3 mb-3">Relatório de fretes por dia</h1>

<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Dia</label>
                <input type="date" name="dia" class="form-control" value="<?= e($dia) ?>" required>
            </div>
            <div class="col-md-8">
                <button class="btn btn-primary">Buscar</button>
                <?php if ($doDia): ?>
                    <a href="?dia=<?= e($dia) ?>&baixar=1" class="btn btn-success">Baixar relatório</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<?php if (!$doDia): ?>
    <p class="text-muted">Nenhum frete em <?= dataBr($dia) ?>.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped bg-white align-middle">
            <thead class="table-primary">
                <tr><th>#</th><th>Transportador</th><th>Origem</th><th>Destino</th><th>Produtos</th><th>Peso</th><th>Valor</th></tr>
            </thead>
            <tbody>
                <?php foreach ($doDia as $d): ?>
                    <tr>
                        <td><?= $d['id'] ?></td>
                        <td><?= e($d['transportador']) ?></td>
                        <td><?= e($d['origem']) ?></td>
                        <td><?= e($d['destino']) ?></td>
                        <td>
                            <?php foreach ($d['itens'] as $item): ?>
                                <?= $item['quantidade'] ?>x <?= e($item['nome']) ?><br>
                            <?php endforeach; ?>
                        </td>
                        <td><?= peso($d['peso_total']) ?></td>
                        <td><?= moeda($d['valor_total']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="fw-bold"><td colspan="5">Total do dia (<?= count($doDia) ?> fretes)</td><td><?= peso($pesoDia) ?></td><td><?= moeda($valorDia) ?></td></tr>
            </tfoot>
        </table>
    </div>
<?php endif; ?>
<?php require 'includes/rodape.php'; ?>