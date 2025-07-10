// assets/js/carousel.js - Versão SEM LOOP

class SimpleCarousel {
    constructor(selector) {
        this.carousel = document.querySelector(selector);
        if (!this.carousel) {
            console.error('❌ Elemento do carrossel não encontrado:', selector);
            return;
        }
        
        this.currentSlide = 0;
        this.slides = [];
        this.autoPlayInterval = null;
        this.autoPlayDelay = 4000;
        this.bannerImages = [];
        this.isLoading = true;
        
        this.init();
    }
    
    async init() {
        
        this.showLoading();
        await this.loadBanners();
        this.createSlides();
        this.createControls();
        this.createIndicators();
        this.bindEvents();
        this.hideLoading();
        this.startAutoPlay();
        this.showSlide(0);
        this.updateResponsiveStyles();
        
    }
    
    async loadBanners() {
        
        try {
            const response = await fetch('api/listar_banners.php', {
                method: 'GET',
                headers: { 'Content-Type': 'application/json' }
            });
            
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const responseText = await response.text();
            
            const data = JSON.parse(responseText);
            
            if (data.status === 'success' && data.images && data.images.length > 0) {
                this.bannerImages = data.images;
            } else {
                console.warn('⚠️ Usando fallbacks - nenhum banner na API');
                this.bannerImages = this.getFallbackImages();
            }
            
        } catch (error) {
            console.error('❌ Erro ao carregar banners:', error);
            this.bannerImages = this.getFallbackImages();
        }
        
    }
    
    getFallbackImages() {
        return [
            this.createSVGBanner('#2c5530', 'Extrato de Própolis', 'Poder da Natureza'),
            this.createSVGBanner('#ff6b35', 'Vitaminas Premium', 'Sua Saúde em Primeiro Lugar'),
            this.createSVGBanner('#28a745', 'Suplementos Naturais', 'Qualidade Garantida'),
            this.createSVGBanner('#e91e63', 'Ofertas Especiais', 'Até 50% OFF')
        ];
    }
    
    createSVGBanner(color, title, subtitle) {
        const svg = `
            <svg width="1200" height="400" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="grad${color.replace('#', '')}" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" style="stop-color:${color};stop-opacity:1" />
                        <stop offset="100%" style="stop-color:${color}dd;stop-opacity:1" />
                    </linearGradient>
                </defs>
                <rect width="100%" height="100%" fill="url(#grad${color.replace('#', '')})"/>
                <text x="50%" y="45%" dominant-baseline="middle" text-anchor="middle" 
                      fill="white" font-size="48" font-weight="bold" font-family="Arial, sans-serif">
                    ${title}
                </text>
                <text x="50%" y="60%" dominant-baseline="middle" text-anchor="middle" 
                      fill="white" font-size="24" font-family="Arial, sans-serif">
                    ${subtitle}
                </text>
                <text x="95%" y="95%" dominant-baseline="middle" text-anchor="end" 
                      fill="white" font-size="18" font-family="Arial, sans-serif">
                    VitaTop
                </text>
            </svg>
        `;
        return 'data:image/svg+xml;base64,' + btoa(svg);
    }
    
