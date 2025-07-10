<?php
// Configurações da API
define('API_URL', 'https://vitatop.tecskill.com.br/rest.php');
define('API_KEY', '50119e057567b086d83fe5dd18336042ff2cf7bef3c24807bc55e34dbe5a');

// Configurações opcionais
define('API_TIMEOUT', 30); // Timeout em segundos
define('API_LOG_ENABLED', false); // Log de requisições (para debug)

// Headers padrão para requisições
define('API_HEADERS', [
    'Content-Type: application/json',
    'Authorization: Basic ' . API_KEY,
    'User-Agent: VitaTop-Ecommerce/1.0'
]);

// Configurações de cache (opcional)
define('CACHE_PRODUTOS_TIME', 300); // 5 minutos
define('CACHE_ENABLED', false);