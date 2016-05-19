<?php

/**
 * Data formatting utilities class.
 *
 * @link       https://www.bytepixie.com/options-pixie/
 * @since      1.0
 *
 * @package    Options_Pixie
 * @subpackage Options_Pixie/includes
 */

/**
 * Data formatting utilities class.
 *
 * @since      1.0
 * @package    Options_Pixie
 * @subpackage Options_Pixie/includes
 * @author     Byte Pixie <hello@bytepixie.com>
 */
class Options_Pixie_Data_Format {

	/**
	 * Turn a string of serialized data into HTML.
	 *
	 * @since    1.0
	 *
	 * @param string $data      The string containing serialized data.
	 * @param string $div_class An optional CSS class to wrap the elements with.
	 *
	 * @return string of HTML or empty string if there was no serialized data.
	 */
	public static function to_html( $data, $div_class = '' ) {
		$html = Options_Pixie_Data_Format::_to_html( $data );

		if ( ! empty( $div_class ) ) {
			$html = '<div class="' . $div_class . '">' . $html . '</div>';
		}

		return $html;
	}

	/**
	 * Turn a string of serialized data into HTML recursively.
	 *
	 * @since    1.0
	 *
	 * @param mixed $data The data to be converted to HTML.
	 * @param int   $recursion_level
	 *
	 * @return string of HTML or empty string if there was no serialized data.
	 */
	private static function _to_html( $data, $recursion_level = 0 ) {
		$html = '';
		if ( is_array( $data ) ) {
			// Normally data needing conversion to an array is passed.
			// However, if the passed data is already an array bump the recursion level to start showing its children properly.
			if ( 0 === $recursion_level ) {
				$recursion_level = 1;
			}

			$array_class = '';
			foreach ( $data as $key => $value ) {
				if ( ! is_int( $key ) ) {
					$array_class = ' associative';
					break;
				}
			}
			if ( 1 < $recursion_level ) {
				$html .= '<span class="array count">';
				$html .= '<span class="dashicons dashicons-arrow-right collapsed"></span>';
				$html .= '<span class="dashicons dashicons-arrow-down expanded hidden"></span>';
				$html .= sprintf( _x( '(%1$d items)', 'count of array entries', 'options-pixie' ), count( $data ) );
				$html .= '</span>';
				$array_class .= ' hidden';
			}
			$html .= '<dl class="array' . $array_class . '">';
			foreach ( $data as $key => $value ) {
				$html .= '<dt class="key">' . $key . '</dt>';
				$html .= '<dd class="value">' . Options_Pixie_Data_Format::_to_html( $value, $recursion_level + 1 ) . '</dd>';
			}
			$html .= '</dl>';
		} elseif ( Options_Pixie_Data_Format::is_broken_serialized( $data ) ) {
			$html .= preg_replace_callback( '/s:(\d+):"(.*?)";/', array( 'Options_Pixie_Data_Format', '_highlight_broken_serialized_string' ), $data );
		} elseif ( is_serialized( $data ) ) {
			$value = unserialize( $data );
			$html .= Options_Pixie_Data_Format::_to_html( $value, $recursion_level + 1 );
		} elseif ( is_object( $data ) ) {
			// The top level object should not be treated as an expandable array.
			if ( 1 === $recursion_level ) {
				$recursion_level = 0;
			}
			$object_array = Options_Pixie_Data_Format::_object_to_array( $data );
			$html .= Options_Pixie_Data_Format::_to_html( $object_array, $recursion_level + 1 );
		} elseif ( Options_Pixie_Data_Format::is_json( $data ) ) {
			$value = json_decode( $data, true );
			$html .= Options_Pixie_Data_Format::_to_html( $value, $recursion_level + 1 );
		} elseif ( Options_Pixie_Data_Format::is_base64( $data ) ) {
			$value = base64_decode( $data, true );
			$html .= Options_Pixie_Data_Format::_to_html( $value, $recursion_level );
		} else {
			$html .= esc_html( print_r( $data, true ) );
		}

		return $html;
	}

	/**
	 * Highlights broken segments of a serialized string.
	 *
	 * @since 1.0
	 *
	 * @param array $matches Array of matched parts.
	 *
	 * @return string
	 */
	private static function _highlight_broken_serialized_string( $matches ) {
		$return = esc_html( $matches[0] );
		if ( strlen( $matches[2] ) != $matches[1] ) {
			$return = Options_Pixie_Data_Format::wrap_with_error( $return, __( 'Broken string segment', 'options-pixie' ) );
		}

		return $return;
	}

