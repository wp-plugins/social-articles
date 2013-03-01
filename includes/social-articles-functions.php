<?php
if ( !defined( 'ABSPATH' ) ) exit;

function social_articles_load_template_filter( $found_template, $templates ) {
    global $bp;

    if ( $bp->current_component != $bp->social_articles->slug )
        return $found_template;
    foreach ( (array) $templates as $template ) {
        if ( file_exists( STYLESHEETPATH . '/' . $template ) )
            $filtered_templates[] = STYLESHEETPATH . '/' . $template;
        else
            $filtered_templates[] = dirname( __FILE__ ) . '/templates/' . $template;
    }
    $found_template = $filtered_templates[0];
    return apply_filters( 'social_articles_load_template_filter', $found_template );
}
add_filter( 'bp_located_template', 'social_articles_load_template_filter', 10, 2 );


function social_articles_load_sub_template( $template ) {
    if ( $located_template = apply_filters( 'bp_located_template', locate_template( $template , false ), $template ) )  
        load_template( apply_filters( 'bp_load_template', $located_template ) );
}

function get_short_text($text, $limitwrd ) {   
    if (str_word_count($text) > $limitwrd) {
      $words = str_word_count($text, 2);
      if ($words > $limitwrd) {
          $pos = array_keys($words);
          $text = substr($text, 0, $pos[$limitwrd]) . ' [...]';
      }
    }
    return $text;
}

function custom_get_user_posts_count($status){
    global $bp, $post, $wpdb;        
    $args = array();     
    $args['post_status'] = $status;
    $args['author'] = bp_displayed_user_id();
    $args['fields'] = 'ids';
    $args['posts_per_page'] = "-1";
    $args['post_type'] = 'post';
    $ps = get_posts($args);
    return count($ps);
}

add_action('save_post','send_notification');
function send_notification($id){
    global $post, $bp, $socialArticles;     
    $savedPost = get_post($id);
    if($savedPost->post_status == "publish" && $savedPost->post_type=="post" && !wp_is_post_revision($id) && $socialArticles->options['bp_notifications'] == "true"){
        $friends = friends_get_friend_user_ids($savedPost->post_author);  
        foreach($friends as $friend):        
            bp_core_add_notification($savedPost->ID,  $friend , $bp->social_articles->id, 'new_article'.$savedPost->ID, $savedPost->post_author);         
        endforeach;
        bp_core_add_notification($savedPost->ID,  $savedPost->post_author , $bp->social_articles->id, 'new_article'.$savedPost->ID, -1);        
    }        
    
     
}

function social_articles_remove_screen_notifications() {
    global $bp;
    bp_core_delete_notifications_for_user_by_type( $bp->loggedin_user->id, 'social_articles', 'new_high_five' );
}
add_action( 'xprofile_screen_display_profile', 'social_articles_remove_screen_notifications' );


function social_articles_format_notifications( $action, $item_id, $secondary_item_id, $total_items, $format = 'string' ) {

    global $bp;
    
    do_action( 'social_articles_format_notifications', $action, $item_id, $secondary_item_id, $total_items, $format );
    
    $createdPost = get_post($item_id);
   
    $text = "";
    if($secondary_item_id == "-1"){
         $text = '</a> <div id="'.$action.'"class="notification">'. 
                    __("One of your articles was approved","social-articles").'<a class="ab-item" title="'.$createdPost->post_title.'"href="'.get_permalink( $item_id ).'">,'.__("check it out!", "social-articles").'
                 </a> 
                 <a href="#" class="social-delete" onclick="deleteArticlesNotification(\''.$action.'\',\''.$item_id.'\', \''.admin_url( 'admin-ajax.php' ).'\'); return false;">x</a><span class="social-loader"></span></div>';
    
    }else{
        $creator = get_userdata($secondary_item_id); 
        $text = '</a> <div id="'.$action.'"class="notification">'. 
                    __("There is a new article by ", "social-articles").'<a class="ab-item" href="'.bloginfo('blog').'/members/'.$creator->user_login.'">'.$creator->user_nicename.', </a>
                 <a class="ab-item" title="'.$createdPost->post_title.'"href="'.get_permalink( $item_id ).'"> '.__("check it out!", "social-articles").'
                 </a> 
                 <a href="#" class="social-delete" onclick="deleteArticlesNotification(\''.$action.'\',\''.$item_id.'\', \''.admin_url( 'admin-ajax.php' ).'\'); return false;">x</a><span class="social-loader"></span></div>';
    }
    return $text;
}
?>