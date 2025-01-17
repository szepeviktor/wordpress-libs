<?php

namespace Lipe\Lib\Util;

use Lipe\Lib\Traits\Singleton;

/**
 * @author Mat Lipe
 * @since  December, 2018
 *
 */
class Arrays {
	use Singleton;


	/**
	 * Turn a numeric array of values into an associative array with
	 * the odd values being keys for the even values.
	 *
	 * @param array $array
	 *
	 * @example ['page', 3, 'category', 6 ] becomes [ 'page' => 3, 'category' => 6 ]
	 *
	 * @since   2.11.0
	 *
	 * @return array
	 */
	public function array_chunk_to_associative( array $array ) : array {
		$assoc = [];
		foreach ( array_chunk( $array, 2 ) as $pair ) {
			if ( 2 === count( $pair ) ) {
				[ $key, $value ] = $pair;
				$assoc[ $key ] = $value;
			} else {
				$assoc[] = array_shift( $pair );
			}
		}

		return $assoc;
	}


	/**
	 * Apply a callback to all elements of an array recursively.
	 *
	 * Similar to `array_walk_recursive` except this returns the result as
	 * a new array instead of requiring you pass the array element by reference
	 * and alter it directly.
	 *
	 * @param callable $callback
	 * @param array    $array
	 *
	 * @since 2.5.0
	 *
	 * @return array
	 */
	public function array_map_recursive( callable $callback, array $array ) : array {
		$output = [];
		foreach ( $array as $key => $data ) {
			if ( \is_array( $data ) ) {
				$output[ $key ] = $this->array_map_recursive( $callback, $data );
			} else {
				$output[ $key ] = $callback( $data );
			}
		}

		return $output;
	}


	/**
	 * Works the same as `array_merge_recursive` except instead of turning
	 * duplicate array keys into arrays, this will favor the $args over
	 * the $defaults and clobber identical $default keys.
	 *
	 * @param array $args
	 * @param array $defaults
	 *
	 * @since 2.1.0
	 *
	 * @return array
	 */
	public function array_merge_recursive( array $args, array $defaults ) : array {
		foreach ( $args as $key => $val ) {
			if ( \is_array( $val ) && isset( $defaults[ $key ] ) && \is_array( $defaults[ $key ] ) ) {
				$defaults[ $key ] = $this->array_merge_recursive( $val, $defaults[ $key ] );
			} else {
				$defaults[ $key ] = $val;
			}
		}

		return $defaults;
	}


	/**
	 * Works the same as `array_map` except the array key is passed as the
	 * second argument to the callback and original keys are preserved.
	 *
	 *
	 * @param callable $callback
	 * @param array    $array
	 *
	 * @since 2.17.0
	 *
	 * @return array
	 */
	public function array_map_assoc( callable $callback, array $array ) : array {
		return array_combine( array_keys( $array ), array_map( $callback, $array, array_keys( $array ) ) );
	}


	/**
	 * Removes a key from an array recursively.
	 *
	 * @param string $key - Key to remove.
	 * @param array  $array - Array to recursively remove keys from.
	 *
	 * @since 2.23.1
	 *
	 * @return array
	 */
	public function array_recursive_unset( string $key, array $array ) : array {
		unset( $array[ $key ] );
		foreach ( $array as $_key => $_values ) {
			if ( \is_array( $_values ) ) {
				$array[ $_key ] = $this->array_recursive_unset( $key, $_values );
			}
		}
		return $array;
	}
}
