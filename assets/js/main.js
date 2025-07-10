// assets/js/main.js

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar componentes
    initSearch();
    initProductFilters();
    initCepLookup();
    initPaymentMethods();
    
    // Máscaras de input
    initInputMasks();
        // Corrigir altura do viewport em mobile
    function setVH() {
        let vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
    }
    
    setVH();
    window.addEventListener('resize', setVH);
    
    // Fechar busca ao clicar fora
    document.addEventListener('click', function(e) {
        const searchContainer = document.querySelector('.search-container');
        const searchResults = document.getElementById('searchResults');
        
        if (searchContainer && !searchContainer.contains(e.target)) {
            if (searchResults) {
                searchResults.style.display = 'none';
            }
        }
    });
    
    // Smooth scroll para links internos
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Lazy loading para imagens
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
    // Preloader
    const loader = document.querySelector('.page-loader');
    if (loader) {
        window.addEventListener('load', function() {
            loader.classList.add('hide');
            setTimeout(() => {
                loader.remove();
            }, 300);
        });
    }
        // Inicializar animações simples (sem biblioteca AOS)
    function initScrollAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('aos-animate');
                }
            });
        }, observerOptions);
        
        document.querySelectorAll('[data-aos]').forEach(el => {
            observer.observe(el);
        });
    }
    
    // Inicializar formulário de newsletter
    function initNewsletter() {
        const form = document.getElementById('newsletterForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const email = this.querySelector('input[type="email"]').value;
                const button = this.querySelector('button');
                
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                button.disabled = true;
                
                // Simular envio
                setTimeout(() => {
                    button.innerHTML = '<i class="fas fa-check"></i>';
                    this.querySelector('input').value = '';
                    showToast('E-mail cadastrado com sucesso!', 'success');
                    
                    setTimeout(() => {
                        button.innerHTML = '<i class="fas fa-paper-plane"></i>';
                        button.disabled = false;
                    }, 2000);
                }, 1500);
            });
        }
    }
    
    // Melhorar botões de adicionar ao carrinho
    function initAddToCartButtons() {
        document.querySelectorAll('.btn-add-cart').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productId;
                const originalContent = this.innerHTML;
                
                // Estado de loading
                this.classList.add('loading');
                this.innerHTML = '<span>Adicionando...</span>';
                
                // Simular adição (substituir pela função real)
                setTimeout(() => {
                    this.classList.remove('loading');
                    this.innerHTML = '<i class="fas fa-check me-2"></i>Adicionado!';
                    this.classList.add('btn-success');
                    this.classList.remove('btn-primary');
                    
                    // Voltar ao estado original
                    setTimeout(() => {
                        this.innerHTML = originalContent;
                        this.classList.remove('btn-success');
                        this.classList.add('btn-primary');
                    }, 2000);
                }, 800);
            });
        });
    }
    
    // Inicializar favoritos
    function initFavorites() {
        document.querySelectorAll('.btn-favorite').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const icon = this.querySelector('i');
                const isFavorite = icon.classList.contains('fas');
                
                if (isFavorite) {
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                    showToast('Removido dos favoritos', 'info');
                } else {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    showToast('Adicionado aos favoritos!', 'success');
                }
                
                // Animação de coração
                this.style.transform = 'scale(1.2)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 200);
            });
        });
    }
    
    // Scroll suave para seção de produtos
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const headerOffset = 100;
                    const elementPosition = target.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                    
                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }
    
    // Lazy loading aprimorado
    function initLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        
                        // Criar nova imagem para preload
                        const newImg = new Image();
                        newImg.onload = function() {
                            img.src = this.src;
                            img.classList.add('loaded');
                        };
                        newImg.src = img.dataset.src || img.src;
                        
                        observer.unobserve(img);
                    }
                });
            }, {
                rootMargin: '50px'
            });
            
            document.querySelectorAll('img[loading="lazy"]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }
    
    // Inicializar todas as funcionalidades
    initScrollAnimations();
    initNewsletter();
    initAddToCartButtons();
    initFavorites();
    initSmoothScroll();
    initLazyLoading();
    
    // Performance: debounce do scroll
    let ticking = false;
    function updateOnScroll() {
        // Adicionar efeitos no scroll se necessário
        ticking = false;
    }
    
    window.addEventListener('scroll', function() {
        if (!ticking) {
            requestAnimationFrame(updateOnScroll);
            ticking = true;
        }
    });
});

