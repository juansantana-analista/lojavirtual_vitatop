// session/carrinho.php
<?php
session_start();

class CarrinhoManager {
    
    public static function adicionarItem($produto_id, $quantidade = 1) {
        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = [];
        }
        
        if (!isset($_SESSION['carrinho'][$produto_id])) {
            $_SESSION['carrinho'][$produto_id] = 0;
        }
        
        $_SESSION['carrinho'][$produto_id] += $quantidade;
        return true;
    }
    
    public static function removerItem($produto_id) {
        if (isset($_SESSION['carrinho'][$produto_id])) {
            unset($_SESSION['carrinho'][$produto_id]);
            return true;
        }
        return false;
    }
    
    public static function atualizarQuantidade($produto_id, $quantidade) {
        if ($quantidade > 0) {
            $_SESSION['carrinho'][$produto_id] = $quantidade;
        } else {
            self::removerItem($produto_id);
        }
        return true;
    }
    
    public static function obterItens() {
        return $_SESSION['carrinho'] ?? [];
    }
    
    public static function contarItens() {
        return array_sum($_SESSION['carrinho'] ?? []);
    }
    
    public static function calcularTotal($produtos) {
        $total = 0;
        foreach (self::obterItens() as $produto_id => $quantidade) {
            foreach ($produtos as $produto) {
                if ($produto['id'] == $produto_id) {
                    $total += $produto['preco_lojavirtual'] * $quantidade;
                    break;
                }
            }
        }
        return $total;
    }
    
    public static function limpar() {
        $_SESSION['carrinho'] = [];
        return true;
    }
}