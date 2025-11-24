<?php
require_once 'actions.php';
$products = listProducts($pdo);
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastro de Produtos - PDV Simples</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <h1>📦 Cadastro de Produtos</h1>
        <p class="lead">Cadastre os produtos que serão vendidos no PDV.</p>
        
        <?php include 'messages.php'; ?>

        <div class="row">
            <div class="col-md-4">
                <div class="card p-3 shadow-sm">
                    <h5 class="card-title">Novo Produto</h5>
                    <form method="POST" action="actions.php">
                        <input type="hidden" name="action" value="save_product">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome do Produto</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Categoria</label>
                            <select class="form-select" id="category" name="category">
                                <option value="Geral">Geral</option>
                                <option value="Eletronicos">Eletrônicos</option>
                                <option value="Vestuario">Vestuário</option>
                                <option value="Alimentos">Alimentos</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label for="price" class="form-label">Preço (R$)</label>
                                <input type="number" step="0.01" min="0.01" class="form-control" id="price" name="price" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="stock" class="form-label">Estoque Inicial</label>
                                <input type="number" min="0" class="form-control" id="stock" name="stock" required>
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">Ativo para Venda</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Salvar Produto</button>
                    </form>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card p-3 shadow-sm">
                    <h5 class="card-title">Produtos Cadastrados</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Categoria</th>
                                    <th>Preço</th>
                                    <th>Estoque</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $p): ?>
                                <tr>
                                    <td><?php echo $p['id']; ?></td>
                                    <td><?php echo htmlspecialchars($p['name']); ?></td>
                                    <td><?php echo htmlspecialchars($p['category']); ?></td>
                                    <td>R$ <?php echo number_format($p['price'], 2, ',', '.'); ?></td>
                                    <td><?php echo $p['stock']; ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $p['is_active'] ? 'success' : 'danger'; ?>">
                                            <?php echo $p['is_active'] ? 'Ativo' : 'Inativo'; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>