<?php
// pages/pedido.php
$codigo_pedido = $_GET['codigo'] ?? null;
$tipo_pagamento = $_GET['tipo'] ?? null;

if (!$codigo_pedido) {
    header('Location: ?page=home');
    exit;
}

// Buscar detalhes do pedido
$pedido_response = detalhesPedido(API_URL, API_KEY, $codigo_pedido);
$pedido = null;

if ($pedido_response && isset($pedido_response['status']) && $pedido_response['status'] === 'success') {
    $pedido = $pedido_response['data'];
}
$pedidoId = $pedido['data']['pedido_id'] ?? '';

// Obter dados do último pedido da sessão
$ultimo_pedido = $_SESSION['ultimo_pedido'] ?? null;
$dados_pagamento = null;

if ($pedido) {
    $dados_pagamento = $pedido['data']['forma_pagamento'];
    
    // Limpar da sessão após uso
    unset($_SESSION['ultimo_pedido']);
}

// Obter dados específicos do tipo de pagamento
$dados_pix = $_SESSION['dados_pix'] ?? null;
$dados_boleto = $_SESSION['dados_boleto'] ?? null;
$dados_cartao = $_SESSION['dados_cartao'] ?? null;

// Limpar dados da sessão após uso
unset($_SESSION['dados_pix'], $_SESSION['dados_boleto'], $_SESSION['dados_cartao']);
?>

