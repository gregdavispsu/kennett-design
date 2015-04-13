<?php
/**
 * Bars Class - Determines what bars should be displayed on this page view
 *
 * @package IT_Boom_Bar
 * @since 0.1
*/
if ( ! class_exists( 'IT_Boom_Bar_Bars' ) ) {
	class IT_Boom_Bar_Bars {

		/**
		 * @var array $current_conditions A multi-dimentional array of the current pageview's conditions
		 * @since 0.1
		*/
		var $current_conditions;

		/**
		 * @var array $visible_bars An array of objects containing all published bars
		 * @since 0.1
		*/
		var $visible_bars = array();

		/**
		 * @var array $current_bars An array of objects containing all bars to be shown on current pageview
		 * @since 0.1
		*/
		var $current_bars = array();

		/**
		 * @var int $top_margin Offset for the top margin
		 * @since 0.1
		*/
		var $top_margin = 0;

		/**
		 * Class Constructor. Add's correct actions / filters based on where we are in WordPress
		 *
		 * @uses is_admin()
		 * @uses add_action()
		 * @uses add_filter()
		 * @since 0.1
		 * @return null
		*/
		function IT_Boom_Bar_Bars() {
			add_action( 'init', array( $this, 'register_bars_post_type' ) );
			if ( ! is_admin() ) {
				add_action( 'template_redirect', array( $this, 'load_current_bars' ) );
			} else {
				add_filter( 'manage_it_boom_bar_posts_columns', array( $this, 'filter_admin_columns' ) );
				add_action( 'manage_it_boom_bar_posts_custom_column' , array( $this, 'add_admin_column_data' ), 10, 2 );
				add_filter( 'manage_edit-it_boom_bar_sortable_columns', array( $this, 'sort_existing_bars_by_priority' ) );
				add_filter( 'post_row_actions', array( $this, 'remove_quick_edit' ) );
				add_filter( 'screen_layout_columns', array( $this, 'modify_add_edit_page_layout' ) );
				//add_filter( 'pre_get_posts', array( $this, 'set_default_sort' ) );

				// Force user settings to 1 column view for add / edit bars
				add_filter( 'get_user_option_screen_layout_it_boom_bar', '__return_true' );
			}
		}

		/**
		 * Register's our custom post type with WP
		 *
		 * @uses register_post_type()
		 * @since 0.1
		 * @return null
		*/
		function register_bars_post_type() {
			register_post_type( 'it_boom_bar', array(
				'labels' => array(
					'name'               => _x( 'Bars', 'post type general name', 'it-l10n-boombar' ),
					'singular_name'      => _x( 'Bar', 'singular name', 'it-l10n-boombar' ),
					'menu_name'          => _x( 'Boom Bar' , 'name in admin menu', 'it-l10n-boombar' ),
					'name_admin_bar'     => _x( 'Boom Bar', 'add new from admin bar', 'it-l10n-boombar' ),
					'all_items'          => _x( 'Existing Bars', 'existing bars', 'it-l10n-boombar' ),
					'add_new'            => _x( 'Add New Bar', 'add new bar', 'it-l10n-boombar' ),
					'add_new_item'       => _x( 'Add New Bar', 'add new page title', 'it-l10n-boombar' ),
					'edit_item'          => _x( 'Edit Bar', 'edit bar', 'it-l10n-boombar' ),
					'view_item'          => _x( 'View Bar', 'view bar', 'it-l10n-boombar' ),
					'not_found_in_trash' => _x( 'No Bars found in trash', 'no bars in trash', 'it-l10n-boombar' ),
				),
				'public'               => false,
				'show_ui'              => true,
				'capability_type'      => 'post',
				'map_meta_cap'         => true,
				'hierarchical'         => false,
				'rewrite'              => false,
				'query_var'            => false,
				'delete_with_user'     => false,
				'supports'             => array( 'title', ),
				'menu_position'        => 100,
				'register_meta_box_cb' => array( $this, 'add_metaboxes' ),
			) );
		}

		/**
		 * Determines correct bars to display on current page
		 *
		 * @uses IT_Boom_Bar_Bars::build_current_conditions()
		 * @uses IT_Boom_Bar_Bars::set_visible_bars()
		 * @uses IT_Boom_Bar_Bars::set_current_bars()
		 * @since 0.1
		 * @return null
		*/
		function load_current_bars() {
			// Grab the current page conditionals
			$this->build_current_conditions();
			$this->set_visible_bars();
			$this->set_current_bars();
		}

		/**
		 * Sets current conditions for this page view
		 *
		 * @todo Use WP time localization functions for when object
		 * @uses IT_Boom_Bar_Bars::get_default_bar_for_pageview()
		 * @uses is_user_logged_in()
		 * @uses IT_Boom_Bar_Bars::is_first_time_visitor()
		 * @uses IT_Boom_Bar_Bars::is_google_referral()
		 * @since 0.1
		 * @return null
		*/
		function build_current_conditions() {
			global $current_user;

			$conditions = new stdClass();
			$conditions->who = new stdClass();
			$conditions->when = new stdClass();
			$conditions->referer = new stdClass();

			$conditions->default_bar              = $this->get_default_bar_for_pageview();
			$user_authenticated                   = is_user_logged_in();
			$conditions->who->everyone            = ! $user_authenticated;
			$conditions->who->authenticated_users = $user_authenticated;
			$conditions->who->first_time_visitor  = $this->is_first_time_visitor();
			$conditions->who->returning_visitors  = ! $conditions->who->first_time_visitor;
			$conditions->when->startdate          = current_time( 'timestamp' );
			$conditions->when->enddate            = $conditions->when->startdate;
			$conditions->when->dayofweek          = date( 'w', $conditions->when->startdate );
			$conditions->referer->google          = $this->is_google_referral();

			$this->current_conditions             = $conditions;
		}

		/**
		 * Grabs the default bar for a frontend pageview
		 *
		 * If this is a singular pageview, see if there is an override to the default bar
		 * Otherwise, use the default bar, if one exists
		 * If one exists, but user has closed it and cookie is still valid, return 0
		 *
		 * @uses is_singular()
		 * @uses get_post_meta()
		 * @since 0.2
		 * @return boolean|int False if no bar. Bar ID if we found one.
		*/
		function get_default_bar_for_pageview() {
			global $it_boom_bar, $post;

			// Maybe update the default bar
			$this->maybe_update_default_bar();

			if ( is_singular() && ! empty( $post->ID ) && $bar = get_post_meta( $post->ID, '_it_boom_bar_pt_override', true ) )
				$default = $bar;
			else
				$default = $it_boom_bar->options['default_bar'];

			// Return 0 if current user has cookie saying they closed the bar
			if ( ! empty( $_COOKIE['it_boom_bar_' . $default] ) )
				$default = 0;

			return $default;
		}

		/**
		 * This checks to see if a non-default bar with a start date equal to current date needs to become the new default bar
		 *
		 * @since 0.1.3
		 * @return void
		*/
		function maybe_update_default_bar() {
			global $it_boom_bar;
			$bars_args = array(
				'posts_per_page' => -1,
				'post_type'      => 'it_boom_bar',
				'post_status'    => 'any',
				'meta_query'     => array(
					'relation' => 'AND',
					array( 
						'key'     => '_it_boom_bar_set_default_on',
						'value'   => date( 'Ymd' ),
						'compare' => '<=',
						'type'    => 'DATE',
					),
					array(
						'key'     => '_it_boom_bar_set_default_on',
						'compare' => 'EXISTS',
					),
				),
			);
			if ( ! $bars = get_posts( $bars_args ) )
				return;

			foreach ( $bars as $key => $bar ) {
				if ( $options = $it_boom_bar->get_plugin_options() ) {
					$options['default_bar'] = $bar->ID;
					$it_boom_bar->update_plugin_options( $options );
					delete_post_meta( $bar->ID, '_it_boom_bar_set_default_on' );
				}
			}
		}

		/**
		 * Reads cookies to determine if they've been here before
		 *
		 * Not technically 'first time' visitor. more like, in recent history.
		 * If it is first time, lets set the cookie.
		 *
		 * @since 0.1
		 * @return boolean True if first time visitor. False if not.
		*/
		function is_first_time_visitor() {
			$time        = current_time( 'timestamp' );
			$twenty4ago  = $time - DAY_IN_SECONDS;
			$first_visit = empty( $_COOKIE['it_boombar_first_visit'] ) ? $time : $_COOKIE['it_boombar_first_visit'];

			// Set cookie if it doesn't exist
			if ( empty( $_COOKIE['it_boombar_first_visit'] ) ) {
				$this->set_first_visit_cookie( $first_visit );
			}

			// Return true if first visit was within last 24 hours
			if ( $first_visit >= $twenty4ago ) {
				return true;
			}

			// Return false
			return false;
		}

		/**
		 * Set COOKIE
		 *
		 * @since 1.1.21
		 *
		 * @param int $first_visit unix timestamp of first visit time
		 * @return void
		 */
		function set_first_visit_cookie( $first_visit ) {
			setcookie( 'it_boombar_first_visit', $first_visit, strtotime( '+10 years' ), COOKIEPATH, COOKIE_DOMAIN );
		}

		/**
		 * Returns referral is from Google, return search term. Return false, otherwise.
		 *
		 * Use HTTP_REFERER for simple solution
		 *
		 * @since 0.1
		 * @return boolean True if from Google. False if not.
		*/
		function is_google_referral() {
			if ( ! isset( $_SERVER['HTTP_REFERER'] ) )
				return false;

			$referer = parse_url( $_SERVER['HTTP_REFERER'] );
			if ( false === strpos( $referer['host'], 'google.' ) )
				return false;

			parse_str( $referer['query'], $vars );
			return empty( $vars['q'] ) ? '' : $vars['q'];
		}

		/**
		 * Queiries the DB for all visible bars and sets the associated property
		 *
		 * @uses get_posts()
		 * @since 0.1
		 * @return null
		*/
		function set_visible_bars() {
			global $it_boom_bar;

			$default_params = array(
				'post_type'      => 'it_boom_bar',
				'post_status'    => 'publish',
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
				'posts_per_page' => $it_boom_bar->options['bar_max_page'],
			);

			// If we're not showing a default bar, return 0 bars
			if ( empty( $this->current_conditions->default_bar ) )
				return array();

			// Set bar for this page
			$default_params['p'] = $this->current_conditions->default_bar;

			// Query DB for visible bar
			$this->visible_bars = get_posts( $default_params );
		}

		/**
		 * Filters the visible bars array for current conditions
		 *
		 * Since 0.2 - only one bar will be returned.
		 *
		 * @since 0.1
		 * @return null
		*/
		function set_current_bars() {
			$visible_bars = $this->visible_bars;

			foreach( $visible_bars as $id => $post_obj ) {
				$bar = new IT_Boom_Bar_Single_Bar( $post_obj );

				if ( $this->is_current_bar( $bar ) ) {
					$this->current_bars[$bar->id]	= $bar;
					unset( $bar );
				}
			}
		}

		/**
		 * Validates that a given bar object matches the current conditons
		 *
		 * @param object $bar An instance of IT_Boom_Bar_Single_Bar
		 * @uses is_user_logged_in()
		 * @since 0.1
		 * @return boolean True if the user can see the referenced bar. False if it can't.
		*/
		function is_current_bar( $bar ) {

			$current_user_can_see_bar = false;

			// If bar is login and user is logged in, don't show it
			if ( 'login' == $bar->type && is_user_logged_in() )
				return false;

			// It doesn't matter who you are if we have a limited timeframe. Blacklist outside that timeframe
			if ( ! empty( $bar->conditions['when']['startdate'] ) && ( mysql2date( 'U', $bar->conditions['when']['startdate'] . '00:01:01' ) > $this->current_conditions->when->startdate ) )
				return false;
			if ( ! empty( $bar->conditions['when']['enddate'] ) && ( mysql2date( 'U', $bar->conditions['when']['enddate'] . '23:59:00' ) < $this->current_conditions->when->enddate ) )
				return false;

			// If the current day of the week wasn't checked, don't show the bar. Only exceptions is if no days of the week are checked.
			if ( ! empty( $bar->conditions['when']['dayofweek'] ) && is_array( $bar->conditions['when']['dayofweek'] ) ) {
				if ( ! array_key_exists( $this->current_conditions->when->dayofweek, $bar->conditions['when']['dayofweek'] ) )
					return false;
			}
			/** If we made it here, we aren't going to hide the bar based on when it is being shown **/

			// It's not very conditional if everyone was checked...
			if ( ! empty( $bar->conditions['who']['everyone'] ) )
				$current_user_can_see_bar = true;

			// If All Authenticated Users is checked and the visitor IS logged in, they can see the bar
			if ( ! empty( $bar->conditions['who']['authenticated'] ) && $this->current_conditions->who->authenticated_users )
				$current_user_can_see_bar = true;

			// If All UNauthenticated Users is checked and the visitor is NOT logged in, they can see the bar
			if ( ! empty( $bar->conditions['who']['unauthenticated'] ) && ! $this->current_conditions->who->authenticated_users )
				$current_user_can_see_bar = true;

			// If First Time Visitors is checked and the visitor is here for the first time, they can see the bar
			if ( ! empty( $bar->conditions['who']['first_time_visitors'] ) && $this->current_conditions->who->first_time_visitor )
				$current_user_can_see_bar = true;

			// If Returning Visitors is checked and the visitor is has been here before, they can see the bar
			if ( ! empty( $bar->conditions['who']['returning_visitors'] ) && $this->current_conditions->who->returning_visitors )
				$current_user_can_see_bar = true;

			// We should probably not prevent it from being seen if no 'who' conditions were checked
			if ( empty( $bar->conditions['who'] ) )
				$current_user_can_see_bar = true;

			return $current_user_can_see_bar;
		}

		/**
		 * Metabox callback for it_boom_bar post type
		 *
		 * Hooked from the register_post_type. All metaboxes get added/removed from in here
		 *
		 * @uses remove_meta_box()
		 * @uses add_meta_box()
		 * @since 0.1
		 * @return null
		*/
		function add_metaboxes() {
			remove_meta_box( 'submitdiv', null, 'side' );
			add_meta_box( 'it_boom_bar_type', __( 'Bar Content', 'it-l10n-boombar' ), array( $this, 'add_bar_type_form_fields'), 'it_boom_bar', 'normal', 'high' );
			add_meta_box( 'it_boom_bar_settings', __( 'General Settings', 'it-l10n-boombar' ), array( $this, 'add_bar_settings_form_fields'), 'it_boom_bar', 'normal', 'high' );
			add_meta_box( 'it_boom_bar_css', __( 'Custom CSS', 'it-l10n-boombar' ), array( $this, 'add_custom_css_field'), 'it_boom_bar', 'normal', 'high' );
			add_meta_box( 'submitdiv', __( 'Publish' ), array( $this, 'add_publish_meta_box' ), 'it_boom_bar', 'normal' );
		}

		/**
		 * Bar Settings fields
		 *
		 * Includes views/ file with HTML field elements
		 *
		 * @since 0.1
		 * @return null
		*/
		function add_bar_settings_form_fields() {
			global $it_boom_bar, $it_boom_bar_admin;
			require_once( $it_boom_bar->_pluginPath . '/views/add-edit-settings-fields.php' );
		}

		/**
		 * Bar Type fields
		 *
		 * Includes views/ file with HTML field elements
		 *
		 * @since 0.1
		 * @return null
		*/
		function add_bar_type_form_fields() {
			global $it_boom_bar, $it_boom_bar_admin;
			require_once( $it_boom_bar->_pluginPath . '/views/add-edit-type-fields.php' );
		}

		/**
		 * Customized Publish Meta Box
		 *
		 * Includes the customize publish meta box
		 * @since 0.1
		 * @return null
		*/
		function add_publish_meta_box( $post ) {
			global $it_boom_bar;
			require_once( $it_boom_bar->_pluginPath . '/views/publish-meta-box.php' );
		}

		/**
		 * Custom CSS Field
		 *
		 * Includes views/ file with HTML field elements
		 *
		 * @since 0.1
		 * @return null
		*/
		function add_custom_css_field( $post ) {
			global $it_boom_bar;
			require_once( $it_boom_bar->_pluginPath . '/views/add-edit-css-fields.php' );
		}

		/**
		 * Returns the current value for a field in the add / edit bar screen
		 * 
		 * 1) Is there a value already set
		 * 2) If not, what's the default
		 * 3) Also - take empty into consideration
		 *
		 * @uses get_post_status()
		 * @uses IT_Boom_Bar::get_plugin_options()
		 * @since 0.1
		 * @return string Value of bar setting
		*/
		function get_settings_value( $setting ) {
			global $it_boom_bar;

			if ( is_array( $setting ) ) {
				$existing_value = $it_boom_bar->admin->current_bar->$setting[0];
				$real_setting = $setting[0];
				unset( $setting[0] );
				foreach ( $setting as $key ) {
					if ( isset( $existing_value[$key] ) )
						$existing_value = $existing_value[$key];
					else
						$existing_value = false;
				}
				$setting = $real_setting;
			} else {
				$existing_value = empty( $it_boom_bar->admin->current_bar->$setting ) ? '' : $it_boom_bar->admin->current_bar->$setting;
			}

			if( empty( $existing_value ) ) {
				if ( 'publish' == get_post_status( $it_boom_bar->admin->current_bar->id ) )
					$existin_value = '';
				else
					$existing_value = $it_boom_bar->get_plugin_options( 'bar_' . $setting );
			}

			return $existing_value;
		}

		/**
		 * Modifies the columns that appear in the Existing Bars table
		 *
		 * @param array $columns Existing columns passed to us by the WP hook
		 * @since 0.1
		 * @return array Associated array of columns
		*/
		function filter_admin_columns( $columns ) {
			unset( $columns['date'] );
			unset( $columns['builder_layout'] );
			$columns['type'] = __( 'Content Type', 'it-l10n-boombar' );
			return $columns;
		}

		/**
		 * Populates our custom data in the cells for our custom columns
		 *
		 * @uses IT_Boom_Bar_Bars::get_bar_types()
		 * @since 0.1
		 * @return string Data for cell
		*/
		function add_admin_column_data( $name, $post_id ) {
			global $it_boom_bar;
			include_once( $it_boom_bar->_pluginPath . '/classes/single-bar.php' );
			if(isset($variable_IT_Boom_Bar_Single_Bar)){
			if ( $bar = new IT_Boom_Bar_Single_Bar( $post_id ) ) {
				switch( $name ) {
					case 'type' :
						$types = $this->get_bar_types();
						echo $types[$bar->type];
						break;
					case 'visibility' :
						break;
					case 'priority' :
						echo $bar->priority;
						break;
					default:
					break;
				}
			}
		}
	}
		/**
		 * Sorts the Existing bars row based on the priority column
		 *
		 * Disabled in 0.2
		 *
		 * @param array Associated array of existing sort
		 * @since 0.1
		 * @return array
		*/
		function sort_existing_bars_by_priority( $columns ) {
			//$columns['priority'] = 'menu_order';
			return $columns;
		}

		/**
		 * An assoc array of bar types
		 *
		 * @since 0.1
		 * @return array Bar Types
		*/
		function get_bar_types() {
			$types = array(
				'text'  => __( 'Custom Text', 'it-l10n-boombar' ),
				'tweet' => __( 'Latest Tweet', 'it-l10n-boombar' ),
				'login' => __( 'Log In', 'it-l10n-boombar' ),
			);
			return $types;
		}

		/**
		 * NOT CURRENTLY USED
		 *
		 * Sets the default sort by priority for Existing Bars view in WP Admin
		 * @since 0.1
		*/
		function set_default_sort( $query ) {
			global $current_screen;

			if ( 'edit-it_boom_bar' != $current_screen->id )
				return;

			if ( ! get_query_var( 'orderby' ) )
				$query->set( 'orderby', 'menu_order' );
			if ( ! get_query_var( 'order' ) )
				$query->set( 'order', 'asc' );
		}

		/**
		 * Hooked filter to remove quick edit for Existing Bars
		 *
		 * @param array $actions Array of action available to be preformd on each existing bar
		 * @since 0.2
		 * @return array Filtered array of actions
		*/
		function remove_quick_edit( $actions ) {
			global $current_screen;

			if ( 'edit.php?post_type=it_boom_bar' == $current_screen->parent_file )
				unset( $actions['inline boombar-hide-if-no-js'] );
			return $actions;
		}

		/**
		 * Set the max columns option for the add / edit bar page.
		 *
		 * @param $columns Existing array of how many colunns to show for a post type
		 * @since 0.2
		 * @return arra Filtered array
		*/
		function modify_add_edit_page_layout( $columns ) {
			$columns['it_boom_bar'] = 1;
			return $columns;
		}
	}
}
