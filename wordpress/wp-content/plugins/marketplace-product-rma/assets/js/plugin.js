mp_rma = jQuery.noConflict();
var count = 2;
(function(mp_rma)
{
    mp_rma(document).ready(function()
    {

        // rma reason table
        mp_rma('table.reasonlist').dataTable(
          {
            "order": [],
            "columnDefs": [{
              'targets': [2],
              'orderable': false
            }]
          }
        )

        mp_rma('table.mpRmaList').dataTable(
          {
            "order": [[ 0, "desc" ]],
            'columnDefs': [ {
              'targets': [4,7],
              'orderable': false
            } ]
          }
        )

        var i = 2;
        /*---------append items on order select---------*/
        mp_rma("#mp-rma-order").on("change", function()
        {
            var order_id = mp_rma(this).val();

            mp_rma.ajax({
                type: "post",
                url: mp_rma_ajax.ajax_url,
                data: "action=mp_rma_get_order_items&nonce="+mp_rma_ajax.ajax_nonce+"&order_id="+order_id,
                success: function(response)
                {
                    mp_rma("table.mp_rma_items_ordered tbody").html(response);
                }
            });
        });

        // request mp_rma form images
        mp_rma("#mk-rss-attach-more").click(function()
        {
              mp_rma("#mk-rss-img-wrapper").append('<label class="image-preview" id="mk-rss-attach-img-label-'+i+'" for="mk-rss-attach-img-'+i+'"><span class="mk-rss-image-remove" onclick=remove_preview('+i+')>x</span><input type="file" name="product-img-'+i+'" class="hide-input" id="mk-rss-attach-img-'+i+'" onchange=image_selected('+i+')></label>');
              i++;
        });

        //upload shipping label
        mp_rma(document).on('click','#upload_shipping_label',function(event) {

            var custom_uploader;

            event.preventDefault();

            var custom_uploader = wp.media({

                title:'Upload Shipping Label',

                button: {

                    text: 'Select',

                },

                multiple: false  // Set this to true to allow multiple files to be selected

            })

            .on('select', function() {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                mp_rma('.shipping-label-path').val(attachment.url);
            })
            .open();

        });

        mp_rma(document).on('click', "input[type='checkbox'].check-item", function(evt) {
            var id = mp_rma(this).val();
            var this_elm = mp_rma(this);
            if ( mp_rma('.check-item:checkbox:checked').length > 1 ) {
                var checkValues = mp_rma('.check-item:checkbox:checked').map(function()
                {
                    return mp_rma(this).val();
                }).get();
                mp_rma.ajax({
                    type: "post",
                    url: mp_rma_ajax.ajax_url,
                    data: "action=mp_check_product_author&nonce="+mp_rma_ajax.ajax_nonce+"&product_id="+checkValues+"&this_id="+id,
                    success: function(response) {
                        if (response == 'false') {
                            if (confirm("You can create rma only for one seller's product at a time.")) {
                                mp_rma( "input[type='checkbox'].check-item" ).attr('checked', false);
                                mp_rma( "input[type='checkbox'].check-item" ).parent().next().next().children('input').attr("disabled", 'disable');
                                mp_rma( "input[type='checkbox'].check-item" ).parent().next().children('select').attr("disabled", 'disable');
                                mp_rma( this_elm ).attr('checked', true);
                                mp_rma(this_elm).parent().next().children('select').removeAttr('disabled');
                                mp_rma(this_elm).parent().next().next().children('input').removeAttr('disabled');
                            }
                            else {
                                mp_rma( this_elm ).attr('checked', false);
                                mp_rma(this_elm).parent().next().children('select').attr("disabled", 'disable');
                            }
                        }
                    }
                });
            }
            if( this.checked ) {
                mp_rma(this).parent().next().children('select').removeAttr('disabled');
                mp_rma(this).parent().next().next().children('input').removeAttr('disabled');
            }
            else {
                mp_rma(this).parent().next().children('select').attr("disabled", 'disable');
                mp_rma(this).parent().next().next().children('input').attr("disabled", 'disable');
            }

        });

        // request mp_rma form validation
        mp_rma( "form.mp_request_rma" ).on( "submit", function( event ) {

            mp_rma("p.required").remove();
            var orderSelect = 0
            var error = 0
            var quantity = 0
            var reasonError = 0
            var id = mp_rma("#mp-rma-order").val();
            var qty = mp_rma(".item-qty").val();
            var info = mp_rma(".mp_rma_add_info").val();
            var order_status = mp_rma(".mp_order_status").val();
            var resolution = mp_rma(".mp_resolution").val();
            var check = mp_rma("input[type='checkbox'].check-item");
            check.each(function() {
                if (mp_rma(this).is(':checked')) {
                    orderSelect = 1
                    var item = mp_rma(this).parent().siblings('td').children("input[type='number'].item-qty");
                    var reason = mp_rma(this).parent().siblings('td').children('select.reason-select');
                    if (!item.val()) {
                      quantity = 1
                      error = 1
                    }
                    if (!reason.val()) {
                      reasonError = 1
                      error = 1
                    }
                }
            });

            if (!id) {
                event.preventDefault();
                mp_rma("#mp-rma-order").parent().after('<p class="required">This is a required field.</p>');
                mp_rma("#mp-rma-order").focus();
            }
            else {
                if (quantity === 1) {
                  mp_rma('table.mp_rma_items_ordered').after('<p class="required">Please enter quantity for selected order(s).</p>')
                }
                if (reasonError === 1) {
                  mp_rma('table.mp_rma_items_ordered').after('<p class="required">Please select reason for selected order(s).</p>')
                }
                if (orderSelect === 0) {
                  mp_rma('table.mp_rma_items_ordered').after('<p class="required">Please select order(s).</p>')
                  error = 1
                }
                if (orderSelect === 0 || reasonError === 1 || quantity === 1) {
                  mp_rma('html,body').animate(
                    {
                      scrollTop: mp_rma('table.mp_rma_items_ordered').offset().top
                    },
                    'slow'
                  )
                }
                if (!info) {
                    mp_rma(".mp_rma_add_info").parent().after('<p class="required">This is a required field.</p>');
                    mp_rma(".mp_rma_add_info").focus()
                    error = 1
                }
                if (!order_status) {
                    mp_rma(".mp_order_status").parent().after('<p class="required">This is a required field.</p>');
                    error = 1
                }
                if (!resolution) {
                    mp_rma(".mp_resolution").parent().after('<p class="required">This is a required field.</p>');
                    error = 1
                }
                if (!mp_rma("#wk_i_agree").is(':checked')){
                    mp_rma("#wk_i_agree").parent().after('<p class="required">This is a required field.</p>');
                    error = 1
                }
            }
            if (error === 1) {
              event.preventDefault()
              return false
            } else {
              mp_rma('#mp_rma_add_button').addClass('mp-rma-disable-button')
            }
        });

        //mp_rma_details_tab
        mp_rma('.wk_mp_rma_container').hide();
        id = mp_rma('#mp_rma_details_tab li a').not('.inactive').attr('id');
        mp_rma('#'+ id +'_wk').show();
        mp_rma('#mp_rma_details_tab li a').click(function(){
          var t = mp_rma(this).attr('id');
          if(mp_rma(this).hasClass('inactive')){ //this is the start of our condition
            mp_rma('#mp_rma_details_tab li a').addClass('inactive');
            mp_rma(this).removeClass('inactive');
            mp_rma('.wk_mp_rma_container').hide();
            mp_rma('#'+ t +'_wk').fadeIn('slow');
          }
        });

        mp_rma(".mp-rma-image-link").on("click", function(evt) {
            evt.preventDefault();
            src = mp_rma(this).data('source');
            mp_rma('.mp-rma-image-full-cover img').attr('src', src);
            mp_rma(".mp-rma-image-full-overlay-bg").show();
            mp_rma(".mp-rma-image-full-overlay").show();
        });

        mp_rma(".mfp-close").on("click", function() {
            mp_rma(".mp-rma-image-full-overlay-bg").hide();
            mp_rma(".mp-rma-image-full-overlay").hide();
        });

        mp_rma(".mp-rma-action.cancel").on('click', function(evt) {

            evt.preventDefault();
            var mp_rma_id = mp_rma(this).data('rma-id');

            if (confirm("Are you sure you want to cancel the rma.")) {
                mp_rma.ajax({
                    type: "post",
                    url: mp_rma_ajax.ajax_url,
                    data: "action=mp_update_rma_status&nonce="+mp_rma_ajax.ajax_nonce+"&mp_rma_id="+mp_rma_id,
                    success: function(response) {
                      console.log(response);
                        if (response == true) {
                            window.location.reload();
                        }
                    }
                });
            }

        });

        mp_rma(".wkmp-selleritem.rma > a").on("click", function(evt) {
            evt.preventDefault();
            mp_rma(".wkmp-selleritem.rma ul").toggle();
        });

    });

})(mp_rma);

function image_selected(id) {
    var preview = document.querySelector('#mk-rss-attach-img-label-'+id);
    var file    = document.querySelector('#mk-rss-attach-img-'+id).files[0];
    var reader  = new FileReader();
    var img = null;

    reader.addEventListener("load", function () {
        var startPoint = reader.result.indexOf(":");
        startPoint++;
        var endPoint = reader.result.indexOf(";");
        var length = endPoint - startPoint;
        var type = reader.result.substr(startPoint,length);
        var typeArr = type.split("/");
        if (typeArr[0] == "image" && ( typeArr[1] == "jpeg" || typeArr[1] == "png")) {
            mp_rma('#mk-rss-attach-img-label-'+id).css('background-image','url('+reader.result+')' );
        }
        else {
          alert("Please upload image with jpeg or png extention.");
          mp_rma("#mk-rss-attach-img-label-"+id).remove();
          count--;
        }
    }, false);

    if (file) {
      reader.readAsDataURL(file);
    }
}
function remove_preview(id) {
    event.preventDefault();
    mp_rma("#mk-rss-attach-img-label-"+id).remove();
    count--;
}
