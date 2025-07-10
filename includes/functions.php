<?php
// Funções auxiliares para o sistema

/**
 * Formatar preço no padrão brasileiro
 */
function formatPrice($price) {
    return 'R$ ' . number_format((float)$price, 2, ',', '.');
}

/**
 * Obter o afiliado atual da sessão
 */
function getAfiliado() {
    return isset($_SESSION['afiliado']) ? $_SESSION['afiliado'] : 'default';
}

/**
 * Adicionar produto ao carrinho
 */
function addToCart($produto_id, $quantidade = 1) {
    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = [];
    }
    
    if (!isset($_SESSION['carrinho'][$produto_id])) {
        $_SESSION['carrinho'][$produto_id] = 0;
    }
    
    $_SESSION['carrinho'][$produto_id] += $quantidade;
    return true;
}

/**
 * Remover produto do carrinho
 */
function removeFromCart($produto_id) {
    if (isset($_SESSION['carrinho'][$produto_id])) {
        unset($_SESSION['carrinho'][$produto_id]);
        return true;
    }
    return false;
}

/**
 * Atualizar quantidade no carrinho
 */
function updateCartQuantity($produto_id, $quantidade) {
    if ($quantidade > 0) {
        $_SESSION['carrinho'][$produto_id] = $quantidade;
    } else {
        removeFromCart($produto_id);
    }
    return true;
}

/**
 * Contar itens no carrinho
 */
function getCartCount() {
    if (!isset($_SESSION['carrinho'])) {
        return 0;
    }
    return array_sum($_SESSION['carrinho']);
}

/**
 * Calcular total do carrinho
 */
function calculateCartTotal($produtos) {
    if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) {
        return 0;
    }
    
    $total = 0;
    foreach ($_SESSION['carrinho'] as $produto_id => $quantidade) {
        foreach ($produtos as $produto) {
            if ($produto['id'] == $produto_id) {
                $total += (float)$produto['preco_lojavirtual'] * $quantidade;
                break;
            }
        }
    }
    return $total;
}

/**
 * Limpar carrinho
 */
function clearCart() {
    $_SESSION['carrinho'] = [];
    return true;
}

/**
 * Validar CPF
 */
function validarCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
    if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }
    
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }
    
    return true;
}

/**
 * Validar email
 */
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Sanitizar string
 */
function sanitizeString($string) {
    return htmlspecialchars(trim($string), ENT_QUOTES, 'UTF-8');
}

/**
 * Gerar slug para URL amigável
 */
function generateSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[áàâãäª]/', 'a', $string);
    $string = preg_replace('/[éèêë]/', 'e', $string);
    $string = preg_replace('/[íìîï]/', 'i', $string);
    $string = preg_replace('/[óòôõöº]/', 'o', $string);
    $string = preg_replace('/[úùûü]/', 'u', $string);
    $string = preg_replace('/[ç]/', 'c', $string);
    $string = preg_replace('/[^a-z0-9\s]/', '', $string);
    $string = preg_replace('/\s+/', '-', $string);
    return trim($string, '-');
}

/**
 * Calcular desconto percentual
 */
function calcularDesconto($preco_original, $preco_promocional) {
    if ($preco_original <= 0) return 0;
    return round((($preco_original - $preco_promocional) / $preco_original) * 100);
}

/**
 * Verificar se produto está em promoção
 */
function isPromocao($produto) {
    return isset($produto['preco2']) && 
           (float)$produto['preco_lojavirtual'] < (float)$produto['preco2'];
}

/**
 * Formatar telefone
 */
function formatTelefone($telefone) {
    $telefone = preg_replace('/[^0-9]/', '', $telefone);
    
    if (strlen($telefone) == 11) {
        return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone);
    } elseif (strlen($telefone) == 10) {
        return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $telefone);
    }
    
    return $telefone;
}

/**
 * Formatar CEP
 */
function formatCEP($cep) {
    $cep = preg_replace('/[^0-9]/', '', $cep);
    if (strlen($cep) == 8) {
        return preg_replace('/(\d{5})(\d{3})/', '$1-$2', $cep);
    }
    return $cep;
}

/**
 * Gerar código único para pedido
 */
function generateOrderCode() {
    return 'VT' . date('Ymd') . rand(1000, 9999);
}

/**
 * Converter array para XML (para integração)
 */
function arrayToXml($array, $rootElement = 'root', $xml = null) {
    if ($xml === null) {
        $xml = new SimpleXMLElement("<{$rootElement}/>");
    }
    
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            arrayToXml($value, $key, $xml->addChild($key));
        } else {
            $xml->addChild($key, htmlspecialchars($value));
        }
    }
    
    return $xml->asXML();
}

/**
 * Log de atividades
 */