// Busca de produtos
function initSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            filterProducts(query);
        });
    }
}

function filterProducts(query) {
    const products = document.querySelectorAll('.produto-item');
    products.forEach(product => {
        const name = product.dataset.name;
        if (name.includes(query)) {
            product.style.display = 'block';
        } else {
            product.style.display = 'none';
        }
    });
}

// Filtros de produtos
function initProductFilters() {
    const sortSelect = document.getElementById('sortBy');
    const categorySelect = document.getElementById('filterCategory');
    
    if (sortSelect) {
        sortSelect.addEventListener('change', sortProducts);
    }
    
    if (categorySelect) {
        categorySelect.addEventListener('change', filterByCategory);
    }
}

function sortProducts() {
    const sortBy = document.getElementById('sortBy').value;
    const container = document.getElementById('produtosContainer');
    const products = Array.from(container.children);
    
    products.sort((a, b) => {
        switch(sortBy) {
            case 'price_asc':
                return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
            case 'price_desc':
                return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
            case 'name_asc':
                return a.dataset.name.localeCompare(b.dataset.name);
            case 'name_desc':
                return b.dataset.name.localeCompare(a.dataset.name);
            default:
                return 0;
        }
    });
    
    products.forEach(product => container.appendChild(product));
}

function filterByCategory() {
    const category = document.getElementById('filterCategory').value;
    const products = document.querySelectorAll('.produto-item');
    
    products.forEach(product => {
        if (category === '' || product.dataset.categoria === category) {
            product.style.display = 'block';
        } else {
            product.style.display = 'none';
        }
    });
}

// Busca de CEP
function initCepLookup() {
    const cepInput = document.getElementById('cep');
    if (cepInput) {
        cepInput.addEventListener('blur', function() {
            const cep = this.value.replace(/\D/g, '');
            if (cep.length === 8) {
                buscarCep();
            }
        });
    }
}

function buscarCep() {
    const cep = document.getElementById('cep').value.replace(/\D/g, '');
    
    if (cep.length !== 8) {
        alert('CEP deve ter 8 dígitos');
        return;
    }
    
    // Mostrar loading
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = 'Buscando...';
    button.disabled = true;
    
    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(response => response.json())
        .then(data => {
            if (data.erro) {
                alert('CEP não encontrado');
                return;
            }
            
            // Preencher campos
            document.getElementById('endereco').value = data.logradouro;
            document.getElementById('bairro').value = data.bairro;
            document.getElementById('cidade').value = data.localidade;
            document.getElementById('estado').value = data.uf;
            
            // Calcular frete
            calcularFrete(cep);
        })
        .catch(error => {
            console.error('Erro ao buscar CEP:', error);
            alert('Erro ao buscar CEP. Tente novamente.');
        })
        .finally(() => {
            button.textContent = originalText;
            button.disabled = false;
        });
}

function calcularFrete(cep) {
    const valor = parseFloat(document.getElementById('subtotal').textContent.replace(/[^\d,]/g, '').replace(',', '.'));
    
    // Fazer requisição para calcular frete
    fetch('/api/calcular_frete.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            cep: cep,
            valor: valor
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const freteValor = parseFloat(data.valor);
            document.getElementById('frete').textContent = formatMoney(freteValor);
            
            // Atualizar total
            atualizarTotal();
            
            // Mostrar informações do frete
            const freteInfo = document.getElementById('freteCalculado');
            freteInfo.innerHTML = `
                <strong>Frete calculado:</strong><br>
                Valor: ${formatMoney(freteValor)}<br>
                Prazo: ${data.prazo} dias úteis
            `;
            freteInfo.style.display = 'block';
        }
    })
    .catch(error => {
        console.error('Erro ao calcular frete:', error);
    });
}

