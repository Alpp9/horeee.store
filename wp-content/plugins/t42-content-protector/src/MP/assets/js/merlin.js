/**
 * Content Protector for WordPress. Exclusively on Envato Market: https://1.envato.market/42themeCC
 * @encoding     UTF-8
 * @version      1.0.9
 * @copyright    Copyright (C) 2016 - 2021 42Theme (https://42theme.com). All rights reserved.
 * @license      Envato Standard Licenses
 * @author       Alexander Khmelnitskiy
 * @support      support@42theme.com
 **/

"use strict";

let Merlin = ( function($){

    let t;

    function window_loaded() {

    	let body 		= $('.merlin__body');
    	let drawer_trigger 	= $('#merlin__drawer-trigger');
    	let drawer_opened 	= 'merlin__drawer--open';

    	setTimeout(function(){
	        body.addClass('loaded');
	    },100);

    	drawer_trigger.on('click', function(){
        	body.toggleClass( drawer_opened );
        });

        /** Requirements step. */
        if ( $( '.merlin__content.requirements' ).length ) {

            setTimeout( function () {

                body.addClass( drawer_opened );

            }, 1000 );

        }

    	/** Next button. */
    	$('.merlin__button--proceed:not(.merlin__button--closer)').click( function (e) {

		    e.preventDefault();

            let goTo = this.getAttribute( "href" );

            /** Requirements step. */
            if ( $( this ).parents( '.merlin__content.requirements' ).length ) {

                setTimeout( function () {
                    $( '.merlin__body' ).addClass( 'js--finished' );
                }, 300 );

                setTimeout( function () {
                    body.removeClass( drawer_opened );
                    $( '.merlin__body' ).addClass( 'exiting' );
                }, 2300 );

                setTimeout( function () {
                    window.location = goTo;
                }, 2800 );

            }

            /** Normal step. */
            else {

                body.addClass('exiting');

                setTimeout(function(){
                    window.location = goTo;
                },400);

            }

    	} );

        $(".merlin__button--closer").on('click', function(e){

        	body.removeClass( drawer_opened );

            e.preventDefault();
            let goTo = this.getAttribute( 'href' );

		    setTimeout(function(){
                body.addClass( 'exiting' );
		    },600);

		    setTimeout(function(){
		        window.location = goTo;
		    },1100);

        });

        /** Subscribe button. */
        $( ".t42-merlin-subscribe-btn" ).on( 'click', function ( e ) {
            e.preventDefault();

            let $btn = $( this );
            if ( $btn.hasClass( 'process' ) ) { return; }
            $btn.addClass( 'process' );

            let goTo = $btn[0].getAttribute( 'href' );

            /** Validate email. */
            let $mail = $( '[name="t42-merlin-email-input"]' );
            let join = document.getElementById( 't42-merlin-subscribe' ).checked;

            /** Valid email. */
            if ( ! validateEmail( $mail.val() ) ) {
                $mail.addClass( 't42-form-danger' );
                $mail.removeClass( 't42-form-success' );
                return;
            }

            /** Invalid email. */
            else {
                $mail.removeClass( 't42-form-danger' );
                $mail.addClass( 't42-form-success' );
            }

            /**
             * Prepare data for AJAX request.
             *
             * @param {{merlin_params:array}} data
             * @param merlin_params.ajaxurl
             * @param merlin_params.wpnonce
             **/
            let data = {
                action: 'merlin_subscribe',
                wpnonce: merlin_params.wpnonce,
                join: join,
                mail: $mail.val(),
            };

            /** Do AJAX request to subscribe. */
            $.post( merlin_params.ajaxurl, data, function( response ) {

                if ( response.success ) {

                    /** Show Green Checkmark and go to next step. */
                    showGreenCheckmark( goTo );

                } else {

                    /** Show Activation failed message. */
                    subscriptionFailedMsg( $btn );
                    console.warn( response );

                }

            }, 'json' ).fail( function( response ) {

                subscriptionFailedMsg( $btn );
                console.warn( response );

            } );

        } );

        /** Activate button. */
        $( '.t42-merlin-license-activate-btn' ).on( 'click', function ( e ) {

            e.preventDefault();

            let $btn = $( this );

            if ( $btn.hasClass( 'process' ) ) { return; }
            $btn.addClass( 'process' );

            let goTo = $btn[0].getAttribute( 'href' );
            let purchaseCode = $( '#license-key' ).val();

            /**
             * Prepare data for AJAX request.
             *
             * @param {{merlin_params:array}} data
             * @param merlin_params.ajaxurl
             * @param merlin_params.wpnonce
             **/
            let data = {
                action: 'merlin_activate',
                wpnonce: merlin_params.wpnonce,
                purchase_code: purchaseCode,
            };

            /** Do AJAX request to activate. */
            $.post( merlin_params.ajaxurl, data, function( response ) {

                if ( response.success ) {

                    /** Show Green Checkmark and go to next step. */
                    showGreenCheckmark( goTo );

                } else {

                    /** Show Activation failed message. */
                    activationFailedMsg( $btn );
                    console.warn( response );

                }

            }, 'json' ).fail( function( response ) {

                /** Show Activation failed message. */
                activationFailedMsg( $btn );
                console.warn( response );

            } );

        } );

        /** Show Subscription failed message. */
        function subscriptionFailedMsg( $btn ) {

            alert( 'Subscription failed. Check your e-mail and try again. Or skip this step.' );
            $btn.removeClass( 'process' );

        }

        /** Show Activation failed message. */
        function activationFailedMsg( $btn ) {

            alert( 'Activation failed. Check your purchase code and try again. Or skip this step.' );
            $btn.removeClass( 'process' );

        }

        /** Show Green Checkmark and go to next step. */
        function showGreenCheckmark( goTo ) {

            setTimeout( function () {
                $( '.merlin__body' ).addClass( 'js--finished' );
            }, 300 );

            setTimeout( function () {
                $( '.merlin__body' ).addClass( 'exiting' );
            }, 2300 );

            setTimeout( function () {
                window.location = goTo;
            }, 2800 );

        }

    } // End window_loaded().

    /** Validate E-mail address. */
    function validateEmail( email ) {

        // noinspection RegExpRedundantEscape
        const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test( String( email ).toLowerCase() );

    }

    return {

        init: function(){
            t = this;
            $( window_loaded );
        },

        callback: function ( func ) {
            console.log(func);
            console.log(this);
        }

    }

} ) ( jQuery );

Merlin.init();
