<?php
/**
 * Translation functions
 *
 * @author Daniel Bakovic <contact@myarcadeplugin.com>
 * @package MyArcadePlugin/Translate
 */

// No direct access.
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Generate a ClientTraceId to uniquely identify the request.
 * Output example: 71af7169-b531-4b23-ad2c-ac87b838aadf
 *
 * @return string
 */
function myarcade_microsoft_guid() {
	return sprintf(
		'%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		wp_rand( 0, 0xffff ),
		wp_rand( 0, 0xffff ),
		wp_rand( 0, 0xffff ),
		wp_rand( 0, 0x0fff ) | 0x4000,
		wp_rand( 0, 0x3fff ) | 0x8000,
		wp_rand( 0, 0xffff ),
		wp_rand( 0, 0xffff ),
		wp_rand( 0, 0xffff )
	);
}

/**
 * Translate a given string with Microsoft Translator
 * https://docs.microsoft.com/de-de/azure/cognitive-services/translator/reference/v3-0-translate
 *
 * @param  string $text Text to translate.
 * @return string|bool Translated string or FALSE on error
 */
function myarcade_microsoft_translate( $text = '' ) {
	global $myarcade_feedback;

	if ( empty( $text ) ) {
		return false;
	}

	$general = get_option( 'myarcade_general' );

	if ( empty( $general['azure_key'] ) ) {
		$myarcade_feedback->add_error( __( 'Microsoft Translator', 'myarcadeplugin' ) . ': ' . __( 'Azure Subscription Key missing!', 'myarcadeplugin' ) );
		return false;
	}

	$body_content = array(
		array(
			'Text' => $text,
		),
	);

	$content = wp_json_encode( $body_content );

	// Build the request URL.
	$url = add_query_arg(
		array(
			'api-version' => '3.0',
			'to'          => $general['translate_to'],
		),
		'https://api.cognitive.microsofttranslator.com/translate'
	);

	$params = array(
		'timeout'   => 45,
		'sslverify' => false,
		'headers'   => array(
			'Content-Type'              => 'application/json',
			'Content-Length'            => strlen( $content ),
			'Ocp-Apim-Subscription-Key' => $general['azure_key'],
			'X-ClientTraceId'           => myarcade_microsoft_guid(),
		),
		'body'      => $content,
	);

	$response      = wp_remote_post( $url, $params );
	$response_body = wp_remote_retrieve_body( $response );
	$response_code = wp_remote_retrieve_response_code( $response );
	$result        = false;

	if ( 200 === $response_code ) {
		$response_object = json_decode( $response_body );

		if ( isset( $response_object[0]->translations[0]->text ) ) {
			$result = $response_object[0]->translations[0]->text;
		}
	} else {
		// Something went wrong.
		$myarcade_feedback->add_error( sprintf( __( 'Microsoft Translator Error Code %d', 'myarcadeplugin' ), $response_code ) );
	}

	return $result;
}

/**
 * Translate a given string with Google Translator
 *
 * @param  string $text Text to translate.
 * @return string|bool Translated string or FALSE on error
 */
function myarcade_google_translate( $text ) {
	global $myarcade_feedback;

	$result = false;

	$general = get_option( 'myarcade_general' );

	if ( ! empty( $general['google_id'] ) ) {

		// build the url for the google request
		// reference documentation: http://code.google.com/intl/de-DE/apis/ajaxlanguage/documentation/reference.html
		// example API v1 - 'http://ajax.googleapis.com/ajax/services/language/translate?v=1.0&q=hello%20world&langpair=en%7Cit'
		// Example API v2 - [ GET https://www.googleapis.com/language/translate/v2?key=INSERT-YOUR-KEY&source=en&target=de&q=Hello%20world ].
		$search  = array( '\\\\\\\"', '\\\\\"', '\\\\n', '\\\\r', '\\\\t', '\\\\$', '\\0', "\\'", '\\\\' );
		$replace = array( '\"', '"', "\n", "\r", "\\t", '\\$', '\0', "'", '\\' );
		$text    = str_replace( $search, $replace, $text );
		add_filter( 'https_ssl_verify', '__return_false' );

		$url = 'https://www.googleapis.com/language/translate/v2?key=' . $general['google_id'] . '&source=en&target=' . $general['google_translate_to'] . '&q=' . rawurlencode( $text );

		// Translate given content.
		$translation = myarcade_get_file( $url );

		if ( ! isset( $translation['error'] ) ) {
			$response = json_decode( $translation['response'] );

			if ( isset( $response->error ) ) {
				$myarcade_feedback->add_error( __( 'Google Translation Error', 'myarcadeplugin' ) . ': ' . $response->error->code . ' - ' . $response->error->message );
			} else {
				// Get translated content.
				if ( isset( $response->data )
					&& isset( $response->data->translations )
					&& isset( $response->data->translations[0]->translatedText ) ) {
					// Get translation.
					$result = $response->data->translations[0]->translatedText;
				} else {
					$myarcade_feedback->add_error( __( 'Google Translation Error', 'myarcadeplugin' ) . ': ' . __( 'Unknown Error', 'myarcadeplugin' ) );
				}
			}
		} else {
			$myarcade_feedback->add_error( __( 'Google Translation Error', 'myarcadeplugin' ) . ': ' . $translation['error'] );
		}
	} else {
		$myarcade_feedback->add_error( __( 'Google Translator - Google API Key not provided', 'myarcadeplugin' ) );
	}

	return $result;
}