// Métodos de pagamento
function initPaymentMethods() {
    const paymentRadios = document.querySelectorAll('input[name="forma_pagamento"]');
    paymentRadios.forEach(radio => {
        radio.addEventListener('change', handlePaymentChange);
    });
}

function handlePaymentChange() {
    const selectedPayment = document.querySelector('input[name="forma_pagamento"]:checked').value;
    const parcelamentoDiv = document.getElementById('parcelamento');
    const descontoDiv = document.getElementById('descontoDiv');
    
    if (selectedPayment === 'cartao') {
        parcelamentoDiv.style.display = 'block';
        descontoDiv.style.display = 'none';
        carregarParcelas();
    } else {
        parcelamentoDiv.style.display = 'none';
        descontoDiv.style.display = 'none';
        /*
        if (selectedPayment === 'pix') {
            descontoDiv.style.display = 'flex';
            aplicarDescontoPix();
        } else {
            descontoDiv.style.display = 'none';
        }
        */
    }
    
    atualizarTotal();
}

function carregarParcelas() {
    const valor = parseFloat(document.getElementById('subtotal').textContent.replace(/[^\d,]/g, '').replace(',', '.'));
    
    fetch('/api/calcular_parcelas.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            valor: valor
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const select = document.getElementById('parcelasSelect');
            select.innerHTML = '<option value="">Selecione o parcelamento</option>';
            
            data.parcelas.forEach(parcela => {
                const option = document.createElement('option');
                option.value = parcela.numero;
                option.textContent = `${parcela.numero}x de ${formatMoney(parcela.valor)} ${parcela.juros > 0 ? 'com juros' : 'sem juros'}`;
                select.appendChild(option);
            });
        }
    })
    .catch(error => {
        console.error('Erro ao carregar parcelas:', error);
    });
}

function aplicarDescontoPix() {
    const subtotal = parseFloat(document.getElementById('subtotal').textContent.replace(/[^\d,]/g, '').replace(',', '.'));
    const desconto = subtotal * 0.05; // 5% de desconto
    
    document.getElementById('desconto').textContent = '-' + formatMoney(desconto);
}

function atualizarTotal() {
    const subtotal = parseFloat(document.getElementById('subtotal').textContent.replace(/[^\d,]/g, '').replace(',', '.'));
    const freteText = document.getElementById('frete').textContent;
    const frete = freteText === 'A calcular' ? 0 : parseFloat(freteText.replace(/[^\d,]/g, '').replace(',', '.'));
    
    let total = subtotal + frete;
    
    // Aplicar desconto PIX se selecionado
    const pixSelected = document.querySelector('input[value="pix"]:checked');
    if (pixSelected) {
        const desconto = subtotal * 0.05;
        total -= desconto;
    }
    
    document.getElementById('total').textContent = formatMoney(total);
}

// Máscaras de input
function initInputMasks() {
    // Máscara de CEP
    const cepInput = document.getElementById('cep');
    if (cepInput) {
        cepInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').replace(/(\d{5})(\d{3})/, '$1-$2');
        });
    }
    
    // Máscara de telefone
    const telefoneInput = document.querySelector('input[name="telefone"]');
    if (telefoneInput) {
        telefoneInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '')
                .replace(/(\d{2})(\d)/, '($1) $2')
                .replace(/(\d{5})(\d{4})/, '$1-$2');
        });
    }
    
    // Máscara de CPF
    const cpfInput = document.querySelector('input[name="cpf"]');
    if (cpfInput) {
        cpfInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '')
                .replace(/(\d{3})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d{1,2})/, '$1-$2');
        });
    }
}

// Utilitários
function formatMoney(value) {
    return 'R$ ' + value.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function showLoading(element) {
    element.innerHTML = '<div class="loading"></div>';
}

function hideLoading() {
    document.querySelectorAll('.loading').forEach(el => el.remove());
}