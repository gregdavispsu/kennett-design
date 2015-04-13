jQuery(function(){
	it_boom_bar_origBodyMarginTop           = jQuery(document.body).css('margin-top');
	it_boom_bar_origBodyMarginBottom        = jQuery(document.body).css('margin-bottom');
	it_boom_bar_origBodyFirstChildMarginTop = jQuery(document.body).children('div:visible:first').css('margin-top');
	it_boom_bar_origBodyMarginTopUnits      = it_boom_bar_origBodyMarginTop.substr(it_boom_bar_origBodyMarginTop.length - 2);
	it_boom_bar_origBodyMarginBottomUnits   = it_boom_bar_origBodyMarginBottom.substr(it_boom_bar_origBodyMarginBottom.length - 2);
	it_boom_bar_closed = false;

	// Hide / Show if JS functions
	jQuery('.boombar-hide-if-no-js').show();
	jQuery('.hide-if-js').hide();

	// User Login magic
	jQuery('#it_boom_bar_user_login').focus(function(){
		if ( 'username' == jQuery(this).val() ) {
			jQuery(this).val('').css('color','#000000');
		}
	});
	jQuery('#it_boom_bar_user_login').blur(function(){
		if ( '' == jQuery(this).val() ) {
			jQuery(this).css('color', '#DDDDDD').val('username');
		}
	});
	jQuery('#it_boom_bar_user_pass_text').focus(function(){
		jQuery(this).hide();
		jQuery('#it_boom_bar_user_pass').show().focus();
	});
	jQuery('#it_boom_bar_user_pass').blur(function() {
		if ( '' == jQuery(this).val() ) {
			jQuery(this).hide();
			jQuery('#it_boom_bar_user_pass_text').show();
		}
	});

	// Boom Bar Close functionality
	function it_boom_bar_close_bar(event) {
		event.preventDefault();
		jQuery('.boom_bar').hide();

		// Restore original body margins
		jQuery(document.body).css('margin-top', it_boom_bar_origBodyMarginTop);
		jQuery(document.body).css('margin-bottom', it_boom_bar_origBodyMarginBottom);
		
		// Remove class name
		jQuery(document.body).removeClass('boom_bar-static-top-above_wpab boom_bar-fixed-top-above_wpab boom_bar-static-top-below_wpab boom_bar-fixed-top-below_wpab boom_bar-static-top-no_wpab boom_bar-fixed-top-no_wpab boom_bar-fixed-bottom-above_wpab boom_bar-fixed-bottom-below_wpab boom_bar-fixed-bottom-no_wpab');

		// Set closed variable
		it_boom_bar_closed = true;

		// Set cookie
		var bar_id = jQuery('.boom_bar').attr('boom_bar-id');
		var date = new Date();
		date.setTime(date.getTime()+(it_boom_bar_cookieExp[bar_id]));
		var expires = "; expires="+date.toGMTString();
		document.cookie = "it_boom_bar_"+bar_id+"="+true+expires+"; path=/;";

		event.preventPropagation();
	}
	jQuery('.boom_bar_close').click(it_boom_bar_close_bar);

	// Stick wp-admin to top on scroll if Boom Bar is static and above wpadminbar
	jQuery(document).scroll(function(){
		if ( jQuery(document.body).hasClass('boom_bar-static-top-above_wpab') 
			|| jQuery(document.body).hasClass('boom_bar-static-top-above_wpab-hide') ) {
			if ( !jQuery('#wpadminbar').attr('data-top') ) {
				// If already fixed, then do nothing
				if (jQuery('#wpadminbar').hasClass('temp-fixed')) return;
				// Remember top position
				var offset = jQuery('#wpadminbar').offset()
				jQuery('#wpadminbar').attr('data-top', offset.top);
			}

			if (jQuery('#wpadminbar').attr('data-top') - jQuery('#wpadminbar').outerHeight() <= jQuery(this).scrollTop()) {
				jQuery('#wpadminbar').addClass('temp-fixed');
				jQuery(document.body).removeClass('boom_bar-static-top-above_wpab')
				jQuery(document.body).addClass('boom_bar-static-top-above_wpab-hide')
			}else {
				jQuery('#wpadminbar').removeClass('temp-fixed');
				jQuery(document.body).removeClass('boom_bar-static-top-above_wpab-hide')
				jQuery(document.body).addClass('boom_bar-static-top-above_wpab')
			}
		}
	});

	it_boombar_adjust_heights( jQuery( '.boom_bar' ).height() );
	it_boombar_adjust_static_bottom_width();

	jQuery(window).resize(function(event) {
		it_boombar_adjust_heights( jQuery('.boom_bar').height() );
		it_boombar_adjust_static_bottom_width();
	});

});

