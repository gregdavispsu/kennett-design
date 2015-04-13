<?php
if ( ! class_exists( 'IT_Boom_Bar_Dev_Tools' ) ) {
	class IT_Boom_Bar_Dev_Tools {

		var $id           = false;
		var $title        = false;
		var $name         = false;
		var $type         = false;
		var $color_scheme = false;
		var $bg_color     = false;
		var $text_color   = false;
		var $link_color   = false;
		var $border_color = false;
		var $font         = false;
		var $closable     = false;
		var $cookie_exp   = false;
		var $location     = false;
		var $position     = false;
		var $priority     = false;
		var $conditions   = false;
		var $custom_css   = false;
		var $text         = false;
		var $twitter_un   = false;
		var $link_text    = false;
		var $link_url     = false;
		var $default      = 'No';

		/**
		 * Constructor
		 *
		 * @uses IT_Boom_Bar_Dev_Tools::init()
		 * @since 0.2
		 * @return null
		*/
		function IT_Boom_Bar_Dev_Tools() {
			add_action( 'init', array( $this, 'init' ) );
		}

		/**
		 * Inits the plugin, loads the vars, etc if not disabled
		 *
		 * @uses apply_filter()
		 * @uses add_action()
		 * @since 0.2
		 * @return null
		*/
		function init() {
			if ( apply_filters( 'it_disable_boom_bar_dev_tools', false ) )
				return;

			add_action( 'template_redirect', array( $this, 'load_vars' ) );
			add_action( 'template_redirect', array( $this, 'do_overrides' ) );
			add_action( 'template_redirect', array( $this, 'enqueue_scripts_styles' ) );
			add_action( 'wp_footer', array( $this, 'print_dev_tools' ) );
		}

		/**
		 * Loads the class properties for this page view
		 *
		 * @since 0.2
		 * @return null
		*/
		function load_vars() {
			global $it_boom_bar;

			$this->version = $it_boom_bar->_version;

			// If we have a current bar, grab the raw properties from it and load them.
			if ( ! empty( $it_boom_bar->bars->current_bars ) ) {
				$current_bar_vars = get_object_vars( reset( $it_boom_bar->bars->current_bars ) );
				foreach( (array) $current_bar_vars as $var => $value ) {
					$this->{$var} = $value;
				}
			}

			// Remove unneeded vars
			unset( $this->name );
			unset( $this->priority );
			
			// Format conditions
			foreach( (array) $this->conditions as $condition => $value ) {
				$output = $condition . ': ';
				if ( is_array( $value ) )
					$output .= print_r( $value, true ) . '<br />';
				else
					$output .= $value . '<br />';
			}
			$this->conditions = isset( $output ) ? $output : 'No conditions set';

			// Convert '0' Font Value to 'Theme Styles'
			$this->font = $this->font ? $this->font : 'Theme Styles'; 
		}

		/**
		 * Allows devs to override specific settings for a bar
		 *
		 * @since 0.2
		 * @return null
		*/
		function do_overrides() {
			global $it_boom_bar;
			$overrideable_settings = get_object_vars( $this );
			unset( $overrideable_settings['id'] );
			unset( $overrideable_settings['title'] );
			unset( $overrideable_settings['conditions'] );
			unset( $overrideable_settings['custom_css'] );
			unset( $overrideable_settings['default'] );
			unset( $overrideable_settings['bg_color'] );
			unset( $overrideable_settings['text_color'] );
			unset( $overrideable_settings['link_color'] );
			unset( $overrideable_settings['border_color'] );
			unset( $overrideable_settings['cookie_exp'] );
			unset( $overrideable_settings['text'] );
			unset( $overrideable_settings['twitter_un'] );
			unset( $overrideable_settings['link_text'] );
			unset( $overrideable_settings['link_url'] );

			$this->overrideable_settings = $overrideable_settings;

			foreach( $overrideable_settings as $key => $value ) {
				if ( isset( $_GET[$key] ) ) {
					$it_boom_bar->bars->current_bars[$this->id]->$key = $_GET[$key];
				}
			}

			if ( empty( $it_boom_bar->bars->current_bars[$this->id]->twitter_un ) )
				$it_boom_bar->bars->current_bars[$this->id]->twitter_un = 'ithemes';
		}

		/**
		 * Enqueues my scripts and styles
		 *
		 * @uses wp_enqueue_style();
		 * @since 0.2
		 * @return null
		*/
		function enqueue_scripts_styles() {
			global $it_boom_bar;
			wp_enqueue_style( 'it_boom_bar_dev_tools', $it_boom_bar->_pluginURL . '/styles/dev-tools.css' );
			wp_enqueue_script( 'it_boom_bar_dev_tools', $it_boom_bar->_pluginURL . '/js/frontend-dev-tools.js', array( 'jquery-ui-draggable' ) );
		}

		/**
		 * Prints a div with our tool options
		 *
		 * @since 0.2
		*/
		function print_dev_tools() {
			?>
			<div id="it_boom_bar_dev_tools">
				<div id="it_boom_bar_dev_tools_handle">Move Me</div>
				<form action="" method="get">
					<table>
						<tr id="heading"><th class="heading">Setting</th><th class="heading">Current Value</th><th class="heading"><span title="<?php esc_attr_e( 'Version ' . $this->version ); ?>">Dev Options</span></th></tr>
						<?php foreach( (array) get_object_vars( $this ) as $setting => $value ) {
							if ( 'overrideable_settings' == $setting || 'version' == $setting )
								continue;
							echo '<tr><td class="setting">' . $setting . '</td><td class="value">' . $value . '</td>';
							echo '<td>' . $this->get_overrideable_selects( $setting ) . '</td>';
							echo '</tr>';
						} ?>
						<tr class="boombar-hide-if-no-js">
							<td class="setting">Calculated margins:</td>
							<td id="calculated_margins"></td>
						</tr>
					</table>
					<input type="hidden" name="it_boom_bar_dev_tools" value="1" />
					<input type="submit" id="it_boom_bar_dev_tools_submit" value="Update" />
				</form>
				<form action="" method="get">
					<input type="hidden" name="it_boom_bar_dev_tools" value="1" />
					<input type="submit" id="it_boom_bar_dev_tools_submit" value="Reset" />
				</form
			</div>
			<?php
		}

		/**
		 * Builds the options for an overrideable select or return nothing
		 *
		*/
		function get_overrideable_selects( $setting ) {

			if ( ! isset( $this->overrideable_settings[$setting] ) )
				return '';

			return '<select name="' . $setting . '" id="it_boom_bar_dev_tools_' . $setting . '">' . $this->get_dev_options( $setting ) . '</option>';
		}

		/**
		 * Returns possible options for setting
		 *
		*/
		function get_dev_options( $setting ) {
			global $it_boom_bar;

			$output = '';
			switch( $setting ) {
				case 'type' :
					$options = array( 'text', 'tweet', 'login' );
					break;
				case 'color_scheme' :
					$options = array( 'custom', 'lime', 'silver', 'light', 'inset', 'flatdark', 'seasonal' );
					break;
				case 'font' :
					$options = array_keys( $it_boom_bar->get_google_fonts() );
					break;
				case 'closable' :
					$options = array( 'yes', 'no' );
					break;
				case 'location' :
					$options = array( 'top', 'bottom' );
					break;
				case 'position' :
					$options = array( 'fixed', 'static' );
					break;
				default :
					$options = array( $setting );
				
			}

			foreach ( (array) $options as $option ) {
				$selected = isset( $_GET[$setting] ) ? $_GET[$setting] : $this->$setting;
				$output .= '<option value="' . $option. '" ' . selected( $option, $selected, false ) . '>' . $option . '</option>';
			}
			return $output;
		}
	}
}
