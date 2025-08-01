<?php
require_once 'config/config.php';
require_once 'includes/db.php';
require_once 'includes/AnunciosManager.php';

// Verificar se é uma requisição AJAX
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($isAjax) {
    header('Content-Type: application/json');
    
    // Obter parâmetros
    $localizacao = $_GET['localizacao'] ?? '';
    $postId = (int) ($_GET['post_id'] ?? 0);

    // Validar localização
    $localizacoesValidas = ['sidebar', 'conteudo'];
    if (!in_array($localizacao, $localizacoesValidas)) {
        http_response_code(400);
        echo json_encode(['error' => 'Localização inválida']);
        exit;
    }

    try {
        $anunciosManager = new AnunciosManager($pdo);
        
        // Buscar anúncios para a localização
        $anuncios = $anunciosManager->getAnunciosPorLocalizacao($localizacao, $postId > 0 ? $postId : null);
        
        // Gerar HTML para cada anúncio
        $anunciosComHTML = [];
        foreach ($anuncios as $anuncio) {
            $anunciosComHTML[] = [
                'id' => $anuncio['id'],
                'titulo' => $anuncio['titulo'],
                'imagem' => $anuncio['imagem'],
                'link_compra' => $anuncio['link_compra'],
                'cta_ativo' => $anuncio['cta_ativo'],
                'cta_texto' => $anuncio['cta_texto'],
                'html' => $anunciosManager->gerarHTMLAnuncio($anuncio)
            ];
        }
        
        echo json_encode([
            'success' => true,
            'anuncios' => $anunciosComHTML,
            'total' => count($anunciosComHTML)
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro interno do servidor']);
    }
    exit;
}

// Se não for AJAX, mostrar página normal
$page_title = 'Anúncios';
include 'includes/header.php';
?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <h1>Anúncios</h1>
            <p>Esta página contém a API de anúncios. Para acessar os dados, use AJAX.</p>
            
            <div class="card">
                <div class="card-header">
                    <h5>Teste da API</h5>
                </div>
                <div class="card-body">
                    <p>Para testar a API, acesse:</p>
                    <ul>
                        <li><code>/anuncios.php?localizacao=sidebar&post_id=0</code></li>
                        <li><code>/anuncios.php?localizacao=conteudo&post_id=0</code></li>
                    </ul>
                    <p>Certifique-se de incluir o header: <code>X-Requested-With: XMLHttpRequest</code></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 