function logActivity($action, $data = []) {
    $log = [
        'timestamp' => date('Y-m-d H:i:s'),
        'afiliado' => getAfiliado(),
        'action' => $action,
        'data' => $data,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ];
    
    if (!file_exists('logs')) {
        mkdir('logs', 0755, true);
    }
    
    file_put_contents('logs/activity_' . date('Y-m-d') . '.log', 
        json_encode($log) . "\n", FILE_APPEND | LOCK_EX);
}

/**
 * Verificar se é mobile
 */
function isMobile() {
    return isset($_SERVER['HTTP_USER_AGENT']) && 
           preg_match('/Mobile|Android|iPhone|iPad/', $_SERVER['HTTP_USER_AGENT']);
}

/**
 * Redirect com código de status
 */
function redirect($url, $code = 302) {
    header("Location: $url", true, $code);
    exit();
}

/**
 * Exibir mensagem de erro/sucesso
 */
function showMessage($message, $type = 'info') {
    $_SESSION['message'] = [
        'text' => $message,
        'type' => $type
    ];
}

/**
 * Obter e limpar mensagem da sessão
 */
function getMessage() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        unset($_SESSION['message']);
        return $message;
    }
    return null;
}

/**
 * Cache simples para requisições da API
 */
function getCachedData($key, $callback, $ttl = 300) {
    if (!CACHE_ENABLED) {
        return $callback();
    }
    
    $cacheFile = "cache/{$key}.json";
    
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $ttl) {
        return json_decode(file_get_contents($cacheFile), true);
    }
    
    $data = $callback();
    
    if (!file_exists('cache')) {
        mkdir('cache', 0755, true);
    }
    
    file_put_contents($cacheFile, json_encode($data));
    
    return $data;
}

/**
 * Limpar cache
 */
function clearCache($pattern = '*') {
    $files = glob("cache/{$pattern}.json");
    foreach ($files as $file) {
        unlink($file);
    }
}

/**
 * Buscar produtos sugeridos para frete grátis
 */
function getProdutosSugeridos($total_atual, $limite_frete_gratis = 300, $limite_produtos = 4) {
    // Buscar todos os produtos
    $produtos_response = listarProdutos();
    if ($produtos_response['status'] !== 'success') {
        return [];
    }
    
    $todos_produtos = $produtos_response['data']['data'];
    $produtos_carrinho = array_keys($_SESSION['carrinho'] ?? []);
    
    // Filtrar produtos que não estão no carrinho
    $produtos_disponiveis = array_filter($todos_produtos, function($produto) use ($produtos_carrinho) {
        return !in_array($produto['id'], $produtos_carrinho);
    });
    
    // Calcular quanto falta para frete grátis
    $falta_para_frete = $limite_frete_gratis - $total_atual;
    
    if ($falta_para_frete <= 0) {
        return []; // Já tem frete grátis
    }
    
    // Ordenar produtos por relevância para frete grátis
    usort($produtos_disponiveis, function($a, $b) use ($falta_para_frete) {
        $preco_a = (float)$a['preco_lojavirtual'];
        $preco_b = (float)$b['preco_lojavirtual'];
        
        // Priorizar produtos que completam o frete grátis
        $completa_a = $preco_a >= $falta_para_frete;
        $completa_b = $preco_b >= $falta_para_frete;
        
        if ($completa_a && !$completa_b) return -1;
        if (!$completa_a && $completa_b) return 1;
        
        // Se ambos completam ou não completam, priorizar os mais baratos
        return $preco_a <=> $preco_b;
    });
    
    // Retornar apenas os primeiros produtos
    return array_slice($produtos_disponiveis, 0, $limite_produtos);
}

/**
 * Gerar desconto visual aleatório para produtos (efeito visual apenas)
 */
function getDescontoVisual($produto_id) {
    // Usar o ID do produto como seed para gerar desconto consistente
    $seed = crc32($produto_id);
    srand($seed);
    
    // Gerar desconto entre 5% e 30%
    $desconto = rand(5, 30);
    
    // Reset do seed
    srand();
    
    return $desconto;
}

/**
 * Calcular preço com desconto visual (apenas para exibição)
 */
function getPrecoComDescontoVisual($preco_original, $produto_id) {
    $desconto = getDescontoVisual($produto_id);
    $valor_desconto = ($preco_original * $desconto) / 100;
    return $preco_original + $valor_desconto;
}

/**
 * Formatar preço com desconto visual
 */
function formatPriceWithDiscount($preco_original, $produto_id) {
    $preco_com_desconto = getPrecoComDescontoVisual($preco_original, $produto_id);
    $desconto = getDescontoVisual($produto_id);
    
    return [
        'preco_original' => formatPrice($preco_com_desconto),
        'preco_com_desconto' => formatPrice($preco_original),
        'desconto_percentual' => $desconto,
        'valor_desconto' => formatPrice($preco_com_desconto - $preco_original)
    ];
}