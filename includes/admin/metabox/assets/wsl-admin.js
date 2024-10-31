jQuery( function ( $ ) {

    var $upload       = {
            imgPreview  : '.upload-img-preview',
            uploadButton: '.upload-attachment-button',
            imgUrl             : $( '.upload-img-url' )
        },
        $wpAddMedia   = $( '.add_media' );

    
    /* Upload */
    if ( typeof wp !== 'undefined' && typeof wp.media !== 'undefined' ) {

        var _custom_media = true;
        // preview
        $upload.imgUrl.change( function () {
            var url     = $( this ).val(),
                re      = new RegExp( "(http|ftp|https)://[a-zA-Z0-9@?^=%&amp;:/~+#-_.]*.(gif|jpg|jpeg|png|ico)" ),
                preview = $( this ).parent().find( $upload.imgPreview ).first();

            if ( preview.length < 1 )
                preview = $( this ).parent().parent().find( $upload.imgPreview ).first();

            if ( re.test( url ) ) {
                preview.html( '<img src="' + url + '" style="max-width:100px; max-height:100px;" />' );
            } else {
                preview.html( '' );
            }
        } ).trigger( 'change' );

        $( document ).on( 'click', $upload.uploadButton, function ( e ) {
            e.preventDefault();

            var t  = $( this ),
                custom_uploader,
                id = t.attr( 'id' ).replace( /-button$/, '' );

            //If the uploader object has already been created, reopen the dialog
            if ( custom_uploader ) {
                custom_uploader.open();
                return;
            }

            var custom_uploader_states = [
                // Main states.
                new wp.media.controller.Library( {
                    library   : wp.media.query(),
                    multiple  : false,
                    title     : 'Choose Image',
                    priority  : 20,
                    filterable: 'uploaded'
                 } )
            ];

            // Create the media frame.
            custom_uploader = wp.media.frames.downloadable_file = wp.media( {
                // Set the title of the modal.
                title   : 'Choose Image',
                library : {
                    type: ''
                },
                button  : {
                    text: 'Choose Image'
                },
                multiple: false,
                states  : custom_uploader_states
            } );

            //When a file is selected, grab the URL and set it as the text field's value
            custom_uploader.on( 'select', function () {
                var attachment = custom_uploader.state().get( 'selection' ).first().toJSON();

                $( "#" + id ).val( attachment.url );
                // Save the id of the selected element to an element which name is the same with a suffix "-yith-attachment-id"
                if ( $( "#" + id + "-attachment-id" ) ) {
                    $( "#" + id + "-attachment-id" ).val( attachment.id );
                }
                $upload.imgUrl.trigger( 'change' );
            } );

            //Open the uploader dialog
            custom_uploader.open();
        } );   
    }

    $wpAddMedia.on( 'click', function () {
        _custom_media = false;
    } );    
} );
