<?php
/**
 * Single Bar Class
 *
 * @package IT_Boom_Bar
 * @since 0.1
*/
if ( ! class_exists( 'IT_Boom_Bar_Single_Bar' ) ) {
	class IT_Boom_Bar_Single_Bar {

		/**
		 * @var int $id The WP post id for the bar
		 * @since 0.1
		*/
		var $id;

		/**
		 * @var string $title The WP post_title for the bar
		 * @since 0.1
		*/
		var $title;

		/**
		 * @var string $name The WP post_name for the bar
		 * @since 0.1
		*/
		var $name;

		/**
		 * @var string $type The type of content the bar holds eg: text, tweet, login
		 * @since 0.1
		*/
		var $type;

		/**
		 * @var string $color_scheme The chosen color scheme for the bar
		 * @since 0.2
		*/
		var $color_scheme;

		/**
		 * @var string $bg_color The background color for the bar if it has a custom $color_scheme
		 * @since 0.2
		*/
		var $bg_color;

		/**
		 * @var string $text_color The text color for the bar if it has a custom $color_scheme
		 * @since 0.2
		*/
		var $text_color;

		/**
		 * @var string $link_color The link color for the bar if it has a custom $color_scheme
		 * @since 0.2
		*/
		var $link_color;

		/**
		 * @var string $border_color The border color for the bar if it has a custom $color_scheme
		 * @since 0.2
		*/
		var $border_color;

		/**
		 * @var string @font The Google Font for the bar
		 * @since 0.2
		*/
		var $font;

		/**
		 * @var boolean $closable True if the bar can be closed by the viewer
		 * @since 0.2
		*/
		var $closable;

		/**
		 * @var int $cookie_exp Time in seconds to expire closed bar cookies
		 * @since 0.2
		*/
		var $cookie_exp;

		/**
		 * @var string $location Does the bar appear the top or bottom of the page
		 * @since 0.2
		*/
		var $location;

		/**
		 * @var string $position CSS value: Fixed or Static
		 * @since 0.2
		*/
		var $position;

		/**
		 * @var string $position Link target attribute: _self or _blank
		 * @since 1.0.1
		*/
		var $links_target;

		/**
		 * @var int $id The WP post_order / boom bar priority for the bar
		 * @since 0.1
		*/
		var $priority;

		/**
		 * @var array $conditions An assoc array representing the conditions this bar should be shown under
		 * @since 0.1
		*/
		var $conditions;

		/**
		 * @var string $custom_css Any custom CSS rules provided for this bar
		 * @since 0.1
		*/
		var $custom_css;

		/**
		 * @var string $text This will contain custom text if $type is 'text'
		 * @since 0.1
		*/
		var $text;

		/**
		 * @var string $twitter_un This will contain the Twitter username when the $type is 'tweet'
		 * @since 0.1
		*/
		var $twitter_un;

		/**
		 * @var boolean $default_on_startdate If this value is true, the bar will become default on the startdate
		 * @since 0.1.3
		*/
		var $default_on_startdate;

		/**
		 * Class constructor
		 *
		 * Inits the class, calls methods that will load the data
		 *
		 * @param object|int $bar The WP post object or the WP post ID for a bar
		 * @uses IT_Boom_Bar_Single_Bar::is_valid_post_type()
		 * @uses IT_Boom_Bar_Single_Bar::get_bar_by_id()
		 * @uses IT_Boom_Bar_Single_Bar::set_bar()
		 * @since 0.1
		 * @return false
		*/
		function IT_Boom_Bar_Single_Bar( $bar=false ) {
			if ( is_object( $bar ) && $this->is_valid_post_type( $bar ) )
				$this->post_obj = $bar;
			else if ( is_numeric( $bar ) )
				$this->post_obj = $this->get_bar_by_id( $bar );
			else
				return; // Must have been called from new bar admin screen

			$this->set_bar();
		}

		/**
		 * Grabs a bar by its id
		 *
		 * @param int $id WP post ID of bar
		 * @uses IT_Boom_Bar_Single_Bar::is_valid_post_type()
		 * @since 0.1
		 * @return boolean|object False if no bar exists. WP Post type object if it does
		*/
		function get_bar_by_id( $id ) {
			$bar = get_post( $id );
			if ( $this->is_valid_post_type( $bar ) )
				return $bar;

			return false;
		}

		/**
		 * Validates WP post object as the correct bar type
		 *
		 * @param object $post_obj WP Post object
		 * @uses get_post_type
		 * @since 0.1
		 * @return boolean True if is the it_boom_bar post type
		*/
		function is_valid_post_type ( $post_obj ) {
			// Validate as a bar
			return ( is_object( $post_obj ) && 'it_boom_bar' == get_post_type( $post_obj ) );
		}

		/**
		 * Sets the bar property formatted correctly
		 *
		 * @uses get_post_meta()
		 * @uses maybe_unserialize()
		 * @since 0.1
		 * @return null
		*/
		function set_bar() {
			$p = $this->post_obj;
			unset( $this->post_obj );

			// Set object properties
			$this->id       = $p->ID;
			$this->title    = $p->post_title;
			$this->name     = $p->post_name;
			$this->type     = $p->post_type;
			$this->priority = $p->menu_order;

			// Set properties from meta values
			if ( $mv = get_post_meta( $p->ID, '_it_boom_bar', true ) ) {
				foreach ( $mv as $key => $value ) {
					$key = substr( $key, 4 );
					$this->$key = maybe_unserialize( $value );
				}
			}
		}
	}
}
