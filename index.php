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
if (!empty($_SESSION['afiliado'])) {
    if (is_numeric($_SESSION['afiliado'])) {
        $lojinha_id = (int)$_SESSION['afiliado'];
    } else {
        $lojinha_id = buscarIdLojinhaPorSlug($_SESSION['afiliado']);
    }
}

// Debug explícito
if (php_sapi_name() !== 'cli') {
    echo '<pre style="background:#ffe;border:1px solid #ccc;padding:10px;">';
    echo 'DEBUG: $_SESSION["afiliado"] = ' . var_export($_SESSION['afiliado'], true) . "\n";
    echo 'DEBUG: lojinha_id usado = ' . var_export($lojinha_id, true) . "\n";
    echo '</pre>';
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