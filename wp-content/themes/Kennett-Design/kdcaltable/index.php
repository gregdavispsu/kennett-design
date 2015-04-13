<?php
/*
  Plugin Name: Event Espresso Template - Calendar Table
  Plugin URI: http://www.eventespresso.com
  Description: This template creates a list of events, displayed in a table. It can display events by category and/or maximum number of days. [EVENT_CUSTOM_VIEW template_name="calendar-table" max_days="30" category_identifier="concerts"]
  Version: 1.1
  Author: Event Espresso
  Author URI: http://www.eventespresso.com
  Copyright 2013 Event Espresso (email : support@eventespresso.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA02110-1301USA

*/

//Requirements: CSS skills to customize styles, some renaming of the table columns, Espresso WP User Add-on (optional)
// Parameter: show_featured=true/false. If set to true, the dates will be replaced with the featured images.
// Parameter: change_title="something". If set the Band/Artist default title will be change to the string provided.

//The end of the action name (example: "action_hook_espresso_custom_template_") should match the name of the template. In this example, the last part the action name is "calendar-table",
add_action('action_hook_espresso_custom_template_kdcaltable','espresso_custom_template_kdcaltable');

function espresso_custom_template_kdcaltable(){

	global $org_options, $this_event_id, $events, $ee_attributes;

	//Extract shortcode attributes, if any.
	extract($ee_attributes);

	if(isset($ee_attributes['show_featured'])) { $show_featured = $ee_attributes['show_featured']; }

	if(isset($ee_attributes['change_title'])) { $change_title = $ee_attributes['change_title']; }

	//Load the css file
 	/* wp_register_style( 'espresso_cal_table_css', WP_PLUGIN_URL. "/".plugin_basename(dirname(__FILE__)) .'/style.css' );  */
	wp_register_style( 'espresso_cal_table_css', 'http://kennettdesign.wpengine.com/wp-content/themes/Kennett-Design/kdcaltable/style.css' ); 
	wp_enqueue_style( 'espresso_cal_table_css');

	//Clears the month name
	$temp_month = '';

	//Uncomment to view the data being passed to this file
	//echo '<h4>$events : <pre>' . print_r($events,true) . '</pre> <span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span></h4>';
	?>

<table class="cal-table-list">
	<?php
		foreach ($events as $event){
			//Debug
			$this_event_id		= $event->id;
			$this_event_desc	= explode('<!--more-->', $event->event_desc);
			$this_event_desc 	= array_shift($this_event_desc);
			$member_only		= !empty($event->member_only) ? $event->member_only : '';
			$event_meta			= unserialize($event->event_meta);
			$externalURL 		= $event->externalURL;
			$registration_url 	= !empty($externalURL) ? $externalURL : espresso_reg_url($event->id);
			$live_button 		= '<a id="a_register_link-'.$event->id.'" href="'.$registration_url.'"><img class="buytix_button" src="http://kennettdesign.wpengine.com/wp-content/themes/Kennett-Design/kdcaltable/register-now.png" alt="Buy Tickets"></a>';
			if ( ! has_filter( 'filter_hook_espresso_get_num_available_spaces' ) ){
				$open_spots		= apply_filters('filter_hook_espresso_get_num_available_spaces', $event->id); //Available in 3.1.37
			}else{
				$open_spots		= get_number_of_attendees_reg_limit($event->id, 'number_available_spaces');
			}
			$featured_image		= isset($event_meta['event_thumbnail_url']) ? $event_meta['event_thumbnail_url'] : FALSE;
			$event_status = event_espresso_get_status($event->id);
			
			if($open_spots < 1 && $event->allow_overflow == 'N') {
				$live_button = '<img class="buytix_button" src="'.WP_PLUGIN_URL. "/".plugin_basename(dirname(__FILE__)) .'/closed.png" alt="Closed">';
			} else if ($open_spots < 1 && $event->allow_overflow == 'Y'){
				$live_button = !empty($event->overflow_event_id) ? '<a href="'.espresso_reg_url($event->overflow_event_id).'"><img class="buytix_button" src="'.WP_PLUGIN_URL. "/".plugin_basename(dirname(__FILE__)) .'/waiting.png" alt="Join Waiting List"></a>' : '<img class="buytix_button" src="http://kennettdesign.wpengine.com/wp-content/themes/Kennett-Design/kdcaltable/closed.png" alt="Closed">';
			}
				
			if ( isset($event_status) &&  $event_status == 'NOT_ACTIVE' ) {
				$live_button = '<img class="buytix_button" src="http://kennettdesign.wpengine.com/wp-content/themes/Kennett-Design/kdcaltable/closed.png" alt="Closed">';
			}
				
			//Build the table headers
			$full_month = event_date_display($event->start_date, "F");
			if ($temp_month != $full_month){
				?>
				<tr class="cal-header-month">
					<th class="cal-header-month-name" id="calendar-header-<?php echo $full_month; ?>" colspan="3"><?php echo $full_month; ?></th>
				</tr>
				<tr class="cal-header">
					<th><?php echo !isset($show_featured) || $show_featured === 'false' ? __('Date','event_espresso') :  '' ?></th>
					<th class="th-event-info"><?php if(isset($change_title)) { echo $change_title; } else { _e('Band / Artist','event_espresso'); } ?></th>
					<th class="th-tickets"><?php _e('Tickets','event_espresso'); ?></th>
				</tr>
				<?php
				$temp_month = $full_month;
			}

			//Gets the member options, if the Members add-on is installed.
			$member_options = get_option('events_member_settings');

			//If enough spaces exist then show the form
			//Check to see if the Members plugin is installed.
			if ( function_exists('espresso_members_installed') && espresso_members_installed() == true && !is_user_logged_in() && ($member_only == 'Y' || $member_options['member_only_all'] == 'Y') ) {
				event_espresso_user_login();
			}else{
				?>
		<tr class="event-row" id="event-row-<?php echo $this_event_id; ?>">


			<?php
	
			if(isset($show_featured ) && $show_featured === 'true') { ?>
				<td class="td-fet-image"><div class="">
					<img src="<?php echo $featured_image; ?>" />
				</div></td>
			<?php } else { ?>
			<td class="td-date-holder"><div class="dater">
					<div class="cal-day-title"><?php echo event_date_display($event->start_date, "l"); ?></div>
					<div class="cal-day-num"><?php echo event_date_display($event->start_date, "j"); ?></div>
					<div><span><?php echo event_date_display($event->start_date, "M"); ?></span></div>
				<?php } ?>
				</div>
			</td>
	
			<td class="td-event-info"><span class="event-title"><a href="<?php echo $registration_url ?>"><?php echo stripslashes_deep($event->event_name); ?></a></span>
				<p>
					<?php _e('When:', 'event_espresso'); ?>
					<?php echo event_date_display($event->start_date); ?>  at 
					<?php echo event_espresso_get_time($this_event_id, $format = 'start_time'); ?>		  												<!-- 38solutions -->
					<?php _e(' - Add to calendar: ', 'event_espresso'); ?>																				<!-- 38solutions -->
					<?php echo apply_filters('filter_hook_espresso_display_ical', (array)$event ); ?> <br />											<!-- 38solutions -->
					<?php _e('Where:', 'event_espresso'); ?>																							<!-- 38solutions -->
					<?php echo stripslashes_deep($event->venue_address.', '.$event->venue_city.', '.$event->venue_state); ?><br />
					<?php _e('Price: ', 'event_espresso'); ?>
					<?php echo  $org_options['currency_symbol'].$event->event_cost; ?> </p>
					<?php _e('Registered: ', 'event_espresso'); ?>																					<!-- 38solutions -->
					<?php echo do_shortcode('[ATTENDEE_NUMBERS event_id="'.$this_event_id.'" type="num_attendees"]');?>								<!-- 38solutions -->
					<?php _e(' / Limit: ', 'event_espresso'); ?> 																					<!-- 38solutions -->
					<?php echo do_shortcode('[ATTENDEE_NUMBERS event_id="'.$this_event_id.'" type="reg_limit"]');?>									<!-- 38solutions -->
				<?php echo espresso_format_content(array_shift(explode('<!--more-->', $event->event_desc))); //Includes <p> tags ?></td>
			<td class="td-event-register"><?php echo $live_button ?></td>
		</tr>
	<?php
			}// close is_user_logged_in
		 } //close foreach ?>
</table>
<?php
}

