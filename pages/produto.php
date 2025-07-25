<?php
$produto_id = $_GET['id'] ?? null;

if (!$produto_id) {
    header('Location: ?page=produtos');
    exit;
}

// Buscar produto específico
$produto_response = getProdutoPorId($produto_id);
$produto = null;

if ($produto_response['status'] === 'success') {
    $produto = $produto_response['data'];
} else {
    // Fallback: buscar na lista geral
    $produtos_response = listarProdutos();
    if ($produtos_response['status'] === 'success') {
        foreach ($produtos_response['data']['data'] as $p) {
            if ($p['id'] == $produto_id) {
                $produto = $p;
                break;
            }
        }
    }
}

if (!$produto) {
    header('Location: ?page=produtos');
    exit;
}

// Calcular desconto
$desconto_percent = 0;
if ($produto['preco'] < $produto['preco2']) {
    $desconto_percent = round((($produto['preco2'] - $produto['preco_lojavirtual']) / $produto['preco2']) * 100);
}
?>

<div class="container">
    <div class="breadcrumb-nav py-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="?page=home">Início</a></li>
                <li class="breadcrumb-item"><a href="?page=produtos">Produtos</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($produto['nome']); ?></li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="product-image-detail">
                <div class="main-image mb-3">
                    <img src="https://vitatop.tecskill.com.br/<?php echo $produto['foto']; ?>" 
                         alt="<?php echo htmlspecialchars($produto['nome']); ?>" 
                         class="img-fluid rounded">
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="product-details">
                <h1 class="product-title"><?php echo htmlspecialchars($produto['titulo']); ?></h1>
                <p class="product-subtitle text-muted"><?php echo htmlspecialchars($produto['nome']); ?></p>
                
                <div class="price-section mb-4">
                    <?php 
                    $precos_desconto = formatPriceWithDiscount($produto['preco_lojavirtual'], $produto['id']);
                    ?>
                    <div class="old-price">De <?php echo $precos_desconto['preco_original']; ?></div>
                    <div class="current-price"><?php echo $precos_desconto['preco_com_desconto']; ?></div>
                    <div class="discount-info">
                        <span class="badge bg-danger">-<?php echo $precos_desconto['desconto_percentual']; ?>%</span>
                        <span class="economy">Você economiza <?php echo $precos_desconto['valor_desconto']; ?></span>
                    </div>
                </div>
                
                <div class="purchase-options mb-4">
                    <div class="quantity-selector mb-3">
                        <label class="form-label">Quantidade:</label>
                        <div class="quantity-controls">
                            <button type="button" class="btn btn-outline-secondary" onclick="decreaseProductQuantity()">-</button>
                            <input type="number" id="productQuantity" value="1" min="1" max="10" class="form-control quantity-input mx-2">
                            <button type="button" class="btn btn-outline-secondary" onclick="increaseProductQuantity()">+</button>
                        </div>
                    </div>
                    
                    <div class="action-buttons">
                        <button class="btn btn-success btn-lg w-100 mb-2" onclick="addToCartWithQuantity()">
                            <i class="fas fa-shopping-bag me-2"></i>Adicionar ao Carrinho
                        </button>
                        <button class="btn btn-primary btn-lg w-100" onclick="buyNow()">
                            <i class="fas fa-credit-card me-2"></i>Comprar Agora
                        </button>
                    </div>
                </div>
                
                <div class="shipping-calculator">
                    <h6>Calcular Frete</h6>
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" placeholder="Digite seu CEP" id="cepFrete" oninput="applyCepMask(this)">
                        <button class="btn btn-outline-primary" onclick="calcularFreteDetalhes()">Calcular</button>
                    </div>
                    <div id="freteResultado"></div>
                </div>
                
                <div class="product-features mt-4">
                    <h6>Características do Produto</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i>Produto 100% natural</li>
                        <li><i class="fas fa-check text-success me-2"></i>Fabricado no Brasil</li>
                        <li><i class="fas fa-check text-success me-2"></i>Registro na ANVISA</li>
                        <li><i class="fas fa-check text-success me-2"></i>Entrega garantida</li>
                    </ul>
                </div>
                
                <div class="security-badges mt-4">
                    <div class="row text-center">
                        <div class="col-4">
                            <i class="fas fa-shield-alt fa-2x text-success mb-2"></i>
                            <p class="small">Compra Segura</p>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-truck fa-2x text-primary mb-2"></i>
                            <p class="small">Entrega Rápida</p>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-medal fa-2x text-warning mb-2"></i>
                            <p class="small">Qualidade Garantida</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="product-tabs mt-5">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#description">Descrição</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#contraindicacao" id="tabContraIndicacao">Contra indicação</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#reviews">Avaliações</a>
            </li>
        </ul>
        
        <div class="tab-content mt-3">
            <div class="tab-pane fade show active" id="description">
                <div class="product-description">
                    <h5>Sobre o Produto</h5>
                    <p><?php echo htmlspecialchars($produto['nome']); ?> é um suplemento natural de alta qualidade, desenvolvido com ingredientes selecionados para proporcionar os melhores resultados.</p>
                    
                    <h6>Benefícios:</h6>
                    <ul>
                        <li>Auxilia no fortalecimento do sistema imunológico</li>
                        <li>Rico em nutrientes essenciais</li>
                        <li>Fórmula concentrada e de fácil absorção</li>
                        <li>Produto natural sem conservantes artificiais</li>
                    </ul>
                    
                    <h6>Modo de Usar:</h6>
                    <p>Tome 2 cápsulas ao dia, preferencialmente antes das refeições, ou conforme orientação de profissional habilitado.</p>
                </div>
            </div>
            <div class="tab-pane fade" id="contraindicacao">
                <div id="contraIndicacaoContent">
                    <div class="text-center text-muted py-4">Clique para carregar as contraindicações...</div>
                </div>
            </div>
            <div class="tab-pane fade" id="reviews">
                <div class="reviews-section">
                    <h5>Avaliações dos Clientes</h5>
                    <div class="rating-summary mb-4">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <div class="rating-score">
                                    <span class="score">4.8</span>
                                    <div class="stars">
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                    </div>
                                    <p class="text-muted">Baseado em 156 avaliações</p>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="rating-breakdown">
                                    <div class="rating-bar">
                                        <span>5 estrelas</span>
                                        <div class="progress mx-3">
                                            <div class="progress-bar bg-warning" style="width: 85%"></div>
                                        </div>
                                        <span>85%</span>
                                    </div>
                                    <div class="rating-bar">
                                        <span>4 estrelas</span>
                                        <div class="progress mx-3">
                                            <div class="progress-bar bg-warning" style="width: 10%"></div>
                                        </div>
                                        <span>10%</span>
                                    </div>
                                    <div class="rating-bar">
                                        <span>3 estrelas</span>
                                        <div class="progress mx-3">
                                            <div class="progress-bar bg-warning" style="width: 3%"></div>
                                        </div>
                                        <span>3%</span>
                                    </div>
                                    <div class="rating-bar">
                                        <span>2 estrelas</span>
                                        <div class="progress mx-3">
                                            <div class="progress-bar bg-warning" style="width: 1%"></div>
                                        </div>
                                        <span>1%</span>
                                    </div>
                                    <div class="rating-bar">
                                        <span>1 estrela</span>
                                        <div class="progress mx-3">
                                            <div class="progress-bar bg-warning" style="width: 1%"></div>
                                        </div>
                                        <span>1%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="reviews-list">
                        <div class="review-item border-bottom pb-3 mb-3">
                            <div class="review-header d-flex justify-content-between">
                                <div>
                                    <strong>Maria S.</strong>
                                    <div class="stars">
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                    </div>
                                </div>
                                <small class="text-muted">Há 2 dias</small>
                            </div>
                            <p class="review-text mt-2">Produto excelente! Notei melhora significativa na minha disposição. Recomendo!</p>
                        </div>
                        
                        <div class="review-item border-bottom pb-3 mb-3">
                            <div class="review-header d-flex justify-content-between">
                                <div>
                                    <strong>João P.</strong>
                                    <div class="stars">
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="far fa-star text-warning"></i>
                                    </div>
                                </div>
                                <small class="text-muted">Há 1 semana</small>
                            </div>
                            <p class="review-text mt-2">Ótima qualidade e entrega rápida. Já é minha segunda compra.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function applyCepMask(input) {
    input.value = input.value.replace(/\D/g, '').replace(/(\d{5})(\d{1,3})/, '$1-$2');
}

