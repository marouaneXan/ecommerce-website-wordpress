(function( $ ) {
	'use strict';
	
	$(document).ready(function() {
		
		/**
    	 *  Copy to clipboard
    	 * 	@since 1.3.5
    	 */
    	$(document).on('click', '.wt_sc_copy_to_clipboard', function(){
    		var target_class=$(this).attr('data-target');
    		var target_elm=$('.'+target_class);
    		if(target_elm.length>0 && target_elm.text().trim()!="")
    		{
    			navigator.clipboard.writeText(target_elm.text().trim());
    			wt_sc_notify_msg.success(WTSmartCouponOBJ.msgs.copied);
    		}
    	});

		// Insert Product condition
		var element_product_ids = $("#woocommerce-coupon-data .form-field:has('[name=\"product_ids[]\"]')"); //Since WC3.0.0
		if (element_product_ids.length != 1) element_product_ids = $("#woocommerce-coupon-data .form-field:has('[name=\"product_ids\"]')"); //Prior to WC3.0.0
		if (element_product_ids.length == 1) {
			var element_product_ids = $("#woocommerce-coupon-data .form-field:has('[name=\"product_ids[]\"]')"); 
			$("#woocommerce-coupon-data .form-field._wt_product_condition_field").detach().insertBefore( element_product_ids );
		}

		// Insert Category Condiiton
		var element_product_categories = $("#woocommerce-coupon-data .form-field:has('[name=\"product_categories[]\"]')");
		if (element_product_categories.length == 1) {
			$("#woocommerce-coupon-data .form-field._wt_category_condition_field").detach().insertBefore( element_product_categories );

		}

		$('#upload').on('change',function( ){
			$('.wt-file-container-label').html('selected').addClass('selected');
		});

	

		// Check if selected is a Simple product
		$('#wt_give_away_product').on('change',function() {
			var product_id = $(this).val();
			$('.error_message.wt_coupon_error').hide();
			var data = {
				'action'        : 'wt_check_product_type',
				'product'       : product_id
			};

			jQuery.ajax({
				type: "POST",
				async: true,
				url: WTSmartCouponOBJ.ajaxurl,
				data: data,
				success: function (response) {
					if( response != 'simple' ) {
						$('.error_message.wt_coupon_error').show();
						$('#wt_give_away_product').val('');
					}
				}
			});

			
		});


		$('.wt_colorpick').wpColorPicker({
			change:function(event,ui)
			{	
				var element = jQuery(event.target);
				var elementID = element.attr('id');
				element.val(ui.color);
				reload_all_coupon_preview();
			}
		});
		// $('#wt_active_coupon_border_color').wpColorPicker( options );


		var wt_create_coupon_preview = function(bg_color,text_color  ) {
			// var bg_color = $('#wt_active_coupon_bg_color').val() ;
			// var text_color = $('#wt_active_coupon_border_color').val() ;
			var coupon_html = '<div class="wt-single-coupon" style="background: '+ bg_color + ';\
								 color: '+ text_color+ ';\
								 box-shadow: 0 0 0 4px '+ bg_color + ', 2px 1px 6px 4px rgba(10, 10, 0, 0.5);\
								 text-shadow: -1px -1px '+ bg_color + '; \
								 border: 2px dashed  '+ text_color+ '; ">\
								 <div class="wt-coupon-content">\
									<div class="wt-coupon-amount">\
										<span class="amount"> 10 % </span><span>  Cart Discount </span>\
									</div>\
									<div class="wt-coupon-code"> <code> flat10% </code></div>\
									<div class="wt-coupon-expiry"></div>\
								</div></div>';

			return coupon_html;

		};
	

		var wt_reload_coupon_preview = function( coupon_type ) {
			switch( coupon_type) {
				case 'active_coupon' : 
					var coupon_preview_element = '.active_coupon_preview';
					var bg_color = $('#wt_active_coupon_bg_color').val();
					var text_color = $('#wt_active_coupon_border_color').val();
					break;
				case 'used_coupon' : 
					var coupon_preview_element = '.used_coupon_preview';
					var bg_color = $('#wt_used_coupon_bg_color').val();
					var text_color = $('#wt_used_coupon_border_color').val();
					break;
				case 'expired_coupon' : 
					var coupon_preview_element = '.expired_coupon_preview';
					var bg_color = $('#wt_expired_coupon_bg_color').val();
					var text_color = $('#wt_expired_coupon_border_color').val();
					break;

			}
			var preview = wt_create_coupon_preview( bg_color,text_color );
			
			jQuery( coupon_preview_element ).find('.wc-sc-coupon-preview-container').remove();
			jQuery( coupon_preview_element ).append( '<span class="wc-sc-coupon-preview-container">' + preview + '</span>' );
		};
		var reload_all_coupon_preview = function( ) {
			wt_reload_coupon_preview( 'active_coupon');
			wt_reload_coupon_preview( 'used_coupon');
			wt_reload_coupon_preview( 'expired_coupon');
		}

		jQuery(document).ready(function(){
			reload_all_coupon_preview();
		});

		jQuery('#wt_active_coupon_bg_color, #wt_active_coupon_border_color').on('change keyup irischange', function(){
			wt_reload_coupon_preview( 'active_coupon' );
		});

		jQuery('#wt_used_coupon_bg_color, #wt_used_coupon_border_color').on('change keyup irischange', function(){
			wt_reload_coupon_preview( 'used_coupon' );
		});
		
		jQuery('#wt_expired_coupon_bg_color, #wt_expired_coupon_border_color').on('change keyup irischange', function(){
			wt_reload_coupon_preview( 'expired_coupon' );
		});

	});


	// Implement Subtab for admin screen.
	jQuery(document).ready(function(  ){

		jQuery('.wt_sub_tab li a').click(function( e ) {
			e.preventDefault();
			if( $(this).parent('li').hasClass('active') ) {
				return;//nothing to do;
			}
			var target=$(this).attr('href');
			var parent = $(this).parents('.wt_sub_tab');
			var container = $('.wt_sub_tab_container');
			$('.wt_sub_tab li').removeClass('active');
			$(this).parent('li').addClass('active');
			container.find('.wt_sub_tab_content').hide().removeClass('active');
			container.find(target).fadeIn().addClass('active');
		});
	});
	
	

})( jQuery );


/**
 *  Toast notification
 * 	@since 1.3.5
 */
var wt_sc_notify_msg=
{
	error:function(message, auto_close)
	{
		var auto_close=(auto_close!== undefined ? auto_close : true);
		var er_elm=jQuery('<div class="wt_sc_notify_msg wt_sc_notify_msg_error">'+message+'</div>');				
		this.setNotify(er_elm, auto_close);
	},
	success:function(message, auto_close)
	{
		var auto_close=(auto_close!== undefined ? auto_close : true);
		var suss_elm=jQuery('<div class="wt_sc_notify_msg wt_sc_notify_msg_success">'+message+'</div>');				
		this.setNotify(suss_elm, auto_close);
	},
	setNotify:function(elm, auto_close)
	{
		jQuery('body').append(elm);
		elm.on('click',function(){
			wt_sc_notify_msg.fadeOut(elm);
		});
		elm.stop(true,true).animate({'opacity':1,'top':'50px'},1000);
		if(auto_close)
		{
			setTimeout(function(){
				wt_sc_notify_msg.fadeOut(elm);
			},5000);
		}else
		{
			jQuery('body').on('click',function(){
				wt_sc_notify_msg.fadeOut(elm);
			});
		}
	},
	fadeOut:function(elm)
	{
		elm.animate({'opacity':0,'top':'100px'},1000,function(){
			elm.remove();
		});
	}
}
