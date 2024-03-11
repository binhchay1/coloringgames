<?php get_header(); ?>

<?php
if ( function_exists( 'is_game' ) && is_game() ) {
	$style = tricera_get_option( 'game_page_layout' );

	if ( 'simple' == $style ) {
		$template = 'simple';
	}
	else {
		$template = 'complex';
	}
}
else {
	$template = 'content';
}

get_template_part( 'partials/single', $template );
?>

<?php get_footer(); ?>
