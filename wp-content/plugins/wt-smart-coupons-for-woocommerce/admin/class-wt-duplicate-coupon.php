<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if( !class_exists('WT_Duplicate_Shop_Coupon') )  {
    class WT_Duplicate_Shop_Coupon {

        public function __construct() {

            add_action('admin_action_wt_duplicate_post_as_draft', array($this, 'wt_duplicate_post_as_draft'));
            add_filter('post_row_actions', array($this, 'wt_duplicate_post_link'), 10, 2);
        }

        function wt_duplicate_post_as_draft()
        {
            global $wpdb;
            if(!current_user_can('edit_posts')) 
            {
                wp_die(__('You do not have sufficient permission to perform this operation', 'wt-smart-coupons-for-woocommerce'));
            }
            if(!( isset($_GET['post']) || isset($_POST['post']) || ( isset($_REQUEST['action']) && 'wt_duplicate_post_as_draft' == $_REQUEST['action'])))
            {
                wp_die( __('No post to duplicate has been supplied!', 'wt-smart-coupons-for-woocommerce'));
            }

            if(!isset($_GET['duplicate_nonce']) || !wp_verify_nonce($_GET['duplicate_nonce'], basename(__FILE__)))
            {
                return;
            }

            $post_id=(isset($_GET['post']) ? absint($_GET['post']) : absint($_POST['post']));

            $post=get_post($post_id);

            if(isset($post) && $post != null) 
            {               
                $current_user = wp_get_current_user();
                $new_post_author = $current_user->ID;

                $maybe_post_title = $post->post_title;
                $p_title = $maybe_post_title;
                $counter = 1;

                while(post_exists($p_title))
                {
                    $p_title = $maybe_post_title.$counter;
                    $counter++;
                }    
                
                /*
                * new post data array
                */
                $args = array(
                    'comment_status' => $post->comment_status,
                    'ping_status' => $post->ping_status,
                    'post_author' => $new_post_author,
                    'post_content' => $post->post_content,
                    'post_excerpt' => $post->post_excerpt,
                    'post_name' => $post->post_name,
                    'post_parent' => $post->post_parent,
                    'post_password' => $post->post_password,
                    'post_status' => apply_filters('wt_smartcoupon_default_duplicate_coupon_status', 'publish'),
                    'post_title' => $p_title,
                    'post_type' => $post->post_type,
                    'to_ping' => $post->to_ping,
                    'menu_order' => $post->menu_order
                );
                
                $new_post_id = wp_insert_post($args);


                $taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
                foreach ($taxonomies as $taxonomy) {
                    $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
                    wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
                }

                $post_meta_data = $wpdb->get_results($wpdb->prepare("SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id=%d", $post_id));
                if(!empty($post_meta_data))
                {
                    $sql_query = "INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value) VALUES ";
                    $placeholders_arr=array();
                    $values_arr=array();
                    foreach($post_meta_data as $meta_info)
                    {
                        $meta_key=$meta_info->meta_key;
                        if($meta_key == '_wp_old_slug' || $meta_key == 'wt_credit_history' || $meta_key == '_wt_smart_coupon_initial_credit')
                        {
                            continue;
                        }
                        $placeholders_arr[]='(%d, %s, %s)';
                        array_push($values_arr, $new_post_id, $meta_key, $meta_info->meta_value);
                    }
                    
                    $sql_query.= implode(", ", $placeholders_arr);
                    $sql_query=$wpdb->prepare($sql_query, $values_arr);
                    $wpdb->query($sql_query);
                }
                wp_redirect(admin_url('post.php?action=edit&post=' . $new_post_id));
                exit();
            }else
            {
                wp_die(__('Post creation failed, could not find original post: ', 'wt-smart-coupons-for-woocommerce') . $post_id);
            }
        }

        /*
        * Add the duplicate link to action list for post_row_actions
        */

        function wt_duplicate_post_link($actions, $post) {

            if (current_user_can('edit_posts')) {
                if ((isset($_GET['post_type'])) && ($_GET['post_type'] == 'shop_coupon')) {
                    $href_text = __('Duplicate', 'wt-smart-coupons-for-woocommerce');
                    $href_title = __('Duplicate this item', 'wt-smart-coupons-for-woocommerce');
                    $actions['duplicate'] = '<a href="' . wp_nonce_url('admin.php?action=wt_duplicate_post_as_draft&post=' . $post->ID, basename(__FILE__), 'duplicate_nonce') . '" title="' . $href_title . '" rel="permalink">' . $href_text . '</a>';
                }
            }
            return $actions;
        }

    }
    new WT_Duplicate_Shop_Coupon();

}
