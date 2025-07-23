<?php
require_once 'config/config.php';

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


<script>

  function loadGoogleAnalytics() {
    const script = document.createElement('script');
    script.async = true;
    script.src = 'https://www.googletagmanager.com/gtag/js?id=G-M6BPB3MLZ2';
    document.head.appendChild(script);
    
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-M6BPB3MLZ2', {
      'consent_mode': 'default',
      'analytics_storage': 'denied'
    });
    

    const consent = getCookieConsent();
    if (consent && consent.analytics) {
      gtag('consent', 'update', {
        'analytics_storage': 'granted'
      });
    }
  }
  

  function getCookieConsent() {
    const nameEQ = 'brasil_hilario_cookie_consent' + "=";
    const ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
      let c = ca[i];
      while (c.charAt(0) === ' ') c = c.substring(1, c.length);
      if (c.indexOf(nameEQ) === 0) {
        try {
          return JSON.parse(c.substring(nameEQ.length, c.length));
        } catch (e) {
          return null;
        }
      }
    }
    return null;
  }
  
  const existingConsent = getCookieConsent();
  if (existingConsent && existingConsent.analytics) {
    loadGoogleAnalytics();
  }
</script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="<?php echo $page_description; ?>">
    <meta name="keywords" content="<?php echo $page_keywords; ?>">
    <meta name="google-adsense-account" content="ca-pub-8313157699231074">

    <meta property="og:type" content="<?php echo $page_og_type; ?>">
    <meta property="og:url" content="<?php echo $page_url; ?>">
    <meta property="og:title" content="<?php echo $page_title; ?>">
    <meta property="og:description" content="<?php echo $page_description; ?>">
    <meta property="og:image" content="<?php echo $page_image; ?>">
    <meta property="og:site_name" content="<?php echo BLOG_TITLE; ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="pt_BR">

    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo $page_url; ?>">
    <meta property="twitter:title" content="<?php echo $page_title; ?>">
    <meta property="twitter:description" content="<?php echo $page_description; ?>">
    <meta property="twitter:image" content="<?php echo $page_image; ?>">

    <link rel="icon" type="image/png" href="<?php echo BLOG_URL; ?>/assets/img/icone-favi-brasil-hilario.png">
    <link rel="apple-touch-icon" href="<?php echo BLOG_URL; ?>/assets/img/icone-favi-brasil-hilario.png">
    <link rel="shortcut icon" href="<?php echo BLOG_URL; ?>/assets/img/icone-favi-brasil-hilario.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="<?php echo BLOG_URL; ?>/assets/css/style.css?v=02" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Merriweather:ital,opsz,wght@0,18..144,300..900;1,18..144,300..900&display=swap" rel="stylesheet">
    

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

        .related-posts-title {
            font-family: "Merriweather", serif;
            font-weight: 700;
            margin-bottom: 1.5rem !important;
            border-bottom: 3px solid #d92332;
            padding-bottom: 0.5rem;
            display: inline-block;
        }

        .related-post-link {
            text-decoration: none;
            color: inherit;
            display: block;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            height: 100%;
        }

        .related-post-link:hover {
            transform: translateY(-5px);
        }

        .related-post-link:hover .related-post-card {
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        }

        .related-post-card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075);
            transition: box-shadow 0.2s ease-in-out;
        }

        .related-post-img {
            height: 120px;
            object-fit: cover;
        }

        .related-post-badge {
            background-color: #d92332 !important;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.7rem;
        }

        .related-post-title {
            font-family: "Merriweather", serif;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0;
            line-height: 1.3;
        }
    </style>
    

    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v18.0"></script>


