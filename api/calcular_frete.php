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
    echo json_encode([
        'status' => 'success',
        'valor' => $resultado['data']['valor'] ?? 15.90,
        'prazo' => $resultado['data']['prazo'] ?? '5-7'
    ]);
} else {
    // Valores padrão em caso de erro
    echo json_encode([
        'status' => 'success',
        'valor' => 15.90,
        'prazo' => '5-7'
    ]);
}