// assets/js/carousel.js - Versão com Debug Melhorado

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
        this.autoPlayDelay = 4000; // 4 segundos
        this.bannerImages = []; // Array para armazenar URLs dos banners
        this.isLoading = true;
        
        this.init();
    }
    
    async init() {
        console.log('🚀 Iniciando carregamento do carrossel...');
        
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
        
        console.log('✅ Carrossel inicializado com sucesso!');
    }
    
    async loadBanners() {
        console.log('📡 Carregando banners da API...');
        
        try {
            console.log('🔄 Fazendo requisição para: api/listar_banners.php');
            
            const response = await fetch('api/listar_banners.php', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            
            console.log('📊 Status da resposta:', response.status, response.statusText);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const responseText = await response.text();
            console.log('📄 Resposta bruta da API:', responseText);
            
            let data;
            try {
                data = JSON.parse(responseText);
            } catch (parseError) {
                console.error('❌ Erro ao parsear JSON:', parseError);
                console.log('📝 Conteúdo que falhou no parse:', responseText);
                throw new Error('Resposta não é um JSON válido');
            }
            
            console.log('📦 Dados parseados:', data);
            
            if (data.status === 'success' && data.images && data.images.length > 0) {
                this.bannerImages = data.images;
                console.log('✅ Banners carregados com sucesso:', this.bannerImages.length, 'banners');
                console.log('🖼️ URLs dos banners:', this.bannerImages);
            } else {
                console.warn('⚠️ Nenhum banner encontrado na resposta da API:', data);
                console.log('🔄 Usando fallbacks...');
                this.bannerImages = this.getFallbackImages();
            }
            
        } catch (error) {
            console.error('❌ Erro ao carregar banners:', error);
            console.log('🔄 Usando imagens fallback devido ao erro');
            this.bannerImages = this.getFallbackImages();
        }
        
        console.log('📋 Total de banners a serem exibidos:', this.bannerImages.length);
    }
    
    getFallbackImages() {
        console.log('🎨 Gerando imagens fallback...');
        return [
            'https://via.placeholder.com/1200x400/2c5530/ffffff?text=Extrato+de+Própolis+-+Poder+da+Natureza',
            'https://via.placeholder.com/1200x400/ff6b35/ffffff?text=Vitaminas+Premium+-+Sua+Saúde+em+Primeiro+Lugar',
            'https://via.placeholder.com/1200x400/28a745/ffffff?text=Suplementos+Naturais+-+Qualidade+Garantida',
            'https://via.placeholder.com/1200x400/e91e63/ffffff?text=Ofertas+Especiais+-+Até+50%+OFF'
        ];
    }
    
    showLoading() {
        console.log('⏳ Mostrando tela de loading...');
        this.carousel.innerHTML = `
            <div class="carousel-loading" style="
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                height: 400px;
                background: #f8f9fa;
                border-radius: 15px;
            ">
                <div class="loading-spinner" style="
                    width: 50px;
                    height: 50px;
                    border: 5px solid #e9ecef;
                    border-left: 5px solid #2c5530;
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                "></div>
                <p style="margin-top: 20px; color: #6c757d;">Carregando banners...</p>
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
        console.log('✨ Removendo tela de loading...');
        const loading = this.carousel.querySelector('.carousel-loading');
        if (loading) {
            loading.remove();
        }
    }
    
    createSlides() {
        console.log('🖼️ Criando slides...', this.bannerImages.length, 'slides');
        
        const container = document.createElement('div');
        container.className = 'carousel-container';
        
        this.bannerImages.forEach((imageUrl, index) => {
            console.log(`🎯 Criando slide ${index + 1}:`, imageUrl);
            
            const slide = document.createElement('div');
            slide.className = 'carousel-slide';
            
            const img = document.createElement('img');
            img.src = imageUrl;
            img.alt = `Banner VitaTop ${index + 1}`;
            img.loading = 'lazy';
            
            // Adicionar evento de erro para fallback
            img.onerror = () => {
                console.warn(`⚠️ Erro ao carregar banner ${index + 1}:`, imageUrl);
                const fallbackUrl = this.getFallbackImages()[index] || this.getFallbackImages()[0];
                console.log(`🔄 Usando fallback para slide ${index + 1}:`, fallbackUrl);
                img.src = fallbackUrl;
            };
            
            // Adicionar evento de carregamento
            img.onload = () => {
                console.log(`✅ Banner ${index + 1} carregado com sucesso:`, imageUrl);
                slide.classList.add('loaded');
            };
            
            slide.appendChild(img);
            container.appendChild(slide);
            this.slides.push(slide);
        });
        
        this.carousel.appendChild(container);
        console.log('✅ Slides criados com sucesso:', this.slides.length);
    }
    
    createControls() {
        console.log('🎮 Criando controles do carrossel...');
        
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
        
        console.log('✅ Controles criados');
    }
    
    createIndicators() {
        console.log('🔘 Criando indicadores...', this.slides.length, 'indicadores');
        
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
        
        console.log('✅ Indicadores criados');
    }
    
    bindEvents() {
        console.log('🔗 Vinculando eventos...');
        
        // Pausar autoplay ao passar o mouse
        this.carousel.addEventListener('mouseenter', () => {
            console.log('🐭 Mouse sobre carrossel - pausando autoplay');
            this.stopAutoPlay();
        });
        this.carousel.addEventListener('mouseleave', () => {
            console.log('🐭 Mouse saiu do carrossel - retomando autoplay');
            this.startAutoPlay();
        });
        
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
                console.log('📱 Aba oculta - pausando autoplay');
                this.stopAutoPlay();
            } else {
                console.log('📱 Aba visível - retomando autoplay');
                this.startAutoPlay();
            }
        });
        
        console.log('✅ Eventos vinculados');
    }
    
    handleSwipe(startX, endX) {
        const difference = startX - endX;
        const threshold = 50;
        
        if (Math.abs(difference) > threshold) {
            if (difference > 0) {
                console.log('👆 Swipe para próximo slide');
                this.nextSlide();
            } else {
                console.log('👆 Swipe para slide anterior');
                this.prevSlide();
            }
        }
    }
    
    showSlide(index) {
        console.log(`🎯 Mostrando slide ${index + 1}/${this.slides.length}`);
        
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
        
        console.log(`✅ Slide ${index + 1} ativado`);
    }
    
    nextSlide() {
        const nextIndex = (this.currentSlide + 1) % this.slides.length;
        console.log(`➡️ Próximo slide: ${nextIndex + 1}`);
        this.showSlide(nextIndex);
    }
    
    prevSlide() {
        const prevIndex = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
        console.log(`⬅️ Slide anterior: ${prevIndex + 1}`);
        this.showSlide(prevIndex);
    }
    
    goToSlide(index) {
        console.log(`🎯 Indo para slide específico: ${index + 1}`);
        this.showSlide(index);
    }
    
    startAutoPlay() {
        if (this.slides.length <= 1) {
            console.log('📋 Apenas 1 slide - autoplay desabilitado');
            return;
        }
        
        this.stopAutoPlay(); // Limpar qualquer timer existente
        this.autoPlayInterval = setInterval(() => {
            console.log('⏰ Autoplay - próximo slide');
            this.nextSlide();
        }, this.autoPlayDelay);
        
        console.log('▶️ Autoplay iniciado');
    }
    
    stopAutoPlay() {
        if (this.autoPlayInterval) {
            clearInterval(this.autoPlayInterval);
            this.autoPlayInterval = null;
            console.log('⏸️ Autoplay pausado');
        }
    }
    
    // Método para recarregar banners
    async reloadBanners() {
        console.log('🔄 Recarregando banners...');
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
        
        console.log('✅ Banners recarregados');
    }
    
    destroy() {
        console.log('💥 Destruindo carrossel...');
        this.stopAutoPlay();
        this.carousel.innerHTML = '';
        this.slides = [];
        this.bannerImages = [];
    }
}

// Função para testar a API diretamente
async function testBannersAPI() {
    console.log('🧪 === TESTE DIRETO DA API DE BANNERS ===');
    
    try {
        console.log('📡 Testando api/listar_banners.php...');
        
        const response = await fetch('api/listar_banners.php', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        
        console.log('📊 Status:', response.status);
        console.log('📋 Headers:', Object.fromEntries(response.headers.entries()));
        
        const text = await response.text();
        console.log('📄 Resposta bruta:', text);
        
        try {
            const data = JSON.parse(text);
            console.log('📦 Dados JSON:', data);
            
            if (data.status === 'success') {
                console.log('✅ API funcionando - banners encontrados:', data.images?.length || 0);
                if (data.images) {
                    data.images.forEach((url, index) => {
                        console.log(`🖼️ Banner ${index + 1}:`, url);
                    });
                }
            } else {
                console.warn('⚠️ API retornou erro:', data.message);
            }
        } catch (parseError) {
            console.error('❌ Erro ao parsear JSON:', parseError);
        }
        
    } catch (error) {
        console.error('❌ Erro na requisição:', error);
    }
    
    console.log('🧪 === FIM DO TESTE ===');
}

// Inicializar o carrossel quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', async function() {
    console.log('🚀 DOM carregado - iniciando sistema de carrossel');
    
    // Teste da API primeiro (opcional para debug)
    if (window.location.search.includes('debug=banners')) {
        await testBannersAPI();
    }
    
    const carouselElement = document.querySelector('.hero-carousel');
    if (carouselElement) {
        console.log('✅ Elemento do carrossel encontrado');
        const carousel = new SimpleCarousel('.hero-carousel');
        
        // Expor globalmente para debug
        window.heroCarousel = carousel;
        
        // Função global para testar API
        window.testBannersAPI = testBannersAPI;
        
        console.log('💡 Para debug, digite no console: testBannersAPI()');
        console.log('💡 Para recarregar banners: window.heroCarousel.reloadBanners()');
        
    } else {
        console.error('❌ Elemento .hero-carousel não encontrado');
    }
});

// Intersection Observer para pausar quando não visível
function setupIntersectionObserver() {
    const carousel = document.querySelector('.hero-carousel');
    if (!carousel) return;
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                console.log('👁️ Carrossel visível - iniciando autoplay');
                if (window.heroCarousel) {
                    window.heroCarousel.startAutoPlay();
                }
            } else {
                console.log('👁️ Carrossel não visível - pausando autoplay');
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
        console.log('📱 Dispositivo móvel detectado - otimizando carrossel');
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