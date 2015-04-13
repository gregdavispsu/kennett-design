<?php
global $pagenow, $post;
// Set everyone by default if a new bar
$everyone_checked = ( 'post-new.php' == $pagenow ) ? 1 :$this->get_settings_value( array( 'conditions', 'who', 'everyone' ) );
?>
<div class="meta-options">
	<span class="settings-label"><?php _e( 'Audience:', 'it-l10n-boombar' ); ?> </span>
	<ul style="margin-top:0;margin-bottom:0;display:inline-block;">
		<li><label for="conditions_who_everyone"><input type="checkbox" id="conditions_who_everyone" name="bar_conditions[who][everyone]" value="1" <?php checked( '1', $everyone_checked ); ?> /> <?php _e( 'Everyone', 'it-l10n-boombar' ); ?></label></li>
		<li><label for="conditions_who_authenticated"><input type="checkbox" id="conditions_who_authenticated" name="bar_conditions[who][authenticated]" value="1" <?php checked( '1', $this->get_settings_value( array( 'conditions', 'who', 'authenticated' ) ) ); ?> /> <?php _e( 'All authenticated users', 'it-l10n-boombar' ); ?></label></li>
		<li><label for="conditions_who_unauthenticated"><input type="checkbox" id="conditions_who_unauthenticated" name="bar_conditions[who][unauthenticated]" value="1" <?php checked( '1', $this->get_settings_value( array( 'conditions', 'who', 'unauthenticated' ) ) ); ?> /> <?php _e( 'All unauthenticated visitors', 'it-l10n-boombar' ); ?></label></li>
		<li><label for="conditions_who_first_time"><input type="checkbox" id="conditions_who_first_time" name="bar_conditions[who][first_time_visitors]" value="1" <?php checked( '1', $this->get_settings_value( array( 'conditions', 'who', 'first_time_visitors' ) ) ); ?> /> <?php _e( 'First time visitors', 'it-l10n-boombar' ); ?></label></li>
		<li><label for="conditions_who_returning"><input type="checkbox" id="conditions_who_returning" name="bar_conditions[who][returning_visitors]"  value="1" <?php checked( '1', $this->get_settings_value( array( 'conditions', 'who', 'returning_visitors' ) ) ); ?> /> <?php _e( 'Returning visitors', 'it-l10n-boombar' ); ?></label></li>
	</ul>
	<br /><span class="description"><?php _e( 'Limit who can see the bar by toggling the above checkboxes.', 'it-l10n-boombar' ); ?></span>
</div>
<div class="meta-options">
	<ul>
		<li>
			<label for="bar_startdate"><?php _e( 'Start Date:', 'it-l10n-boombar' ); ?></label><input type="text" name="bar_conditions[when][startdate]" value="<?php esc_attr_e( $this->get_settings_value( array( 'conditions', 'when', 'startdate' ) ) ); ?>" id="bar_startdate" />
			&nbsp;<input type="checkbox" name="bar_default_on_startdate" id="bar_default_on_startdate" value="1" <?php checked( '1', $this->get_settings_value( 'default_on_startdate' ) ); ?> />&nbsp;<label style="vertical-align:middle;" for="bar_default_on_startdate">Set as the default bar on the start date?</label>
			<br /><span class="description"><?php _e( 'If set, the bar will not be displayed before this date. eg:', 'it-l10n-boombar' );?> <?php echo date( 'Y-m-d' ); ?></span>
		</li>
		<li><label for="bar_enddate"><?php _e( 'End Date:', 'it-l10n-boombar' ); ?></label><input type="text" name="bar_conditions[when][enddate]" id="bar_enddate" value="<?php esc_attr_e( $this->get_settings_value( array( 'conditions', 'when', 'enddate' ) ) ); ?>" /><br /><span class="description"><?php _e( 'If set, the bar will not be displayed after this date. eg:', 'it-l10n-boombar' ); ?> <?php echo date( 'Y-m-d' ); ?></span></li>
	</ul>

<?php 
/****** 
Not sure we're going to use this. Just commenting it out of the UI for now 
<h4>What days of the week can this bar be displayed?</h4>
<ul>
	<li><label for="conditions_when_dw_0"><input type="checkbox" id="conditions_when_dw_0" name="bar_conditions[when][dayofweek][0]" value="1" <?php checked( '1', $this->get_settings_value( array( 'conditions', 'when', 'dayofweek', '0' ) ) ); ?> /> Sunday</label></li>
	<li><label for="conditions_when_dw_1"><input type="checkbox" id="conditions_when_dw_1" name="bar_conditions[when][dayofweek][1]" value="1" <?php checked( '1', $this->get_settings_value( array( 'conditions', 'when', 'dayofweek', '1' ) ) ); ?> /> Monday</label></li>
	<li><label for="conditions_when_dw_2"><input type="checkbox" id="conditions_when_dw_2" name="bar_conditions[when][dayofweek][2]" value="1" <?php checked( '1', $this->get_settings_value( array( 'conditions', 'when', 'dayofweek', '2' ) ) ); ?> /> Tuesday</label></li>
	<li><label for="conditions_when_dw_3"><input type="checkbox" id="conditions_when_dw_3" name="bar_conditions[when][dayofweek][3]" value="1" <?php checked( '1', $this->get_settings_value( array( 'conditions', 'when', 'dayofweek', '3' ) ) ); ?> /> Wednesday</label></li>
	<li><label for="conditions_when_dw_4"><input type="checkbox" id="conditions_when_dw_4" name="bar_conditions[when][dayofweek][4]" value="1" <?php checked( '1', $this->get_settings_value( array( 'conditions', 'when', 'dayofweek', '4' ) ) ); ?> /> Thursday</label></li>
	<li><label for="conditions_when_dw_5"><input type="checkbox" id="conditions_when_dw_5" name="bar_conditions[when][dayofweek][5]" value="1" <?php checked( '1', $this->get_settings_value( array( 'conditions', 'when', 'dayofweek', '5' ) ) ); ?> /> Friday</label></li>
	<li><label for="conditions_when_dw_6"><input type="checkbox" id="conditions_when_dw_6" name="bar_conditions[when][dayofweek][6]" value="1" <?php checked( '1', $this->get_settings_value( array( 'conditions', 'when', 'dayofweek', '6' ) ) ); ?> /> Saturday</label></li>
</ul>
***/
?>
</div>
