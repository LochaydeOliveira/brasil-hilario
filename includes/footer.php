    </main>
    <footer class="bg-white py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <a class="footer-logo-link d-flex align-items-center mb-3" href="<?php echo BLOG_PATH; ?>">
                        <img src="<?php echo BLOG_PATH; ?>/assets/img/logo-brasil-hilario-quadrada-svg.svg" alt="<?php echo BLOG_TITLE; ?>" class="footer-logo-img me-2">
                    </a>
                    <p class="footer-description mb-3"><?php echo BLOG_DESCRIPTION; ?></p>
                    <div class="social-links d-flex">
                        <a href="https://www.facebook.com/profile.php?id=61577306277011" target="_blank" class="social-icon me-2"><i class="fab fa-facebook"></i></a>
                        <a href="#" target="_blank" class="social-icon me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" target="_blank" class="social-icon me-2"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <h5 class="footer-title">INSTITUCIONAL</h5>
                    <ul class="list-unstyled footer-links">
                        <li><a href="<?php echo BLOG_PATH; ?>/equipe">Sobre Nós</a></li>
                        <li><a href="<?php echo BLOG_PATH; ?>/equipe">Nossos Editores Web</a></li>
                        <li><a href="<?php echo BLOG_PATH; ?>/contato">Fale Conosco</a></li>
                        <li><a href="<?php echo BLOG_PATH; ?>/privacidade">Política de Privacidade</a></li>
                        <li><a href="<?php echo BLOG_PATH; ?>/termos">Termos de Uso</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5 class="footer-title">CATEGORIAS</h5>
                    <ul class="list-unstyled footer-links">
                        <li><a href="<?php echo BLOG_PATH; ?>/categoria/politica">Política</a></li>
                        <li><a href="<?php echo BLOG_PATH; ?>/categoria/economia">Economia</a></li>
                        <li><a href="<?php echo BLOG_PATH; ?>/categoria/brasil">Brasil</a></li>
                        <li><a href="<?php echo BLOG_PATH; ?>/categoria/mundo">Mundo</a></li>
                        <li><a href="<?php echo BLOG_PATH; ?>/categoria/tecnologia">Tecnologia</a></li>
                        <li><a href="<?php echo BLOG_PATH; ?>/categoria/empreendedorismo">Empreendedorismo</a></li>
                        <li><a href="<?php echo BLOG_PATH; ?>/categoria/agro">Agro</a></li>
                        <li><a href="<?php echo BLOG_PATH; ?>/categoria/musica">Música</a></li>
                        <li><a href="<?php echo BLOG_PATH; ?>/categoria/cinema">Cinema</a></li>
                        <li><a href="<?php echo BLOG_PATH; ?>/categoria/famosos">Famosos</a></li>
                    </ul>
                </div>

                <!-- <div class="col-md-3 mb-4">
                    <h5 class="footer-title">FAQ</h5>
                    <ul class="list-unstyled footer-links">
                        <li><a href="<?php echo BLOG_PATH; ?>/criar-conta">Crie uma Conta</a></li>
                        <li><a href="<?php echo BLOG_PATH; ?>/assinatura">Assine a Revista</a></li>
                    </ul>
                </div> -->

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
