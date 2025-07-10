<?php
// config/config.php
define('SITE_URL', 'http://localhost');
define('SITE_NAME', 'VitaTop - Encapsulados Naturais');

// Configurações do carrinho
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}
