<?php
/**
 * TEMP DIAGNOSTIC — Theme Options / Customizer save tracing.
 *
 * Remove this file (and its require_once in functions.php) after the
 * cf_theme_options / theme_mods save bug is identified.
 *
 * Enable with WP_DEBUG + WP_DEBUG_LOG in wp-config.php, then save Theme
 * Options or Customizer once and read wp-content/debug.log for lines
 * prefixed with [CF TEMP].
 *
 * Does not change save or sanitize behavior.
 *
 * @package Collective_Finity
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
    return;
}

/**
 * Request-scoped id so one save/refresh can be followed in debug.log.
 *
 * @return string
 */
function collective_finity_debug_rid() {
    static $rid = null;
    if ( null === $rid ) {
        $rid = substr( str_replace( '.', '', uniqid( 'cf', true ) ), 0, 16 );
    }
    return $rid;
}

/**
 * Compact snapshot of cf_theme_options (or any array) for logs.
 *
 * @param mixed $value Raw option value.
 * @return array<string, mixed>
 */
function collective_finity_debug_snapshot( $value ) {
    if ( ! is_array( $value ) ) {
        return array(
            '_type'  => gettype( $value ),
            '_value' => is_scalar( $value ) ? $value : wp_json_encode( $value ),
        );
    }

    $keys = array(
        'primary_color',
        'enable_glow_effects',
        'footer_copyright',
        'show_global_player',
        'custom_css',
        'body_font',
        '_submitted_tab',
    );

    $out = array(
        '_key_count' => count( $value ),
        '_keys'      => array_slice( array_keys( $value ), 0, 40 ),
    );

    foreach ( $keys as $key ) {
        if ( ! array_key_exists( $key, $value ) ) {
            continue;
        }
        $item = $value[ $key ];
        if ( is_string( $item ) && strlen( $item ) > 80 ) {
            $item = substr( $item, 0, 80 ) . '…';
        }
        $out[ $key ] = $item;
    }

    return $out;
}

/**
 * Short backtrace for identifying the caller.
 *
 * @param int $limit Frames to keep.
 * @return string
 */
function collective_finity_debug_trace( $limit = 8 ) {
    $frames = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, $limit + 3 );
    $out    = array();
    foreach ( $frames as $i => $frame ) {
        if ( $i < 2 ) {
            continue;
        }
        $file = isset( $frame['file'] ) ? basename( $frame['file'] ) : '?';
        $line = isset( $frame['line'] ) ? (string) $frame['line'] : '?';
        $fn   = ( isset( $frame['class'] ) ? $frame['class'] : '' )
            . ( isset( $frame['type'] ) ? $frame['type'] : '' )
            . ( isset( $frame['function'] ) ? $frame['function'] : '' );
        $out[] = $file . ':' . $line . ' ' . $fn;
        if ( count( $out ) >= $limit ) {
            break;
        }
    }
    return implode( ' <- ', $out );
}

/**
 * Write one diagnostic line.
 *
 * @param string $event Short event name.
 * @param array  $data  Extra context.
 */
function collective_finity_debug_log( $event, $data = array() ) {
    static $in_log = false;
    if ( $in_log ) {
        return;
    }
    $in_log = true;

    $payload = array_merge(
        array(
            'rid'    => collective_finity_debug_rid(),
            'event'  => $event,
            'hook'   => current_action() ? current_action() : current_filter(),
            'pagenow'=> isset( $GLOBALS['pagenow'] ) ? $GLOBALS['pagenow'] : '',
            'method' => isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : '',
        ),
        $data
    );

    error_log( '[CF TEMP] ' . wp_json_encode( $payload ) );
    $in_log = false;
}

/**
 * Whether an option name is one we are tracing.
 *
 * @param string $option Option name.
 * @return bool
 */
function collective_finity_debug_is_watched_option( $option ) {
    if ( 'cf_theme_options' === $option ) {
        return true;
    }
    if ( 0 === strpos( $option, 'theme_mods_' ) ) {
        return true;
    }
    return false;
}

/**
 * Compare get_option / cache vs a raw SQL row.
 *
 * @param string $label Probe label (hook:priority).
 */
