<?php
// api/verificar_pagamento.php
session_start();
require_once '../api/requests.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$pedido_id = $input['pedido_id'] ?? '';

if (empty($pedido_id)) {
    echo json_encode(['status' => 'error', 'message' => 'ID do pedido é obrigatório']);
    exit;
}

$resultado = verificarPagamento(API_URL, API_KEY, $pedido_id);

if ($resultado['status'] === 'success' && $resultado['data']['status'] === 'success') {
    echo json_encode([
        'status' => $resultado['data']['data']['status_compra'] ?? 'pending',
        'message' => $resultado['data']['message'] ?? 'Status verificado'
    ]);
} else {
    echo json_encode([
        'status' => 'pending',
        'message' => 'Aguardando confirmação'
    ]);
}