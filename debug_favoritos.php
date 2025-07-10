<?php
// debug_favoritos.php - Página para debug dos favoritos

require_once 'config/config.php';
require_once 'api/requests.php';

// Buscar produtos
$produtos_response = listarProdutos();
$todos_produtos = $produtos_response['status'] === 'success' ? $produtos_response['data']['data'] : [];

echo "<h1>Debug Favoritos</h1>";
echo "<h2>Status da API:</h2>";
echo "<pre>" . print_r($produtos_response, true) . "</pre>";

echo "<h2>Primeiros 3 produtos:</h2>";
if (!empty($todos_produtos)) {
    for ($i = 0; $i < min(3, count($todos_produtos)); $i++) {
        echo "<h3>Produto " . ($i + 1) . ":</h3>";
        echo "<pre>" . print_r($todos_produtos[$i], true) . "</pre>";
    }
} else {
    echo "<p>Nenhum produto encontrado</p>";
}

echo "<h2>Total de produtos: " . count($todos_produtos) . "</h2>";
?>

<div style="margin: 20px 0; padding: 20px; background: #f5f5f5; border-radius: 5px;">
    <h3>Teste Manual de Favoritos</h3>
    <button onclick="adicionarFavoritoTeste()" style="margin: 5px; padding: 10px;">Adicionar Favorito Teste</button>
    <button onclick="limparFavoritos()" style="margin: 5px; padding: 10px;">Limpar Favoritos</button>
    <button onclick="verFavoritos()" style="margin: 5px; padding: 10px;">Ver Favoritos</button>
    <button onclick="irParaFavoritos()" style="margin: 5px; padding: 10px;">Ir para Página de Favoritos</button>
</div>

<script>
// Verificar localStorage
console.log('=== DEBUG FAVORITOS ===');
console.log('localStorage vitatop_favorites:', localStorage.getItem('vitatop_favorites'));

try {
    const favoritos = JSON.parse(localStorage.getItem('vitatop_favorites') || '[]');
    console.log('Favoritos parseados:', favoritos);
    console.log('Tipo dos favoritos:', typeof favoritos);
    console.log('É array?', Array.isArray(favoritos));
} catch (error) {
    console.error('Erro ao parsear favoritos:', error);
}

// Dados dos produtos
const produtosData = <?php echo json_encode($todos_produtos); ?>;
console.log('Produtos disponíveis:', produtosData);
console.log('Primeiro produto:', produtosData[0]);

if (produtosData.length > 0) {
    console.log('IDs dos primeiros 5 produtos:');
    produtosData.slice(0, 5).forEach((produto, index) => {
        console.log(`Produto ${index + 1}: ID = ${produto.id} (tipo: ${typeof produto.id})`);
    });
}

// Funções de teste
function adicionarFavoritoTeste() {
    if (produtosData.length > 0) {
        const produtoId = produtosData[0].id.toString();
        let favoritos = JSON.parse(localStorage.getItem('vitatop_favorites') || '[]');
        
        if (!favoritos.includes(produtoId)) {
            favoritos.push(produtoId);
            localStorage.setItem('vitatop_favorites', JSON.stringify(favoritos));
            alert(`Favorito adicionado: ${produtoId}`);
        } else {
            alert(`Favorito já existe: ${produtoId}`);
        }
        
        verFavoritos();
    } else {
        alert('Nenhum produto disponível para teste');
    }
}

function limparFavoritos() {
    localStorage.removeItem('vitatop_favorites');
    alert('Favoritos limpos');
    verFavoritos();
}

function verFavoritos() {
    const favoritos = JSON.parse(localStorage.getItem('vitatop_favorites') || '[]');
    console.log('Favoritos atuais:', favoritos);
    alert(`Favoritos: ${JSON.stringify(favoritos)}`);
}

function irParaFavoritos() {
    window.location.href = '?page=favoritos';
}
</script> 