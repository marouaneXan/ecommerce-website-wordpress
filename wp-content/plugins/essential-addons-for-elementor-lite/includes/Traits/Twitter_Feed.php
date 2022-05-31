<?php

namespace Essential_Addons_Elementor\Traits;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

trait Twitter_Feed
{
    /**
     * Twitter Feed
     *
     * @since 3.0.6
     */
    public function twitter_feed_render_items($id, $settings, $class = '')
    {
        $token = get_option($id . '_' . $settings['eael_twitter_feed_ac_name'] . '_tf_token');
	    $expiration = ! empty( $settings['eael_auto_clear_cache'] ) && ! empty( $settings['eael_twitter_feed_cache_limit'] ) ? absint( $settings['eael_twitter_feed_cache_limit'] ) * MINUTE_IN_SECONDS : DAY_IN_SECONDS;
	    $cache_key = $settings['eael_twitter_feed_ac_name'] . '_' . $expiration . '_' . md5( $settings['eael_twitter_feed_hashtag_name'] . $settings['eael_twitter_feed_consumer_key'] . $settings['eael_twitter_feed_consumer_secret'] ) . '_tf_cache';
        $items = get_transient( $cache_key );
        $html = '';

        if (empty($settings['eael_twitter_feed_consumer_key']) || empty($settings['eael_twitter_feed_consumer_secret'])) {
            return;
        }

        if ($items === false) {
            if (empty($token)) {
                $credentials = base64_encode($settings['eael_twitter_feed_consumer_key'] . ':' . $settings['eael_twitter_feed_consumer_secret']);

                add_filter('https_ssl_verify', '__return_false');

                $response = wp_remote_post('https://api.twitter.com/oauth2/token', [
                    'method' => 'POST',
                    'httpversion' => '1.1',
                    'blocking' => true,
                    'headers' => [
                        'Authorization' => 'Basic ' . $credentials,
                        'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8',
                    ],
                    'body' => ['grant_type' => 'client_credentials'],
                ]);

                $body = json_decode(wp_remote_retrieve_body($response));

                if ($body) {
                    update_option($id . '_' . $settings['eael_twitter_feed_ac_name'] . '_tf_token', $body->access_token);
                    $token = $body->access_token;
                }
            }

            add_filter('https_ssl_verify', '__return_false');

            $response = wp_remote_get('https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=' . $settings['eael_twitter_feed_ac_name'] . '&count=999&tweet_mode=extended', [
                'httpversion' => '1.1',
                'blocking' => true,
                'headers' => [
                    'Authorization' => "Bearer $token",
                ],
            ]);

	        if ( ! empty( $response['response'] ) && $response['response']['code'] == 200 ) {
		        $items      = json_decode( wp_remote_retrieve_body( $response ), true );
		        set_transient( $cache_key, $items, $expiration );
	        }
        }

	    if ( empty( $items ) ) {
		    return $html;
	    }

        if ($settings['eael_twitter_feed_hashtag_name']) {
            foreach ($items as $key => $item) {
                $match = false;

                if ($item['entities']['hashtags']) {
                    foreach ($item['entities']['hashtags'] as $tag) {
                        if (strcasecmp($tag['text'], $settings['eael_twitter_feed_hashtag_name']) == 0) {
                            $match = true;
                        }
                    }
                }

                if ($match == false) {
                    unset($items[$key]);
                }
            }
        }

        $items = array_splice($items, 0, $settings['eael_twitter_feed_post_limit']);

        foreach ($items as $item) {
            $delimeter = strlen($item['full_text']) > $settings['eael_twitter_feed_content_length'] ? '...' : '';

	        $media = isset( $item['extended_entities']['media'] ) ? $item['extended_entities']['media'] :
		        ( isset( $item['retweeted_status']['entities']['media'] ) ? $item['retweeted_status']['entities']['media'] :
			        ( isset( $item['quoted_status']['entities']['media'] ) ? $item['quoted_status']['entities']['media'] :
				        [] ) );

            $html .= '<div class="eael-twitter-feed-item ' . $class . '">
				<div class="eael-twitter-feed-item-inner">
				    <div class="eael-twitter-feed-item-header clearfix">';
                        if ($settings['eael_twitter_feed_show_avatar'] == 'true') {
                            $html .= '<a class="eael-twitter-feed-item-avatar avatar-' . $settings['eael_twitter_feed_avatar_style'] . '" href="//twitter.com/' . $settings['eael_twitter_feed_ac_name'] . '" target="_blank">
                                <img src="' . $item['user']['profile_image_url_https'] . '">
                            </a>';
                        }
                        
                        $html .= '<a class="eael-twitter-feed-item-meta" href="//twitter.com/' . $settings['eael_twitter_feed_ac_name'] . '" target="_blank">';
                            if ($settings['eael_twitter_feed_show_icon'] == 'true') {
                                $html .= '<i class="fab fa-twitter eael-twitter-feed-item-icon"></i>';
                            }
                            $html .= '<span class="eael-twitter-feed-item-author">' . $item['user']['name'] . '</span>
                        </a>';
            
                        if ($settings['eael_twitter_feed_show_date'] == 'true') {
                            $html .= '<span class="eael-twitter-feed-item-date">' . sprintf(__('%s ago', 'essential-addons-for-elementor-lite'), human_time_diff(strtotime($item['created_at']))) . '</span>';
                        }
                    $html .= '</div>

                    <div class="eael-twitter-feed-item-content">';
                            $link_free_text = isset($item['entities']['urls'][0]['url'])?str_replace($item['entities']['urls'][0]['url'], '', $item['full_text']):$item['full_text'];
                            $html .= '<p>' . substr( $link_free_text, 0, $settings['eael_twitter_feed_content_length']) . $delimeter . '</p>';
                        if ($settings['eael_twitter_feed_show_read_more'] == 'true') {
	                        $read_more = !empty( $settings[ 'eael_twitter_feed_show_read_more_text' ] ) ? $settings[ 'eael_twitter_feed_show_read_more_text' ] : __( 'Read More', 'essential-addons-for-elementor-lite' );
                            $html .= '<a href="//twitter.com/' . $item['user']['screen_name'] . '/status/' . $item['id_str'] . '" target="_blank" class="read-more-link">'.$read_more.' <i class="fas fa-angle-double-right"></i></a>';
                        }
                    $html .= '</div>
                    ' . ( isset( $media[0] ) && $settings['eael_twitter_feed_media'] == 'true' ? ( $media[0]['type'] == 'photo' ? '<img src="' . $media[0]['media_url_https'] . '">' : '' ) : '' ) . '
                </div>
			</div>';
        }

        return $html;
    }
}
