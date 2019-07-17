var jQuery
jQuery(document).ready(function () {

	jQuery('.seller-query-revert').on('click', function(evt){
		query_id = jQuery(this).data('qid');
		reply_message = jQuery(this).prev('div').find('.admin_msg_to_seller').val();
		reply_message = reply_message.replace(/\r\n|\r|\n/g,"<br/>");
		jQuery.ajax({
			type: 'POST',
			url: the_mpadminajax_script.mpajaxurl,
			data: {
				"action":"send_mail_to_seller",
				"qid":query_id,
				"reply_message": reply_message,
				"nonce":the_mpadminajax_script.nonce
			},
			success: function (data) {
				if (data) {
						location.reload()
				} else{
					alert( the_mpadminajax_script.adajax_tr.aajax29 );
				}
			}

		})
	});

	jQuery('.pay-rem-amt').on('click', function () {
		var amtrem = jQuery(this).closest('.payment-modelbox').find('.thickbox_amt_rem').val()
		var paidamt = jQuery(this).closest('.payment-modelbox').find('.thickbox_paid_amt').val()
		var sellermain = jQuery(this).closest('.payment-modelbox').find('.seller_main').val()
		var isChecked = jQuery(this).closest('.payment-modelbox').find('.notify_seller').is(':checked')
		if (isChecked) {
			notifyseller = isChecked
		} else {
			notifyseller = false
		}
		jQuery.ajax({
			type: 'POST',
			url: the_mpadminajax_script.mpajaxurl,
			data: {
				"action":"wk_commission_resetup",
				"amt_rem":amtrem,
				"paid_amt":paidamt,
				"seller_main":sellermain,
				"notify_seller":notifyseller,
				"nonce":the_mpadminajax_script.nonce
			},
			success: function (data) {
						if (data) {
								location.reload()
						}
			}

		})

	});

	//banner trigger file upload
   jQuery('#wkmp_seller_banner').click(function(){
    jQuery('#wk_mp_shop_banner').trigger('click');
  });

	jQuery('.wkmp-fade-banner').click(function(){
  document.getElementById('wk_mp_shop_banner').addEventListener('change', changeseller_bannerimage, false);
  });

	jQuery('.mp_seller_profile_img').click(function(){
  document.getElementById('mp_useravatar').addEventListener('change', changeprofile_image, false);
  });

	jQuery('.Company_Logo').click(function(){
  document.getElementById('mp_company_logo').addEventListener('change', seller_logo_image, false);
  });

	/* function to change profile image */
	function changeprofile_image(evt) {
		jQuery('#mp_seller_image').empty();
		var files = evt.target.files;
		for (var i = 0, f; f = files[i]; i++)
	{
		if (!f.type.match('image.*'))
		{
			continue;
		}
		var reader = new FileReader();
		reader.onload = (function(theFile){
			return function(e)
			{
				var div = document.createElement('div');
				div.innerHTML = ['<img class="thumb" src="', e.target.result,'" title="', escape(theFile.name), '"/><span class="wkmp_image_over" ></span><input type="hidden" name ="mpthumbimg[]" value="',escape(theFile.name),'">'].join('');
				document.getElementById('mp_seller_image').insertBefore(div, null);
				jQuery('#mp_seller_image div').attr({class:'imgdiv'});
			};
				})(f);
		reader.readAsDataURL(f);
		}
	}

	/* seller logo image */
  function seller_logo_image(evt) {
    jQuery('#seller_com_logo_img').empty();

    var files = evt.target.files;
    for (var i = 0, f; f = files[i]; i++)
  {
    if (!f.type.match('image.*'))
    {
      continue;
    }
    var reader = new FileReader();
    reader.onload = (function(theFile){
      return function(e)
      {
        var div = document.createElement('div');
        div.innerHTML = ['<img class="thumb" src="', e.target.result,'" title="', escape(theFile.name), '"/><span class="wkmp_image_over" ></span><input type="hidden" name ="mpthumbimg[]" value="',escape(theFile.name),'">'].join('');
        if(document.getElementById('seller_com_logo_img')!=''){
          document.getElementById('seller_com_logo_img').insertBefore(div, null);
        }
        jQuery('#seller_com_logo_img div').attr({class:'imgdiv'});
      };
        })(f);
    reader.readAsDataURL(f);
    }
  }

	/* function to change banner image */
	function changeseller_bannerimage(evt) {
		jQuery('#wk_seller_banner').empty();

		var files = evt.target.files;
		for (var i = 0, f; f = files[i]; i++)
	{
		if (!f.type.match('image.*'))
		{
			continue;
		}
		var reader = new FileReader();
		reader.onload = (function(theFile){
			return function(e){
				var div = document.createElement('div');
				div.innerHTML = ['<img class="thumb" src="', e.target.result,'" title="', escape(theFile.name), '"/><span class="wkmp_image_over" ></span><input type="hidden" name ="mpthumbimg[]" value="',escape(theFile.name),'">'].join('');
				document.getElementById('wk_seller_banner').insertBefore(div, null);
				jQuery('#wk_seller_banner div').attr({class:'imgdiv'});
			};
				})(f);
		reader.readAsDataURL(f);
		}
	}

	/* remove logo image start */
  jQuery('#seller_com_logo_img .mp-image-remove-icon').on('click', function() {
    var thumbId = jQuery(this).data('id')
    var defaultSrc = jQuery(this).data('default')
    jQuery(this).siblings('img').attr('src', defaultSrc)
    jQuery(this).next('.mp-remove-company-logo').val(thumbId)
    jQuery(this).remove()
  })
  /* remove logo image ends */

  /* remove banner image start */
  jQuery('#wk_seller_banner .mp-image-remove-icon').on('click', function() {
    var thumbId = jQuery(this).data('id')
    var defaultSrc = jQuery(this).data('default')
    jQuery(this).siblings('img').attr('src', defaultSrc)
    jQuery(this).next('.mp-remove-shop-banner').val(thumbId)
    jQuery(this).remove()
  })
  /* remove banner image ends */

  /* remove banner image start */
  jQuery('#mp_seller_image .mp-image-remove-icon').on('click', function() {
    var thumbId = jQuery(this).data('id')
    var defaultSrc = jQuery(this).data('default')
    jQuery(this).siblings('img').attr('src', defaultSrc)
    jQuery(this).next('.mp-remove-avatar').val(thumbId)
    jQuery(this).remove()
  })

	jQuery( '#wp-admin-bar-mp-seperate-seller-dashboard a' ).on( 'click', function( ev ){
		nonce = jQuery(this).attr('href');
		nonce = nonce.split( '=' );
		nonce = nonce[1];

		if( nonce == the_mpadminajax_script.nonce ) {
			ev.preventDefault();
			jQuery.ajax({
				type: 'POST',
				url: the_mpadminajax_script.mpajaxurl,
				data: {
					"action":"change_seller_dashboard",
					"change_to":'front_dashboard',
					"nonce":the_mpadminajax_script.nonce
				},
				success: function (data) {
					data = jQuery.parseJSON(data);
					if (data) {
						window.location.href = data.redirect;
					}
				}

			})
		}
	} );

	if ( jQuery('#wk_store_country').length ) {
	  jQuery('#wk_store_country').select2();
	  if ( jQuery('#wk_store_state').is( 'select' ) ) {
	    jQuery('#wk_store_state').select2();
	  }
	}

	jQuery( '#seller_countries_field' ).on( 'change', function(ert){

		if(jQuery( '#wk_store_country' ).val()){
		country_code = jQuery( '#wk_store_country' ).val();
		jQuery.ajax({
			type: 'POST',
			url: the_mpadminajax_script.mpajaxurl,
			data: {
				"action": "country_get_state",
				"country_code": country_code,
				"nonce": the_mpadminajax_script.nonce,
			},
			success: function (data) {

				if( data ){
				// jQuery('#wk_store_state').replaceWith(data);
								jQuery('#wk_store_state').siblings('span.select2').remove();
				jQuery('#wk_store_state').replaceWith(data);
				if ( jQuery('#wk_store_state').is( 'select' ) ) {
					jQuery('#wk_store_state').select2();
				}
				}
			}
			});
		}
  	});


	var name_regex = /^[a-zA-Z\s-, ]+$/
	var contact_regex = /^[0-9]+$/

	jQuery(document).on("blur","#tmplt_name",function(){
		if(jQuery("#tmplt_name").val()==''){
				jQuery("#tmplt_name").next("span.name_err").text(the_mpadminajax_script.adajax_tr.aajax1);
		}
		else{
			if(!jQuery("#tmplt_name").val().match(name_regex)){
				jQuery("#tmplt_name").next("span.name_err").text(the_mpadminajax_script.adajax_tr.aajax2);
			} else{
				jQuery("#tmplt_name").next("span.name_err").text('');
			}
		}
	});

	jQuery(document).on("blur","#clr1",function(){
		if(jQuery("#clr1").val()==''){
			jQuery("#clr1").next("span.bsclr_err").text(the_mpadminajax_script.adajax_tr.aajax1);
		}
		else{
			jQuery("#clr1").next("span.bsclr_err").text('');
		}
	});

	jQuery(document).on("blur","#clr2",function(){
		if(jQuery("#clr2").val()==''){
			jQuery("#clr2").next("span.bdclr_err").text(the_mpadminajax_script.adajax_tr.aajax1);
		} else{
			jQuery("#clr2").next("span.bdclr_err").text('');
		}
	});

	jQuery(document).on("blur","#clr3",function(){
		if(jQuery("#clr3").val()==''){
			jQuery("#clr3").next("span.bkclr_err").text(the_mpadminajax_script.adajax_tr.aajax1);
		}else{
			jQuery("#clr3").next("span.bkclr_err").text('');
		}
	});

	jQuery(document).on("blur","#clr4",function(){
		if(jQuery("#clr4").val()==''){
			jQuery("#clr4").next("span.txclr_err").text(the_mpadminajax_script.adajax_tr.aajax1);
		}
		else{
			jQuery("#clr4").next("span.txclr_err").text('');
		}
	});


	jQuery("form#emailtemplate input").on('focus',function(evt) {
		jQuery('span.required').remove();
	});

 	jQuery("form#emailtemplate").on('submit',function(evt){

		var t_name=jQuery('.tmplt_name').val();
		var t_clr1=jQuery('#clr1').val();
		var t_clr2=jQuery('#clr2').val();
		var t_clr3=jQuery('#clr3').val();
		var t_clr4=jQuery('#clr4').val();
		var error = 0

		var name_regex = /^[a-zA-Z0-9\s-, ]+$/;
		var contact_regex = /^[0-9]+$/;

		jQuery('span.required').remove();
		if(t_name=='' || t_clr1=='' || t_clr2=='' || t_clr3=='' || t_clr4=='') {
			if(t_name==''){
				jQuery('.tmplt_name').after('<span class="required">'+the_mpadminajax_script.adajax_tr.aajax3+'</span>');
				return false;
			}else{
				if(!name_regex.test(t_name)){
					jQuery('.tmplt_name').after('<span class="required">'+the_mpadminajax_script.adajax_tr.aajax2+'</span>');
					return false;
				}
			}

			if(t_clr1==''){
				jQuery('#clr1').after('<span class="required">'+the_mpadminajax_script.adajax_tr.aajax5+'</span>');
				return false;
			}
			if(t_clr2==''){
				jQuery('#clr2').after('<span class="required">'+the_mpadminajax_script.adajax_tr.aajax6+'</span>');
				return false;
			}
			if(t_clr3==''){
				jQuery('#clr3').after('<span class="required">'+the_mpadminajax_script.adajax_tr.aajax7+'</span>');
				return false;
			}
			if(t_clr4==''){
				jQuery('#clr4').after('<span class="required">'+the_mpadminajax_script.adajax_tr.aajax8+'</span>');
				return false;
			}
			if(t_width==''){
				jQuery('.width_err').after('<br><span class="required">'+the_mpadminajax_script.adajax_tr.aajax9+'</span>');
				return false;
			}else{
				if((!b_contact.match(contact_regex))){
					jQuery('.width_err').after('<span class="required">'+the_mpadminajax_script.adajax_tr.aajax26+'</span>');
					return false;
				}
			}
			evt.preventDefault();
		}
		else{
			if(!name_regex.test(t_name)){
				jQuery('.tmplt_name').after('<span class="required">'+the_mpadminajax_script.adajax_tr.aajax2+'</span>');
				evt.preventDefault();
			}
		}
 	});

	jQuery(document).on('click','a.wk_seller_app_button', function(w){
		w.preventDefault();	
		let seller_status=this.id;
		let elm = jQuery(this);
		let status =  confirm(the_mpadminajax_script.adajax_tr.aajax10);
		if ( status ) {
			jQuery.ajax({
				type: 'POST',
				url: the_mpadminajax_script.mpajaxurl,
				data: {"action": "wk_admin_seller_approve", "seller_app":seller_status},
				beforeSend : function(){
						elm.addClass('mp-disabled');
						elm.attr('href','');
				},
				success: function(data){
					let sel_data=data.split(':');
					if(sel_data[1]==0) {
						jQuery('#'+seller_status).addClass('active');
						var this_sel_id='wk_seller_approval_mp'+sel_data[0]+'_mp1';
						this_sel_id=this_sel_id.replace(/\s+/g, '');
						jQuery('#'+seller_status).text(the_mpadminajax_script.adajax_tr.aajax11);
						jQuery('#'+seller_status).attr('id',this_sel_id);
					}
					else {
						jQuery('#'+seller_status).removeClass('active');
						let this_sel_id='wk_seller_approval_mp'+sel_data[0]+'_mp0';
						this_sel_id=this_sel_id.replace(/\s+/g, '');
						jQuery('#'+seller_status).text(the_mpadminajax_script.adajax_tr.aajax12);
						jQuery('#'+seller_status).attr('id',this_sel_id);
					}
					elm.removeClass('mp-disabled');
				}
			});
		}
	});

	if (jQuery(".return-seller select").length) {
		jQuery(".return-seller select").select2()
	}

	jQuery('select#role').on('change', function() {

		if (jQuery(this).val() == 'wk_marketplace_seller') {
			jQuery('.mp-seller-details').show();
			jQuery('#org-name').focus();
		}
		else {
			jQuery('.mp-seller-details').hide();
		}

	});

	jQuery('#org-name').on('focusout', function() {
		var value = jQuery(this).val().toLowerCase().replace(/-+/g, '').replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
		if (value == '') {
			jQuery('#seller-shop-alert-msg').removeClass('text-success').addClass('text-danger').text(the_mpadminajax_script.adajax_tr.aajax13);
			jQuery('#org-name').focus();
		}
		else {
			jQuery('#seller-shop-alert-msg').text("");
		}
		jQuery('#seller-shop').val(value);
	});

	jQuery('#seller-shop').on('focusout', function() {
		var self = jQuery(this);
		jQuery.ajax({
			type: 'POST',
			url: the_mpadminajax_script.mpajaxurl,
			data: {"action": "wk_check_myshop","shop_slug":self.val(),"nonce":the_mpadminajax_script.nonce},
			success: function(response)
			{
				if ( response == 0){
					jQuery('#seller-shop-alert').removeClass('text-success').addClass('text-danger');
					jQuery('#seller-shop-alert-msg').removeClass('text-success').addClass('text-danger').text(the_mpadminajax_script.adajax_tr.aajax14);
				}else if(response == 2){
					jQuery('#seller-shop-alert').removeClass('text-success').addClass('text-danger');
					jQuery('#seller-shop-alert-msg').removeClass('text-success').addClass('text-danger').text(the_mpadminajax_script.adajax_tr.aajax15);
					jQuery('#org-name').focus();
				}else{
					jQuery('#seller-shop-alert').removeClass('text-danger').addClass('text-success');
					jQuery('#seller-shop-alert-msg').removeClass('text-danger').addClass('text-success').text(the_mpadminajax_script.adajax_tr.aajax16);
				}
			}
		});
	});

	jQuery(document).ready(function($){
		jQuery("#uploadButton").click(function(event) {
			var frame = wp.media({
			title: the_mpadminajax_script.adajax_tr.aajax17,
			button: {
				text: the_mpadminajax_script.adajax_tr.aajax18,
			},
			multiple: false
			});
			frame.on( 'select', function() {
				var attachment = frame.state().get('selection').first().toJSON();
				jQuery("#img_url").val(attachment.url);
			});
			frame.open();
		});
	});
	jQuery(document).ready(function($){
		// Add Color Picker to all inputs that have 'color-field' class
		$(function() {
			$('#clr1').wpColorPicker();
			$('#clr2').wpColorPicker();
			$('#clr3').wpColorPicker();
			$('#clr4').wpColorPicker();
		});
	});


	/* commission payment and seller payment */
	//jQuery('.alternate').on("click",".column-pay_action",function(){
	jQuery('tbody').on("click",".column-pay_action .pay",function(){

		var seller_com_id=jQuery(this).attr('id');

		if(seller_com_id){

			jQuery.ajax({
				type: 'POST',
				url: the_mpadminajax_script.mpajaxurl,
				data: {"action": "marketplace_statndard_payment", "seller_id":seller_com_id,"nonce":the_mpadminajax_script.nonce},
				success: function(data) {
					jQuery('#com-pay-ammount').html(data);
					jQuery('#com-pay-ammount').css('display','block');
					jQuery('<div class="standard-pay-backdrop">&nbsp;</div>').appendTo('body');
				}

			});

		}


	});

	jQuery('#com-pay-ammount').on('click','.standard-pay-close',function(){
		jQuery('#com-pay-ammount').hide();
		jQuery( "div" ).remove( ".standard-pay-backdrop" );
	});

	jQuery('#com-pay-ammount').on('click','#MakePaymentbtn',function(evt){

		var remain_ammount=jQuery('#com-pay-ammount').find('#mp_remain_ammount').val();
		var pay_ammount=jQuery('#com-pay-ammount').find('#mp_paying_ammount').val();
		var seller_acc=jQuery('#com-pay-ammount').find('#mp_paying_acc_id').val();
		pay_ammount = parseInt( pay_ammount );

		if( ( parseInt(remain_ammount) < parseInt( pay_ammount ) ) || ( pay_ammount <= 0 || pay_ammount == '' ) || isNaN( pay_ammount ) ) {
			if( isNaN( pay_ammount ) ) {
				jQuery('#com-pay-ammount').find('#mp_paying_ammount_error').text(the_mpadminajax_script.adajax_tr.aajax19);
			}else{
				jQuery('#com-pay-ammount').find('#mp_paying_ammount_error').text(the_mpadminajax_script.adajax_tr.aajax20);
			}
		} else {

			jQuery.ajax({
				type: 'POST',
				url: the_mpadminajax_script.mpajaxurl,
				data: {
					"action": "marketplace_mp_make_payment",
					"seller_acc":seller_acc,
					"pay":pay_ammount,
					"nonce":the_mpadminajax_script.nonce
				},
				beforeSend : function (){
					jQuery("#MakePaymentbtn").val(the_mpadminajax_script.adajax_tr.aajax21).attr('disabled','true');
				},
				success: function(data) {
					if( data ) {
						if( data.error != undefined ) {
							if( data.error == 1 ){
								jQuery("#MakePaymentbtn").val(the_mpadminajax_script.adajax_tr.aajax27);
								jQuery(".wkmp-modal-footer").prepend("<p class='mp-error'>"+data.msg+"</p>");
								window.setTimeout(function(){
									location.reload();
								}, 2000);
							} else if( data.error == 0 ){
								jQuery("#MakePaymentbtn").val(the_mpadminajax_script.adajax_tr.aajax28);
								jQuery(".wkmp-modal-footer").prepend("<p class='mp-success'>"+data.msg+"</p>");
								window.setTimeout(function(){
									location.reload();
								}, 2000);
							}
						}
					}
				}
			});
		}
	})

	setTimeout(function(){
		jQuery('#wk_payment_success').remove();
	}, 5000);
	/* commission payment and seller payment */
});