	/**
	 * Wrap a string with standard error highlighting.
	 *
	 * @since 1.0
	 *
	 * @param string $string The value to be wrapped with standard error highlighting.
	 * @param string $title  An optional title to be shown on hover.
	 *
	 * @return string
	 */
	public static function wrap_with_error( $string, $title = '' ) {
		if ( ! empty( $title ) ) {
			$title = ' title="' . esc_attr( $title ) . '"';
		}

		return '<span class="error"' . $title . '>' . $string . '</span>';
	}

	/**
	 * Turns an Object into an Array, even when the class of the Object is no longer available.
	 *
	 * @since 1.0
	 *
	 * @param Object $object
	 *
	 * @return array
	 */
	private static function _object_to_array( $object ) {
		$object_array = array();

		$object_name = get_class( $object );

		if ( '__PHP_Incomplete_Class' == $object_name ) {
			foreach ( $object as $key => $value ) {
				if ( '__PHP_Incomplete_Class_Name' == $key ) {
					$object_name = $value;
					break;
				}
			}
		}

		$object_name = '&lt;' . _x( 'Object', 'Showing an unserialized object', 'options-pixie' ) . '&gt; ' . $object_name;

		foreach ( $object as $key => $value ) {
			if ( is_a( $value, '__PHP_Incomplete_Class' ) ) {
				$object_array[ $object_name ][ $key ] = Options_Pixie_Data_Format::_object_to_array( $value );
			} elseif ( '__PHP_Incomplete_Class' == get_class( $object ) && '__PHP_Incomplete_Class_Name' == $key ) {
				continue;
			} else {
				$object_array[ $object_name ][ $key ] = $value;
			}
		}

		return $object_array;
	}

	/**
	 * Can the supplied string be treated as JSON?
	 *
	 * @since 1.0
	 *
	 * @param string $value
	 *
	 * @return bool
	 */
	public static function is_json( $value ) {
		$is_json = false;

		if ( is_string( $value ) && 0 < strlen( trim( $value ) ) && ! is_numeric( $value ) && null !== json_decode( $value ) ) {
			$is_json = true;
		}

		return $is_json;
	}

	/**
	 * Is the data expandable when converted to html?
	 *
	 * @since 1.0
	 *
	 * @param mixed $data
	 *
	 * @return bool
	 */
	public static function is_expandable( $data ) {
		$value = Options_Pixie_Data_Format::to_html( $data );
		if ( false !== strpos( $value, 'class="array count"' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Is the data a base64 encoded serialized string, object or JSON string?
	 *
	 * @since 1.0
	 *
	 * @param mixed $data
	 *
	 * @return bool
	 */
	public static function is_base64( $data ) {
		if ( ! empty( $data ) && is_string( $data ) && base64_encode( base64_decode( $data, true ) ) === $data ) {

			$data = base64_decode( $data, true );

			if ( is_serialized( $data ) || is_object( $data ) || Options_Pixie_Data_Format::is_json( $data ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Tests whether given serialized data is broken or not.
	 *
	 * @since 1.0
	 *
	 * @param string $data Serialized data string.
	 *
	 * @return bool
	 */
	public static function is_broken_serialized( $data ) {
		$broken = false;

		if ( is_serialized( $data ) ) {
			$value = @unserialize( $data );

			if ( false === $value && serialize( false ) !== $value ) {
				$broken = true;
			}
		}

		return $broken;
	}

	/**
	 * Returns an array of the high level types of the data other than general text or number.
	 *
	 * Types: S (serialized), O (object), J (JSON), b64 (base64), !!! (broken serialized).
	 *
	 * @since 1.0
	 *
	 * @param mixed $data
	 *
	 * @return array
	 */
	public static function get_data_types( $data ) {
		$types = array();

		if ( Options_Pixie_Data_Format::is_base64( $data ) ) {
			$types[] = 'b64';
			$data    = base64_decode( $data, true );
		}

		if ( is_serialized( $data ) ) {
			$types[] = 'S';

			if ( Options_Pixie_Data_Format::is_broken_serialized( $data ) ) {
				$types[] = '!!!';
			} else {
				$data = unserialize( $data );
			}
		}

		if ( is_object( $data ) ) {
			$types[] = 'O';
		}

		if ( Options_Pixie_Data_Format::is_json( $data ) ) {
			$types[] = 'J';
		}

		return $types;
	}

	/**
	 * Checks whether data contains a serialized value, including if base64 encoded.
	 *
	 * @param string $data
	 *
	 * @return bool
	 */
	public static function contains_serialized( $data ) {
		if ( ! empty( $data ) && in_array( 'S', Options_Pixie_Data_Format::get_data_types( $data ) ) ) {
			return true;
		}

		return false;
	}
}
