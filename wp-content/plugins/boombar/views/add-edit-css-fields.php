<div class="meta-options">
	<p class="description"><?php _e( 'You can target this bar uniquely with the CSS id:', 'it-l10n-boombar' ); ?> <code>boom_bar-<?php esc_attr_e( $post->ID );?></code><br />
	<?php _e( 'Example:', 'it-l10n-boombar' ); ?>
	<code>#boom_bar-<?php esc_attr_e( $post->ID );?> { color:#000; }</code>
	</p>
	<textarea rows="1" cols="40" name="bar_custom_css" id="bar_custom_css"><?php esc_html_e( $this->get_settings_value( 'custom_css' ) ); ?></textarea>
</div>
