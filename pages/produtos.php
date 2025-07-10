<?php
// pages/produtos.php
$produtos_response = listarProdutos();
$produtos = $produtos_response['status'] === 'success' ? $produtos_response['data']['data'] : [];

// Verificar se há busca via URL
$termo_busca = $_GET['busca'] ?? '';
$categoria_filtro = $_GET['categoria'] ?? '';
$produtos_filtrados = $produtos;

// Aplicar filtros
if (!empty($termo_busca) || !empty($categoria_filtro)) {
    $produtos_filtrados = array_filter($produtos, function($produto) use ($termo_busca, $categoria_filtro) {
        $passa_filtro = true;
        
        // Filtro por termo de busca
        if (!empty($termo_busca)) {
            $termo_lower = strtolower(trim($termo_busca));
            $nome = strtolower($produto['nome']);
            $titulo = strtolower($produto['titulo']);
            $passa_filtro = $passa_filtro && (strpos($nome, $termo_lower) !== false || strpos($titulo, $termo_lower) !== false);
        }
        
        // Filtro por categoria
        if (!empty($categoria_filtro)) {
            $passa_filtro = $passa_filtro && ($produto['categoria_id'] == $categoria_filtro);
        }
        
        return $passa_filtro;
    });
}

// Gerar lista de categorias únicas (id => nome)
$categorias_unicas = [];
foreach ($produtos_filtrados as $produto) {
    if (!empty($produto['categoria_id']) && !empty($produto['categoria_nome'])) {
        $categorias_unicas[$produto['categoria_id']] = $produto['categoria_nome'];
    }
}
?>

<div class="container">
    <div class="breadcrumb-nav py-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="?page=home">Início</a></li>
                <li class="breadcrumb-item active">
                    <?php if (!empty($termo_busca)): ?>
                        Busca: "<?php echo htmlspecialchars($termo_busca); ?>"
                    <?php elseif (!empty($categoria_filtro) && isset($categorias_unicas[$categoria_filtro])): ?>
                        <?php echo htmlspecialchars($categorias_unicas[$categoria_filtro]); ?>
                    <?php else: ?>
                        Produtos
                    <?php endif; ?>
                </li>
            </ol>
        </nav>
    </div>

    <div class="page-header mb-4">
        <h1>
            <?php if (!empty($termo_busca)): ?>
                Resultados para "<?php echo htmlspecialchars($termo_busca); ?>"
            <?php elseif (!empty($categoria_filtro) && isset($categorias_unicas[$categoria_filtro])): ?>
                <?php echo htmlspecialchars($categorias_unicas[$categoria_filtro]); ?>
            <?php else: ?>
                Todos os Produtos
            <?php endif; ?>
        </h1>
        <p class="text-muted"><?php echo count($produtos_filtrados); ?> produtos encontrados</p>
    </div>

    <?php if ((!empty($termo_busca) || !empty($categoria_filtro)) && count($produtos_filtrados) === 0): ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-search mb-2"></i>
            <h5>Nenhum produto encontrado</h5>
            <p>
                <?php if (!empty($termo_busca)): ?>
                    Não encontramos produtos para "<?php echo htmlspecialchars($termo_busca); ?>"
                <?php elseif (!empty($categoria_filtro)): ?>
                    Não encontramos produtos nesta categoria
                <?php endif; ?>
            </p>
            <a href="?page=produtos" class="btn btn-primary">Ver todos os produtos</a>
        </div>
    <?php endif; ?>

    <?php if (count($produtos_filtrados) > 0): ?>
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
                        foreach ($categorias_unicas as $cat_id => $cat_nome):
                        ?>
                            <option value="<?php echo $cat_id; ?>" <?php echo ($categoria_filtro == $cat_id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat_nome); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="produtos-grid">
            <div class="row" id="produtosContainer">
                <?php foreach ($produtos_filtrados as $produto): ?>
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
                                <?php 
                                $precos_desconto = formatPriceWithDiscount($produto['preco_lojavirtual'], $produto['id']);
                                ?>
                                <span class="old-price">De <?php echo $precos_desconto['preco_original']; ?></span>
                                <span class="current-price"><?php echo $precos_desconto['preco_com_desconto']; ?></span>
                            </div>
                            <button class="btn btn-primary btn-add-cart w-100 mt-2" 
                                    data-product-id="<?php echo $produto['id']; ?>">
                                <i class="fas fa-shopping-bag me-2"></i>Adicionar
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>