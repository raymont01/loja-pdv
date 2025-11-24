<?php
require_once 'actions.php';
$clients = listClients($pdo);
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastro de Clientes - PDV Simples</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <h1>👤 Cadastro de Clientes</h1>
        <p class="lead">Cadastre clientes para registrar as vendas em nome deles.</p>
        
        <?php include 'messages.php'; ?>

        <div class="row">
            <div class="col-md-4">
                <div class="card p-3 shadow-sm">
                    <h5 class="card-title">Novo Cliente</h5>
                    <form method="POST" action="actions.php">
                        <input type="hidden" name="action" value="save_client">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome do Cliente</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Salvar Cliente</button>
                    </form>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card p-3 shadow-sm">
                    <h5 class="card-title">Clientes Cadastrados</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Telefone</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($clients as $c): ?>
                                <tr>
                                    <td><?php echo $c['id']; ?></td>
                                    <td><?php echo htmlspecialchars($c['name']); ?></td>
                                    <td><?php echo htmlspecialchars($c['phone'] ?? ''); ?></td>
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