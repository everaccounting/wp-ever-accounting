<?php
/**
 * Main Rest Controller Class.
 *
 * @package     EverAccounting
 * @subpackage  Api
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || die();

abstract class EAccounting_REST_Controller extends WP_REST_Controller {
    
    /**
     * @param WP_REST_Request $request
     *
     * @return bool|WP_Error
     * @since       1.0.2
     */
    public function get_items_permissions_check( $request ) {
        return current_user_can( 'manage_options' );
    }
    
    /**
     *
     * @param WP_REST_Request $request
     *
     * @return bool|WP_Error
     * @since       1.0.2
     */
    public function get_item_permissions_check( $request ) {
        return current_user_can( 'manage_options' );
    }
    
    /**
     *
     * @param WP_REST_Request $request
     *
     * @return bool|WP_Error
     * @since       1.0.2
     */
    public function create_item_permissions_check( $request ) {
        return current_user_can( 'manage_options' );
    }
    
    /**
     *
     * @param WP_REST_Request $request
     *
     * @return bool|WP_Error
     * @since       1.0.2
     */
    public function update_item_permissions_check( $request ) {
        return current_user_can( 'manage_options' );
    }
    
    /**
     *
     * @param WP_REST_Request $request
     *
     * @return bool|WP_Error
     * @since       1.0.2
     */
    public function delete_item_permissions_check( $request ) {
        return current_user_can( 'manage_options' );
    }
    
    /**
     * Retrieves the query params for the collections.
     *
     * @return array Query parameters for the collection.
     * @since 1.0.2
     *
     */
    public function get_collection_params() {
        return array(
            'context'  => $this->get_context_param(),
            'page'     => array(
                'description'       => __( 'Current page of the collection.', 'wp-ever-accounting' ),
                'type'              => 'integer',
                'default'           => 1,
                'sanitize_callback' => 'absint',
                'validate_callback' => 'rest_validate_request_arg',
                'minimum'           => 1,
            ),
            'per_page' => array(
                'description'       => __( 'Maximum number of items to be returned in result set.', 'wp-ever-accounting' ),
                'type'              => 'integer',
                'default'           => 20,
                'minimum'           => 1,
                'maximum'           => 100,
                'sanitize_callback' => 'absint',
                'validate_callback' => 'rest_validate_request_arg',
            ),
            'offset'   => array(
                'description'       => __( 'Offset the result set by a specific number of items.', 'wp-ever-accounting' ),
                'type'              => 'integer',
                'sanitize_callback' => 'absint',
            ),
            'search'   => array(
                'description'       => __( 'Limit results to those matching a string.', 'wp-ever-accounting' ),
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => 'rest_validate_request_arg',
            ),
            'order'    => array(
                'description' => __( 'Order sort attribute ascending or descending.', 'wp-ever-accounting' ),
                'type'        => 'string',
                'default'     => 'desc',
                'enum'        => array( 'asc', 'desc', ),
            ),
            'orderby'  => array(
                'description' => __( 'Sort collection by contact attribute.', 'wp-ever-accounting' ),
                'type'        => 'string',
                'default'     => 'id',
            ),
        );
    }
    
    /**
     * Checks the post_date_gmt or modified_gmt and prepare
     *
     * @param null $date
     *
     * @return string|null
     * @since 1.0.2
     */
    protected function prepare_date_response( $date = null ) {
        if ( '0000-00-00 00:00:00' === $date || '0000-00-00' === $date ) {
            return null;
        }
        
        return mysql_to_rfc3339( $date );
    }
    
    /**
     *
     * @param $keyvalues
     *
     * @return array
     * @since       1.0.2
     */
    protected function assoc_to_options( $keyvalues ) {
        $options = [];
        foreach ( $keyvalues as $key => $value ) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        
        return $options;
    }
    
    /**
     * @param $user_id
     *
     * @return array
     * @since 1.0.2
     */
    protected function get_wp_user_data( $user_id ) {
        $user = get_user_by( 'ID', $user_id );
        
        $data = [
            'id'    => isset( $user->ID ) ? $user->ID : '',
            'name'  => isset( $user->display_name ) ? $user->display_name : '',
            'email' => isset( $user->user_email ) ? $user->user_email : '',
            'photo' => isset( $user->ID ) ? get_avatar_url( $user->ID ) : get_avatar_url( null ),
        ];
        
        return $data;
    }
    
    /**
     * @param $date
     *
     * @return array|bool
     * @since 1.0.2
     */
    protected function get_query_dates( $date ) {
        $dates = explode( '_', $date, 2 );
        $start = eaccounting_sanitize_date( $dates[0] );
        $end   = isset( $dates[1] ) ? eaccounting_sanitize_date( $dates[1] ) : date( 'Y-m-d', current_time( 'timestamp' ) );
        if ( ! $start || ! $end ) {
            return array(
                'start_date' => '',
                'end_date'   => '',
            );
        }
        
        return [
            'start_date' => sanitize_text_field( $start ),
            'end_date'   => sanitize_text_field( $end ),
        ];
        
    }
    
    /**
     * The method is used for retriving single object from rest request
     *
     * @param        $endpoint
     * @param        $id
     * @param null   $default
     * @param string $base
     *
     * @return array|null
     * @since 1.0.2
     */
    protected static function get_rest_object( $endpoint, $id, $default = null, $base = '/ea/v1/' ) {
        if ( empty( $id ) ) {
            return $default;
        }
        
        $endpoint = $base . untrailingslashit( ltrim( $endpoint, '/' ) ) . '/' . intval( $id );
        $response = eaccounting_rest_request( $endpoint, $args = array(), $method = 'GET' );
        
        return is_wp_error( $response ) ? $default : $response;
    }
}
