<?php

namespace njb\ds;
use njb\ds\Transform;

Class WPStore {

    function __construct( &$wp ) {
        if (!is_object($wp)) {
            die('Needed WP object!');
        }
    }

    public static function post_expand_meta ($post_id, $post, $is_update) {

        delete_post_meta($post_id, 'obj');
        $post = get_post($post_id); // required to re-fetch post when versioning is on
        $obj = json_decode( $post->post_content , true );
    
        if($obj!=NULL)  { 
            $cur_meta = []; //get_post_meta( $post_id, 'obj');
            $new_meta =  Transform::serialize( $obj );
            $add_meta = [];
    
            /* foreach($cur_meta as $meta) {
                if (!in_array($meta, $new_meta)) {
                    delete_post_meta($post_id, 'obj', $meta);
                }
            } */
    
            // Insert new meta
            foreach($new_meta as $meta) {
                if (!in_array($meta, $cur_meta)) {
                    add_post_meta($post_id, 'obj', $meta);
                }
            }
    
        }
    }

    /* Creates a WP query filter array that includes exctly what's in object */
    public static function obj_eq_filter($obj, $key = 'obj', $ignore_array_pos = true) {
        $criteria = Transform::serialize($obj);
        $query=[];
        foreach($criteria as $crit){
            
            if($ignore_array_pos) $crit = substr($crit, 0, strrpos($crit, '$')+1);
            $query[] = ['compare' => 'BETWEEN', 'key'=>$key,'value'=> [$crit, $crit . '999999999999']];
        }
        return $query;           
    }

    public static function get_user_authenticated_posts($uid, $type, $num = -1, $add_filters=[]) {
        $filter_by['groups'][] = $uid ? [] : 'vid' . $_COOKIE['task_vid'];
        if($uid) {
            foreach( get_all_member_groups($uid) as $group) {
                $filter_by['groups'][] = $group;
            }
        }
        $filter_by = self::obj_eq_filter($filter_by);        
        return get_posts( ['numberposts'=>$num, 'post_type'=>$type, 'meta_query'=>[[$filter_by]]]);
    }

    public static function get_all_member_groups($uid) {
        $groups = [];
        foreach(get_posts( ['numberposts'=>-1,'post_type'=>'group', 'meta_query'=>[['key'=>'uid','value'=>"{$uid}"]] ]) as $group) {
            $groups[] = $group->ID;
        }
        return $groups;
    }
}