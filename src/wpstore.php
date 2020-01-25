<?php

namespace njb\ds;
use njb\ds\Transform;
echo 'made it'; exit();

Class WPStore {

    function __construct( &$wp ) {
        if (!is_object($wp)) {
            die('Needed WP object!');
        }
    }

    public static function post_expand_meta ($post_id, $post, $is_update) {

        $obj = json_decode( $raw_json , true );
    
        if($obj!=NULL)  { 
            $cur_meta = get_post_meta( $post_id, 'obj');
            $new_meta =  Transform::serialize( $obj );
            $add_meta = [];
    
            foreach($cur_meta as $meta) {
                if (!in_array($meta, $new_meta)) {
                    delete_post_meta($post_id, 'obj', $meta);
                }
            }
    
            // Insert new meta
            foreach($new_meta as $meta) {
                if (!in_array($meta, $cur_meta)) {
                    add_post_meta($post_id, 'obj', $meta);
                }
            }
    
        }
    
    
        
    }
}