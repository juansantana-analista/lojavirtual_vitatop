<?php
// pages/home.php - Estilo Boticário VitaTop
$produtos_response = listarProdutos();
$produtos = $produtos_response['status'] === 'success' ? $produtos_response['data']['data'] : [];

// Pegar produtos em destaque e promoções
$produtos_destaque = array_slice($produtos, 0, 8);
$produtos_promocao = array_slice($produtos, 8, 4);
?>

<!-- Carrossel Dinâmico de Banners -->
<section class="hero-carousel">
    <!-- O conteúdo será gerado dinamicamente pelo JavaScript -->
</section>

<div class="container">
    <!-- Categorias Principais - Estilo Boticário -->
    <section class="categories-section-main py-5">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6 col-6">
                <a href="?page=produtos&categoria=lancamentos" class="category-card-main">
                    <div class="category-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h5>Lançamentos</h5>
                    <p>Novidades em suplementos</p>
                </a>
            </div>
            <div class="col-lg-3 col-md-6 col-6">
                <a href="?page=produtos&filter=promocao" class="category-card-main promo-category">
                    <div class="category-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <h5>Promos</h5>
                    <p>Ofertas imperdíveis</p>
                </a>
            </div>
            <div class="col-lg-3 col-md-6 col-6">
                <a href="?page=produtos&categoria=vitaminas" class="category-card-main">
                    <div class="category-icon">
                        <i class="fas fa-pills"></i>
                    </div>
                    <h5>Vitaminas</h5>
                    <p>Saúde e bem-estar</p>
                </a>
            </div>
            <div class="col-lg-3 col-md-6 col-6">
                <a href="?page=produtos&categoria=proteinas" class="category-card-main">
                    <div class="category-icon">
                        <i class="fas fa-tint"></i>
                    </div>
                    <h5>Mel</h5>
                    <p>Produtos a base de Mel</p>
                </a>
            </div>
        </div>
    </section>

    <!-- Seção de Mais Vendidos -->
    <section class="best-sellers-section py-5" id="mais-vendidos">
        <div class="section-header text-center mb-5">
            <div class="section-badge">
                <i class="fas fa-trophy me-2"></i>Mais Vendidos
            </div>
            <h2 class="section-title">Os Preferidos dos Nossos Clientes</h2>
            <p class="section-subtitle">Produtos testados e aprovados por milhares de pessoas</p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($produtos_destaque as $index => $produto): ?>
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                    <div class="product-card">
                        <div class="product-image">
                            <a href="?page=produto&id=<?php echo $produto['id']; ?>">
                                <img src="https://vitatop.tecskill.com.br/<?php echo $produto['foto']; ?>" 
                                     alt="<?php echo htmlspecialchars($produto['nome']); ?>" 
                                     class="img-fluid"
                                     loading="lazy">
                            </a>
                            <button class="btn-favorite" data-product-id="<?php echo $produto['id']; ?>" aria-label="Adicionar aos favoritos">
                                <i class="far fa-heart"></i>
                            </button>
                            <?php if (isPromocao($produto)): ?>
                                <span class="discount-label">
                                    -<?php echo calcularDesconto($produto['preco2'], $produto['preco_lojavirtual']); ?>%
                                </span>
                            <?php endif; ?>
                            <?php if ($index < 3): ?>
                                <span class="bestseller-badge">
                                    <i class="fas fa-crown"></i>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <div class="product-brand">VitaTop</div>
                            <h5 class="product-name">
                                <a href="?page=produto&id=<?php echo $produto['id']; ?>" class="text-decoration-none">
                                    <?php echo htmlspecialchars($produto['titulo']); ?>
                                </a>
                            </h5>
                            <div class="product-rating">
                                <div class="stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <span class="rating-count">(<?php echo rand(50, 200); ?>)</span>
                            </div>
                            <div class="product-price">
                                <?php if (isPromocao($produto)): ?>
                                    <span class="old-price">De <?php echo formatPrice($produto['preco2']); ?></span>
                                <?php endif; ?>
                                <span class="current-price"><?php echo formatPrice($produto['preco_lojavirtual']); ?></span>
                                <?php if (isPromocao($produto)): ?>
                                    <span class="discount-percent">
                                        -<?php echo calcularDesconto($produto['preco2'], $produto['preco_lojavirtual']); ?>%
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="product-actions mt-3">
                                <button class="btn btn-add-cart w-100" 
                                        onclick="addToCart(<?php echo $produto['id']; ?>)"
                                        data-product-id="<?php echo $produto['id']; ?>">
                                    <i class="fas fa-shopping-bag me-2"></i>Adicionar à Sacola
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-5">
            <a href="?page=produtos" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-th-large me-2"></i>Ver Todos os Produtos
            </a>
        </div>
    </section>

    <!-- Seção de Ofertas Especiais -->
    <section class="special-offers-section py-5">
        <div class="offer-banner">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="offer-content">
                        <span class="offer-label">Oferta Especial</span>
                        <h3 class="offer-title">Compre 2 e Leve 3 em Produtos Selecionados</h3>
                        <p class="offer-description">Aproveite nossa promoção especial e economize ainda mais!</p>
                        <a href="?page=produtos&filter=promocao" class="btn btn-offer">
                            Aproveitar Oferta
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="offer-products">
                        <?php foreach (array_slice($produtos_promocao, 0, 2) as $produto): ?>
                            <div class="offer-product-item">
                                <img src="https://vitatop.tecskill.com.br/<?php echo $produto['foto']; ?>" 
                                     alt="<?php echo htmlspecialchars($produto['nome']); ?>"
                                     class="img-fluid">
                                <div class="offer-product-info">
                                    <h6><?php echo htmlspecialchars($produto['titulo']); ?></h6>
                                    <span class="price"><?php echo formatPrice($produto['preco_lojavirtual']); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefícios VitaTop -->
    <section class="benefits-section-main py-5">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">Por que Escolher VitaTop?</h2>
            <p class="section-subtitle">Compromisso com sua saúde e bem-estar</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="benefit-card-main">
                    <div class="benefit-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h5>Entrega Rápida</h5>
                    <p>Receba em casa em até 7 dias úteis para todo o Brasil</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="benefit-card-main">
                    <div class="benefit-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <h5>Qualidade Garantida</h5>
                    <p>Produtos certificados e testados em laboratório</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="benefit-card-main">
                    <div class="benefit-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h5>Exclusivo no App</h5>
                    <p>Ofertas especiais e descontos exclusivos</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="benefit-card-main">
                    <div class="benefit-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h5>Suporte 24h</h5>
                    <p>Atendimento especializado sempre disponível</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter VitaTop -->
    <section class="newsletter-section-main py-5">
        <div class="newsletter-container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="newsletter-content">
                        <h3>Receba nossas ofertas especiais</h3>
                        <p>Cadastre-se e seja o primeiro a saber das promoções e lançamentos VitaTop</p>
                        <ul class="newsletter-benefits">
                            <li><i class="fas fa-check text-success me-2"></i>Ofertas exclusivas</li>
                            <li><i class="fas fa-check text-success me-2"></i>Dicas de saúde</li>
                            <li><i class="fas fa-check text-success me-2"></i>Lançamentos em primeira mão</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="newsletter-form-container">
                        <form id="newsletterForm" class="newsletter-form">
                            <div class="form-group mb-3">
                                <input type="email" class="form-control newsletter-input" 
                                       placeholder="Digite seu melhor e-mail" required>
                            </div>
                            <button type="submit" class="btn btn-newsletter w-100">
                                <i class="fas fa-paper-plane me-2"></i>Quero Receber Ofertas
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- CSS Adicional para o Estilo Boticário -->
<style>
/* Categorias Principais */
.categories-section-main {
    margin-bottom: 50px;
}

