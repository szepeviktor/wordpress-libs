<?php

namespace Lipe\Lib\Util;

use Lipe\Lib\Traits\Singleton;

/**
 * @author Mat Lipe
 * @since  2.14.0
 *
 */
class Colors {
	use Singleton;

	/**
	 * Convert a hexadecimal color to an rgba version.
	 *
	 * @param       $color - Hexadecimal version of color with leading #
	 * @param float [$transparency] - Adds an alpha value to make the color transparent.
	 *                     Will return `rgba` version of color if provided, `rgb` if not.
	 *
	 * @return string
	 */
	public function hex_to_rgba( $color, float $transparency = 1.0 ) : string {
		if ( '#' === $color[0] ) {
			$color = substr( $color, 1 );
		} else {
			return $color;
		}

		if ( 6 === strlen( $color ) ) {
			$hex = [ $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] ];
		} elseif ( 3 === strlen( $color ) ) {
			$hex = [ $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] ];
		} else {
			// Something is not right.
			return 'rgb(0,0,0)';
		}
		$rgb = array_map( 'hexdec', $hex );
		if ( 1.0 === $transparency ) {
			return 'rgb(' . implode( ',', $rgb ) . ')';
		}

		return 'rgba(' . implode( ',', $rgb ) . ',' . $transparency . ')';
	}


	/**
	 * Convert an rgb(a) color to an hexadecimal version.
	 *
	 * @param string $rgba - Rgba version of color include leading `rgb` or `rgba`.
	 *
	 * @return string
	 */
	public function rgba_to_hex( string $rgba ) : string {
		if ( strpos( $rgba, '#' ) === 0 ) {
			return $rgba;
		}

		preg_match( '/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i', $rgba, $by_color );

		return sprintf( '#%02x%02x%02x', $by_color[1], $by_color[2], $by_color[3] );
	}
}