<div class="container">
    <div class="page-header text-center py-5">
        <div class="success-icon mb-3">
            <i class="fas fa-check-circle fa-4x text-success"></i>
        </div>
        <h1 class="text-success">Pedido Realizado com Sucesso!</h1>
        <p class="lead">Obrigado por sua compra. Seu pedido foi registrado e será processado em breve.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-receipt me-2"></i>Detalhes do Pedido</h5>
                </div>
                <div class="card-body">
                    <div class="order-info mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Número do Pedido:</strong> #<?php echo $codigo_pedido; ?></p>
                                <p><strong>Data:</strong> <?php echo date('d/m/Y H:i'); ?></p>
                                <p><strong>Status:</strong> 
                                    <?php if ($dados_pagamento && isset($dados_pagamento['transacao_mensagem'])): ?>
                                        <span class="badge bg-primary"><?= htmlspecialchars($dados_pagamento['transacao_mensagem']) ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Aguardando Pagamento</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Distribuidor:</strong> <?php echo getAfiliado(); ?></p>
                                <p><strong>Forma de Pagamento:</strong> 
                                    <?php 
                                    $formas = ['pix' => 'PIX', 'boleto' => 'Boleto Bancário', 'cartao' => 'Cartão de Crédito'];
                                    echo $formas[$tipo_pagamento] ?? 'N/A'; 
                                    ?>
                                </p>
                                <?php if ($dados_pagamento && isset($dados_pagamento['valor_total'])): ?>
                                    <p><strong>Valor Total:</strong> <?php echo formatPrice($dados_pagamento['valor_total']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Informações específicas do pagamento -->
                    <?php if ($tipo_pagamento === 'pix' && $dados_pagamento): ?>
                        <div class="payment-details mb-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6><i class="fas fa-qrcode me-2"></i>Dados para Pagamento PIX</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?php if (isset($dados_pagamento['pix_qrcode']) && $dados_pagamento['pix_qrcode']): ?>
                                                <div class="text-center mb-3">
                                                    <p><strong>QR Code PIX:</strong></p>
                                                    <img src="<?php echo $dados_pagamento['pix_qrcode']; ?>" 
                                                         alt="QR Code PIX" class="img-fluid" style="max-width: 200px;">
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?php if (isset($dados_pagamento['pix_key']) && $dados_pagamento['pix_key']): ?>
                                                <div class="mb-3">
                                                    <label class="form-label"><strong>Chave PIX Copia e Cola:</strong></label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" 
                                                               value="<?php echo $dados_pagamento['pix_key']; ?>" 
                                                               id="pixKey" readonly>
                                                        <button class="btn btn-outline-secondary" type="button" 
                                                                onclick="copiarPixKey()">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <strong>Como pagar:</strong><br>
                                                1. Escaneie o QR Code ou copie a chave PIX<br>
                                                2. Faça o pagamento no seu banco ou app<br>
                                                3. O pagamento será confirmado automaticamente
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php elseif ($tipo_pagamento === 'boleto' && $dados_pagamento): ?>
                        <div class="payment-details mb-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6><i class="fas fa-barcode me-2"></i>Dados do Boleto Bancário</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <?php if (isset($dados_pagamento['boleto_linhadigitavel'])): ?>
                                                <div class="mb-3">
                                                    <label class="form-label"><strong>Linha Digitável:</strong></label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" 
                                                               value="<?php echo $dados_pagamento['boleto_linhadigitavel']; ?>" 
                                                               id="linhaDigitavel" readonly>
                                                        <button class="btn btn-outline-secondary" type="button" 
                                                                onclick="copiarLinhaDigitavel()">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if (isset($dados_pagamento['data_vencimento'])): ?>
                                                <p><strong>Vencimento:</strong> <?php echo date('d/m/Y', strtotime($dados_pagamento['data_vencimento'])); ?></p>
                                            <?php endif; ?>
                                            
                                            <?php if (isset($dados_pagamento['boleto_impressao'])): ?>
                                                <div class="mt-3">
                                                    <a href="<?php echo $dados_pagamento['boleto_impressao']; ?>" 
                                                       target="_blank" class="btn btn-primary">
                                                        <i class="fas fa-print me-2"></i>Imprimir Boleto
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                <strong>Importante:</strong><br>
                                                Pague até o vencimento para evitar multa e juros.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php elseif ($tipo_pagamento === 'cartao' && $dados_pagamento): ?>
                        <div class="payment-details mb-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6><i class="fas fa-credit-card me-2"></i>Dados do Cartão de Crédito</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?php if (isset($dados_pagamento['bandeira'])): ?>
                                                <p><strong>Bandeira:</strong> <?php echo $dados_pagamento['bandeira']; ?></p>
                                            <?php endif; ?>
                                            
                                            <?php if (isset($dados_pagamento['cartao_numero'])): ?>
                                                <p><strong>Cartão:</strong> **** **** **** <?php echo substr($dados_pagamento['cartao_numero'], -4); ?></p>
                                            <?php endif; ?>
                                            
                                            <?php if (isset($dados_pagamento['nome_cartao'])): ?>
                                                <p><strong>Portador:</strong> <?php echo $dados_pagamento['nome_cartao']; ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?php if (isset($dados_pagamento['status_mensagem'])): ?>
                                                <div class="alert alert-<?php echo $dados_pagamento['status_compra'] == 3 ? 'success' : 'info'; ?>">
                                                    <strong>Status:</strong> <?php echo $dados_pagamento['status_mensagem']; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($pedido): ?>
                        <div class="order-items">
                            <h6>Itens do Pedido</h6>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Produto</th>
                                            <th>Quantidade</th>
                                            <th>Valor Unitário</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pedido['data']['itens'] as $item): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($item['descricao']); ?></td>
                                                <td><?php echo $item['qtde']; ?></td>
                                                <td><?php echo formatPrice($item['preco']); ?></td>
                                                <td><?php echo formatPrice($item['total']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3">Subtotal:</th>
                                            <th><?php echo formatPrice($pedido['data']['cliente']['valor_produto'] ?? 0); ?></th>
                                        </tr>
                                        <tr>
                                            <th colspan="3">Frete:</th>
                                            <th><?php echo formatPrice($pedido['data']['cliente']['frete'] ?? 0); ?></th>
                                        </tr>
                                        <tr>
                                            <th colspan="3">Total do Pedido:</th>
                                            <th><?php echo formatPrice($pedido['data']['cliente']['valor_total'] ?? 0); ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>


                    <div class="next-steps mt-4">
                        <h6>Próximos Passos</h6>
                        <div class="steps">
                            <div class="step">
                                <i class="fas fa-credit-card text-primary me-2"></i>
                                <span>
                                    <?php if ($tipo_pagamento === 'pix'): ?>
                                        Efetue o pagamento via PIX usando o QR Code ou chave
                                    <?php elseif ($tipo_pagamento === 'boleto'): ?>
                                        Pague o boleto até a data de vencimento
                                    <?php else: ?>
                                        Aguarde a confirmação do pagamento no cartão
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="step">
                                <i class="fas fa-box text-muted me-2"></i>
                                <span>Aguarde a confirmação e separação dos produtos</span>
                            </div>
                            <div class="step">
                                <i class="fas fa-truck text-muted me-2"></i>
                                <span>Receba seu pedido no endereço informado</span>
                            </div>
                        </div>
                    </div>

                    <div class="payment-status mt-4 p-3 bg-light rounded">
                        <h6>Status do Pagamento</h6>
                        <div id="paymentStatus">
                            <button class="btn btn-primary" onclick="verificarPagamento()">
                                <i class="fas fa-sync-alt me-2"></i>Verificar Pagamento
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="?page=home" class="btn btn-outline-primary me-2">
                    <i class="fas fa-home me-2"></i>Voltar ao Início
                </a>
                <a href="?page=produtos" class="btn btn-primary">
                    <i class="fas fa-shopping-bag me-2"></i>Continuar Comprando
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.step {
    display: flex;
    align-items: flex-start;
    margin-bottom: 15px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 8px;
}

.step i {
    margin-top: 2px;
    font-size: 1.1rem;
}

.payment-details .card {
    border: 1px solid #dee2e6;
}

.payment-details .card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.order-items {
    margin-top: 30px;
}

.order-items table {
    margin-bottom: 0;
}

.success-icon i {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
    100% {
        transform: scale(1);
    }
}
</style>

<script>
function verificarPagamento() {
    const button = event.target;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Verificando...';
    
    fetch('api/verificar_pagamento.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            pedido_id: '<?php echo $pedidoId; ?>'
        })
    })
    .then(response => response.json())
    .then(data => {
        const statusDiv = document.getElementById('paymentStatus');
        console.log(data);
        if (data.status == '3') {
            statusDiv.innerHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    Pagamento confirmado! Seu pedido será processado em breve.
                </div>
            `;
            
            // Recarregar a página após 3 segundos para atualizar o status
            setTimeout(() => {
                window.location.reload();
            }, 3000);
            
        } else if (data.status === 'pending' || data.status === '1') {
            statusDiv.innerHTML = `
                <div class="alert alert-warning">
                    <i class="fas fa-clock me-2"></i>
                    Pagamento ainda não identificado. Aguarde alguns minutos e tente novamente.
                </div>
                <button class="btn btn-primary mt-2" onclick="verificarPagamento()">
                    Verificar Novamente
                </button>
            `;
        } else {
            statusDiv.innerHTML = `
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Status: ${data.message || 'Aguardando pagamento'}
                </div>
                <button class="btn btn-primary mt-2" onclick="verificarPagamento()">
                    Verificar Novamente
                </button>
            `;
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-sync-alt me-2"></i>Verificar Pagamento';
        alert('Erro ao verificar pagamento. Tente novamente.');
    });
}

function copiarPixKey() {
    const pixKey = document.getElementById('pixKey');
    if (pixKey) {
        pixKey.select();
        pixKey.setSelectionRange(0, 99999);
        
        navigator.clipboard.writeText(pixKey.value).then(() => {
            showToast('Chave PIX copiada!', 'success');
        });
    }
}

function copiarLinhaDigitavel() {
    const linhaDigitavel = document.getElementById('linhaDigitavel');
    if (linhaDigitavel) {
        linhaDigitavel.select();
        linhaDigitavel.setSelectionRange(0, 99999);
        
        navigator.clipboard.writeText(linhaDigitavel.value).then(() => {
            showToast('Linha digitável copiada!', 'success');
        });
    }
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed`;
    toast.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s ease;
    `;
    toast.innerHTML = `
        <i class="fas fa-check-circle me-2"></i>
        ${message}
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(0)';
    }, 100);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (document.body.contains(toast)) {
                document.body.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

// Auto-verificar pagamento a cada 30 segundos se for PIX
<?php if ($tipo_pagamento === 'pix'): ?>
setInterval(() => {
    // Verificar silenciosamente sem alterar a UI
    fetch('api/verificar_pagamento.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            pedido_id: '<?php echo $pedidoId; ?>'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === '3') {
            // Pagamento confirmado - recarregar página
            window.location.reload();
        }
    })
    .catch(error => {
        console.log('Verificação automática falhou:', error);
    });
}, 30000); // 30 segundos
<?php endif; ?>
</script>