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

// Normaliza o afiliado para busca (remove acentos para URL)
$afiliado_normalizado = generateSlug($afiliado);

// Determina o lojinha_id de forma robusta
$lojinha_id = 14; // valor padrão
$loja_nao_encontrada = false;
if (!empty($_SESSION['afiliado'])) {
    if (is_numeric($_SESSION['afiliado'])) {
        $lojinha_id = (int)$_SESSION['afiliado'];
    } else {
        $lojinha_id = buscarIdLojinhaPorSlug($afiliado);
        if ($lojinha_id === null || $lojinha_id === 0) {
            $loja_nao_encontrada = true;
        }
    }
}

if ($loja_nao_encontrada) {
    echo '<!DOCTYPE html><html lang="pt-br"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<title>Loja não encontrada</title>';
    echo '<style>body{background:#f8f9fa;font-family:sans-serif;margin:0;padding:0;} .notfound-container{max-width:520px;margin:80px auto;padding:48px 32px;background:#fff;border-radius:16px;box-shadow:0 6px 32px rgba(0,0,0,0.10);} .notfound-logo{text-align:center;margin-bottom:24px;} .notfound-logo img{max-width:180px;} .notfound-title{font-size:2.3rem;font-weight:800;color:#1a1a1a;margin-bottom:12px;text-align:center;} .notfound-msg{font-size:1.15rem;color:#444;margin-bottom:28px;text-align:center;line-height:1.6;} .notfound-sugestao{font-size:1rem;color:#888;text-align:center;margin-bottom:0;} .notfound-emoji{text-align:center;font-size:3.5rem;margin-bottom:18px;}@media(max-width:600px){.notfound-container{padding:28px 8px;}}</style>';
    echo '</head><body><div class="notfound-container">';
    echo '<div class="notfound-logo"><img src="assets/images/logos/logo.png" alt="VitaTop"></div>';
    echo '<div class="notfound-emoji">😕</div>';
    echo '<div class="notfound-title">Ops! Loja não encontrada</div>';
    echo '<div class="notfound-msg">A loja que você tentou acessar não está disponível no momento.<br>Isso pode acontecer se o endereço estiver incorreto ou se a loja foi desativada.<br><br><strong>Que tal explorar outras oportunidades de bem-estar na VitaTop?</strong></div>';
    echo '<div class="notfound-sugestao">Se precisar de ajuda, nosso time está pronto para te apoiar. Conte com a gente para cuidar da sua saúde e felicidade! 💚</div>';
    echo '</div></body></html>';
    exit;
}

if (!isset($_SESSION['visita_registrada']) || !is_array($_SESSION['visita_registrada'])) {
    $_SESSION['visita_registrada'] = [];
}
if (empty($_SESSION['visita_registrada'][$lojinha_id])) {
    registrarVisitaLojinha($lojinha_id);
    $_SESSION['visita_registrada'][$lojinha_id] = true;
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