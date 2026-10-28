<?php
/**
 * Runs once, after the sample content is imported.
 *
 * - Removes WordPress's default "Hello world!" post and "Sample Page".
 * - Sets the "Home" page as the front page and "Campus News" as the posts page.
 */

require_once '/wordpress/wp-load.php';

$ucsc_hello  = get_page_by_path( 'hello-world', OBJECT, 'post' );
$ucsc_sample = get_page_by_path( 'sample-page', OBJECT, 'page' );

foreach ( array( $ucsc_hello, $ucsc_sample ) as $ucsc_default ) {
	if ( $ucsc_default ) {
		wp_delete_post( $ucsc_default->ID, true );
	}
}

$ucsc_home = get_page_by_path( 'home' );
$ucsc_news = get_page_by_path( 'campus-news' );

// A posts page only takes effect with a static front page, so set both or neither.
if ( $ucsc_home ) {
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $ucsc_home->ID );

	if ( $ucsc_news ) {
		update_option( 'page_for_posts', $ucsc_news->ID );
	}
}
