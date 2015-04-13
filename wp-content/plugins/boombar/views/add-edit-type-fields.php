<ul class="meta-options bar-type-fields">
	<li><label for="bar_type"><?php _e( 'Type of Content:', 'it-l10n-boombar' ); ?></label><select name="bar_type" id="bar_type"><?php echo $it_boom_bar->admin->get_bar_type_select_options( $this->get_settings_value( 'type' ) ); ?></select></li>
	<li id="bar_text_line" class="hide-if-js">
		<label for="bar_text"><?php _e( 'Bar Text:', 'it-l10n-boombar' ); ?></label><input type="text" name="bar_text" id="bar_text" value="<?php esc_attr_e( $this->get_settings_value( 'text' ) ); ?>" /><br /><span class="description"><?php _e( 'This is the content of your Boom Bar.', 'it-l10n-boombar' ); ?></span>
	</li>
	<li id="bar_link_text_line" class="hide-if-js">
		<label for="bar_link_text"><?php _e( 'Link Text:', 'it-l10n-boombar' ); ?></label><input type="text" name="bar_link_text" id="bar_link_text" value="<?php esc_attr_e( $this->get_settings_value( 'link_text' ) ); ?>" /><br /><span class="description"><?php _e( "This will appear as a link after the bar's main content.", 'it-l10n-boombar' ); ?></span><br />
	</li>
	<li id="bar_link_url_line" class="hide-if-js">
		<label id="label_bar_link_url" for="bar_link_url"><?php _e( 'Link URL:', 'it-l10n-boombar' ); ?></label><input type="text" name="bar_link_url" id="bar_link_url" value="<?php echo esc_url( $this->get_settings_value( 'link_url' ) ); ?>" /><br /><span class="description"><?php _e( 'This is the URL for the Link Text.', 'it-l10n-boombar' ); ?></span>
	</li>
	<li id="bar_text_desc_line" class="hide-if-js">
		<span class="description"><?php _e( "Only used with 'Text' bar type.", 'it-l10n-boombar' ); ?></span>
	</li>
	<li id="bar_twitter_un_line" class="hide-if-js"><label for="bar_twitter_un"><?php _e( 'Twitter Username:', 'it-l10n-boombar' ); ?> </label><input type="text" name="bar_twitter_un" id="bar_twitter_un" value="<?php esc_attr_e( $this->get_settings_value( 'twitter_un' ) ); ?>" /><br /><span class="description"><?php _e( 'The username for the Twitter account.', 'it-l10n-boombar' ); ?></span></li>
	<li id="bar_twitter_un_desc_line" class="hide-if-js"><span class="description"><?php _e( "Only used with 'Twitter' bar type.", 'it-l10n-boombar' ); ?></span></li>
</ul>