/**
 * hook into PUE updates
 */
//Update notifications
add_action('action_hook_espresso_template_kdcaltable_update_api', 'espresso_template_kdcaltable_load_pue_update');
function espresso_template_kdcaltable_load_pue_update() {
	global $org_options, $espresso_check_for_updates;
	if ( $espresso_check_for_updates == false )
		return;
		
	if (file_exists(EVENT_ESPRESSO_PLUGINFULLPATH . 'class/pue/pue-client.php')) { //include the file 
		require(EVENT_ESPRESSO_PLUGINFULLPATH . 'class/pue/pue-client.php' );
		$api_key = $org_options['site_license_key'];
		$host_server_url = 'http://eventespresso.com';
		$plugin_slug = array(
			'premium' => array('p'=> 'espresso-template-calendar-table'),
			'prerelease' => array('b'=> 'espresso-template-calendar-table-pr')
			);
		$options = array(
			'apikey' => $api_key,
			'lang_domain' => 'event_espresso',
			'checkPeriod' => '24',
			'option_key' => 'site_license_key',
			'options_page_slug' => 'event_espresso',
			'plugin_basename' => plugin_basename(__FILE__),
			'use_wp_update' => FALSE
		);
		$check_for_updates = new PluginUpdateEngineChecker($host_server_url, $plugin_slug, $options); //initiate the class and start the plugin update engine!
	}
}