.category-card-main {
    display: block;
    background: white;
    border-radius: 15px;
    padding: 30px 20px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    text-decoration: none;
    color: inherit;
    border: 2px solid transparent;
    height: 180px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.category-card-main:hover {
    color: inherit;
    text-decoration: none;
    transform: translateY(-8px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border-color: var(--boticario-green);
}

.category-card-main.promo-category {
    background: linear-gradient(135deg, #ff6b35, #e91e63);
    color: white;
}

.category-card-main.promo-category:hover {
    color: white;
    border-color: #ff6b35;
}

.category-icon {
    width: 60px;
    height: 60px;
    background: var(--light-gray);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    font-size: 1.5rem;
    color: var(--boticario-green);
    transition: all 0.3s ease;
}

.promo-category .category-icon {
    background: rgba(255,255,255,0.2);
    color: white;
}

.category-card-main:hover .category-icon {
    transform: scale(1.1);
}

.category-card-main h5 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 5px;
    color: var(--boticario-green);
}

.promo-category h5 {
    color: white;
}

.category-card-main p {
    font-size: 0.85rem;
    color: var(--dark-gray);
    margin: 0;
}

.promo-category p {
    color: rgba(255,255,255,0.9);
}

/* Section Headers */
.section-badge {
    background: linear-gradient(135deg, var(--boticario-green), var(--boticario-light-green));
    color: white;
    padding: 8px 20px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    display: inline-block;
    margin-bottom: 20px;
}

/* Bestseller Badge */
.bestseller-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    background: #ffc107;
    color: #333;
    padding: 5px 10px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: bold;
    z-index: 2;
}

/* Product Rating */
.product-rating {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
}

.stars {
    color: #ffc107;
    font-size: 0.8rem;
}

.rating-count {
    font-size: 0.75rem;
    color: var(--dark-gray);
}

/* Ofertas Especiais */
.special-offers-section {
    margin: 50px 0;
}

.offer-banner {
    background: linear-gradient(135deg, var(--light-gray), var(--medium-gray));
    border-radius: 20px;
    padding: 40px;
    border: 2px solid var(--border-color);
    position: relative;
    overflow: hidden;
}

.offer-banner::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="%23f0f0f0" opacity="0.5"/></svg>') repeat;
    animation: float 20s linear infinite;
}

