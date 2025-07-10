// assets/js/carrinho.js

// Funções do carrinho
function addToCart(produtoId, quantidade = 1) {
    // Determinar o caminho correto da API
    const apiPath = window.location.pathname.includes('/lojinha_vitatop/') 
        ? '/lojinha_vitatop/api/carrinho.php' 
        : '/api/carrinho.php';
    
    fetch(apiPath, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'add',
            produto_id: produtoId,
            quantidade: quantidade
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Atualizar contador do carrinho
            updateCartCounter();
            
            // Atualizar seção de sugestões de frete grátis
            if (typeof atualizarSugestoesFrete === 'function') {
                setTimeout(atualizarSugestoesFrete, 500);
            }
            
            // Mostrar feedback
            showToast('Produto adicionado à sua Sacola!', 'success');
        } else {
            showToast('Erro ao adicionar produto', 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showToast('Erro ao adicionar produto', 'error');
    });
}

function removeFromCart(produtoId) {
    if (confirm('Deseja remover este item do carrinho?')) {
        fetch('/lojinha_vitatop/api/carrinho.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'remove',
                produto_id: produtoId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                location.reload();
            }
        });
    }
}

function updateCartQuantity(produtoId, quantidade) {
    fetch('/lojinha_vitatop/api/carrinho.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'update',
            produto_id: produtoId,
            quantidade: quantidade
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            updateCartDisplay();
        }
    });
}

function increaseQuantity(produtoId) {
    const input = document.querySelector(`input[name="quantidade[${produtoId}]"]`);
    const currentValue = parseInt(input.value);
    input.value = currentValue + 1;
    updateCartQuantity(produtoId, currentValue + 1);
}

function decreaseQuantity(produtoId) {
    const input = document.querySelector(`input[name="quantidade[${produtoId}]"]`);
    const currentValue = parseInt(input.value);
    if (currentValue > 1) {
        input.value = currentValue - 1;
        updateCartQuantity(produtoId, currentValue - 1);
    }
}

function updateCartCounter() {
    // Determinar o caminho correto da API
    const apiPath = window.location.pathname.includes('/lojinha_vitatop/') 
        ? '/lojinha_vitatop/api/carrinho.php?action=count' 
        : '/api/carrinho.php?action=count';
    
    fetch(apiPath)
        .then(response => response.json())
        .then(data => {
            // Atualizar o badge principal do header
            const cartBadge = document.querySelector('.cart-badge');
            if (cartBadge) {
                if (data.count > 0) {
                    cartBadge.textContent = data.count;
                    cartBadge.style.display = 'inline';
                } else {
                    cartBadge.style.display = 'none';
                }
            }
            
            // Atualizar também o badge do menu mobile
            const mobileBadge = document.querySelector('.mobile-nav .badge');
            if (mobileBadge) {
                if (data.count > 0) {
                    mobileBadge.textContent = data.count;
                    mobileBadge.style.display = 'inline';
                } else {
                    mobileBadge.style.display = 'none';
                }
            }
            
            // Se não existir badge, criar um
            if (!cartBadge && data.count > 0) {
                const cartLink = document.querySelector('.cart-link');
                if (cartLink) {
                    const newBadge = document.createElement('span');
                    newBadge.className = 'cart-badge';
                    newBadge.textContent = data.count;
                    cartLink.appendChild(newBadge);
                }
            }
        })
        .catch(error => {
            console.error('Erro ao atualizar contador do carrinho:', error);
        });
}

function updateCartDisplay() {
    // Recalcular totais na página do carrinho
    let total = 0;
    document.querySelectorAll('.cart-item').forEach(item => {
        const preco = parseFloat(item.dataset.preco);
        const quantidade = parseInt(item.querySelector('.quantity-input').value);
        const subtotal = preco * quantidade;
        
        item.querySelector('.item-total').textContent = formatMoney(subtotal);
        total += subtotal;
    });
    
    document.getElementById('cart-total').textContent = formatMoney(total);
    document.getElementById('cart-subtotal').textContent = formatMoney(total);
    
    // Atualizar seção de sugestões de frete grátis
    if (typeof atualizarSugestoesFrete === 'function') {
        setTimeout(atualizarSugestoesFrete, 300);
    }
    
    // Recalcular frete automaticamente se o CEP estiver preenchido
    const cepInput = document.getElementById('cep');
    if (cepInput && cepInput.value.length >= 8) {
        calcularFreteCarrinho();
    }
}

function clearCart() {
    if (confirm('Deseja limpar todo o carrinho?')) {
        fetch('/lojinha_vitatop/api/carrinho.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'clear'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                location.reload();
            }
        });
    }
}

