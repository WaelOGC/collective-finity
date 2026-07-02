<?php
/**
 * Theme Options admin page.
 *
 * @package Collective_Finity
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function collective_finity_theme_options_key() {
    return 'cf_theme_options';
}

function collective_finity_default_theme_options() {
    return array(
        'primary_color'      => '#FFB700',
        'accent_color'       => '#0D0D0D',
        'enable_preloader'   => 0,
        'enable_back_to_top' => 1,
        'show_global_player' => 1,
        'default_volume'     => 72,
        'footer_copyright'   => '',
        'footer_tagline'     => 'Experience Music Beyond Imagination',
        'footer_description' => 'Welcome to Collective Finity — a cinematic world where emotional sound, visual stories and creativity connect in one immersive universe.',
        'social_instagram'   => '',
        'social_youtube'     => '',
        'social_spotify'     => '',
        'social_facebook'    => '',
        'social_x'           => '',
        'custom_css'         => '',
        'ad_preview_mode'    => 0,
        'ad_zones'           => collective_finity_default_ad_zones(),
    );
}

function collective_finity_get_theme_options() {
    $saved    = get_option( collective_finity_theme_options_key(), array() );
    if ( ! is_array( $saved ) ) {
        $saved = array();
    }
    $defaults = collective_finity_default_theme_options();
    $merged   = wp_parse_args( $saved, $defaults );

    if ( isset( $defaults['ad_zones'] ) ) {
        $merged['ad_zones'] = isset( $saved['ad_zones'] ) && is_array( $saved['ad_zones'] ) ? $saved['ad_zones'] : array();
        $merged['ad_zones'] = wp_parse_args( $merged['ad_zones'], $defaults['ad_zones'] );
        foreach ( $defaults['ad_zones'] as $zone_id => $zone_defaults ) {
            $merged['ad_zones'][ $zone_id ] = wp_parse_args( $merged['ad_zones'][ $zone_id ] ?? array(), $zone_defaults );
        }
    }

    return $merged;
}

function collective_finity_get_theme_option( $key, $default = null ) {
    $options = collective_finity_get_theme_options();
    if ( array_key_exists( $key, $options ) ) {
        return $options[ $key ];
    }
    if ( null !== $default ) {
        return $default;
    }
    $defaults = collective_finity_default_theme_options();
    return isset( $defaults[ $key ] ) ? $defaults[ $key ] : null;
}


function collective_finity_register_theme_options_settings() {
    register_setting(
        'collective_finity_theme_options_group',
        collective_finity_theme_options_key(),
        array(
            'type'              => 'array',
            'sanitize_callback' => 'collective_finity_sanitize_theme_options',
            'default'           => collective_finity_default_theme_options(),
        )
    );
}
add_action( 'admin_init', 'collective_finity_register_theme_options_settings' );

function collective_finity_sanitize_theme_options( $input ) {
    $defaults = collective_finity_default_theme_options();
    $output   = collective_finity_get_theme_options();

    if ( ! is_array( $input ) ) {
        return $output;
    }

    $output['primary_color']      = sanitize_hex_color( $input['primary_color'] ?? $defaults['primary_color'] ) ?: $defaults['primary_color'];
    $output['accent_color']       = sanitize_hex_color( $input['accent_color'] ?? $defaults['accent_color'] ) ?: $defaults['accent_color'];
    $output['enable_preloader']   = empty( $input['enable_preloader'] ) ? 0 : 1;
    $output['enable_back_to_top'] = empty( $input['enable_back_to_top'] ) ? 0 : 1;
    $output['show_global_player'] = empty( $input['show_global_player'] ) ? 0 : 1;
    $output['default_volume']     = min( 100, max( 0, absint( $input['default_volume'] ?? $defaults['default_volume'] ) ) );
    $output['footer_copyright']   = sanitize_text_field( $input['footer_copyright'] ?? '' );
    $output['footer_tagline']     = sanitize_text_field( $input['footer_tagline'] ?? $defaults['footer_tagline'] );
    $desc                         = sanitize_text_field( $input['footer_description'] ?? '' );
    $output['footer_description'] = mb_substr( $desc ?: $defaults['footer_description'], 0, 140 );
    $social_fields                = array( 'social_instagram', 'social_youtube', 'social_spotify', 'social_facebook', 'social_x' );
    foreach ( $social_fields as $field ) {
        $output[ $field ] = esc_url_raw( $input[ $field ] ?? '' );
    }
    $output['custom_css']         = wp_strip_all_tags( $input['custom_css'] ?? '' );

    $output['ad_preview_mode'] = empty( $input['ad_preview_mode'] ) ? 0 : 1;

    $default_zones = collective_finity_default_ad_zones();
    $input_zones   = isset( $input['ad_zones'] ) && is_array( $input['ad_zones'] ) ? $input['ad_zones'] : array();
    $output['ad_zones'] = $output['ad_zones'] ?? $default_zones;

    foreach ( $default_zones as $zone_id => $zone_defaults ) {
        $zone_input = isset( $input_zones[ $zone_id ] ) && is_array( $input_zones[ $zone_id ] ) ? $input_zones[ $zone_id ] : array();

        $output['ad_zones'][ $zone_id ]['enabled'] = empty( $zone_input['enabled'] ) ? 0 : 1;

        if ( current_user_can( 'unfiltered_html' ) ) {
            $output['ad_zones'][ $zone_id ]['code'] = isset( $zone_input['code'] ) ? $zone_input['code'] : '';
        } else {
            $output['ad_zones'][ $zone_id ]['code'] = wp_kses_post( $zone_input['code'] ?? '' );
        }

        if ( isset( $zone_defaults['frequency'] ) ) {
            $output['ad_zones'][ $zone_id ]['frequency'] = min( 50, max( 2, absint( $zone_input['frequency'] ?? $zone_defaults['frequency'] ) ) );
        }
    }

    if ( isset( $input['active_header'] ) ) {
        collective_finity_set_theme_part_template_id( 'header', absint( $input['active_header'] ) );
    }
    if ( isset( $input['active_footer'] ) ) {
        collective_finity_set_theme_part_template_id( 'footer', absint( $input['active_footer'] ) );
    }
    if ( isset( $input['active_sidebar'] ) ) {
        collective_finity_set_theme_part_template_id( 'sidebar', absint( $input['active_sidebar'] ) );
    }

    return $output;
}

function collective_finity_theme_options_tabs() {
    return array(
        'general'  => __( 'General', 'collective-finity' ),
        'header'   => __( 'Header Settings', 'collective-finity' ),
        'footer'   => __( 'Footer Settings', 'collective-finity' ),
        'sidebar'  => __( 'Side Panel Settings', 'collective-finity' ),
        'player'   => __( 'Music Player', 'collective-finity' ),
        'ads'      => __( 'Ad Manager', 'collective-finity' ),
        'advanced' => __( 'Advanced', 'collective-finity' ),
    );
}

function collective_finity_theme_options_assets( $hook ) {
    if ( 'collective-finity_page_collective-finity-options' !== $hook && 'toplevel_page_collective-finity-options' !== $hook ) {
        return;
    }
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' );
    wp_enqueue_style( 'cf-theme-options-admin', get_template_directory_uri() . '/assets/css/theme-options-admin.css', array(), wp_get_theme()->get( 'Version' ) );
    wp_enqueue_script( 'cf-theme-options-admin', get_template_directory_uri() . '/assets/js/theme-options-admin.js', array( 'jquery', 'wp-color-picker' ), wp_get_theme()->get( 'Version' ), true );
}
add_action( 'admin_enqueue_scripts', 'collective_finity_theme_options_assets' );

function collective_finity_render_theme_options_page() {
    if ( ! current_user_can( 'edit_theme_options' ) ) {
        return;
    }

    $options    = collective_finity_get_theme_options();
    $tabs       = collective_finity_theme_options_tabs();
    $active_tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'general';
    if ( ! isset( $tabs[ $active_tab ] ) ) {
        $active_tab = 'general';
    }
    ?>
    <div class="wrap cf-theme-options-wrap cf-branded-admin-wrap">
        <div class="cf-admin-page-header">
            <h1><?php esc_html_e( 'Theme Options', 'collective-finity' ); ?></h1>
            <p class="description"><?php esc_html_e( 'Control your Collective Finity theme from one place.', 'collective-finity' ); ?></p>
        </div>

        <nav class="nav-tab-wrapper cf-theme-options-tabs">
            <?php foreach ( $tabs as $slug => $label ) : ?>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=collective-finity-options&tab=' . $slug ) ); ?>" class="nav-tab <?php echo $active_tab === $slug ? 'nav-tab-active' : ''; ?>"><?php echo esc_html( $label ); ?></a>
            <?php endforeach; ?>
        </nav>

        <form method="post" action="options.php" class="cf-theme-options-form">
            <?php settings_fields( 'collective_finity_theme_options_group' ); ?>
            <div class="cf-theme-options-panel">
                <?php
                switch ( $active_tab ) {
                    case 'header':
                        collective_finity_render_theme_options_part_tab( 'header' );
                        break;
                    case 'footer':
                        collective_finity_render_theme_options_footer_content_tab( $options );
                        collective_finity_render_theme_options_part_tab( 'footer' );
                        break;
                    case 'sidebar':
                        collective_finity_render_theme_options_part_tab( 'sidebar' );
                        break;
                    case 'player':
                        collective_finity_render_theme_options_player_tab( $options );
                        break;
                    case 'ads':
                        collective_finity_render_theme_options_ads_tab( $options );
                        break;
                    case 'advanced':
                        collective_finity_render_theme_options_advanced_tab( $options );
                        break;
                    default:
                        collective_finity_render_theme_options_general_tab( $options );
                        break;
                }
                ?>
            </div>
            <?php if ( ! in_array( $active_tab, array( 'header', 'footer', 'sidebar' ), true ) ) : ?>
                <?php submit_button( __( 'Save Changes', 'collective-finity' ) ); ?>
            <?php endif; ?>
        </form>
    </div>
    <?php
}

function collective_finity_render_theme_options_general_tab( $options ) {
    ?>
    <h2><?php esc_html_e( 'General Settings', 'collective-finity' ); ?></h2>
    <table class="form-table" role="presentation">
        <tr>
            <th scope="row"><label for="cf_primary_color"><?php esc_html_e( 'Primary Color', 'collective-finity' ); ?></label></th>
            <td><input type="text" class="cf-color-field" id="cf_primary_color" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[primary_color]" value="<?php echo esc_attr( $options['primary_color'] ); ?>" data-default-color="#FFB700"></td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_accent_color"><?php esc_html_e( 'Dark Accent Color', 'collective-finity' ); ?></label></th>
            <td><input type="text" class="cf-color-field" id="cf_accent_color" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[accent_color]" value="<?php echo esc_attr( $options['accent_color'] ); ?>" data-default-color="#0D0D0D"></td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e( 'Preloader', 'collective-finity' ); ?></th>
            <td><label><input type="checkbox" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[enable_preloader]" value="1" <?php checked( $options['enable_preloader'], 1 ); ?>> <?php esc_html_e( 'Enable site preloader', 'collective-finity' ); ?></label></td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e( 'Back to Top', 'collective-finity' ); ?></th>
            <td><label><input type="checkbox" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[enable_back_to_top]" value="1" <?php checked( $options['enable_back_to_top'], 1 ); ?>> <?php esc_html_e( 'Show back-to-top button', 'collective-finity' ); ?></label></td>
        </tr>
        <tr>
            <th scope="row"><?php esc_html_e( 'Customizer', 'collective-finity' ); ?></th>
            <td><a class="button" href="<?php echo esc_url( admin_url( 'customize.php?autofocus[panel]=cf_theme_parts' ) ); ?>"><?php esc_html_e( 'Open Theme Parts in Customizer', 'collective-finity' ); ?></a></td>
        </tr>
    </table>
    <?php
}

function collective_finity_render_theme_options_footer_content_tab( $options ) {
    ?>
    <h2><?php esc_html_e( 'Footer Branding', 'collective-finity' ); ?></h2>
    <p class="description"><?php esc_html_e( 'Logo uses your site logo from Customizer. Description is limited to 140 characters.', 'collective-finity' ); ?></p>
    <table class="form-table" role="presentation">
        <tr>
            <th scope="row"><label for="cf_footer_tagline"><?php esc_html_e( 'Tagline', 'collective-finity' ); ?></label></th>
            <td><input type="text" class="regular-text" id="cf_footer_tagline" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[footer_tagline]" value="<?php echo esc_attr( $options['footer_tagline'] ); ?>"></td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_footer_description"><?php esc_html_e( 'Description', 'collective-finity' ); ?></label></th>
            <td>
                <textarea id="cf_footer_description" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[footer_description]" rows="3" class="large-text" maxlength="140"><?php echo esc_textarea( $options['footer_description'] ); ?></textarea>
                <p class="description"><?php esc_html_e( 'Maximum 140 characters.', 'collective-finity' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_footer_copyright"><?php esc_html_e( 'Copyright Override', 'collective-finity' ); ?></label></th>
            <td><input type="text" class="regular-text" id="cf_footer_copyright" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[footer_copyright]" value="<?php echo esc_attr( $options['footer_copyright'] ); ?>" placeholder="<?php echo esc_attr( '© ' . gmdate( 'Y' ) . ' Collective Finity' ); ?>"></td>
        </tr>
    </table>
    <h2><?php esc_html_e( 'Social Media Links', 'collective-finity' ); ?></h2>
    <table class="form-table" role="presentation">
        <tr><th scope="row"><label for="cf_social_instagram">Instagram</label></th><td><input type="url" class="regular-text" id="cf_social_instagram" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[social_instagram]" value="<?php echo esc_url( $options['social_instagram'] ); ?>" placeholder="https://instagram.com/..."></td></tr>
        <tr><th scope="row"><label for="cf_social_youtube">YouTube</label></th><td><input type="url" class="regular-text" id="cf_social_youtube" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[social_youtube]" value="<?php echo esc_url( $options['social_youtube'] ); ?>" placeholder="https://youtube.com/..."></td></tr>
        <tr><th scope="row"><label for="cf_social_spotify">Spotify</label></th><td><input type="url" class="regular-text" id="cf_social_spotify" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[social_spotify]" value="<?php echo esc_url( $options['social_spotify'] ); ?>" placeholder="https://open.spotify.com/..."></td></tr>
        <tr><th scope="row"><label for="cf_social_facebook">Facebook</label></th><td><input type="url" class="regular-text" id="cf_social_facebook" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[social_facebook]" value="<?php echo esc_url( $options['social_facebook'] ); ?>" placeholder="https://facebook.com/..."></td></tr>
        <tr><th scope="row"><label for="cf_social_x">X (Twitter)</label></th><td><input type="url" class="regular-text" id="cf_social_x" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[social_x]" value="<?php echo esc_url( $options['social_x'] ); ?>" placeholder="https://x.com/..."></td></tr>
    </table>
    <p class="description"><?php esc_html_e( 'Assign footer menu links under Appearance → Menus → Footer Menu. Suggested sections: Explore, Community, Legal.', 'collective-finity' ); ?></p>
    <?php
}

function collective_finity_render_theme_options_part_tab( $part ) {
    $parts      = collective_finity_get_theme_parts();
    $config     = $parts[ $part ];
    $choices    = collective_finity_get_templates_for_part( $part );
    $current_id = collective_finity_get_theme_part_template_id( $part );
    $field_key  = 'active_' . $part;
    $tab_title  = isset( $config['settings_tab'] ) ? $config['settings_tab'] : $config['menu_name'];
    ?>
    <h2><?php echo esc_html( $tab_title ); ?></h2>
    <p class="description cf-settings-tab-help">
        <?php
        printf(
            /* translators: %s: layout menu name, e.g. Header Layout */
            esc_html__( 'These are simple toggles and template assignment. To edit the actual visual layout, go to Collective Finity → %s.', 'collective-finity' ),
            esc_html( $config['menu_name'] )
        );
        ?>
    </p>
    <p class="description"><?php printf( esc_html__( 'Create unlimited %s designs and edit them with Elementor.', 'collective-finity' ), esc_html( strtolower( $config['menu_name'] ) ) ); ?></p>
    <table class="form-table" role="presentation">
        <tr>
            <th scope="row"><label for="cf_<?php echo esc_attr( $part ); ?>_template"><?php esc_html_e( 'Active Template', 'collective-finity' ); ?></label></th>
            <td>
                <select name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[<?php echo esc_attr( $field_key ); ?>]" id="cf_<?php echo esc_attr( $part ); ?>_template">
                    <?php foreach ( $choices as $id => $label ) : ?>
                        <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $current_id, $id ); ?>><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </table>
    <p class="cf-theme-options-actions">
        <a class="button button-primary" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=' . $config['post_type'] ) ); ?>"><?php echo esc_html( $config['add_new'] ); ?></a>
        <a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=' . $config['post_type'] ) ); ?>"><?php echo esc_html( $config['all_items'] ); ?></a>
        <?php if ( $current_id ) : ?>
            <a class="button" href="<?php echo esc_url( collective_finity_elementor_edit_url( $current_id ) ); ?>"><?php esc_html_e( 'Edit Active with Elementor', 'collective-finity' ); ?></a>
        <?php endif; ?>
        <a class="button" href="<?php echo esc_url( collective_finity_customizer_part_url( $part ) ); ?>"><?php esc_html_e( 'Open in Customizer', 'collective-finity' ); ?></a>
    </p>
    <?php submit_button( __( 'Save Assignment', 'collective-finity' ) ); ?>
    <?php
}

