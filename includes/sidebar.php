<?php

try {
    // Últimas postagens
    $stmt = $pdo->prepare("
        SELECT id, titulo, slug, data_publicacao, imagem_destacada 
        FROM posts 
        WHERE publicado = 1 
        ORDER BY data_publicacao DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $ultimas_postagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Categorias
    $stmt = $pdo->prepare("SELECT id, nome, slug FROM categorias ORDER BY nome ASC");
    $stmt->execute();
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Posts populares
    $stmt = $pdo->prepare("
        SELECT id, titulo, slug, visualizacoes 
        FROM posts 
        WHERE publicado = 1 
        ORDER BY visualizacoes DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $posts_populares = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Erro ao buscar dados no banco: " . $e->getMessage());
    // Definir variáveis vazias para evitar erros no frontend
    $ultimas_postagens = [];
    $categorias = [];
    $posts_populares = [];
}
?>


<div class="sidebar">

    <div class="card mb-4" data-aos="fade-left">
        <div class="card-header">
            <h3 class="mb-0">Mais Recentes</h3>
        </div>
        <div class="card-body">
            <ul class="list-unstyled">
                <?php foreach ($ultimas_postagens as $post): ?>
                <li class="mb-3">
                    <?php if (!empty($post['imagem_destacada'])): ?>
                        <div class="post-thumbnail mb-2">
                            <a href="<?php echo BLOG_URL; ?>/post/<?php echo $post['slug']; ?>">
                                <img src="<?php echo BLOG_URL; ?>/uploads/images/<?php echo htmlspecialchars($post['imagem_destacada']); ?>" 
                                     class="img-fluid" 
                                     alt="<?php echo htmlspecialchars($post['titulo']); ?>"
                                    >
                            </a>
                        </div>
                    <?php endif; ?>
                    <a style="line-height: 1.1rem !important;" href="<?php echo BLOG_URL; ?>/post/<?php echo $post['slug']; ?>" class="text-decoration-none">
                        <?php echo htmlspecialchars($post['titulo']); ?>
                    </a>
                    <small class="text-muted d-block">
                        <?php echo date('d/m/Y', strtotime($post['data_publicacao'])); ?>
                    </small>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="card mb-4" data-aos="fade-left" data-aos-delay="100">
        <div class="card-header">
            <h3 class="mb-0">Categorias</h3>
        </div>
        <div class="card-body">
            <ul class="list-unstyled">
                <?php foreach ($categorias as $categoria): ?>
                <li class="mb-2">
                    <a href="<?php echo BLOG_URL; ?>/categoria/<?php echo $categoria['slug']; ?>" class="text-decoration-none">
                        <?php echo htmlspecialchars($categoria['nome']); ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="card mb-4" data-aos="fade-left" data-aos-delay="200">
        <div class="card-header">
            <h3 class="mb-0">Mais Lidos</h3>
        </div>
        <div class="card-body">
            <ul class="list-unstyled">
                <?php foreach ($posts_populares as $post): ?>
                <li class="mb-3">
                    <?php if (!empty($post['imagem_destacada'])): ?>
                        <div class="post-thumbnail mb-2">
                            <a href="<?php echo BLOG_URL; ?>/post/<?php echo $post['slug']; ?>">
                                <img src="<?php echo BLOG_URL; ?>/uploads/images/<?php echo htmlspecialchars($post['imagem_destacada']); ?>" 
                                     class="img-fluid rounded" 
                                     alt="<?php echo htmlspecialchars($post['titulo']); ?>"
                                     >
                            </a>
                        </div>
                    <?php endif; ?>
                    <a href="<?php echo BLOG_URL; ?>/post/<?php echo $post['slug']; ?>" class="text-decoration-none">
                        <?php echo htmlspecialchars($post['titulo']); ?>
                    </a>
                    <small class="text-muted d-block">
                        <?php echo number_format($post['visualizacoes']); ?> visualizações
                    </small>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="card mb-4" data-aos="fade-left" data-aos-delay="300">
        <div class="card-body">

        <ins class="adsbygoogle"
            style="display:block; text-align:center;"
            data-ad-layout="in-article"
            data-ad-format="fluid"
            data-ad-client="ca-pub-8313157699231074"
            data-ad-slot="4177902168">
        </ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>

        </div>
    </div>

</div> 