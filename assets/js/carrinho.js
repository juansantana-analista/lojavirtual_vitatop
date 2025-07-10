// assets/js/carrinho.js

// Funções do carrinho
function addToCart(produtoId, quantidade = 1) {
    fetch('/lojinha_vitatop/api/carrinho.php', {
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
    fetch('/lojinha_vitatop/api/carrinho.php?action=count')
        .then(response => response.json())
        .then(data => {
            const badge = document.querySelector('.badge');
            if (badge) {
                if (data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = 'inline';
                } else {
                    badge.style.display = 'none';
                }
            }
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
function calcularFrete() {
    const cep = document.getElementById('cep').value;
    const resultado = document.getElementById('frete-resultado');
    const freteValor = document.getElementById('frete-valor');
    resultado.textContent = '';
    freteValor.textContent = 'Calculando...';
    if (!cep || cep.length < 8) {
        resultado.textContent = 'Digite um CEP válido.';
        freteValor.textContent = 'A calcular';
        return;
    }
    // Captura o valor do subtotal do carrinho
    const subtotalText = document.getElementById('cart-subtotal').textContent.replace(/[^\d,\.]/g, '').replace(',', '.');
    const valor = parseFloat(subtotalText);
    fetch('/lojinha_vitatop/api/calcular_frete.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ cep: cep, valor: valor })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            freteValor.textContent = data.valor_formatado || data.valor;
            resultado.textContent = data.prazo ? `Prazo: ${data.prazo} dia(s)` : '';
            // Atualizar total do carrinho se necessário
            if (!isNaN(valor)) {
                const frete = parseFloat(data.valor);
                if (!isNaN(frete)) {
                    const total = valor + frete;
                    document.getElementById('cart-total').textContent = formatMoney(total);
                }
            }
        } else {
            freteValor.textContent = 'Erro';
            resultado.textContent = data.message || 'Erro ao calcular frete.';
        }
    })
    .catch(() => {
        freteValor.textContent = 'Erro';
        resultado.textContent = 'Erro ao calcular frete.';
    });
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

// Inicializar quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    updateCartCounter();
    
    // Adicionar listeners para inputs de quantidade
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const produtoId = this.name.match(/\d+/)[0];
            const quantidade = parseInt(this.value);
            updateCartQuantity(produtoId, quantidade);
        });
    });
});