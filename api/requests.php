<?php
// Dispatcher para requisições genéricas (class/method)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['class']) && isset($input['method'])) {
        require_once __DIR__ . '/../config/api.php';
        $requestData = $input;
        $ch = curl_init(API_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic ' . API_KEY
        ]);
        $response = curl_exec($ch);
        if ($response === false) {
            echo json_encode(['status' => 'error', 'message' => curl_error($ch)]);
        } else {
            $jsonStart = strpos($response, '{');
            if ($jsonStart !== false) {
                $response = substr($response, $jsonStart);
            }
            echo $response;
        }
        curl_close($ch);
        exit;
    }
}
// api/requests.php - Adequado para PedidoDigitalRest
require_once __DIR__ . '/../config/api.php';

// Suas funções originais (mantidas)
function calcularFrete($location, $rest_key, $cep, $valor)
{
    $dados = [
        'cep' => $cep,
        'valor' => $valor
    ];
    
    $requestData = [
        'class' => 'PedidoDigitalRest',
        'method' => 'CalcularFrete',
        'dados' => $dados
    ];

    $ch = curl_init($location);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic ' . $rest_key
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['status' => 'error', 'message' => $error];
    }

    $result = json_decode($response, true);
    curl_close($ch);

    return $result;
}

function detalhesPedido($location, $rest_key, $codigoPedido)
{
    $dados = [
        'codigo_pedido' => $codigoPedido
    ];
    
    $requestData = [
        'class' => 'PedidoDigitalRest',
        'method' => 'ListarPedidoDigital',
        'dados' => $dados
    ];

    $ch = curl_init($location);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic ' . $rest_key
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['status' => 'error', 'message' => $error];
    }

    $result = json_decode($response, true);
    curl_close($ch);

    return $result;
}

function calcularParcelas($location, $rest_key, $valor)
{
    $dados = [
        'valor' => $valor
    ];
    
    $requestData = [
        'class' => 'PedidoDigitalRest',
        'method' => 'Parcelamento',
        'dados' => $dados
    ];

    $ch = curl_init($location);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic ' . $rest_key
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['status' => 'error', 'message' => $error];
    }

    $result = json_decode($response, true);
    curl_close($ch);

    return $result;
}

// FUNÇÃO PRINCIPAL CORRIGIDA PARA A GravarPedido
function enviarDadosCheckout($location, $rest_key, $dataCliente, $dataPedido)
{
    // Debug: Log dos dados enviados
    logActivity('envio_checkout', [
        'cliente' => $dataCliente,
        'pedido' => $dataPedido
    ]);

    // Estrutura EXATA que a função GravarPedido espera
    $requestData = [
        'class' => 'PedidoDigitalRest',
        'method' => 'GravarPedido',
        'dados' => [
            'cliente' => [
                'nome_completo' => $dataCliente['nome_completo'],
                'email' => $dataCliente['email'],
                'celular' => $dataCliente['celular'],
                'tipo' => $dataCliente['tipo'],  // 'F' para pessoa física
                'cpfcnpj' => $dataCliente['cpfcnpj'],
                'inscricao_estadual' => $dataCliente['inscricao_estadual'] ?? '',
                'cep' => $dataCliente['cep'],
                'endereco' => $dataCliente['endereco'],
                'numero' => $dataCliente['numero'],
                'complemento' => $dataCliente['complemento'] ?? '',
                'bairro' => $dataCliente['bairro'],
                'cidade' => $dataCliente['cidade'],    // Nome da cidade
                'estado' => $dataCliente['estado']     // Sigla do estado (ex: SP, RJ)
            ],
            'pedido' => [
                'nome_loja' => $dataPedido['nome_loja'],           // codigo_indicador do vendedor
                'opcao_pagamento' => $dataPedido['opcao_pagamento'], // 1=Cartão, 2=Boleto, 3=PIX
                'total' => (float)$dataPedido['total'],
                'frete' => (float)$dataPedido['frete'],
                'parcelas' => (int)$dataPedido['parcelas'],
                'itens' => $dataPedido['itens'],
                
                // Campos opcionais para cartão de crédito (quando opcao_pagamento = 1)
                'nome_cartao' => $dataPedido['nome_cartao'] ?? '',
                'numero_cartao' => $dataPedido['numero_cartao'] ?? '',
                'data_expira' => $dataPedido['data_expira'] ?? '',
                'codigo_seguranca' => $dataPedido['codigo_seguranca'] ?? ''
            ]
        ]
    ];

    // Debug: Log da estrutura final
    logActivity('estrutura_final', $requestData);

    $ch = curl_init($location);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic ' . $rest_key
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60); // Timeout maior para processamento de pagamento

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        
        // Log do erro
        logActivity('curl_error', ['erro' => $error]);
        
        return false;
    }

    curl_close($ch);

    // Debug: Log da resposta
    logActivity('resposta_api', [
        'http_code' => $httpCode,
        'response' => $response
    ]);

    // Limpar a resposta de possíveis notices/warnings PHP
    $cleanResponse = $response;
    
    // Procurar pelo início do JSON válido
    $jsonStart = strpos($response, '{');
    if ($jsonStart !== false) {
        $cleanResponse = substr($response, $jsonStart);
        
        // Log da limpeza se necessário
        if ($jsonStart > 0) {
            logActivity('response_cleaned', [
                'original_length' => strlen($response),
                'clean_length' => strlen($cleanResponse),
                'removed_content' => substr($response, 0, $jsonStart)
            ]);
        }
    }
    
    // Verificar se a resposta limpa é um JSON válido
    $responseData = json_decode($cleanResponse, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        logActivity('json_decode_error', [
            'error' => json_last_error_msg(),
            'original_response' => $response,
            'clean_response' => $cleanResponse
        ]);
        return false;
    }

    return $cleanResponse;
}

