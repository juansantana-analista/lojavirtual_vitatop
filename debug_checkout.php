<?php
// debug_checkout.php - Arquivo temporário para testar a integração
session_start();
require_once 'config/api.php';
require_once 'api/requests.php';
require_once 'includes/functions.php';

// Simular dados de um pedido para teste
$dadosCliente = [
    'nome_completo' => 'João da Silva',
    'email' => 'joao@teste.com',
    'celular' => '11999999999',
    'tipo' => 'F',
    'cpfcnpj' => '10008907919',
    'inscricao_estadual' => '',
    'cep' => '01310100',
    'endereco' => 'Av. Paulista',
    'numero' => '1000',
    'complemento' => '',
    'bairro' => 'Bela Vista',
    'cidade' => 'São Paulo',
    'estado' => 'SP'
];

$dadosPedido = [
    'vendedor' => '001', // Altere para um código válido do seu sistema
    'opcao_pagamento' => 3, // PIX
    'total' => 150.00,
    'frete' => 15.90,
    'parcelas' => 1,
    'itens' => [
        [
            'produto_id' => 1,
            'qtde' => 2,
            'preco_unitario' => 67.05,
            'total' => 134.10
        ],
        [
            'produto_id' => 2,
            'qtde' => 1,
            'preco_unitario' => 15.90,
            'total' => 15.90
        ]
    ]
];

echo "<h2>Debug - Teste de Integração com PedidoDigitalRest</h2>";

echo "<h3>1. Dados que serão enviados:</h3>";
echo "<h4>Cliente:</h4>";
echo "<pre>" . json_encode($dadosCliente, JSON_PRETTY_PRINT) . "</pre>";

echo "<h4>Pedido:</h4>";
echo "<pre>" . json_encode($dadosPedido, JSON_PRETTY_PRINT) . "</pre>";

echo "<h3>2. Estrutura final da requisição:</h3>";
$requestData = [
    'class' => 'PedidoDigitalRest',
    'method' => 'GravarPedido',
    'dados' => [
        'cliente' => $dadosCliente,
        'pedido' => $dadosPedido
    ]
];

echo "<pre>" . json_encode($requestData, JSON_PRETTY_PRINT) . "</pre>";

echo "<h3>3. Testando envio para API:</h3>";

// Teste real
try {
    $resultado = enviarDadosCheckout(API_URL, API_KEY, $dadosCliente, $dadosPedido);
    
    if ($resultado) {
        echo "<div style='background: #d4edda; padding: 10px; border: 1px solid #c3e6cb; margin: 10px 0;'>";
        echo "<strong>✅ Sucesso!</strong><br>";
        echo "Resposta da API:<br>";
        
        $resposta = json_decode($resultado, true);
        if ($resposta) {
            echo "<pre>" . json_encode($resposta, JSON_PRETTY_PRINT) . "</pre>";
            
            if (isset($resposta['status']) && $resposta['status'] === 'success') {
                echo "<br><strong>Status:</strong> " . $resposta['status'];
                echo "<br><strong>Mensagem:</strong> " . $resposta['message'];
                
                if (isset($resposta['data'])) {
                    echo "<br><strong>Dados retornados:</strong>";
                    echo "<pre>" . json_encode($resposta['data'], JSON_PRETTY_PRINT) . "</pre>";
                }
            }
        } else {
            echo "<strong>Resposta bruta:</strong><br>";
            echo "<pre>" . htmlspecialchars($resultado) . "</pre>";
        }
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; margin: 10px 0;'>";
        echo "<strong>❌ Erro!</strong><br>";
        echo "A função retornou false. Verifique os logs.";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 10px; border: 1px solid #f5c6cb; margin: 10px 0;'>";
    echo "<strong>❌ Exceção capturada:</strong><br>";
    echo htmlspecialchars($e->getMessage());
    echo "</div>";
}

echo "<h3>4. Verificar logs:</h3>";
$logFile = 'logs/checkout_debug_' . date('Y-m-d') . '.log';
if (file_exists($logFile)) {
    echo "<strong>Log encontrado:</strong> $logFile<br>";
    echo "<textarea rows='20' cols='100' readonly>";
    echo htmlspecialchars(file_get_contents($logFile));
    echo "</textarea>";
} else {
    echo "<em>Nenhum log encontrado ainda.</em>";
}

echo "<h3>5. Testar configurações:</h3>";
echo "<strong>API_URL:</strong> " . (defined('API_URL') ? API_URL : 'NÃO DEFINIDA') . "<br>";
echo "<strong>API_KEY:</strong> " . (defined('API_KEY') ? '***' . substr(API_KEY, -4) : 'NÃO DEFINIDA') . "<br>";

echo "<h3>6. Testar conectividade básica:</h3>";
$ch = curl_init(API_URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response !== false) {
    echo "<span style='color: green;'>✅ Conectividade OK - HTTP $httpCode</span>";
} else {
    echo "<span style='color: red;'>❌ Erro de conectividade</span>";
}

?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
h3 { color: #007bff; margin-top: 30px; }
h4 { color: #6c757d; }
pre { background: #f8f9fa; padding: 15px; border: 1px solid #e9ecef; border-radius: 5px; overflow-x: auto; }
textarea { background: #f8f9fa; border: 1px solid #e9ecef; padding: 10px; }
</style>