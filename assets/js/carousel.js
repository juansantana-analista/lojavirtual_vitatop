// assets/js/carousel.js - Carrossel com Banners Dinâmicos

class SimpleCarousel {
    constructor(selector) {
        this.carousel = document.querySelector(selector);
        if (!this.carousel) return;
        
        this.currentSlide = 0;
        this.slides = [];
        this.autoPlayInterval = null;
        this.autoPlayDelay = 4000; // 4 segundos
        this.bannerImages = []; // Array para armazenar URLs dos banners
        this.isLoading = true;
        
        this.init();
    }
    
    async init() {
        // Mostrar loading
        this.showLoading();
        
        // Buscar banners da API
        await this.loadBanners();
        
        // Criar slides com os banners carregados
        this.createSlides();
        this.createControls();
        this.createIndicators();
        this.bindEvents();
        
        // Esconder loading
        this.hideLoading();
        
        // Iniciar carrossel
        this.startAutoPlay();
        this.showSlide(0);
    }
    
    async loadBanners() {
        try {            
            const response = await fetch('api/listar_banners.php', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();            
            if (data.status === 'success' && data.images && data.images.length > 0) {
                this.bannerImages = data.images;
            } else {
                console.warn('Nenhum banner encontrado, usando fallbacks');
                this.bannerImages = this.getFallbackImages();
            }
            
        } catch (error) {
            console.error('Erro ao carregar banners:', error);
            this.bannerImages = this.getFallbackImages();
        }
    }
    
    getFallbackImages() {
        return [
            'https://via.placeholder.com/1200x400/2c5530/ffffff?text=Extrato+de+Própolis+-+Poder+da+Natureza',
            'https://via.placeholder.com/1200x400/ff6b35/ffffff?text=Vitaminas+Premium+-+Sua+Saúde+em+Primeiro+Lugar',
            'https://via.placeholder.com/1200x400/28a745/ffffff?text=Suplementos+Naturais+-+Qualidade+Garantida',
            'https://via.placeholder.com/1200x400/e91e63/ffffff?text=Ofertas+Especiais+-+Até+50%+OFF'
        ];
    }
    
    showLoading() {
        this.carousel.innerHTML = `
            <div class="carousel-loading">
                <div class="loading-spinner"></div>
                <p>Carregando banners...</p>
            </div>
        `;
    }
    
    hideLoading() {
        const loading = this.carousel.querySelector('.carousel-loading');
        if (loading) {
            loading.remove();
        }
    }
    
    createSlides() {
        const container = document.createElement('div');
        container.className = 'carousel-container';
        
        this.bannerImages.forEach((imageUrl, index) => {
            const slide = document.createElement('div');
            slide.className = 'carousel-slide';
            
            const img = document.createElement('img');
            img.src = imageUrl;
            img.alt = `Banner VitaTop ${index + 1}`;
            img.loading = 'lazy';
            
            // Adicionar evento de erro para fallback
            img.onerror = () => {
                console.warn(`Erro ao carregar banner ${index + 1}:`, imageUrl);
                img.src = this.getFallbackImages()[index] || this.getFallbackImages()[0];
            };
            
            // Adicionar evento de carregamento
            img.onload = () => {
                slide.classList.add('loaded');
            };
            
            slide.appendChild(img);
            container.appendChild(slide);
            this.slides.push(slide);
        });
        
        this.carousel.appendChild(container);
    }
    
    createControls() {
        // Botão anterior
        const prevBtn = document.createElement('button');
        prevBtn.className = 'carousel-nav carousel-prev';
        prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
        prevBtn.setAttribute('aria-label', 'Banner anterior');
        prevBtn.addEventListener('click', () => this.prevSlide());
        
        // Botão próximo
        const nextBtn = document.createElement('button');
        nextBtn.className = 'carousel-nav carousel-next';
        nextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
        nextBtn.setAttribute('aria-label', 'Próximo banner');
        nextBtn.addEventListener('click', () => this.nextSlide());
        
        this.carousel.appendChild(prevBtn);
        this.carousel.appendChild(nextBtn);
    }
    
    createIndicators() {
        const indicatorsContainer = document.createElement('div');
        indicatorsContainer.className = 'carousel-indicators';
        
        this.slides.forEach((_, index) => {
            const dot = document.createElement('button');
            dot.className = 'carousel-dot';
            dot.setAttribute('aria-label', `Ir para banner ${index + 1}`);
            dot.addEventListener('click', () => this.goToSlide(index));
            indicatorsContainer.appendChild(dot);
        });
        
        this.carousel.appendChild(indicatorsContainer);
        this.indicators = indicatorsContainer.querySelectorAll('.carousel-dot');
    }
    
    bindEvents() {
        // Pausar autoplay ao passar o mouse
        this.carousel.addEventListener('mouseenter', () => this.stopAutoPlay());
        this.carousel.addEventListener('mouseleave', () => this.startAutoPlay());
        
        // Suporte a touch/swipe em mobile
        let startX = 0;
        let endX = 0;
        
        this.carousel.addEventListener('touchstart', (e) => {
            startX = e.changedTouches[0].screenX;
            this.stopAutoPlay();
        });
        
        this.carousel.addEventListener('touchend', (e) => {
            endX = e.changedTouches[0].screenX;
            this.handleSwipe(startX, endX);
            this.startAutoPlay();
        });
        
        // Pausar quando a aba não estiver visível
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.stopAutoPlay();
            } else {
                this.startAutoPlay();
            }
        });
        
        // Navegação por teclado
        document.addEventListener('keydown', (e) => {
            if (!this.carousel.matches(':focus-within')) return;
            
            switch(e.key) {
                case 'ArrowLeft':
                    e.preventDefault();
                    this.prevSlide();
                    break;
                case 'ArrowRight':
                    e.preventDefault();
                    this.nextSlide();
                    break;
                case ' ':
                    e.preventDefault();
                    if (this.autoPlayInterval) {
                        this.stopAutoPlay();
                    } else {
                        this.startAutoPlay();
                    }
                    break;
            }
        });
    }
    
    handleSwipe(startX, endX) {
        const difference = startX - endX;
        const threshold = 50;
        
        if (Math.abs(difference) > threshold) {
            if (difference > 0) {
                this.nextSlide();
            } else {
                this.prevSlide();
            }
        }
    }
    
    showSlide(index) {
        // Remover classe active de todos os slides
        this.slides.forEach(slide => slide.classList.remove('active'));
        
        // Adicionar classe active ao slide atual
        this.slides[index].classList.add('active');
        
        // Atualizar indicadores
        this.indicators.forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
        });
        
        this.currentSlide = index;
        
        // Disparar evento customizado para tracking
        this.carousel.dispatchEvent(new CustomEvent('slideChanged', {
            detail: { 
                currentSlide: index, 
                totalSlides: this.slides.length,
                imageUrl: this.bannerImages[index]
            }
        }));
    }
    
    nextSlide() {
        const nextIndex = (this.currentSlide + 1) % this.slides.length;
        this.showSlide(nextIndex);
    }
    
    prevSlide() {
        const prevIndex = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
        this.showSlide(prevIndex);
    }
    
    goToSlide(index) {
        this.showSlide(index);
    }
    
    startAutoPlay() {
        if (this.slides.length <= 1) return;
        
        this.stopAutoPlay(); // Limpar qualquer timer existente
        this.autoPlayInterval = setInterval(() => {
            this.nextSlide();
        }, this.autoPlayDelay);
    }
    
    stopAutoPlay() {
        if (this.autoPlayInterval) {
            clearInterval(this.autoPlayInterval);
            this.autoPlayInterval = null;
        }
    }
    
    // Método para recarregar banners
    async reloadBanners() {
        this.showLoading();
        await this.loadBanners();
        
        // Limpar conteúdo atual
        this.carousel.innerHTML = '';
        this.slides = [];
        
        // Recriar carrossel
        this.createSlides();
        this.createControls();
        this.createIndicators();
        
        this.hideLoading();
        this.showSlide(0);
        this.startAutoPlay();
    }
    
    destroy() {
        this.stopAutoPlay();
        this.carousel.innerHTML = '';
        this.slides = [];
        this.bannerImages = [];
    }
}