function verificarPagamento($location, $rest_key, $pedido_id)
{
    $requestData = [
        'class' => 'PedidoDigitalRest',
        'method' => 'VerificaPix',
        'pedido_id' => $pedido_id
    ];

    $ch = curl_init($location);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic ' . $rest_key
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['status' => 'error', 'message' => $error];
    }

    $result = json_decode($response, true);
    curl_close($ch);

    return $result;
}

// Novas funções específicas para o e-commerce (mantidas)
function listarProdutos() {
    $requestData = [
        'class' => 'ProdutoVariacaoRest',
        'method' => 'listarProdutos'
    ];

    $ch = curl_init(API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic ' . API_KEY
    ]);

    $response = curl_exec($ch);
    
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['status' => 'error', 'message' => $error];
    }

    $result = json_decode($response, true);
    curl_close($ch);
    
    return $result;
}

function getProdutoPorId($produto_id) {
    $requestData = [
        'class' => 'ProdutoVariacaoRest',
        'method' => 'getProduto',
        'produto_id' => $produto_id
    ];

    $ch = curl_init(API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic ' . API_KEY
    ]);

    $response = curl_exec($ch);
    
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['status' => 'error', 'message' => $error];
    }

    $result = json_decode($response, true);
    curl_close($ch);
    
    return $result;
}

function buscarProdutos($termo) {
    $requestData = [
        'class' => 'ProdutoVariacaoRest',
        'method' => 'buscarProdutos',
        'termo' => $termo
    ];

    $ch = curl_init(API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic ' . API_KEY
    ]);

    $response = curl_exec($ch);
    
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['status' => 'error', 'message' => $error];
    }

    $result = json_decode($response, true);
    curl_close($ch);
    
    return $result;
}

function listarBanners($nomeLoja) {
    $requestData = [
        'class' => 'LojinhaBannerRest',
        'method' => 'listarBannersAtivos',
        'nome_loja' => $nomeLoja
    ];

    $ch = curl_init(API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic ' . API_KEY
    ]);

    $response = curl_exec($ch);
    
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['status' => 'error', 'message' => $error];
    }

    $result = json_decode($response, true);
    curl_close($ch);
    
    return $result;
}

function listarCategorias() {
    $requestData = [
        'class' => 'CategoriaRest',
        'method' => 'listarCategorias'
    ];

    $ch = curl_init(API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic ' . API_KEY
    ]);

    $response = curl_exec($ch);
    
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['status' => 'error', 'message' => $error];
    }

    $result = json_decode($response, true);
    curl_close($ch);
    
    return $result;
}

function listarCategoriasLojinha($lojinha_id) {
    $requestData = [
        'class' => 'LojinhaRestService',
        'method' => 'listarCategoriasLojinha',
        'lojinha_vitatop_id' => $lojinha_id
    ];

    $ch = curl_init(API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic ' . API_KEY
    ]);

    $response = curl_exec($ch);
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['status' => 'error', 'message' => $error];
    }
    $result = json_decode($response, true);
    curl_close($ch);
    return $result;
}

function registrarVisitaLojinha($lojinha_id) {
    $requestData = [
        'class' => 'LojinhaRestService',
        'method' => 'registrarVisitaLojinha',
        'lojinha_vitatop_id' => $lojinha_id
    ];

    $ch = curl_init(API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic ' . API_KEY
    ]);

    $response = curl_exec($ch);
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['status' => 'error', 'message' => $error];
    }
    $result = json_decode($response, true);
    curl_close($ch);
    return $result;
}

