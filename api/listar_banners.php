<?php
// api/listar_banners.php - Versão com Debug Melhorado
session_start();
require_once '../api/requests.php';

// Função para log de debug
function logDebug($message, $data = null) {
    $logEntry = date('Y-m-d H:i:s') . ' - ' . $message;
    if ($data !== null) {
        $logEntry .= ' - ' . json_encode($data);
    }
    error_log($logEntry);
}

header('Content-Type: application/json');

// Log início da requisição
logDebug('BANNERS API - Início da requisição');

try {
    // Obter dados de entrada
    $input = json_decode(file_get_contents('php://input'), true);
    $nomeLoja = $_SESSION['afiliado'] ?? null;
    
    logDebug('BANNERS API - Nome da loja (sessão)', $nomeLoja);
    logDebug('BANNERS API - Input recebido', $input);
    logDebug('BANNERS API - Sessão completa', $_SESSION);
    
    if (empty($nomeLoja)) {
        logDebug('BANNERS API - ERRO: Nome da loja vazio');
        echo json_encode([
            'status' => 'error', 
            'message' => 'Nome da loja é obrigatório',
            'debug' => [
                'session_afiliado' => $_SESSION['afiliado'] ?? 'não definido',
                'session_keys' => array_keys($_SESSION)
            ]
        ]);
        exit;
    }
    
    logDebug('BANNERS API - Chamando função listarBanners', $nomeLoja);
    
    // Chamar função para listar banners
    $resultado = listarBanners($nomeLoja);
    
    logDebug('BANNERS API - Resultado da função listarBanners', $resultado);
    
    // Verificar resultado
    if ($resultado && 
        isset($resultado['status']) && 
        $resultado['status'] === 'success' &&
        isset($resultado['data']) &&
        isset($resultado['data']['status']) &&
        $resultado['data']['status'] === 'success') {
        
        logDebug('BANNERS API - Sucesso na resposta da API');
        
        // Verificar se há dados de banners
        if (isset($resultado['data']['data']) && is_array($resultado['data']['data'])) {
            
            logDebug('BANNERS API - Banners encontrados', count($resultado['data']['data']));
            
            // Extrair apenas as URLs
            $urls = array_map(function($banner) {
                logDebug('BANNERS API - Processando banner', $banner);
                return $banner['url_arquivo'] ?? $banner['url'] ?? null;
            }, $resultado['data']['data']);
            
            // Filtrar URLs válidas
            $urls = array_filter($urls, function($url) {
                return !empty($url);
            });
            
            logDebug('BANNERS API - URLs extraídas', $urls);
            
            if (count($urls) > 0) {
                echo json_encode([
                    'status' => 'success', 
                    'images' => array_values($urls),
                    'debug' => [
                        'total_banners' => count($urls),
                        'loja' => $nomeLoja,
                        'fonte' => 'api'
                    ]
                ]);
            } else {
                logDebug('BANNERS API - Nenhuma URL válida encontrada');
                echo json_encode([
                    'status' => 'error', 
                    'message' => 'Nenhuma URL de banner válida encontrada',
                    'debug' => [
                        'banners_raw' => $resultado['data']['data'],
                        'loja' => $nomeLoja
                    ]
                ]);
            }
        } else {
            logDebug('BANNERS API - Dados de banners não encontrados na resposta');
            echo json_encode([
                'status' => 'error', 
                'message' => 'Dados de banners não encontrados na resposta',
                'debug' => [
                    'resposta_completa' => $resultado,
                    'loja' => $nomeLoja
                ]
            ]);
        }
        
    } else {
        logDebug('BANNERS API - Erro na resposta da API externa');
        echo json_encode([
            'status' => 'error', 
            'message' => 'Erro ao carregar banners da API externa',
            'debug' => [
                'resposta_api' => $resultado,
                'loja' => $nomeLoja,
                'erro_detalhe' => $resultado['message'] ?? 'Erro desconhecido'
            ]
        ]);
    }
    
} catch (Exception $e) {
    logDebug('BANNERS API - EXCEÇÃO: ' . $e->getMessage());
    echo json_encode([
        'status' => 'error', 
        'message' => 'Erro interno do servidor',
        'debug' => [
            'erro' => $e->getMessage(),
            'linha' => $e->getLine(),
            'arquivo' => $e->getFile()
        ]
    ]);
}

logDebug('BANNERS API - Fim da requisição');
?>