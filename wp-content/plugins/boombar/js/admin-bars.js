var it_boom_bar_intialized_color_pickers = new Array();
jQuery(function() {
	// Init color pickers
	jQuery('.it_boom_bar_colorpicker').each(function(){
		it_boom_bar_enable_color_picker( jQuery(this) );
	});

	// Show hide additional bar type fields based on dropdown selection
	function it_boom_bar_onchange_type() {
		var selected = jQuery(this).find(":selected").val();

		jQuery( '#bar_twitter_un_line, #bar_text_line, #bar_link_text_line, #bar_link_url_line' ).addClass('hide-if-js');
		if ( 'tweet' == selected ) {
			jQuery("#bar_twitter_un_line").removeClass('hide-if-js');
		} else if ( 'text' == selected ) {
			jQuery("#bar_text_line, #bar_link_text_line, #bar_link_url_line").removeClass('hide-if-js');
		}
	}
	jQuery('#bar_type').change(it_boom_bar_onchange_type).triggerHandler("change");

	function it_boom_bar_onchange_color_scheme() {
		var selected = jQuery(this).find(":selected").val();

		jQuery( '.custom_colors' ).addClass('hide-if-js');
		if ( 'custom' == selected ) {
			jQuery(".custom_colors").removeClass('hide-if-js');
		} else {
			jQuery(".custom_colors").addClass('hide-if-js');
		}
	}
	jQuery('#bar_color_scheme').change(it_boom_bar_onchange_color_scheme).triggerHandler("change");

	// Hide cookie duration option if bar is not closable
	function it_boom_bar_onchange_closable() {
		var selected = jQuery(this).find(":selected").val();

		jQuery( '#bar_cookie_line' ).addClass('hide-if-js');
		if ( 'yes' == selected ) {
			jQuery("#bar_cookie_line").removeClass('hide-if-js');
		} else {
			jQuery("#bar_cookie_line").addClass('hide-if-js');
		}
	}
	jQuery('#bar_closable').change(it_boom_bar_onchange_closable).triggerHandler("change");
	
	// tab in textareas - Extracted from wp-admin/js/common.js
	jQuery('#bar_custom_css').bind('keydown.wpevent_InsertTab', function(e) {
		var el = e.target, selStart, selEnd, val, scroll, sel;

		if ( e.keyCode == 27 ) { // escape key
			jQuery(el).data('tab-out', true);
			return;
		}

		if ( e.keyCode != 9 || e.ctrlKey || e.altKey || e.shiftKey ) // tab key
			return;

		if ( jQuery(el).data('tab-out') ) {
			jQuery(el).data('tab-out', false);
			return;
		}

		selStart = el.selectionStart;
		selEnd = el.selectionEnd;
		val = el.value;

		try {
			this.lastKey = 9; // not a standard DOM property, lastKey is to help stop Opera tab event. See blur handler below.
		} catch(err) {}

		if ( document.selection ) {
			el.focus();
			sel = document.selection.createRange();
			sel.text = '\t';
		} else if ( selStart >= 0 ) {
			scroll = this.scrollTop;
			el.value = val.substring(0, selStart).concat('\t', val.substring(selEnd) );
			el.selectionStart = el.selectionEnd = selStart + 1;
			this.scrollTop = scroll;
		}

		if ( e.stopPropagation )
			e.stopPropagation();
		if ( e.preventDefault )
			e.preventDefault();
	});
});

function it_boom_bar_enable_color_picker( node ) {
	if ( true === it_boom_bar_intialized_color_pickers[jQuery(node).attr("name")] )
		return;

	jQuery(node).css( 'background-color', jQuery(node).val() ).css( 'color', jQuery(node).val() ).css( 'width', '25px' );
	jQuery(node).unbind( 'focus' );

	jQuery(node).ColorPicker({
		onChange: function( color, el ) {
			jQuery(el).val( color );
			jQuery(node).css( 'background-color', color ).css( 'color', color );
		},
		onBeforeShow: function () {
			color = ( '' !== this.value ) ? this.value : '#9EDCF0';
			jQuery(this).ColorPickerSetColor( color );
		}
	}).bind('keyup',
		function() {
			jQuery(this).ColorPickerSetColor( this.value );
		}
	);
	it_boom_bar_intialized_color_pickers[jQuery(node).attr( 'name' )] = true;
}