function collective_finity_debug_probe( $label ) {
    static $in_probe = false;
    if ( $in_probe ) {
        return;
    }
    $in_probe = true;

    global $wpdb;

    $key = 'cf_theme_options';
    $row = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT option_id, autoload, LENGTH(option_value) AS bytes FROM {$wpdb->options} WHERE option_name = %s",
            $key
        ),
        ARRAY_A
    );

    $with_sentinel = get_option( $key, '__CF_MISSING__' );
    $without       = get_option( $key );

    $stylesheet = get_option( 'stylesheet' );
    $mods_key   = $stylesheet ? 'theme_mods_' . $stylesheet : '';
    $mods_row   = false;
    if ( $mods_key ) {
        $mods_row = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT LENGTH(option_value) FROM {$wpdb->options} WHERE option_name = %s",
                $mods_key
            )
        );
    }

    $mods = array(
        'cf_theme_part_header'  => get_theme_mod( 'cf_theme_part_header', '__CF_MISSING__' ),
        'cf_theme_part_footer'  => get_theme_mod( 'cf_theme_part_footer', '__CF_MISSING__' ),
        'cf_theme_part_sidebar' => get_theme_mod( 'cf_theme_part_sidebar', '__CF_MISSING__' ),
    );

    collective_finity_debug_log(
        'probe',
        array(
            'label'                 => $label,
            'db_row_exists'         => (bool) $row,
            'db_option_id'          => $row ? $row['option_id'] : null,
            'db_autoload'           => $row ? $row['autoload'] : null,
            'db_bytes'              => $row ? $row['bytes'] : null,
            'get_option_sentinel'   => ( '__CF_MISSING__' === $with_sentinel ) ? '__CF_MISSING__' : collective_finity_debug_snapshot( $with_sentinel ),
            'get_option_no_default' => ( false === $without ) ? false : collective_finity_debug_snapshot( $without ),
            'registered_setting'    => isset( get_registered_settings()[ $key ] ),
            'has_pre_option'        => (bool) has_filter( 'pre_option_cf_theme_options' ),
            'has_pre_update'        => (bool) has_filter( 'pre_update_option_cf_theme_options' ),
            'has_sanitize_option'   => (bool) has_filter( 'sanitize_option_cf_theme_options' ),
            'can_manage_options'    => function_exists( 'current_user_can' ) ? current_user_can( 'manage_options' ) : null,
            'can_edit_theme_opts'   => function_exists( 'current_user_can' ) ? current_user_can( 'edit_theme_options' ) : null,
            'theme_mods_db_bytes'   => $mods_row,
            'theme_mods'            => $mods,
        )
    );

    $in_probe = false;
}

add_action(
    'plugins_loaded',
    function () {
        collective_finity_debug_log(
            'request_start',
            array(
                'is_admin'       => is_admin(),
                'doing_ajax'     => function_exists( 'wp_doing_ajax' ) ? wp_doing_ajax() : false,
                'doing_cron'     => function_exists( 'wp_doing_cron' ) ? wp_doing_cron() : false,
                'page'           => isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '',
                'action'         => isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : '',
                'option_page'    => isset( $_POST['option_page'] ) ? sanitize_text_field( wp_unslash( $_POST['option_page'] ) ) : '',
                'post_has_cf'    => isset( $_POST['cf_theme_options'] ),
                'post_cf_keys'   => ( isset( $_POST['cf_theme_options'] ) && is_array( $_POST['cf_theme_options'] ) ) ? array_keys( wp_unslash( $_POST['cf_theme_options'] ) ) : array(),
                'post_tab'       => ( isset( $_POST['cf_theme_options']['_submitted_tab'] ) ) ? sanitize_key( wp_unslash( $_POST['cf_theme_options']['_submitted_tab'] ) ) : '',
                'post_primary'   => isset( $_POST['cf_theme_options']['primary_color'] ) ? sanitize_text_field( wp_unslash( $_POST['cf_theme_options']['primary_color'] ) ) : '',
                'customize_changeset' => isset( $_POST['customize_changeset_uuid'] ),
            )
        );
    },
    0
);

