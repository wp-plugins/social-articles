<?php

/*Load components*/
class Social_Articles_Component extends BP_Component {

    function __construct() {
        global $bp;

        parent::start(
            'social_articles',
            __( 'Social Articles', 'articles' ),
            SA_BASE_PATH
        );

        $this->includes();

        $bp->active_components[$this->id] = '1';

    }

    function includes() {
       
        $includes = array(
            'includes/social-articles-screens.php',
            'includes/social-articles-functions.php',
            'includes/social-articles-manage-functions.php',
            'includes/social-articles-notifications.php',            
        );         
        parent::includes( $includes );
    }

    function setup_globals() {
        global $bp;
    
        if ( !defined( 'SA_SLUG' ) )
            define( 'SA_SLUG', $this->id );
       
        $globals = array(
            'slug'                  => SA_SLUG,
            'root_slug'             => isset( $bp->pages->{$this->id}->slug ) ? $bp->pages->{$this->id}->slug : SA_SLUG,
            'has_directory'         => false, 
            'notification_callback' => 'social_articles_format_notifications',
            'search_string'         => __( 'Search articles...', 'social-articles' )
        );
           
        parent::setup_globals( $globals );
    }

    function setup_nav() {
        $main_nav = array(
            'name'                => __( 'My articles', 'social-articles' ),
            'slug'                => SA_SLUG,
            'position'            => 100,
            'screen_function'     => 'social_articles_screen',
            'default_subnav_slug' => 'articles'
        );

        $user_domain = bp_is_user() ? bp_displayed_user_domain() : bp_loggedin_user_domain();
        
        $social_articles_link = trailingslashit( $user_domain . SA_SLUG );
       
        $user_id = bp_is_user() ? bp_displayed_user_id() : bp_loggedin_user_id();

        if(bp_displayed_user_id()==bp_loggedin_user_id()){

            $sub_nav[] = array(
                'name'            =>  sprintf( __( 'My articles <span>%d</span>', 'social-articles' ), custom_get_user_posts_count(array("publish", "pending", "draft"))),
                'slug'            => 'articles',
                'parent_url'      => $social_articles_link,
                'parent_slug'     => SA_SLUG,
                'screen_function' => 'my_articles_screen',
                'position'        => 10
            );

            $sub_nav[] = array(
                'name'            =>  __( 'New article' , 'social-articles' ),
                'slug'            => 'new',
                'parent_url'      => $social_articles_link,
                'parent_slug'     => SA_SLUG,
                'screen_function' => 'new_article_screen',
                'position'        => 20
            );
        }
        parent::setup_nav( $main_nav, $sub_nav );
    }
}

function social_articles_load_core_component() {
    global $bp;

    $bp->social_articles = new Social_Articles_Component;
    do_action('social_articles_load_core_component');
}
add_action( 'bp_loaded', 'social_articles_load_core_component' );
?>