<?php
/*
 * Plugin Name: iThemes Boom Bar
 * Version: 1.1.21
 * Description: Add notification bars to the top of your site.
 * Plugin URI: http://ithemes.com/purchase/boombar/
 * Author: iThemes
 * Author URI: http://ithemes.com
 * iThemes Package: boombar
 *
 * Installation:
 * 1. Download and unzip the latest release zip file.
 * 2. If you use the WordPress plugin uploader to install this plugin skip to step 4.
 * 3. Upload the entire plugin directory to your `/wp-content/plugins/` directory.
 * 4. Activate the plugin through the 'Plugins' menu in WordPress Administration.
 *
 * Usage:
 * 1. Navigate to the new plugin menu in the Wordpress Administration Panel.
 *
*/

/**
 * Boom Bar main class.
 *
 * This class manages options and loads additional classes as needed.
 *
 * @package IT_Boom_Bar
 * @since 0.1
*/
if ( ! class_exists( 'IT_Boom_Bar' ) ) {
	class IT_Boom_Bar {

		var $_version            = '1.1.21';
		var $_slug               = 'it_boom_bar';

		var $_pluginPath         = '';
		var $_pluginRelativePath = '';
		var $_pluginURL          = '';
		var $_selfLink           = '';
		var $_pluginBase         = '';

		var $options             = array();
		var $bars;

		/**
		 * Setup the plugin
		 *
		 * Class Constructor. Sets up the environment and then loads admin or enqueues active bar
		 *
		 * @uses IT_Boom_Bar::set_plugin_locations()
		 * @uses IT_Boom_Bar::set_plugin_options()
		 * @uses IT_Boom_Bar::set_textdomain()
		 * @uses IT_Boom_Bar::set_bars()
		 * @uses IT_Boom_Bar::load_admin()
		 * @uses IT_Boom_Bar::enqueue_bars()
		 * @uses is_admin()
		 * @since 0.1
		 * @return null
		*/
		function it_boom_bar() {
			$this->set_plugin_locations();
			$this->set_plugin_options();
			$this->set_textdomain();
			$this->set_bars();

			if ( is_admin() ) {
				$this->load_admin();
			} else {
				$this->enqueue_bars();
				$this->maybe_init_dev_tools();
			}
		}

		/**
		 * Defines where the plugin lives on the server
		 *
		 * @uses WP_PLUGIN_DIR
		 * @uses ABSPATH
		 * @uses site_url()
		 * @since 0.1
		 * @return null
		*/
		function set_plugin_locations() {
			$this->_pluginPath              = WP_PLUGIN_DIR . '/' . basename( dirname( __FILE__ ) );
			$this->_pluginRelativePath      = ltrim( str_replace( '\\', '/', str_replace( rtrim( ABSPATH, '\\\/' ), '', $this->_pluginPath ) ), '\\\/' );
			$this->_pluginURL               = plugin_dir_url( __FILE__ );

			// Adjust URL for HTTPS if needed
			if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) {
				$this->_pluginURL = str_replace( 'http://', 'https://', $this->_pluginURL );
			
			$selflinkvar = explode( '?', $_SERVER['REQUEST_URI'] );
			$this->_selfLink = array_shift( $selflinkvar ) . '?page=boombar';
			}
		}

		/**
		 * Sets the options by merging those stored in DB with our defaults
		 *
		 * @uses IT_Boom_Bar::get_default_options()
		 * @uses IT_Boom_Bar::get_plugin_options()
		 * @uses wp_parse_args()
		 * @uses update_option()
		 * @since 0.1
		 * @return null
		*/
		function set_plugin_options() {
			$defaults = $this->get_default_options();
			$current = $this->get_plugin_options();

			// Temp Fix for http://cl.ly/170G1C0g0V1I
			foreach( $current as $key => $value ) {
				 if ( empty( $value ) && is_numeric( $key ) )
					 unset( $current[$key] );
			}

			$this->options = wp_parse_args( (array) $current, $defaults );
			update_option( $this->_slug, $this->options ); // TODO: Only update when different
		}

		/**
		 * Return default plugin options
		 *
		 * @since 0.1
		 * @return array Default Options
		*/
		function get_default_options() {
			$default_options = array(
				'bar_text'                    => __( 'You are using the Boom Bar plugin built by iThemes!', 'it-l10n-boombar' ),
				'bar_link_text'               => __( 'Learn more', 'it-l10n-boombar' ),
				'bar_link_url'                => 'http://ithemes.com/purchase/boombar',
				'bar_color_scheme'            => 'lime',
				'bar_bg_color'                => '#0088CD',
				'bar_text_color'              => '#D7F0FC',
				'bar_link_color'              => '#FFFFFF',
				'bar_border_color'            => '#D7F0FC',
				'bar_cookie_exp'              => -1, // Until the next page load
				'bar_position'                => 'fixed',
				'bar_links_target'            => '_self',
				'bar_location'                => 'top',
				'bar_max_page'                => 1, // Not currently an option in the UI
				'bar_priority'                => 2, // Not currently an option in the UI
				'wpab_priority'               => 1, // Not currently an option in the UI
				'default_bar'                 => false,
				'twitter_consumer_key'        => '',
				'twitter_consumer_secret'     => '',
				'twitter_access_token'        => '',
				'twitter_access_token_secret' => '',
			);
			return $default_options;
		}

		/**
		 * Return Plugin Options
		 *
		 * @param string Key of single plugin option. Value will be returned if found,
		 * @uses get_option()
		 * @since 0.1
		 * @return string|bool|array If option param was provided and a value found, value is returned as string. Otherwise false or array of options.
		*/
		function get_plugin_options( $option=false ) {
			$options = get_option( $this->_slug );

			if ( $option )
				return ( isset( $options[$option] ) && ! is_object( $options[$option] ) ) ? $options[$option] : false;
			else
				return $options;
		}

		/**
		 * Update plugin options
		 *
		 * @uses wp_parse_args()
		 * @uses update_option()
		 * @since 0.1
		 * @return null
		*/
		function update_plugin_options( $new_options=array() ) {
			$this->options = wp_parse_args( (array) $new_options, $this->options );
			update_option( $this->_slug, $this->options );
		}

		/**
		 * Loads the translation data for WordPress
		 *
		 * @uses load_plugin_textdomain()
		 * @since 0.1
		 * @return null
		*/
		function set_textdomain() {
			load_plugin_textdomain( 'it-l10n-boombar', false, dirname( $this->_pluginBase ) . '/lang/' );
		}

		/**
		 * Inits the bars object
		 *
		 * This function determines if a bar should be shown on the current page and loads all the data into the bars property
		 *
		 * @uses IT_Boom_Bar_Bars
		 * @since 0.1
		 * @return null
		*/
		function set_bars() {
			require_once( $this->_pluginPath . '/classes/bars.php' );
			require_once( $this->_pluginPath . '/classes/single-bar.php' );
			$this->bars = new IT_Boom_Bar_Bars();
		}

		/**
		 * Inits the admin class and executes appropriate actions / views
		 *
		 * If we're in the admin, init the admin object which fires off all our actions, filters, etc for the admin interface
		 *
		 * @uses IT_Boom_Bar_Admin
		 * @since 0.1
		 * @return null
		*/
		function load_admin() {
			require_once( $this->_pluginPath . '/classes/admin.php' );
			$this->admin = new IT_Boom_Bar_Admin();
			
			require( $this->_pluginPath . '/classes/settings-page.php' );
			new IT_Boom_Bar_Settings_Page();
		}

		/**
		 * Enqueues any CSS / JS needed for current bars as well as adds hook to print HTML
		 *
		 * @uses add_action
		 * @uses IT_Boom_Bar::enqueue_bar_styles_scripts()
		 * @since 0.1
		 * @return null
		*/
		function enqueue_bars() {
			add_action ( 'template_redirect', array( $this, 'enqueue_bar_styles_scripts' ) );

			if ( 'builder' == strtolower( get_option( 'template' ) ) )
				add_action( 'builder_finish', array( $this, 'display_bars' ) );
			else if ( 'flexx' == strtolower( substr( get_option( 'template' ), 0, 5 ) ) )
				add_action( 'flexx_footer_stats', array( $this, 'display_bars' ) );
			else if ( 'icompany' == strtolower( get_option( 'template' ) ) )
				add_action( 'it_footer', array( $this, 'display_bars' ) );
			else
				add_action ( 'wp_footer', array( $this, 'display_bars' ) );
		}

		/**
		 * Enqueues the proper JS and Style for current bars
		 *
		 * Also adds additional classes to the body for use with CSS
		 * Also prints individual bar's CSS settings and link google fonts stylesheet if needed
		 *
		 * @uses wp_enqueue_style()
		 * @uses wp_enqueue_script()
		 * @uses add_filter()
		 * @uses IT_Boom_Bar::get_boom_bar_body_classes()
		 * @uses IT_Boom_Bar::print_active_bars_css()
		 * @since 0.1
		 * @return null
		*/
		function enqueue_bar_styles_scripts() {
			if ( ! empty( $this->bars->current_bars ) ) {
				wp_enqueue_style( 'boom_bar-frontent-global', $this->_pluginURL . '/styles/frontend-global.css' );
				wp_enqueue_script( 'boom_bar-frontend-global-js', $this->_pluginURL . '/js/frontend-global.js', array( 'jquery' ) );
				add_filter( 'body_class', array( $this, 'get_boom_bar_body_classes' ) );
				add_action( 'wp_footer', array( $this, 'maybe_modify_body_class' ) );
			}

			// Register variable CSS to be inserted in header
			add_action( 'wp_head', array( $this, 'print_active_bars_css') );
		}

		/**
		 * Manages printing the Bars on the page. 
		 *
		 * @uses IT_Boom_Bar::print_bar_header()
		 * @uses IT_Boom_Bar::print_bar_content()
		 * @uses IT_Boom_Bar::print_bar_footer()
		 * @since 0.1
		 * @return null
		*/
		function display_bars() {
			foreach ( $this->bars->current_bars as $id => $bar ) {
				$this->print_bar_header( $bar );
				$this->print_bar_content( $bar );
				$this->print_bar_footer( $bar );
			}
		}

		/**
		 * This adds unique CSS rules to the <head> of the page for bars that are shown on this page
		 *
		 * It also adds the style link for Google Fonts if needed
		 *
		 * @uses IT_Boom_Bar::print_bar_css()
		 * @uses IT_Boom_Bar::get_google_fonts()
		 * @uses esc_attr()
		 * @since 0.2
		 * @return null
		*/
		function print_active_bars_css() {
			$fonts = array();
			echo '<style type="text/css">';
			foreach( $this->bars->current_bars as $id => $bar ) {
				$this->print_bar_css( $bar );
				if ( ! empty( $bar->font ) )
					$fonts[] = $bar->font;
			}
			echo '</style>';
			if ( ! empty( $fonts ) && $gfonts = $this->get_google_fonts() ) {
				foreach ( $fonts as $font ) {
					if ( ! empty( $gfonts[$font]['args'] ) )
						echo '<link href="https://fonts.googleapis.com/css?' . esc_attr( $gfonts[$font]['args'] ) . '" rel="stylesheet" type="text/css">';
				}
			}
		}

		/**
		 * Actually prints the CSS for a specific bar in the header
		 *
		 * @param object IT_Boom_Bar_Single_Bar for a specific bar
		 * @uses IT_Boom_Bar::get_google_fonts()
		 * @uses esc_attr_e()
		 * @uses esc_html_e()
		 * @since 0.1
		*/
		function print_bar_css( $bar ) {
			$family = '';
			$border = ( 'top' == $bar->location ) ? 'bottom' : 'top';
			if ( ! empty( $bar->font ) && $gfonts = $this->get_google_fonts() )
				$family = empty( $gfonts[$bar->font]['family'] ) ? $family : 'font-family: ' . $gfonts[$bar->font]['family'] . ';';
			
			if ( 'custom' == $bar->color_scheme ) { ?>
				/* CSS for the '<?php esc_attr_e( $bar->name ); ?>' boom bar */
				#boom_bar-<?php esc_attr_e( $bar->id ); ?> {
					background: <?php esc_attr_e( $bar->bg_color ); ?>;
					color: <?php esc_attr_e( $bar->text_color ); ?>;
					border-<?php echo $border; ?>: 1px solid <?php esc_attr_e( $bar->border_color ); ?>;
				}
				#boom_bar-<?php esc_attr_e( $bar->id ); ?> a{
					color: <?php esc_attr_e( $bar->link_color ); ?>;
				}
			<?php } ?>
			#boom_bar-<?php esc_attr_e( $bar->id ); ?> .boom_bar-inner-container, 
			#boom_bar-<?php esc_attr_e( $bar->id ); ?> .boom_bar-inner-container p {
				<?php echo $family; ?>
			}
			<?php
			if ( ! empty( $bar->custom_css ) )
				esc_html_e( $bar->custom_css );
		}

		/**
		 * Returns the CSS classes for a specific bar
		 *
		 * This is a filter hooked to body_classes
		 * 
		 * @uses is_admin_bar_showing()
		 * @uses esc_attr()
		 * @since 0.2
		 * @return array Array of classes to be appended to the end of existing WP body classes
		*/
		function get_boom_bar_body_classes( $existing ) {
			if ( ! empty ( $this->bars->current_bars ) ) {
				$bar = reset( $this->bars->current_bars );

				$position = ( $bar->position ) ? $bar->position : 'fixed';
				$location = ( $bar->location ) ? $bar->location : 'top';

				// Admin bar
				$wpab = ( 1 === $this->options['wpab_priority'] ) ? 'below_wpab' : 'above_wpab';
				$wpab = is_admin_bar_showing() ? $wpab: 'no_wpab';

				$existing[] = 'boom_bar-' . esc_attr( $bar->position ) . '-' . esc_attr( $bar->location ) . '-' . $wpab;
			}
			return $existing;
		}

		/**
		* Returns an array of possible google fonts
		*
		* Format is a multi-dimentional array:
		* 'Font Nice Name' => array(
		*       'name'   => 'Font Nice Name',
		*       'args'   => 'family=Font+Nice+Name', // any part of Google Style link after ?
		*       'family' => "'Font Nice Name', serif", // value for font-family CSS rule
		* ),
		*
		* @uses apply_filters so that 3rd party developers can add or remove fonts from list
		* @since 0.2
		* @return array List of google fonts
		*/
		function get_google_fonts() {
			$fonts = array(
				'Alfa Slab One' => array(
					'name'   => 'Alfa Slab One',
					'args'   => 'family=Alfa+Slab+One',
					'family' => "'Alfa Slab One', cursive",
				),
				'Droid Sans' => array(
					'name'   => 'Droid Sans',
					'args'   => 'family=Droid+Sans',
					'family' => "'Droid Sans', sans-serif",
				),
				'Ropa Sans' => array(
					'name'   => 'Ropa Sans',
					'args'   => 'family=Ropa+Sans',
					'family' => "'Ropa Sans', sans-serif",
				),
				'Droid Serif' => array(
					'name'   => 'Droid Serif',
					'args'   => 'family=Droid+Serif:400,700',
					'family' => "'Droid Serif', serif",
				),
				'Open Sans' => array(
					'name'   => 'Open Sans',
					'args'   => 'family=Open+Sans:400,700',
					'family' => "'Open Sans', sans-serif",
				),
				'Oswald' => array(
					'name'   => 'Oswald',
					'args'   => 'family=Oswald',
					'family' => "'Oswald', sans-serif",
				),
				'Alice' => array(
					'name'   => 'Alice',
					'args'   => 'family=Alice',
					'family' => "'Alice', serif",
				),
				'Kaushan Script' => array(
					'name'   => 'Kaushan Script',
					'args'   => 'family=Kaushan+Script',
					'family' => "'Kaushan Script', cursive",
				),
				'Raleway' => array(
					'name'   => 'Raleway',
					'args'   => 'family=Raleway:400,700',
					'family' => "'Raleway', sans-serif",
				),
				'Francois One' => array(
					'name'   => 'Francois One',
					'args'   => 'family=Francois+One',
					'family' => "'Francois One', sans-serif",
				),
				'Varela Round' => array(
					'name'   => 'Varela Round',
					'args'   => 'family=Varela+Round',
					'family' => "'Varela Round', sans-serif",
				),
				'Stint Ultra Expanded' => array(
					'name'   => 'Stint Ultra Expanded',
					'args'   => 'family=Stint+Ultra+Expanded',
					'family' => "'Stint Ultra Expanded', cursive",
				),
			);
			return apply_filters( 'it_boom_bar_google_fonts', $fonts );
			}

		/**
		 * Prints the header for the bar
		 *
		 * @since 0.2
		*/
		function print_bar_header( $bar ) {
			echo '<div id="boom_bar-' . absint( $bar->id ) . '" boom_bar-id="' . absint( $bar->id ) . '" class="boom_bar boom_bar-' . esc_attr( $bar->type ) . ' boom_bar_' . esc_attr( $bar->color_scheme ) . '">';
			if ( 'yes' == $bar->closable ) {
				?>
				<script type="text/javascript">var it_boom_bar_cookieExp={}; it_boom_bar_cookieExp['<?php echo esc_js( $bar->id ); ?>'] = <?php echo esc_js ( $bar->cookie_exp ); ?>;</script>
				<a class="boom_bar_close boombar-hide-if-no-js" href="" title="<?php esc_attr_e( __( 'Close Bar', 'it-l10n-boombar' ) ); ?>">&times;</a>
				<?php
			}
			?><div class="boom_bar-inner-container"><?php
		}

		/**
		 * Prints the bar's main content
		 *
		 * Switch to determine bar type and then prints appropriate content

		 * @param object IT_Boom_Bar_Single_Bar object for specfic bar
		 * @uses esc_ur()
		 * @uses esc_attr()
		 * @uses esc_attr()
		 * @uses is_user_logged_in()
		 * @uses home_url()
		 * @uses IT_Boom_Bar_Latest_Tweet::get_text()
		 * @uses IT_Boom_Bar_Latest_Tweet::get_link()
		 * @since 0.1
		 * @return null
		*/
		function print_bar_content( $bar ) {
			if ( 'text' == $bar->type ) {
				$link = '';
				if ( $bar->link_text && $bar->link_url )
					$link = ' <a href="' . esc_url( $bar->link_url ) . '" target="' . esc_attr( $bar->links_target ) . '">' . esc_attr( $bar->link_text ) . '</a>';
				echo '<p class="boom_bar-text">' . esc_html( $bar->text ) . $link . '</p>';
			} else if ( 'login' == $bar->type ) {
				if ( ! is_user_logged_in() ) {
					$user_login = empty( $_POST['log'] ) ? __( 'username', 'it-l10n-boombar' ) : $_POST['log'];
					?>
					<form name="it_boom_bar_loginform" id="it_boom_bar_loginform" action="<?php echo esc_url( wp_login_url() ); ?>" method="post">
						<p>
							<input type="text" name="log" id="it_boom_bar_user_login" class="input" value="<?php esc_attr_e( stripslashes( $user_login ) ); ?>" size="20" />
						</p>
						<p>
							<input type="password" name="pwd" id="it_boom_bar_user_pass" class="boombar-hide-if-js input" value="" size="20" />
							<input type="text" name="pwd_text" id="it_boom_bar_user_pass_text" class="boombar-hide-if-no-js input" value="<?php esc_attr_e( __( 'password', 'it-l10n-boombar' ) ); ?>" size="20" />
						</p>
						<p class="submit">
							<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="<?php esc_attr_e( __( 'Log In', 'it-l10n-boombar' ) ); ?>" />
						</p>
					</form>
					<?php
				}
			} else if ( 'tweet' == $bar->type ) {
				include_once( $this->_pluginPath . '/classes/latest-tweet.php' );
				$latest = new IT_Boom_Bar_Latest_Tweet( $bar->twitter_un );
				$latest->set_link_target( $bar->links_target );
				$latest->set_tweet_data();
				if ( ! $latest->wp_error ) {
					$tweet = $latest->get_text();
					$url = $latest->get_link();
					echo '<a class="it_boom_bar_latest_tweet_link" href="' . esc_url( $url ) . '" target="' . esc_attr( $bar->links_target ) . '">@' . esc_attr( $bar->twitter_un ) . ':</a> <span class="it_boom_bar_latest_tweet_content">' . $tweet . '</a>';
				} else {
					foreach ( $latest->wp_error->get_error_codes() as $code ) {
						$message = $latest->wp_error->get_error_message( $code );

						if ( current_user_can( 'edit_posts' ) )
							echo '<p>' . sprintf( __( 'Error: %s', 'it-l10n-boombar' ), $message ) . "</p>\n";

						echo '<span style="display:none">' . esc_html( $code ) . "</span>\n";
					}
				}
			}
		}

		/**
		 * Closes off the bar div. Echos the output.
		 *
		 * @param object IT_Boom_Bar_Single_Bar object for specfic bar
		 * @since 0.1
		 * @return null
		*/
		function print_bar_footer( $bar ) {
			echo '</div></div>';
		}

		/**
		 * Inits dev tools if requested. Helpful for troubleshooting.
		 *
		 * Developers and plugin users can disable this by adding the following code to their theme's function.php file
		 * add_filter( 'it_disable_boom_bar_dev_tools', '__return_true' );
		 *
		 * @since 0.2
		 * @return null;
		*/
		function maybe_init_dev_tools() {
			if ( empty( $_GET['it_boom_bar_dev_tools'] ) )
				return;

			require_once( $this->_pluginPath . '/classes/dev-tools.php' );
			$it_boom_bar_dev_tools = new IT_Boom_Bar_Dev_Tools();
		}

		/**
		 * Inserts correct body class via JS if current theme has not done so already
		 *
		 * @uses IT_Boom_Bar::get_boom_bar_body_classes()
		 * @since 1.0.1
		 * @return null
		 */
		function maybe_modify_body_class() {
			$existing = (array) $this->get_boom_bar_body_classes( array() );
			$existing = reset( $existing );
			?>
			<script type="text/javascript">
				jQuery(function(){
					if ( ! jQuery('body').hasClass('<?php echo esc_js( $existing ); ?>') ) {
						jQuery('body').addClass('<?php echo esc_js( $existing ); ?>');
						it_boombar_adjust_heights(jQuery('.boom_bar').css('height'));
						it_boombar_adjust_static_bottom_width();
					}
				});
			</script>
			<?php
		}
	}
}

// Init plugin
if ( ! isset( $_GET['no_bb'] ) ) // For testing
	$it_boom_bar = new IT_Boom_Bar();


/**
 * iThemes Updater
 *
 * @since 1.0.4
 * @param object $updater Updater object
 * @return void
*/
function ithemes_boombar_updater_register( $updater ) { 
	$updater->register( 'boombar', __FILE__ );
}
add_action( 'ithemes_updater_register', 'ithemes_boombar_updater_register' );
require( dirname( __FILE__ ) . '/lib/updater/load.php' );
