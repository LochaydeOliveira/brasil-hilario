    </main>
    <?php
    // Buscar categorias para o rodapé
    $stmt = $conn->prepare("SELECT id, nome, slug FROM categorias ORDER BY nome ASC");
    $stmt->execute();
    $footer_categorias = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    ?>
    <footer class="bg-white py-5 mt-5">
        <div class="container pd-cst-ftr">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <a class="footer-logo-link d-flex align-items-center mb-3" href="<?php echo BLOG_PATH; ?>">
                        <img src="<?php echo BLOG_PATH; ?>/assets/img/logo-brasil-hilario-quadrada-svg.svg" alt="<?php echo BLOG_TITLE; ?>" class="footer-logo-img me-2">
                    </a>
                    <p class="footer-description mb-3"><?php echo BLOG_DESCRIPTION; ?></p>
                    <div class="social-links d-flex">
                        <a href="https://www.facebook.com/profile.php?id=61577306277011" target="_blank" class="social-icon me-2" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                        <a href="#" target="_blank" class="social-icon me-2" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" target="_blank" class="social-icon me-2" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <button class="footer-title btn btn-link d-flex justify-content-between align-items-center w-100 p-0 text-decoration-none text-uppercase" type="button" data-bs-toggle="collapse" data-bs-target="#institucionalMenu" aria-expanded="false" aria-controls="institucionalMenu">
                        INSTITUCIONAL
                        <i class="fas fa-chevron-down d-md-none"></i>
                    </button>
                    <ul class="list-unstyled footer-links collapse d-md-block" id="institucionalMenu">
                        <li><a href="<?php echo BLOG_PATH; ?>/equipe">Sobre Nós</a></li>
                        <li><a href="<?php echo BLOG_PATH; ?>/equipe">Nossos Editores Web</a></li>
                        <li><a href="<?php echo BLOG_PATH; ?>/contato">Fale Conosco</a></li>
                        <li><a href="<?php echo BLOG_PATH; ?>/privacidade">Política de Privacidade</a></li>
                        <li><a href="<?php echo BLOG_PATH; ?>/termos">Termos de Uso</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <button class="footer-title btn btn-link d-flex justify-content-between align-items-center w-100 p-0 text-decoration-none text-uppercase" type="button" data-bs-toggle="collapse" data-bs-target="#categoriasMenu" aria-expanded="false" aria-controls="categoriasMenu">
                        CATEGORIAS
                        <i class="fas fa-chevron-down d-md-none"></i>
                    </button>
                    <ul class="list-unstyled footer-links collapse d-md-block" id="categoriasMenu">
                        <?php foreach ($footer_categorias as $categoria): ?>
                        <li><a href="<?php echo BLOG_PATH; ?>/categoria/<?php echo htmlspecialchars($categoria['slug']); ?>"><?php echo htmlspecialchars($categoria['nome']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="col-md-3 mb-4">
                    <button class="footer-title btn btn-link d-flex justify-content-between align-items-center w-100 p-0 text-decoration-none text-uppercase" type="button" data-bs-toggle="collapse" data-bs-target="#usuariosMenu" aria-expanded="false" aria-controls="usuariosMenu">
                        USUÁRIOS
                        <i class="fas fa-chevron-down d-md-none"></i>
                    </button>
                    <ul class="list-unstyled footer-links collapse d-md-block" id="usuariosMenu">
                        <li><a href="<?php echo BLOG_PATH; ?>/admin/login.php">Entrar na Conta</a></li>
                        <li><a href="<?php echo BLOG_PATH; ?>/criar-conta">Criar conta</a></li>
                    </ul>
                </div> 

            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-9 text-md-start text-center mb-3 mb-md-0">
                    <p class="mb-0 footer-copyright-text">&copy; <?php echo date('Y'); ?> <?php echo BLOG_TITLE; ?>. Todos os direitos reservados.</p>
                </div>
                <div class="col-md-3 text-md-end text-center">
                    <!-- Removido o botão "Ir para o topo" duplicado do HTML -->
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AOS - Animate On Scroll -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true
        });
    </script>
    
    <!-- Custom JS -->
    <script src="<?php echo BLOG_PATH; ?>/assets/js/main.js"></script>
</body>
</html>
