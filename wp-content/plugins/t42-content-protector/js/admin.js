/**
 * Content Protector for WordPress. Exclusively on Envato Market: https://1.envato.market/42themeCC
 * @encoding     UTF-8
 * @version      1.0.9
 * @copyright    Copyright (C) 2016 - 2021 42Theme (https://42theme.com). All rights reserved.
 * @license      Envato Standard Licenses
 * @author       Alexander Khmelnitskiy
 * @support      support@42theme.com
 **/

( function ( $ ) {

    "use strict";

    /** Run as soon as the page DOM becomes safe to manipulate. */
    $( document ).ready( function () {

        /** JavaScript Required switcher */
        let $JSRequiredSwitcher = $( '#t42_content_protector_general_settings-js_required' )

        /** Toggle JavaScript Required Message field on switcher change. */
        $JSRequiredSwitcher.on( 'change', toggleJSRequiredMessage );

        /**
         * Show/Hide JavaScript Required Message field.
         *
         * @return {*}
         **/
        function toggleJSRequiredMessage() {

            if ( $JSRequiredSwitcher.is( ':checked' ) ) {

                $JSRequiredSwitcher.closest( 'tr' ).next().show( 350 );

            } else {

                $JSRequiredSwitcher.closest( 'tr' ).next().hide( 350 );

            }

        }
        toggleJSRequiredMessage();

        /** Copyright Dialog switcher */
        let $JSCopyrightDialog = $( '#t42_content_protector_general_settings-copyright_dialog' )

        /** Toggle Copyright Warning Message field on switcher change. */
        $JSCopyrightDialog.on( 'change', toggleJSCopyrightDialog );

        /**
         * Show/Hide Copyright Warning Message field.
         *
         * @return {*}
         **/
        function toggleJSCopyrightDialog() {

            if ( $JSCopyrightDialog.is( ':checked' ) ) {

                $JSCopyrightDialog.closest( 'tr' ).next().show( 350 );

            } else {

                $JSCopyrightDialog.closest( 'tr' ).next().hide( 350 );

            }

        }
        toggleJSCopyrightDialog();

    } );

} ( jQuery ) );