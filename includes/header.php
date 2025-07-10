<?php
// includes/header.php - Estilo Boticário
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
    <div class="affiliate-bar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="affiliate-info">
                    <i class="fas fa-user-tie me-2"></i>
                    <span>Distribuidor Oficial: <strong><?php echo ucfirst(getAfiliado()); ?></strong></span>
                </div>
                <div class="contact-info d-none d-md-flex">
                    <span class="me-3">
                        <i class="fas fa-phone me-1"></i>
                        <small>(11) 99999-9999</small>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Faixa de Frete Grátis -->
    <div class="frete-gratis-banner">
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
                                <img src="assets/images/logos/logo.png" alt="" width="150px">
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
                            <button class="btn-search" type="button" aria-label="Buscar">
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
                        <a href="?page=produtos&filter=promocao" class="promo-button">
                            <i class="fas fa-fire me-2"></i>
                            <span class="promo-text">Vita Promo</span>
                        </a>
                        
                        <!-- Menu de Navegação -->
                        <ul class="nav-menu d-none d-lg-flex">
                            <li class="nav-item">
                                <a href="?page=produtos&categoria=lancamentos" class="nav-link">
                                    Lançamentos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="?page=produtos&filter=promocao" class="nav-link">
                                    Promos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="?page=produtos&categoria=vitaminas" class="nav-link">
                                    Vitaminas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="?page=produtos&categoria=suplementos" class="nav-link">
                                    Suplementos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="?page=produtos&categoria=naturais" class="nav-link">
                                    Naturais
                                </a>
                            </li>
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
    -->
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
    </div>

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
                        <i class="fas fa-fire me-3"></i>Boti Promo
                        <span class="badge bg-danger ms-2">Ofertas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?page=produtos&categoria=lancamentos" class="nav-link">
                        <i class="fas fa-star me-3"></i>Lançamentos
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?page=produtos&categoria=vitaminas" class="nav-link">
                        <i class="fas fa-pills me-3"></i>Vitaminas
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?page=produtos&categoria=suplementos" class="nav-link">
                        <i class="fas fa-dumbbell me-3"></i>Suplementos
                    </a>
                </li>
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