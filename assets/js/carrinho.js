// assets/js/carrinho.js - Versão Atualizada

// Funções do carrinho
function addToCart(produtoId, quantidade = 1) {
    fetch('api/carrinho.php', {
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
            
            // Mostrar feedback
            showToast('Produto adicionado ao carrinho!', 'success');
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
        fetch('api/carrinho.php', {
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
                // Se estamos na página do carrinho, remover da interface
                const itemElement = document.getElementById('item-' + produtoId);
                if (itemElement) {
                    itemElement.style.transition = 'opacity 0.3s ease';
                    itemElement.style.opacity = '0';
                    
                    setTimeout(() => {
                        itemElement.remove();
                        updateCartDisplay();
                        
                        // Verificar se carrinho está vazio
                        const cartItems = document.getElementById('cartItems');
                        if (cartItems && cartItems.children.length === 0) {
                            location.reload(); // Recarregar para mostrar carrinho vazio
                        }
                    }, 300);
                } else {
                    // Se não estamos na página do carrinho, apenas mostrar mensagem
                    showToast('Item removido do carrinho', 'success');
                }
                
                updateCartCounter();
            } else {
                showToast('Erro ao remover produto', 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showToast('Erro ao remover produto', 'error');
        });
    }
}

function updateCartQuantity(produtoId, quantidade) {
    if (quantidade < 1) {
        removeFromCart(produtoId);
        return;
    }
    
    fetch('api/carrinho.php', {
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
            updateCartCounter();
        }
    })
    .catch(error => {
        console.error('Erro:', error);
    });
}

function increaseQuantity(produtoId) {
    const input = document.querySelector(`input[name="quantidade[${produtoId}]"], input[data-produto-id="${produtoId}"]`);
    if (input) {
        const currentValue = parseInt(input.value);
        input.value = currentValue + 1;
        updateCartQuantity(produtoId, currentValue + 1);
    }
}

function decreaseQuantity(produtoId) {
    const input = document.querySelector(`input[name="quantidade[${produtoId}]"], input[data-produto-id="${produtoId}"]`);
    if (input) {
        const currentValue = parseInt(input.value);
        if (currentValue > 1) {
            input.value = currentValue - 1;
            updateCartQuantity(produtoId, currentValue - 1);
        } else {
            removeFromCart(produtoId);
        }
    }
}

function updateCartCounter() {
    fetch('api/carrinho.php?action=count')
        .then(response => response.json())
        .then(data => {
            const badge = document.querySelector('.cart-badge');
            if (badge) {
                if (data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = 'inline';
                } else {
                    badge.style.display = 'none';
                }
            }
        })
        .catch(error => {
            console.error('Erro ao atualizar contador:', error);
        });
}

function updateCartDisplay() {
    // Recalcular totais na página do carrinho
    let total = 0;
    let itemCount = 0;
    
    document.querySelectorAll('.cart-item').forEach(item => {
        const quantityInput = item.querySelector('.quantity-input');
        if (quantityInput) {
            const quantidade = parseInt(quantityInput.value) || 0;
            
            // Buscar preço unitário
            const priceElement = item.querySelector('.col-md-2:nth-child(5) strong');
            if (priceElement) {
                const priceText = priceElement.textContent.replace('R$', '').replace(/\./g, '').replace(',', '.');
                const preco = parseFloat(priceText) || 0;
                
                const subtotal = preco * quantidade;
                
                // Atualizar total do item
                const totalElement = item.querySelector('.item-total');
                if (totalElement) {
                    totalElement.textContent = formatMoney(subtotal);
                }
                
                total += subtotal;
                itemCount += quantidade;
            }
        }
    });
    
    // Atualizar elementos da página se existirem
    const subtotalElement = document.getElementById('cart-subtotal');
    const totalElement = document.getElementById('cart-total');
    const countElement = document.getElementById('cart-count');
    
    if (subtotalElement) subtotalElement.textContent = formatMoney(total);
    if (totalElement) totalElement.textContent = formatMoney(total);
    if (countElement) countElement.textContent = itemCount;
}

function clearCart() {
    if (confirm('Deseja limpar todo o carrinho?')) {
        fetch('api/carrinho.php', {
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
                // Se estamos na página do carrinho, recarregar
                if (document.getElementById('cartItems')) {
                    location.reload();
                } else {
                    // Apenas atualizar contador
                    updateCartCounter();
                    showToast('Carrinho limpo!', 'success');
                }
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showToast('Erro ao limpar carrinho', 'error');
        });
    }
}

// Função auxiliar para formatar valores monetários
function formatMoney(value) {
    return 'R$ ' + value.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

// Toast notifications
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <span class="toast-message">${message}</span>
            <button class="toast-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
        </div>
    `;
    
    // Adicionar estilos se não existirem
    if (!document.getElementById('toast-styles')) {
        const styles = document.createElement('style');
        styles.id = 'toast-styles';
        styles.textContent = `
            .toast {
                position: fixed;
                top: 20px;
                right: 20px;
                background: white;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                padding: 15px;
                max-width: 300px;
                z-index: 9999;
                animation: slideIn 0.3s ease;
            }
            .toast-success { border-left: 4px solid #28a745; }
            .toast-error { border-left: 4px solid #dc3545; }
            .toast-info { border-left: 4px solid #17a2b8; }
            .toast-content {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .toast-close {
                background: none;
                border: none;
                font-size: 18px;
                cursor: pointer;
                margin-left: 10px;
            }
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        `;
        document.head.appendChild(styles);
    }
    
    document.body.appendChild(toast);
    
    // Remover automaticamente após 5 segundos
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 5000);
}

// Função para lidar com mudanças nos inputs de quantidade (para página do carrinho)
function onQuantityChange(input) {
    const produtoId = input.getAttribute('data-produto-id') || 
                     input.name.match(/\d+/)?.[0]; // Extrair ID do name="quantidade[123]"
    
    if (produtoId) {
        const quantidade = parseInt(input.value) || 0;
        updateCartQuantity(produtoId, quantidade);
    }
}

// Inicializar quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    updateCartCounter();
    
    // Adicionar listeners para inputs de quantidade na página do carrinho
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            onQuantityChange(this);
        });
    });
    
    // Detectar se estamos na página do carrinho e fazer configurações específicas
    if (document.getElementById('cartItems')) {
        // Estamos na página do carrinho
        console.log('Página do carrinho carregada');
        
        // Adicionar event listeners para botões de incremento/decremento
        document.querySelectorAll('[onclick*="increaseQuantity"]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const match = this.getAttribute('onclick').match(/increaseQuantity\((\d+)\)/);
                if (match) {
                    increaseQuantity(match[1]);
                }
            });
        });
        
        document.querySelectorAll('[onclick*="decreaseQuantity"]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const match = this.getAttribute('onclick').match(/decreaseQuantity\((\d+)\)/);
                if (match) {
                    decreaseQuantity(match[1]);
                }
            });
        });
    }
});