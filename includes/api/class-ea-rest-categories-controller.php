<?php
/**
 * Categories Rest Controller Class.
 *
 * @package     EverAccounting
 * @subpackage  Api
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();

class EAccounting_Categories_Controller extends EAccounting_REST_Controller {
    /**
     * @var string
     */
    protected $namespace = 'ea/v1';
    
    /**
     * @var string
     */
    protected $rest_base = 'categories';
    
    /**
     * @since 1.0.0
     */
    public function register_routes() {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_items' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args'                => $this->get_collection_params(),
                ),
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'create_item' ),
                    'permission_callback' => array( $this, 'create_item_permissions_check' ),
                    'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
                ),
                'schema' => array( $this, 'get_public_item_schema' ),
            )
        );
        
        $get_item_args = array(
            'context' => $this->get_context_param( array( 'default' => 'view' ) ),
        );
        
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<id>[\d]+)',
            array(
                'args'   => array(
                    'id' => array(
                        'description' => __( 'Unique identifier for the object.', 'wp-ever-accounting' ),
                        'type'        => 'integer',
                        'required'    => true,
                    ),
                ),
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_item' ),
                    'permission_callback' => array( $this, 'get_item_permissions_check' ),
                    'args'                => $get_item_args,
                ),
                array(
                    'methods'             => WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_item' ),
                    'permission_callback' => array( $this, 'update_item_permissions_check' ),
                    'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
                ),
                array(
                    'methods'             => WP_REST_Server::DELETABLE,
                    'callback'            => array( $this, 'delete_item' ),
                    'permission_callback' => array( $this, 'delete_item_permissions_check' ),
                ),
                'schema' => array( $this, 'get_public_item_schema' ),
            )
        );
        
        register_rest_route( $this->namespace, '/' . $this->rest_base . '/bulk', array(
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => [ $this, 'handle_bulk_actions' ],
                'permission_callback' => array( $this, 'get_item_permissions_check' ),
                'args'                => $this->get_collection_params(),
            ),
        ) );
        
        register_rest_route( $this->namespace, '/' . $this->rest_base . '/types', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [ $this, 'get_types' ],
                'permission_callback' => array( $this, 'get_item_permissions_check' ),
                'args'                => $this->get_collection_params(),
            ),
        ) );
        
    }
    
    /**
     *
     * @param WP_REST_Request $request
     *
     * @return mixed|WP_Error|WP_REST_Response
     * @since       1.0.2
     */
    public function get_items( $request ) {
        $args = array(
            'include'  => $request['include'],
            'exclude'  => $request['exclude'],
            'search'   => $request['search'],
            'type'     => $request['type'],
            'orderby'  => $request['orderby'],
            'order'    => $request['order'],
            'per_page' => $request['per_page'],
            'page'     => $request['page'],
            'offset'   => $request['offset'],
        );
        
        $query_result = eaccounting_get_categories( $args );
        $total_items  = eaccounting_get_categories( $args, true );
        $response     = array();
        
        foreach ( $query_result as $item ) {
            $data       = $this->prepare_item_for_response( $item, $request );
            $response[] = $this->prepare_response_for_collection( $data );
        }
        
        $response = rest_ensure_response( $response );
        
        $per_page = (int) $args['per_page'];
        
        $response->header( 'X-WP-Total', (int) $total_items );
        
        $max_pages = ceil( $total_items / $per_page );
        
        $response->header( 'X-WP-TotalPages', (int) $max_pages );
        
        return rest_ensure_response( $response );
    }
    
    /***
     *
     * @param WP_REST_Request $request
     *
     * @return int|mixed|WP_Error|WP_REST_Response|null
     * @since       1.0.2
     */
    public function create_item( $request ) {
        $request->set_param( 'context', 'edit' );
        
        
        $prepared = $this->prepare_item_for_database( $request );
        
        $item_id = eaccounting_insert_category( (array) $prepared );
        if ( is_wp_error( $item_id ) ) {
            return $item_id;
        }
        
        $item = eaccounting_get_category( $item_id );
        
        $request->set_param( 'context', 'view' );
        
        $response = $this->prepare_item_for_response( $item, $request );
        $response = rest_ensure_response( $response );
        $response->set_status( 201 );
        
        return $response;
    }
    
    /**
     *
     * @param WP_REST_Request $request
     *
     * @return mixed|WP_Error|WP_REST_Response
     * @since       1.0.2
     */
    public function get_item( $request ) {
        $item_id = intval( $request['id'] );
        $request->set_param( 'context', 'view' );
        $item = eaccounting_get_category( $item_id );
        if ( is_null( $item ) ) {
            return new WP_Error( 'rest_invalid_item_id', __( 'Could not find the category', 'wp-ever-accounting' ) );
        }
        
        $response = $this->prepare_item_for_response( $item, $request );
        
        return rest_ensure_response( $response );
    }
    
    /**
     *
     * @param WP_REST_Request $request
     *
     * @return int|mixed|WP_Error|WP_REST_Response|null
     * @since       1.0.2
     */
    public function update_item( $request ) {
        $request->set_param( 'context', 'edit' );
        $item_id = intval( $request['id'] );
        
        $item = eaccounting_get_category( $item_id );
        if ( is_null( $item ) ) {
            return new WP_Error( 'rest_invalid_item_id', __( 'Could not find the category', 'wp-ever-accounting' ) );
        }
        $prepared_args = $this->prepare_item_for_database( $request );
        
        $prepared_args->id = $item_id;
        
        if ( ! empty( $prepared_args ) ) {
            $updated = eaccounting_insert_category( (array) $prepared_args );
            
            if ( is_wp_error( $updated ) ) {
                return $updated;
            }
        }
        
        $request->set_param( 'context', 'view' );
        $item     = eaccounting_get_category( $item_id );
        $response = $this->prepare_item_for_response( $item, $request );
        
        return rest_ensure_response( $response );
    }
    
    /**
     *
     * @param WP_REST_Request $request
     *
     * @return void|WP_Error|WP_REST_Response|bool
     * @since       1.0.2
     */
    public function delete_item( $request ) {
        $item_id = intval( $request['id'] );
        $item    = eaccounting_get_category( $item_id );
        if ( is_null( $item ) ) {
            return new WP_Error( 'rest_invalid_item_id', __( 'Could not find the category', 'wp-ever-accounting' ) );
        }
        
        $request->set_param( 'context', 'view' );
        
        $previous = $this->prepare_item_for_response( $item, $request );
        $retval   = eaccounting_delete_category( $item_id );
        if ( is_wp_error( $retval ) ) {
            return $retval;
        }
        
        $response = new WP_REST_Response();
        $response->set_data(
            array(
                'deleted'  => true,
                'previous' => $previous->get_data(),
            )
        );
        
        return $response;
    }
    /**
     *
     * @param $request
     *
     * @return mixed|WP_REST_Response
     * @since       1.0.2
     */
    public function handle_bulk_actions( $request ) {
        $actions = [
            'delete',
        ];
        $action  = $request['action'];
        $items   = $request['items'];
        if ( empty( $action ) || ! in_array( $action, $actions ) ) {
            return new WP_Error( 'invalid_bulk_action', __( 'Invalid bulk action', 'wp-ever-accounting' ) );
        }
        
        switch ( $action ) {
            case 'delete':
                foreach ( $items as $item ) {
                    eaccounting_delete_category( $item );
                }
                break;
        }
        unset( $request['action'] );
        unset( $request['items'] );
        
        return $this->get_items( $request );
    }
    
    /**
     *
     * @param $request
     *
     * @return mixed|WP_REST_Response
     * @since       1.0.2
     */
    public function get_types( $request ) {
        $types = eaccounting_get_category_types();
        
        return rest_ensure_response( $this->assoc_to_options( $types ) );
    }
    
    /**
     * @param WP_REST_Request $request
     *
     * @return object|stdClass|WP_Error
     * @since       1.0.2
     */
    public function prepare_item_for_database( $request ) {
        $prepared_item = new stdClass();
        $schema        = $this->get_item_schema();
        
        if ( ! empty( $schema['properties']['id'] ) && isset( $request['id'] ) ) {
            $prepared_item->id = $request['id'];
        }
        if ( ! empty( $schema['properties']['name'] ) && isset( $request['name'] ) ) {
            $prepared_item->name = $request['name'];
        }
        if ( ! empty( $schema['properties']['type'] ) && isset( $request['type'] ) ) {
            $prepared_item->type = $request['type'];
        }
        if ( ! empty( $schema['properties']['color'] ) && isset( $request['color'] ) ) {
            $prepared_item->color = $request['color'];
        }
        
        return $prepared_item;
    }
    
    
    /**
     *
     * @param mixed           $item
     * @param WP_REST_Request $request
     *
     * @return mixed|WP_Error|WP_REST_Response
     * @since       1.0.2
     */
    public function prepare_item_for_response( $item, $request ) {
        $data = array(
            'id'         => intval( $item->id ),
            'name'       => $item->name,
            'type'       => $item->type,
            'color'      => sanitize_hex_color( $item->color ),
            'created_at' => $this->prepare_date_response( $item->created_at )
        );
        
        $context = ! empty( $request['context'] ) ? $request['context'] : 'view';
        $data    = $this->add_additional_fields_to_object( $data, $request );
        $data    = $this->filter_response_by_context( $data, $context );
        
        $response = rest_ensure_response( $data );
        $response->add_links( $this->prepare_links( $item ) );
        
        return $response;
    }
    
    
    /**
     *
     * @param $item
     *
     * @return array
     * @since       1.0.2
     */
    protected function prepare_links( $item ) {
        $base = sprintf( '/%s/%s/', $this->namespace, $this->rest_base );
        $url  = $base . $item->id;
        
        // Entity meta.
        $links = array(
            'self'       => array(
                'href' => rest_url( $url ),
            ),
            'collection' => array(
                'href' => rest_url( $base ),
            )
        );
        
        return $links;
    }
    
    /**
     * Retrieves the items's schema, conforming to JSON Schema.
     *
     * @return array Item schema data.
     * @since       1.0.2
     *
     */
    public function get_item_schema() {
        $schema = array(
            '$schema'    => 'http://json-schema.org/draft-04/schema#',
            'title'      => __( 'Category', 'wp-ever-accounting' ),
            'type'       => 'object',
            'properties' => array(
                'id'           => array(
                    'description' => __( 'Unique identifier for the category.', 'wp-ever-accounting' ),
                    'type'        => 'integer',
                    'context'     => array( 'view', 'embed', 'edit' ),
                    'readonly'    => true,
                    'arg_options' => array(
                        'sanitize_callback' => 'intval',
                    ),
                ),
                'name'         => array(
                    'description' => __( 'Name of the category.', 'wp-ever-accounting' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'embed', 'edit' ),
                    'required'    => true,
                    'arg_options' => array(
                        'sanitize_callback' => 'sanitize_text_field'
                    ),
                ),
                'type'         => array(
                    'description' => __( 'Type of the category.', 'wp-ever-accounting' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'embed', 'edit' ),
                    'required'    => true,
                    'enum'        => array_keys( eaccounting_get_category_types() ),
                    'arg_options' => array(
                        'sanitize_callback' => 'sanitize_text_field'
                    ),
                ),
                'color'        => array(
                    'description' => __( 'Color of the category.', 'wp-ever-accounting' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'embed', 'edit' ),
                    'arg_options' => array(
                        'sanitize_callback' => 'sanitize_hex_color',
                    ),
                ),
                'date_created' => array(
                    'description' => __( 'Created date of the category.', 'wp-ever-accounting' ),
                    'type'        => 'string',
                    'format'      => 'date-time',
                    'context'     => array( 'view' ),
                    'readonly'    => true,
                ),
            
            )
        );
        
        return $this->add_additional_fields_schema( $schema );
    }
    
    
    /**
     * Retrieves the query params for the items collection.
     *
     * @return array Collection parameters.
     * @since       1.0.2
     *
     */
    public function get_collection_params() {
        $query_params                       = parent::get_collection_params();
        $query_params['context']['default'] = 'view';
        $query_params['exclude']            = array(
            'description' => __( 'Ensure result set excludes specific ids.', 'wp-ever-accounting' ),
            'type'        => 'array',
            'items'       => array(
                'type' => 'integer',
            ),
            'default'     => array(),
        );
        
        $query_params['include'] = array(
            'description' => __( 'Limit result set to specific IDs.', 'wp-ever-accounting' ),
            'type'        => 'array',
            'items'       => array(
                'type' => 'integer',
            ),
            'default'     => array(),
        );
        
        $query_params['search'] = array(
            'description' => __( 'Limit result set to specific search.', 'wp-ever-accounting' ),
            'type'        => 'string',
            'default'     => '',
        );
        
        return $query_params;
    }
    
}
