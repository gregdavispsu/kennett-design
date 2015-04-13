<?php
global $mbpro_nonce_all_in_one_offer;
?>

<script type="text/javascript">	
	jQuery(document).ready(function() {
		var form_data = jQuery("#offers_form").serialize();
		form_data += "&action=mbpro_all_in_one_offer";

		jQuery.ajax({
			type: "POST",
			url: "<?php echo admin_url('admin-ajax.php') ?>",
			data: form_data,
			dataType: "json",
			success: function(result) {
				if (result != "") {
					if (result != "error") {
						jQuery("#all_in_one_price_title").html(result.all_in_one_price);
						jQuery("#all_in_one_price_content").html(result.all_in_one_price);
						jQuery("#all_in_one_discount").html(result.all_in_one_discount);
						jQuery("#all_in_one_button_packs_count").html(result.button_packs_count);
						jQuery("#all_in_one_everything_normal_total_cost").html(result.everything_normal_total_cost);
						
						// Set values for hidden form fields
						jQuery("#all_in_one_id_hidden_product").val(result.all_in_one_id);
						jQuery("#all_in_one_id_hidden_options").val("");
						jQuery("#all_in_one_id_hidden_quantity").val("1");
						
						// Set names for hidden form fields
						jQuery("#all_in_one_id_hidden_product").attr("name", "products[" + result.all_in_one_id + "][product]");
						jQuery("#all_in_one_id_hidden_options").attr("name", "products[" + result.all_in_one_id + "][options][]");
						jQuery("#all_in_one_id_hidden_quantity").attr("name", "products[" + result.all_in_one_id + "][quantity]");
					}
				}
			}
		});
		
		return false;
	});
</script>

<form id="offers_form">
	<input type="hidden" id="api_action" name="api_action" value="all_in_one_offer" />
	<?php wp_nonce_field($mbpro_nonce_all_in_one_offer['action'], $mbpro_nonce_all_in_one_offer['name']) ?>
</form>

<div class="option-container">
	<div class="title big centered">
		<?php printf(__('Everything for $99', 'maxbuttons-pro')) ?>
	</div>
	<div class="inside">
		<p><?php printf(__('Our best deal is the All-In-One package, which gets you everything we have for only $99.', 'maxbuttons-pro')) ?></p>
		<p><?php printf(__('This includes all %s%s%s current button packs%s and all new button packs for one year.', 'maxbuttons-pro'), '<strong>', '<span id="all_in_one_button_packs_count">', '</span>', '</strong>') ?></p>
		<p><?php printf(__('You %ssave a massive 87%s%s compared to buying everything individually, %sregularly valued at over $700.%s', 'maxbuttons-pro'), '<strong>', '%', '</strong>', '<strong>', '</strong>') ?></p>

		<form action="<?php echo mbpro_get_cart_url() ?>" method="post">
			<input id="all_in_one_id_hidden_product" type="hidden" name="products[][product]" value="" />
			<input id="all_in_one_id_hidden_options" type="hidden" name="products[][options][]" value="" />
			<input id="all_in_one_id_hidden_quantity" type="hidden" name="products[][quantity]" value="" />
			<input type="hidden" name="cart" value="add" />
			<div align="center">
				<input type="submit" name="addtocart" value="<?php _e('Buy All-In-One Package', 'maxbuttons-pro') ?>" class="button-primary wide" />
			</div>
		</form>
	</div>
</div>

