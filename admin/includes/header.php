<?php
/**
 * Arquivo: header.php
 * Descrição: Cabeçalho padrão do painel administrativo
 * Funcionalidades:
 * - Define estrutura HTML base
 * - Inclui CSS e JavaScript necessários
 * - Exibe menu de navegação
 * - Gerencia sessão e autenticação
 */

// Inicia a sessão se ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está autenticado
if (!isLoggedIn()) {
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
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Estilos personalizados -->
    <style>
        /* Estilo da barra lateral */
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
            background-color: #212529;
        }

        /* Estilo do conteúdo principal */
        .main-content {
            margin-left: 240px;
            padding: 20px;
        }

        /* Estilo da barra de navegação */
        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }

        /* Estilo dos links da barra lateral */
        .sidebar .nav-link {
            color: #fff;
            padding: .5rem 1rem;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255,255,255,.1);
        }

        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255,255,255,.2);
        }

        /* Estilo do cabeçalho */
        .navbar-brand {
            padding: .5rem 1rem;
        }

        /* Ajuste para telas pequenas */
        @media (max-width: 767.98px) {
            .sidebar {
                position: static;
                height: auto;
                padding-top: 0;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Barra de navegação superior -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container-fluid">
            <!-- Logo e nome do site -->
            <a class="navbar-brand" href="index.php">
                <img src="../assets/img/logo-brasil-hilario-quadrada-svg.svg" alt="Logo" height="30" class="d-inline-block align-text-top">
                Brasil Hilário
            </a>
            
            <!-- Menu de usuário -->
            <div class="ms-auto">
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle text-dark" type="button" id="userDropdown" data-bs-toggle="dropdown">
                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário'); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="perfil.php"><i class="fas fa-user-cog"></i> Perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Container principal -->
    <div class="container-fluid">
        <div class="row">
            <!-- Barra lateral -->
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <?php include 'sidebar.php'; ?>
            </nav>

            <!-- Conteúdo principal -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- Título da página -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?php echo $page_title ?? 'Dashboard'; ?></h1>
                </div>

                <!-- Mensagens de alerta -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php 
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php 
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
</body>
</html> 