// Função para calcular o frete
function calcularFreteCarrinho() {
    const cep = document.getElementById('cep').value;
    const resultado = document.getElementById('frete-resultado');
    const freteValorElement = document.getElementById('frete-valor');
    resultado.textContent = '';
    freteValorElement.textContent = 'Calculando...';
    if (!cep || cep.length < 8) {
        resultado.textContent = 'Digite um CEP válido.';
        freteValorElement.textContent = 'A calcular';
        return;
    }
    const subtotalText = document.getElementById('cart-subtotal').textContent.replace(/[^\d,\.]/g, '').replace(',', '.');
    const valor = parseFloat(subtotalText);
    const body = { cep: cep, valor: valor };
    console.log('Enviando para calcular_frete.php:', body);
    fetch('/lojinha_vitatop/api/calcular_frete.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body)
    })
    .then(async response => {
        const text = await response.text();
        console.log('Texto bruto da resposta:', text);
        // Encontrar o início do JSON
        const jsonStart = text.indexOf('{');
        let data;
        try {
            data = JSON.parse(text.slice(jsonStart));
        } catch (e) {
            console.error('Erro ao fazer parse do JSON:', e);
            freteValorElement.textContent = 'Erro';
            resultado.textContent = 'Erro ao calcular frete.';
            return;
        }
        console.log('Resposta recebida do calcular_frete.php:', data);
        // Acessar corretamente o valor do frete na estrutura aninhada
        const freteValor = parseFloat(
            data?.data?.data?.frete ?? data?.data?.frete ?? data?.frete ?? data?.valor
        );
        if (data.status === 'success' && !isNaN(freteValor)) {
            if (freteValor === 0) {
                freteValorElement.textContent = 'Grátis';
            } else {
                freteValorElement.textContent = formatMoney(freteValor);
            }
            const subtotal = parseFloat(document.getElementById('cart-subtotal').textContent.replace(/[^\d,\.]/g, '').replace(',', '.'));
            if (!isNaN(subtotal)) {
                const total = subtotal + freteValor;
                document.getElementById('cart-total').textContent = formatMoney(total);
            }
            resultado.textContent = 'Frete calculado!';
        } else {
            freteValorElement.textContent = 'Erro';
            resultado.textContent = data.message || 'Erro ao calcular frete.';
        }
    })
    .catch((error) => {
        console.error('Erro no fetch do frete:', error);
        freteValorElement.textContent = 'Erro';
        resultado.textContent = 'Erro ao calcular frete.';
    });
}

// Toast notifications
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    
    const icon = type === 'success' ? 'fa-check-circle' : 
                 type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle';
    
    toast.innerHTML = `
        <div class="toast-content">
            <i class="fas ${icon} me-2"></i>
            <span class="toast-message">${message}</span>
            <button class="toast-close" onclick="this.parentElement.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    // Adicionar estilos se não existirem
    if (!document.getElementById('toast-styles')) {
        const styles = document.createElement('style');
        styles.id = 'toast-styles';
        styles.textContent = `
            .toast-notification {
                position: fixed;
                top: 20px;
                right: 20px;
                background: white;
                border-radius: 12px;
                box-shadow: 0 8px 25px rgba(0,0,0,0.15);
                padding: 16px 20px;
                max-width: 350px;
                z-index: 9999;
                animation: slideInRight 0.4s ease;
                border: 1px solid #e9ecef;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }
            .toast-success { 
                border-left: 4px solid #28a745; 
                background: linear-gradient(135deg, #f8fff9, #ffffff);
            }
            .toast-error { 
                border-left: 4px solid #dc3545; 
                background: linear-gradient(135deg, #fff8f8, #ffffff);
            }
            .toast-info { 
                border-left: 4px solid #17a2b8; 
                background: linear-gradient(135deg, #f8fdff, #ffffff);
            }
            .toast-content {
                display: flex;
                align-items: center;
                gap: 12px;
            }
            .toast-message {
                flex: 1;
                font-size: 0.95rem;
                font-weight: 500;
                color: #333;
            }
            .toast-close {
                background: none;
                border: none;
                font-size: 14px;
                cursor: pointer;
                color: #6c757d;
                padding: 4px;
                border-radius: 50%;
                transition: all 0.2s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 24px;
                height: 24px;
            }
            .toast-close:hover {
                background: #f8f9fa;
                color: #333;
            }
            .toast-success .fas.fa-check-circle { color: #28a745; }
            .toast-error .fas.fa-exclamation-circle { color: #dc3545; }
            .toast-info .fas.fa-info-circle { color: #17a2b8; }
            @keyframes slideInRight {
                from { 
                    transform: translateX(100%); 
                    opacity: 0; 
                }
                to { 
                    transform: translateX(0); 
                    opacity: 1; 
                }
            }
            @media (max-width: 768px) {
                .toast-notification {
                    top: 10px;
                    right: 10px;
                    left: 10px;
                    max-width: none;
                    animation: slideInTop 0.4s ease;
                }
                @keyframes slideInTop {
                    from { 
                        transform: translateY(-100%); 
                        opacity: 0; 
                    }
                    to { 
                        transform: translateY(0); 
                        opacity: 1; 
                    }
                }
            }
        `;
        document.head.appendChild(styles);
    }
    
    document.body.appendChild(toast);
    
    // Remover automaticamente após 4 segundos
    setTimeout(() => {
        if (toast.parentElement) {
            toast.style.animation = 'slideOutRight 0.3s ease forwards';
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 300);
        }
    }, 4000);
}

// Inicializar quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    // Atualizar contador do carrinho
    updateCartCounter();
    
    // Adicionar listeners para inputs de quantidade
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const produtoId = this.name.match(/\d+/)[0];
            const quantidade = parseInt(this.value);
            updateCartQuantity(produtoId, quantidade);
        });
    });
    
    // Atualizar contador periodicamente (a cada 30 segundos)
    setInterval(updateCartCounter, 30000);
});

// Função global para ser chamada de outros arquivos
window.updateCartCounter = updateCartCounter;
window.showToast = showToast;