<?php
// pages/checkout.php

// Verificar se há itens no carrinho
if (empty($_SESSION['carrinho'])) {
    header('Location: ?page=carrinho');
    exit;
}

// Variáveis para controle
$erro_checkout = null;
$success_checkout = null;

// Processar envio do formulário
if ($_POST['action'] ?? '' === 'finalizar_pedido') {
    try {
        // Adequação: Estrutura de dados do cliente conforme esperado pela GravarPedido
        $dadosCliente = [
            'nome_completo' => $_POST['nome'],           
            'email' => $_POST['email'],
            'celular' => $_POST['telefone'],             
            'tipo' => 'F',                               
            'cpfcnpj' => preg_replace('/[^0-9]/', '', $_POST['cpf']), 
            'inscricao_estadual' => '',                  
            'cep' => preg_replace('/[^0-9]/', '', $_POST['cep']), 
            'endereco' => $_POST['endereco'],
            'numero' => $_POST['numero'],
            'complemento' => $_POST['complemento'] ?? '',
            'bairro' => $_POST['bairro'],
            'cidade' => $_POST['cidade'],                
            'estado' => $_POST['estado']                 
        ];
        
        // Buscar produtos do carrinho
        $produtos_response = listarProdutos();
        $todos_produtos = $produtos_response['status'] === 'success' ? $produtos_response['data']['data'] : [];
        
        // Adequação: Estrutura de itens conforme esperado pela GravarPedido
        $itens_pedido = [];
        foreach ($_SESSION['carrinho'] as $produto_id => $quantidade) {
            foreach ($todos_produtos as $produto) {
                if ($produto['id'] == $produto_id) {
                    $itens_pedido[] = [
                        'produto_id' => $produto_id,
                        'qtde' => $quantidade,                    
                        'preco_unitario' => $produto['preco_lojavirtual'],
                        'total' => $produto['preco_lojavirtual'] * $quantidade
                    ];
                    break;
                }
            }
        }
        
        // Adequação: Mapeamento das formas de pagamento conforme esperado pela GravarPedido
        $opcao_pagamento_map = [
            'cartao' => 1,  // CARTAO
            'boleto' => 2,  // BOLETO  
            'pix' => 3      // PIX
        ];
        
        $forma_pagamento = $_POST['forma_pagamento'];
        $opcao_pagamento = $opcao_pagamento_map[$forma_pagamento] ?? 3; // Default PIX
        
        // Calcular frete se necessário
        $total_produtos = calculateCartTotal($todos_produtos);
        $frete = 0; // Você pode implementar o cálculo de frete aqui
        
        // Adequação: Estrutura de dados do pedido conforme esperado pela GravarPedido
        $dadosPedido = [
            'nome_loja' => getAfiliado() ?: '001',        
            'opcao_pagamento' => $opcao_pagamento,       
            'total' => $total_produtos,
            'frete' => $frete,
            'parcelas' => $_POST['parcelas'] ?? 1,       
            'itens' => $itens_pedido,
            
            // Campos específicos para pagamento com cartão (se aplicável)
            'nome_cartao' => $_POST['nome_cartao'] ?? '',
            'numero_cartao' => $_POST['numero_cartao'] ?? '',
            'data_expira' => $_POST['data_expira'] ?? '',
            'codigo_seguranca' => $_POST['codigo_seguranca'] ?? ''
        ];
        
        // Enviar pedido via API
        $resultado = enviarDadosCheckout(API_URL, API_KEY, $dadosCliente, $dadosPedido);
        
        if ($resultado) {
            // Limpar a resposta de possíveis notices PHP
            $cleanResponse = $resultado;
            $jsonStart = strpos($resultado, '{');
            if ($jsonStart !== false && $jsonStart > 0) {
                $cleanResponse = substr($resultado, $jsonStart);
            }
            
            // Decodificar resposta
            $resposta = json_decode($cleanResponse, true);
            
            // Verificar se a resposta tem o formato esperado
            if ($resposta && isset($resposta['status']) && $resposta['status'] === 'success') {
                // Limpar carrinho
                $_SESSION['carrinho'] = [];
                
                // Extrair dados da resposta (pode estar aninhado)
                $dados_retorno = $resposta['data'];
                if (isset($dados_retorno['data'])) {
                    $dados_retorno = $dados_retorno['data'];
                }
                
                // Salvar dados na sessão para exibir na próxima página
                $_SESSION['ultimo_pedido'] = [
                    'forma_pagamento' => $forma_pagamento,
                    'opcao_pagamento' => $opcao_pagamento,
                    'dados_resposta' => $dados_retorno
                ];
                
                // Obter código do pedido
                $pedido_codigo = $dados_retorno['codigo_pedido'] ?? 'N/A';
                
                // Usar JavaScript para redirecionar (evita problemas de header)
                echo "<script>window.location.href = '?page=pedido&codigo={$pedido_codigo}&tipo={$forma_pagamento}';</script>";
                exit;
                
            } else {
                // Erro na resposta da API
                $erro_checkout = $resposta['message'] ?? "Erro ao processar pedido. Resposta inválida da API.";
                
                // Log do erro para debug
                logActivity('checkout_error', [
                    'erro' => $erro_checkout,
                    'resposta_completa' => $resposta,
                    'resposta_original' => $resultado
                ]);
            }
        } else {
            $erro_checkout = "Erro de comunicação com o servidor. Tente novamente.";
        }
        
    } catch (Exception $e) {
        $erro_checkout = "Erro interno: " . $e->getMessage();
        logActivity('checkout_exception', ['erro' => $e->getMessage()]);
    }
}