</head>
<body>

    <div id="cookie-banner" class="cookie-banner" style="display: none;">
        <div class="cookie-content">
            <div class="cookie-text">
                <h5><i class="fas fa-cookie-bite me-2"></i>Política de Cookies</h5>
                <p>Utilizamos cookies para melhorar sua experiência em nosso site, analisar o tráfego e personalizar conteúdo. Ao continuar navegando, você concorda com nossa <a href="<?php echo BLOG_URL; ?>/politica-de-privacidade" target="_blank">Política de Privacidade</a> e uso de cookies.</p>
            </div>
            <div class="cookie-buttons">
                <button id="accept-cookies" class="btn btn-success btn-sm">
                    <i class="fas fa-check me-1"></i>Aceitar Todos
                </button>
                <button id="reject-cookies" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-times me-1"></i>Recusar
                </button>
                <button id="customize-cookies" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-cog me-1"></i>Personalizar
                </button>
            </div>
        </div>
    </div>


    <div class="modal fade" id="cookieModal" tabindex="-1" aria-labelledby="cookieModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cookieModalLabel">
                        <i class="fas fa-cog me-2"></i>Configurações de Cookies
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-4">Escolha quais tipos de cookies você permite que utilizemos:</p>
                    
                    <div class="cookie-option mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="essential-cookies" checked disabled>
                            <label class="form-check-label" for="essential-cookies">
                                <strong>Cookies Essenciais</strong>
                            </label>
                        </div>
                        <small class="text-muted">Necessários para o funcionamento básico do site. Não podem ser desativados.</small>
                    </div>

                    <div class="cookie-option mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="analytics-cookies">
                            <label class="form-check-label" for="analytics-cookies">
                                <strong>Cookies de Análise</strong>
                            </label>
                        </div>
                        <small class="text-muted">Nos ajudam a entender como os visitantes interagem com o site, coletando e relatando informações anonimamente.</small>
                    </div>

                    <div class="cookie-option mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="marketing-cookies">
                            <label class="form-check-label" for="marketing-cookies">
                                <strong>Cookies de Marketing</strong>
                            </label>
                        </div>
                        <small class="text-muted">Usados para rastrear visitantes em sites para exibir anúncios relevantes e envolventes.</small>
                    </div>

                    <div class="cookie-option mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="preference-cookies">
                            <label class="form-check-label" for="preference-cookies">
                                <strong>Cookies de Preferências</strong>
                            </label>
                        </div>
                        <small class="text-muted">Permitem que o site lembre informações que mudam a forma como o site se comporta ou se parece.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="save-cookie-preferences">
                        <i class="fas fa-save me-1"></i>Salvar Preferências
                    </button>
                </div>
            </div>
        </div>
    </div>


    <style>
        .cookie-banner {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            background: #fff;
            color: #222;
            z-index: 9999;
            box-shadow: 0 4px 24px rgba(0,0,0,0.10), 0 1.5px 6px rgba(0,0,0,0.08);
            animation: fadeInCookie 0.5s ease-out;
            width: 100%;
            padding: 1.2rem 1.2rem 1rem 1.2rem;
        }

        @keyframes fadeInCookie {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }

        .cookie-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.7rem;
        }

        .cookie-text {
            flex: 1;
        }

        .cookie-text h5 {
            margin: 0 0 0.3rem 0;
            font-size: 1rem;
            font-weight: 600;
            color: #222;
        }

        .cookie-text p {
            margin: 0;
            font-size: 0.92rem;
            line-height: 1.5;
            color: #444;
        }

        .cookie-text a {
            color: #0b8103;
            text-decoration: underline;
        }

        .cookie-text a:hover {
            color: #0a6b02;
        }

        .cookie-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            width: 100%;
            justify-content: center;
        }

        .cookie-buttons .btn {
            font-size: 0.92rem;
            padding: 0.35rem 1.1rem;
            border-radius: 0
        }

        .cookie-buttons .btn-success {
            background: #0b8103;
            border: none;
        }
        .cookie-buttons .btn-success:hover {
            background: #0a6b02;
        }
        .cookie-buttons .btn-outline-secondary {
            border-color: #bbb;
            color: #444;
        }
        .cookie-buttons .btn-outline-secondary:hover {
            background: #f3f3f3;
            color: #222;
        }
        .cookie-buttons .btn-outline-primary {
            border-color: #0b8103;
            color: #0b8103;
        }
        .cookie-buttons .btn-outline-primary:hover {
            background: #e8f5e9;
            color: #0a6b02;
        }

        .cookie-option {
            padding: 1rem;
            border: 1px solid #e9ecef;
            border-radius: 0.5rem;
            background-color: #f8f9fa;
        }

        .cookie-option .form-check-label {
            font-weight: 500;
            color: #333;
        }

        .cookie-option small {
            display: block;
            margin-top: 0.25rem;
        }

        @media (max-width: 600px) {
            .cookie-buttons {
                margin: 0 0 0 20px;
            }
            .cookie-banner {
                position: fixed;
                bottom: 0;
                left: 44%;
                transform: translateX(-50%);
                background: #fff;
                color: #222;
                z-index: 9999;
                box-shadow: 0 4px 24px rgba(0,0,0,0.10), 0 1.5px 6px rgba(0,0,0,0.08);
                animation: fadeInCookie 0.5s ease-out;
                width: 100%;
                padding: 1.2rem 1.2rem 1rem 1.2rem;
                max-width: 27rem;
            }
            .cookie-banner {
                bottom: 0;
                padding: 0.8rem 0.7rem 0.7rem 0.7rem;
                border-radius: 0;
            }
            .cookie-content {
                gap: 0.5rem;
            }
            .cookie-text h5 {
                font-size: 0.98rem;
            }
            .cookie-text p {
                font-size: 0.87rem;
                max-width: 20rem;
            }
            .cookie-buttons .btn {
                font-size: 0.87rem;
                padding: 0.32rem 0.7rem;
            }
        }

        .cookie-banner.hidden {
            display: none !important;
        }
    </style>

    <header class="bg-light shadow-sm">
        <nav class="navbar navbar-expand-lg navbar-light ht-custom">
            <div class="container bg-nav-custom">
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
                        <?php foreach (PAGES as $page_item): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $page_item['url']; ?>"><?php echo $page_item['title']; ?></a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <form class="d-flex mg-bt-search" action="<?php echo BLOG_URL; ?>/busca.php" method="GET">
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
        <button class="arrow left" aria-label="Categorias anteriores">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" width="25" height="25" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
            </svg>
        </button>
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
        <button class="arrow right" aria-label="Próximas categorias">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"  width="25" height="25" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
        </button>
    </nav>

    <main class="container mg-custom">

<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainer = document.querySelector('.category-scroll-container');
    const leftArrow = document.querySelector('.arrow.left');
    const rightArrow = document.querySelector('.arrow.right');

    function updateArrows() {

        const canScroll = scrollContainer.scrollWidth > scrollContainer.clientWidth + 2;
        if (!canScroll) {
            leftArrow.style.display = 'none';
            rightArrow.style.display = 'none';
            return;
        }

        leftArrow.style.display = (scrollContainer.scrollLeft > 2) ? '' : 'none';

        rightArrow.style.display = (scrollContainer.scrollLeft < scrollContainer.scrollWidth - scrollContainer.clientWidth - 2) ? '' : 'none';
    }


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
