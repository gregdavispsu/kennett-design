<?php


/**
 * Validates where Twitter API credentials are valid or not.
 *
 * @uses it_boom_bar_get_tweets()
 * @since 1.1.0
 * @param array|$credentials Listing of credentials: consumer_key, consumer_secret, access_token, access_token_secret
 * @return mixed True, on validated credentials. WP_Error, on error.
*/
function it_boom_bar_validate_twitter_credentials( $credentials ) {
	$params = array(
		'screen_name' => 'twitter',
		'count'       => '1',
	);

	$result = it_boom_bar_get_tweets( $credentials, $params );

	if ( is_wp_error( $result ) )
		return $result;

	return true;
}

/**
 * Returns Twitter tweets based upon request information.
 *
 * @uses IT_THM_OAuth::url()
 * @uses IT_THM_OAuth::request()
 * @since 1.1.0
 * @param array|$credentials Listing of credentials: consumer_key, consumer_secret, access_token, access_token_secret
 * @param array|$params Listing of key=>value pairs to be sent in the Twitter request. See Twitter's documentation for full details https://dev.twitter.com/docs/api/1.1/get/statuses/user_timeline
 * @return mixed array List of tweet data. WP_Error, on error.
*/
function it_boom_bar_get_tweets( $credentials, $params ) {
	$settings_page_url = admin_url( 'edit.php?post_type=it_boom_bar&page=it-boombar-settings' );

	$required_credentials = array(
		'consumer_key',
		'consumer_secret',
		'access_token',
		'access_token_secret',
	);

	foreach ( $required_credentials as $key ) {
		if ( empty( $credentials[$key] ) ) {
			$input_name = ucwords( str_replace( '_', ' ', $key ) );
			
			return new WP_Error( "it_boombar_missing_twitter_credential_$key", sprintf( __( 'The request to load tweets from Twitter failed. The following Twitter API key is missing: %1$s. Please verify your Twitter Settings in <a href="%2$s">BoomBar &gt; Settings</a> are valid.', 'it-l10n-boombar' ), $input_name, $settings_page_url ) );
		}
	}

	$credentials['user_token'] = $credentials['access_token'];
	$credentials['user_secret'] = $credentials['access_token_secret'];

	unset( $credentials['access_token'] );
	unset( $credentials['access_token_secret'] );


	require_once( dirname( __FILE__ ) . '/tmh-oauth/tmh-oauth.php' );

	$auth = new IT_THM_OAuth( $credentials );

	$url = $auth->url( '1.1/statuses/user_timeline', 'json' );

	$response_code = $auth->request( 'GET', $url, $params );


	if ( 200 != $response_code )
		return new WP_Error( 'it_boombar_bad_twitter_credentials', sprintf( __( 'The request to load tweets from Twitter failed. The supplied Twitter API keys were not accepted. Please verify your Twitter Settings in <a href="%s">BoomBar &gt; Settings</a> are valid.', 'it-l10n-boombar' ), $settings_page_url ) );


	$items = $auth->response['response'];

	if ( empty( $items ) )
		return new WP_Error( 'it_boombar_empty_twitter_response', __( 'The request to load tweets from Twitter failed. The returned data was empty. This indicates a possible error with Twitters servers.', 'it-l10n-boombar' ) );


	$items = json_decode( $items, true );

	if ( empty( $items ) )
		return new WP_Error( 'it_boombar_bad_twitter_json_response', __( 'The request to load tweets from Twitter failed. The returned data was not in JSON format. This indicates a possible error with Twitters servers.', 'it-l10n-boombar' ) );

	if ( ! is_array( $items ) )
		return new WP_Error( 'it_boombar_non_array_twitter_response', __( 'The request to load tweets from Twitter failed. The returned data is not an array. This indicates a possible error with Twitters servers.', 'it-l10n-boombar' ) );


	return $items;
}
