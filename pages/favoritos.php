<?php
// pages/favoritos.php

// Buscar todos os produtos para filtrar os favoritos
$produtos_response = listarProdutos();
$todos_produtos = $produtos_response['status'] === 'success' ? $produtos_response['data']['data'] : [];

// Debug: verificar se os produtos estão sendo carregados
error_log('Favoritos - Status da resposta: ' . $produtos_response['status']);
error_log('Favoritos - Total de produtos: ' . count($todos_produtos));

// Os favoritos serão carregados via JavaScript do localStorage
?>

<div class="container">
    <div class="breadcrumb-nav py-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="?page=home">Início</a></li>
                <li class="breadcrumb-item active">Meus Favoritos</li>
            </ol>
        </nav>
    </div>

    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1>Meus Favoritos</h1>
                <p class="text-muted">Produtos que você salvou para ver depois</p>
            </div>
            <div>
                <button onclick="limparTodosFavoritos()" class="btn btn-outline-danger" id="btnLimparFavoritos" style="display: none;">
                    <i class="fas fa-trash me-2"></i>Limpar Todos
                </button>
            </div>
        </div>
    </div>

    <!-- Container para os produtos favoritos -->
    <div id="favoritosContainer">
        <div class="text-center py-5">
            <i class="fas fa-heart text-muted mb-3" style="font-size: 3rem;"></i>
            <h5>Carregando seus favoritos...</h5>
            <p class="text-muted">Aguarde um momento</p>
        </div>
    </div>

    <!-- Template para produtos favoritos (será usado pelo JavaScript) -->
    <template id="produtoFavoritoTemplate">
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4 produto-favorito">
            <div class="product-card h-100">
                <div class="product-image">
                    <a href="?page=produto&id={id}">
                        <img src="https://vitatop.tecskill.com.br/{foto}" 
                             alt="{nome}" 
                             class="img-fluid">
                    </a>
                    <button class="btn-favorite" data-product-id="{id}">
                        <i class="fas fa-heart"></i>
                    </button>
                </div>
                <div class="product-info">
                    <h6 class="product-name">
                        <a href="?page=produto&id={id}" class="text-decoration-none">
                            {titulo}
                        </a>
                    </h6>
                    <div class="product-price">
                        <span class="old-price">De {preco_original}</span>
                        <span class="current-price">{preco_com_desconto}</span>
                    </div>
                    <button class="btn btn-primary btn-add-cart w-100 mt-2" 
                            data-product-id="{id}">
                        <i class="fas fa-shopping-bag me-2"></i>Adicionar
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
// Dados dos produtos para uso no JavaScript
const produtosData = <?php echo json_encode($todos_produtos); ?>;

// Função para carregar e exibir favoritos
function carregarFavoritos() {
    const container = document.getElementById('favoritosContainer');
    const btnLimpar = document.getElementById('btnLimparFavoritos');
    
    console.log('=== CARREGANDO FAVORITOS ===');
    console.log('Produtos disponíveis:', produtosData);
    
    try {
        const favoritos = JSON.parse(localStorage.getItem('vitatop_favorites') || '[]');
        console.log('Favoritos encontrados:', favoritos);
        
        // Mostrar/ocultar botão de limpar
        if (btnLimpar) {
            btnLimpar.style.display = favoritos.length > 0 ? 'block' : 'none';
        }
        
        if (favoritos.length === 0) {
            console.log('Nenhum favorito encontrado');
            container.innerHTML = `
                <div class="text-center py-5">
                    <i class="far fa-heart text-muted mb-3" style="font-size: 3rem;"></i>
                    <h5>Nenhum favorito ainda</h5>
                    <p class="text-muted">Adicione produtos aos seus favoritos clicando no coração</p>
                    <a href="?page=produtos" class="btn btn-primary">
                        <i class="fas fa-th-large me-2"></i>Ver todos os produtos
                    </a>
                </div>
            `;
            return;
        }
        
        // Teste simples: mostrar apenas os IDs dos favoritos
        let html = '<div class="row">';
        html += '<div class="col-12">';
        html += '<h3>IDs dos Favoritos:</h3>';
        html += '<ul>';
        favoritos.forEach(id => {
            html += `<li>ID: ${id}</li>`;
        });
        html += '</ul>';
        html += '</div>';
        html += '</div>';
        
        // Agora tentar filtrar produtos
        const produtosFavoritos = produtosData.filter(produto => {
            const produtoId = produto.id ? produto.id.toString() : '';
            const isFavorite = favoritos.includes(produtoId);
            console.log(`Produto ID: ${produtoId}, Favorito: ${isFavorite}`);
            return isFavorite;
        });
        
        console.log('Produtos favoritos filtrados:', produtosFavoritos);
        
        if (produtosFavoritos.length === 0) {
            html += '<div class="col-12 mt-4">';
            html += '<h4>Nenhum produto encontrado nos dados</h4>';
            html += '<p>Isso pode indicar um problema na estrutura dos dados ou na comparação de IDs.</p>';
            html += '</div>';
        } else {
            html += '<div class="col-12 mt-4">';
            html += '<h4>Produtos Encontrados:</h4>';
            produtosFavoritos.forEach(produto => {
                html += `<div class="card mb-3">
                    <div class="card-body">
                        <h5>ID: ${produto.id}</h5>
                        <p>Nome: ${produto.nome || produto.titulo || 'N/A'}</p>
                        <p>Preço: ${produto.preco_lojavirtual || 'N/A'}</p>
                    </div>
                </div>`;
            });
            html += '</div>';
        }
        
        container.innerHTML = html;
        
        console.log('Favoritos carregados com sucesso');
        
    } catch (error) {
        console.error('Erro ao carregar favoritos:', error);
        container.innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-exclamation-triangle text-danger mb-3" style="font-size: 3rem;"></i>
                <h5>Erro ao carregar favoritos</h5>
                <p class="text-muted">Erro: ${error.message}</p>
                <button onclick="location.reload()" class="btn btn-primary">
                    <i class="fas fa-redo me-2"></i>Recarregar
                </button>
            </div>
        `;
    }
}

function limparFavoritosInvalidos() {
    try {
        const favoritos = JSON.parse(localStorage.getItem('vitatop_favorites') || '[]');
        const produtosIds = produtosData.map(p => p.id.toString());
        const favoritosValidos = favoritos.filter(id => produtosIds.includes(id));
        
        localStorage.setItem('vitatop_favorites', JSON.stringify(favoritosValidos));
        showToast('Favoritos inválidos removidos', 'success');
        carregarFavoritos();
    } catch (error) {
        console.error('Erro ao limpar favoritos inválidos:', error);
        showToast('Erro ao limpar favoritos', 'error');
    }
}

function limparTodosFavoritos() {
    if (confirm('Tem certeza que deseja remover todos os favoritos?')) {
        try {
            localStorage.removeItem('vitatop_favorites');
            showToast('Todos os favoritos foram removidos', 'success');
            carregarFavoritos();
        } catch (error) {
            console.error('Erro ao limpar favoritos:', error);
            showToast('Erro ao limpar favoritos', 'error');
        }
    }
}

// Carregar favoritos quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    carregarFavoritos();
});
</script> 