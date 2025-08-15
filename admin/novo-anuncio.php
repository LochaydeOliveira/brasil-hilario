<?php
require_once '../config/config.php';
require_once '../config/database_unified.php';
require_once 'includes/auth.php';

// Verificar se o usuário está logado
check_login();

$dbManager = DatabaseManager::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $imagem = trim($_POST['imagem']);
    $link_compra = trim($_POST['link_compra']);
    $marca = $_POST['marca'];
    $ativo = isset($_POST['ativo']) ? 1 : 0;
    
    // Validações básicas
    if (empty($titulo) || empty($link_compra)) {
        $erro = "Título e link são obrigatórios.";
    } else {
        try {
            $sql = "INSERT INTO anuncios (titulo, imagem, link_compra, marca, ativo, criado_em) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            
            $resultado = $dbManager->execute($sql, [$titulo, $imagem, $link_compra, $marca, $ativo]);
            
            if ($resultado) {
                $sucesso = "Anúncio criado com sucesso!";
                // Limpar formulário
                $_POST = array();
            } else {
                $erro = "Erro ao criar anúncio.";
            }
        } catch (Exception $e) {
            $erro = "Erro: " . $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1 class="h3 mb-4">Criar Novo Anúncio</h1>
            
            <?php if (isset($erro)): ?>
                <div class="alert alert-danger"><?php echo $erro; ?></div>
            <?php endif; ?>
            
            <?php if (isset($sucesso)): ?>
                <div class="alert alert-success"><?php echo $sucesso; ?></div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informações do Produto</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="titulo" class="form-label">Nome do Produto *</label>
                                    <input type="text" class="form-control" id="titulo" name="titulo" 
                                           value="<?php echo isset($_POST['titulo']) ? htmlspecialchars($_POST['titulo']) : ''; ?>" 
                                           required>
                                    <div class="form-text">Nome completo do produto para exibição</div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="marca" class="form-label">Marca</label>
                                    <select class="form-select" id="marca" name="marca">
                                        <option value="">Nenhuma</option>
                                        <option value="amazon" <?php echo (isset($_POST['marca']) && $_POST['marca'] === 'amazon') ? 'selected' : ''; ?>>Amazon</option>
                                        <option value="shopee" <?php echo (isset($_POST['marca']) && $_POST['marca'] === 'shopee') ? 'selected' : ''; ?>>Shopee</option>
                                    </select>
                                    <div class="form-text">Marca do produto (opcional)</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="link_compra" class="form-label">Link do Produto *</label>
                            <input type="url" class="form-control" id="link_compra" name="link_compra" 
                                   value="<?php echo isset($_POST['link_compra']) ? htmlspecialchars($_POST['link_compra']) : ''; ?>" 
                                   required>
                            <div class="form-text">Link direto para compra do produto</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="imagem" class="form-label">URL da Imagem</label>
                            <input type="url" class="form-control" id="imagem" name="imagem" 
                                   value="<?php echo isset($_POST['imagem']) ? htmlspecialchars($_POST['imagem']) : ''; ?>">
                            <div class="form-text">URL da imagem do produto (opcional)</div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ativo" name="ativo" 
                                       <?php echo (isset($_POST['ativo']) && $_POST['ativo']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="ativo">
                                    Anúncio Ativo
                                </label>
                                <div class="form-text">Anúncios inativos não aparecem em nenhum grupo</div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="anuncios.php" class="btn btn-secondary">← Voltar</a>
                            <button type="submit" class="btn btn-primary">Criar Anúncio</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Como Funciona</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>📋 Página Anúncios</h6>
                            <ul class="mb-0">
                                <li>Crie o catálogo de produtos</li>
                                <li>Configure informações básicas</li>
                                <li>Defina marca (Amazon/Shopee)</li>
                                <li>Ative/desative produtos</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>🎯 Próximo Passo</h6>
                            <ul class="mb-0">
                                <li>Vá para "Grupos de Anúncios"</li>
                                <li>Selecione produtos do catálogo</li>
                                <li>Configure onde e como exibir</li>
                                <li>Defina posts específicos</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 
