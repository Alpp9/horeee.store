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

/** hotkeys.js. */
//=require hotkeys.js

/** micromodal.js. */
//=require micromodal.js

const t42ContentProtector = ( function () {

    "use strict";

    function _ContentProtector( options= {

        /** Default parameters. */
        disableSelectAll: true,
        disableCopy: true,
        disableCut: true,
        disablePaste: true,
        disableSave: true,
        disableViewSource: true,
        disablePrintPage: true,
        disableDeveloperTool: true,
        disableReaderMode: true,
        disableRightClick: true,
        disableTextSelection: true,
        disableImageDragging: true,
        copyrightDialog: false

    } ) {

        /**
         * @return {*}
         **/
        function init( options ) {

            /** Disable Select All. */
            if ( options.disableSelectAll ) { disableSelectAll(); }

            /** Disable Copy. */
            if ( options.disableCopy ) { disableCopy(); }

            /** Disable Cut. */
            if ( options.disableCut ) { disableCut(); }

            /** Disable Paste. */
            if ( options.disablePaste ) { disablePaste(); }

            /** Disable Save. */
            if ( options.disableSave ) { disableSave(); }

            /** Disable View Source. */
            if ( options.disableViewSource ) { disableViewSource(); }

            /** Disable Print Page. */
            if ( options.disablePrintPage ) { disablePrintPage(); }

            /** Disable Developer Tool. */
            if ( options.disableDeveloperTool ) { disableDeveloperTool(); }

            /** Disable Reader Mode. */
            if ( options.disableReaderMode ) { disableReaderMode(); }

            /** Disable Right Click. */
            if ( options.disableRightClick ) { disableRightClick(); }

            /** Disable Text Selection. */
            if ( options.disableTextSelection ) { disableTextSelection(); }

            /** Disable Image Dragging by Mouse. */
            if ( options.disableImageDragging ) { disableImageDragging(); }

        }
        /**
         * Show Copyright Dialog with warning.
         **/
        function showCopyrightDialog() {

            /** Exit if Copyright Dialog is disabled. */
            if ( ! options.copyrightDialog ) { return; }

            /** Show Copyright Dialog. */
            MicroModal.show( 't42-content-protector-copyright-dialog', {} );

        }

        /**
         * Disable Select All, HotKeys: Ctrl+A, ⌘+A.
         * Protect Your Text from Being Copied by Select All HotKeys.
         **/
        function disableSelectAll() {

            disable_key( 65 ); // Ctrl+A, ⌘+A.

        }

        /**
         * Disable Copy, HotKeys: Ctrl+C, ⌘+C.
         * Protect Your Text from Being Copied by Copy HotKeys.
         **/
        function disableCopy() {

            disable_key( 67 ); // Ctrl+C, ⌘+C.

        }

        /**
         * Disable Cut, HotKeys: Ctrl+X, ⌘+X.
         * Protect Your Text from Being Copied by Cut HotKeys.
         **/
        function disableCut() {

            disable_key( 88 ); // Ctrl+X, ⌘+X.

        }

        /**
         * Disable Paste, HotKeys: Ctrl+V, ⌘+V.
         * Disable Paste HotKeys.
         **/
        function disablePaste() {

            disable_key( 86 ); // Ctrl+V, ⌘+V.

        }

        /**
         * Disable Save, HotKeys: Ctrl+S, ⌘+S.
         * Protect Your Text from Being Saved by Save HotKeys.
         **/
        function disableSave() {

            disable_key( 83 ); // Ctrl+S, ⌘+S.

        }

        /**
         * Disable View Source, HotKeys: Ctrl+U, ⌘+U.
         * Disable to View Source Code of Page by HotKeys.
         **/
        function disableViewSource() {

            disable_key( 85 ); // Ctrl+U, ⌘+U.

        }

        /**
         * Disable Print Page, HotKeys: Ctrl+P, ⌘+P.
         * Protect Your Page from Being Printed by HotKeys.
         **/
        function disablePrintPage() {

            disable_key( 80 ); // Ctrl+P, ⌘+P.

        }

        /**
         * Disable Reader Mode in Safari, HotKeys: ⌘+Shift+P.
         * Protect Your Page from Being open in Reader mode
         **/
        function disableReaderMode() {

            if ( navigator.userAgent.toLowerCase().includes( 'safari' ) && !navigator.userAgent.toLowerCase().includes( 'chrome' ) ) {

                window.addEventListener( 'keydown', function( e ) {

                    if ( ( e.ctrlKey || e.metaKey ) && e.shiftKey && e.keyCode === 82 ) {
                        e.preventDefault();
                    }

                } );

            }

        }

        /**
         * Disable Developer Tool, HotKeys: Ctrl+Shift+I, ⌘+⌥+I, F12
         * Disable to View Source Code of Page by Developer Tools.
         **/
        function disableDeveloperTool() {

            hotkeys( 'command+option+j,command+option+i,command+shift+c,command+option+c,command+option+k,command+option+z,command+option+e,f12,ctrl+shift+i,ctrl+shift+j,ctrl+shift+c,ctrl+shift+k,ctrl+shift+e,shift+f7,shift+f5,shift+f9,shift+f12', function( event, handler ) {
                event.preventDefault();
            } );

            /** Disabled developer tools detections for iOS. */
            if ( is_iOS() ) { return; }

            /** Short way to detect iOS. */
            function is_iOS() {
                return [
                        'iPad Simulator',
                        'iPhone Simulator',
                        'iPod Simulator',
                        'iPad',
                        'iPhone',
                        'iPod'
                    ].includes(navigator.platform)
                    // iPad on iOS 13 detection
                    || (navigator.userAgent.includes("Mac") && "ontouchend" in document)
            }

            (function () {
                if ( typeof window.CustomEvent === "function" ) return false;

                function CustomEvent( event, params ) {
                    params = params || { bubbles: false, cancelable: false, detail: undefined };
                    var evt = document.createEvent( "CustomEvent" );
                    evt.initCustomEvent( event, params.bubbles, params.cancelable, params.detail );
                    return evt
                }

                CustomEvent.prototype = window.Event.prototype;
                window.CustomEvent = CustomEvent
            })();

            (function () {
                'use strict';
                var devtools = {
                    open: false,
                    orientation: null
                };
                var threshold = 160;
                var emitEvent = function (state, orientation) {
                    window.dispatchEvent(new CustomEvent('devtoolschange', {
                        detail: {
                            open: state,
                            orientation: orientation
                        }
                    }));
                };

                setInterval(function () {
                    var widthThreshold = window.outerWidth - window.innerWidth > threshold;
                    var heightThreshold = window.outerHeight - window.innerHeight > threshold;
                    var orientation = widthThreshold ? 'vertical' : 'horizontal';

                    if (!(heightThreshold && widthThreshold) &&
                        ((window.Firebug && window.Firebug.chrome && window.Firebug.chrome.isInitialized) || widthThreshold || heightThreshold)) {
                        if (!devtools.open || devtools.orientation !== orientation) {
                            emitEvent(true, orientation);
                        }

                        devtools.open = true;
                        devtools.orientation = orientation;
                    } else {
                        if (devtools.open) {
                            emitEvent(false, null);
                        }

                        devtools.open = false;
                        devtools.orientation = null;
                    }
                }, 500);

                if (typeof module !== 'undefined' && module.exports) {
                    module.exports = devtools;
                } else {
                    window.devtools = devtools;
                }
            })();

            let element = new Image;
            let ua = navigator.userAgent.toLowerCase();
            let msie = ua.indexOf( "msie" ) > -1 || ua.indexOf( "edge/" ) > -1;
            if ( ! msie && navigator.appName === "Netscape" && ua.indexOf( "trident/" ) > -1 ) { msie = true; }
            var chrome = ! msie && ua.indexOf( "chrome" ) > -1;
            var safari = ! chrome && ! msie && ua.indexOf( "safari" ) > -1;
            let consoleMsg = "No source code for you! Close the console to refresh the page :)";
            let consoleStyle = "background: black; color: transparent; font-size: 0px;";

            let timerMax = 500;
            let utm = '__utmq';

            /** Renamed console.log to soleCon.go */
            let soleCon = clone( console );

            /** Copy and rename console.log to soleCon.go */
            function clone( obj ) {
                if ( null == obj || "object" != typeof obj ) { return obj; }
                let copy = obj.constructor();
                for ( let attr in obj ) {
                    if ( obj.hasOwnProperty( attr ) ) {
                        copy[attr === "log" ? "go" : attr] = obj[attr];
                    }
                }
                return copy;
            }

            function opened() {
                rmbody();
            }

            function closed() {
                noconsole();
                location.reload();
            }

            function noconsole2() {
                noconsole();
            }

            function noconsole() {
                if ( readCookie( utm ) === '1' ) { return; }
                createCookie( utm, 1, 365 * 10 );
                if ( inIframe() ) { return; }
                location.reload();
            }

            function inIframe () {
                try {
                    return window.self !== window.top;
                } catch (e) {
                    return true;
                }
            }

            function egg8log( arg ) {

                if ( msie ) {

                    arg ? soleCon.go( consoleMsg, arg ) : soleCon.go( consoleMsg );

                } else {

                    arg ? soleCon.go( "%c" + consoleMsg, consoleStyle, arg ) : soleCon.go( "%c" + consoleMsg, consoleStyle )

                }

            }

            if ( readCookie( utm ) !== '1' ) {

                let threshold = 160;
                let widthThreshold = window.outerWidth - window.innerWidth > threshold;
                let heightThreshold = window.outerHeight - window.innerHeight > threshold;
                let orientation = widthThreshold ? "vertical" : "horizontal";

                if ( !(heightThreshold && widthThreshold) &&
                    (window.Firebug && window.Firebug.chrome && window.Firebug.chrome.isInitialized || widthThreshold || heightThreshold) ) {

                    if ( ! devtools.open || devtools.orientation !== orientation ) {}

                } else {

                    noconsole();

                }
            }
            dtToStr();

            element.__defineGetter__( 'id', function () {
                if ( intr ) { clearTimeout( intr ); }
                rmbody();
                setTimeout( noconsole2, timerMax * 1.5 );
            } );

            window.addEventListener( 'devtoolschange', function ( e ) {

                if ( e.detail.open ) {
                    rmbody();
                    egg8log();
                } else {
                    noconsole();
                }

            } );

            /** Remove Document body on devtools open. */
            function rmbody() {

                if ( inIframe() ) { return; }

                if ( document.body != null ) {
                    document.body.parentNode.removeChild( document.body );
                }

                if ( document.head != null ) {
                    document.head.parentNode.removeChild( document.head );
                }

            }

            function dtToStr() {

                let cnt = 0;
                let open = 0;

                if ( ! safari && ! chrome ) { return; }

                let isOpen = () => cnt > (safari ? 0 : chrome ? 1 : 1);

                let w = new Function;
                w.toString = () => {
                    cnt++;
                    checkOpen()
                };

                let test = () => {
                    checkClosed();
                    cnt = 0;
                    egg8log( w );
                };

                let checkOpen = () => {

                    if ( isOpen() && ! open ) {

                        open = 1;
                        opened();

                    }

                };

                let checkClosed = () => {

                    if ( ! isOpen() && open ) {

                        open = 0;
                        closed();

                    }

                };

                setInterval( test, 500 );

            }

            function createCookie( name, value, days ) {

                if ( days > 0 ) { eraseCookie( name ); }

                let expires = '';
                if ( days ) {

                    let date = new Date;
                    date.setTime( date.getTime() + days * 24 * 60 * 60 * 1000 );

                    expires = "; expires=" + date.toGMTString();

                }

                document.cookie = name + "=" + value + expires + "; path=/"

            }

            function readCookie( name ) {

                let nameEQ = name + "=";
                let ca = document.cookie.split( ";" );

                for ( let i = 0; i < ca.length; i++ ) {

                    let c = ca[i];
                    while ( c.charAt( 0 ) === ' ' ) {
                        c = c.substring( 1, c.length );
                    }

                    if ( c.indexOf( nameEQ ) === 0 ) {

                        return c.substring( nameEQ.length, c.length );

                    }

                }

                return null

            } 

            function eraseCookie( name ) {

                createCookie( name, '', -1 );

            }

        }

        /**
         * Disable Right Click, Context Menu by Mouse Right Click.
         * Protect Your Content from Being Copied by Context Menu.
         **/
        function disableRightClick() {

            document.oncontextmenu = function( e ) {

                var t = e || window.event;
                var n = t.target || t.srcElement;

                if ( n.nodeName !== 'A' ) {

                    /** Show Copyright Dialog with warning. */
                    showCopyrightDialog();

                    return false;

                }

            };

            document.body.oncontextmenu = function () {

                /** Show Copyright Dialog with warning. */
                showCopyrightDialog();

                return false;

            };

            document.onmousedown = function ( e ) {

                if ( e.button === 2 ) {

                    /** Show Copyright Dialog with warning. */
                    showCopyrightDialog();

                    return false;

                }

            };

            /** To block "Enable Right Click" extensions. */
            let enableRightClickExtensionsInterval = setInterval( function () {

                if ( null === document.oncontextmenu ) {

                    document.body.parentNode.removeChild( document.body );
                    document.head.parentNode.removeChild( document.head );

                    clearInterval( enableRightClickExtensionsInterval );

                }

            }, 500 );

        }

        /**
         * Disable Text Selection.
         * Disable Text Highlight (Text Selection) by Mouse.
         **/
        function disableTextSelection() {

            if ( typeof document.body.onselectstart != 'undefined' ){
                document.body.onselectstart = function(){ return false; };
            } else if ( typeof document.body.style.MozUserSelect !== 'undefined' ) {
                document.body.style.MozUserSelect = 'none';
            } else if ( typeof document.body.style.webkitUserSelect !== 'undefined' ) {
                document.body.style.webkitUserSelect = 'none';
            } else {
                document.body.onmousedown = function(){ return false; };
            }

            /** Skip css layer for Safari. */
            if ( navigator.userAgent.search( "Safari" ) >= 0 && navigator.userAgent.search( "Chrome" ) < 0 ) { return; }

            /** Add css layer protection. */
            document.documentElement.style.webkitTouchCallout = "none";
            document.documentElement.style.webkitUserSelect = "none";
            let css = document.createElement( 'style' );
            css.type = 'text/css';
            css.innerText = `
            *:not(input):not(textarea):not([contenteditable=""]):not([contenteditable="true"]) {
                -webkit-user-select: none !important;
                -moz-user-select: none !important;
                -ms-user-select: none !important;
                user-select: none !important;
            }`;
            document.head.appendChild( css );

        }

        /**
         * Disable Image Dragging by Mouse.
         **/
        function disableImageDragging() {

            document.ondragstart = function() {

                /** Show Copyright Dialog with warning. */
                showCopyrightDialog();

                return false;
            };

        }

        /**
         * Disable CTRL|CMD + Key by key Code.
         *
         * @param {number} code
         **/
        function disable_key( code ) {

            window.addEventListener( 'keydown', function( e ) {

                /** For Windows Check CTRL. */
                if ( e.ctrlKey && e.which === code ) {

                    /** Show Copyright Dialog with warning. */
                    showCopyrightDialog();

                    e.preventDefault();
                }

                /** For Mac Check Metakey. */
                if ( e.metaKey && e.which === code ) {

                    /** Show Copyright Dialog with warning. */
                    showCopyrightDialog();

                    e.preventDefault();

                }

            } );

            document.keypress = function( e ) {

                /** For Windows Check CTRL. */
                if ( e.ctrlKey && e.which === code ) {

                    /** Show Copyright Dialog with warning. */
                    showCopyrightDialog();

                    return false;

                }

                /** For Mac Check Metakey. */
                if ( e.metaKey && e.which === code ) {

                    /** Show Copyright Dialog with warning. */
                    showCopyrightDialog();

                    return false;

                }

            };

        }

        return {
            init: init( options )
        };

    }

    return _ContentProtector;

} )();

