<?php
//List Attendees Template
//Show a list of attendees using a shortcode
//[LISTATTENDEES]
//[LISTATTENDEES limit="30"]
//[LISTATTENDEES show_expired="false"]
//[LISTATTENDEES show_deleted="false"]
//[LISTATTENDEES show_secondary="false"]
//[LISTATTENDEES show_gravatar="true"]
//[LISTATTENDEES paid_only="true"]
//[LISTATTENDEES show_recurrence="false"]
//[LISTATTENDEES event_identifier="your_event_identifier"]
//[LISTATTENDEES category_identifier="your_category_identifier"]

//Please refer to this page for an updated lsit of shortcodes: http://eventespresso.com/forums/?p=592


/*Example CSS for your themes style sheet:

li.attendee_details{
	display:block;
	margin-bottom:20px;
	background: #ECECEC;
	border:#CCC 1px solid;
}
.espresso_attendee{
	width:400px;
	padding:5px;
}
.espresso_attendee img.avatar{
	float:left;
	padding:5px;
}
.clear{
	clear:both;
}
*/

function cmp1($a, $b)
{
    return strcmp($a->start_date, $b->start_date);
}


//The following code displays your list of attendees.
//The processing for this function is managed in the shortcodes.php file.

if (!function_exists('event_espresso_show_attendess')) {
	function event_espresso_show_attendess($sql,$show_gravatar,$paid_only, $sort=''){
		//echo $sql;
		global $wpdb,$this_is_a_reg_page;
		$events = $wpdb->get_results($sql);
		
		usort($events, "cmp1");
		
		
		foreach ($events as $event){
			$event_id = $event->id;
			$event_name = stripslashes_deep($event->event_name);
			/*if (!$this_is_a_reg_page){
				$event_desc = do_shortcode(stripslashes_deep($event->event_desc));
			}*/

			//This variable is only available using the espresso_event_status function which is loacted in the Custom Files Addon (http://eventespresso.com/download/plugins-and-addons/custom-files-addon/)
			$event_status = function_exists('espresso_event_status') ? ' - ' . espresso_event_status($event_id) : '';
			//Example usage in the event title:
			/*<h2><?php _e('Attendee Listing For: ','event_espresso'); ?><?php echo $event_name . ' - ' . $event_status?> </h2>*/
			$event_meta['start_time'] = empty($event->start_time) ? '' : $event->start_time;
			$event_meta['start_date'] = $event->start_date;

?>
<!--
					<?php _e('When:', 'event_espresso'); ?>
					<?php echo event_date_display($event->start_date); ?>  at 
					<?php echo event_espresso_get_time($this_event_id, $format = 'start_time'); ?>		  												
					<?php _e(' - Add to calendar: ', 'event_espresso'); ?>																				
					<?php echo apply_filters('filter_hook_espresso_display_ical', (array)$event ); ?> <br />											
					<?php _e('Where:', 'event_espresso'); ?>																							
					<?php echo stripslashes_deep($event->venue_address.', '.$event->venue_city.', '.$event->venue_state); ?><br />
					<?php _e('Price: ', 'event_espresso'); ?>
					<?php echo  $org_options['currency_symbol'].$event->event_cost; ?> </p>
					<?php _e('Registered: ', 'event_espresso'); ?>																					
					<?php echo do_shortcode('[ATTENDEE_NUMBERS event_id="'.$this_event_id.'" type="num_attendees"]');?>								
					<?php _e(' / Limit: ', 'event_espresso'); ?> 																					
					<?php echo do_shortcode('[ATTENDEE_NUMBERS event_id="'.$this_event_id.'" type="reg_limit"]');?>									
-->

<div class="event-display-boxes ui-widget">

		<h3 class="attendee_list_date"> <?php echo event_date_display($event->start_date); ?>  at 
		<?php echo event_espresso_get_time($event->id, 'start_time'); ?></h3>	
		<h4 class="event_title ui-widget-header ui-corner-top">	<?php echo $event_name . ' - Registrations (paid/unpaid): ' . do_shortcode('[ATTENDEE_NUMBERS event_id="'.$event->id.'" type="num_completed_slash_incomplete"]'); ?></h4> 

		<div class="event-data-display ui-widget-content ui-corner-bottom">

		<?php //echo wpautop($event_desc); ?>
			<ol class="attendee_list">
				<?php
					$a_sql = "SELECT * FROM " . EVENTS_ATTENDEE_TABLE . " WHERE event_id='" . $event_id . "'";
					/* $a_sql .= $paid_only == 'true'? " AND (payment_status='Completed' OR payment_status='Pending' OR payment_status='Refund') ":''; */
					$a_sql .= $sort;
					/* echo $a_sql; */
					$attendees = $wpdb->get_results($a_sql);
					foreach ($attendees as $attendee){
						$id = $attendee->id;
						$lname = $attendee->lname;
						$fname = $attendee->fname;
						$city = $attendee->city;
						$state = $attendee->state;
						$country = $attendee->state;
						$email = $attendee->email;
						$quantity = $attendee->quantity;
						$gravatar = $show_gravatar == 'true'? get_avatar( $email, $size = '100', $default = 'http://www.gravatar.com/avatar/' ) : '';
						$city_state = $city != '' || $state != '' ? '<br />' . ($city != '' ? $city :'') . ($state != '' ? ', ' . $state :' ') :'';
						$amt_paid = $attendee->amount_pd;
						$price_option = $attendee->price_option;
						$payment_status = $attendee->payment_status;
				
						//These are example variables to show answers to questions
						$custom_question_1 = do_shortcode('[EE_ANSWER q="12" a="'.$id.'"]');  /* Special Intructions */
						
						$kd_kid_name = do_shortcode('[EE_ANSWER q="13" a="'.$id.'"]');
						$custom_question_2 = 'name: '. $kd_kid_name . ' age: ' .do_shortcode('[EE_ANSWER q="14" a="'.$id.'"]');  /* Kid's Name & Age */
						
						$attendee_name = stripslashes_deep($fname . ' ' . $lname);
					

						if ($payment_status == 'Completed') {
							$pmt_status = 'paid'; 
							$pmt_class = 'pmt-paid';
						} else {
							$pmt_status = 'UNPAID';
							$pmt_class = 'pmt-unpaid';
						}

						$attendee_print_line = ' - ' . $quantity . '  ' . $price_option . ' ' . $custom_question_1;
						
						if (!empty($kd_kid_name)) {
							$attendee_print_line = $attendee_print_line . ' : ' . $custom_question_2;
						}
				
				?>
					
						<li class="attendee_details"> <span class="espresso_attendee"><?php echo $attendee_name . ' ' ?></span><span class="<?php echo $pmt_class ?>"><?php echo ('[' . $pmt_status . '] ')?></span><span class="espresso_attendee"><?php echo $attendee_print_line . '</br>'; ?></span>
						
						<div class="clear"></div>
<!--
						<span class="attendee_questions"><?php echo ' - Special Instructions: ' . $custom_question_1 . '</br>'; ?></span>
						<span class="attendee_kid_questions"><?php echo ' -- for kid events: ' . $custom_question_2 . '</br>'; ?></span>
-->
						</li>
				<?php
					}
				?>
			</ol>
	</div>
</div>
<?php
		}
	}
}


