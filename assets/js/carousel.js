// assets/js/carousel.js - Versão SEM LOOP

class SimpleCarousel {
    constructor(selector) {
        console.log('🎠 Inicializando carrossel...', selector);
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
        console.log('🚀 Iniciando carregamento do carrossel...');
        
        this.showLoading();
        await this.loadBanners();
        this.createSlides();
        this.createControls();
        this.createIndicators();
        this.bindEvents();
        this.hideLoading();
        this.startAutoPlay();
        this.showSlide(0);
        
        console.log('✅ Carrossel inicializado com sucesso!');
    }
    
    async loadBanners() {
        console.log('📡 Carregando banners da API...');
        
        try {
            const response = await fetch('api/listar_banners.php', {
                method: 'GET',
                headers: { 'Content-Type': 'application/json' }
            });
            
            console.log('📊 Status da resposta:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const responseText = await response.text();
            console.log('📄 Resposta da API:', responseText.substring(0, 200) + '...');
            
            const data = JSON.parse(responseText);
            console.log('📦 Dados parseados:', data);
            
            if (data.status === 'success' && data.images && data.images.length > 0) {
                this.bannerImages = data.images;
                console.log('✅ Banners carregados:', this.bannerImages.length);
            } else {
                console.warn('⚠️ Usando fallbacks - nenhum banner na API');
                this.bannerImages = this.getFallbackImages();
            }
            
        } catch (error) {
            console.error('❌ Erro ao carregar banners:', error);
            this.bannerImages = this.getFallbackImages();
        }
        
        console.log('📋 Total de banners:', this.bannerImages.length);
    }
    
    getFallbackImages() {
        console.log('🎨 Gerando SVGs fallback...');
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
        console.log('🖼️ Criando slides...');
        
        const container = document.createElement('div');
        container.className = 'carousel-container';
        
        this.bannerImages.forEach((imageUrl, index) => {
            console.log(`🎯 Criando slide ${index + 1}:`, imageUrl.substring(0, 50) + '...');
            
            const slide = document.createElement('div');
            slide.className = 'carousel-slide';
            slide.setAttribute('data-slide', index);
            
            if (imageUrl.startsWith('data:image/svg')) {
                // É um SVG - inserir diretamente
                const img = document.createElement('img');
                img.src = imageUrl;
                img.alt = `Banner VitaTop ${index + 1}`;
                img.style.cssText = 'width: 100%; height: 100%; object-fit: cover; display: block;';
                slide.appendChild(img);
                
                console.log(`✅ SVG slide ${index + 1} criado`);
            } else {
                // É uma URL externa - criar com fallback
                this.createImageSlide(slide, imageUrl, index);
            }
            
            container.appendChild(slide);
            this.slides.push(slide);
        });
        
        this.carousel.appendChild(container);
        console.log('✅ Slides criados:', this.slides.length);
    }
    
    createImageSlide(slide, imageUrl, index) {
        const img = document.createElement('img');
        img.src = imageUrl;
        img.alt = `Banner VitaTop ${index + 1}`;
        img.style.cssText = 'width: 100%; height: 100%; object-fit: cover; display: block;';
        
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
                
                console.log(`🔄 Fallback aplicado para slide ${index + 1}`);
            } else {
                console.error(`❌ Fallback também falhou para slide ${index + 1} - isso não deveria acontecer com SVG`);
            }
        };
        
        img.onload = () => {
            console.log(`✅ Banner ${index + 1} carregado com sucesso`);
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
        console.log('🔄 Recarregando banners...');
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
    console.log('🚀 DOM carregado - iniciando carrossel');
    
    const carouselElement = document.querySelector('.hero-carousel');
    if (carouselElement) {
        const carousel = new SimpleCarousel('.hero-carousel');
        window.heroCarousel = carousel;
        
        console.log('💡 Para debug: window.heroCarousel.reloadBanners()');
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