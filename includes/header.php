<?php
require_once 'config/config.php';

// Definir valores padrão para as meta tags, caso não sejam definidos por um post específico
$page_title = isset($og_title) ? $og_title : BLOG_TITLE;
$page_description = isset($meta_description) ? $meta_description : BLOG_DESCRIPTION;
$page_keywords = isset($meta_keywords) ? $meta_keywords : META_KEYWORDS;
$page_url = isset($og_url) ? $og_url : BLOG_URL;
$page_image = isset($og_image) ? $og_image : BLOG_URL . '/assets/img/logo-brasil-hilario-para-og.png';
$page_og_type = isset($og_type) ? $og_type : 'website';


$categories = [];
try {
    $stmt = $conn->prepare("SELECT id, nome, slug FROM categorias ORDER BY nome ASC");
    $stmt->execute();
    $result = $stmt->get_result();
    $categories = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {

    error_log("Erro ao carregar categorias para a barra de navegação: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="<?php echo $page_description; ?>">
    <meta name="keywords" content="<?php echo $page_keywords; ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="<?php echo $page_og_type; ?>">
    <meta property="og:url" content="<?php echo $page_url; ?>">
    <meta property="og:title" content="<?php echo $page_title; ?>">
    <meta property="og:description" content="<?php echo $page_description; ?>">
    <meta property="og:image" content="<?php echo $page_image; ?>">
    <meta property="og:site_name" content="<?php echo BLOG_TITLE; ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="pt_BR">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo $page_url; ?>">
    <meta property="twitter:title" content="<?php echo $page_title; ?>">
    <meta property="twitter:description" content="<?php echo $page_description; ?>">
    <meta property="twitter:image" content="<?php echo $page_image; ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo BLOG_URL; ?>/assets/img/icone-favi-brasil-hilario.png">
    <link rel="apple-touch-icon" href="<?php echo BLOG_URL; ?>/assets/img/icone-favi-brasil-hilario.png">
    <link rel="shortcut icon" href="<?php echo BLOG_URL; ?>/assets/img/icone-favi-brasil-hilario.png">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- AOS - Animate On Scroll -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?php echo BLOG_URL; ?>/assets/css/style.css?v=02" rel="stylesheet">
    
    <!-- Preconnect para melhor performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Merriweather:ital,opsz,wght@0,18..144,300..900;1,18..144,300..900&display=swap" rel="stylesheet">
    
    <!-- Schema.org markup para SEO -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "<?php echo isset($is_post) && $is_post ? 'Article' : 'Blog'; ?>",
        "name": "<?php echo $page_title; ?>",
        "description": "<?php echo $page_description; ?>",
        "url": "<?php echo $page_url; ?>"
        <?php if (isset($is_post) && $is_post): ?>
        ,
        "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": "<?php echo $page_url; ?>"
        },
        "headline": "<?php echo $page_title; ?>",
        "image": [
            "<?php echo $page_image; ?>"
        ],
        "datePublished": "<?php echo date('c', strtotime($post['data_publicacao'])); ?>",
        "dateModified": "<?php echo date('c', strtotime($post['data_atualizacao'] ?? $post['data_publicacao'])); ?>",
        "author": {
            "@type": "Person",
            "name": "<?php echo htmlspecialchars($post['autor_nome']); ?>"
        },
        "publisher": {
            "@type": "Organization",
            "name": "<?php echo BLOG_TITLE; ?>",
            "logo": {
                "@type": "ImageObject",
                "url": "<?php echo BLOG_URL; ?>/assets/img/logo-brasil-hilario-quadrada-svg.svg"
            }
        },
        "description": "<?php echo $page_description; ?>"
        <?php endif; ?>
    }
    </script>

    <style>
        body {
            font-family: "Inter", sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;
        }

        h1, h2, h3, h4, h5, h6, .site-title, .post-title {
            font-family: "Merriweather", serif;
            font-optical-sizing: auto;
            font-weight: 700;
            font-style: normal;
            font-variation-settings: "wdth" 100;
        }

        .btn-search {
            border-color: #0b8103!important;
            color: #0b8103;
        }

        .btn-search:hover {
            color: #ffc107;
            background-color: #0b8103;
            border: none!important;
        }

        .category-navbar {
            display: flex;
            align-items: center;
            position: relative;
            background-color: var(--logo-green-color);
            width: 100%;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        .category-scroll-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
            flex: 1;
        }
        .category-scroll-container::-webkit-scrollbar {
            display: none;
        }
        .category-navbar .nav {
            display: flex;
            flex-wrap: nowrap;
            margin: 0;
            padding: 0;
        }
        .arrow {
            background: none;
            border: none;
            color: #fff;
            font-size: 1.5rem;
            padding: 0 0.5rem;
            cursor: pointer;
            z-index: 2;
        }
        @media (min-width: 768px) {
            .arrow {
                display: none;
            }
        }
        .category-nav-link {
            font-family: var(--font-secondary);
            font-weight: 500;
            color: #fff;
            padding: 0.3rem 0.8rem;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            text-decoration: none;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .category-nav-link:hover {
            background-color: rgba(255,255,255,0.2);
            color: #fff;
            text-decoration: none;
        }
        @media (max-width: 767.98px) {
            .category-navbar .nav {
                flex-wrap: nowrap;
                padding: 0;
            }
            .category-navbar .nav-item:first-child {
                margin-left: 8px;
            }
            .category-navbar .nav-item:last-child {
                margin-right: 8px;
            }
            .category-nav-link {
                font-size: 0.7rem;
                padding: 0.2rem 0.6rem;
            }
        }
    </style>
    
    <!-- Facebook Comments Plugin -->
    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v18.0"></script>
</head>
<body>
    <header class="bg-light shadow-sm">
        <nav class="navbar navbar-expand-lg navbar-light ht-custom">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="<?php echo BLOG_URL; ?>">
                    <img src="<?php echo BLOG_URL; ?>/assets/img/logo-brasil-hilario-quadrada-svg.svg" alt="<?php echo BLOG_TITLE; ?>" class="logo-img me-2">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Alternar navegação">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BLOG_URL; ?>">Início</a>
                        </li>
                        <?php foreach (PAGES as $page): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $page['url']; ?>"><?php echo $page['title']; ?></a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <form class="d-flex" action="<?php echo BLOG_URL; ?>/busca.php" method="GET">
                        <div class="input-group">
                            <input type="search" name="q" class="form-control" placeholder="Buscar no blog..." aria-label="Buscar" required>
                            <button class="btn btn-outline-success" type="submit" aria-label="Buscar">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </nav>
    </header>

<nav class="category-navbar">
    <button class="arrow left" aria-label="Categorias anteriores">&#8592;</button>
    <div class="category-scroll-container">
        <ul class="nav">
            <?php foreach ($categories as $category): ?>
                <li class="nav-item">
                    <a class="category-nav-link" href="<?php echo BLOG_PATH; ?>/categoria/<?php echo htmlspecialchars($category['slug']); ?>">
                        <?php echo htmlspecialchars($category['nome']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <button class="arrow right" aria-label="Próximas categorias">&#8594;</button>
</nav>

    <main class="container mg-custom">

<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainer = document.querySelector('.category-scroll-container');
    const leftArrow = document.querySelector('.arrow.left');
    const rightArrow = document.querySelector('.arrow.right');

    function updateArrows() {
        // Só mostra as setas se houver overflow (scroll possível)
        const canScroll = scrollContainer.scrollWidth > scrollContainer.clientWidth + 2;
        if (!canScroll) {
            leftArrow.style.display = 'none';
            rightArrow.style.display = 'none';
            return;
        }
        // Seta esquerda só aparece se já deslizou
        leftArrow.style.display = (scrollContainer.scrollLeft > 2) ? '' : 'none';
        // Seta direita só aparece se ainda há mais para deslizar
        rightArrow.style.display = (scrollContainer.scrollLeft < scrollContainer.scrollWidth - scrollContainer.clientWidth - 2) ? '' : 'none';
    }

    // Atualiza as setas ao rolar, redimensionar e ao carregar fontes
    scrollContainer.addEventListener('scroll', updateArrows);
    window.addEventListener('resize', updateArrows);

    if (document.fonts) {
        document.fonts.ready.then(updateArrows);
    }
    window.addEventListener('load', updateArrows);

    leftArrow.addEventListener('click', function() {
        scrollContainer.scrollBy({ left: -120, behavior: 'smooth' });
    });
    rightArrow.addEventListener('click', function() {
        scrollContainer.scrollBy({ left: 120, behavior: 'smooth' });
    });

    // Inicializa as setas
    updateArrows();
});
</script>
