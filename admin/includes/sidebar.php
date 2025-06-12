<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>" href="index.php">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'posts.php' ? 'active' : ''; ?>" href="posts.php">
                    <i class="fas fa-file-alt"></i> Posts
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'novo-post.php' ? 'active' : ''; ?>" href="novo-post.php">
                    <i class="fas fa-plus"></i> Novo Post
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'categorias.php' ? 'active' : ''; ?>" href="categorias.php">
                    <i class="fas fa-folder"></i> Categorias
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'tags.php' ? 'active' : ''; ?>" href="tags.php">
                    <i class="fas fa-tags"></i> Tags
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'comentarios.php' ? 'active' : ''; ?>" href="comentarios.php">
                    <i class="fas fa-comments"></i> Comentários
                </a>
            </li>
            
            <?php if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'usuarios.php' ? 'active' : ''; ?>" href="usuarios.php">
                    <i class="fas fa-users"></i> Usuários
                </a>
            </li>
            <?php endif; ?>
            
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'configuracoes.php' ? 'active' : ''; ?>" href="configuracoes.php">
                    <i class="fas fa-cog"></i> Configurações
                </a>
            </li>
        </ul>
        
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-white">
            <span>Conta</span>
        </h6>
        
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page === 'perfil.php' ? 'active' : ''; ?>" href="perfil.php">
                    <i class="fas fa-user"></i> Perfil
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </li>
        </ul>
    </div>
</nav> 