<?php
/**
 * Account Rest Controller Class.
 *
 * @package     EverAccounting
 * @subpackage  Api
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();

class Accounts_Controller extends Controller {
    /**
     * @var string
     */
    protected $namespace = 'ea/v1';

    /**
     * @var string
     */
    protected $rest_base = 'accounts';

    /**
     * @since 1.0.2
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
                        'description' => __( 'Unique identifier for the account.', 'wp-ever-accounting' ),
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
    }

    /**
     *
     * @param WP_REST_Request $request
     *
     * @return mixed|WP_Error|WP_REST_Response
     * @since 1.0.2
     */
    public function get_items( $request ) {
        $args = array(
            'include'  => $request['include'],
            'exclude'  => $request['exclude'],
            'search'   => $request['search'],
            'orderby'  => $request['orderby'],
            'order'    => $request['order'],
            'per_page' => $request['per_page'],
            'page'     => $request['page'],
            'offset'   => $request['offset'],
        );
        if ( isset( $request['filterBy'] ) && is_array( $request['filterBy'] ) ) {
            foreach ( $request['filterBy'] as $filter => $value ) {
                $args[ $filter ] = sanitize_text_field( $value );
            }
        }


        $query_result = eaccounting_get_accounts( $args );
        $total_items  = eaccounting_get_accounts( $args, true );
        $response     = array();

        foreach ( $query_result as $tag ) {
            $data       = $this->prepare_item_for_response( $tag, $request );
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
     * @since 1.0.2
     */
    public function create_item( $request ) {
        $request->set_param( 'context', 'edit' );


        $prepared = $this->prepare_item_for_database( $request );

        $item_id = eaccounting_insert_account( (array) $prepared );
        if ( is_wp_error( $item_id ) ) {
            return $item_id;
        }

        $item = eaccounting_get_account( $item_id );

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
     * @since 1.0.2
     */
    public function get_item( $request ) {
        $item_id = intval( $request['id'] );
        if ( empty( $item_id ) ) {
            return null;
        }

        $request->set_param( 'context', 'view' );
        $item = eaccounting_get_account( $item_id );
        if ( is_null( $item ) ) {
            return new WP_Error( 'rest_invalid_item_id', __( 'Could not find the account', 'wp-ever-accounting' ) );
        }

        $response = $this->prepare_item_for_response( $item, $request );

        return rest_ensure_response( $response );
    }

    /**
     *
     * @param WP_REST_Request $request
     *
     * @return int|mixed|WP_Error|WP_REST_Response|null
     * @since 1.0.2
     */
    public function update_item( $request ) {
        $request->set_param( 'context', 'edit' );
        $item_id = intval( $request['id'] );

        $item = eaccounting_get_account( $item_id );
        if ( is_null( $item ) ) {
            return new WP_Error( 'rest_invalid_item_id', __( 'Could not find the account', 'wp-ever-accounting' ) );
        }
        $prepared_args = $this->prepare_item_for_database( $request );

        $prepared_args->id = $item_id;

        if ( ! empty( $prepared_args ) ) {
            $updated = eaccounting_insert_account( (array) $prepared_args );

            if ( is_wp_error( $updated ) ) {
                return $updated;
            }
        }

        $request->set_param( 'context', 'view' );
        $item     = eaccounting_get_account( $item_id );
        $response = $this->prepare_item_for_response( $item, $request );

        return rest_ensure_response( $response );
    }

    /**
     *
     * @param WP_REST_Request $request
     *
     * @return void|WP_Error|WP_REST_Response
     * @since 1.0.2
     */
    public function delete_item( $request ) {
        $item_id = intval( $request['id'] );
        $item    = eaccounting_get_account( $item_id );
        if ( is_null( $item ) ) {
            return new WP_Error( 'rest_invalid_item_id', __( 'Could not find the account', 'wp-ever-accounting' ) );
        }

        $request->set_param( 'context', 'view' );

        $previous = $this->prepare_item_for_response( $item, $request );
        $retval   = eaccounting_delete_account( $item_id );
        if ( ! $retval ) {
            return new WP_Error( 'rest_cannot_delete', __( 'This account cannot be deleted.', 'wp-ever-accounting' ), array( 'status' => 500 ) );
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
     * @param WP_REST_Request $request
     *
     * @return void|WP_Error|WP_REST_Response
     * @since 1.0.2
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
        $deleted = [];
        switch ( $action ) {
            case 'delete':
                foreach ( $items as $item ) {
                    $is_wp_error = eaccounting_delete_account( $item );
                    if ( $is_wp_error ) {
                        return $is_wp_error;
                        break;
                    }
                    $deleted[ $item ] = $is_wp_error;
                }
                break;
        }

        return rest_ensure_response( $deleted );
    }

    /**
     *
     * @param WP_REST_Request $request
     *
     * @return object|stdClass|WP_Error
     * @since 1.0.2
     *
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
        if ( ! empty( $schema['properties']['number'] ) && isset( $request['number'] ) ) {
            $prepared_item->number = $request['number'];
        }
        if ( ! empty( $schema['properties']['currency_code'] ) && isset( $request['currency_code'] ) ) {
            $prepared_item->currency_code = $request['currency_code'];
        }
        if ( ! empty( $schema['properties']['bank_name'] ) && isset( $request['bank_name'] ) ) {
            $prepared_item->bank_name = $request['bank_name'];
        }
        if ( ! empty( $schema['properties']['bank_phone'] ) && isset( $request['bank_phone'] ) ) {
            $prepared_item->bank_phone = $request['bank_phone'];
        }
        if ( ! empty( $schema['properties']['bank_address'] ) && isset( $request['bank_address'] ) ) {
            $prepared_item->bank_address = $request['bank_address'];
        }

        if ( ! empty( $schema['properties']['opening_balance'] ) && isset( $request['opening_balance'] ) ) {
            $prepared_item->opening_balance = eaccounting_money( $request['opening_balance'], $request['currency_code'], false )->getAmount();
        }

        return $prepared_item;
    }

    /**
     *
     * @param mixed           $item
     * @param WP_REST_Request $request
     *
     * @return mixed|WP_Error|WP_REST_Response
     * @since 1.0.2
     */
    public function prepare_item_for_response( $item, $request ) {

        $data = array(
            'id'              => intval( $item->id ),
            'name'            => $item->name,
            'number'          => $item->number,
            'opening_balance' => $item->opening_balance,
            'balance'         => eaccounting_get_account_current_balance( $item->id, true ),
            'currency_code'   => $item->currency_code,
            'bank_name'       => $item->bank_name,
            'bank_phone'      => $item->bank_phone,
            'bank_address'    => $item->bank_address,
            'created_at'      => $this->prepare_date_response( $item->created_at )
        );

        $context = ! empty( $request['context'] ) ? $request['context'] : 'view';
        $data    = $this->add_additional_fields_to_object( $data, $request );
        $data    = $this->filter_response_by_context( $data, $context );

        $response = rest_ensure_response( $data );
        $response->add_links( $this->prepare_links( $item ) );

        return $response;
    }


    /**
     * @param $item
     *
     * @return array
     * @since 1.0.2
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
     * @since 1.0.2
     *
     */
    public function get_item_schema() {
        $schema = array(
            '$schema'    => 'http://json-schema.org/draft-04/schema#',
            'title'      => __( 'Account', 'wp-ever-accounting' ),
            'type'       => 'object',
            'properties' => array(
                'id'              => array(
                    'description' => __( 'Unique identifier for the account.', 'wp-ever-accounting' ),
                    'type'        => 'integer',
                    'context'     => array( 'view', 'embed', 'edit' ),
                    'readonly'    => true,
                    'arg_options' => array(
                        'sanitize_callback' => 'intval',
                    ),
                ),
                'account_id'      => array(
                    'description' => __( 'Account id of the item.', 'wp-ever-accounting' ),
                    'type'        => 'integer',
                    'context'     => array( 'embed', 'view' ),
                    'arg_options' => array(
                        'sanitize_callback' => 'intval',
                    ),
                ),
                'account'         => array(
                    'description' => __( 'Account id of the item.', 'wp-ever-accounting' ),
                    'type'        => 'object',
                    'context'     => array( 'embed', 'view' ),
                    'readonly'    => true,
                ),
                'name'            => array(
                    'description' => __( 'Name of the account.', 'wp-ever-accounting' ),
                    'type'        => 'string',
                    'context'     => array( 'embed', 'view', 'edit' ),
                    'arg_options' => array(
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                    'required'    => true,
                    'default'     => 'John Doe',
                ),
                'number'          => array(
                    'description' => __( 'Number of the account.', 'wp-ever-accounting' ),
                    'type'        => 'string',
                    'context'     => array( 'embed', 'view' ),
                    'readonly'    => true,
                    'arg_options' => array(
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
                'opening_balance' => array(
                    'description' => __( 'Opening balance of the account', 'wp-ever-accounting' ),
                    'type'        => 'string',
                    'context'     => array( 'embed', 'view' ),
                    'default'     => '0',
                    'readonly'    => true,
                    'arg_options' => array(
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
                'currency_code'   => array(
                    'description' => __( 'Currency code of the account', 'wp-ever-accounting' ),
                    'type'        => 'string',
                    'context'     => array( 'embed', 'view', 'edit' ),
                    'arg_options' => array(
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                    'required'    => true,
                ),
                'bank_name'       => array(
                    'description' => __( 'Bank name of the account', 'wp-ever-accounting' ),
                    'type'        => 'string',
                    'context'     => array( 'embed', 'view', 'edit' ),
                    'arg_options' => array(
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
                'bank_phone'      => array(
                    'description' => __( 'Phone number of the bank', 'wp-ever-accounting' ),
                    'type'        => 'string',
                    'context'     => array( 'embed', 'view', 'edit' ),
                    'arg_options' => array(
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
                'bank_address'    => array(
                    'description' => __( 'Address of the bank', 'wp-ever-accounting' ),
                    'type'        => 'string',
                    'context'     => array( 'embed', 'view', 'edit' ),
                    'arg_options' => array(
                        'sanitize_callback' => 'sanitize_textarea_field',
                    ),
                ),
                'date_created'    => array(
                    'description' => __( 'Created date of the account.', 'wp-ever-accounting' ),
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
     * @since 1.0.2
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
            'description' => __( 'Limit result set to specific searched.', 'wp-ever-accounting' ),
            'type'        => 'string',
            'default'     => '',
        );

        return $query_params;
    }

}