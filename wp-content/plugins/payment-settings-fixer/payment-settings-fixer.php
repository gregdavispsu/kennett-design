<?php
/**
 * @package Payment Settings Fixer
 * @version 0.1
 */
/*
Plugin Name: Payment Settings Fixer
Plugin URI: https://github.com/joshfeck
Description: A little patch to fix a strange issue in some WPEngine sites' admin for the Event Espresso 3 Payment settings page
Author: Josh Feck
Version: 0.1
Author URI: https://github.com/joshfeck
*/

add_action( 'action_hook_espresso_display_gateway_settings', 'ee_display_gateway_settings_on_wpengine' );

function ee_display_gateway_settings_on_wpengine() { ?>
   <script>
      jQuery(document).ready(function($){
         $('.postbox h3, .handlediv').click( function() {
            $($(this).parent().get(0)).toggleClass('closed');
         });
      });
   </script>
<?php }