function increaseProductQuantity() {
    const input = document.getElementById('productQuantity');
    const currentValue = parseInt(input.value);
    if (currentValue < 10) {
        input.value = currentValue + 1;
    }
}

function decreaseProductQuantity() {
    const input = document.getElementById('productQuantity');
    const currentValue = parseInt(input.value);
    if (currentValue > 1) {
        input.value = currentValue - 1;
    }
}

function addToCartWithQuantity() {
    const quantidade = parseInt(document.getElementById('productQuantity').value);
    addToCart(<?php echo $produto['id']; ?>, quantidade);
}

function buyNow() {
    const quantidade = parseInt(document.getElementById('productQuantity').value);
    addToCart(<?php echo $produto['id']; ?>, quantidade);
    setTimeout(() => {
        window.location.href = '?page=carrinho';
    }, 1000);
}

function calcularFreteDetalhes() {
    const cep = document.getElementById('cepFrete').value.replace(/\D/g, '');
    if (cep.length !== 8) {
        alert('CEP deve ter 8 dígitos');
        return;
    }
    
    const quantidade = parseInt(document.getElementById('productQuantity').value);
    const valorUnitario = <?php echo $produto['preco_lojavirtual']; ?>;
    const valor = valorUnitario * quantidade;
    
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
            document.getElementById('freteResultado').innerHTML = `
                <div class="alert alert-danger">
                    Erro ao calcular frete.
                </div>
            `;
            return;
        }
        // Acessar corretamente o valor do frete na estrutura aninhada
        const freteValor = parseFloat(
            data?.data?.data?.frete ?? data?.data?.frete ?? data?.frete ?? data?.valor
        );
        if (data.status === 'success' && !isNaN(freteValor)) {
            const freteDisplay = freteValor === 0 ? 'Grátis' : formatMoney(freteValor);
            document.getElementById('freteResultado').innerHTML = `
                <div class="alert alert-info">
                    <strong>Frete calculado:</strong><br>
                    Valor: ${freteDisplay}<br>
                    Para ${quantidade} unidade(s) - Total: ${formatMoney(valor + freteValor)}
                </div>
            `;
        } else {
            document.getElementById('freteResultado').innerHTML = `
                <div class="alert alert-danger">
                    Erro ao calcular frete.
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Erro ao calcular frete:', error);
        document.getElementById('freteResultado').innerHTML = `
            <div class="alert alert-danger">
                Erro ao calcular frete.
            </div>
        `;
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const tabContra = document.getElementById('tabContraIndicacao');
    if (tabContra) {
        tabContra.addEventListener('shown.bs.tab', function () {
            carregarContraIndicacao();
        });
    }
});

function carregarContraIndicacao() {
    const content = document.getElementById('contraIndicacaoContent');
    content.innerHTML = '<div class="text-center text-muted py-4">Carregando contraindicações...</div>';
    fetch('/lojinha_vitatop/api/requests.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            class: 'ProdutoVariacaoRest',
            method: 'obterProdutoCompleto',
            produto_id: <?php echo (int)$produto['id']; ?>
        })
    })
    .then(async response => {
        const text = await response.text();
        console.log('Texto bruto da resposta contraindicação:', text);
        const jsonStart = text.indexOf('{');
        let data;
        try {
            data = JSON.parse(text.slice(jsonStart));
        } catch (e) {
            content.innerHTML = '<div class="alert alert-danger">Erro ao carregar contraindicações (JSON inválido).</div>';
            return;
        }
        const contra = data && data.data && data.data.data && data.data.data.contra_indicacoes;
        if (Array.isArray(contra) && contra.length > 0) {
            let html = '<ul class="list-group">';
            contra.forEach(item => {
                html += `<li class="list-group-item"><strong>${item.titulo}</strong>${item.descricao ? ': ' + item.descricao : ''}</li>`;
            });
            html += '</ul>';
            content.innerHTML = html;
        } else {
            content.innerHTML = '<div class="alert alert-info">Nenhuma contraindicação cadastrada para este produto.</div>';
        }
    })
    .catch((e) => {
        content.innerHTML = '<div class="alert alert-danger">Erro ao carregar contraindicações (fetch).</div>';
    });
}
</script>