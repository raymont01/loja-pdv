<?php
// Exibe mensagens de sucesso/erro após redirecionamento
if (isset($_GET['msg']) && isset($_GET['type'])):
    $message = htmlspecialchars($_GET['msg']);
    $type = ($_GET['type'] == 'success') ? 'success' : 'danger';
?>
<div class="alert alert-<?php echo $type; ?> alert-dismissible fade show" role="alert">
    <?php echo $message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>