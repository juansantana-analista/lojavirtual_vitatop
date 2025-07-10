<?php
// pages/produtos.php
$produtos_response = listarProdutos();
$produtos = $produtos_response['status'] === 'success' ? $produtos_response['data']['data'] : [];
?>

<div class="container">
    <div class="breadcrumb-nav py-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="?page=home">Início</a></li>
                <li class="breadcrumb-item active">Produtos</li>
            </ol>
        </nav>
    </div>

    <div class="page-header mb-4">
        <h1>Todos os Produtos</h1>
        <p class="text-muted"><?php echo count($produtos); ?> produtos encontrados</p>
    </div>

    <div class="filters-section mb-4">
        <div class="row">
            <div class="col-md-3">
                <select class="form-select" id="sortBy">
                    <option value="">Ordenar por</option>
                    <option value="price_asc">Menor preço</option>
                    <option value="price_desc">Maior preço</option>
                    <option value="name_asc">A-Z</option>
                    <option value="name_desc">Z-A</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="filterCategory">
                    <option value="">Todas as categorias</option>
                    <?php
                    $categorias = array_unique(array_column($produtos, 'categoria_id'));
                    foreach ($categorias as $categoria):
                    ?>
                        <option value="<?php echo $categoria; ?>">Categoria <?php echo $categoria; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <div class="produtos-grid">
        <div class="row" id="produtosContainer">
            <?php foreach ($produtos as $produto): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4 produto-item" 
                     data-categoria="<?php echo $produto['categoria_id']; ?>"
                     data-price="<?php echo $produto['preco_lojavirtual']; ?>"
                     data-name="<?php echo strtolower($produto['nome']); ?>">
                    <div class="product-card h-100">
                        <div class="product-image">
                            <a href="?page=produto&id=<?php echo $produto['id']; ?>">
                                <img src="https://vitatop.tecskill.com.br/<?php echo $produto['foto']; ?>" 
                                     alt="<?php echo htmlspecialchars($produto['nome']); ?>" 
                                     class="img-fluid">
                            </a>
                            <button class="btn-favorite">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                        <div class="product-info">
                            <h6 class="product-name">
                                <a href="?page=produto&id=<?php echo $produto['id']; ?>" class="text-decoration-none">
                                    <?php echo htmlspecialchars($produto['titulo']); ?>
                                </a>
                            </h6>
                            <div class="product-price">
                                <?php if ($produto['preco'] < $produto['preco_lojavirtual']): ?>
                                    <span class="old-price">De <?php echo formatPrice($produto['preco2']); ?></span>
                                <?php endif; ?>
                                <span class="current-price"><?php echo formatPrice($produto['preco_lojavirtual']); ?></span>
                            </div>
                            <button class="btn btn-primary btn-add-cart w-100 mt-2" 
                                    onclick="addToCart(<?php echo $produto['id']; ?>)">
                                <i class="fas fa-shopping-bag me-2"></i>Adicionar
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>