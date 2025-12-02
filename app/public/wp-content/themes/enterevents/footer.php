<footer class="site-footer">
    <div class="container">
        <div class="footer-inner">

            <div class="footer-logo">
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/logo-white.png" alt="<?php bloginfo('name'); ?>">
                </a>
            </div>

            <!-- Navigation -->
            <nav class="footer-nav">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary-menu',
                    'container'      => false,
                    'menu_class'     => 'nav-list',
                ));
                ?>
            </nav>

            <div class="footer-social">
                <!-- Instagram -->
                <a href="https://www.instagram.com/enterevents.al/" aria-label="Instagram" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.75 2h8.5A5.75 5.75 0 0122 7.75v8.5A5.75 5.75 0 0116.25 22h-8.5A5.75 5.75 0 012 16.25v-8.5A5.75 5.75 0 017.75 2z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 7.5h.01M12 8.75A3.25 3.25 0 1112 15.25 3.25 3.25 0 0112 8.75z" />
                    </svg>
                </a>

                <!-- Facebook -->
                <a href="https://www.facebook.com/enteralb" aria-label="Facebook" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z" />
                    </svg>
                </a>

                <!-- YouTube -->
                <a href="https://www.youtube.com/@Enter-Events" aria-label="YouTube" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M21.6 7.2s-.2-1.5-.8-2.1c-.8-.8-1.7-.8-2.1-.9C15.8 4 12 4 12 4h-.1s-3.8 0-6.7.2c-.4 0-1.3 0-2.1.9-.6.6-.8 2.1-.8 2.1S2 8.9 2 10.6v1.8c0 1.7.3 3.4.3 3.4s.2 1.5.8 2.1c.8.8 1.9.8 2.4.9 1.7.2 6.5.2 6.5.2s3.8 0 6.7-.2c.4 0 1.3 0 2.1-.9.6-.6.8-2.1.8-2.1s.3-1.7.3-3.4v-1.8c0-1.7-.3-3.4-.3-3.4zM10 14.9V8.7l5.7 3.1L10 14.9z" />
                    </svg>
                </a>
            </div>

        </div>

        <div class="footer-bottom">
            <p>© 2025 Enter Events. All rights reserved.</p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
Test
</body>

</html>