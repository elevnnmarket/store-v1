    var uploaded_image;
    jQuery(document).ready(function() {
        jQuery("#upload-btn-2").click(function(e){
          e.preventDefault();
              var image = wp.media({
                  title: 'Upload Image',
                  multiple: false
              }).open()
              .on('select', function(e){
                  uploaded_image = image.state().get('selection').first();
                  jQuery('#upload-img-id').val(uploaded_image.id);
                  var x= uploaded_image.attributes.filename;
                  if(x.substring(x.indexOf(".")+1)!='png'&& x.substring(x.indexOf(".")+1)!='jpeg'&&x.substring(x.indexOf(".")+1)!='jpg'){
                      console.log('hi');
                    jQuery('#image_up_error').text("Only images are allowed");
                    jQuery('#image_url_2').val(null);
                    jQuery('#image_url2').attr('src',null);
                    return;
                  }
                  var image_url = uploaded_image.toJSON().url;
                  jQuery('#image_url_2').val(image_url);
                  jQuery('#image_url2').attr('src',image_url);
                  jQuery('#image_up_error').text("");
               
              });
        });
    });