// Adjusts the width of a static bar if body has padding
function it_boombar_adjust_static_bottom_width() {

	// Grab the padding for the body element
	var paddingLeft  = jQuery(document.body).css('padding-left');
	var paddingRight = jQuery(document.body).css('padding-right');

	// I was originally detecting the margins like the padding above.
	// Auto Margins aren't playing well except in Chrome though. This calculates them differently
	var autoWidth   = ( jQuery(window).width() - jQuery(document.body).outerWidth() ) / 2;
	var marginLeft  = 0;
	var marginRight = 0;
	if ( autoWidth >= 0 )
		marginLeft = marginRight = autoWidth + 'px';

	// Grab the margins for the first visible child element of the body
	var firstChildMarginLeft = jQuery(document.body).children('div:visible:first').css('margin-left');
	var firstChildMarginRight = jQuery(document.body).children('div:visible:first').css('margin-right');

	// Add the margin and the padding together (jQuery always returns in px)
	marginLeft  = parseFloat(marginLeft) + parseFloat(paddingLeft) + 'px';
	marginRight = parseFloat(marginRight) + parseFloat(paddingRight) + 'px';

	// If the body's margin is 0 and one of the first child's left or right margins are greater than 0, compensate for that.
	if ( 0 == parseFloat(marginLeft) && ( parseFloat( firstChildMarginLeft ) > 0 || parseFloat( firstChildMarginRight > 0 ) ) ) {
		marginLeft  = firstChildMarginLeft;
		marginRight = firstChildMarginRight;
	}

	// Adjust the width for the bar if it's bottom and static
	jQuery('.boom_bar-static-bottom-below_wpab .boom_bar').css('margin-left', '-' + marginLeft);
	jQuery('.boom_bar-static-bottom-below_wpab .boom_bar').css('padding-left', marginRight);
	jQuery('.boom_bar-static-bottom-below_wpab .boom_bar').css('padding-right', marginRight);
	jQuery('.boom_bar-static-bottom-above_wpab .boom_bar').css('margin-left', '-' + marginLeft);
	jQuery('.boom_bar-static-bottom-above_wpab .boom_bar').css('padding-left', marginRight);
	jQuery('.boom_bar-static-bottom-above_wpab .boom_bar').css('padding-right', marginRight);
	jQuery('.boom_bar-static-bottom-no_wpab .boom_bar').css('margin-left', '-' + marginLeft);
	jQuery('.boom_bar-static-bottom-no_wpab .boom_bar').css('padding-left',  marginRight);
	jQuery('.boom_bar-static-bottom-no_wpab .boom_bar').css('padding-right', marginRight);

	// Adjust the width for a top static bar if the body element is position:relative
	if ( 'relative' == jQuery('body').css('position') ) { 
		jQuery('.boom_bar-static-top-below_wpab .boom_bar').css('margin-left', '-' + marginLeft);
		jQuery('.boom_bar-static-top-below_wpab .boom_bar').css('padding-left', marginRight);
		jQuery('.boom_bar-static-top-below_wpab .boom_bar').css('padding-right', marginRight);
		jQuery('.boom_bar-static-top-above_wpab .boom_bar').css('margin-left', '-' + marginLeft);
		jQuery('.boom_bar-static-top-above_wpab .boom_bar').css('padding-left', marginRight);
		jQuery('.boom_bar-static-top-above_wpab .boom_bar').css('padding-right', marginRight);
		jQuery('.boom_bar-static-top-no_wpab .boom_bar').css('margin-left', '-' + marginLeft);
		jQuery('.boom_bar-static-top-no_wpab .boom_bar').css('padding-left',  marginRight);
		jQuery('.boom_bar-static-top-no_wpab .boom_bar').css('padding-right', marginRight);
	}
}

