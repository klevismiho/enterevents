<!doctype html>
<html <?php language_attributes(); ?>>

<head>
    <title><?php wp_title(); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <?php wp_head(); ?>
    <?php if (current_user_can('administrator')) : ?>
        <style>
            .wc_payment_method.payment_method_cod {
                display: block !important;
            }
        </style>
    <?php endif; ?>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-H58RMGEF68"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-H58RMGEF68');
    </script>
</head>

<body <?php body_class(); ?>>
    <?php
    // Add My Account to mobile menu using filter
    function add_mobile_account_to_menu($items, $args)
    {
        // Only add to primary menu
        if ($args->theme_location == 'primary-menu') {
            $account_url = wc_get_page_permalink('myaccount');
            $mobile_account = '<li class="menu-item mobile-account-item">';
            $mobile_account .= '<a href="' . esc_url($account_url) . '" class="mobile-account-link">';
            $mobile_account .= 'My Account';
            $mobile_account .= '</a>';
            $mobile_account .= '</li>';

            $items .= $mobile_account;
        }
        return $items;
    }
    add_filter('wp_nav_menu_items', 'add_mobile_account_to_menu', 10, 2);
    ?>

    <header class="site-header">
        <div class="container">
            <div class="header-inner">

                <!-- Logo -->
                <div class="header-logo">
                    <a href="<?php echo esc_url(home_url('/')); ?>">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/logo-white.png" alt="<?php bloginfo('name'); ?>">
                    </a>
                </div>

                <!-- Navigation -->
                <nav class="header-menu">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary-menu',
                        'container'      => false,
                        'menu_class'     => 'nav-list',
                    ));
                    ?>
                </nav>

                <!-- Right-side icons -->
                <div class="header-actions">
                    <!-- Desktop My Account Button -->
                    <a href="<?php echo wc_get_page_permalink('myaccount'); ?>" class="secondary header-button desktop-account-button">My Account</a>
                    <!-- Cart Link -->
                    <a href="<?php echo wc_get_cart_url(); ?>" class="cart-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9h14l-2-9M10 21a1 1 0 11-2 0 1 1 0 012 0zm8 0a1 1 0 11-2 0 1 1 0 012 0z" />
                        </svg>
                        <span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                    </a>
                </div>

                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle" aria-label="Toggle Menu">
                    <div class="hamburger">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </button>

            </div>
        </div>

        <!-- Mobile Menu Overlay -->
        <div class="menu-overlay"></div>
    </header>