<?php
/**
 * Import games AJAX Handler
 * Handles file uploads for each game type
 *
 * @author  Daniel Bakovic <contact@myarcadeplugin.com>
 * @package MyArcade/Game/Imprt
 */

// No direct access.
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

// Check user.
if ( function_exists( 'current_user_can' ) && ! current_user_can( 'edit_posts' ) ) {
	die();
}

// Load required WordPress files.
require_once ABSPATH . 'wp-admin/includes/file.php';

// Define required constants for PHPBB and IBPArcade Games.
define( 'IN_PHPBB_ARCADE', true );
define( 'IN_PHPBB', true );

define( 'AMOD_GAME', 1 );
define( 'IBPRO_GAME', 2 );
define( 'V3ARCADE_GAME', 3 );
define( 'IBPROV3_GAME', 4 );
define( 'ARCADELIB_GAME', 5 );
define( 'NOSCORE_GAME', 6 );
define( 'AR_GAME', 7 );
define( 'PHPBBARCADE_GAME', 8 );

define( 'GAME_CONTROL_KEYBOARD_MOUSE', 1 );
define( 'GAME_CONTROL_KEYBOARD', 2 );
define( 'GAME_CONTROL_MOUSE', 3 );
define( 'UNKNOWN_GAME', 4 );

define( 'SCORETYPE_HIGH', 'high' );
define( 'SCORETYPE_LOW', 'low' );

