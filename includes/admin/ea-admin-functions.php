<?php
/**
 * EverAccounting Admin Functions.
 *
 * @package    EverAccounting
 * @subpackage Admin
 * @since      1.0.2
 */

defined( 'ABSPATH' ) || exit();


/**
 * Get all EverAccounting screen ids.
 *
 * @return array
 * @since  1.0.2
 */
function eaccounting_get_screen_ids() {
    $eaccounting_screen_id = sanitize_title( __( 'Accounting', 'wp-ever-accounting' ) );
    $screen_ids            = array(
        'toplevel_page_e' . $eaccounting_screen_id,
        $eaccounting_screen_id . '_page_ea-transactions',
        $eaccounting_screen_id . '_page_ea-sales',
        $eaccounting_screen_id . '_page_ea-expenses',
        $eaccounting_screen_id . '_page_ea-banking',
        $eaccounting_screen_id . '_page_ea-reports',
        $eaccounting_screen_id . '_page_ea-tools',
        $eaccounting_screen_id . '_page_ea-settings',
    );
    
    return apply_filters( 'eaccounting_screen_ids', $screen_ids );
}

/**
 * Check current page if admin page.
 *
 * @param string $page
 *
 * @return mixed|void
 * @since 1.0.2
 *
 */
function eaccounting_is_admin_page( $page = '' ) {
    if ( ! is_admin() || ! did_action( 'wp_loaded' ) ) {
        $ret = false;
    }
    
    if ( empty( $page ) && isset( $_GET['page'] ) ) {
        $page = eaccounting_clean( $_GET['page'] );
    } else {
        $ret = false;
    }
    
    $pages = str_replace( [ 'toplevel_page_', 'accounting_page_' ], '', eaccounting_get_screen_ids() );
    
    if ( ! empty( $page ) && in_array( $page, $pages ) ) {
        $ret = true;
    } else {
        $ret = in_array( $page, $pages );
    }
    
    return apply_filters( 'eaccounting_is_admin_page', $ret );
}


/**
 * Generates an EverAccounting admin URL based on the given type.
 *
 * @param array  $query_args Optional. Query arguments to append to the admin URL. Default empty array.
 * @param string $type       Optional Type of admin URL. Accepts 'transactions', 'sales', 'purchases', 'banking', 'reports', 'settings', 'tools', 'add-ons'.
 *
 * @return string Constructed admin URL.
 * @since 1.0.2
 *
 */
function eaccounting_admin_url( $query_args = array(), $page = null ) {
    if ( null === $page ) {
        $page = isset( $_GET['page'] ) ? eaccounting_clean( $_GET['page'] ) : '';
    }
    
    $whitelist = str_replace( [ 'toplevel_page_', 'accounting_page_' ], '', eaccounting_get_screen_ids() );
    
    if ( ! in_array( $page, $whitelist, true ) ) {
        $page = '';
    }
    
    $admin_query_args = array_merge( array( 'page' => $page ), $query_args );
    
    $url = add_query_arg( $admin_query_args, admin_url( 'admin.php' ) );
    
    /**
     * Filters the EverAccounting admin URL.
     *
     * @param string $url        Admin URL.
     * @param string $type       Admin URL type.
     * @param array  $query_args Query arguments originally passed to eaccounting_admin_url().
     *
     * @since 1.0.2
     *
     */
    return apply_filters( 'eaccounting_admin_url', $url, $page, $query_args );
}

/**
 * Get activate tab.
 *
 * @param      $tabs
 * @param null $default
 *
 * @return array|mixed|string
 * @since 1.0.2
 *
 */
function eaccounting_get_active_tab( $tabs, $default = null ) {
    if ( isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $tabs ) ) {
        $active_tab = eaccounting_clean( $_GET['tab'] );
    } else if ( ! empty( $default ) ) {
        $active_tab = $default;
    } else {
        $array      = array_keys( $tabs );
        $active_tab = reset( $array );
    }
    
    return $active_tab;
}

/**
 * Outputs navigation tabs markup in core screens.
 *
 * @param array  $tabs       Navigation tabs.
 * @param string $active_tab Active tab slug.
 * @param array  $query_args Optional. Query arguments used to build the tab URLs. Default empty array.
 *
 * @since 1.0.2
 *
 */
function eaccounting_navigation_tabs( $tabs, $active_tab, $query_args = array() ) {
    $tabs = (array) $tabs;
    
    if ( empty( $tabs ) ) {
        return;
    }
    
    $tabs = apply_filters( 'eaccounting_navigation_tabs', $tabs, $active_tab, $query_args );
    
    foreach ( $tabs as $tab_id => $tab_name ) {
        $args    = wp_parse_args( $query_args, array( 'tab' => $tab_id ) );
        $tab_url = eaccounting_admin_url( $args );
        printf( '<a href="%1$s" alt="%2$s" class="%3$s">%4$s</a>',
            esc_url( $tab_url ),
            esc_attr( $tab_name ),
            $active_tab == $tab_id ? 'nav-tab nav-tab-active' : 'nav-tab',
            esc_html( $tab_name )
        );
    }
    
    do_action( 'eaccounting_after_navigation_tabs', $tabs, $active_tab, $query_args );
}

/**
 * Get current tab.
 *
 * @return array|string
 * @since 1.0.2
 */
function eaccounting_get_current_tab() {
    return ( isset( $_GET['tab'] ) ) ? eaccounting_clean( $_GET['tab'] ) : '';
}

/**
 * Per page screen option value for the Affiliates list table
 *
 * @param bool|int $status
 * @param string   $option
 * @param mixed    $value
 *
 * @return mixed
 * @since  1.0.2
 */
function eaccounting_accounts_set_screen_option( $status, $option, $value ) {
    if ( in_array( $option, array( 'eaccounting_edit_accounts_per_page' ), true ) ) {
        return $value;
    }
    
    return $status;
    
}

add_filter( 'set-screen-option', 'eaccounting_accounts_set_screen_option', 10, 3 );

/**
 * Function to get js template
 * @param $slug
 * @return mixed
 * @since 1.0.2
 *
*/
function eaccounting_get_js_template( $slug ) {
    $file = EACCOUNTING_ABSPATH . '/includes/admin/js-templates/' . $slug . '.php';
    if ( file_exists( $file ) ) {
        return include $file;
    }
}


/**
 * Get admin view.
 *
 * since 1.0.2
 *
 * @param       $template_name
 * @param array $args
 */
function eaccounting_get_admin_template( $template_name, $args = [] ) {
    if ( $args && is_array( $args ) ) {
        extract( $args );
    }
    
    $file = apply_filters( 'eaccounting_admin_template', EACCOUNTING_ADMIN_ABSPATH . '/views/' . $template_name . '.php' );
    
    if ( file_exists( $file ) ) {
        include $file;
    }
}
