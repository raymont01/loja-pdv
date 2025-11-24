<?php
require_once 'actions.php';
$availableProducts = getAvailableProducts($pdo);
$clients = listClients($pdo);
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PDV Simples — Tela de Vendas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container-fluid">
        <h1>🛒 Ponto de Venda (PDV)</h1>
        <p class="lead">Selecione os produtos e finalize a venda.</p>

        <?php include 'messages.php'; ?>

        <div class="row">
            <div class="col-md-7">
                <h2 class="mb-3">Produtos Disponíveis</h2>
                <div class="row row-cols-1 row-cols-md-3 g-3">
                    <?php if (empty($availableProducts)): ?>
                        <div class="col-12"><div class="alert alert-warning">Nenhum produto ativo ou em estoque.</div></div>
                    <?php else: ?>
                        <?php foreach ($availableProducts as $p): ?>
                        <div class="col">
                            <div class="card product-card h-100 shadow-sm"
                                data-id="<?php echo $p['id']; ?>"
                                data-name="<?php echo htmlspecialchars($p['name']); ?>"
                                data-price="<?php echo $p['price']; ?>"
                                data-stock="<?php echo $p['stock']; ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($p['name']); ?></h5>
                                    <p class="card-text mb-1">R$ <strong><?php echo number_format($p['price'], 2, ',', '.'); ?></strong></p>
                                    <p class="card-text small text-muted">Estoque: <?php echo $p['stock']; ?></p>
                                    <button type="button" class="btn btn-sm btn-success disabled w-100">Adicionar (Clique)</button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card p-3 sticky-top" style="top: 70px;">
                    <h2 class="mb-3">Carrinho <i class="bi bi-cart"></i></h2>
                    
                    <div id="cartList" class="cart-list mb-3">
                        </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded">
                        <h5>Total:</h5>
                        <h4 class="text-primary">R$ <span id="cartTotal">0.00</span></h4>
                    </div>

                    <form id="finalizeSaleForm" method="POST" action="actions.php" class="mt-2">
                        <input type="hidden" name="action" value="finalize_sale">
                        <input type="hidden" name="cart_data" id="cartDataInput"> <div class="mb-3">
                            <label for="client_id" class="form-label">Cliente da Venda:</label>
                            <select id="client_id" name="client_id" class="form-select">
                                <option value="0">Cliente Anônimo</option>
                                <?php foreach ($clients as $c): ?>
                                    <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" id="finalizeSaleBtn" class="btn btn-success btn-lg" disabled>
                                <i class="bi bi-cash-stack"></i> Finalizar Venda
                            </button>
                            <button type="button" id="clearCartBtn" class="btn btn-outline-danger" disabled>
                                <i class="bi bi-trash"></i> Limpar Carrinho
                            </button>
                        </div>
                    </form>
                    
                    <a href="actions.php?action=report" class="btn btn-secondary mt-3">
                        <i class="bi bi-file-earmark-text"></i> Baixar Relatório TXT
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>