// Função auxiliar para validar dados antes do envio
function validarDadosCheckout($dataCliente, $dataPedido) {
    $erros = [];
    
    // Validações do cliente
    if (empty($dataCliente['nome_completo'])) {
        $erros[] = "Nome completo é obrigatório";
    }
    
    if (empty($dataCliente['email']) || !filter_var($dataCliente['email'], FILTER_VALIDATE_EMAIL)) {
        $erros[] = "E-mail válido é obrigatório";
    }
    
    if (empty($dataCliente['cpfcnpj'])) {
        $erros[] = "CPF/CNPJ é obrigatório";
    }
    
    if (empty($dataCliente['cep'])) {
        $erros[] = "CEP é obrigatório";
    }
    
    if (empty($dataCliente['endereco'])) {
        $erros[] = "Endereço é obrigatório";
    }
    
    if (empty($dataCliente['cidade'])) {
        $erros[] = "Cidade é obrigatória";
    }
    
    if (empty($dataCliente['estado'])) {
        $erros[] = "Estado é obrigatório";
    }
    
    // Validações do pedido
    if (empty($dataPedido['itens']) || !is_array($dataPedido['itens'])) {
        $erros[] = "Itens do pedido são obrigatórios";
    }
    
    if (empty($dataPedido['opcao_pagamento']) || !in_array($dataPedido['opcao_pagamento'], [1, 2, 3])) {
        $erros[] = "Forma de pagamento inválida";
    }
    
    if (empty($dataPedido['total']) || $dataPedido['total'] <= 0) {
        $erros[] = "Total do pedido inválido";
    }
    
    // Validação do vendedor
    if (empty($dataPedido['vendedor'])) {
        $erros[] = "Código do vendedor é obrigatório";
    }
    
    // Validações específicas para cartão de crédito
    if ($dataPedido['opcao_pagamento'] == 1) {
        if (empty($dataPedido['nome_cartao'])) {
            $erros[] = "Nome no cartão é obrigatório";
        }
        
        if (empty($dataPedido['numero_cartao'])) {
            $erros[] = "Número do cartão é obrigatório";
        }
        
        if (empty($dataPedido['data_expira'])) {
            $erros[] = "Data de expiração é obrigatória";
        }
        
        if (empty($dataPedido['codigo_seguranca'])) {
            $erros[] = "Código de segurança é obrigatório";
        }
    }
    
    return $erros;
}

// Função para limpar dados (remover caracteres especiais)
function limparDados($dados) {
    if (isset($dados['cpfcnpj'])) {
        $dados['cpfcnpj'] = preg_replace('/[^0-9]/', '', $dados['cpfcnpj']);
    }
    
    if (isset($dados['cep'])) {
        $dados['cep'] = preg_replace('/[^0-9]/', '', $dados['cep']);
    }
    
    if (isset($dados['celular'])) {
        $dados['celular'] = preg_replace('/[^0-9]/', '', $dados['celular']);
    }
    
    return $dados;
}

// Função melhorada para enviar dados do checkout com validações
function enviarDadosCheckoutComValidacao($location, $rest_key, $dataCliente, $dataPedido) {
    // Limpar dados
    $dataCliente = limparDados($dataCliente);
    
    // Validar dados
    $erros = validarDadosCheckout($dataCliente, $dataPedido);
    
    if (!empty($erros)) {
        return [
            'status' => 'error',
            'message' => 'Dados inválidos',
            'errors' => $erros
        ];
    }
    
    // Se passou nas validações, enviar dados
    return enviarDadosCheckout($location, $rest_key, $dataCliente, $dataPedido);
}

// Função para buscar endereço por CEP (opcional, para integração com ViaCEP)
function buscarEnderecoPorCep($cep) {
    $cep = preg_replace('/[^0-9]/', '', $cep);
    
    if (strlen($cep) != 8) {
        return ['status' => 'error', 'message' => 'CEP inválido'];
    }
    
    $url = "https://viacep.com.br/ws/{$cep}/json/";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if ($response === false || $httpCode != 200) {
        curl_close($ch);
        return ['status' => 'error', 'message' => 'Erro ao consultar CEP'];
    }
    
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    if (isset($data['erro'])) {
        return ['status' => 'error', 'message' => 'CEP não encontrado'];
    }
    
    return [
        'status' => 'success',
        'data' => [
            'endereco' => $data['logradouro'],
            'bairro' => $data['bairro'],
            'cidade' => $data['localidade'],
            'estado' => $data['uf'],
            'cep' => $data['cep']
        ]
    ];
}
