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
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const productId = this.dataset.productId;
                const originalContent = this.innerHTML;
                
                // Estado de loading
                this.disabled = true;
                this.classList.add('loading');
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adicionando...';
                
                // Chamar função real de adicionar ao carrinho
                if (typeof addToCart === 'function') {
                    addToCart(productId, 1);
                } else {
                    console.error('Função addToCart não encontrada');
                    showToast('Erro ao adicionar produto', 'error');
                }
                
                // Voltar ao estado original após um delay
                setTimeout(() => {
                    this.disabled = false;
                    this.classList.remove('loading');
                    this.innerHTML = originalContent;
                }, 2000);
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
            const endereco = document.getElementById('endereco');
            if (endereco) endereco.value = data.logradouro;
            const bairro = document.getElementById('bairro');
            if (bairro) bairro.value = data.bairro;
            const cidade = document.getElementById('cidade');
            if (cidade) cidade.value = data.localidade;
            const estado = document.getElementById('estado');
            if (estado) estado.value = data.uf;
            
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
    const subtotalElem = document.getElementById('subtotal');
    const freteElem = document.getElementById('frete');
    const freteInfo = document.getElementById('freteCalculado');
    if (!subtotalElem || !freteElem) {
        console.error('Elementos de subtotal ou frete não encontrados no DOM.');
        return;
    }
    const valor = parseFloat(subtotalElem.textContent.replace(/[^\d,]/g, '').replace(',', '.'));
    fetch('/lojinha_vitatop/api/calcular_frete.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            cep: cep,
            valor: valor
        })
    })
    .then(async response => {
        const text = await response.text();
        // Encontrar o início do JSON
        const jsonStart = text.indexOf('{');
        let data;
        try {
            data = JSON.parse(text.slice(jsonStart));
        } catch (e) {
            console.error('Erro ao fazer parse do JSON:', e);
            if (freteElem) freteElem.textContent = 'Erro';
            if (freteInfo) freteInfo.textContent = 'Erro ao calcular frete.';
            return;
        }
        // Acessar corretamente o valor do frete na estrutura aninhada
        const freteValor = parseFloat(
            data?.data?.data?.frete ?? data?.data?.frete ?? data?.frete ?? data?.valor
        );
        if (data.status === 'success' && !isNaN(freteValor)) {
            if (freteValor === 0) {
                freteElem.textContent = 'Grátis';
            } else {
                freteElem.textContent = formatMoney(freteValor);
            }
            atualizarTotal();
            if (freteInfo) {
                freteInfo.innerHTML = `<strong>Frete calculado:</strong><br>Valor: ${freteValor === 0 ? 'Grátis' : formatMoney(freteValor)}`;
                freteInfo.style.display = 'block';
            }
        } else {
            freteElem.textContent = 'Erro';
            if (freteInfo) freteInfo.textContent = data.message || 'Erro ao calcular frete.';
        }
    })
    .catch(error => {
        console.error('Erro ao calcular frete:', error);
        if (freteElem) freteElem.textContent = 'Erro';
        if (freteInfo) freteInfo.textContent = 'Erro ao calcular frete.';
    });
}

// Métodos de pagamento
function initPaymentMethods() {
    const paymentRadios = document.querySelectorAll('input[name="forma_pagamento"]');
    paymentRadios.forEach(radio => {
        radio.addEventListener('change', handlePaymentChange);
    });
    // Remover chamada de carregarParcelas para não popular opções
    //carregarParcelas();
    // Garantir que só existe 1x sem juros
    const select = document.getElementById('parcelasSelect');
    if (select) {
        select.innerHTML = '<option value="1">1x sem juros</option>';
    }
}

function handlePaymentChange() {
    const selectedPayment = document.querySelector('input[name="forma_pagamento"]:checked').value;
    const parcelamentoDiv = document.getElementById('parcelamento');
    
    if (selectedPayment === 'cartao') {
        parcelamentoDiv.style.display = 'block';
    } else {
        parcelamentoDiv.style.display = 'none';
    }
    
    atualizarTotal();
}

function carregarParcelas() {
    const valor = parseFloat(document.getElementById('subtotal').textContent.replace(/[^\d,]/g, '').replace(',', '.'));
    
    fetch('/lojinha_vitatop/api/calcular_parcelas.php', {
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

function atualizarTotal() {
    const subtotal = parseFloat(document.getElementById('subtotal').textContent.replace(/[^\d,]/g, '').replace(',', '.'));
    const freteText = document.getElementById('frete').textContent;
    const frete = (freteText === 'A calcular' || freteText === 'Grátis') ? 0 : parseFloat(freteText.replace(/[^\d,]/g, '').replace(',', '.'));
    let total = subtotal + frete;
    document.getElementById('total').textContent = formatMoney(total);
    // Atualizar campos ocultos para envio ao backend
    const inputFrete = document.getElementById('inputFrete');
    if (inputFrete) inputFrete.value = frete;
    const inputTotalPedido = document.getElementById('inputTotalPedido');
    if (inputTotalPedido) inputTotalPedido.value = total;
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

    // Máscara de número do cartão (16 a 19 dígitos, espaçado)
    const numeroCartaoInput = document.querySelector('input[name="numero_cartao"]');
    if (numeroCartaoInput) {
        numeroCartaoInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '').slice(0, 19);
            value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
            this.value = value;
        });
    }

    // Máscara de vencimento (MM/YYYY)
    const dataExpiraInput = document.querySelector('input[name="data_expira"]');
    if (dataExpiraInput) {
        dataExpiraInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '').slice(0, 6);
            if (value.length > 2) {
                value = value.replace(/(\d{2})(\d{1,4})/, '$1/$2');
            }
            this.value = value;
        });
    }

    // Máscara de CVV (3 ou 4 dígitos)
    const cvvInput = document.querySelector('input[name="codigo_seguranca"]');
    if (cvvInput) {
        cvvInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').slice(0, 4);
        });
    }
}

