( function( $ ) {

    $( document ).ready( function() {
        $( '.contacts' ).on( 'click', '.send-email-to-contacts', function( event ) {

            var data = {
                'action': 'contacts_ajax',
                'nonce': $('.contacts-email').data('nonce'),
                'email': $('.contacts-email').val(),
            };

            $.post( contacts_script_ajax_object.ajax_url, data, function(response) {
                if (response.success) {
                    $('.contacts-email').val('');
                    $('.contacts-response span').html(response.data);
                } else {
                    $('.contacts-response span').html(response.data[0].message);
                }
            } );

        } );
    });

})( jQuery );