/**
 * Translate a given string with Yandex Translator
 *
 * https://api.yandex.com/translate/doc/dg/reference/translate.xml
 * http://code.google.com/p/translate-api/source/browse/trunk/
 *
 * @param  string $text Text to translate.
 * @return string|bool Translated string or FALSE on error
 */
function myarcade_yandex_translate( $text = '' ) {
	global $myarcade_feedback;

	$result = false;

	$general = get_option( 'myarcade_general' );

	if ( ! empty( $general['yandex_key'] ) ) {
		// Build the url for the request.
		$search  = array( '\\\\\\\"', '\\\\\"', '\\\\n', '\\\\r', '\\\\t', '\\\\$', '\\0', "\\'", '\\\\' );
		$replace = array( '\"', '"', "\n", "\r", "\\t", '\\$', '\0', "'", '\\' );
		$text    = str_replace( $search, $replace, $text );

		add_filter( 'https_ssl_verify', '__return_false' );

		$url = 'https://translate.yandex.net/api/v1.5/tr.json/translate?key=' . $general['yandex_key'] . '&lang=en-' . $general['yandex_translate_to'] . '&format=html&text=' . rawurlencode( $text );

		// Translate given content.
		$translation = myarcade_get_file( $url );

		if ( ! isset( $translation['error'] ) ) {
			$response = json_decode( $translation['response'] );

			if ( isset( $response->code ) && 200 !== $response->code ) {
				$status_codes = array(
					200 => __( 'Operation completed successfully', 'myarcadeplugin' ),
					401 => __( 'Invalid API key', 'myarcadeplugin' ),
					402 => __( 'This API key has been blocked', 'myarcadeplugin' ),
					403 => __( 'You have reached the daily limit for requests', 'myarcadeplugin' ),
					404 => __( 'You have reached the daily limit for the volume of translated text', 'myarcadeplugin' ),
					413 => __( 'The text size exceeds the maximum', 'myarcadeplugin' ),
					422 => __( 'The text could not be translated', 'myarcadeplugin' ),
					501 => __( 'The specified translation direction is not supported', 'myarcadeplugin' ),
				);

				$myarcade_feedback->add_error( __( 'Yandex Translation Error', 'myarcadeplugin' ) . ': ' . $status_codes[ $response->code ] );
			} else {
				// Get translated content.
				if ( isset( $response->text ) && isset( $response->text[0] ) ) {
					// Get translation.
					$result = $response->text[0];
				} else {
					$myarcade_feedback->add_error( __( 'Yandex Translation Error', 'myarcadeplugin' ) . ': ' . __( 'Unknown Error', 'myarcadeplugin' ) );
				}
			}
		} else {
			$myarcade_feedback->add_error( __( 'Yandex Translation Error', 'myarcadeplugin' ) . ': ' . $translation['error'] );
		}
	}

	return $result;
}

/**
 * Translate a given text using Mircosoft, Google Translator API, Yandex
 *
 * @param  string $content String to translate.
 * @return string|bool Translated string or FALSE on error
 */
function myarcade_translate( $content ) {
	global $myarcade_feedback;

	// Initialite the result.
	$result = false;

	// Get general settings.
	$general = get_option( 'myarcade_general' );

	switch ( $general['translation'] ) {
		case 'google':
			$result = myarcade_google_translate( $content );
			break;

		case 'microsoft':
			$result = myarcade_microsoft_translate( $content );
			break;

		case 'yandex':
			$result = myarcade_yandex_translate( $content );
			break;

		default:
			$myarcade_feedback->add_error( __( 'Translation service not selected!', 'myarcadeplugin' ) );
	}

	if ( empty( $result ) ) {
		$result = false;
	}

	return $result;
}
