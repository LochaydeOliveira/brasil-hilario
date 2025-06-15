<?php
// Buscar últimas postagens
$stmt = $pdo->query("
    SELECT id, titulo, slug, data_publicacao, imagem_destacada 
    FROM posts 
    WHERE publicado = 1 
    ORDER BY data_publicacao DESC 
    LIMIT 5
");
$ultimas_postagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar categorias
$stmt = $pdo->query("SELECT id, nome, slug FROM categorias ORDER BY nome ASC");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar posts mais lidos
$stmt = $pdo->query("
    SELECT id, titulo, slug, visualizacoes 
    FROM posts 
    WHERE publicado = 1 
    ORDER BY visualizacoes DESC 
    LIMIT 5
");
$posts_populares = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="sidebar">
    <!-- Widget de Últimas Postagens -->
    <div class="card mb-4" data-aos="fade-left">
        <div class="card-header">
            <h5 class="mb-0">Últimas Postagens</h5>
        </div>
        <div class="card-body">
            <ul class="list-unstyled">
                <?php foreach ($ultimas_postagens as $post): ?>
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
                        <?php echo date('d/m/Y', strtotime($post['data_publicacao'])); ?>
                    </small>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- Widget de Categorias -->
    <div class="card mb-4" data-aos="fade-left" data-aos-delay="100">
        <div class="card-header">
            <h5 class="mb-0">Categorias</h5>
        </div>
        <div class="card-body">
            <ul class="list-unstyled">
                <?php
                // Busca todas as categorias com contagem de posts
                $stmt = $conn->prepare("
                    SELECT c.*, COUNT(pc.post_id) as total_posts 
                    FROM categorias c 
                    LEFT JOIN posts_categorias pc ON c.id = pc.categoria_id 
                    LEFT JOIN posts p ON pc.post_id = p.id AND p.status = 'publicado'
                    GROUP BY c.id 
                    ORDER BY c.nome
                ");
                $stmt->execute();
                $categorias = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                
                foreach ($categorias as $cat): 
                ?>
                <li class="mb-2">
                    <a href="<?php echo BLOG_URL; ?>/categoria/<?php echo $cat['slug']; ?>" class="d-flex justify-content-between align-items-center">
                        <?php echo htmlspecialchars($cat['nome']); ?>
                        <span class="badge bg-primary rounded-pill"><?php echo $cat['total_posts']; ?></span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- Widget de Posts Populares -->
    <div class="card mb-4" data-aos="fade-left" data-aos-delay="200">
        <div class="card-header">
            <h5 class="mb-0">Posts Mais Lidos</h5>
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

    <!-- Widget do AdSense -->
    <div class="card mb-4" data-aos="fade-left" data-aos-delay="300">
        <div class="card-body">
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="<?php echo ADSENSE_CLIENT_ID; ?>"
                 data-ad-slot="<?php echo ADSENSE_SLOT_ID; ?>"
                 data-ad-format="auto"
                 data-full-width-responsive="true"></ins>
            <script>
                 (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>
    </div>
</div> 