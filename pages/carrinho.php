<?php
// pages/carrinho.php - VERSÃO SIMPLIFICADA E CORRETA

// Processar ações do carrinho APENAS se não houver output anterior
if (!headers_sent()) {
    if ($_POST['action'] ?? '' === 'update_cart') {
        foreach ($_POST['quantidade'] as $produto_id => $quantidade) {
            if ($quantidade > 0) {
                $_SESSION['carrinho'][$produto_id] = $quantidade;
            } else {
                unset($_SESSION['carrinho'][$produto_id]);
            }
        }
        header('Location: ?page=carrinho');
        exit;
    }
}

// Buscar dados dos produtos no carrinho
$produtos_response = listarProdutos();
$todos_produtos = $produtos_response['status'] === 'success' ? $produtos_response['data']['data'] : [];
$produtos_carrinho = [];

foreach ($_SESSION['carrinho'] as $produto_id => $quantidade) {
    foreach ($todos_produtos as $produto) {
        if ($produto['id'] == $produto_id) {
            $produto['quantidade'] = $quantidade;
            $produtos_carrinho[] = $produto;
            break;
        }
    }
}

$total_carrinho = calculateCartTotal($todos_produtos);
?>

<div class="container">
    <div class="breadcrumb-nav py-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="?page=home">Início</a></li>
                <li class="breadcrumb-item active">Carrinho</li>
            </ol>
        </nav>
    </div>

    <div class="page-header mb-4">
        <h1>Meu Carrinho</h1>
        <p class="text-muted"><span id="cart-count"><?php echo count($produtos_carrinho); ?></span> item(ns) no carrinho</p>
    </div>

    <?php if (empty($produtos_carrinho)): ?>
        <div class="empty-cart text-center py-5">
            <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
            <h3>Seu carrinho está vazio</h3>
            <p class="text-muted mb-4">Adicione alguns produtos para continuar</p>
            <a href="?page=produtos" class="btn btn-primary">Ver Produtos</a>
        </div>
    <?php else: ?>
        <form method="POST" id="updateCartForm">
            <input type="hidden" name="action" value="update_cart">
            
            <div class="cart-items" id="cartItems">
                <?php foreach ($produtos_carrinho as $produto): ?>
                    <div class="cart-item mb-3" id="item-<?php echo $produto['id']; ?>" data-produto-id="<?php echo $produto['id']; ?>" data-preco="<?php echo $produto['preco_lojavirtual']; ?>">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <img src="https://vitatop.tecskill.com.br/<?php echo $produto['foto']; ?>" 
                                             alt="<?php echo htmlspecialchars($produto['nome']); ?>" 
                                             class="img-fluid">
                                    </div>
                                    <div class="col-md-4">
                                        <h6><?php echo htmlspecialchars($produto['titulo']); ?></h6>
                                        <p class="text-muted small"><?php echo htmlspecialchars($produto['nome']); ?></p>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="quantity-controls">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                    onclick="decreaseQuantity(<?php echo $produto['id']; ?>)">-</button>
                                            <input type="number" 
                                                   name="quantidade[<?php echo $produto['id']; ?>]" 
                                                   value="<?php echo $produto['quantidade']; ?>" 
                                                   min="0" 
                                                   class="form-control quantity-input d-inline-block mx-2" 
                                                   style="width: 60px;"
                                                   data-produto-id="<?php echo $produto['id']; ?>">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                    onclick="increaseQuantity(<?php echo $produto['id']; ?>)">+</button>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="removeFromCart(<?php echo $produto['id']; ?>)"
                                                    title="Remover item">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <strong><?php echo formatPrice($produto['preco_lojavirtual']); ?></strong>
                                    </div>
                                    <div class="col-md-3 text-center">
                                        <strong class="item-total"><?php echo formatPrice($produto['preco_lojavirtual'] * $produto['quantidade']); ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="row">
              <div class="col-md-8 mx-auto">
                <?php if ($total_carrinho < 300): ?>
                <?php
                    if (function_exists('getProdutosSugeridos')) {
                        $produtos_sugeridos = getProdutosSugeridos($total_carrinho, 300, 4);
                    } else {
                        $produtos_carrinho_ids = array_keys($_SESSION['carrinho'] ?? []);
                        $produtos_disponiveis = array_filter($todos_produtos, function($p) use ($produtos_carrinho_ids) {
                            return !in_array($p['id'], $produtos_carrinho_ids);
                        });
                        shuffle($produtos_disponiveis);
                        $produtos_sugeridos = array_slice($produtos_disponiveis, 0, 4);
                    }
                ?>
                <div class="frete-gratis-sugestao-box compact">
                  <div class="frete-gratis-msg">
                    <i class="fas fa-truck"></i>
                    <span>
                      Faltam <strong>R$ <?php echo number_format(300 - $total_carrinho, 2, ',', '.'); ?></strong> para você ganhar <b>frete grátis</b>! Aproveite:
                    </span>
                  </div>
                  <div class="produtos-sugeridos-lista justify-content-center">
                    <?php foreach ($produtos_sugeridos as $produto): ?>
                      <div class="produto-sugestao-card mini">
                        <img src="https://vitatop.tecskill.com.br/<?php echo $produto['foto']; ?>" alt="<?php echo htmlspecialchars($produto['titulo']); ?>" class="produto-img">
                        <?php if ($produto['preco_lojavirtual'] >= (300 - $total_carrinho)): ?>
                          <span class="badge-completa">Completa o frete grátis!</span>
                        <?php endif; ?>
                        <div class="produto-nome"><?php echo htmlspecialchars($produto['titulo']); ?></div>
                        <div class="produto-preco">R$ <?php echo number_format($produto['preco_lojavirtual'], 2, ',', '.'); ?></div>
                        <button class="btn-adicionar" onclick="addToCart(<?php echo $produto['id']; ?>, 1)">+</button>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
                <?php endif; ?>
              </div>
            </div>

            <div class="cart-summary mt-4">
                <div class="row">
                    <div class="col-md-8">
                        <button type="submit" class="btn btn-outline-primary me-2">Atualizar Carrinho</button>
                        <a href="?page=produtos" class="btn btn-outline-secondary">Continuar Comprando</a>
                        <button type="button" class="btn btn-outline-danger ms-2" onclick="clearCart()">
                            <i class="fas fa-trash me-2"></i>Limpar Carrinho
                        </button>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5>Resumo do Pedido</h5>
                                <div class="mb-2">
                                    <label for="cep">Calcular Frete:</label>
                                    <div class="input-group">
                                        <input type="text" id="cep" name="cep" class="form-control" placeholder="Digite seu CEP">
                                        <button type="button" class="btn btn-outline-secondary" onclick="calcularFreteCarrinho()">Calcular</button>
                                    </div>
                                    <div id="frete-resultado" class="small text-muted mt-1"></div>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span id="cart-subtotal"><?php echo formatPrice($total_carrinho); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Frete:</span>
                                    <span id="frete-valor" class="text-muted">A calcular</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-3">
                                    <strong>Total:</strong>
                                    <strong id="cart-total"><?php echo formatPrice($total_carrinho); ?></strong>
                                </div>
                                <a href="?page=checkout" class="btn btn-success w-100" id="checkoutBtn">
                                    <i class="fas fa-credit-card me-2"></i>Finalizar Compra
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>