<?php
// api/buscar_produtos.php

require_once '../config/config.php';
require_once '../config/api.php';
require_once '../includes/functions.php';
require_once 'requests.php';

header('Content-Type: application/json');

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

// Obter dados da requisição
$input = json_decode(file_get_contents('php://input'), true);
$termo = $input['termo'] ?? '';

if (empty($termo)) {
    echo json_encode(['status' => 'error', 'message' => 'Termo de busca é obrigatório']);
    exit;
}

try {
    // Buscar todos os produtos
    $produtos_response = listarProdutos();
    
    if ($produtos_response['status'] !== 'success') {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao buscar produtos']);
        exit;
    }
    
    $todos_produtos = $produtos_response['data']['data'];
    $termo_lower = strtolower(trim($termo));
    
    // Filtrar produtos que contêm o termo de busca
    $produtos_encontrados = [];
    
    foreach ($todos_produtos as $produto) {
        $nome = strtolower($produto['nome']);
        $titulo = strtolower($produto['titulo']);
        
        // Buscar no nome ou título do produto
        if (strpos($nome, $termo_lower) !== false || strpos($titulo, $termo_lower) !== false) {
            // Adicionar informações de desconto visual
            $precos_desconto = formatPriceWithDiscount($produto['preco_lojavirtual'], $produto['id']);
            
            $produtos_encontrados[] = [
                'id' => $produto['id'],
                'nome' => $produto['nome'],
                'titulo' => $produto['titulo'],
                'foto' => $produto['foto'],
                'preco_original' => $precos_desconto['preco_original'],
                'preco_com_desconto' => $precos_desconto['preco_com_desconto'],
                'desconto_percentual' => $precos_desconto['desconto_percentual'],
                'url' => "?page=produto&id=" . $produto['id']
            ];
        }
    }
    
    // Limitar a 8 resultados para não sobrecarregar a interface
    $produtos_encontrados = array_slice($produtos_encontrados, 0, 8);
    
    echo json_encode([
        'status' => 'success',
        'data' => $produtos_encontrados,
        'total' => count($produtos_encontrados)
    ]);
    
} catch (Exception $e) {
    logActivity('busca_error', ['erro' => $e->getMessage(), 'termo' => $termo]);
    echo json_encode(['status' => 'error', 'message' => 'Erro interno do servidor']);
}
?> 