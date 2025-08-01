<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Painel Administrativo</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./css-custom/style-custom-adm.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Merriweather:ital,opsz,wght@0,18..144,300..900;1,18..144,300..900&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?php echo BLOG_URL; ?>/assets/img/icone-favi-brasil-hilario.png">
    <link rel="apple-touch-icon" href="<?php echo BLOG_URL; ?>/assets/img/icone-favi-brasil-hilario.png">
    <link rel="shortcut icon" href="<?php echo BLOG_URL; ?>/assets/img/icone-favi-brasil-hilario.png">
    
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
            background-color: #343a40;
        }
        
        .sidebar-sticky {
            position: relative;
            top: 0;
            height: calc(100vh - 48px);
            padding-top: .5rem;
            overflow-x: hidden;
            overflow-y: auto;
        }
        
        .navbar {
            background-color: #fff !important;
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        
        .navbar-brand {
            padding-top: .75rem;
            padding-bottom: .75rem;
            font-size: 1rem;
            background: #fff!important;
        }
        
        .navbar-brand img {
            max-height: 40px;
            width: auto;
        }
        
        .navbar .navbar-toggler {
            top: .25rem;
            right: 1rem;
        }
        
        .navbar .form-control {
            padding: .75rem 1rem;
            border-width: 0;
            border-radius: 0;
        }
        
        .form-control-dark {
            color: #fff;
            background-color: rgba(255, 255, 255, .1);
            border-color: rgba(255, 255, 255, .1);
        }
        
        .form-control-dark:focus {
            border-color: transparent;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, .25);
        }
        
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }
        
        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        .nav-link:hover {
            color: #fff !important;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .nav-link.active {
            color: #fff !important;
            background-color: #0d6efd;
        }

        .nav-link i {
            margin-right: 0.5rem;
            width: 20px;
            text-align: center;
        }

        .sidebar-heading {
            font-size: .75rem;
            text-transform: uppercase;
        }

        .main-content {
            margin-left: 240px;
            padding: 20px;
        }

        .navbar .nav-link {
            color: #333 !important;
        }

        .navbar .nav-link:hover {
            color: #0d6efd !important;
            background-color: transparent;
        }

        @media (max-width: 767.98px) {
            .main-content {
                margin-left: 0;
            }
        }
        
        /* CSS específico para paginação no admin */
        .pagination {
            margin: 2rem 0 !important;
            display: flex !important;
            justify-content: center !important;
        }
        
        .page-link {
            color: #007bff !important;
            border: 1px solid #007bff !important;
            background-color: #ffffff !important;
            padding: 0.5rem 0.75rem !important;
            margin: 0 0.1rem !important;
            font-size: 0.875rem !important;
            text-decoration: none !important;
            border-radius: 0.25rem !important;
            transition: all 0.2s ease !important;
            display: block !important;
            position: relative !important;
        }
        
        .page-link:hover {
            background-color: #007bff !important;
            color: #ffffff !important;
            border-color: #007bff !important;
            text-decoration: none !important;
            z-index: 2 !important;
        }
        
        .page-item.active .page-link {
            background-color: #007bff !important;
            color: #ffffff !important;
            border-color: #007bff !important;
            font-weight: 600 !important;
            z-index: 3 !important;
        }
        
        .page-item.disabled .page-link {
            color: #6c757d !important;
            border-color: #6c757d !important;
            background-color: #ffffff !important;
            opacity: 0.6 !important;
            cursor: not-allowed !important;
        }
        
        .page-item {
            display: inline-block !important;
            margin: 0 0.1rem !important;
        }
        
        .page-item .page-link {
            min-width: 40px !important;
            text-align: center !important;
        }
    </style>
</head>
<body>
    <header class="navbar sticky-top flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="index.php">
            <img width="150" src="../assets/img/logo-brasil-hilario-quadrada-svg.svg" alt="Brasil Hilário" class="img-fluid logo-header-adm">
        </a>
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="w-100"></div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="position-sticky pt-3">
                    <?php include 'sidebar.php'; ?>
                </div>
            </nav> 