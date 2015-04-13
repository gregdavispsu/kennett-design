<?php
/**
 * Boom Bar settings page class.
 *
 * This class manages the settings page of BoomBar.
 *
 * @package IT_Boom_Bar
 * @since 1.1.0
*/
class IT_Boom_Bar_Settings_Page {

	/**
	 * The name WordPress uses to associate to the page.
	 * @since 1.1.0
	*/
	var $page_var = 'it-boombar-settings';

	/**
	 * Settings data as found in storage or as modified on form submission in the event of an error.
	 * @since 1.1.0
	*/
	var $options = array();

	/**
	 * Error messages produced while processing form submissions.
	 * @since 1.1.0
	*/
	var $errors = array();

	/**
	 * Status messages produced while processing form submissions.
	 * @since 1.1.0
	*/
	var $messages = array();


	/**
	 * Class constructor
	 *
	 * @uses add_action()
	 * @since 1.1.0
	 * @return null
	*/
	function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
	}

	/**
	 * Add the settings page to the Dashboard menu.
	 *
	 * @uses add_submenu_page()
	 * @since 1.1.0
	 * @return null
	*/
	function add_admin_menu() {
		$page_title = _x( 'BoomBar Settings', 'page title', 'it-l10n-boombar' );
		$menu_title = _x( 'Settings', 'menu title', 'it-l10n-boombar' );

		add_submenu_page( 'edit.php?post_type=it_boom_bar', $page_title, $menu_title, 'edit_posts', $this->page_var, array( $this, 'index' ) );
	}

	/**
	 * Save settings when appropriate and show the settings page.
	 *
	 * @since 1.1.0
	 * @return null
	*/
	function index() {
		require_once( $GLOBALS['it_boom_bar']->_pluginPath . '/lib/twitter-api/functions.php' );

		$this->options = $GLOBALS['it_boom_bar']->get_plugin_options();

		if ( isset( $_POST['submit'] ) )
			$this->save_settings();

		$this->show_settings();
	}

	/**
	 * Save submitted form values.
	 *
	 * @since 1.1.0
	 * @return null
	*/
	function save_settings() {
		check_admin_referer( $this->page_var );


		$errors = array();
		$messages = array();

		$credentials = array(
			'consumer_key'        => '',
			'consumer_secret'     => '',
			'access_token'        => '',
			'access_token_secret' => '',
		);

		$empty_inputs = array();

		foreach ( array_keys( $credentials ) as $key ) {
			if ( empty( $_POST["twitter_$key"] ) ) {
				$empty_inputs[] = ucwords( str_replace( '_', ' ', $key ) );
			} else {
				$credentials[$key] = $_POST["twitter_$key"];
				$this->options["twitter_$key"] = $_POST["twitter_$key"];
			}
		}

		if ( empty( $empty_inputs ) ) {
			$result = it_boom_bar_validate_twitter_credentials( $credentials );

			if ( is_wp_error( $result ) ) {
				$code = $result->get_error_code();
				
				if ( 'it_boombar_bad_twitter_credentials' == $code )
					$errors[] = __( 'Requests to Twitter using the supplied Twitter Settings failed. Please verify the values for each input with the authentication keys from Twitter.', 'it-l10n-boombar' );
			}
		} else {
			foreach ( $empty_inputs as $input )
				$errors[] = sprintf( _x( 'A value must be supplied for %s', 'input validation error message', 'it-l10n-boombar' ), $input );
		}

		if ( empty( $errors ) ) {
			$GLOBALS['it_boom_bar']->update_plugin_options( $this->options );
			
			$messages[] = __( 'Settings saved.', 'it-l10n-boombar' );
		} else {
			$errors[] = _n( 'Due to the error, the settings were not saved.', 'Due to the errors, the settings were not saved.', count( $errors ), 'it-l10n-boombar' );
		}


		$this->errors = array_merge( $this->errors, $errors );
		$this->messages = array_merge( $this->messages, $messages );
	}

	/**
	 * Show the settings page form.
	 *
	 * @since 1.1.0
	 * @return null
	*/
	function show_settings() {
		$action_url = admin_url( 'edit.php?post_type=it_boom_bar' ) . '&page=' . $this->page_var;

		if ( empty( $this->messages ) && empty( $this->errors ) ) {
			$credentials = array(
				'consumer_key'        => $this->options['twitter_consumer_key'],
				'consumer_secret'     => $this->options['twitter_consumer_secret'],
				'access_token'        => $this->options['twitter_access_token'],
				'access_token_secret' => $this->options['twitter_access_token_secret'],
			);

			$result = it_boom_bar_validate_twitter_credentials( $credentials );

			if ( is_wp_error( $result ) ) {
				$code = $result->get_error_code();
				
				if ( 'it_boombar_bad_twitter_credentials' == $code )
					$this->errors[] = __( 'Requests to Twitter using the supplied Twitter Settings failed. Please verify the values for each input with the authentication keys from Twitter.', 'it-l10n-boombar' );
			}
		}

?>
	<div class="wrap">
		<div id="icon-edit" class="icon32 icon32-posts-it_boom_bar">
			<br />
		</div>

		<h2>BoomBar Settings</h2>

		<?php
			foreach ( $this->messages as $message )
				echo "<div class=\"updated fade\"><p><strong>$message</strong></p></div>\n";

			foreach ( $this->errors as $error )
				echo "<div class=\"error\"><p><strong>$error</strong></p></div>\n";
		?>

		<form action="<?php echo esc_attr( $action_url ); ?>" method="post">
			<?php wp_nonce_field( $this->page_var ); ?>

			<h3 class="title"><?php _e( 'Twitter Settings', 'it-l10n-boombar' ); ?></h3>

			<p><?php printf( __( 'Starting with the 1.1 version of Twitter\'s API, authentication keys are required in order to request information from Twitter. The authentication keys can be generated by creating a new Twitter application <a href="%1$s" target="_blank">here</a>. For help in creating the application and finding the correct keys, please view <a href="%2$s" target="_blank">this video</a>.', 'it-l10n-boombar' ), 'https://dev.twitter.com/apps/new', 'http://www.youtube.com/watch?v=JkvIdLQ8zYE' ); ?></p>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<label for="twitter_consumer_key"><?php _ex( 'Consumer Key', 'Twitter API consumer key', 'it-l10n-boombar' ); ?></label>
					</th>
					<td>
						<input type="text" name="twitter_consumer_key" value="<?php echo esc_attr( $this->options['twitter_consumer_key'] ); ?>" id="twitter_consumer_key" class="regular-text code" style="width:35em" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="twitter_consumer_secret"><?php _ex( 'Consumer Secret', 'Twitter API consumer secret', 'it-l10n-boombar' ); ?></label>
					</th>
					<td>
						<input type="text" name="twitter_consumer_secret" value="<?php echo esc_attr( $this->options['twitter_consumer_secret'] ); ?>" id="twitter_consumer_secret" class="regular-text code" style="width:35em" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="twitter_access_token"><?php _ex( 'Access Token', 'Twitter API access token', 'it-l10n-boombar' ); ?></label>
					</th>
					<td>
						<input type="text" name="twitter_access_token" value="<?php echo esc_attr( $this->options['twitter_access_token'] ); ?>" id="twitter_access_token" class="regular-text code" style="width:35em" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="twitter_access_token_secret"><?php _ex( 'Access Token Secret', 'Twitter API access token secret', 'it-l10n-boombar' ); ?></label>
					</th>
					<td>
						<input type="text" name="twitter_access_token_secret" value="<?php echo esc_attr( $this->options['twitter_access_token_secret'] ); ?>" id="twitter_access_token_secret" class="regular-text code" style="width:35em" />
					</td>
				</tr>
			</table>

			<p class="submit">
				<input type="submit" name="submit" value="<?php _e( 'Save Changes' ); ?>" id="submit" class="button button-primary" />
			</p>
		</form>
	</div>
<?php
		
	}
}
