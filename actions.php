<?php
require_once 'db.php';
session_start();

// Função genérica para redirecionar
function redirect($page, $message = null, $type = 'success') {
    $url = $page;
    if ($message) {
        $url .= (strpos($page, '?') ? '&' : '?') . 'msg=' . urlencode($message) . '&type=' . urlencode($type);
    }
    header("Location: $url");
    exit;
}

// Função para salvar um novo produto
function saveProduct($data, $pdo) {
    if (empty($data['name']) || !is_numeric($data['price']) || !is_numeric($data['stock'])) {
        return ['error' => 'Dados inválidos. Nome, Preço e Estoque são obrigatórios.'];
    }
    $sql = 'INSERT INTO products (name, category, price, stock, is_active) VALUES (?, ?, ?, ?, ?)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $data['name'],
        $data['category'],
        $data['price'],
        $data['stock'],
        isset($data['is_active']) ? 1 : 0
    ]);
    return ['success' => 'Produto cadastrado com sucesso!'];
}

// Função para listar todos os produtos
function listProducts($pdo) {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY name");
    return $stmt->fetchAll();
}

// Função para salvar um novo cliente
function saveClient($data, $pdo) {
    if (empty($data['name'])) {
        return ['error' => 'O nome do cliente é obrigatório.'];
    }
    $sql = 'INSERT INTO clients (name, phone) VALUES (?, ?)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$data['name'], $data['phone']]);
    return ['success' => 'Cliente cadastrado com sucesso!'];
}

// Função para listar todos os clientes
function listClients($pdo) {
    $stmt = $pdo->query("SELECT id, name FROM clients ORDER BY name");
    return $stmt->fetchAll();
}

// Função para obter produtos disponíveis para venda
function getAvailableProducts($pdo) {
    $stmt = $pdo->query("SELECT id, name, price, stock FROM products WHERE is_active = TRUE AND stock > 0 ORDER BY name");
    return $stmt->fetchAll();
}

// Função para informar um nome de um cliente
function getClientNameById($clientId, $pdo) {
    if (!$clientId) return 'Cliente Anônimo';
    $stmt = $pdo->prepare("SELECT name FROM clients WHERE id = ?");
    $stmt->execute([$clientId]);
    $client = $stmt->fetch();
    return $client ? $client['name'] : 'Cliente Anônimo';
}

// Função para finalizar uma venda
function finalizeSale($cart, $clientId, $pdo) {
    if (empty($cart)) return ['error' => 'Carrinho vazio.'];

    $totalAmount = 0;
    $detailsString = [];

    // Calcula o total e verifica estoque
    foreach ($cart as $item) {
        $subtotal = $item['price'] * $item['quantity'];
        $totalAmount += $subtotal;
        $detailsString[] = "{$item['name']} ({$item['quantity']}x R$ ".number_format($item['price'], 2, ',', '.').")";
    }

    $clientName = getClientNameById($clientId, $pdo);

    $pdo->beginTransaction();
    try {
        // Salva a Venda
        $stmt = $pdo->prepare("INSERT INTO sales (client_name, total_amount, sale_details) VALUES (?, ?, ?)");
        $stmt->execute([
            $clientName,
            $totalAmount,
            implode('; ', $detailsString)
        ]);

        // Atualiza o Estoque
        foreach ($cart as $item) {
            $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $stmt->execute([$item['quantity'], $item['id']]);
        }

        $pdo->commit();
        return ['success' => "Venda para {$clientName} finalizada! Total: R$ " . number_format($totalAmount, 2, ',', '.') . "."];

    } catch (Exception $e) {
        $pdo->rollBack();
        return ['error' => "Erro ao finalizar a venda: " . $e->getMessage()];
    }
}

// Função para gerar relatório das vendas
function generateReport($pdo) {
    $stmt = $pdo->query("SELECT id, client_name, total_amount, sale_details, sale_date FROM sales ORDER BY sale_date DESC");
    $sales = $stmt->fetchAll();

    $output = "--- RELATÓRIO DE VENDAS (PDV SIMPLES) ---\n";
    $output .= "GERADO EM: " . date('Y-m-d H:i:s') . "\n\n";

    if (empty($sales)) {
        $output .= "Nenhuma venda registrada.\n";
    } else {
        foreach ($sales as $sale) {
            $output .= "VENDA #{$sale['id']} | DATA: {$sale['sale_date']} \n";
            $output .= "CLIENTE: {$sale['client_name']} \n";
            $output .= "VALOR TOTAL: R$ " . number_format($sale['total_amount'], 2, ',', '.') . "\n";
            $output .= "DETALHES: {$sale['sale_details']} \n";
            $output .= "---------------------------------------\n";
        }
    }

    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="relatorio_vendas_' . date('Ymd_His') . '.txt"');
    echo $output;
    exit;
}


// Tratamento de Requisições POST e GET
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $result = [];
    $redirect_page = 'index.php';

    if ($action === 'save_product') {
        $result = saveProduct($_POST, $pdo);
        $redirect_page = 'cadastro_produtos.php';
    } elseif ($action === 'save_client') {
        $result = saveClient($_POST, $pdo);
        $redirect_page = 'cadastro_clientes.php';
    } elseif ($action === 'finalize_sale') {
        $clientId = $_POST['client_id'] ?? 0;
        $cart = json_decode($_POST['cart_data'] ?? '[]', true); // Recebe o carrinho JSON do JS
        
        $result = finalizeSale($cart, $clientId, $pdo);
        // O JS limpará o Local Storage após o sucesso, mas precisamos limpar a mensagem
        $redirect_page = 'index.php';
    }

    if (!empty($result)) {
        $message = $result['success'] ?? $result['error'];
        $type = isset($result['success']) ? 'success' : 'danger';
        redirect($redirect_page, $message, $type);
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'report') {
    generateReport($pdo);
}