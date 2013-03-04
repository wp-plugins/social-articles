<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * social_articles_screen()
 *
 * Sets up and displays the screen output for the sub nav item "social-articles/screen-one"
 */
function social_articles_screen() {
	global $bp;
	bp_update_is_directory( true, 'articles' );
	/* Add a do action here, so your component can be extended by others. */
	do_action( 'social_articles_screen' );
        
	bp_core_load_template( apply_filters( 'social_articles_screen', 'members/single/articles' ) );
}


/**
 * my_articles_screen()
 *
 * Sets up and displays the screen output for the sub nav item "social-articles/screen-two"
 */
function my_articles_screen() {
	global $bp;
	bp_update_is_directory( true, 'my_articles' );
	/* Add a do action here, so your component can be extended by others. */
	do_action( 'my_articles_screen' );
	bp_core_load_template( apply_filters( 'my_articles_screen', 'members/single/articles' ) );
}

/**
 * new_article_screen()
 *
 * Sets up and displays the screen output for the sub nav item "social-articles/screen-two"
 */
function new_article_screen() {
	global $bp;
	bp_update_is_directory( true, 'new_article' );
	
	/* Add a do action here, so your component can be extended by others. */
	do_action( 'new_article_screen' );
	
	bp_core_load_template( apply_filters( 'bp_followers_screen', 'members/single/articles' ) );
}