// Buscar dados dos produtos no carrinho
$produtos_response = listarProdutos();
$todos_produtos = $produtos_response['status'] === 'success' ? $produtos_response['data']['data'] : [];
$total_carrinho = calculateCartTotal($todos_produtos);
?>

<div class="container">
    <div class="breadcrumb-nav py-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="?page=home">Início</a></li>
                <li class="breadcrumb-item"><a href="?page=carrinho">Carrinho</a></li>
                <li class="breadcrumb-item active">Checkout</li>
            </ol>
        </nav>
    </div>

    <div class="page-header mb-4">
        <h1>Finalizar Compra</h1>
    </div>

    <?php if ($erro_checkout): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?php echo htmlspecialchars($erro_checkout); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($success_checkout): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            <?php echo htmlspecialchars($success_checkout); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="POST" id="checkoutForm">
        <input type="hidden" name="action" value="finalizar_pedido">
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Dados Pessoais -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-user me-2"></i>Dados Pessoais</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nome Completo *</label>
                                <input type="text" name="nome" class="form-control" required 
                                       value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">E-mail *</label>
                                <input type="email" name="email" class="form-control" required
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Celular *</label>
                                <input type="tel" name="telefone" class="form-control" required 
                                       placeholder="(11) 99999-9999"
                                       value="<?php echo htmlspecialchars($_POST['telefone'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">CPF *</label>
                                <input type="text" name="cpf" class="form-control" required 
                                       placeholder="000.000.000-00"
                                       value="<?php echo htmlspecialchars($_POST['cpf'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Endereço de Entrega -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-map-marker-alt me-2"></i>Endereço de Entrega</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">CEP *</label>
                                <input type="text" name="cep" id="cep" class="form-control" required 
                                       placeholder="00000-000"
                                       value="<?php echo htmlspecialchars($_POST['cep'] ?? ''); ?>">
                                <button type="button" class="btn btn-sm btn-outline-primary mt-1" onclick="buscarCep()">
                                    Buscar CEP
                                </button>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Endereço *</label>
                                <input type="text" name="endereco" id="endereco" class="form-control" required
                                       value="<?php echo htmlspecialchars($_POST['endereco'] ?? ''); ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Número *</label>
                                <input type="text" name="numero" class="form-control" required
                                       value="<?php echo htmlspecialchars($_POST['numero'] ?? ''); ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Complemento</label>
                                <input type="text" name="complemento" class="form-control"
                                       value="<?php echo htmlspecialchars($_POST['complemento'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bairro *</label>
                                <input type="text" name="bairro" id="bairro" class="form-control" required
                                       value="<?php echo htmlspecialchars($_POST['bairro'] ?? ''); ?>">
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Cidade *</label>
                                <input type="text" name="cidade" id="cidade" class="form-control" required
                                       value="<?php echo htmlspecialchars($_POST['cidade'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Estado *</label>
                                <select name="estado" id="estado" class="form-select" required>
                                    <option value="">Selecione</option>
                                    <?php
                                    $estados = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];
                                    $estado_selecionado = $_POST['estado'] ?? '';
                                    foreach($estados as $uf) {
                                        $selected = ($estado_selecionado === $uf) ? 'selected' : '';
                                        echo "<option value=\"$uf\" $selected>$uf</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div id="freteCalculado" class="alert alert-info" style="display: none;"></div>
                    </div>
                </div>

                <!-- Forma de Pagamento -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-credit-card me-2"></i>Forma de Pagamento</h5>
                    </div>
                    <div class="card-body">
                        <div class="payment-options">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="forma_pagamento" value="pix" id="pix" required>
                                <label class="form-check-label" for="pix">
                                    <i class="fas fa-qrcode me-2"></i>PIX - (Confirmação Imediata)
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="forma_pagamento" value="boleto" id="boleto">
                                <label class="form-check-label" for="boleto">
                                    <i class="fas fa-barcode me-2"></i>Boleto Bancário
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="forma_pagamento" value="cartao" id="cartao">
                                <label class="form-check-label" for="cartao">
                                    <i class="fas fa-credit-card me-2"></i>Cartão de Crédito
                                </label>
                            </div>
                        </div>
                        
                        <!-- Campos específicos para cartão -->
                        <div id="dadosCartao" style="display: none;">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Nome no Cartão</label>
                                    <input type="text" name="nome_cartao" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Número do Cartão</label>
                                    <input type="text" name="numero_cartao" class="form-control" 
                                           placeholder="0000 0000 0000 0000">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Vencimento</label>
                                    <input type="text" name="data_expira" class="form-control" 
                                           placeholder="MM/AAAA">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">CVV</label>
                                    <input type="text" name="codigo_seguranca" class="form-control" 
                                           placeholder="000">
                                </div>
                            </div>
                        </div>
                        
                        <div id="parcelamento" style="display: none;">
                            <label class="form-label">Parcelamento</label>
                            <select name="parcelas" class="form-select" id="parcelasSelect">
                                <option value="1">1x sem juros</option>
                                <option value="2">2x sem juros</option>
                                <option value="3">3x sem juros</option>
                                <option value="4">4x sem juros</option>
                                <option value="5">5x sem juros</option>
                                <option value="6">6x sem juros</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Observações -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-comment me-2"></i>Observações</h5>
                    </div>
                    <div class="card-body">
                        <textarea name="observacoes" class="form-control" rows="3" 
                                  placeholder="Observações adicionais sobre o pedido"><?php echo htmlspecialchars($_POST['observacoes'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 checkout-summary-sticky">
                <!-- Resumo do Pedido -->
                <div class="card">
                    <div class="card-header">
                        <h5>Resumo do Pedido</h5>
                    </div>
                    <div class="card-body">
                        <div class="order-items mb-3">
                            <?php 
                            foreach ($_SESSION['carrinho'] as $produto_id => $quantidade):
                                foreach ($todos_produtos as $produto):
                                    if ($produto['id'] == $produto_id):
                            ?>
                                <div class="order-item d-flex justify-content-between mb-2">
                                    <div>
                                        <span><?php echo htmlspecialchars($produto['nome']); ?></span>
                                        <small class="text-muted"> x<?php echo $quantidade; ?></small>
                                    </div>
                                    <span><?php echo formatPrice($produto['preco_lojavirtual'] * $quantidade); ?></span>
                                </div>
                            <?php 
                                    break;
                                    endif;
                                endforeach;
                            endforeach; 
                            ?>
                        </div>
                        
                        <hr>
                        
                        <div class="order-summary">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span id="subtotal"><?php echo formatPrice($total_carrinho); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Frete:</span>
                                <span id="frete">A calcular</span>
                            </div>
                            <!--
                            <div class="d-flex justify-content-between mb-2" id="descontoDiv" style="display: none;">
                                <span>Desconto PIX:</span>
                                <span id="desconto" class="text-success">-R$ 0,00</span>
                            </div>
                            -->
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total:</strong>
                                <strong id="total"><?php echo formatPrice($total_carrinho); ?></strong>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100 btn-lg" id="btnFinalizar">
                            <i class="fas fa-lock me-2"></i>Finalizar Compra
                        </button>
                        
                        <div class="security-info mt-3 text-center">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1"></i>
                                Compra 100% segura e protegida
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Mostrar campos do cartão quando selecionado
document.getElementById('cartao').addEventListener('change', function() {
    document.getElementById('dadosCartao').style.display = this.checked ? 'block' : 'none';
    document.getElementById('parcelamento').style.display = this.checked ? 'block' : 'none';
});

// Esconder campos do cartão quando outras opções são selecionadas
document.getElementById('pix').addEventListener('change', function() {
    if (this.checked) {
        document.getElementById('dadosCartao').style.display = 'none';
        document.getElementById('parcelamento').style.display = 'none';
    }
});

document.getElementById('boleto').addEventListener('change', function() {
    if (this.checked) {
        document.getElementById('dadosCartao').style.display = 'none';
        document.getElementById('parcelamento').style.display = 'none';
    }
});

// Prevenir múltiplos cliques no botão de finalizar
document.getElementById('checkoutForm').addEventListener('submit', function() {
    const btnFinalizar = document.getElementById('btnFinalizar');
    btnFinalizar.disabled = true;
    btnFinalizar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processando...';
});

// Função para buscar CEP (integração com ViaCEP)
function buscarCep() {
    const cep = document.getElementById('cep').value.replace(/\D/g, '');
    
    if (cep.length === 8) {
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(response => response.json())
            .then(data => {
                if (!data.erro) {
                    document.getElementById('endereco').value = data.logradouro;
                    document.getElementById('bairro').value = data.bairro;
                    document.getElementById('cidade').value = data.localidade;
                    document.getElementById('estado').value = data.uf;
                } else {
                    alert('CEP não encontrado!');
                }
            })
            .catch(error => {
                console.error('Erro ao buscar CEP:', error);
                alert('Erro ao buscar CEP. Tente novamente.');
            });
    } else {
        alert('CEP deve ter 8 dígitos!');
    }
}
</script>