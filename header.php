<!DOCTYPE html>
<html <?php language_attributes(); ?> class="dark">
<head>

    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header id="masthead" class="site-header" role="banner">
    <div class="mx-auto" style="max-width: 960px;">
        <div class="flex justify-between items-center gap-6 py-8">
            <div class="flex-none">
                <h1 class="text-2xl font-bold leading-tight m-0" style="font-family: var(--rakan-font-heading);">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="no-underline transition-colors duration-200" style="color: var(--rakan-text-primary);">
                        <?php bloginfo('name'); ?>
                    </a>
                </h1>
                <?php 
                $description = get_bloginfo('description', 'display');
                if ($description || is_customize_preview()) : 
                ?>
                    <p class="text-xs italic mt-1 m-0" style="color: var(--rakan-text-muted); font-family: var(--rakan-font-body);">
                        <?php echo $description; ?>
                    </p>
                <?php endif; ?>
            </div>

            <nav class="flex-none" role="navigation">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_class'     => 'flex gap-6 list-none m-0 p-0 items-center',
                    'container'      => false,
                    'fallback_cb'    => false,
                ));
                ?>
            </nav>
        </div>

        <!-- Barra de usuário e redes sociais -->
        <div class="flex justify-between items-center py-3 text-sm pb-6" style="border-color: var(--rakan-border);">
            <!-- Saudação / Login -->
            <div class="user-greeting">
                <?php if (is_user_logged_in()) : 
                    $current_user = wp_get_current_user();
                    $user_name = $current_user->user_firstname;
                ?>
                    <span style="color: var(--rakan-text-secondary);">
                        Olá <strong style="color: var(--rakan-text-primary);"><?php echo esc_html($user_name); ?></strong>, 
                        <span class="greeting-time"></span>.
                    </span>
                <?php else : ?>
                    <div class="flex gap-4 items-center">
                        <a href="<?php echo esc_url(wp_registration_url()); ?>" class="flex items-center gap-1.5 no-underline transition-colors duration-200" style="color: var(--rakan-text-secondary);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <line x1="19" y1="8" x2="19" y2="14"/>
                                <line x1="22" y1="11" x2="16" y2="11"/>
                            </svg>
                            Cadastre-se
                        </a>
                        <span style="color: var(--rakan-border);">|</span>
                        <a href="<?php echo esc_url(wp_login_url()); ?>" class="flex items-center gap-1.5 no-underline transition-colors duration-200" style="color: var(--rakan-text-secondary);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                                <polyline points="10 17 15 12 10 7"/>
                                <line x1="15" y1="12" x2="3" y2="12"/>
                            </svg>
                            Entre
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Redes Sociais -->
            <div class="social-links flex gap-4 items-center">
                <a href="<?php echo esc_url(get_theme_mod('bluesky_url', '#')); ?>" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="Bluesky">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 576 512" fill="currentColor">
                        <path d="M407.8 294.7c-3.3-.4-6.7-.8-10-1.3c3.4 .4 6.7 .9 10 1.3zM288 227.1C261.9 176.4 190.9 81.9 124.9 35.3C61.6-9.4 37.5-1.7 21.6 5.5C3.3 13.8 0 41.9 0 58.4S9.1 194 15 213.9c19.5 65.7 89.1 87.9 153.2 80.7c3.3-.5 6.6-.9 10-1.4c-3.3 .5-6.6 1-10 1.4C74.3 308.6-9.1 342.8 100.3 464.5C220.6 589.1 265.1 437.8 288 361.1c22.9 76.7 49.2 222.5 185.6 103.4c102.4-103.4 28.1-156-65.8-169.9c-3.3-.4-6.7-.8-10-1.3c3.4 .4 6.7 .9 10 1.3c64.1 7.1 133.6-15.1 153.2-80.7C566.9 194 576 75 576 58.4s-3.3-44.7-21.6-52.9c-15.8-7.1-40-14.9-103.2 29.8C385.1 81.9 314.1 176.4 288 227.1z"/>
                    </svg>
                </a>
                <a href="<?php echo esc_url(get_theme_mod('instagram_url', '#')); ?>" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="Instagram">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
                    </svg>
                </a>
                <a href="<?php echo esc_url(get_theme_mod('discord_url', '#')); ?>" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="Discord">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 127.14 96.36" fill="currentColor">
                        <path d="M107.7,8.07A105.15,105.15,0,0,0,81.47,0a72.06,72.06,0,0,0-3.36,6.83A97.68,97.68,0,0,0,49,6.83,72.37,72.37,0,0,0,45.64,0,105.89,105.89,0,0,0,19.39,8.09C2.79,32.65-1.71,56.6.54,80.21h0A105.73,105.73,0,0,0,32.71,96.36,77.7,77.7,0,0,0,39.6,85.25a68.42,68.42,0,0,1-10.85-5.18c.91-.66,1.8-1.34,2.66-2a75.57,75.57,0,0,0,64.32,0c.87.71,1.76,1.39,2.66,2a68.68,68.68,0,0,1-10.87,5.19,77,77,0,0,0,6.89,11.1A105.25,105.25,0,0,0,126.6,80.22h0C129.24,52.84,122.09,29.11,107.7,8.07ZM42.45,65.69C36.18,65.69,31,60,31,53s5-12.74,11.43-12.74S54,46,53.89,53,48.84,65.69,42.45,65.69Zm42.24,0C78.41,65.69,73.25,60,73.25,53s5-12.74,11.44-12.74S96.23,46,96.12,53,91.08,65.69,84.69,65.69Z"/>
                    </svg>
                </a>
                <a href="<?php echo esc_url(get_feed_link()); ?>" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="RSS">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 11a9 9 0 0 1 9 9"/>
                        <path d="M4 4a16 16 0 0 1 16 16"/>
                        <circle cx="5" cy="19" r="1"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</header>

<div id="content" class="site-content">