// Validação dos campos do cartão no submit do checkout
const checkoutForm = document.getElementById('checkoutForm');
if (checkoutForm) {
    checkoutForm.addEventListener('submit', function(e) {
        const forma = document.querySelector('input[name="forma_pagamento"]:checked');
        if (forma && forma.value === 'cartao') {
            let erro = '';
            // Número do cartão
            const numero = document.querySelector('input[name="numero_cartao"]').value.replace(/\s/g, '');
            if (numero.length < 16 || numero.length > 19) {
                erro = 'Número do cartão inválido.';
            }
            // Vencimento
            const venc = document.querySelector('input[name="data_expira"]').value;
            if (!/^\d{2}\/\d{4}$/.test(venc)) {
                erro = 'Data de vencimento inválida. Use MM/YYYY.';
            } else {
                const [mes, ano] = venc.split('/').map(Number);
                const dataAtual = new Date();
                const anoAtual = dataAtual.getFullYear();
                const mesAtual = dataAtual.getMonth() + 1;
                if (mes < 1 || mes > 12 || ano < anoAtual || (ano === anoAtual && mes < mesAtual)) {
                    erro = 'Data de vencimento inválida ou expirada.';
                }
            }
            // CVV
            const cvv = document.querySelector('input[name="codigo_seguranca"]').value;
            if (cvv.length < 3 || cvv.length > 4) {
                erro = 'CVV inválido.';
            }
            if (erro) {
                e.preventDefault();
                alert(erro);
                return false;
            }
        }
    });
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

// Função para atualizar seção de sugestões de frete grátis
function atualizarSugestoesFrete() {
    const sugestaoSection = document.querySelector('.frete-gratis-sugestao');
    if (!sugestaoSection) return;
    
    // Buscar total atual do carrinho
    const subtotalElement = document.getElementById('cart-subtotal') || document.getElementById('subtotal');
    if (!subtotalElement) return;
    
    const totalText = subtotalElement.textContent;
    const total = parseFloat(totalText.replace(/[^\d,]/g, '').replace(',', '.'));
    
    // Se já tem frete grátis, ocultar seção
    if (total >= 300) {
        sugestaoSection.style.display = 'none';
        return;
    }
    
    // Atualizar valor que falta
    const faltaElement = sugestaoSection.querySelector('.falta-valor');
    if (faltaElement) {
        const falta = 300 - total;
        faltaElement.innerHTML = `Faltam R$ ${falta.toFixed(2).replace('.', ',')} para frete grátis`;
    }
    
    // Atualizar badges "Completa!"
    const cards = sugestaoSection.querySelectorAll('.produto-sugestao-card');
    cards.forEach(card => {
        const precoElement = card.querySelector('.produto-preco');
        const badgeElement = card.querySelector('.completa-frete');
        
        if (precoElement) {
            const precoText = precoElement.textContent;
            const preco = parseFloat(precoText.replace(/[^\d,]/g, '').replace(',', '.'));
            
                            if (preco >= (300 - total)) {
                    if (!badgeElement) {
                        const badge = document.createElement('div');
                        badge.className = 'completa-frete';
                        badge.innerHTML = 'Completa';
                        card.appendChild(badge);
                    }
                } else {
                if (badgeElement) {
                    badgeElement.remove();
                }
            }
        }
    });
}

// Função para recarregar seção de sugestões via AJAX
function recarregarSugestoesFrete() {
    const sugestaoSection = document.querySelector('.frete-gratis-sugestao');
    if (!sugestaoSection) return;
    
    // Mostrar loading
    sugestaoSection.style.opacity = '0.5';
    
    // Fazer requisição AJAX para recarregar a seção
    fetch(window.location.href)
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const novaSugestao = doc.querySelector('.frete-gratis-sugestao');
            
            if (novaSugestao) {
                sugestaoSection.innerHTML = novaSugestao.innerHTML;
            } else {
                sugestaoSection.style.display = 'none';
            }
            
            sugestaoSection.style.opacity = '1';
        })
        .catch(error => {
            console.error('Erro ao recarregar sugestões:', error);
            sugestaoSection.style.opacity = '1';
        });
}