add_action( 'init', function () { collective_finity_debug_probe( 'init:0' ); }, 0 );
add_action( 'init', function () { collective_finity_debug_probe( 'init:999' ); }, 999 );
add_action( 'admin_init', function () { collective_finity_debug_probe( 'admin_init:0' ); }, 0 );
add_action( 'admin_init', function () { collective_finity_debug_probe( 'admin_init:999' ); }, 999 );
add_action( 'wp_loaded', function () { collective_finity_debug_probe( 'wp_loaded' ); }, 0 );

add_filter(
    'pre_option_cf_theme_options',
    function ( $pre ) {
        static $n = 0;
        $n++;
        if ( $n <= 20 || false !== $pre ) {
            collective_finity_debug_log(
                'pre_option_cf_theme_options',
                array(
                    'n'        => $n,
                    'pre'      => ( false === $pre ) ? false : collective_finity_debug_snapshot( $pre ),
                    'shortcircuit' => ( false !== $pre ),
                    'trace'    => collective_finity_debug_trace(),
                )
            );
        }
        return $pre;
    },
    0
);

add_filter(
    'default_option_cf_theme_options',
    function ( $default, $option, $passed_default ) {
        collective_finity_debug_log(
            'default_option_cf_theme_options',
            array(
                'passed_default' => $passed_default,
                'default'        => is_string( $default ) ? $default : collective_finity_debug_snapshot( $default ),
                'note'           => 'THIS FIRING MEANS THE OPTION ROW WAS NOT FOUND IN DB/CACHE',
                'trace'          => collective_finity_debug_trace(),
            )
        );
        return $default;
    },
    0,
    3
);

add_filter(
    'option_cf_theme_options',
    function ( $value ) {
        static $n = 0;
        $n++;
        if ( $n > 25 ) {
            return $value;
        }
        collective_finity_debug_log(
            'option_cf_theme_options',
            array(
                'n'     => $n,
                'value' => collective_finity_debug_snapshot( $value ),
                'trace' => collective_finity_debug_trace(),
            )
        );
        return $value;
    },
    0
);

add_filter(
    'sanitize_option_cf_theme_options',
    function ( $value, $option, $original ) {
        collective_finity_debug_log(
            'sanitize_option_BEFORE_registered_callback',
            array(
                'priority' => 0,
                'original' => collective_finity_debug_snapshot( $original ),
                'value'    => collective_finity_debug_snapshot( $value ),
                'trace'    => collective_finity_debug_trace(),
            )
        );
        return $value;
    },
    0,
    3
);

add_filter(
    'sanitize_option_cf_theme_options',
    function ( $value, $option, $original ) {
        collective_finity_debug_log(
            'sanitize_option_AFTER_registered_callback',
            array(
                'priority' => 999,
                'original' => collective_finity_debug_snapshot( $original ),
                'value'    => collective_finity_debug_snapshot( $value ),
                'tab_in_original' => ( is_array( $original ) && isset( $original['_submitted_tab'] ) ) ? $original['_submitted_tab'] : '',
                'tab_in_value'    => ( is_array( $value ) && isset( $value['_submitted_tab'] ) ) ? $value['_submitted_tab'] : '',
            )
        );
        return $value;
    },
    999,
    3
);

add_filter(
    'pre_update_option',
    function ( $value, $option, $old_value ) {
        if ( ! collective_finity_debug_is_watched_option( $option ) ) {
            return $value;
        }
        collective_finity_debug_log(
            'pre_update_option',
            array(
                'option'    => $option,
                'old'       => collective_finity_debug_snapshot( $old_value ),
                'new'       => collective_finity_debug_snapshot( $value ),
                'identical' => ( $value === $old_value ),
                'trace'     => collective_finity_debug_trace(),
            )
        );
        return $value;
    },
    10,
    3
);

add_filter(
    'pre_update_option_cf_theme_options',
    function ( $value, $old_value ) {
        collective_finity_debug_log(
            'pre_update_option_cf_theme_options',
            array(
                'old'       => collective_finity_debug_snapshot( $old_value ),
                'new'       => collective_finity_debug_snapshot( $value ),
                'identical' => ( $value === $old_value ),
                'trace'     => collective_finity_debug_trace(),
            )
        );
        return $value;
    },
    10,
    2
);

add_action(
    'update_option_cf_theme_options',
    function ( $old_value, $value ) {
        collective_finity_debug_log(
            'update_option_cf_theme_options',
            array(
                'old' => collective_finity_debug_snapshot( $old_value ),
                'new' => collective_finity_debug_snapshot( $value ),
            )
        );
        collective_finity_debug_probe( 'after_update_option_cf_theme_options' );
    },
    10,
    2
);