@keyframes float {
    0% { transform: translateX(0) translateY(0); }
    100% { transform: translateX(-50px) translateY(-50px); }
}

.offer-label {
    background: var(--boticario-red);
    color: white;
    padding: 6px 15px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-block;
    margin-bottom: 15px;
}

.offer-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--boticario-green);
    margin-bottom: 15px;
    line-height: 1.3;
}

.offer-description {
    color: var(--dark-gray);
    margin-bottom: 25px;
    font-size: 1rem;
}

.btn-offer {
    background: var(--boticario-green);
    color: white;
    border: none;
    padding: 15px 30px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.btn-offer:hover {
    background: var(--boticario-light-green);
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 5px 20px rgba(44, 85, 48, 0.3);
}

.offer-products {
    display: flex;
    gap: 20px;
    justify-content: center;
}

.offer-product-item {
    background: white;
    border-radius: 15px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    flex: 1;
    max-width: 200px;
}

.offer-product-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.offer-product-item img {
    width: 80px;
    height: 80px;
    object-fit: contain;
    margin-bottom: 15px;
}

.offer-product-item h6 {
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 8px;
    line-height: 1.2;
}

.offer-product-item .price {
    color: var(--boticario-green);
    font-weight: 700;
    font-size: 1.1rem;
}

/* Benefícios Principais */
.benefits-section-main {
    background: var(--light-gray);
    border-radius: 20px;
    margin: 50px 0;
    padding: 40px;
}

.benefit-card-main {
    background: white;
    border-radius: 15px;
    padding: 30px 20px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    height: 100%;
    border: 2px solid transparent;
}

.benefit-card-main:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border-color: var(--boticario-green);
}

.benefit-card-main .benefit-icon {
    width: 70px;
    height: 70px;
    background: var(--boticario-green);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 1.8rem;
    color: white;
    transition: all 0.3s ease;
}

.benefit-card-main:hover .benefit-icon {
    transform: scale(1.1);
    background: var(--boticario-light-green);
}

.benefit-card-main h5 {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--boticario-green);
    margin-bottom: 15px;
}

.benefit-card-main p {
    color: var(--dark-gray);
    line-height: 1.5;
    margin: 0;
}

/* Newsletter Seção */
.newsletter-section-main {
    margin: 50px 0;
}

.newsletter-container {
    background: linear-gradient(135deg, var(--boticario-green), var(--boticario-light-green));
    border-radius: 20px;
    padding: 50px;
    color: white;
    position: relative;
    overflow: hidden;
}

.newsletter-container::before {
    content: '';
    position: absolute;
    top: -100px;
    right: -100px;
    width: 300px;
    height: 300px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.newsletter-content {
    position: relative;
    z-index: 2;
}

.newsletter-content h3 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 15px;
}

.newsletter-content p {
    font-size: 1.1rem;
    margin-bottom: 25px;
    opacity: 0.9;
}

.newsletter-benefits {
    list-style: none;
    padding: 0;
    margin: 0;
}

.newsletter-benefits li {
    margin-bottom: 10px;
    font-size: 0.95rem;
}

.newsletter-form-container {
    position: relative;
    z-index: 2;
}

.newsletter-input {
    border: none;
    border-radius: 25px;
    padding: 15px 20px;
    font-size: 1rem;
    background: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.newsletter-input:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(255,255,255,0.3);
}

