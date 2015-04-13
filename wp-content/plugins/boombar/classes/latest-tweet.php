<?php
/**
 * Boom Bar Latest Tweet class.
 *
 * This class contains the functionality needed to fetch, cache, and display a tweet
 *
 * eg:
 * $latest = new IT_Boom_Bar_Latest_Tweet( $username );
 * $latest->set_link_target( '_blank' ); // Optional. Defaults to '_self'
 * $latest->set_tweet_data();
 *
 * @package IT_Boom_Bar
 * @since 0.2
*/
class IT_Boom_Bar_Latest_Tweet {

	/**
	 * @var string Twitter username we will fetch tweets from
	 * @since 0.2
	*/
	var $username;

	/**
	 * @var string Composite of plugin name and $username property
	 * @since 0.2
	*/
	var $transient_key;

	/**
	 * @var string The content of the tweet returned by the Twitter API
	 * @since 0.2
	*/
	var $tweet_text;

	/**
	 * @var string The link to the tweet returned by the Twitter API
	 * @since 0.2
	*/
	var $tweet_link;

	/**
	 * @var boolen|object False or WP_Error object from IT_THM_OAuth::request()
	 * @since 1.0.1
	*/
	var $wp_error = false;

	/**
	 * @var string Link Target
	 * @since 1.0.1
	*/
	var $link_target = '_blank';

	/**
	 * The class constructor takes a username and fires sets up the data
	 *
	 * @param string Twitter username we will feth tweets from
	 * @uses IT_Boom_Bar_Latest_Twet::set_username()
	 * @uses IT_Boom_Bar_Latest_Twet::set_transient_key()
	 * @since 0.2
	 * @return null
	*/
	function IT_Boom_Bar_Latest_Tweet( $username ) {
		$this->set_username( $username );
	}

	/**
	 * Sets the username
	 *
	 * @param string Twitter username we will fetch tweets from
	 * @since 0.2
	 * @return null
	*/
	function set_username( $username ) {
		$this->username = $username;
		$this->set_transient_key();
	}

	/**
	 * Sets the transient key for this twitter handle
	 *
	 * @since 0.2
	 * @return null
	*/
	function set_transient_key() {
		$this->transient_key = 'it_boom_bar-latest_tweet-' . $this->username;
	}

	/**
	 * Sets the tweet data based on cache or response from Twitter
	 *
	 * First looks for a transient cache witht the data.
	 * If no cache exists, calls fetch_tweet_data() to get data from Twitter and stores in cache
	 *
	 * @uses get_transient()
	 * @uses IT_Boom_Bar_Latest_Tweet::fetch_tweet_data()
	 * @uses set_transient()
	 * @since 0.2
	*/
	function set_tweet_data() {

		// Check for cached tweet
		if ( false === ( $tweet_data = get_transient( $this->transient_key ) ) ) {
			$tweet_data = $this->fetch_tweet_data();
			if ( $tweet_data )
				set_transient( $this->transient_key, $tweet_data, 60 * 5 );
		}

		$this->tweet_text = $tweet_data['tweet'];
		$this->tweet_link = $tweet_data['link'];
	}

	/**
	 * Uses HTTP API to grab and parse latest tweet from Twitter
	 *
	 * @since 0.2
	 * @uses IT_THM_OAuth::url()
	 * @uses IT_THM_OAuth::request()
	 * @return bool|array False if we don't get a response from twitter. The data if we do.
	*/
	function fetch_tweet_data() {
		require_once( $GLOBALS['it_boom_bar']->_pluginPath . '/lib/twitter-api/functions.php' );


		$options = $GLOBALS['it_boom_bar']->get_plugin_options();

		$credentials = array(
			'consumer_key'        => $options['twitter_consumer_key'],
			'consumer_secret'     => $options['twitter_consumer_secret'],
			'access_token'        => $options['twitter_access_token'],
			'access_token_secret' => $options['twitter_access_token_secret'],
		);

		$params = array(
			'include_rts' => 1,
			'count'       => 1,
			'screen_name' => $this->username,
		);

		$tweets = it_boom_bar_get_tweets( $credentials, $params );

		if ( is_wp_error( $tweets ) ) {
			$this->wp_error = $tweets;
			return false;
		}

		$data['tweet'] = $tweets[0]['text'];
		$data['link'] = 'http://twitter.com/' . $this->username . '/status/' . $tweets[0]['id_str'];
		
		return $data;
	}

	/**
	 * Returns the value of the tweet_text property
	 *
	 * @since 0.2
	 * @return string The tweet itself
	*/
	function get_text() {
		return preg_replace( '@(?<![.*">])\b(?:(?:https?|ftp|file)://|[a-z]\.)[-A-Z0-9+&#/%=~_|$?!:,.]*[A-Z0-9+&#/%=~_|$]@i', '<a href="\0" target="' .  esc_attr( $this->link_target ) . '">\0</a>', $this->tweet_text );
	}

	/**
	 * Returns the value of the $tweet_link property
	 *
	 * Link is displayed as twitter username and points to permalink for tweet content
	 *
	 * @since 0.2
	 * @return string The link to the tweet
	*/
	function get_link() {
		return $this->tweet_link;
	}

	/**
	 * Sets the link target property
	 *
	 * @since 1.0.1
	 * @param string $target Link Target value
	 * @return null
	*/
	function set_link_target( $target ) {
		$this->link_target = $target;
	}
}