add_action(
    'add_option_cf_theme_options',
    function ( $option, $value ) {
        collective_finity_debug_log(
            'add_option_cf_theme_options',
            array(
                'new' => collective_finity_debug_snapshot( $value ),
            )
        );
        collective_finity_debug_probe( 'after_add_option_cf_theme_options' );
    },
    10,
    2
);

add_action(
    'delete_option_cf_theme_options',
    function () {
        collective_finity_debug_log(
            'delete_option_cf_theme_options',
            array(
                'trace' => collective_finity_debug_trace(),
            )
        );
    }
);

add_action(
    'updated_option',
    function ( $option, $old_value, $value ) {
        if ( ! collective_finity_debug_is_watched_option( $option ) ) {
            return;
        }
        collective_finity_debug_log(
            'updated_option',
            array(
                'option' => $option,
                'old'    => collective_finity_debug_snapshot( $old_value ),
                'new'    => collective_finity_debug_snapshot( $value ),
            )
        );
    },
    10,
    3
);

add_action(
    'added_option',
    function ( $option, $value ) {
        if ( ! collective_finity_debug_is_watched_option( $option ) ) {
            return;
        }
        collective_finity_debug_log(
            'added_option',
            array(
                'option' => $option,
                'new'    => collective_finity_debug_snapshot( $value ),
            )
        );
    },
    10,
    2
);

add_action(
    'deleted_option',
    function ( $option ) {
        if ( ! collective_finity_debug_is_watched_option( $option ) ) {
            return;
        }
        collective_finity_debug_log(
            'deleted_option',
            array( 'option' => $option )
        );
    }
);

foreach ( array( 'cf_theme_part_header', 'cf_theme_part_footer', 'cf_theme_part_sidebar' ) as $mod_key ) {
    add_filter(
        "theme_mod_{$mod_key}",
        function ( $value ) use ( $mod_key ) {
            static $counts = array();
            if ( ! isset( $counts[ $mod_key ] ) ) {
                $counts[ $mod_key ] = 0;
            }
            $counts[ $mod_key ]++;
            if ( $counts[ $mod_key ] > 10 ) {
                return $value;
            }
            collective_finity_debug_log(
                'get_theme_mod',
                array(
                    'mod'   => $mod_key,
                    'n'     => $counts[ $mod_key ],
                    'value' => $value,
                    'trace' => collective_finity_debug_trace( 5 ),
                )
            );
            return $value;
        },
        0
    );

    add_filter(
        "pre_set_theme_mod_{$mod_key}",
        function ( $value, $old_value ) use ( $mod_key ) {
            collective_finity_debug_log(
                'pre_set_theme_mod',
                array(
                    'mod'   => $mod_key,
                    'old'   => $old_value,
                    'new'   => $value,
                    'trace' => collective_finity_debug_trace(),
                )
            );
            return $value;
        },
        10,
        2
    );
}

add_action(
    'customize_save',
    function ( $wp_customize ) {
        $posted = array();
        if ( isset( $_POST['customized'] ) ) {
            $raw = json_decode( wp_unslash( $_POST['customized'] ), true );
            if ( is_array( $raw ) ) {
                foreach ( $raw as $id => $val ) {
                    if ( false !== strpos( $id, 'cf_theme_options' ) || 0 === strpos( $id, 'cf_theme_part_' ) ) {
                        $posted[ $id ] = is_scalar( $val ) ? $val : '(complex)';
                    }
                }
            }
        }
        collective_finity_debug_log(
            'customize_save',
            array(
                'posted_cf_settings' => $posted,
            )
        );
        collective_finity_debug_probe( 'customize_save' );
    }
);

add_action(
    'customize_save_after',
    function () {
        collective_finity_debug_probe( 'customize_save_after' );
    }
);

add_action(
    'shutdown',
    function () {
        collective_finity_debug_log(
            'shutdown',
            array(
                'note' => 'End of request. If you saw default_option_cf_theme_options after a save, the row was never written. If you saw sanitize with empty tab, Customizer/posted values were ignored.',
            )
        );
    }
);