// Inicializar o carrossel quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', async function() {
    
    const carouselElement = document.querySelector('.hero-carousel');
    if (carouselElement) {
        const carousel = new SimpleCarousel('.hero-carousel');
        
        // Expor globalmente para debug
        window.heroCarousel = carousel;
        
    } else {
        console.error('Elemento .hero-carousel não encontrado');
    }
});

// Intersection Observer para pausar quando não visível
function setupIntersectionObserver() {
    const carousel = document.querySelector('.hero-carousel');
    if (!carousel) return;
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                if (window.heroCarousel) {
                    window.heroCarousel.startAutoPlay();
                }
            } else {
                if (window.heroCarousel) {
                    window.heroCarousel.stopAutoPlay();
                }
            }
        });
    }, {
        threshold: 0.3
    });
    
    observer.observe(carousel);
}

// Configurar observer após DOM carregado
document.addEventListener('DOMContentLoaded', setupIntersectionObserver);

// Performance: Otimizar para dispositivos móveis
function optimizeForMobile() {
    if (window.innerWidth <= 768) {
        // Reduzir velocidade do autoplay em mobile
        if (window.heroCarousel) {
            window.heroCarousel.autoPlayDelay = 5000; // 5 segundos em mobile
            window.heroCarousel.stopAutoPlay();
            window.heroCarousel.startAutoPlay();
        }
    }
}

// Executar otimizações móveis
window.addEventListener('load', optimizeForMobile);
window.addEventListener('resize', optimizeForMobile);

// Função para recarregar banners (útil para atualizações)
function reloadCarouselBanners() {
    if (window.heroCarousel) {
        window.heroCarousel.reloadBanners();
    }
}

// Expor função globalmente
window.reloadCarouselBanners = reloadCarouselBanners;

// Export para uso em outros módulos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SimpleCarousel;
}