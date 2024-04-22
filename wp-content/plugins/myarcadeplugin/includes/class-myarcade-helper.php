<?php
/**
 * Heler functions.
 *
 * @author Daniel Bakovic <contact@myarcadeplugin.com>
 * @package MyArcadePlugin/Helpers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // Exit if accessed directly.
}

/**
 * Helper functions.
 */
class MyArcade_Helper {

	/**
	 * Function to check if the array has correct syntax. Used for some IBPArcade games,
	 * that uses outdated array declaration
	 *
	 * @param string $contents Content of a game config file. Usually a PHP file.
	 * @return boolean
	 */
	public static function has_correct_array_synthax( $contents ) {

		$tokens = token_get_all( '<?php ' . $contents );

		$brace_count = 0;
		$last_token  = null;

		foreach ( $tokens as $token ) {
			if ( is_array( $token ) ) {
				$type  = $token[0];
				$value = $token[1];

				if ( T_ARRAY === $type || '[' === $value ) {
					$brace_count++;
				} elseif ( T_WHITESPACE === $type || T_COMMENT === $type ) {
					continue;
				} elseif ( $value === ']' ) {
					$brace_count--;
				}

				// Check for missing quotes around array keys.
				if ( T_CONSTANT_ENCAPSED_STRING === $type && T_DOUBLE_ARROW === $last_token ) {
					return false; // Missing quotes around array key.
				}

				$last_token = $type;
			}
		}

		return 0 === $brace_count;
	}

	/**
	 * Fix config array of IBPArcade Games.
	 * - Add missing quotes around array keys.
	 *
	 * @param string $contents Content of a game config file. Usually a PHP file.
	 */
	public static function fix_array_synthax( $contents ) {
		return preg_replace( '/([a-zA-Z_]\w*)\s*=>/', "'$1' =>", $contents );
	}

	/**
	 * Function to extract and parse the IBPArcadr $config array.
	 *
	 * @param string $contents Content of a game config file. Usually a PHP file.
	 * @return array
	 */
	public static function extract_and_parse_config( $contents ) {

		// Extract the $config array using regular expressions.
		if ( preg_match( '/\$config\s*=\s*array\((.*?)\);/s', $contents, $matches ) ) {
			$config_str = $matches[1];

			// Fix the array syntax by adding quotes around keys.
			$config_str_fixed = preg_replace( '/([a-zA-Z_]\w*)\s*=>/', '"$1":', $config_str );

			// Remove comments to avoid interference with parsing.
			$config_str_fixed = preg_replace( '/\/\*.+?\*\/|\/\/[^\n]+/', '', $config_str_fixed );

			// Remove empty lines.
			$config_str_fixed = preg_replace( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $config_str_fixed );

			// Remove white spaces.
			$config_str_fixed = preg_replace( '/\s+/', ' ', $config_str_fixed );

			// Trim leading and trailing spaces.
			$config_str_fixed = trim( $config_str_fixed );

			// Remove the last comma if it exists.
			$config_str_fixed = rtrim( $config_str_fixed, ',' );

			// Add curly brace to make a json object.
			$config_str_fixed = '{' . $config_str_fixed . '}';

			// Convert the fixed array definition to a PHP array.
			$config_array = json_decode( $config_str_fixed, true );

			// Check if decoding was successful.
			if ( JSON_ERROR_NONE === json_last_error() ) {
				return $config_array;
			} else {
				return array();
			}
		}

		return array();
	}
}