    showLoading() {
        this.carousel.innerHTML = `
            <div class="carousel-loading" style="
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                height: 400px;
                background: linear-gradient(135deg, #f8f9fa, #e9ecef);
                border-radius: 15px;
                font-family: Arial, sans-serif;
            ">
                <div style="
                    width: 50px;
                    height: 50px;
                    border: 5px solid #e9ecef;
                    border-left: 5px solid #2c5530;
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                "></div>
                <p style="margin-top: 20px; color: #6c757d; font-size: 16px;">Carregando banners...</p>
            </div>
            <style>
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            </style>
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
            slide.setAttribute('data-slide', index);
            
            if (imageUrl.startsWith('data:image/svg')) {
                // É um SVG - inserir diretamente
                const img = document.createElement('img');
                img.src = imageUrl;
                img.alt = `Banner VitaTop ${index + 1}`;
                // Aplicar estilos responsivos
                const isMobile = window.innerWidth <= 768;
                if (isMobile) {
                    img.style.cssText = 'width: 100%; height: auto; object-fit: contain; display: block;';
                } else {
                    img.style.cssText = 'width: 100%; height: 100%; object-fit: cover; display: block;';
                }
                slide.appendChild(img);
                
            } else {
                // É uma URL externa - criar com fallback
                this.createImageSlide(slide, imageUrl, index);
            }
            
            container.appendChild(slide);
            this.slides.push(slide);
        });
        
        this.carousel.appendChild(container);
    }
    
    createImageSlide(slide, imageUrl, index) {
        const img = document.createElement('img');
        img.src = imageUrl;
        img.alt = `Banner VitaTop ${index + 1}`;
        // Aplicar estilos responsivos
        const isMobile = window.innerWidth <= 768;
        if (isMobile) {
            img.style.cssText = 'width: 100%; height: auto; object-fit: contain; display: block;';
        } else {
            img.style.cssText = 'width: 100%; height: 100%; object-fit: cover; display: block;';
        }
        
        // Variável para controlar se já tentou fallback
        let fallbackAttempted = false;
        
        img.onerror = () => {
            if (!fallbackAttempted) {
                fallbackAttempted = true;
                console.warn(`⚠️ Banner ${index + 1} falhou, usando SVG fallback`);
                
                // Substituir por SVG fallback
                const colors = ['#2c5530', '#ff6b35', '#28a745', '#e91e63'];
                const titles = ['Extrato de Própolis', 'Vitaminas Premium', 'Suplementos Naturais', 'Ofertas Especiais'];
                const subtitles = ['Poder da Natureza', 'Sua Saúde em Primeiro Lugar', 'Qualidade Garantida', 'Até 50% OFF'];
                
                const color = colors[index] || '#2c5530';
                const title = titles[index] || 'VitaTop';
                const subtitle = subtitles[index] || 'Produtos Naturais';
                
                const svgUrl = this.createSVGBanner(color, title, subtitle);
                img.src = svgUrl;
                
            } else {
                console.error(`❌ Fallback também falhou para slide ${index + 1} - isso não deveria acontecer com SVG`);
            }
        };
        
        img.onload = () => {
        };
        
        slide.appendChild(img);
    }
    
    createControls() {
        const prevBtn = document.createElement('button');
        prevBtn.className = 'carousel-nav carousel-prev';
        prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
        prevBtn.setAttribute('aria-label', 'Banner anterior');
        prevBtn.addEventListener('click', () => this.prevSlide());
        
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
        this.carousel.addEventListener('mouseenter', () => this.stopAutoPlay());
        this.carousel.addEventListener('mouseleave', () => this.startAutoPlay());
        
        // Touch/swipe support
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
        
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.stopAutoPlay();
            } else {
                this.startAutoPlay();
            }
        });
        
        // Responsive resize handler
        window.addEventListener('resize', () => {
            this.updateResponsiveStyles();
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
    
    updateResponsiveStyles() {
        const isMobile = window.innerWidth <= 768;
        const images = this.carousel.querySelectorAll('.carousel-slide img');
        
        images.forEach(img => {
            if (isMobile) {
                img.style.cssText = 'width: 100%; height: auto; object-fit: contain; display: block;';
            } else {
                img.style.cssText = 'width: 100%; height: 100%; object-fit: cover; display: block;';
            }
        });
    }
    
    showSlide(index) {
        this.slides.forEach(slide => slide.classList.remove('active'));
        this.slides[index].classList.add('active');
        
        this.indicators.forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
        });
        
        this.currentSlide = index;
        
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
        
        this.stopAutoPlay();
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
    
    async reloadBanners() {
        this.showLoading();
        await this.loadBanners();
        
        this.carousel.innerHTML = '';
        this.slides = [];
        
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

// Inicializar quando DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    
    const carouselElement = document.querySelector('.hero-carousel');
    if (carouselElement) {
        const carousel = new SimpleCarousel('.hero-carousel');
        window.heroCarousel = carousel;
        
    } else {
        console.error('❌ Elemento .hero-carousel não encontrado');
    }
});

// Intersection Observer
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
    }, { threshold: 0.3 });
    
    observer.observe(carousel);
}

document.addEventListener('DOMContentLoaded', setupIntersectionObserver);

// Otimização para mobile
function optimizeForMobile() {
    if (window.innerWidth <= 768) {
        if (window.heroCarousel) {
            window.heroCarousel.autoPlayDelay = 5000;
            window.heroCarousel.stopAutoPlay();
            window.heroCarousel.startAutoPlay();
        }
    }
}

window.addEventListener('load', optimizeForMobile);
window.addEventListener('resize', optimizeForMobile);

// Função global para recarregar
window.reloadCarouselBanners = function() {
    if (window.heroCarousel) {
        window.heroCarousel.reloadBanners();
    }
};

// Export para módulos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SimpleCarousel;
}