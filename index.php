<?php
session_start();
require_once 'config/config.php';
require_once 'config/api.php';
require_once 'includes/functions.php';
require_once 'api/requests.php';

// Captura o afiliado da URL
$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);
$segments = explode('/', trim($path, '/'));

// Define o afiliado
$afiliado = (isset($segments[0], $segments[1]) && $segments[0] === 'lojinha_vitatop') ? $segments[1] : 'default';
$_SESSION['afiliado'] = $afiliado;

// Determina o lojinha_id de forma robusta
$lojinha_id = 14; // valor padrão
$loja_nao_encontrada = false;
if (!empty($_SESSION['afiliado'])) {
    if (is_numeric($_SESSION['afiliado'])) {
        $lojinha_id = (int)$_SESSION['afiliado'];
    } else {
        $lojinha_id = buscarIdLojinhaPorSlug($_SESSION['afiliado']);
        if ($lojinha_id === null || $lojinha_id === 0) {
            $loja_nao_encontrada = true;
        }
    }
}

if ($loja_nao_encontrada) {
    echo '<!DOCTYPE html><html lang="pt-br"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<title>Loja não encontrada</title>';
    echo '<style>body{background:#f8f9fa;font-family:sans-serif;margin:0;padding:0;} .notfound-container{max-width:500px;margin:80px auto;padding:40px 30px;background:#fff;border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,0.08);} .notfound-title{font-size:2.2rem;font-weight:700;color:#d9534f;margin-bottom:10px;} .notfound-msg{font-size:1.1rem;color:#555;margin-bottom:24px;} .notfound-icon{font-size:4rem;color:#d9534f;margin-bottom:16px;display:block;text-align:center;} .notfound-btn{display:inline-block;padding:10px 24px;background:#007bff;color:#fff;border-radius:6px;text-decoration:none;font-weight:600;transition:background 0.2s;} .notfound-btn:hover{background:#0056b3;}</style>';
    echo '</head><body><div class="notfound-container">';
    echo '<div class="notfound-icon">🚫</div>';
    echo '<div class="notfound-title">Loja não encontrada</div>';
    echo '<div class="notfound-msg">A loja que você tentou acessar não existe ou foi removida.<br>Verifique o endereço ou entre em contato com o suporte.</div>';
    echo '<a href="/" class="notfound-btn">Voltar para o início</a>';
    echo '</div></body></html>';
    exit;
}

if (empty($_SESSION['visita_registrada'])) {
    registrarVisitaLojinha($lojinha_id);
    $_SESSION['visita_registrada'] = true;
}

// Roteamento simples
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$produto_id = isset($_GET['produto']) ? $_GET['produto'] : null;

// Definir título da página
$page_title = 'VitaTop - Encapsulados Naturais';

switch($page) {
    case 'produtos':
        $page_title = 'Produtos - VitaTop';
        $template = 'pages/produtos.php';
        break;
    case 'produto':
        $page_title = 'Produto - VitaTop';
        $template = 'pages/produto.php';
        break;
    case 'carrinho':
        $page_title = 'Carrinho - VitaTop';
        $template = 'pages/carrinho.php';
        break;
    case 'checkout':
        $page_title = 'Finalizar Pedido - VitaTop';
        $template = 'pages/checkout.php';
        break;
    case 'pedido':
        $page_title = 'Status do Pedido - VitaTop';
        $template = 'pages/pedido.php';
        break;
    default:
        $template = 'pages/home.php';
}

// Incluir template
include 'includes/header.php';
include $template;
include 'includes/footer.php';
?>