function collective_finity_render_theme_options_player_tab( $options ) {
    ?>
    <h2><?php esc_html_e( 'Music Player', 'collective-finity' ); ?></h2>
    <table class="form-table" role="presentation">
        <tr>
            <th scope="row"><?php esc_html_e( 'Global Player', 'collective-finity' ); ?></th>
            <td><label><input type="checkbox" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[show_global_player]" value="1" <?php checked( $options['show_global_player'], 1 ); ?>> <?php esc_html_e( 'Show sticky global audio player', 'collective-finity' ); ?></label></td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_default_volume"><?php esc_html_e( 'Default Volume', 'collective-finity' ); ?></label></th>
            <td><input type="number" min="0" max="100" id="cf_default_volume" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[default_volume]" value="<?php echo esc_attr( $options['default_volume'] ); ?>"></td>
        </tr>
    </table>
    <?php
}

function collective_finity_render_theme_options_advanced_tab( $options ) {
    ?>
    <h2><?php esc_html_e( 'Advanced', 'collective-finity' ); ?></h2>
    <table class="form-table" role="presentation">
        <tr>
            <th scope="row"><label for="cf_footer_copyright"><?php esc_html_e( 'Footer Copyright Override', 'collective-finity' ); ?></label></th>
            <td><input type="text" class="regular-text" id="cf_footer_copyright" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[footer_copyright]" value="<?php echo esc_attr( $options['footer_copyright'] ); ?>"></td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_custom_css"><?php esc_html_e( 'Custom CSS', 'collective-finity' ); ?></label></th>
            <td><textarea id="cf_custom_css" name="<?php echo esc_attr( collective_finity_theme_options_key() ); ?>[custom_css]" rows="10" class="large-text code"><?php echo esc_textarea( $options['custom_css'] ); ?></textarea></td>
        </tr>
    </table>
    <?php
}

