jQuery(document).ready(function () {
  jQuery('#mp-update-sale-order').on('change', function (evt) {
    evt.preventDefault()
    jQuery(window).scrollTop(0)
    jQuery('body').append('<div class="wk-mp-loader"><div class="wk-mp-spinner wk-mp-skeleton"><!--////--></div></div>')
    jQuery('.wk-mp-loader').css('display', 'inline-block')
    jQuery('body').css('overflow', 'hidden')
    setTimeout(function () {
      jQuery('body').css('overflow', 'auto')
      jQuery('.wk-mp-loader').remove()
    }, 1500)
  })

})



google.load("visualization", "1", {packages:["geochart"]});

google.setOnLoadCallback(function() { drawRegionsMap(topBilling); });

function drawRegionsMap(topBilling) {
  var options = {};
  var chart = new google.visualization.GeoChart(document.getElementById('top_billing_country'));
  chart.draw(topBilling, options);
  go();

  window.addEventListener('resize', go);

  function go(){
    chart.draw(topBilling, options);
  }
}