jQuery(document).ready(function () {
	jQuery('.admin-order-pay').on('click', function () {
		var order_pay = jQuery(this)
		var id = jQuery(this).data('id')
		var seller_id = jQuery('#seller_id').val()
		if ( id && seller_id ) {
			jQuery.ajax({
				type: 'POST',
				url: the_mpadminajax_script.mpajaxurl,
				data: {
					"action": "mp_order_manual_payment",
					"id": id,
					"seller_id": seller_id,
					"nonce": the_mpadminajax_script.nonce
				},
				beforeSend : function (){
					order_pay.html(the_mpadminajax_script.adajax_tr.aajax21).attr('disabled', 'true')
				},
				success: function (response) {
					if ( response == 'done' ) {
						order_pay.replaceWith('<button class="button button-primary" disabled>'+the_mpadminajax_script.adajax_tr.aajax22+'</button>')
						jQuery( '#notice-wrapper' ).html( '<div  class="notice notice-success is-dismissible"><p>'+the_mpadminajax_script.adajax_tr.aajax23+'</p></div>' )
					} else if ( response == 'Already Paid' ) {
						order_pay.replaceWith('<button class="button button-primary" disabled>'+the_mpadminajax_script.adajax_tr.aajax24+'</button>')
						jQuery( '#notice-wrapper' ).html( '<div  class="notice notice-error is-dismissible"><p>'+the_mpadminajax_script.adajax_tr.aajax25+'</p></div>' )
					}
				},
			})
		}
	})

	// product seller assign in bulk
	if (jQuery('#mp-product-seller-select-list').length) {
		jQuery('#mp-product-seller-select-list').select2();

		jQuery('#mp-assign-product-seller').on('click', function () {
				if (jQuery('#mp-product-seller-select-list').val()) {
					return confirm(jQuery(this).data('alert-msg'));
				}
		});
	}

	if (jQuery('#wkmp_seller_allowed_product_types').length) {
		jQuery('#wkmp_seller_allowed_product_types').select2();
		jQuery('#wkmp_seller_allowed_categories').select2();
	}

	if (jQuery('#wkmp_allowed_categories_per_seller').length) {
		jQuery('#wkmp_allowed_categories_per_seller').select2();
	}

	if (jQuery('#reassign_user').length) {
		jQuery('#reassign_user').select2();
	}
})
