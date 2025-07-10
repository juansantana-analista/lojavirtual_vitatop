<?php
// api/listar_banners.php
session_start();
require_once '../api/requests.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$nomeLoja = $_SESSION['afiliado'] ?? null;

if (empty($nomeLoja)) {
    echo json_encode(['status' => 'error', 'message' => 'Nome da loja é obrigatório']);
    exit;
}

$resultado = listarBanners($nomeLoja);

if (
    $resultado['status'] === 'success' &&
    $resultado['data']['status'] === 'success'
) {
    // Extrair apenas as URLs
    $urls = array_map(function($banner) {
        return $banner['url_arquivo'];
    }, $resultado['data']['data']);

    echo json_encode(['status' => 'success', 'images' => $urls]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao carregar banners']);
}
