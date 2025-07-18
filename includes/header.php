<?php
// includes/header.php - Estilo Boticário

// Buscar categorias dinâmicas dos produtos
$categorias_nav = [];
try {
    $produtos_response = listarProdutos();
    if ($produtos_response['status'] === 'success') {
        $produtos = $produtos_response['data']['data'];
        $categorias_temp = [];
        
        foreach ($produtos as $produto) {
            if (!empty($produto['categoria_id']) && !empty($produto['categoria_nome'])) {
                $categorias_temp[$produto['categoria_id']] = $produto['categoria_nome'];
            }
        }
        
        // Limitar a 5 categorias para não sobrecarregar a navegação
        $categorias_nav = array_slice($categorias_temp, 0, 5, true);
    }
} catch (Exception $e) {
    // Em caso de erro, usar categorias padrão
    $categorias_nav = [
        '1' => 'Imunidade',
        '2' => 'Diabetes',
        '3' => 'Emagrecedor'
    ];
}

// Buscar dados da loja para o logo dinâmico
// $lojinha_id = 14; // REMOVIDO: agora usa o valor já definido no index.php
$loja_dados_response = obterLojaDados($lojinha_id);
$logo_url = 'assets/images/logos/logo.png'; // padrão
if (
    isset($loja_dados_response['status']) && $loja_dados_response['status'] === 'success' &&
    isset($loja_dados_response['data']['status']) && $loja_dados_response['data']['status'] === 'success' &&
    isset($loja_dados_response['data']['data']['is_especial']) && $loja_dados_response['data']['data']['is_especial'] === 'S' &&
    !empty($loja_dados_response['data']['data']['logo_url'])
) {
    $logo_url = 'https://vitatop.tecskill.com.br/' . ltrim($loja_dados_response['data']['data']['logo_url'], '/');
}
$loja_dados = $loja_dados_response['data']['data'] ?? [];
$corPrincipal = $loja_dados['cor_principal'] ?? '#2c5530';
$corSecundaria = $loja_dados['cor_secundaria'] ?? '#41714d';
$whatsapp = $loja_dados['whatsapp'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>
<body>
    <!-- Barra do Distribuidor/Afiliado -->
    <?php $isEspecial = $loja_dados['is_especial'] ?? 'N'; ?>
    <div class="affiliate-bar"<?php if ($isEspecial === 'S'): ?> style="background: <?php echo $corPrincipal; ?>;"<?php endif; ?>>
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="affiliate-info">
                    <i class="fas fa-user-tie me-2"></i>
                    <span>Distribuidor Oficial: <strong><?php echo ucfirst(getAfiliado()); ?></strong></span>
                </div>
                <div class="contact-info d-none d-md-flex">
                    <span class="me-3">
                        <i class="fab fa-whatsapp me-1"></i>
                        <?php if ($whatsapp): ?>
                            <a href="https://wa.me/<?php echo preg_replace('/\D/', '', $whatsapp); ?>" target="_blank" class="text-success" style="text-decoration:none;">
                                <small style="color: #fff;"><?php echo formatTelefone($whatsapp); ?></small>
                            </a>
                        <?php else: ?>
                            <small style="color: #fff;">(11) 99999-9999</small>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Faixa de Frete Grátis -->
    <div class="frete-gratis-banner"<?php if ($isEspecial === 'S'): ?> style="background: <?php echo $corPrincipal; ?>; color: #fff;"<?php endif; ?>>
        <div class="content">
            <i class="fas fa-truck"></i>
            <span>Frete <span class="highlight">GRÁTIS</span> em compras acima de <span class="highlight">R$ 300</span></span>
            <i class="fas fa-gift"></i>
        </div>
    </div>

    <!-- Header Principal -->
    <header class="header-main">
        <div class="container">
            <div class="row align-items-center py-3">
                <!-- Menu Mobile Toggle -->
                <div class="col-2 d-lg-none">
                    <button class="btn btn-link p-0 menu-toggle" data-bs-toggle="offcanvas" data-bs-target="#menuMobile" aria-label="Menu">
                        <i class="fas fa-bars fs-4"></i>
                    </button>
                </div>
                
                <!-- Logo -->
                <div class="col-8 col-lg-3">
                    <a href="?page=home" class="logo-main">
                        <div class="d-flex align-items-center">
                            <div class="logo-text-container">
                                <img src="<?php echo $logo_url; ?>" alt="" width="150px">
                            </div>
                        </div>
                    </a>
                </div>
                
                <!-- Barra de Busca Central -->
                <div class="col-12 col-lg-6 order-3 order-lg-2 mt-3 mt-lg-0">
                    <div class="search-container">
                        <div class="search-bar">
                            <input type="text" class="form-control search-input" 
                                   placeholder="O que você procura hoje?" 
                                   id="searchInput" 
                                   autocomplete="off">
                            <button class="btn-search" type="button" aria-label="Buscar"<?php if ($isEspecial === 'S'): ?> style="background: <?php echo $corPrincipal; ?>; color: #fff;"<?php endif; ?>>
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <div id="searchResults" class="search-results"></div>
                    </div>
                </div>
                
                <!-- Ações do Header -->
                <div class="col-2 col-lg-3 order-2 order-lg-3">
                    <div class="header-actions">
                        
                        <!-- Login 
                        <div class="user-info d-none d-lg-flex align-items-center me-3">
                            <i class="fas fa-user text-primary me-2"></i>
                            <div>
                                <strong class="user-text">Minha Conta</strong>
                            </div>
                        </div>
                        -->
                        
                        <!-- Carrinho -->
                        <div class="cart-info">
                            <a href="?page=carrinho" class="cart-link">
                                <i class="fas fa-shopping-bag cart-icon"></i>
                                <div class="cart-text d-none d-lg-block">
                                    <small class="text-muted d-block">Sua</small>
                                    <strong>Sacola</strong>
                                </div>
                                <?php if (getCartCount() > 0): ?>
                                    <span class="cart-badge">
                                        <?php echo getCartCount(); ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        </div>
                        

                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Navegação Principal -->
    <nav class="navbar-main">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <div class="d-flex justify-content-center justify-content-lg-start">
                        <!-- Botão de Ofertas Especiais -->
                        <a href="?page=produtos&filter=promocao" class="promo-button"<?php if ($isEspecial === 'S'): ?> style="background: <?php echo $corPrincipal; ?>; color: #fff;"<?php endif; ?>>
                            <i class="fas fa-fire me-2"></i>
                            <span class="promo-text">Vita Promo</span>
                        </a>
                        
                        <!-- Menu de Navegação -->
                        <ul class="nav-menu d-none d-lg-flex">
                            <?php foreach ($categorias_nav as $cat_id => $cat_nome): ?>
                            <li class="nav-item">
                                <a href="?page=produtos&categoria=<?php echo $cat_id; ?>" class="nav-link">
                                    <?php echo htmlspecialchars($cat_nome); ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                            <li class="nav-item">
                                <a href="?page=produtos" class="nav-link">
                                    <i class="fas fa-th-large me-1"></i>
                                    Todos os Produtos
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Benefícios em Destaque 
    
    <div class="benefits-bar d-none d-lg-block">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="benefits-scroll">
                        <div class="benefit-item">
                            <i class="fas fa-trophy text-warning"></i>
                            <span>Mais Vendidos</span>
                        </div>
                        <div class="benefit-item">
                            <i class="fas fa-mobile-alt text-info"></i>
                            <span>Exclusivos da Loja</span>
                        </div>
                        <div class="benefit-item">
                            <i class="fas fa-gift text-success"></i>
                            <span>Beautybox</span>
                        </div>
                        <div class="benefit-item">
                            <i class="fas fa-percentage text-danger"></i>
                            <span>Cashback</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>-->

    <!-- Offcanvas Menu Mobile -->
    <div class="offcanvas offcanvas-start offcanvas-mobile" tabindex="-1" id="menuMobile" aria-labelledby="menuMobileLabel">
        <div class="offcanvas-header">
            <div class="d-flex align-items-center">
                <div class="logo-circle-small me-2">
                    <span class="logo-text-small">V</span>
                </div>
                <h5 class="offcanvas-title mb-0" id="menuMobileLabel">VitaTop</h5>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
        </div>
        <div class="offcanvas-body">
            <!-- Informações do Usuário -->
            <div class="user-section mb-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-user-circle fa-2x text-primary me-3"></i>
                    <div>
                        <strong>Olá! Entre na sua conta</strong>
                        <small class="d-block text-muted">Acesse sua conta ou cadastre-se</small>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <!-- Menu de Navegação -->
            <ul class="nav nav-pills flex-column mobile-nav">
                <li class="nav-item">
                    <a href="?page=home" class="nav-link">
                        <i class="fas fa-home me-3"></i>Início
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?page=produtos&filter=promocao" class="nav-link promo-link">
                        <i class="fas fa-fire me-3"></i>Vita Promo
                        <span class="badge bg-danger ms-2">Ofertas</span>
                    </a>
                </li>
                <?php foreach ($categorias_nav as $cat_id => $cat_nome): ?>
                <li class="nav-item">
                    <a href="?page=produtos&categoria=<?php echo $cat_id; ?>" class="nav-link">
                        <i class="fas fa-tag me-3"></i><?php echo htmlspecialchars($cat_nome); ?>
                    </a>
                </li>
                <?php endforeach; ?>
                <li class="nav-item">
                    <a href="?page=produtos" class="nav-link">
                        <i class="fas fa-th-large me-3"></i>Todos os Produtos
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?page=carrinho" class="nav-link">
                        <i class="fas fa-shopping-bag me-3"></i>Minha Sacola
                        <?php if (getCartCount() > 0): ?>
                            <span class="badge bg-primary ms-2"><?php echo getCartCount(); ?></span>
                        <?php endif; ?>
                    </a>
                </li>

            </ul>
            
            <hr>
            
            <!-- Informações do Distribuidor -->
            <div class="affiliate-info-mobile">
                <h6 class="text-muted mb-2">Seu Distribuidor:</h6>
                <div class="d-flex align-items-center">
                    <i class="fas fa-user-tie text-success me-2"></i>
                    <strong class="text-primary"><?php echo ucfirst(getAfiliado()); ?></strong>
                </div>
                <small class="text-muted">Distribuidor oficial VitaTop</small>
            </div>
        </div>
    </div>

    <!-- WhatsApp Float Button -->
    <div class="whatsapp-float d-block d-lg-none">
        <a href="#" class="whatsapp-btn">
            <i class="fab fa-whatsapp"></i>
            <span>Compre pelo WhatsApp</span>
        </a>
    </div>

    <!-- Conteúdo Principal -->
    <main class="main-content"><?php echo "\n"; ?>