.btn-newsletter {
    background: var(--boticario-orange);
    border: none;
    color: white;
    padding: 15px 30px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.btn-newsletter:hover {
    background: #e55a2b;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 107, 53, 0.4);
}

/* Responsive */
@media (max-width: 768px) {
    .category-card-main {
        height: 150px;
        padding: 20px 15px;
    }
    
    .category-icon {
        width: 50px;
        height: 50px;
        font-size: 1.2rem;
        margin-bottom: 10px;
    }
    
    .offer-banner {
        padding: 30px 20px;
    }
    
    .offer-title {
        font-size: 1.4rem;
    }
    
    .offer-products {
        flex-direction: column;
        gap: 15px;
        margin-top: 30px;
    }
    
    .newsletter-container {
        padding: 30px 20px;
    }
    
    .newsletter-content h3 {
        font-size: 1.5rem;
    }
    
    .benefit-card-main .benefit-icon {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
    
    .benefits-section-main {
        padding: 30px 20px;
    }
}
</style>

<!-- Script para funcionalidades da home -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Newsletter form
    const newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            const button = this.querySelector('button');
            const originalText = button.innerHTML;
            
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Cadastrando...';
            button.disabled = true;
            
            setTimeout(() => {
                button.innerHTML = '<i class="fas fa-check me-2"></i>Cadastrado!';
                this.querySelector('input').value = '';
                
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.disabled = false;
                    showToast('Obrigado! Você receberá nossas ofertas em: ' + email, 'success');
                }, 2000);
            }, 1500);
        });
    }
    
    // Animações suaves
    function initScrollAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);
        
        // Aplicar animação aos cards
        document.querySelectorAll('.category-card-main, .product-card, .benefit-card-main').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'all 0.6s ease';
            observer.observe(el);
        });
    }
    
    // Inicializar animações após um breve delay
    setTimeout(initScrollAnimations, 300);
    
    // Event listener para mudanças no carrossel
    const carouselElement = document.querySelector('.hero-carousel');
    if (carouselElement) {
        carouselElement.addEventListener('slideChanged', function(e) {
            
            // Aqui você pode adicionar tracking ou outras funcionalidades
            // Por exemplo, Google Analytics
            // gtag('event', 'carousel_slide_view', {
            //     'slide_index': e.detail.currentSlide,
            //     'banner_url': e.detail.imageUrl
            // });
        });
    }
    
});

// Função para adicionar produto ao carrinho
function addToCart(productId) {
    const button = event.target;
    const originalContent = button.innerHTML;
    
    button.classList.add('loading');
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adicionando...';
    button.disabled = true;
    
    // Fazer requisição para adicionar ao carrinho
    fetch('api/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            produto_id: productId,
            quantidade: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            button.classList.remove('loading');
            button.innerHTML = '<i class="fas fa-check me-2"></i>Adicionado!';
            button.classList.add('btn-success');
            button.classList.remove('btn-add-cart');
            
            // Atualizar contador do carrinho se existir
            const cartBadge = document.querySelector('.cart-badge');
            if (cartBadge) {
                const currentCount = parseInt(cartBadge.textContent) || 0;
                cartBadge.textContent = currentCount + 1;
                cartBadge.style.animation = 'pulse 0.5s ease';
            }
            
            showToast('Produto adicionado à sacola!', 'success');
        } else {
            throw new Error(data.message || 'Erro ao adicionar produto');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        button.classList.remove('loading');
        button.innerHTML = originalContent;
        button.disabled = false;
        showToast('Erro ao adicionar produto. Tente novamente.', 'error');
    });
    
    // Voltar ao estado original após 2 segundos
    setTimeout(() => {
        button.innerHTML = originalContent;
        button.classList.remove('btn-success');
        button.classList.add('btn-add-cart');
        button.disabled = false;
    }, 2000);
}

// Função para mostrar toast de notificação
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    
    const icon = type === 'success' ? 'fa-check-circle' : 
                 type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle';
    
    toast.innerHTML = `
        <i class="fas ${icon} me-2"></i>
        ${message}
    `;
    
    // Estilos do toast
    const bgColor = type === 'success' ? '#28a745' : 
                    type === 'error' ? '#dc3545' : '#17a2b8';
    
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${bgColor};
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        z-index: 9999;
        opacity: 0;
        transform: translateX(100px);
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        max-width: 300px;
        font-size: 0.9rem;
    `;
    
    document.body.appendChild(toast);
    
    // Mostrar toast
    setTimeout(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(0)';
    }, 100);
    
    // Remover toast
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100px)';
        setTimeout(() => {
            if (document.body.contains(toast)) {
                document.body.removeChild(toast);
            }
        }, 300);
    }, 4000);
}

// Função para recarregar banners manualmente (útil para admin)
function reloadBanners() {
    if (window.heroCarousel) {
        window.heroCarousel.reloadBanners();
        showToast('Banners recarregados!', 'success');
    }
}

// Adicionar CSS para variáveis do Boticário
document.documentElement.style.setProperty('--boticario-green', '#2c5530');
document.documentElement.style.setProperty('--boticario-light-green', '#4a7c59');
document.documentElement.style.setProperty('--boticario-orange', '#ff6b35');
document.documentElement.style.setProperty('--boticario-red', '#e91e63');
document.documentElement.style.setProperty('--light-gray', '#f8f9fa');
document.documentElement.style.setProperty('--medium-gray', '#e9ecef');
document.documentElement.style.setProperty('--dark-gray', '#6c757d');
document.documentElement.style.setProperty('--border-color', '#dee2e6');
</script>