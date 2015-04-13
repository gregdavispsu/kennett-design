<?php


if(isset($_POST['alter_charset'])) {
    
    global $mbpro_installed_version;
    global $wpdb;
    $table_name = mbpro_get_buttons_table_name();

    // IMPORTANT: There MUST be two spaces between the PRIMARY KEY keywords
    // and the column name, and the column name MUST be in parenthesis.
    $sql = "ALTER TABLE " . $table_name . " CONVERT TO CHARACTER SET utf8";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $wpdb->query($wpdb->prepare($sql));
    $response = 'CHARSET now utf_8 COLLATE utf8_general_ci';

} else {
    $response = '';
}

?>

<div id="maxbuttons">
	<div class="wrap">
		<div class="icon32">
			<a href="http://maxbuttons.com" target="_blank"><img src="<?php echo MAXBUTTONS_PRO_PLUGIN_URL ?>/images/mb-32.png" alt="MaxButtons" /></a>
		</div>
		
		<h2 class="title"><?php _e('MaxButtons Pro: Support', 'maxbuttons-pro') ?></h2>
		
		<div class="logo">
			<?php _e('Brought to you by', 'maxbuttons-pro') ?>
			<a href="http://maxfoundry.com" target="_blank"><img src="<?php echo MAXBUTTONS_PRO_PLUGIN_URL ?>/images/max-foundry.png" alt="Max Foundry" /></a>
			<?php printf(__('makers of %sMaxGalleria%s and %sMaxInbound%s', 'maxbuttons-pro'), '<a href="http://maxgalleria.com/?ref=mbpro" target="_blank">', '</a>', '<a href="http://maxinbound.com/?ref=mbpro" target="_blank">', '</a>') ?>
		</div>

		<div class="clear"></div>
		
		<div class="main">
			<h2 class="tabs">
				<span class="spacer"></span>
				<a class="nav-tab" href="<?php echo admin_url() ?>admin.php?page=maxbuttons-controller&action=buttons"><?php _e('Buttons', 'maxbuttons-pro') ?></a>
				<a class="nav-tab" href="<?php echo admin_url() ?>admin.php?page=maxbuttons-packs"><?php _e('Packs', 'maxbuttons-pro') ?></a>
				<a class="nav-tab" href="<?php echo admin_url() ?>admin.php?page=maxbuttons-export"><?php _e('Export', 'maxbuttons-pro') ?></a>
                <a class="nav-tab nav-tab-active" href=""><?php _e('Settings', 'maxbuttons-pro') ?></a>
				<a class="nav-tab" href="<?php echo admin_url() ?>admin.php?page=maxbuttons-support"><?php _e('Support', 'maxbuttons-pro') ?></a>
				<a class="nav-tab" href="<?php echo admin_url() ?>admin.php?page=maxbuttons-license"><?php _e('License', 'maxbuttons-pro') ?></a>
			</h2>

            <h3><?php _e('WARNING: We strongly recommend backing up your database before altering the charset of the MaxButtons table in your WordPress database.', 'maxbuttons-pro') ?></h3>

            <h3><?php _e('The button below should help fix the "foreign character issue" some people experience when using MaxButtons. If you use foreign characters in your buttons and after saving see ????, use this button.', 'maxbuttons-pro') ?></h3>
        
            <form action="" method="POST">
                <input type="submit" name="alter_charset" value="<?php _e('Change MaxButtons Table To UTF8', 'maxbuttons-pro') ?>" /> <?php echo $response; ?>
            </form>
			
			
		</div>
		
		<div class="offers">
			<?php include 'maxbuttons-offers.php' ?>
		</div>
	</div>
</div>
