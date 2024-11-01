jQuery(document).ready( function($) {

      imgContainer = jQuery( '.featured-img-container'),

      jQuery(document).on('click','.open-editor',function(e) {
             var postID = jQuery(this).data('postid');
             e.preventDefault();
             var image_frame;
             if(image_frame){
                 image_frame.open();
             }
             // Define image_frame as wp.media object
             image_frame = wp.media({
                 title: 'Select Media',
                 multiple : false,
                 library : {
                      type : 'image',
                  }
             });

             image_frame.on('close',function() {
                // On close, get selections and save to the hidden input
                // plus other AJAX stuff to refresh the image preview
                var selection =  image_frame.state().get('selection');
                var gallery_ids = new Array();
                var my_index = 0;
                selection.each(function(attachment) {
                   gallery_ids[my_index] = attachment['id'];
                   my_index++;
                });
                var ids = gallery_ids.join(",");
                // jQuery('input#myprefix_image_id').val(ids);
                Refresh_Image(ids,postID);
             });

            image_frame.on('open',function() {
              // On open, get the id from the hidden input
              // and select the appropiate images in the media manager
              var selection =  image_frame.state().get('selection');
              /*ids = jQuery('input#myprefix_image_id').val().split(',');
              ids.forEach(function(id) {
                attachment = wp.media.attachment(id);
                attachment.fetch();
                selection.add( attachment ? [ attachment ] : [] );
              });*/

            });

            image_frame.on( 'select', function() {
      
              // Get media attachment details from the frame state
              var attachment = image_frame.state().get('selection').first().toJSON();
              // Send the attachment URL to our custom image input field.
              jQuery('.img-'+postID).attr( 'src', attachment.url );

              // Send the attachment id to our hidden input
              // imgIdInput.val( attachment.id );

              // Hide the add image link
              // addImgLink.addClass( 'hidden' );

              // Unhide the remove image link
              // delImgLink.removeClass( 'hidden' );

            });

            image_frame.open();
     });

    jQuery(document).on('click','.removeImage', function(){

        var r = confirm("Are you sure ?");
        if (r == true) 
        {
          var postID = jQuery(this).data('postid');
          var data = {
            action: 'remove_featured_img',
            post_id: postID
          };

          jQuery.post(ajaxurl, data, function(response) {

              if(response.success === true) {
                  console.log('deleted');
                  jQuery('#feat_container_'+response.post_id).html( response.html );
              }
          });
        }
    });
});

// Ajax request to refresh the image preview
function Refresh_Image(the_id,postID){
    var data = {
        action: 'update_featured_img',
        id: the_id,
        post_id: postID
    };

    jQuery.post(ajaxurl, data, function(response) {

        if(response.success === true) {
            console.log('updated');
             jQuery('#feat_container_'+response.post_id).find('.contorls-featured-action').html( response.html );
        }
    });
}