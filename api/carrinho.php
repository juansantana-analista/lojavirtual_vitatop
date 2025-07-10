<?php
// api/carrinho.php
session_start();
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? $_GET['action'] ?? '';

switch($action) {
    case 'add':
        $produto_id = $input['produto_id'];
        $quantidade = $input['quantidade'] ?? 1;
        
        if (!isset($_SESSION['carrinho'][$produto_id])) {
            $_SESSION['carrinho'][$produto_id] = 0;
        }
        $_SESSION['carrinho'][$produto_id] += $quantidade;
        
        echo json_encode(['status' => 'success', 'message' => 'Produto adicionado']);
        break;
        
    case 'remove':
        $produto_id = $input['produto_id'];
        unset($_SESSION['carrinho'][$produto_id]);
        
        echo json_encode(['status' => 'success', 'message' => 'Produto removido']);
        break;
        
    case 'update':
        $produto_id = $input['produto_id'];
        $quantidade = $input['quantidade'];
        
        if ($quantidade > 0) {
            $_SESSION['carrinho'][$produto_id] = $quantidade;
        } else {
            unset($_SESSION['carrinho'][$produto_id]);
        }
        
        echo json_encode(['status' => 'success', 'message' => 'Carrinho atualizado']);
        break;
        
    case 'clear':
        $_SESSION['carrinho'] = [];
        echo json_encode(['status' => 'success', 'message' => 'Carrinho limpo']);
        break;
        
    case 'count':
        $count = array_sum($_SESSION['carrinho'] ?? []);
        echo json_encode(['count' => $count]);
        break;
        
    default:
        echo json_encode(['status' => 'error', 'message' => 'Ação inválida']);
}