// Courtesy of php.net, the strings that describe the error indicated in $_FILES[{form field}]['error'].
$upload_error_strings = array(
	false,
	__( 'The uploaded file exceeds the upload_max_filesize directive in php.ini.' ),
	__( 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.', 'myarcadeplugin' ),
	__( 'The uploaded file was only partially uploaded.', 'myarcadeplugin' ),
	__( 'No file was uploaded.', 'myarcadeplugin' ),
	'',
	__( 'Missing a temporary folder.', 'myarcadeplugin' ),
	__( 'Failed to write file to disk.', 'myarcadeplugin' ),
	__( 'File upload stopped by extension.', 'myarcadeplugin' ),
);

$upload_dir = MyArcade()->upload_dir();

$game           = new stdClass();
$game->info_dim = '';
$game->error    = '';

$result = false;

$upload_action = filter_input( INPUT_POST, 'upload' );
$gameurl       = filter_input( INPUT_POST, 'gameurl' );
$fileselectswf = filter_input( INPUT_POST, 'fileselectswf' );
$thumburl      = filter_input( INPUT_POST, 'thumburl' );

// Check the submission.
switch ( $upload_action ) {

	// Upload SWF / DCR File.
	case 'swf':
		if ( ! empty( $_FILES['gamefile']['name'] ) ) {
			// Error check.
			if ( ! empty( $_FILES['gamefile']['error'] ) ) {
				$game->error = $upload_error_strings[ intval( $_FILES['gamefile']['error'] ) ];
			} else {
				$file_temp = esc_html( $_FILES['gamefile']['tmp_name'] );
				$file_info = pathinfo( esc_html( $_FILES['gamefile']['name'] ) );
				// generate new file name.
				$upload_dir_specific = myarcade_get_folder_path( $file_info['filename'], 'custom' );
				$file_name           = wp_unique_filename( $upload_dir_specific['gamesdir'], $file_info['basename'] );
				$result              = move_uploaded_file( $file_temp, $upload_dir_specific['gamesdir'] . $file_name );
				// Delete temp file.
				unlink( esc_html( $_FILES['gamefile']['tmp_name'] ) );
			}
		} elseif ( $gameurl ) {
			// grab from net?
			$file_temp = myarcade_get_file( $gameurl );

			if ( ! empty( $file_temp['error'] ) ) {
				// Get error message.
				$game->error = $file_temp['error'];
			} else {
				$file_info           = pathinfo( $gameurl );
				$upload_dir_specific = myarcade_get_folder_path( $file_info['filename'], 'custom' );
				$file_name           = wp_unique_filename( $upload_dir_specific['gamesdir'], $file_info['basename'] );
				$result              = file_put_contents( $upload_dir_specific['gamesdir'] . $file_name, $file_temp['response'] );
			}
		} elseif ( $fileselectswf ) {
			$full_abs_path = $upload_dir['gamesdir'] . '/uploads/swf/' . $fileselectswf;

			if ( ! file_exists( $full_abs_path ) ) {
				$game->error = __( "Can't find the selected file.", 'myarcadeplugin' );
			} else {
				$file_info           = pathinfo( $fileselectswf );
				$upload_dir_specific = myarcade_get_folder_path( $file_info['filename'], 'custom' );
				$file_name           = wp_unique_filename( $upload_dir_specific['gamesdir'], $file_info['basename'] );
				$result              = rename( $full_abs_path, $upload_dir_specific['gamesdir'] . $file_name );
			}
		} else {
			$result = false;
		}

		if ( empty( $game->error ) ) {

			if ( true === $result ) {
				// Get the file extension.
				if ( 'dcr' === strtolower( $file_info['extension'] ) ) {
					$game->type = 'dcr';
				} else {
					$game->type = 'custom';
				}

				$game->name         = ucfirst( $file_info['filename'] );
				$game->location_abs = $upload_dir_specific['gamesdir'] . $file_name;
				$game->location_url = $upload_dir_specific['gamesurl'] . $file_name;

				// try to detect dimensions.
				$game_dimensions = @getimagesize( $game->location_abs );
				$game->width     = intval( $game_dimensions[0] );
				$game->height    = intval( $game_dimensions[1] );
				$game->info_dim  = 'Game dimensions: ' . $game->width . 'x' . $game->height;

				if ( empty( $game->width ) || empty( $game->height ) ) {
					$game->width    = 0;
					$game->height   = 0;
					$game->info_dim = 'Can not detect game dimensions';
				}

				// Try to get the game name.
				$name           = explode( '.', $game->name );
				$game->realname = ucfirst( str_replace( '_', ' ', $name[0] ) );
			} else {
				$game->error = __( 'Can not upload file!', 'myarcadeplugin' );
			}
		}
		break;

	// Upload Game Thumb.
	case 'thumb':
		if ( ! empty( $_FILES['thumbfile']['name'] ) ) {
			// Error check.
			if ( ! empty( $_FILES['gamefile']['error'] ) ) {
				$game->error = $upload_error_strings[ intval( $_FILES['gamefile']['error'] ) ];
			} else {
				$file_temp = $_FILES['thumbfile']['tmp_name'];
				$file_info = pathinfo( $_FILES['thumbfile']['name'] );
				// generate new file name.
				$upload_dir_specific = myarcade_get_folder_path( $file_info['filename'], 'custom' );
				$file_name           = wp_unique_filename( $upload_dir_specific['thumbsdir'], $file_info['basename'] );
				$result              = move_uploaded_file( $file_temp, $upload_dir_specific['thumbsdir'] . $file_name );
				// Delete temp file.
				@unlink( $_FILES['thumbfile']['tmp_name'] );
			}
		} elseif ( $thumburl ) {
			// grab from net?
			$file_temp = myarcade_get_file( $thumburl );

			if ( ! empty( $file_temp['error'] ) ) {
				// Get error message.
				$game->error = $file_temp['error'];
			} else {
				$file_info           = pathinfo( $thumburl );
				$upload_dir_specific = myarcade_get_folder_path( $file_info['filename'], 'custom' );
				$file_name           = wp_unique_filename( $upload_dir_specific['thumbsdir'], $file_info['basename'] );
				$result              = file_put_contents( $upload_dir_specific['thumbsdir'] . $file_name, $file_temp['response'] );
			}
		}

		if ( empty( $game->error ) ) {
			if ( true === $result ) {
				$game->thumb_name = $file_name;
				$game->thumb_url  = $upload_dir_specific['thumbsurl'] . $file_name;
				$game->thumb_id   = myarcade_add_attachment( $game->thumb_url, $upload_dir_specific['thumbsdir'] . $file_name );
			} else {
				$game->error = 'Can not upload thumbnail!';
			}
		}
		break;

	// Upload Game Screenshots.
	case 'screen':
		for ( $i = 0; $i <= 3; $i++ ) {
			$screenshot = 'screen' . $i;
			$result     = false;

			if ( ! empty( $_FILES[ $screen ]['name'] ) ) {
				// Error check.
				if ( ! empty( $_FILES[ $screen ]['error'] ) ) {
					$game->error = $upload_error_strings[ $_FILES[ $screenshot ]['error'] ];
				} else {
					// There is a screen to upload.
					$file_temp           = $_FILES[ $screenshot ]['tmp_name'];
					$file_info           = pathinfo( $_FILES[ $screenshot ]['name'] );
					$upload_dir_specific = myarcade_get_folder_path( $file_info['filename'], 'custom' );
					$file_name           = wp_unique_filename( $upload_dir_specific['thumbsdir'], $file_info['basename'] );
					$result              = move_uploaded_file( $file_temp, $upload_dir_specific['thumbsdir'] . $file_name );
					// Delete temp file.
					@unlink( $_FILES[ $screenshot ]['tmp_name'] );
				}
			} elseif ( ! empty( $_POST[ $screenshot . 'url' ] ) ) {
				// There is a screen to grab.
				$file_temp = myarcade_get_file( $_POST [ $screenshot . 'url' ] );

				if ( ! empty( $file_temp['error'] ) ) {
					// Get error message.
					$game->error = $file_temp['error'];
				} else {
					$file_info           = pathinfo( $_POST[ $screen . 'url' ] );
					$upload_dir_specific = myarcade_get_folder_path( $file_info['filename'], 'custom' );
					$file_name           = wp_unique_filename( $upload_dir_specific['thumbsdir'], $file_info['basename'] );
					$result              = file_put_contents( $upload_dir_specific['thumbsdir'] . $file_name, $file_temp['response'] );
				}
			}

			if ( true === $result ) {
				$game->screen_abs[ $i ]   = $upload_dir_specific['thumbsdir'] . $file_name;
				$game->screen_url[ $i ]   = $upload_dir_specific['thumbsurl'] . $file_name;
				$game->screen_name[ $i ]  = $file_name;
				$game->screen_error[ $i ] = 'OK';
			} else {
				$game->screen_error[ $i ] = 'Upload Failed For Screen No. '. ( $i + 1 ) . ' ' . $game->error;
				$game->screen_abs[ $i ]   = '';
				$game->screen_url[ $i ]   = '';
				$game->screen_name[ $i ]  = '';
			}
		}
		break;

	// Upload IBPArcade Game.
	case 'tar':
		// Include the tar handler class.
		require_once MyArcade()->plugin_path() . '/core/tar.php';

		if ( class_exists( 'tar' ) ) {

			if ( ! empty( $_FILES['tarfile']['name'] ) ) {
				// Error check.
				if ( ! empty( $_FILES['tarfile']['error'] ) ) {
					$game->error = $upload_error_strings[ intval( $_FILES['tarfile']['error'] ) ];
				} else {
					$file_temp = $_FILES['tarfile']['tmp_name'];
					$tarname   = $_FILES['tarfile']['name'];
					$file_abs  = $upload_dir['gamesdir'] . $tarname;
					// Put the uploaded file into the working directory.
					$result = @rename( $file_temp, $file_abs );
				}
			} elseif  ( ! empty( $_POST['tarurl'] ) ) {
				// grab from net?
				$file_temp = myarcade_get_file( $_POST['tarurl'] );

				if ( ! empty( $file_temp['error'] ) ) {
					// Get error message.
					$game->error = $file_temp['error'];
				} else {
					$tarname   = basename( $_POST['tarurl'] );
					$file_abs  = $upload_dir['gamesdir'] . $tarname;
					$result    = file_put_contents( $file_abs, $file_temp['response'] );
				}
			} elseif ( ! empty( $_POST['fileselectibparcade'] ) ) {
				$full_abs_path = $upload_dir['gamesdir'] . '/uploads/ibparcade/' . $_POST['fileselectibparcade'];

				if ( ! file_exists( $full_abs_path ) ) {
					$game->error = __("Can't find the selected file.", 'myarcadeplugin' );
				} else {
					$tarname  = $_POST['fileselectibparcade'];
					$file_abs = $upload_dir['gamesdir'] . $tarname;
					// Put the uploaded file into the working directory.
					$result = @rename( $full_abs_path, $file_abs );
				}
			} else {
				$result = false;
			}

			if ( empty( $game->error ) ) {
				if ( true === $result ) {
					$tar_handle = new tar();
					$tar_handle->new_tar( $upload_dir['gamesdir'], $tarname );
					$tar_filelist = $tar_handle->list_files();

					// Get the config file.
					foreach ( $tar_filelist as $filename ) {
						if ( preg_match( '/(.*)(.php)$/i', $filename , $filematch ) ) {
							break;
						}
					}

					if ( ! empty( $filematch ) ) {
						$configfile = $filematch[0];

						// Extract all files into the working directory.
						$tar_handle->extract_files( $upload_dir['gamesdir'] );

						// Include the game config file.
						if ( file_exists( $upload_dir['gamesdir'] . $configfile ) ) {

							require_once $upload_dir['gamesdir'] . $configfile;

							// Check if we have already uploaded this game before.
							global $wpdb;
							$duplicate_game = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}myarcadegames WHERE slug = %s",$config['gname'] ) );

							if ( ! $duplicate_game ) {
								$duplicate_game = $wpdb->get_var( $wpdb->prepare( "
									SELECT m.post_id FROM {$wpdb->postmeta} AS m
									INNER JOIN {$wpdb->posts} AS p ON m.post_id = p.ID
									WHERE m.meta_key = 'mabp_game_slug' AND m.meta_value = %s", $config['gname'] )
								);
							}

							if ( $duplicate_game ) {
								// Seems to be a duplicate game...
								// Clean up.
								foreach ( $tar_filelist as $file_name ) {
									$file = $upload_dir['gamesdir'] . $file_name;
									if ( file_exists( $file ) ) {
										unlink( $file );
									}
								}

								// Delete tar file.
								unlink( $upload_dir['gamesdir'] . $tarname );

								$game->error = __( 'This game already exists in your database!', 'myarcadeplugin' );
							} else {
								// It looks like a new game...
								$upload_dir_specific = myarcade_get_folder_path( $config['gname'], 'ibparcade' );

								// Check if this is a Flash or HTML5 game.
								$swf_file    = stripslashes( $config['gname'] ) . '.swf';
								$index_file  = 'gamedata/' . stripslashes( $config['gname'] ) . '/index.html';

								if ( file_exists( $upload_dir['gamesdir'] . $swf_file ) ) {
									// Handle the Falsh file (swf).
									$swf_file_name = wp_unique_filename( $upload_dir_specific['gamesdir'], $swf_file );

									@chmod( $upload_dir['gamesdir'] . $swf_file, 0644 );
									@rename( $upload_dir['gamesdir'] . $swf_file, $upload_dir_specific['gamesdir'] . $swf_file_name );

									$game->type         = 'ibparcade';
									$game->location_url = $upload_dir_specific['gamesurl'] . $swf_file_name;
								} elseif ( file_exists( $upload_dir['gamesdir'] . $index_file ) ) {
									$game->location_url = $upload_dir['gamesurl'] . $index_file;
									$game->type         = 'iframe';
								} else {
									// Clean up.
									foreach ( $tar_filelist as $file_name ) {
										$file = $upload_dir['gamesdir'] . $file_name;
										if ( file_exists( $file ) ) {
											unlink( $file );
										}
									}

									// Delete tar file.
									unlink( $upload_dir['gamesdir'] . $tarname );

									$game->error = __( "Can't find required game files.", 'myarcadeplugin' );
									break;
								}

								// Handle Thumbnail file.
								$thumb_file      = stripslashes( $config['gname'] ) . '1.gif';
								$thumb_file_name = wp_unique_filename( $upload_dir_specific['thumbsdir'], stripslashes( $config['gname'] ) . '.gif' );

								@chmod( $upload_dir['gamesdir'] . $thumb_file, 0777 );
								@rename( $upload_dir['gamesdir'] . $thumb_file, $upload_dir_specific['thumbsdir'] . $thumb_file_name );

								// Delete the second thumb.
								$thumb_file2 = stripslashes( $config['gname'] ).'2.gif';

								if ( file_exists( $upload_dir['gamesdir'] . $thumb_file2 ) ) {
									@chmod( $upload_dir['gamesdir'] . $thumb_file2, 0777 );
									@unlink( $upload_dir['gamesdir'] . $thumb_file2 );
								}

								// Delete the uploaded tar file.
								if ( file_exists( $upload_dir['gamesdir'] . $tarname ) ) {
									@chmod( $upload_dir['gamesdir'] . $tarname, 0777 );
									@unlink( $upload_dir['gamesdir'] . $tarname );
								}

								$game->name          = ucfirst( $tarname );
								$game->thumbnail_url = $upload_dir_specific['thumbsurl'] . $thumb_file_name;
								$game->thumnail_id   = myarcade_add_attachment( $game->thumbnail_url, $upload_dir_specific['thumbsdir'] . $thumb_file_name );

								// try to detect dimensions.
								$game->width    = stripslashes( $config['gwidth'] );
								$game->height   = stripslashes( $config['gheight'] );
								$game->info_dim = 'Game dimensions: ' . $game->width . 'x' . $game->height;

								if ( empty( $game->width ) || empty( $game->height ) ) {
									$game->width    = 0;
									$game->height   = 0;
									$game->info_dim = __( 'Can not detect game dimensions', 'myarcadeplugin' );
								}

								// Try to get the game name.
								$game->realname     = ucfirst( stripslashes( $config['gtitle'] ) );
								$game->slug         = $config['gname'];
								$game->description  = stripslashes( $config['gwords'] . ' ' . $config['object'] );
								$game->instructions = stripslashes( $config['gkeys'] );

								if ( ! empty( $config['highscore_type'] ) ) {
									$game->highscore_type      = stripslashes( $config['highscore_type'] );
									$game->leaderboard_enabled = '1';
								} else {
									$game->highscore_type      = 'high';
									$game->leaderboard_enabled = '0';
								}

								// Try to determinate game categories.
								$ibparcade_categories = array(
									1  => 'Action',
									3  => 'Other',
									5  => 'Casino',
									8  => 'Puzzles',
									9  => 'Shooting',
									10 => 'Sports',
									11 => 'Driving',
									18 => 'Board Game',
									19 => 'Puzzles',
								);

								if ( isset( $config['gcat'] ) && is_numeric( $config['gcat'] ) ) {
									if ( isset( $ibparcade_categories[ $config['gcat'] ] ) ) {

										// Get available WP categories.
										$general = get_option( 'myarcade_general' );

										if ( 'post' !== MyArcade()->get_post_type() && ! empty( $general['custom_category'] ) && taxonomy_exists( $general['custom_category'] ) ) {
											$game_taxonomy = $general['custom_category'];
										} else {
											$game_taxonomy = 'category';
										}

										$categories = get_terms( $game_taxonomy, array( 'hide_empty' => false ) );

										foreach ( $categories as $game_term ) {
											if ( $ibparcade_categories[ $config['gcat'] ] == $game_term->name ) {
												$game->categs = $game_term->term_id;
												break;
											}
										}
									}
								}

								// Delete the config file.
								@chmod( $upload_dir['gamesdir'] . $configfile, 0777 );
								@unlink( $upload_dir['gamesdir'] . $configfile );
							}
						} else {
							$game->error = __( 'Config file not found.', 'myarcadeplugin' );
						}
					} else {
						$game->error = __( 'Can not get the config file.', 'myarcadeplugin' );
					}
				} else {
					$game->error = __( 'Can not upload file!', 'myarcadeplugin' );
				}
			}
		} else {
			$game->error = __( 'Can not include the tar class.', 'myarcadeplugin' );
		}
		break;

	// Upload PHPBB Game.
	case 'phpbb':
		if ( ! empty( $_FILES['zipfile']['name'] ) ) {
			// Error check.
			if ( ! empty( $_FILES['zipfile']['error'] ) ) {
				$game->error = $upload_error_strings[ intval( $_FILES['zipfile']['error'] ) ];
			} else {
				$file_temp = $_FILES['zipfile']['tmp_name'];
				$zipname   = $_FILES['zipfile']['name'];
				$file_abs  = $upload_dir['gamesdir'] . $zipname;
				// Put the uploaded file into the working directory.
				$result = @rename( $file_temp, $file_abs );
			}
		} elseif ( ! empty( $_POST['zipurl'] ) ) {
			// grab from net?
			$file_temp = myarcade_get_file( $_POST['zipurl'] );

			if ( ! empty( $file_temp['error'] ) ) {
				// Get error message.
				$game->error = $file_temp['error'];
			} else {
				$zipname  = basename( $_POST['zipurl'] );
				$file_abs = $upload_dir['gamesdir'] . $zipname;
				$result   = file_put_contents( $file_abs, $file_temp['response'] );
			}
		} elseif ( ! empty( $_POST['fileselectphpbb'] ) ) {
			$full_abs_path = $upload_dir['gamesdir'] . '/uploads/phpbb/' . $_POST['fileselectphpbb'];

			if ( ! file_exists( $full_abs_path ) ) {
				$game->error = __( "Can't find the selected file.", 'myarcadeplugin' );
			} else {
				$zipname  = $_POST['fileselectphpbb'];
				$file_abs = $upload_dir['gamesdir'] . $zipname;

				// Put the uploaded file into the working directory.
				$result = @rename( $full_abs_path, $file_abs );
			}
		} else {
			$result = false;
		}

		if ( true === $result ) {
			// Extract the zip file.
			require_once ABSPATH . 'wp-admin/includes/class-pclzip.php';
			$archive  = new PclZip( $file_abs );
			$contents = $archive->listContent();
			$images   = array( 'png', 'jpg', 'gif', 'bmp' );

			$screenshots = array();
			$game->type = 'phpbb';

			// Check if this is a Mochi ZIP.
			$mochi_game        = false;
			$phpbb_config_file = false;

			foreach ( $contents as $content ) {
				// ignore folders and gamedata.
				if ( false === $content['folder'] || strpos( $content['filename'], 'gamedata' ) === false ) {
					$path_parts = pathinfo( $content['filename'] );

					if ( strpos( $content['filename'], '__metadata__.json' ) !== false ) {
						// This seems to be a Mochi game.
						$mochi_game        = true;
						$mochi_config_file = $content['filename'];
						$game->type        = 'mochi';
					} elseif ( strpos( $content['filename'], '.php' ) !== false ) {
						$phpbb_config_file = $content['filename'];
					} elseif ( strpos( $content['filename'], '_thumb_100x100.' ) !== false ) {
						$thumb_file = $content['filename'];
					} elseif ( 'swf' === $path_parts['extension'] ) {
						$swf_file = $content['filename'];
					} else {
						switch ( $path_parts['filename'] ) {
							case 'screen1':
							case 'screen2':
							case 'screen3':
							case 'screen4':
								$screenshots[] = $content['filename'];
								break;
						}
					}
				}
			}

			// PHPBB game?
			if ( ! $mochi_game ) {
				// find needed files.
				foreach ( $contents as $content ) {
					if ( false === $content['folder'] && strpos( $content['filename'], 'gamedata') === false ) {
						$ext = pathinfo( $content['filename'], PATHINFO_EXTENSION );

						// Get the thumbnail.
						if ( in_array( $ext, $images ) ) {
							$thumb_file = $content['filename'];
						} elseif ( 'swf' === $ext ) {
							$swf_file = $content['filename'];
						}
					}
				}
			}

			if ( $phpbb_config_file ) {
				$zip_game_file = basename( $zipname , '.' . substr( strrchr( $zipname, '.' ), 1 ) );

				// Check if we have already uploaded this game before.
				global $wpdb;
				$duplicate_game = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}myarcadegames WHERE slug = %s AND game_type = 'phpbb'", $zip_game_file ) );

				if ( ! $duplicate_game ) {
					$duplicate_game = $wpdb->get_var( $wpdb->perpare( "
						SELECT m.post_id FROM {$wpdb->postmeta} AS m
						INNER JOIN {$wpdb->posts} AS p ON m.post_id = p.ID
						WHERE m.meta_key = 'mabp_game_slug' AND m.meta_value = %s
						AND m.meta_key = 'mabp_game_type' AND m.meta_value = 'phpbb'
						LIMIT 1", $zip_game_file )
					);
				}

				if ( $duplicate_game ) {
					// Seems to be a duplicate game.
					// Delete zip file.
					unlink( $upload_dir['gamesdir'] . $zipname );

					$game->error = esc_html__( 'This game already exists in your database!', 'myarcadeplugin' );

					// Leave the case.
					break;
				}
			}

			if ( $mochi_game || ( isset( $thumb_file ) && isset( $swf_file ) ) ) {
				// Proceed with the import.
				$clean_file_name = myarcade_make_slug( pathinfo( $swf_file, PATHINFO_FILENAME ) );

				// Extract files.
				if ( $archive->extract( PCLZIP_OPT_PATH, $upload_dir['gamesdir'] ) ) {

					if ( $mochi_game ) {
						if ( file_exists( $upload_dir['gamesdir'] . $mochi_config_file ) ) {
							$mochi_json = file_get_contents( $upload_dir['gamesdir'] . $mochi_config_file );

							if ( ! $mochi_json ) {
								$game->error = 'Required Mochi config file not found.';
							}

							$mochi_config = json_decode( $mochi_json );

							$game->name         = esc_sql( $mochi_config->name );
							$game->description  = esc_sql( $mochi_config->description );
							$game->instructions = esc_sql( $mochi_config->instructions );
							$game->game_tag     = $mochi_config->game_tag;
							$game->tags         = esc_sql( implode( ',', $mochi_config->tags ) );
							$game->width        = intval( $mochi_config->width );
							$game->height       = intval( $mochi_config->height );
							$game->categs       = false;

							if ( ! empty( $mochi_config->category ) ) {
								$cat_id = get_cat_ID( $mochi_config->category );
								if ( $cat_id ) {
									$game->categs = $cat_id;
								}
							} else {
								if ( isset( $mochi_config->categories[0] ) ) {
									$cat_id = get_cat_ID( $mochi_config->category[0] );
									if ( $cat_id ) {
										$game->categs = $cat_id;
									}
								}
							}

							if ( ! empty( $mochi_config->slug ) ) {
								$clean_file_name = $mochi_config->slug;
							}
						} else {
							$game->error = __( 'Required Mochi config file not found.', 'myarcadeplugin' );
						}
					} elseif ( $phpbb_config_file ) {
						// Define a required var.
						$phpEx = '';

						// A PHPBB config has been found. Try to get details from file.
						include_once $upload_dir['gamesdir'] . $phpbb_config_file;

						if ( ! empty( $game_data ) ) {
							$thumb_file      = $zip_game_file . '1.gif';
							$clean_file_name = pathinfo( $swf_file, PATHINFO_FILENAME );

							$game->name         = $game_data['game_name'];
							$game->slug         = $clean_file_name;
							$game->description  = $game_data['game_desc'];
							$game->instructions = $game_data['game_control_desc'];
							$game->width        = intval( $game_data['game_width'] );
							$game->height       = intval( $game_data['game_height'] );

							if ( NOSCORE_GAME !== $game_data['game_type'] ) {
								$game->highscore_type      = $game_data['game_scoretype'];
								$game->leaderboard_enabled = '1';
							} else {
								$game->highscore_type      = 'high';
								$game->leaderboard_enabled = '0';
							}
						} else {
							$game->error = __( 'Invalid PHPBB config file!', 'myarcadeplugin' );
							// Leave the case.
							break;
						}
					} else {
						$game->name = ucfirst( pathinfo( $clean_file_name, PATHINFO_FILENAME ) );
						$game->slug = $clean_file_name;
						// try to detect dimensions.
						$game_dimensions = getimagesize( $upload_dir['gamesdir'] . $swf_file );
						$game->width     = intval( $game_dimensions[0] );
						$game->height    = intval( $game_dimensions[1] );
					}

					$upload_dir_specific = myarcade_get_folder_path( $clean_file_name, $game->type );
					$swf_file_name       = wp_unique_filename( $upload_dir_specific['gamesdir'], $clean_file_name . '.swf' );

					@rename( $upload_dir['gamesdir'] . $swf_file, $upload_dir_specific['gamesdir'] . $swf_file_name );

					$thumb_file_name = wp_unique_filename( $upload_dir_specific['thumbsdir'], $clean_file_name . '.' . pathinfo( $thumb_file, PATHINFO_EXTENSION ) );

					@rename( $upload_dir['gamesdir'] . $thumb_file, $upload_dir_specific['thumbsdir'] . $thumb_file_name );

					// Screenshots.
					$i = 0;
					foreach ( $screenshots as $screenshot ) {
						$i++;
						$screen_file_name = wp_unique_filename( $upload_dir_specific['thumbsdir'], $clean_file_name . '_screen' . $i . '.' . pathinfo( $screenshot, PATHINFO_EXTENSION ) );

						@rename( $upload_dir['gamesdir'] . $screenshot, $upload_dir_specific['thumbsdir'] . $screen_file_name );

						$screen_nr        = 'screen' . $i . '_url';
						$game->$screen_nr = $upload_dir_specific['thumbsurl'] . $screen_file_name;
					}

					$game->location_url  = $upload_dir_specific['gamesurl'] . $swf_file_name;
					$game->thumbnail_url = $upload_dir_specific['thumbsurl'] . $thumb_file_name;
					$game->thumnail_id   = myarcade_add_attachment( $game->thumbnail_url, $upload_dir_specific['thumbsdir'] . $thumb_file_name );

					if ( empty( $game->width ) || empty( $game->height ) ) {
						$game->width    = 0;
						$game->height   = 0;
						$game->info_dim = __( 'Can not detect game dimensions', 'myarcadeplugin' );
					} else {
						$game->info_dim = 'Game dimensions: ' . $game->width . 'x' . $game->height;
					}

					// Try to get the game name.
					$game->realname = $game->name;
				} else {
					// Error.
					$game->error = $archive->errorInfo();
				}
			} else {
				// Thumbnail and/or Swf file not found.
				$game->error = 'Invalid ZIP file';
			}

			// Clean up.
			foreach ( $contents as $content ) {
				// Don't delete gamedata content.
				if ( strpos( $content['filename'], 'gamedata' ) === false ) {
					$file = $upload_dir['gamesdir'] . $content['filename'];
					if ( file_exists( $file ) ) {
						@unlink( $file );
					}
				}
			}

			// Check if the file has been extracted in a subfolder and delete it.
			if ( strpos( $swf_file, '/' ) !== false ) {
				$paths = explode( '/', $swf_file );

				foreach ( $paths as $folder ) {
					if ( file_exists( $upload_dir['gamesdir'] . $folder ) ) {
						@rmdir( $upload_dir['gamesdir'] . $folder );
					}
				}
			}

			// Remove the zip file.
			@chmod( $file_abs, 0777 );
			@unlink( $file_abs );
		} else {
			$game->error = 'Can not upload file!';
		}
		break;

	// Import Embed / Iframe Code.
	case 'emif':
		if ( ! empty( $_POST['embedcode'] ) ) {
			$game_code = filter_input( INPUT_POST, 'embedcode' );

			// Check the code.
			if ( filter_var( $game_code, FILTER_VALIDATE_URL ) ) {
				$game->type = 'iframe';
			} else {
				$game->type = 'embed';
			}

			$game->importgame = urlencode( str_replace( '"', '\'', $game_code ) );
			$game->result     = 'OK';
		} else {
			$game->error = 'No embed code entered!';
		}
		break;

	case 'unity':
		if ( ! empty( $_FILES['unityfile']['name'] ) ) {
			// Error check.
			if ( ! empty( $_FILES['unityfile']['error'] ) ) {
				$game->error = $upload_error_strings[ intval( $_FILES['unityfile']['error'] ) ];
			} else {
				$file_temp = $_FILES['unityfile']['tmp_name'];
				$file_info = pathinfo( $_FILES['unityfile']['name'] );

				// generate new file name.
				$upload_dir_specific = myarcade_get_folder_path( $file_info['filename'], 'unity' );
				$file_name           = wp_unique_filename( $upload_dir_specific['gamesdir'], $file_info['basename'] );
				$result              = move_uploaded_file( $file_temp, $upload_dir_specific['gamesdir'] . $file_name );
				// Delete temp file.
				@unlink( $_FILES['unityfile']['tmp_name'] );
			}
		} elseif ( ! empty( $_POST['unityurl'] ) ) {
			// grab from net?
			$file_temp = myarcade_get_file( $_POST['unityurl'] );

			if ( ! empty( $file_temp['error'] ) ) {
				// Get error message.
				$game->error = $file_temp['error'];
			} else {
				$file_info           = pathinfo( $_POST['unityurl'] );
				$upload_dir_specific = myarcade_get_folder_path( $file_info['filename'], 'unity' );
				$file_name           = wp_unique_filename( $upload_dir_specific['gamesdir'], $file_info['basename'] );
				$result              = file_put_contents( $upload_dir_specific['gamesdir'] . $file_name, $file_temp['response'] );
			}
		} elseif ( ! empty( $_POST['fileselectunity'] ) ) {
			$full_abs_path = $upload_dir['gamesdir'] . '/uploads/unity/' . $_POST['fileselectunity'];

			if ( ! file_exists( $full_abs_path ) ) {
				$game->error = __( "Can't find the selected file.", 'myarcadeplugin' );
			} else {
				$file_info           = pathinfo( $_POST['fileselectunity'] );
				$upload_dir_specific = myarcade_get_folder_path( $file_info['filename'], 'unity' );
				$file_name           = wp_unique_filename( $upload_dir_specific['gamesdir'], $file_info['basename'] );
				$result              = rename( $full_abs_path, $upload_dir_specific['gamesdir'] . $file_name );
			}
		} else {
			$result = false;
		}

		if ( empty( $game->error ) ) {
			if ( true === $result ) {

				$game->type         = 'unity';
				$game->name         = ucfirst( $file_info['filename'] );
				$game->location_abs = $upload_dir_specific['gamesdir'] . $file_name;
				$game->location_url = $upload_dir_specific['gamesurl'] . $file_name;

				// Try to get the game name.
				$name           = explode( '.', $game->name );
				$game->realname = ucfirst( str_replace( '_', ' ', $name[0] ) );
			} else {
				$game->error = 'Can not upload file!';
			}
		}
		break;

	case 'html5':
		if ( ! empty( $_FILES['html5file']['name'] ) ) {
			// Error check.
			if ( ! empty( $_FILES['html5file']['error'] ) ) {
				$game->error = $upload_error_strings[ $_FILES['html5file']['error'] ];
			} else {
				$file_temp = $_FILES['html5file']['tmp_name'];
				$zipname   = $_FILES['html5file']['name'];
				$file_abs  = $upload_dir['gamesdir'] . $zipname;
				// Put the uploaded file into the working directory.
				$result = @rename( $file_temp, $file_abs );
			}
		} elseif ( ! empty( $_POST['html5url'] ) ) {
			// grab from net?
			$file_temp = myarcade_get_file( $_POST['html5url'] );

			if ( ! empty( $file_temp['error'] ) ) {
				// Get error message.
				$game->error = $file_temp['error'];
			} else {
				$zipname  = basename( $_POST['html5url'] );
				$file_abs = $upload_dir['gamesdir'] . $zipname;
				$result   = file_put_contents( $file_abs, $file_temp['response'] );
			}
		} elseif ( ! empty( $_POST['fileselecthtml5'] ) ) {
			$full_abs_path = $upload_dir['gamesdir'] . '/uploads/html5/' . $_POST['fileselecthtml5'];

			if ( ! file_exists( $full_abs_path ) ) {
				$game->error = __( "Can't find the selected file.", 'myarcadeplugin' );
			} else {
				$zipname  = $_POST['fileselecthtml5'];
				$file_abs = $upload_dir['gamesdir'] . $zipname;
				// Put the uploaded file into the working directory.
				$result = @rename( $full_abs_path, $file_abs );
			}
		} else {
			$result = false;
		}

		if ( true === $result ) {
			require_once ABSPATH . 'wp-admin/includes/class-pclzip.php';

			$zip           = new PclZip( $file_abs );
			$zip_content   = $zip->listContent();

			if ( ! $zip_content ) {
				// Error. Zip file seems to be incorrect or damaged
				$game->error = __( "Can't extract this file.", 'myarcadeplugin' );
				@unlink( $file_abs );
				break;
			}

			$zip_file_name = pathinfo( $file_abs, PATHINFO_FILENAME );

			// Try to locate the top level index.html.
			$index_file         = array_search( 'index.html', array_column( $zip_content, 'filename' ) );
			$sub_directory      = $zip_file_name . '/';
			$destination_folder = $sub_directory;

			if ( false === $index_file ) {
				// No top level index.html found. Try to locate it within a folder.
				// Get the folder name.
				$folder_index = array_search( true, array_column( $zip_content, 'folder' ) );

				if ( false === $folder_index ) {
					// Error. We can't import this game.
					$game->error = __( "Can't find index.html.", 'myarcadeplugin' );
					@unlink( $file_abs );
					break;
				}

				if ( empty( $zip_content[ $folder_index ]['filename'] ) ) {
					// Error. We can't import this game.
					$game->error = __( "Can't find index.html.", 'myarcadeplugin' );
					@unlink( $file_abs );
					break;
				}

				$index_file = array_search( $zip_content[ $folder_index ]['filename'] . 'index.html', array_column( $zip_content, 'filename' ) );

				if ( false === $index_file ) {
					// Error. We can't import this game.
					$game->error = __( "Can't find index.html.", 'myarcadeplugin' );
					@unlink( $file_abs );
					break;
				}

				// Replace the subdirectory.
				$sub_directory      = '';
				$destination_folder = $zip_content[ $folder_index ]['filename'];
			}

			$upload_dir_specific = myarcade_get_folder_path( $zip_file_name, 'html5' );

			// Now we can extract the file.
			if ( ! $zip->extract( PCLZIP_OPT_PATH, $upload_dir_specific['gamesdir'] . $sub_directory ) ) {
				// Something went wrong. File couldn't be extracted.
				// Clean up.
				foreach ( $zip_content as $content ) {
					$file = $upload_dir_specific['gamesdir'] . $sub_directory . $content['filename'];

					if ( file_exists( $file ) ) {
						@unlink( $file );
					}
				}

				$game->error = __( 'Error while extracting zip file', 'myarcadeplugin' );
			}

			// Remove the zip file.
			if ( file_exists( $file_abs ) ) {
				@unlink( $file_abs );
			}

			$game->type         = 'html5';
			$game->name         = ucfirst( $zip_file_name );
			$game->realname     = str_replace( '_', ' ', $game->name );
			$game->slug         = myarcade_make_slug( $game->name );
			$game->location_abs = $upload_dir_specific['gamesdir'] . $destination_folder . 'index.html';
			$game->location_url = $upload_dir_specific['gamesurl'] . $destination_folder . 'index.html';
		} else {
			$game->error = __( 'Can not upload file!', 'myarcadeplugin' );
		}
		break;

	default:
		$game->error = __( 'Unknown Import Method', 'myarcadeplugin' );
		break;
}

// Prepare the output.
$json = wp_json_encode( $game );

wp_die( $json );
