<?php

namespace Lipe\Lib\Rest_Api;

use Lipe\Lib\Traits\Singleton;

/**
 * @deprecated in favor of simply using the built in functionality.
 *             For example if you set `show_in_rest` to `true` when registering
 *             a taxonomy, the terms are available via `/wp-json/wp/v2/<taxonomy>`.
 */
class Taxonomies {
	use Singleton;

	private $allowed_taxonomies = [
		'category',
		'post_tag',
	];


	public function hook() : void {
		\_deprecated_file( __FILE__, '2.11.0', null, 'The ' . __CLASS__ . ' has been deprecated in favor of using the built in functionality for retrieving terms. If you set `show_in_rest` to `true` when registering a taxonomy, the terms are available via `/wp-json/wp/v2/<taxonomy>`' );

		add_action( 'rest_api_init', [ $this, 'register_routes' ], 10 );
	}


	public function add_taxonomy( $taxonomy ) : void {
		$this->allowed_taxonomies[] = $taxonomy;
	}


	public function register_routes() : void {
		register_rest_route( '/taxonomies/v1', '/terms/(?P<taxonomy>[a-zA-Z0-9-]+)', [
			'methods'  => 'GET',
			'callback' => [ $this, 'get_terms' ],
			'args'     => [
				'terms' => [
					'type' => 'array',
				],
			],
		] );
	}


	public function get_terms( \WP_REST_Request $request ) {
		$taxonomy = $request->get_param( 'taxonomy' );
		if ( empty( $taxonomy ) ) {
			return new \WP_Error( 'get_terms_failed', __( 'You must supply a taxonomy', 'lipe' ), [ 'status' => 201 ] );
		}

		$object = get_taxonomy( $taxonomy );
		if ( empty( $object ) || ! \in_array( $taxonomy, $this->allowed_taxonomies, true ) ) {
			return new \WP_Error( 'get_terms_failed', __( 'Invalid taxonomy', 'lipe' ), [ 'status' => 201 ] );
		}

		$columns = [
			'terms' => array_values( get_terms( [ 'taxonomy' => $taxonomy ] ) ),
		];

		$response = new \WP_REST_Response( $columns );
		$response->set_status( 200 );

		return $response;
	}
}
