// api/calcular_frete.php
<?php
session_start();
require_once '../config/api.php';
require_once '../api/requests.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$cep = $input['cep'] ?? '';
$valor = $input['valor'] ?? 0;

if (empty($cep) || $valor <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'CEP e valor são obrigatórios']);
    exit;
}

$resultado = calcularFrete(API_URL, API_KEY, $cep, $valor);

if ($resultado['status'] === 'success') {
    // Repasse toda a estrutura da API, inclusive o campo frete
    echo json_encode([
        'status' => 'success',
        'data' => $resultado['data']
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro ao calcular frete'
    ]);
}