function collective_finity_render_theme_options_ads_tab( $options ) {
    $zones       = $options['ad_zones'] ?? collective_finity_default_ad_zones();
    $labels      = collective_finity_ad_zone_labels();
    $descriptions = collective_finity_ad_zone_descriptions();
    $option_key  = collective_finity_theme_options_key();
    ?>
    <div class="cf-ad-manager-tab">
        <div class="notice notice-warning cf-ad-manager-notice">
            <p><strong><?php esc_html_e( 'Automatic ad exclusions', 'collective-finity' ); ?></strong> — <?php esc_html_e( 'Ads are automatically disabled on the Home page, Privacy Policy, Terms of Service, and Contact page — this cannot be changed here, by design.', 'collective-finity' ); ?></p>
        </div>

        <h2><?php esc_html_e( 'Preview Mode', 'collective-finity' ); ?></h2>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><?php esc_html_e( 'Preview Mode', 'collective-finity' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="<?php echo esc_attr( $option_key ); ?>[ad_preview_mode]" value="1" <?php checked( ! empty( $options['ad_preview_mode'] ) ); ?>>
                        <?php esc_html_e( 'Show labeled placeholder boxes instead of real ad code on the frontend', 'collective-finity' ); ?>
                    </label>
                </td>
            </tr>
        </table>

        <h2><?php esc_html_e( 'Ad Zones', 'collective-finity' ); ?></h2>
        <p class="description"><?php esc_html_e( 'Only users with permission to edit theme options can save ad code. Scripts and HTML are stored as entered.', 'collective-finity' ); ?></p>

        <div class="cf-ad-zone-cards">
            <?php foreach ( collective_finity_default_ad_zones() as $zone_id => $zone_defaults ) :
                $zone = wp_parse_args( $zones[ $zone_id ] ?? array(), $zone_defaults );
                $label = $labels[ $zone_id ] ?? $zone_id;
                $desc  = $descriptions[ $zone_id ] ?? '';
                ?>
                <div class="cf-ad-zone-card">
                    <div class="cf-ad-zone-card__header">
                        <h3><?php echo esc_html( $label ); ?></h3>
                        <label class="cf-ad-zone-toggle">
                            <input type="checkbox" name="<?php echo esc_attr( $option_key ); ?>[ad_zones][<?php echo esc_attr( $zone_id ); ?>][enabled]" value="1" <?php checked( ! empty( $zone['enabled'] ) ); ?>>
                            <?php esc_html_e( 'Enabled', 'collective-finity' ); ?>
                        </label>
                    </div>
                    <?php if ( $desc ) : ?>
                        <p class="description"><?php echo esc_html( $desc ); ?></p>
                    <?php endif; ?>
                    <?php if ( isset( $zone_defaults['frequency'] ) ) : ?>
                        <p>
                            <label for="cf_ad_freq_<?php echo esc_attr( $zone_id ); ?>"><?php esc_html_e( 'Show every Nth card in archive grid', 'collective-finity' ); ?></label><br>
                            <input type="number" min="2" max="50" id="cf_ad_freq_<?php echo esc_attr( $zone_id ); ?>" name="<?php echo esc_attr( $option_key ); ?>[ad_zones][<?php echo esc_attr( $zone_id ); ?>][frequency]" value="<?php echo esc_attr( (string) ( $zone['frequency'] ?? 8 ) ); ?>" class="small-text">
                        </p>
                    <?php endif; ?>
                    <p>
                        <label for="cf_ad_code_<?php echo esc_attr( $zone_id ); ?>"><?php esc_html_e( 'Ad code (HTML / JavaScript)', 'collective-finity' ); ?></label>
                        <textarea id="cf_ad_code_<?php echo esc_attr( $zone_id ); ?>" name="<?php echo esc_attr( $option_key ); ?>[ad_zones][<?php echo esc_attr( $zone_id ); ?>][code]" rows="6" class="large-text code"><?php echo esc_textarea( $zone['code'] ?? '' ); ?></textarea>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

function collective_finity_output_theme_option_styles() {
    $options = collective_finity_get_theme_options();
    $css     = ':root{--primary-color:' . esc_attr( $options['primary_color'] ) . ';--secondary-color:' . esc_attr( $options['accent_color'] ) . ';}';
    if ( ! empty( $options['custom_css'] ) ) {
        $css .= "\n" . $options['custom_css'];
    }
    if ( ! $options['show_global_player'] ) {
        $css .= "\n#cf-global-audio-player{display:none!important;}body{padding-bottom:0!important;}";
    }
    wp_add_inline_style( 'main-style', $css );
}
add_action( 'wp_enqueue_scripts', 'collective_finity_output_theme_option_styles', 20 );

function collective_finity_render_back_to_top() {
    if ( ! collective_finity_get_theme_option( 'enable_back_to_top' ) ) {
        return;
    }
    echo '<button type="button" id="cf-back-to-top" class="cf-back-to-top" aria-label="' . esc_attr__( 'Back to top', 'collective-finity' ) . '">';
    echo '<svg class="cf-back-to-top-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 4l-7 7h4v9h6v-9h4z"/></svg>';
    echo '</button>';
}
add_action( 'wp_footer', 'collective_finity_render_back_to_top', 5 );

function collective_finity_theme_options_admin_bar( $wp_admin_bar ) {
    if ( ! current_user_can( 'edit_theme_options' ) ) {
        return;
    }
    $wp_admin_bar->add_node( array(
        'id'    => 'cf-theme-options',
        'title' => __( 'Collective Finity', 'collective-finity' ),
        'href'  => admin_url( 'admin.php?page=' . collective_finity_admin_menu_slug() ),
    ) );
    $wp_admin_bar->add_node( array(
        'id'     => 'cf-theme-options-settings',
        'parent' => 'cf-theme-options',
        'title'  => __( 'Theme Options', 'collective-finity' ),
        'href'   => admin_url( 'admin.php?page=collective-finity-options' ),
    ) );
}
add_action( 'admin_bar_menu', 'collective_finity_theme_options_admin_bar', 75 );
