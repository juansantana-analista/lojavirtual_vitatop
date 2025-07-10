// api/calcular_parcelas.php
<?php
session_start();
require_once '../config/api.php';
require_once '../api/requests.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$valor = $input['valor'] ?? 0;

if ($valor <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Valor inválido']);
    exit;
}

$resultado = calcularParcelas(API_URL, API_KEY, $valor);

if ($resultado['status'] === 'success' && isset($resultado['data'])) {
    echo json_encode([
        'status' => 'success',
        'parcelas' => $resultado['data']
    ]);
} else {
    // Gerar parcelas padrão
    $parcelas = [];
    for ($i = 1; $i <= 12; $i++) {
        $juros = $i > 3 ? 0.05 : 0; // 5% de juros após 3x
        $valorParcela = $i > 3 ? ($valor * (1 + $juros)) / $i : $valor / $i;
        
        $parcelas[] = [
            'numero' => $i,
            'valor' => $valorParcela,
            'juros' => $juros,
            'total' => $valorParcela * $i
        ];
    }
    
    echo json_encode([
        'status' => 'success',
        'parcelas' => $parcelas
    ]);
}