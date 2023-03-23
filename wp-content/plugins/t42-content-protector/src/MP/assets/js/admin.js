/**
 * Content Protector for WordPress. Exclusively on Envato Market: https://1.envato.market/42themeCC
 * @encoding     UTF-8
 * @version      1.0.9
 * @copyright    Copyright (C) 2016 - 2021 42Theme (https://42theme.com). All rights reserved.
 * @license      Envato Standard Licenses
 * @author       Alexander Khmelnitskiy
 * @support      support@42theme.com
 **/

/** MP UIkit JS. */
//=require uikit.js

/** MP uikit-icons.js */
//=require uikit-icons.js

/** MP iziToast.js */
//=require iziToast.js

/** MP smoothscroll.js */
//=require smoothscroll.js

/** MP ion.rangeSlider.js */
//=require ion.rangeSlider.js

/** MP select2.js */
//=require select2.js

/** MP popper.js */
//=require popper.js

/** MP tippy.js */
//=require tippy.js

/** MP spectrum.js */
//=require spectrum.js

/** MP gpickr.js */
//=require gpickr.js

/** MP iconpicker.js */
//=require iconpicker.js

/** MP jquery.form.js */
//=require jquery.form.js

/** MP color-scheme.js */
//=require color-scheme.js

/** MP jquery.bootstrap-touchspin.js */
//=require jquery.bootstrap-touchspin.js

/** MP ripple.js */
//=require ripple.js

