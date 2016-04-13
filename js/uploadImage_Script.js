jQuery( document ).ready( function () {

    UPLOADCARE_PUBLIC_KEY = ajax_object.uploadcare_key;
    UPLOADCARE_LOCALE = ajax_object.uploadcare_locale;
    doc_icon = ajax_object.doc_icon;

    var $ = uploadcare.jQuery;
    var widget = uploadcare.MultipleWidget( '#uploader' );

    widget.onChange( function ( group )
    {
        var $list = $( '#list' ).empty();
        $list.css( 'visibility', 'hidden' );


        if ( !group )
        {
            $( '#wdm_uploaded' ).css( 'display', 'none' );
            return;
        }

        jQuery( '.uploadcare-widget-text' ).addClass( 'wdm-loading-text' );

        $.when.apply( this, group.files() ).done( function ()
        {
            jQuery( '.uploadcare-widget-text' ).addClass( 'wdm-add-img-link' );
            max_files = group.files().length;

            $.each( group.files(), function ( i )
            {
                var file = this;
                if ( i === 0 )
                    $( '#wdm_uploaded' ).css( 'display', 'block' );

                file.done( function ( fileInfo )
                {
                    var extension = fileInfo.name.substr( ( fileInfo.name.lastIndexOf( '.' ) + 1 ) );

                    if ( extension == 'jpg' || extension == 'jpeg' || extension == 'png' || extension == 'bmp' || extension == 'gif' ) {

                        $list.append( $( '<div/>',
                            {
                                class: 'wdm-img-upload'
                            } ).append( $( '<div/>',
                            {
                                class: 'wdm-img-grid'
                            } ).append( [
                            $( '<img/>',
                                {
                                    alt: extension,
                                    title: fileInfo.name,
                                    src: fileInfo.cdnUrl, 
                                    height: '100px',
                                    width: '100px'
                                } )
                        ] )
                            )
                            );

                    }
                    else
                    {
                        $list.append( $( '<div/>',
                            {
                                class: 'wdm-img-upload'
                            } ).append( $( '<div/>',
                            {
                                class: 'wdm-img-grid'
                            } ).append( [
                            $( '<a/>',
                                {
                                    alt: extension,
                                    title: fileInfo.name,
                                    href: fileInfo.cdnUrl,
                                } ).append( $( '<img />', { src: doc_icon } ) )
                        ] )
                            )
                            );

                    }
                } );
            } );

            jQuery( "div.wdm_img_drag_grid" ).css( 'visibility', 'visible' );

        } );

    } );


    //On click of add to cart ajax is called
    jQuery( "body" ).on( "click", 'button.single_add_to_cart_button', function ( e )
    {
        var order = "", fnames = "";
        jQuery( "div.wdm_img_drag_grid" ).children( "div" ).each( function () {

            var elem_id = jQuery( this ).find( "a" ).attr( "href" );
            var elem_name = jQuery( this ).find( "a" ).attr( "alt" );

            if ( elem_id == '' || elem_id == undefined ) {
                elem_id = jQuery( this ).find( "img" ).attr( "src" );
                elem_name = jQuery( this ).find( "img" ).attr( "alt" );
            }

            order += elem_id + "|";
            fnames += elem_name + "|";

        } );
        order = order.substring( 0, ( order.length - 1 ) );

        var data = {
            action: "to_get_image",
            fnames: fnames,
            image_position: order,
            post_id: ajax_object.post_id
        };
        jQuery.ajax( {
            url: ajax_object.ajax_url,
            type: "POST",
            async: false,
            data: data,
            success: function ( res )
            {

            }
        } );
    } );

} );
