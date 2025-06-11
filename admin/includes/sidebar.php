<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
    <div class="position-sticky pt-3">
        <div class="text-center mb-4">
            <img src="../assets/img/logo-brasil-hilario-quadrada-svg.svg" alt="Logo" class="img-fluid" style="max-width: 150px;">
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white <?php echo $current_page === 'index.php' ? 'active bg-primary' : ''; ?>" href="index.php">
                    <i class="fas fa-home"></i>
                    Dashboard
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link text-white <?php echo $current_page === 'posts.php' ? 'active bg-primary' : ''; ?>" href="posts.php">
                    <i class="fas fa-file-alt"></i>
                    Posts
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link text-white <?php echo $current_page === 'novo-post.php' ? 'active bg-primary' : ''; ?>" href="novo-post.php">
                    <i class="fas fa-plus"></i>
                    Novo Post
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link text-white <?php echo $current_page === 'categorias.php' ? 'active bg-primary' : ''; ?>" href="categorias.php">
                    <i class="fas fa-folder"></i>
                    Categorias
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link text-white <?php echo $current_page === 'tags.php' ? 'active bg-primary' : ''; ?>" href="tags.php">
                    <i class="fas fa-tags"></i>
                    Tags
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link text-white <?php echo $current_page === 'comentarios.php' ? 'active bg-primary' : ''; ?>" href="comentarios.php">
                    <i class="fas fa-comments"></i>
                    Comentários
                </a>
            </li>
            
            <?php if (is_admin()): ?>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo $current_page === 'usuarios.php' ? 'active bg-primary' : ''; ?>" href="usuarios.php">
                    <i class="fas fa-users"></i>
                    Usuários
                </a>
            </li>
            <?php endif; ?>
            
            <li class="nav-item">
                <a class="nav-link text-white <?php echo $current_page === 'configuracoes.php' ? 'active bg-primary' : ''; ?>" href="configuracoes.php">
                    <i class="fas fa-cog"></i>
                    Configurações
                </a>
            </li>
        </ul>
        
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Conta</span>
        </h6>
        
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link text-white" href="perfil.php">
                    <i class="fas fa-user"></i>
                    Meu Perfil
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link text-white" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    Sair
                </a>
            </li>
        </ul>
    </div>
</nav> 