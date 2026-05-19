<?php
/**
 * Theme Name: MUSICYOS
 * Theme URI: https://github.com/TKIFF2026/MUSIC
 * Description: Thème WordPress professionnel pour compositeurs et producteurs musicaux
 * Version: 1.0.0
 * Author: TKIFF2026
 * Author URI: https://github.com/TKIFF2026
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: musicyos
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// ============================================================================
// 1. SETUP - Configuration de Base
// ============================================================================

function musicyos_setup() {
    // Support du thème
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    add_theme_support('responsive-embeds');
    
    // Menus
    register_nav_menus(array(
        'primary' => __('Menu Principal', 'musicyos'),
        'footer' => __('Menu Pied de Page', 'musicyos'),
    ));
    
    // Localisation
    load_theme_textdomain('musicyos', get_template_directory() . '/languages');
}
add_action('after_setup_theme', 'musicyos_setup');

// ============================================================================
// 2. CUSTOM POST TYPES (CPT)
// ============================================================================

function musicyos_register_post_types() {
    // Compositions
    register_post_type('composition', array(
        'labels' => array(
            'name' => __('Compositions', 'musicyos'),
            'singular_name' => __('Composition', 'musicyos'),
        ),
        'public' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'menu_icon' => 'dashicons-music',
        'has_archive' => true,
        'rewrite' => array('slug' => 'compositions'),
    ));
    
    // Projets
    register_post_type('projet', array(
        'labels' => array(
            'name' => __('Projets', 'musicyos'),
            'singular_name' => __('Projet', 'musicyos'),
        ),
        'public' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'menu_icon' => 'dashicons-briefcase',
        'has_archive' => true,
        'rewrite' => array('slug' => 'projets'),
    ));
    
    // Services
    register_post_type('service', array(
        'labels' => array(
            'name' => __('Services', 'musicyos'),
            'singular_name' => __('Service', 'musicyos'),
        ),
        'public' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'menu_icon' => 'dashicons-hammer',
        'has_archive' => true,
        'rewrite' => array('slug' => 'services'),
    ));
}
add_action('init', 'musicyos_register_post_types');

// ============================================================================
// 3. TAXONOMIES
// ============================================================================

function musicyos_register_taxonomies() {
    // Styles Musicaux
    register_taxonomy('style_musical', array('composition', 'projet'), array(
        'labels' => array(
            'name' => __('Styles Musicaux', 'musicyos'),
            'singular_name' => __('Style Musical', 'musicyos'),
        ),
        'public' => true,
        'hierarchical' => true,
        'rewrite' => array('slug' => 'style-musical'),
    ));
    
    // Instruments
    register_taxonomy('instrument', array('composition', 'projet'), array(
        'labels' => array(
            'name' => __('Instruments', 'musicyos'),
            'singular_name' => __('Instrument', 'musicyos'),
        ),
        'public' => true,
        'hierarchical' => true,
        'rewrite' => array('slug' => 'instrument'),
    ));
    
    // Genres
    register_taxonomy('genre', array('composition', 'projet'), array(
        'labels' => array(
            'name' => __('Genres', 'musicyos'),
            'singular_name' => __('Genre', 'musicyos'),
        ),
        'public' => true,
        'hierarchical' => true,
        'rewrite' => array('slug' => 'genre'),
    ));
}
add_action('init', 'musicyos_register_taxonomies');

// ============================================================================
// 4. META BOXES (Champs Personnalisés)
// ============================================================================

function musicyos_add_meta_boxes() {
    // Composition Meta Box
    add_meta_box(
        'composition_details',
        __('Détails de la Composition', 'musicyos'),
        'musicyos_composition_meta_callback',
        'composition',
        'normal',
        'high'
    );
    
    // Projet Meta Box
    add_meta_box(
        'projet_details',
        __('Détails du Projet', 'musicyos'),
        'musicyos_projet_meta_callback',
        'projet',
        'normal',
        'high'
    );
    
    // Service Meta Box
    add_meta_box(
        'service_details',
        __('Détails du Service', 'musicyos'),
        'musicyos_service_meta_callback',
        'service',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'musicyos_add_meta_boxes');

function musicyos_composition_meta_callback($post) {
    wp_nonce_field('musicyos_composition_meta', 'musicyos_composition_nonce');
    
    $duration = get_post_meta($post->ID, '_composition_duration', true);
    $tempo = get_post_meta($post->ID, '_composition_tempo', true);
    $key = get_post_meta($post->ID, '_composition_key', true);
    $audio_url = get_post_meta($post->ID, '_composition_audio', true);
    
    ?>
    <div style="padding: 10px; background: rgba(0,212,255,0.05); border-radius: 8px;">
        <p>
            <label for="composition_duration"><strong><?php _e('Durée (secondes)', 'musicyos'); ?></strong></label><br>
            <input type="number" id="composition_duration" name="composition_duration" value="<?php echo esc_attr($duration); ?>" min="1" style="width: 100%; padding: 8px; margin-top: 5px;">
        </p>
        
        <p>
            <label for="composition_tempo"><strong><?php _e('Tempo (BPM)', 'musicyos'); ?></strong></label><br>
            <input type="number" id="composition_tempo" name="composition_tempo" value="<?php echo esc_attr($tempo); ?>" min="1" max="300" style="width: 100%; padding: 8px; margin-top: 5px;">
        </p>
        
        <p>
            <label for="composition_key"><strong><?php _e('Clé Musicale', 'musicyos'); ?></strong></label><br>
            <input type="text" id="composition_key" name="composition_key" value="<?php echo esc_attr($key); ?>" placeholder="C Major, D Minor, etc." style="width: 100%; padding: 8px; margin-top: 5px;">
        </p>
        
        <p>
            <label for="composition_audio"><strong><?php _e('URL Fichier Audio', 'musicyos'); ?></strong></label><br>
            <input type="url" id="composition_audio" name="composition_audio" value="<?php echo esc_attr($audio_url); ?>" placeholder="https://..." style="width: 100%; padding: 8px; margin-top: 5px;">
        </p>
    </div>
    <?php
}

function musicyos_projet_meta_callback($post) {
    wp_nonce_field('musicyos_projet_meta', 'musicyos_projet_nonce');
    
    $client = get_post_meta($post->ID, '_projet_client', true);
    $end_date = get_post_meta($post->ID, '_projet_end_date', true);
    $budget = get_post_meta($post->ID, '_projet_budget', true);
    $status = get_post_meta($post->ID, '_projet_status', true);
    
    ?>
    <div style="padding: 10px; background: rgba(0,212,255,0.05); border-radius: 8px;">
        <p>
            <label for="projet_client"><strong><?php _e('Client', 'musicyos'); ?></strong></label><br>
            <input type="text" id="projet_client" name="projet_client" value="<?php echo esc_attr($client); ?>" style="width: 100%; padding: 8px; margin-top: 5px;">
        </p>
        
        <p>
            <label for="projet_end_date"><strong><?php _e('Date de Fin', 'musicyos'); ?></strong></label><br>
            <input type="date" id="projet_end_date" name="projet_end_date" value="<?php echo esc_attr($end_date); ?>" style="width: 100%; padding: 8px; margin-top: 5px;">
        </p>
        
        <p>
            <label for="projet_budget"><strong><?php _e('Budget (€)', 'musicyos'); ?></strong></label><br>
            <input type="number" id="projet_budget" name="projet_budget" value="<?php echo esc_attr($budget); ?>" min="0" step="0.01" style="width: 100%; padding: 8px; margin-top: 5px;">
        </p>
        
        <p>
            <label for="projet_status"><strong><?php _e('Statut', 'musicyos'); ?></strong></label><br>
            <select id="projet_status" name="projet_status" style="width: 100%; padding: 8px; margin-top: 5px;">
                <option value="en_cours" <?php selected($status, 'en_cours'); ?>>En cours</option>
                <option value="termine" <?php selected($status, 'termine'); ?>>Terminé</option>
                <option value="en_attente" <?php selected($status, 'en_attente'); ?>>En attente</option>
                <option value="annule" <?php selected($status, 'annule'); ?>>Annulé</option>
            </select>
        </p>
    </div>
    <?php
}

function musicyos_service_meta_callback($post) {
    wp_nonce_field('musicyos_service_meta', 'musicyos_service_nonce');
    
    $price = get_post_meta($post->ID, '_service_price', true);
    $emoji = get_post_meta($post->ID, '_service_emoji', true);
    
    ?>
    <div style="padding: 10px; background: rgba(0,212,255,0.05); border-radius: 8px;">
        <p>
            <label for="service_price"><strong><?php _e('Prix (€)', 'musicyos'); ?></strong></label><br>
            <input type="number" id="service_price" name="service_price" value="<?php echo esc_attr($price); ?>" min="0" step="0.01" style="width: 100%; padding: 8px; margin-top: 5px;">
        </p>
        
        <p>
            <label for="service_emoji"><strong><?php _e('Emoji/Icône', 'musicyos'); ?></strong></label><br>
            <input type="text" id="service_emoji" name="service_emoji" value="<?php echo esc_attr($emoji); ?>" placeholder="🎼" maxlength="5" style="width: 100%; padding: 8px; margin-top: 5px; font-size: 20px;">
        </p>
    </div>
    <?php
}

// Sauvegarder les meta boxes
function musicyos_save_meta_boxes($post_id) {
    if (!isset($_POST['musicyos_composition_nonce']) && !isset($_POST['musicyos_projet_nonce']) && !isset($_POST['musicyos_service_nonce'])) {
        return;
    }
    
    // Composition
    if (isset($_POST['composition_duration'])) {
        update_post_meta($post_id, '_composition_duration', sanitize_text_field($_POST['composition_duration']));
    }
    if (isset($_POST['composition_tempo'])) {
        update_post_meta($post_id, '_composition_tempo', sanitize_text_field($_POST['composition_tempo']));
    }
    if (isset($_POST['composition_key'])) {
        update_post_meta($post_id, '_composition_key', sanitize_text_field($_POST['composition_key']));
    }
    if (isset($_POST['composition_audio'])) {
        update_post_meta($post_id, '_composition_audio', esc_url($_POST['composition_audio']));
    }
    
    // Projet
    if (isset($_POST['projet_client'])) {
        update_post_meta($post_id, '_projet_client', sanitize_text_field($_POST['projet_client']));
    }
    if (isset($_POST['projet_end_date'])) {
        update_post_meta($post_id, '_projet_end_date', sanitize_text_field($_POST['projet_end_date']));
    }
    if (isset($_POST['projet_budget'])) {
        update_post_meta($post_id, '_projet_budget', sanitize_text_field($_POST['projet_budget']));
    }
    if (isset($_POST['projet_status'])) {
        update_post_meta($post_id, '_projet_status', sanitize_text_field($_POST['projet_status']));
    }
    
    // Service
    if (isset($_POST['service_price'])) {
        update_post_meta($post_id, '_service_price', sanitize_text_field($_POST['service_price']));
    }
    if (isset($_POST['service_emoji'])) {
        update_post_meta($post_id, '_service_emoji', sanitize_text_field($_POST['service_emoji']));
    }
}
add_action('save_post', 'musicyos_save_meta_boxes');

// ============================================================================
// 5. ENQUEUE SCRIPTS & STYLES
// ============================================================================

function musicyos_enqueue_scripts() {
    // CSS
    wp_enqueue_style('musicyos-style', get_stylesheet_uri(), array(), '1.0.0', 'all');
    
    // Fonts
    wp_enqueue_style('musicyos-fonts', 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap', array(), null);
    
    // JS
    wp_enqueue_script('musicyos-main', get_template_directory_uri() . '/js/main.js', array(), '1.0.0', true);
    
    // Audio JS
    wp_enqueue_script('musicyos-visualizer', get_template_directory_uri() . '/js/visualizer.js', array(), '1.0.0', true);
    wp_enqueue_script('musicyos-mixer', get_template_directory_uri() . '/js/mixer.js', array(), '1.0.0', true);
    
    // Todo JS
    wp_enqueue_script('musicyos-todo', get_template_directory_uri() . '/js/todo.js', array(), '1.0.0', true);
    
    // Localize
    wp_localize_script('musicyos-main', 'musicyos_vars', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('musicyos_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'musicyos_enqueue_scripts');

// ============================================================================
// 6. SHORTCODES
// ============================================================================

// [musicyos_mixer]
function musicyos_mixer_shortcode($atts) {
    ob_start();
    ?>
    <div id="mixer-container" class="musicyos-mixer" style="background: rgba(22, 33, 62, 0.8); border: 1px solid rgba(0, 212, 255, 0.2); border-radius: 12px; padding: 30px; margin: 30px 0;">
        <h3 style="color: #00d4ff; margin-bottom: 20px; text-align: center; font-size: 1.5em;">🎛️ Console de Mixage</h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(80px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <!-- Potentiomètres -->
            <div style="text-align: center;">
                <div style="width: 80px; height: 80px; border-radius: 50%; background: conic-gradient(from 0deg, #00d4ff, #ff006e, #8338ec); display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; cursor: pointer;">
                    <div style="width: 70px; height: 70px; border-radius: 50%; background: #0a0e27; display: flex; align-items: center; justify-content: center; color: #00d4ff;">🔊</div>
                </div>
                <p style="font-size: 0.9em; color: #a0a0a0;">Volume</p>
            </div>
            <div style="text-align: center;">
                <div style="width: 80px; height: 80px; border-radius: 50%; background: conic-gradient(from 0deg, #ff006e, #8338ec, #00d4ff); display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; cursor: pointer;">
                    <div style="width: 70px; height: 70px; border-radius: 50%; background: #0a0e27; display: flex; align-items: center; justify-content: center; color: #ff006e;">✨</div>
                </div>
                <p style="font-size: 0.9em; color: #a0a0a0;">Reverb</p>
            </div>
            <div style="text-align: center;">
                <div style="width: 80px; height: 80px; border-radius: 50%; background: conic-gradient(from 0deg, #8338ec, #00d4ff, #ff006e); display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; cursor: pointer;">
                    <div style="width: 70px; height: 70px; border-radius: 50%; background: #0a0e27; display: flex; align-items: center; justify-content: center; color: #8338ec;">⏱️</div>
                </div>
                <p style="font-size: 0.9em; color: #a0a0a0;">Delay</p>
            </div>
        </div>
        
        <p style="color: #a0a0a0; text-align: center; font-size: 0.9em;">🎵 Console interactive - Cliquez sur les potentiomètres pour contrôler</p>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('musicyos_mixer', 'musicyos_mixer_shortcode');

// [musicyos_compositions]
function musicyos_compositions_shortcode($atts) {
    $atts = shortcode_atts(array(
        'posts_per_page' => 6,
        'orderby' => 'date',
        'order' => 'DESC',
    ), $atts);
    
    $args = array(
        'post_type' => 'composition',
        'posts_per_page' => intval($atts['posts_per_page']),
        'orderby' => $atts['orderby'],
        'order' => $atts['order'],
    );
    
    $query = new WP_Query($args);
    
    ob_start();
    
    if ($query->have_posts()) {
        echo '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin: 30px 0;">';
        
        while ($query->have_posts()) {
            $query->the_post();
            $duration = get_post_meta(get_the_ID(), '_composition_duration', true);
            $tempo = get_post_meta(get_the_ID(), '_composition_tempo', true);
            
            echo '<div style="background: rgba(22, 33, 62, 0.6); border: 1px solid rgba(0, 212, 255, 0.2); border-radius: 10px; overflow: hidden; transition: all 0.3s ease; cursor: pointer;">';
            
            if (has_post_thumbnail()) {
                echo '<div style="position: relative; overflow: hidden; height: 180px;">';
                the_post_thumbnail('medium', array('style' => 'width: 100%; height: 100%; object-fit: cover;'));
                echo '</div>';
            }
            
            echo '<div style="padding: 15px;">';
            echo '<h3 style="color: #00d4ff; margin-bottom: 10px;">' . get_the_title() . '</h3>';
            
            if ($tempo) {
                echo '<p style="color: #a0a0a0; font-size: 0.9em;">♩ ' . esc_html($tempo) . ' BPM</p>';
            }
            
            echo '<a href="' . get_permalink() . '" style="display: inline-block; margin-top: 10px; color: #ff006e; text-decoration: none; font-weight: 600;">Écouter →</a>';
            echo '</div>';
            echo '</div>';
        }
        
        echo '</div>';
        wp_reset_postdata();
    } else {
        echo '<p style="color: #a0a0a0; text-align: center;">Aucune composition trouvée.</p>';
    }
    
    return ob_get_clean();
}
add_shortcode('musicyos_compositions', 'musicyos_compositions_shortcode');

// [musicyos_services]
function musicyos_services_shortcode($atts) {
    $atts = shortcode_atts(array(
        'posts_per_page' => 3,
    ), $atts);
    
    $args = array(
        'post_type' => 'service',
        'posts_per_page' => intval($atts['posts_per_page']),
    );
    
    $query = new WP_Query($args);
    
    ob_start();
    
    if ($query->have_posts()) {
        echo '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin: 30px 0;">';
        
        while ($query->have_posts()) {
            $query->the_post();
            $price = get_post_meta(get_the_ID(), '_service_price', true);
            $emoji = get_post_meta(get_the_ID(), '_service_emoji', true);
            
            echo '<div style="background: rgba(22, 33, 62, 0.6); border: 1px solid rgba(255, 0, 110, 0.2); border-radius: 10px; padding: 20px; text-align: center; transition: all 0.3s ease;">';
            echo '<div style="font-size: 2.5em; margin-bottom: 10px;">' . esc_html($emoji) . '</div>';
            echo '<h3 style="color: #ff006e; margin-bottom: 10px;">' . get_the_title() . '</h3>';
            echo '<p style="color: #a0a0a0; font-size: 0.9em; margin-bottom: 15px;">' . wp_trim_words(get_the_excerpt(), 15) . '</p>';
            
            if ($price) {
                echo '<p style="color: #00d4ff; font-weight: 700; font-size: 1.3em; margin-bottom: 15px;">' . number_format($price, 2, ',', ' ') . ' €</p>';
            }
            
            echo '<a href="' . get_permalink() . '" style="display: inline-block; padding: 10px 20px; background: linear-gradient(135deg, #00d4ff, #8338ec); color: #000; text-decoration: none; border-radius: 6px; font-weight: 600; transition: all 0.3s ease;">En savoir plus</a>';
            echo '</div>';
        }
        
        echo '</div>';
        wp_reset_postdata();
    } else {
        echo '<p style="color: #a0a0a0; text-align: center;">Aucun service trouvé.</p>';
    }
    
    return ob_get_clean();
}
add_shortcode('musicyos_services', 'musicyos_services_shortcode');

// [musicyos_todo]
function musicyos_todo_shortcode() {
    ob_start();
    ?>
    <div id="todo-app-shortcode" style="background: rgba(22, 33, 62, 0.6); border: 1px solid rgba(0, 212, 255, 0.2); border-radius: 12px; padding: 30px; margin: 30px 0;">
        <h3 style="color: #00d4ff; margin-bottom: 20px; text-align: center;">📋 Ma Liste de Tâches</h3>
        <p style="color: #a0a0a0; text-align: center; font-size: 0.9em;">Pour une meilleure expérience, consultez la <a href="<?php echo home_url('/todo/'); ?>" style="color: #00d4ff;">page dédiée</a></p>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('musicyos_todo', 'musicyos_todo_shortcode');

// ============================================================================
// 7. HELPERS
// ============================================================================

function musicyos_get_logo() {
    if (has_custom_logo()) {
        return get_custom_logo();
    }
    return '<h1 style="color: #00d4ff; font-size: 1.8em; letter-spacing: 2px;">🎵 MUSICYOS</h1>';
}

function musicyos_get_primary_menu() {
    return wp_nav_menu(array(
        'theme_location' => 'primary',
        'fallback_cb' => 'wp_page_menu',
        'depth' => 2,
        'echo' => false,
    ));
}

function musicyos_get_footer_menu() {
    return wp_nav_menu(array(
        'theme_location' => 'footer',
        'fallback_cb' => false,
        'echo' => false,
    ));
}