// Monitors the height of the bar and adjusts margin if needed.
function it_boombar_adjust_heights( height ) {
	var barHeight        = jQuery('.boom_bar').css('height');
	var barPaddingTop    = jQuery('.boom_bar').css('padding-top');
	var barPaddingBottom = jQuery('.boom_bar').css('padding-bottom');
	var barBorderTop     = jQuery('.boom_bar').css('border-top-width');
	var barBorderBottom  = jQuery('.boom_bar').css('border-bottom-width');
	var bodyMarginTop    = it_boom_bar_origBodyMarginTop;
	var bodyMarginBottom = it_boom_bar_origBodyMarginBottom;

	// Validate all vars for IE 7, 8
	barHeight    = isNaN(parseFloat(barHeight)) ? 0: barHeight;
	barPaddingTop = isNaN(parseFloat(barPaddingTop)) ? 0 : barPaddingTop;
	barPaddingBottom = isNaN(parseFloat(barPaddingBottom)) ? 0 : barPaddingBottom;
	barBorderTop = isNaN(parseFloat(barBorderTop)) ? 0 : barBorderTop;
	barBorderBottom = isNaN(parseFloat(barBorderBottom)) ? 0 : barBorderBottom;
	bodyMarginTop = isNaN(parseFloat(bodyMarginTop)) ? 0 : bodyMarginTop;
	bodyMarginBottom = isNaN(parseFloat(bodyMarginBottom)) ? 0 : bodyMarginBottom;

	// Looks like we need to add top-margin of next visible div to height
	var firstChildMarginTop = jQuery(document.body).children('div:visible:first').css('margin-top');
	firstChildMarginTop = isNaN(parseFloat(firstChildMarginTop)) ? 0 : firstChildMarginTop;

	// Looks like we also need to add top-margin of builder-container-outer-wrapper div to height
	var builderContainerOuterWrapperMarginTop = jQuery('.builder-container-outer-wrapper').css('margin-top') ? jQuery('.builder-container-outer-wrapper').css('margin-top') : 0;
	builderContainerOuterWrapperMarginTop = isNaN(parseFloat(builderContainerOuterWrapperMarginTop)) ? 0 : builderContainerOuterWrapperMarginTop;
	
	// Compute margin vars
	it_boom_bar_marginTop = parseFloat(barHeight) + parseFloat(barPaddingTop) + parseFloat(barPaddingBottom) + parseFloat(barBorderTop) + parseFloat(barBorderBottom) + parseFloat(firstChildMarginTop) + parseFloat(builderContainerOuterWrapperMarginTop) + parseFloat(bodyMarginTop);
	it_boom_bar_marginBottom = parseFloat(barHeight) + parseFloat(barPaddingTop) + parseFloat(barPaddingBottom) + parseFloat(barBorderTop) + parseFloat(barBorderBottom) + parseFloat(bodyMarginBottom);

	// Set closed margins
	if ( it_boom_bar_closed ) {
		it_boom_bar_marginTop = parseFloat(bodyMarginTop);
		it_boom_bar_marginBottom = parseFloat(bodyMarginBottom);
	}

	// Adjust top margins
	jQuery( 'body.boom_bar-static-top-below_wpab, body.boom_bar-static-top-above_wpab, body.boom_bar-fixed-top-above_wpab, body.boom_bar-fixed-top-below_wpab, body.boom_bar-static-top-no_wpab, body.boom_bar-fixed-top-no_wpab' ).css('margin-top', it_boom_bar_marginTop + 'px');

	// Adjust bottom margins
	jQuery( 'body.boom_bar-fixed-bottom-above_wpab, body.boom_bar-fixed-bottom-below_wpab, body.boom_bar-fixed-bottom-no_wpab' ).css('margin-bottom', it_boom_bar_marginBottom + 'px');

	// Adjust position for top static where body element is relative
	if ( 'relative' == jQuery('body').css('position') ) {
		jQuery('body.boom_bar-static-top-above_wpab .boom_bar').css('top', '-' + it_boom_bar_marginTop + 'px');
		jQuery('body.boom_bar-static-top-below_wpab .boom_bar').css('top', '-' + it_boom_bar_marginTop + 'px');
		jQuery('body.boom_bar-static-top-no_wpab .boom_bar').css('top', '-' + it_boom_bar_marginTop + 'px');
	}

	// Update Dev Tools if open
	jQuery( '#it_boom_bar_dev_tools #calculated_margins' ).html(
		'<span class="margins-output">Bar Height:</span> ' + barHeight + '<br />' +
		'<span class="margins-output">Bar Top Padding:</span> ' + barPaddingTop + '<br />' +
		'<span class="margins-output">Bar Bottom Padding:</span> ' + barPaddingBottom + '<br />' +
		'<span class="margins-output">Bar Top Border Width:</span> ' + barBorderTop + '<br />' +
		'<span class="margins-output">Bar Bottom Border Width:</span> ' + barBorderBottom + '<br />' +
		'<span class="margins-output">Body Top Margin:</span> ' + bodyMarginTop + '<br />' +
		'<span class="margins-output">Body Bottom Margin:</span> ' + bodyMarginBottom + '<br />' +
		'<span class="margins-output">1st Child Top Margin:</span> ' + firstChildMarginTop + '<br />' +
		'<span class="margins-output">Builder Container Outer Wrapper Top Margin:</span> ' + builderContainerOuterWrapperMarginTop + '<br />' +
		'<span class="margins-output">Computed Bar Margin Top:</span> ' + it_boom_bar_marginTop + ' // Only relevant with top bars<br />' +
		'<span class="margins-output">Computer Bar Margin Bottom:</span> ' + it_boom_bar_marginBottom + ' // Only relevant with bottom bars<br />'
	);
}

// Convert ems to px
function it_boom_bar_em_to_px( value ) {
	var em = parseFloat(jQuery(document.body).css('font-size'));
	return (em * parseFloat(value));
}
