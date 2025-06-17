<?php
/**
 * Site Navigation Component
 * 
 * @package ethanede
 */
?>

<nav class="site-nav">
    <div class="container">
        <div class="nav-content">
            <h1 class="nav-title">
                <a href="<?php echo esc_url(home_url('/')); ?>">Ethan Ede</a>
            </h1>
            
            <button class="hamburger" aria-label="Toggle mobile menu">
                <span class="bar top"></span>
                <span class="bar middle"></span>
                <span class="bar bottom"></span>
            </button>

            <?php
            wp_nav_menu([
                'theme_location' => 'main_navigation',
                'menu_class' => 'nav-links',
                'container' => false,
                'walker' => new WP_Bootstrap_Navwalker(),
            ]);
            ?>
        </div>
    </div>
</nav>

<!-- Mobile Menu Overlay -->
<div class="mobile-menu">
    <div class="mobile-nav-links">
        <?php
        wp_nav_menu([
            'theme_location' => 'main_navigation',
            'menu_class' => '',
            'container' => false,
            'walker' => new WP_Bootstrap_Navwalker(),
        ]);
        ?>
    </div>
</div> 