document.addEventListener( 'DOMContentLoaded', function () {

    /** Content Protector Options. */
    let options = {

        /**
         * Disable Select All: Disable HotKeys: Ctrl+A (Windows and Linux), ⌘+A (macOS).
         * Protect Your Text from Being Copied by Select All HotKeys.
         **/
        disableSelectAll: true,

        /**
         * Disable Copy: Disable HotKeys: Ctrl+C (Windows and Linux), ⌘+C (macOS).
         * Protect Your Text from Being Copied by Copy HotKeys.
         **/
        disableCopy: true,

        /**
         * Disable Cut:	Disable HotKeys: Ctrl+X (Windows and Linux), ⌘+X (macOS).
         * Protect Your Text from Being Copied by Cut HotKeys.
         **/
        disableCut: true,

        /**
         * Disable Paste: Disable HotKeys: Ctrl+V (Windows and Linux), ⌘+V (macOS).
         * Disable Paste HotKeys.
         **/
        disablePaste: true,

        /**
         * Disable Save: Disable HotKeys: Ctrl+S (Windows and Linux), ⌘+S (macOS).
         * Protect Your Text from Being Saved by Save HotKeys.
         **/
        disableSave: true,

        /**
         * Disable View Source: Disable HotKeys: Ctrl+U (Windows and Linux), ⌘+U (macOS).
         * Disable to View Source Code of Page by HotKeys.
         **/
        disableViewSource: true,

        /**
         * Disable Print Page: Disable HotKeys: Ctrl+P (Windows and Linux), ⌘+P (macOS).
         * Protect Your Page from Being Printed by HotKeys.
         **/
        disablePrintPage: true,

        /**
         * Disable Developer Tool: Disable HotKeys: Ctrl+Shift+I (Windows and Linux), ⌘+⌥+I (macOS).
         * Disable to View Source Code of Page by Developer Tools.
         **/
        disableDeveloperTool: true,

        /**
         * Disable Safari Reader Mode: Disable Reader mode in Safari: ⌘+Shift+R (macOS).
         * Protect Your Text and Images from being copied in the Safari Reader mode.
         **/
        disableReaderMode: true,

        /**
         * Disable Right Click: Disable Mouse Right Click.
         * Protect Your Content from Being Copied by Context Menu.
         **/
        disableRightClick: true,

        /**
         * Disable Text Selection: Disable Text Selection.
         * Disable Text Highlight (Text Selection) by Mouse.
         **/
        disableTextSelection: true,

        /**
         * Disable Image Dragging by Mouse.
         **/
        disableImageDragging: true,

        /**
         * Show Copyright Dialog.
         **/
        copyrightDialog: false
    };

    /**
     * Get Settings from WordPress.
     *
     * @param window.t42ContentProtectorOptions
     **/
    if ( typeof t42ContentProtectorOptions  !== 'undefined' ) {
        options = t42ContentProtectorOptions;
    }

    new t42ContentProtector( options );

} );