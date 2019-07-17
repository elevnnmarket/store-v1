var jQuery
jQuery(document).ready(function () {
  // pagination on order_history page

  var dataTable = jQuery('table.productlist').dataTable({
    'columnDefs': [ {
      'targets': [0,5,6],
      'orderable': false,
    } ],
    'autoWidth': false,
    'responsive': true
  })
  jQuery('table.eoa').dataTable({
    'responsive': true
  })

  jQuery('table.orderhistory').dataTable({
    'columnDefs': [ {
      'targets': [3,4],
      'orderable': false
    } ],
    'autoWidth': false,
    'responsive': true
  })

  jQuery('table.mp-asktoadmin-history-table').dataTable(
    {
      "order": [[ 0, "desc" ]],
      'columnDefs': [ {
        'targets': [2],
        'orderable': false
      } ],
      'autoWidth': false,
      'responsive': true
    }
  )

  jQuery('table.transactionhistory').dataTable(
    {
      "order": [[ 0, "desc" ]],
      'columnDefs': [ {
        'targets': [2],
        'orderable': false
      } ]
    }
  )

  // product bulk delete seller product list starts

  jQuery('#allDelete').parent('th').removeClass('sorting_asc').addClass('sorting_disabled');

  jQuery('#allDelete').on('click', function () { // bulk checked
    var status = this.checked
    jQuery('.deleteProductRow').each(function () {
      jQuery(this).prop('checked', status)
    })
  })

  jQuery('.deleteProductRow').on('click', function () {
    var dataTable = jQuery('table.productlist').DataTable()
    if (jQuery('.deleteProductRow:checked').length === dataTable.page.info().recordsTotal) {
      jQuery('#allDelete').prop('checked', true)
    } else {
      jQuery('#allDelete').prop('checked', false)
    }
  })

  jQuery('#triggerBulkDelete').on('click', function (event) {
    if (jQuery('.deleteProductRow:checked').length > 0) {
      if (confirm(paginationScript.page_tr.pag1)) {
        var productIds = []

        jQuery('.deleteProductRow').each(function () {
          if (jQuery(this).is(':checked')) {
            productIds.push(jQuery(this).val())
          }
        })

        // productIds = productIds.toString() // array to string conversion
        jQuery(window).scrollTop(0)

        jQuery.ajax({
          type: 'POST',
          url: paginationScript.ajaxurl,
          data: {
            'product_ids': productIds,
            'action': 'mp_bulk_delete_product',
            'nonce': paginationScript.nonce
          },
          beforeSend: function () {
            jQuery('body').append('<div class="wk-mp-loader"><div class="wk-mp-spinner wk-mp-skeleton"><!--////--></div></div>')
            jQuery('.wk-mp-loader').css('display', 'inline-block')
            jQuery('body').css('overflow', 'hidden')
          },
          complete: function () {
            setTimeout(function () {
              jQuery('body').css('overflow', 'auto')
              jQuery('.wk-mp-loader').remove()
            }, 1500)
          },
          success: function (result) {
            if (result) {
              jQuery('#main_container').load(document.URL + ' #main_container', function () {
                jQuery('table.productlist').dataTable({
                  'columnDefs': [ {
                    'targets': [0,5,6],
                    'orderable': false
                  } ],
                  'responsive': true
                })
              })
              setTimeout(function () {
                jQuery('#triggerBulkDelete').before('<div class="woocommerce-message">' + paginationScript.page_tr.pag2 + '</div>')
              }, 1500)
            }
          }
        })
      }
    } else {
      alert( paginationScript.page_tr.pag3 )
    }
  })

  // product bulk delete seller product list ends

  // pagination on order_history page

  jQuery('#mp_product_slider').bxSlider({
    mode: 'horizontal',
    randomStart: true,
    minSlides: 1,
    maxSlides: 2,
    slideWidth: 200,
    slideMargin: 0,
    adaptiveHeight: false,
    adaptiveHeightSpeed: 500,
    easing: 'linear',
    captions: false,
    speed: 500,
    controls: true,
    auto: false,
    autoControls: true,
    pause: 3000,
    autoDelay: 0,
    autoHover: true,
    pager: false,
    pagerType: 'short',
    pagerShortSeparator: '/',
    moveSlides: 1
  })
})