( function ( $ ) {

    "use strict";

    /** Run as soon as the page DOM becomes safe to manipulate. */
    $( document ).ready (function() {

        /** Save Changes button. */
        let $saveBtn = $( '.t42-save-settings-btn' );

        /** Initialise Range Sliders. */
        $( '.t42-range-slider' ).ionRangeSlider();

        /** Initialise Select2. */
        $( '.t42-select2' ).select2({
            placeholder: $( this ).data( 'placeholder' ),
            allowClear: $( this ).data( 'allow-clear' ) === "true",
            dropdownAutoWidth: true,
            minimumResultsForSearch: 8,
            width: '100%'
        });

        /** Initialise Spectrum colorpicker. */
        $( '.t42-colorpicker' ).spectrum( {
            type: 'component',
            showInput: true,
            showInitial: true,
            showButtons: false,
            clickoutFiresChange: true
        } );

        /** Initialise Gradient colorpicker. */
        $( '.t42-gpickr-box .t42-gpickr' ).each( function ( index ) {

                /** Get field container. */
                let $gradientBox = $( '.t42-gradient-mode' ).eq( index ).parent();

                /** Input to store result. */
                let $gradientInput = $gradientBox.find( 'input.t42-gradientpicker' );

                /** Preview block. */
                let $colorPreview = $gradientBox.find( '.t42-gradient-preview-box > .t42-gradient' );

                /** Mode switcher. */
                let $modeSwitcher = $gradientBox.find( '.t42-switch input[type=checkbox]' );

                /** Unique id for each gradient element. */
                let gpID = $( this ).attr( 'id' );

                /** Build options for gradient picker. */
                let GPOptions = {
                    el: '#' + gpID,
                    stops: [],
                }

                /** Create GPickr. */
                let gpickr = new GPickr( GPOptions )
                    .on( 'init', instance => {

                        let CSSGradient = $gradientInput.val();

                        /** Set only valid gradient. */
                        if ( CSSGradient.includes( 'gradient' ) ) {
                            instance.setGradient( CSSGradient );
                        }

                    } );

                /** On change set value to linked input.*/
                gpickr.on( 'change', function ( gp ) {

                    /** Update gradient only if mode switcher is enabled. */
                    if ( $modeSwitcher.is( ':checked' ) ) {

                        let CSSGradient = gp.getGradient();
                        $gradientInput.val( CSSGradient );
                        $gradientInput.trigger( 'change' );
                        $colorPreview.attr( 'style', 'background: ' + CSSGradient + ';');

                        /** Enable Save Changes button. */
                        enableSaveChangesBtn( $saveBtn );

                    }

                } );

        } );

        /** Initialise tippy for tooltips. */
        $( '.t42-tippy' ).each( function () {

            /** Show gradient picker in tooltip. */
            tippy( this, {
                theme: 't42-light',
                content( reference ) {
                    let $template = $( reference ).find( '.t42-tippy-content' );
                    $template.removeClass( 't42-hidden' );
                    return $template[0];
                },
                placement: 'top',
                popperOptions: {
                    modifiers: [
                        {
                            name: 'flip',
                            options: {
                                fallbackPlacements: ['top', 'bottom'],
                            },
                        },
                    ],
                },
                maxWidth: 400,
                interactive: true,
                allowHTML: true,
                arrow: true,
                zIndex: 999999,
                trigger: 'mouseenter'
            } );

        });

        /** Initialise tippy for Gradient Pickers. */
        $( '.t42-gradient-input-box' ).each( function () {

            /** Show gradient picker in tooltip. */
            tippy( this, {
                content( reference ) {
                    let $template = $( reference ).parent().find( '.t42-gpickr-box' );
                    return $template[0];
                },
                placement: 'bottom-start',
                offset: [42, 0],
                popperOptions: {
                    modifiers: [
                        {
                            name: 'flip',
                            options: {
                                fallbackPlacements: ['top-start', 'bottom-start'],
                            },
                        },
                    ],
                },
                maxWidth: 450,
                interactive: true,
                allowHTML: true,
                arrow: false,
                zIndex: 999999,
                hideOnClick: true,
                trigger: 'click'
            } );

        } );

        /** Gradient Mode Switcher change. */
        $( '.t42-gradient-switcher-field input' ).on( 'change', switchGradientMode );

        /**
         * Enable/Disable Gradient mode for colorpicker.
         *
         * @return {*}
         **/
        function switchGradientMode( e ) {

            let $gradientSwitcher = $( e.target );
            let $fieldContainer = $gradientSwitcher.closest( '.t42-colorpicker-field' );
            let $formControls = $fieldContainer.find( '.t42-form-controls' );
            let $gradientInput = $fieldContainer.find( 'input.t42-gradientpicker' );
            let $colorInput = $fieldContainer.find( 'input.t42-colorpicker' );

            /** Enable Gradient Mode. */
            if ( $gradientSwitcher.is( ':checked' ) ) {

                $formControls.addClass( 't42-gradient-mode-on' );
                $gradientInput.trigger( 'change' );

            }

            /** Disable Gradient Mode. */
            else {

                $formControls.removeClass( 't42-gradient-mode-on' );
                $colorInput.trigger( 'change' );

            }

        }

        /** Save settings form by ajax. */
        $( 'form.t42-form' ).ajaxForm( {

            beforeSubmit: function () {

                /** Show preloader. */
                $( '#t42-content' ).addClass( 'preloader' );

            },

            error: function () {

                // noinspection JSUnresolvedVariable
                /** Show Error message. */
                iziToast.show( {
                    title: 'Save error.',
                    message: 'Settings not saved!',
                    icon: 'ico-ban',
                    color: 'red',
                    timeout: 0,
                } );

                /** Enable Save Changes button. */
                enableSaveChangesBtn( $saveBtn );

                /** Hide preloader. */
                $( '#t42-content' ).removeClass( 'preloader' );

            },

            success: function ( data ) {

                /** Show activation messages only on activation tab. */
                if ( window.location.href.indexOf( 'tab=activation' ) > -1 ) {

                    /** If plugin activated. */
                    if ( data.includes( 't42-activated-plugin' ) ) {

                        // noinspection JSUnresolvedVariable
                        /** Show Activation Success message. */
                        iziToast.show( {
                            title: 'Activation Success.',
                            message: 'Thank you for activating.',
                            icon: 'ico-check',
                            color: 'green',
                            timeout: 7000,
                        } );

                        /** Hide activation message. */
                        $( '.t42-activation-status' ).hide( 'slow' );

                    }

                    /** If activation failed. */
                    else {

                        // noinspection JSUnresolvedVariable
                        /** Show Activation failed message. */
                        iziToast.show( {
                            title: 'Activation failed.',
                            message: 'Invalid purchase code.',
                            icon: 'ico-ban',
                            color: 'red',
                            timeout: 7000,
                        } );

                        /** Show activation message. */
                        $( '.t42-activation-status' ).show( 'slow' );

                    }

                }

                // noinspection JSUnresolvedVariable
                /** Show Settings saved message. */
                iziToast.show( {
                    title: 'Settings saved.',
                    message: 'Plugin settings saved successfully.',
                    icon: 'ico-info',
                    color: 'blue',
                    timeout: 7000,
                } );

                /** Disable Save button. */
                disableSaveChangesBtn( $saveBtn );

                /** Hide preloader. */
                $( '#t42-content' ).removeClass( 'preloader' );

            }
        } );

        /** Enable save button on any change. */
        $( ':input' ).not( '#t42-contact-support-modal *') .on( 'change keyup paste', () => {

            /** Enable Save Changes button. */
            enableSaveChangesBtn( $saveBtn );

        } );

        /** Enable save button on any change in editors. */
        if ( typeof tinymce !== 'undefined' ) {

            for ( let i = 0; i < tinymce.editors.length; i++ ) {

                tinymce.editors[i].on( 'change', function () {

                    /** Enable Save Changes button. */
                    enableSaveChangesBtn( $saveBtn );

                } );

            }

        }

        /** Show 'unsaved' message. */
        window.addEventListener( 'beforeunload', function ( e ) {

            if ( $saveBtn.hasClass( 't42-unsaved' ) ) {
                return e.returnValue = true;
            }

            delete e['returnValue'];

        } );

        /** Initialise css_editor fields. */
        function css_editor_init() {

            $( '.t42-css-editor' ).each( function () {

                let editorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
                editorSettings.codemirror = _.extend(
                    {},
                    editorSettings.codemirror, {
                        indentUnit: 2,
                        tabSize: 2,
                        mode: 'css'
                    }
                );

                let css_editor;
                let $t = $( this );
                css_editor = wp.codeEditor.initialize( $t, editorSettings );

                /** Save data from CodeEditor to textarea. */
                css_editor.codemirror.on( 'change', function() {
                    css_editor.codemirror.save();
                    $t.change();
                } );

            } );

        }
        css_editor_init();

        /** Check Updates button. */
        let $checkUpdatesBtn = $( '#t42_content_protector_updates_settings-check_updates' );
        $checkUpdatesBtn.on( 'click', function( e ) {
            e.preventDefault();

            /** Disable button and show process. */
            $checkUpdatesBtn.attr( 'disabled', true ).addClass( 't42-spin' );

            /**
             * Prepare data for AJAX request.
             *
             * @param {{t42ContentProtector:array}} data
             * @param t42ContentProtector.ajaxURL
             **/
            let data = {
                action: 'check_updates',
                nonce: t42ContentProtector.nonce,
                checkUpdates: true
            };

            /** Do AJAX request. */
            $.post( t42ContentProtector.ajaxURL, data, function( response ) {

                if ( response ) {

                    /** Show new changelog with animation. */
                    $( '.t42-changelog-box' ).fadeOut( 'slow', function() {
                        $( this ).html( response ).fadeIn( 'slow' );
                    } );

                } else {

                    console.warn( response );

                }

            }, 'json' ).fail( function( response ) {

                /** Show Error message if returned some data. */
                console.error( response );
                alert( 'Looks like an Error has occurred. Please try again later.' );

            } ).always( function() {

                /** Enable button again. */
                $checkUpdatesBtn.attr( 'disabled', false ).removeClass( 't42-spin' );

            } );

        } );

        /** Subscribe form. */
        $( '.t42-subscribe-btn' ).on( 'click', function ( e ) {
            e.preventDefault();

            let $mail = $( '.t42-subscribe-mail' );
            let $name = $( '.t42-subscribe-name' );
            let $btn =  $( '.t42-subscribe-btn' );

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

            $btn.prop( 'disabled', true );

            /**
             * Prepare data for AJAX request.
             *
             * @param {{t42ContentProtector:array}} data
             * @param t42ContentProtector.ajaxURL
             **/
            let data = {
                action: 'subscribe',
                nonce: t42ContentProtector.nonce,
                name: $name.val(),
                mail: $mail.val(),
            };

            /** Do AJAX request. */
            $.post( t42ContentProtector.ajaxURL, data, function( response ) {

                /** Successfully subscribed. */
                if ( isJSON( response ) ) {

                    response = JSON.parse( response );

                    /** Show success message. */
                    if ( response.result ) {

                        let alertBox = `
                            <div class="t42-alert-success" t42-alert="">
                                <a class="t42-alert-close" t42-close=""></a>
                                <p>We received your Subscription request.<br> Check your inbox to confirm your e-mail.</p>
                            </div>
                        `;
                        $( alertBox ).insertBefore( $btn.parent().parent() ).hide().show( 'slow' );

                    }

                }

                /** Show response as message. */
                else {

                    let alertBox = `
                            <div class="t42-alert-primary" t42-alert="">
                                <a class="t42-alert-close" t42-close=""></a>
                                <p>${response}</p>
                            </div>
                        `;
                    $( alertBox ).insertBefore( $btn.parent().parent() ).hide().show( 'slow' );

                    /** Enable button again. */
                    $btn.prop( 'disabled', false );

                }

            }, 'json' ).fail( function( response ) {

                /** Show Error message if returned some data. */
                console.error( response );

                /** Show Error message. */
                let alertBox = `<div class="t42-alert-danger" t42-alert="">
                        <a class="t42-alert-close" t42-close=""></a>
                        <p>Looks like an Error has occurred. Please try again later.</p>
                    </div>`;
                $( alertBox ).insertBefore( $btn.parent().parent() ).hide().show( 'slow' );

                /** Enable button again. */
                $btn.prop( 'disabled', false );

            } );

        } );

        /** Contact Support form. */
        $( '#t42-contact-support-modal .t42-send-btn' ).on( 'click', function ( e ) {
            e.preventDefault();

            let $mail = $( '.t42-contact-mail' );
            let $name = $( '.t42-contact-name' );
            let $message = $( '.t42-contact-message' );
            let $btn =  $( this );

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

            /** Disable send button. */
            $btn.prop( 'disabled', true );

            /**
             * Prepare data for AJAX request.
             *
             * @param {{t42ContentProtector:array}} data
             * @param t42ContentProtector.ajaxURL
             **/
            let data = {
                action: 'contact_support',
                nonce: t42ContentProtector.nonce,
                name: $name.val(),
                mail: $mail.val(),
                message: $message.val(),
                page_from: window.location.href,
            };

            /** Do AJAX request. */
            $.post( t42ContentProtector.ajaxURL, data, function( response ) {

                /** Message Successfully Sent. */
                if ( isJSON( response ) ) {

                    response = JSON.parse( response );

                    /** Show success message. */
                    if ( response ) {

                        let alertBox = `
                            <div class="t42-alert-success" t42-alert="">
                                <a class="t42-alert-close" t42-close=""></a>
                                <p>Thank you, we received your message.<br> Our support team will contact you shortly.</p>
                            </div>
                        `;
                        $( alertBox ).insertBefore( '#t42-contact-support-modal .t42-form-stacked' ).hide().show( 'slow' );

                        /** Clear message field. */
                        $message.val( '' );
                    }

                }

                /** On problem show response as message. */
                else {

                    let alertBox = `
                            <div class="t42-alert-primary" t42-alert="">
                                <a class="t42-alert-close" t42-close=""></a>
                                <p>${response}</p>
                            </div>
                        `;
                    $( alertBox ).insertBefore( '#t42-contact-support-modal .t42-form-stacked' ).hide().show( 'slow' );

                }

            }, 'json' ).fail( function( response ) {

                /** Show Error message if returned some data. */
                console.error( response );

                /** Show Error message. */
                let alertBox = `<div class="t42-alert-danger" t42-alert="">
                        <a class="t42-alert-close" t42-close=""></a>
                        <p>Looks like an Error has occurred. Please try again later.</p>
                    </div>`;
                $( alertBox ).insertBefore( '#t42-contact-support-modal .t42-form-stacked' ).hide().show( 'slow' );

            } ).always( function () {

                /** Enable button again. */
                $btn.prop( 'disabled', false );

            } );

        } );

        /** Contact Support link. */
        $( '.t42-contact-support-modal-btn' ).on( 'click', function () {

            fillContactForm( 'Contact Support', 'Enter Your Message', '' );

        } );

        /** Feature Request link. */
        $( '.t42-feature-request-modal-btn' ).on( 'click', function () {

            fillContactForm( 'Feature Request', 'Describe Your Idea', '' );

        } );

        /** Report a Bug link. */
        $( '.t42-bur-report-modal-btn' ).on( 'click', function () {

            let msg = `** Steps to Reproduce **
1. 

** Expected Result **


** Actual Result **


** Visual Proof (screenshots, videos, error text) **

`;

            fillContactForm( 'Report a Bug', 'Describe the issue in details', msg );

        } );

        /** Fill contact form fields. */
        function fillContactForm( title, placeholder, message ) {

            let $title = $( '#t42-contact-support-modal .t42-modal-title' );
            let $message = $( '#t42-contact-support-modal .t42-contact-message' );

            /** Remove old alerts. */
            $( '#t42-contact-support-modal .t42-alert' ).remove();

            $title.html( title );
            $message.attr( 'placeholder', placeholder );
            $message.val( message );

            $message.removeAttr( 'style' );
            if ( $message[0].scrollHeight > 100 ) {
                $message.height( $message[0].scrollHeight );
            } else {
                $message.height( 100 );
            }

        }

        /** Close widgets. */
        $( '.t42-widget-close-btn' ).on( 'click', function () {

            let $closeBtn = $( this );

            /**
             * Prepare data for AJAX request.
             *
             * @param {{t42ContentProtector:array}} data
             * @param t42ContentProtector.ajaxURL
             **/
            let data = {
                action: 'close_widget',
                nonce: t42ContentProtector.nonce,
                widget_id: $closeBtn.attr('id'),
            };

            /** We need confirmation. This action can't be undone. */
            if ( ! confirm( 'Hide this widget and never show it again?' ) ) { return; }

            /** Do AJAX request. */
            $.post( t42ContentProtector.ajaxURL, data, function( response ) {

                /** Successfully closed. */
                if ( response) {

                    /** Hide and remove widget. */
                    $closeBtn.parent().parent().parent().parent().hide( 'slow', function () {
                        $( this ).remove();
                    } );

                }

            }, 'json' );

        } );

        /** Validate E-mail address. */
        function validateEmail( email ) {

            // noinspection RegExpRedundantEscape
            const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test( String( email ).toLowerCase() );

        }

        /** Check if string is JSON parsable. */
        function isJSON( str ) {

            try {

                JSON.parse( str );

            } catch ( e ) {

                return false;

            }

            return true;

        }

        /** Return random element from array. */
        Array.prototype.getRandom = function() {

            return this[Math.floor( Math.random() * this.length )];

        }

        /** Getting a random integer between two values. */
        function getRandomInt( min, max ) {

            min = Math.ceil( min );
            max = Math.floor( max );

            /** The maximum is exclusive and the minimum is inclusive. */
            return Math.floor( Math.random() * (max - min) + min );

        }

        /** Show background in dev console. */
        function easterEgg() {

            /** Generate random colors. */
            let schemeName = ['mono', 'contrast', 'triade', 'tetrade', 'analogic'].getRandom();
            let variation = ['default', 'pastel', 'soft', 'light', 'hard', 'pale'].getRandom();

            let scheme = new ColorScheme;
            scheme.from_hue(getRandomInt( 1, 359 ) )
                .scheme( schemeName )
                .variation( variation );

            let colors = scheme.colors();

            let color1 = '#' + colors[0];
            let color2 = '#' + colors[1];
            let color3 = '#' + colors[2];

            /** Build background. */
            let style = `
                font-size: 20px;
                line-height:300px;
                background-color: ${color1};
                background-image: 
                    radial-gradient(circle at 100% 100%,${color2} 1.2em,${color1} 1.25em,${color1} 2.5em,rgba(255,215,0,0) 2.5em),
                    linear-gradient(135deg,${color1} 1.75em,rgba(255,215,0,0) 1.75em),
                    radial-gradient(circle at 0 100%,${color1} 1.2em,${color3} 1.25em,${color3} 2.5em,transparent 2.5em),
                    linear-gradient(-135deg,${color3} 1.75em,transparent 1.75em),
                    radial-gradient(circle at 100% 0,${color1} 1.2em,${color3} 1.25em,${color3} 2.5em,transparent 2.5em),
                    linear-gradient(45deg,${color3} 1.75em,transparent 1.75em),
                    radial-gradient(circle at 0 0,${color2} 1.2em,${color1} 1.25em,${color1} 2.5em,transparent 2.5em),
                    linear-gradient(-45deg,${color1} 1.75em,rgba(255,215,0,0) 1.75em),
                    radial-gradient(circle at 100% 100%,${color1} 1.9em,${color3} 1.95em,${color3} 2.5em,transparent 2.5em),
                    linear-gradient(135deg,${color3} 1.75em,transparent 1.75em),
                    radial-gradient(circle at 0 100%,${color2} 1.9em,${color1} 1.95em,${color1} 2.5em,rgba(255,215,0,0) 2.5em),
                    linear-gradient(-135deg,${color1} 1.75em,rgba(255,215,0,0) 1.75em),
                    radial-gradient(circle at 100% 0,${color2} 1.9em,${color1} 1.95em,${color1} 2.5em,transparent 2.5em),
                    linear-gradient(45deg,${color1} 1.75em,rgba(255,215,0,0) 1.75em),
                    radial-gradient(circle at 0 0,${color1} 1.9em,${color3} 1.95em,${color3} 2.5em,transparent 2.5em),
                    linear-gradient(-45deg,${color3} 1.75em,transparent 1.75em),
                    linear-gradient(45deg,${color3} 1.75em,transparent 1.75em),
                    linear-gradient(135deg,${color2} 1.75em,rgba(255,215,0,0) 1.75em),
                    linear-gradient(-45deg,${color2} 1.75em,rgba(255,215,0,0) 1.75em),
                    linear-gradient(-135deg,${color3} 1.75em,transparent 1.75em);
                    background-size: 15em 5em;
                    background-position: 0 0,-2.5em 2.5em,0 0,2.5em 2.5em,0 0,-2.5em 2.5em,0 0,2.5em 2.5em,5em 2.5em,2.5em 0,5em 2.5em,7.5em 0,5em 2.5em,2.5em 0,5em 2.5em,7.5em 0,7.5em 2.5em,10em 0,10em 0,12.5em 2.5em;
            `;

            let text = "%c                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ";

            console.log( text, style );

        }
        setTimeout( easterEgg, 1000 );

        /** Initialise TouchSpin controls. */
        $( '.t42-touchspin-input-field input' ).each( function () {

            let min = $( this ).data( 'min' );
            let max = $( this ).data( 'max' );
            let step = $( this ).data( 'step' );
            let decimals = $( this ).data( 'decimals' );
            let prefix = $( this ).data( 'prefix' );
            let postfix = $( this ).data( 'suffix' );

            $( this ).TouchSpin( {
                min: min,
                max: max,
                step: step,
                decimals: decimals,
                prefix: prefix,
                postfix: postfix,
                boostat: 3,
                maxboostedstep: 10,
                buttondown_class: 't42-button t42-button-primary',
                buttonup_class: 't42-button t42-button-primary',
            } );

        } );

        /** Disable Save button. */
        disableSaveChangesBtn( $saveBtn );

        /** Responsive Behavior. */
        let $leftCol = $( '#t42-left-col' );
        const smallDevice = window.matchMedia( '(min-width: 959px)' );
        smallDevice.addEventListener( 'change', handleDeviceChange );

        /** Run it initially. */
        handleDeviceChange( smallDevice );

        /** Change layout for mobile views. */
        function handleDeviceChange( e ) {

            /** Desktop view. */
            if ( e.matches && ! $leftCol.hasClass( 't42-desktop' ) ) {

                $leftCol.addClass( 't42-visible\@m t42-position-fixed t42-desktop' );
                $leftCol.insertBefore( "#t42-content" );

            }

            /** Mobile view. */
            else if( ! e.matches && $leftCol.hasClass( 't42-desktop' ) ) {

                $leftCol.removeClass( 't42-visible\@m t42-position-fixed t42-desktop' );
                $leftCol.insertBefore( "#t42-content > .t42-container > .t42-grid" );

            }

        }

        /** Disable Save Changes button. */
        function disableSaveChangesBtn( $saveBtn ) {

            $saveBtn.prop( 'disabled', true ).removeClass( 't42-unsaved' );

        }

        /** Enable Save Changes button. */
        function enableSaveChangesBtn( $saveBtn ) {

            $saveBtn.prop( 'disabled', false ).addClass( 't42-unsaved' );

        }

        /** Reset tab settings to default. */
        function resetTabSettings() {

            let $resetSettingsBtn = $( '.t42-reset-tab-settings-btn' );

            /** Exit if no reset button. */
            if ( ! $resetSettingsBtn.length ) { return; }

            /** Click on reset settings button. */
            $resetSettingsBtn.on( 'click', ( e ) => {

                e. preventDefault();

                /** We need confirmation. This action can't be undone. */
                if ( ! confirm( 'Are you sure want to reset all settings on this page to defaults?' ) ) { return; }

                /** Get current tab slug. */
                let currentTab = $resetSettingsBtn.data( 'tab' );

                /**
                 * Prepare data for AJAX request.
                 *
                 * @param {{t42ContentProtector:array}} data
                 * @param t42ContentProtector.ajaxURL
                 **/
                let data = {
                    action: 'reset_tab_settings',
                    nonce: t42ContentProtector.nonce,
                    tab: currentTab,
                };

                /** Do AJAX request. */
                $.post( t42ContentProtector.ajaxURL, data, function( response ) {

                    /** Settings was reset successfully . */
                    if ( response) {

                        /** Reload current tab. */
                        location.reload();

                    }

                }, 'json' ).always( function () {

                    /** Reload current tab. */
                    location.reload();

                } );

            } )

        }
        resetTabSettings();


    } ); // END $( document ).ready();

} ( jQuery ) );