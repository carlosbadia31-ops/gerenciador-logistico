<?php
date_default_timezone_set('America/Sao_Paulo');
define('PASTA_DADOS', __DIR__ . '/../dados');

// Lê a lista guardada em dados/NOME.json
function lerDados($nome)
{
    $arquivo = PASTA_DADOS . '/' . $nome . '.json';
    if (!file_exists($arquivo)) {
        return [];
    }
    $dados = json_decode(file_get_contents($arquivo), true);
    return is_array($dados) ? $dados : [];
}

// Salva a lista em dados/NOME.json
function salvarDados($nome, $lista)
{
    if (!is_dir(PASTA_DADOS)) {
        mkdir(PASTA_DADOS, 0777, true);
    }
    $json = json_encode(array_values($lista), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents(PASTA_DADOS . '/' . $nome . '.json', $json);
}

// Cria o identificador do próximo cadastro
function proximoId($lista)
{
    $maior = 0;
    foreach ($lista as $item) {
        if ($item['id'] > $maior) {
            $maior = $item['id'];
        }
    }
    return $maior + 1;
}

function buscarPorId($lista, $id)
{
    foreach ($lista as $item) {
        if ($item['id'] == $id) {
            return $item;
        }
    }
    return null;
}

function removerPorId($lista, $id)
{
    $nova = [];
    foreach ($lista as $item) {
        if ($item['id'] != $id) {
            $nova[] = $item;
        }
    }
    return $nova;
}

// Adiciona um item novo (id 0) ou substitui um item editado
function salvarItem($lista, $item)
{
    if (empty($item['id'])) {
        $item['id'] = proximoId($lista);
        $lista[] = $item;
    } else {
        foreach ($lista as $i => $atual) {
            if ($atual['id'] == $item['id']) {
                $lista[$i] = $item;
            }
        }
    }
    return $lista;
}

// Aceita número com ponto ou vírgula
function numero($texto)
{
    $texto = str_replace(',', '.', trim($texto));
    return is_numeric($texto) ? (float) $texto : 0;
}

function e($texto)
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

function moeda($valor)
{
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

function peso($valor)
{
    return number_format($valor, 2, ',', '.') . ' kg';
}

function dataBr($data)
{
    return date('d/m/Y', strtotime($data));
}

function descreverEndereco($endereco)
{
    return $endereco['nome'] . ' - ' . $endereco['cidade'] . '/' . $endereco['uf'];
}

// Junta o frete com os cadastros e calcula peso e valor total
function detalharFrete($frete)
{
    $produtos = lerDados('produtos');
    $enderecos = lerDados('enderecos');
    $transportadores = lerDados('transportadores');

    $itens = [];
    $pesoTotal = 0;
    $valorTotal = 0;
    foreach ($frete['itens'] as $item) {
        $produto = buscarPorId($produtos, $item['produto_id']);
        if (!$produto) {
            continue;
        }
        $pesoItem = $produto['peso'] * $item['quantidade'];
        $valorItem = $produto['valor'] * $item['quantidade'];
        $itens[] = [
            'nome' => $produto['nome'],
            'quantidade' => $item['quantidade'],
            'peso' => $pesoItem,
            'valor' => $valorItem,
        ];
        $pesoTotal += $pesoItem;
        $valorTotal += $valorItem;
    }

    $transportador = buscarPorId($transportadores, $frete['transportador_id']);
    $origem = buscarPorId($enderecos, $frete['origem_id']);
    $destino = buscarPorId($enderecos, $frete['destino_id']);

    return [
        'id' => $frete['id'],
        'data' => $frete['data'],
        'transportador' => $transportador ? $transportador['nome'] : '(removido)',
        'origem' => $origem ? descreverEndereco($origem) : '(removido)',
        'destino' => $destino ? descreverEndereco($destino) : '(removido)',
        'itens' => $itens,
        'peso_total' => $pesoTotal,
        'valor_total' => $valorTotal,
    ];
}