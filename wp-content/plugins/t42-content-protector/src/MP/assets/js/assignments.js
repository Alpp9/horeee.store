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

    /**
     * Plugin Assignments Tab
     **/

    /** INPUT which will save the settings */
    let $assignInput = $("#t42-assignInput");
    
    /**
     * Read assignments settings.
     **/
    function ReadAssignments () {
        
        // Get assignments from field
        let aConf = '';

        try {

            let aConfJson = $assignInput.val();
            aConfJson = aConfJson.replace(/\|/g, '"');
            aConf = JSON.parse( aConfJson );

            // Matching Method
            let matchingMethod = aConf.matchingMethod;
            $("#t42-assign-box .t42-matching-method .t42-button-group .t42-button").removeClass("t42-active");
            if(matchingMethod === 0){ $("#t42-assign-box .t42-matching-method .t42-button-group .t42-all").addClass("t42-active"); }
            if(matchingMethod === 1){ $("#t42-assign-box .t42-matching-method .t42-button-group .t42-any").addClass("t42-active"); }
            
            // WordPress Content
            let WPContent = aConf.WPContent;
            let WPContentVal = aConf.WPContentVal + '';
            $("#t42-assign-box .t42-wp-content .t42-button-group .t42-button").removeClass("t42-active");
            if(WPContent === 0){ $("#t42-assign-box .t42-wp-content .t42-button-group .t42-ignore").addClass("t42-active"); }
            if(WPContent === 1){ $("#t42-assign-box .t42-wp-content .t42-button-group .t42-include").addClass("t42-active"); }
            if(WPContent === 2){ $("#t42-assign-box .t42-wp-content .t42-button-group .t42-exclude").addClass("t42-active"); }
            let WPContentArray = WPContentVal.split(',');
            if( WPContentVal !== '' ) { $("#t42-assign-box .t42-wp-content select.wp-content").val(WPContentArray).trigger( 'change' ); }

            // Home Page
            let homePage = aConf.homePage;
            $("#t42-assign-box .t42-home-page .t42-button-group .t42-button").removeClass("t42-active");
            if(homePage === 0){ $("#t42-assign-box .t42-home-page .t42-button-group .t42-ignore").addClass("t42-active"); }
            if(homePage === 1){ $("#t42-assign-box .t42-home-page .t42-button-group .t42-include").addClass("t42-active"); }
            if(homePage === 2){ $("#t42-assign-box .t42-home-page .t42-button-group .t42-exclude").addClass("t42-active"); }
            
            // Menu Items
            let menuItems = aConf.menuItems;
            let menuItemsVal = aConf.menuItemsVal + '';
            $("#t42-assign-box .t42-menu-items .t42-button-group .t42-button").removeClass("t42-active");
            if(menuItems === 0){ $("#t42-assign-box .t42-menu-items .t42-button-group .t42-ignore").addClass("t42-active"); }
            if(menuItems === 1){ $("#t42-assign-box .t42-menu-items .t42-button-group .t42-include").addClass("t42-active"); }
            if(menuItems === 2){ $("#t42-assign-box .t42-menu-items .t42-button-group .t42-exclude").addClass("t42-active"); }
            let menuItemsArray = menuItemsVal.split(",");
            if ( menuItemsVal !== '' ) { $( "#t42-assign-box .t42-menu-items select.menuitems" ).val( menuItemsArray ).trigger( 'change' ); }

            // Date & Time
            let dateTime = aConf.dateTime;
            let dateTimeStart = aConf.dateTimeStart;
            let dateTimeEnd = aConf.dateTimeEnd;
            $( '#t42-assign-box .t42-date-time .t42-button-group .t42-button' ).removeClass( 't42-active' );
            if( dateTime === 0 ) { $( '#t42-assign-box .t42-date-time .t42-button-group .t42-ignore' ).addClass( 't42-active' ); }
            if( dateTime === 1 ) { $( '#t42-assign-box .t42-date-time .t42-button-group .t42-include' ).addClass( 't42-active' ); }
            if( dateTime === 2 ) { $( '#t42-assign-box .t42-date-time .t42-button-group .t42-exclude' ).addClass( 't42-active' ); }
            $( '#t42-assign-box .t42-date-time input.t42-period-picker-start' ).val( dateTimeStart );
            $( '#t42-assign-box .t42-date-time input.t42-period-picker-end' ).val( dateTimeEnd );

            // User Roles
            let userRoles = aConf.userRoles;
            let userRolesVal = aConf.userRolesVal + '';
            $("#t42-assign-box .t42-user-roles .t42-button-group .t42-button").removeClass("t42-active");
            if(userRoles === 0){ $("#t42-assign-box .t42-user-roles .t42-button-group .t42-ignore").addClass("t42-active"); }
            if(userRoles === 1){ $("#t42-assign-box .t42-user-roles .t42-button-group .t42-include").addClass("t42-active"); }
            if(userRoles === 2){ $("#t42-assign-box .t42-user-roles .t42-button-group .t42-exclude").addClass("t42-active"); }
            let userRolesArray = userRolesVal.split(",");
            if ( userRolesVal !== '' ) { $( "#t42-assign-box .t42-user-roles select.user-roles" ).val( userRolesArray ).trigger( 'change' );}

            // URL
            let URL = aConf.URL;
            let URLVal = aConf.URLVal;
            $("#t42-assign-box .t42-url .t42-button-group .t42-button").removeClass("t42-active");
            if(URL === 0){ $("#t42-assign-box .t42-url .t42-button-group .t42-ignore").addClass("t42-active"); }
            if(URL === 1){ $("#t42-assign-box .t42-url .t42-button-group .t42-include").addClass("t42-active"); }
            if(URL === 2){ $("#t42-assign-box .t42-url .t42-button-group .t42-exclude").addClass("t42-active"); }
            $("#t42-assign-box .t42-url textarea.t42-url-field").val(URLVal);
            
            /** Devices. */
            let devices = aConf.devices;
            let devicesVal = aConf.devicesVal + '';
            $( '#t42-assign-box .t42-devices .t42-button-group .t42-button' ).removeClass( 't42-active' );
            if ( devices === 0) { $( '#t42-assign-box .t42-devices .t42-button-group .t42-ignore' ).addClass( 't42-active' ); }
            if ( devices === 1) { $( '#t42-assign-box .t42-devices .t42-button-group .t42-include' ).addClass( 't42-active' ); }
            if ( devices === 2) { $( '#t42-assign-box .t42-devices .t42-button-group .t42-exclude' ).addClass( 't42-active' ); }
            let devicesArray = devicesVal.split( ',' );
            if ( devicesVal !== '' ) { $( '#t42-assign-box .t42-devices select.devices' ).val( devicesArray ).trigger( 'change' ); }

            // Custom PHP
            let PHP = aConf.PHP;
            let PHPVal = aConf.PHPVal;
            $( '#t42-assign-box .t42-php .t42-button-group .t42-button' ).removeClass( 't42-active' );
            if ( PHP === 0 ) { $( '#t42-assign-box .t42-php .t42-button-group .t42-ignore' ).addClass( 't42-active' ); }
            if ( PHP === 1 ) { $( '#t42-assign-box .t42-php .t42-button-group .t42-include' ).addClass( 't42-active' ); }
            if ( PHP === 2 ) { $( '#t42-assign-box .t42-php .t42-button-group .t42-exclude' ).addClass( 't42-active' ); }
            $( '#t42-assign-box .t42-php textarea.t42-php-field' ).val( PHPVal );

        } catch (e) {
            
            // Reset all controls to default state
            $("#t42-assign-box .t42-button-group .t42-button").removeClass("t42-active");
            
            // Matching Method
            $("#t42-assign-box .t42-matching-method .t42-button-group .t42-all").addClass("t42-active");
            
            // WordPress Content
            $("#t42-assign-box .t42-wp-content .t42-button-group .t42-ignore").addClass("t42-active");
            $( "#t42-assign-box .t42-wp-content select.wp-content" ).val( '' ).trigger( 'change' );

            // Home Page
            $("#t42-assign-box .t42-home-page .t42-button-group .t42-ignore").addClass("t42-active");
            
            // Menu Items
            $("#t42-assign-box .t42-menu-items .t42-button-group .t42-ignore").addClass("t42-active");
            $( "#t42-assign-box .t42-menu-items select.menuitems" ).val( '' ).trigger( 'change' );

            /** Date & Time. */
            $( '#t42-assign-box .t42-date-time .t42-button-group .t42-ignore' ).addClass( 't42-active' );
            $( '#t42-assign-box .t42-date-time input.t42-period-picker-start' ).val('');
            $( '#t42-assign-box .t42-date-time input.t42-period-picker-end' ).val('');

            // User Roles
            $("#t42-assign-box .t42-user-roles .t42-button-group .t42-ignore").addClass("t42-active");
            $( "#t42-assign-box .t42-user-roles select.user-roles" ).val( '' ).trigger( 'change' );

            // URL
            $("#t42-assign-box .t42-url .t42-button-group .t42-ignore").addClass("t42-active");
            $("#t42-assign-box .t42-url textarea.t42-url-field").val("");
            
            /** Devices. */
            $( '#t42-assign-box .t42-devices .t42-button-group .t42-ignore' ).addClass( 't42-active' );
            $( '#t42-assign-box .t42-devices select.devices' ).val( '' ).trigger( 'change' );
            
            /** Custom PHP. */
            $( '#t42-assign-box .t42-php .t42-button-group .t42-ignore' ).addClass( 't42-active' );
            $( '#t42-assign-box .t42-php textarea.t42-php-field' ).val('');
            
        }

    }
        
    /**
     * Save settings.
     **/
    function SaveAssignments() {

        /** Get new values */
        
        // Matching Method
        let matchingMethod = 0;
        if($("#t42-assign-box .t42-matching-method .t42-button-group .t42-all").hasClass("t42-active")) { matchingMethod = 0; }
        if($("#t42-assign-box .t42-matching-method .t42-button-group .t42-any").hasClass("t42-active")) { matchingMethod = 1; }

        // WordPress Content
        let WPContent = 0;
        if($("#t42-assign-box .t42-wp-content .t42-button-group .t42-ignore").hasClass("t42-active")) { WPContent = 0; }
        if($("#t42-assign-box .t42-wp-content .t42-button-group .t42-include").hasClass("t42-active")) { WPContent = 1; }
        if($("#t42-assign-box .t42-wp-content .t42-button-group .t42-exclude").hasClass("t42-active")) { WPContent = 2; }
        
        let WPContentVal = '';
        if(WPContent){
            WPContentVal = $("#t42-assign-box .t42-wp-content select.wp-content").val();
        }
        
        // Home Page
        let homePage = 0;
        if($("#t42-assign-box .t42-home-page .t42-button-group .t42-ignore").hasClass("t42-active")) { homePage = 0; }
        if($("#t42-assign-box .t42-home-page .t42-button-group .t42-include").hasClass("t42-active")) { homePage = 1; }
        if($("#t42-assign-box .t42-home-page .t42-button-group .t42-exclude").hasClass("t42-active")) { homePage = 2; }
        
        // Menu Items
        let menuItems = 0;
        if($("#t42-assign-box .t42-menu-items .t42-button-group .t42-ignore").hasClass("t42-active")) { menuItems = 0; }
        if($("#t42-assign-box .t42-menu-items .t42-button-group .t42-include").hasClass("t42-active")) { menuItems = 1; }
        if($("#t42-assign-box .t42-menu-items .t42-button-group .t42-exclude").hasClass("t42-active")) { menuItems = 2; }
        
        let menuItemsVal = '';
        if(menuItems){
            menuItemsVal = $("#t42-assign-box .t42-menu-items select.menuitems").val();
        }
        
        /** Date & Time. */
        let dateTime = 0;
        if ( $( '#t42-assign-box .t42-date-time .t42-button-group .t42-ignore' ).hasClass( 't42-active' ) ) { dateTime = 0; }
        if ( $( '#t42-assign-box .t42-date-time .t42-button-group .t42-include' ).hasClass( 't42-active' ) ) { dateTime = 1; }
        if ( $( '#t42-assign-box .t42-date-time .t42-button-group .t42-exclude' ).hasClass( 't42-active' ) ) { dateTime = 2; }
        
        let dateTimeStart = '';
        let dateTimeEnd = '';
        if( dateTime ) {
            dateTimeStart = $( '#t42-assign-box .t42-date-time input.t42-period-picker-start' ).val();
            dateTimeEnd = $( '#t42-assign-box .t42-date-time input.t42-period-picker-end' ).val();
        }
        
        // User Roles
        let userRoles = 0;
        if($("#t42-assign-box .t42-user-roles .t42-button-group .t42-ignore").hasClass("t42-active")) { userRoles = 0; }
        if($("#t42-assign-box .t42-user-roles .t42-button-group .t42-include").hasClass("t42-active")) { userRoles = 1; }
        if($("#t42-assign-box .t42-user-roles .t42-button-group .t42-exclude").hasClass("t42-active")) { userRoles = 2; }
        
        let userRolesVal = '';
        if(userRoles){
            userRolesVal = $("#t42-assign-box .t42-user-roles select.user-roles").val();
        }
        
        // URL
        let URL = 0;
        if($("#t42-assign-box .t42-url .t42-button-group .t42-ignore").hasClass("t42-active")) { URL = 0; }
        if($("#t42-assign-box .t42-url .t42-button-group .t42-include").hasClass("t42-active")) { URL = 1; }
        if($("#t42-assign-box .t42-url .t42-button-group .t42-exclude").hasClass("t42-active")) { URL = 2; }
        
        let URLVal = '';
        if(URL){
            URLVal = $("#t42-assign-box .t42-url textarea.t42-url-field").val();
        }
        
        /** Devices. */
        let devices = 0;
        if ( $( '#t42-assign-box .t42-devices .t42-button-group .t42-ignore' ).hasClass( 't42-active' ) ) { devices = 0; }
        if ( $( '#t42-assign-box .t42-devices .t42-button-group .t42-include' ).hasClass( 't42-active' ) ) { devices = 1; }
        if ( $( '#t42-assign-box .t42-devices .t42-button-group .t42-exclude' ).hasClass( 't42-active' ) ) { devices = 2; }
        
        let devicesVal = '';
        if( devices ){
            devicesVal = $( '#t42-assign-box .t42-devices select.devices' ).val();
        }
        
        /** Custom PHP. */
        let PHP = 0;
        if( $( '#t42-assign-box .t42-php .t42-button-group .t42-ignore' ).hasClass( 't42-active' ) ) { PHP = 0; }
        if( $( '#t42-assign-box .t42-php .t42-button-group .t42-include' ).hasClass( 't42-active' ) ) { PHP = 1; }
        if( $( '#t42-assign-box .t42-php .t42-button-group .t42-exclude' ).hasClass( 't42-active' ) ) { PHP = 2; }
        
        let PHPVal = '';
        if( PHP ) {
            PHPVal = $( '#t42-assign-box .t42-php textarea.t42-php-field' ).val();
        }
        
        let aConf = {
            matchingMethod: matchingMethod,
            WPContent: WPContent,
            WPContentVal: WPContentVal,
            homePage: homePage,
            menuItems: menuItems,
            menuItemsVal: menuItemsVal,
            dateTime: dateTime,
            dateTimeStart: dateTimeStart,
            dateTimeEnd: dateTimeEnd,
            userRoles: userRoles,
            userRolesVal: userRolesVal,
            URL: URL,
            URLVal: URLVal,
            devices: devices,
            devicesVal: devicesVal,
            PHP: PHP,
            PHPVal: PHPVal
        };

        let aConfJson = JSON.stringify(aConf);
        aConfJson = aConfJson.replace(/"/g, '|');// Input truncate quotes, so made some replacements

        $assignInput.val( aConfJson );// Set setting to input
        $assignInput.change();

    }
    
    /**
     * Initialization.
     **/
    function AssignmentsInitialization() {
        
        /** Periodpicker. */
        $( '#t42-assign-box .t42-date-time .t42-period-picker-start' ).periodpicker( {
            end: '#t42-period-picker-end',
            todayButton: true,
            formatDate: 'D.MM.YYYY',
            timepicker: true,
            timepickerOptions: {
                twelveHoursFormat: false,
                hours: true,
                minutes: true,
                seconds: false,
                ampm: false
            }
        } );
        
        /** Show/Hide unused controls. */
        $( '#t42-assign-box .t42-button.t42-active' ).click();

        /** Disable Save button. */
        $( '.t42-save-settings-btn' ).prop('disabled', true ).removeClass( 't42-unsaved' );
        
    }
    
    /**
     * Matching Method click.
     **/
    $("#t42-assign-box .t42-matchingMethod .t42-button").on( 'click', function ( e ) {
        e.preventDefault();
        $("#t42-assign-box .t42-matchingMethod button").removeClass("t42-active");
        $(this).addClass("t42-active");
        SaveAssignments();
    });
    
    /**
     * Menu Items Ignore click.
     **/
    $("#t42-assign-box .t42-menu-items .t42-button-group .t42-ignore").on( 'click', function (e){
        e.preventDefault();
        $("#t42-assign-box .t42-menu-items button").removeClass("t42-active");
        $(this).addClass("t42-active");
        $("#t42-assign-box .t42-menu-items .t42-menuitems-selection").hide(200);
        $(this).closest(".t42-menu-items").removeClass("t42-red t42-green");
        SaveAssignments();
    });
    
    /**
     * Menu Items Include click.
     **/
    $("#t42-assign-box .t42-menu-items .t42-button-group .t42-include").on( 'click', function (e){
        e.preventDefault();
        $("#t42-assign-box .t42-menu-items button").removeClass("t42-active");
        $(this).addClass("t42-active");
        $("#t42-assign-box .t42-menu-items .t42-menuitems-selection").show(200);
        $(this).closest(".t42-menu-items").removeClass("t42-red").addClass("t42-green");
        SaveAssignments();
    });
    
    /**
     * Menu Items Exclude click.
     **/
    $("#t42-assign-box .t42-menu-items .t42-button-group .t42-exclude").on( 'click', function (e){
        e.preventDefault();
        $("#t42-assign-box .t42-menu-items button").removeClass("t42-active");
        $(this).addClass("t42-active");
        $("#t42-assign-box .t42-menu-items .t42-menuitems-selection").show(200);
        $(this).closest(".t42-menu-items").removeClass("t42-green").addClass("t42-red");
        SaveAssignments();
    });
    
    /**
     * Date & Time IGNORE click
     **/
    $( '#t42-assign-box .t42-date-time .t42-button-group .t42-ignore' ).on( 'click', function (e){
        e.preventDefault();
        $( '#t42-assign-box .t42-date-time button' ).removeClass( 't42-active' );
        $( this) .addClass( 't42-active' );
        $( this ).closest( '.t42-date-time' ).removeClass( 't42-green t42-red' );
        $( '#t42-assign-box .t42-date-time .t42-period-picker-box' ).hide( 200 );
        SaveAssignments();
    } );
    
    /**
     * Date & Time INCLUDE click
     **/
    $( '#t42-assign-box .t42-date-time .t42-button-group .t42-include' ).on( 'click', function (e){
        e.preventDefault();
        $( '#t42-assign-box .t42-date-time button' ).removeClass( 't42-active' );
        $( this) .addClass( 't42-active' );
        $( this ).closest( '.t42-date-time' ).removeClass( 't42-red' ).addClass( 't42-green' );
        $( '#t42-assign-box .t42-date-time .t42-period-picker-box' ).show( 200 );
        SaveAssignments();
    } );
    
    /**
     * Date & Time EXCLUDE click
     **/
    $( '#t42-assign-box .t42-date-time .t42-button-group .t42-exclude' ).on( 'click', function (e){
        e.preventDefault();
        $( '#t42-assign-box .t42-date-time button' ).removeClass( 't42-active' );
        $( this) .addClass( 't42-active' );
        $( this ).closest( '.t42-date-time' ).removeClass( 't42-green' ).addClass( 't42-red' );
        $( '#t42-assign-box .t42-date-time .t42-period-picker-box' ).show( 200 );
        SaveAssignments();
    } );
    
    /**
     * User Roles Ignore click.
     **/
    $("#t42-assign-box .t42-user-roles .t42-button-group .t42-ignore").on( 'click', function (e){
        e.preventDefault();
        $("#t42-assign-box .t42-user-roles button").removeClass("t42-active");
        $(this).addClass("t42-active");
        $("#t42-assign-box .t42-user-roles .user-roles-box").hide(200);
        $(this).closest(".t42-user-roles").removeClass("t42-red t42-green");
        SaveAssignments();
    });
    
    /**
     * User Roles Include click.
     **/
    $("#t42-assign-box .t42-user-roles .t42-button-group .t42-include").on( 'click', function (e){
        e.preventDefault();
        $("#t42-assign-box .t42-user-roles button").removeClass("t42-active");
        $(this).addClass("t42-active");
        $("#t42-assign-box .t42-user-roles .user-roles-box").show(200);
        $(this).closest(".t42-user-roles").removeClass("t42-red").addClass("t42-green");
        SaveAssignments();
    });
    
    /**
     * User Roles Exclude click.
     **/
    $("#t42-assign-box .t42-user-roles .t42-button-group .t42-exclude").on( 'click', function (e){
        e.preventDefault();
        $("#t42-assign-box .t42-user-roles button").removeClass("t42-active");
        $(this).addClass("t42-active");
        $("#t42-assign-box .t42-user-roles .user-roles-box").show(200);
        $(this).closest(".t42-user-roles").removeClass("t42-green").addClass("t42-red");
        SaveAssignments();
    });
    
    /**
     * Home Page IGNORE click.
     **/
    $("#t42-assign-box .t42-home-page .t42-button-group .t42-ignore").on( 'click', function (e){
        e.preventDefault();
        $("#t42-assign-box .t42-home-page .t42-button-group button").removeClass("t42-active");
        $(this).addClass("t42-active");
        $(this).closest(".t42-home-page").removeClass("t42-green t42-red");
        SaveAssignments();
    });
    
    /**
     * Home Page INCLUDE click.
     **/
    $("#t42-assign-box .t42-home-page .t42-button-group .t42-include").on( 'click', function (e){
        e.preventDefault();
        $("#t42-assign-box .t42-home-page .t42-button-group button").removeClass("t42-active");
        $(this).addClass("t42-active");
        $(this).closest(".t42-home-page").removeClass("t42-red").addClass("t42-green");
        SaveAssignments();
    });
    
    /**
     * Home Page EXCLUDE click.
     **/
    $("#t42-assign-box .t42-home-page .t42-button-group .t42-exclude").on( 'click', function (e){
        e.preventDefault();
        $("#t42-assign-box .t42-home-page .t42-button-group button").removeClass("t42-active");
        $(this).addClass("t42-active");
        $(this).closest(".t42-home-page").removeClass("t42-green").addClass("t42-red");
        SaveAssignments();
    });
       
    /**
     * URL IGNORE click.
     **/
    $("#t42-assign-box .t42-url .t42-button-group .t42-ignore").on( 'click', function (e){
        e.preventDefault();
        $("#t42-assign-box .t42-url button").removeClass("t42-active");
        $(this).addClass("t42-active");
        $(this).closest(".t42-url").removeClass("t42-green t42-red");
        $("#t42-assign-box .t42-url .t42-url-box").hide(200);
        SaveAssignments();
    });
    
    /**
     * URL INCLUDE click.
     **/
    $("#t42-assign-box .t42-url .t42-button-group .t42-include").on( 'click', function (e){
        e.preventDefault();
        $("#t42-assign-box .t42-url button").removeClass("t42-active");
        $(this).addClass("t42-active");
        $(this).closest(".t42-url").removeClass("t42-red").addClass("t42-green");
        $("#t42-assign-box .t42-url .t42-url-box").show(200);
        SaveAssignments();
    });
    
    /**
     * URL EXCLUDE click.
     **/
    $("#t42-assign-box .t42-url .t42-button-group .t42-exclude").on( 'click', function (e){
        e.preventDefault();
        $("#t42-assign-box .t42-url button").removeClass("t42-active");
        $(this).addClass("t42-active");
        $(this).closest(".t42-url").removeClass("t42-green").addClass("t42-red");
        $("#t42-assign-box .t42-url .t42-url-box").show(200);
        SaveAssignments();
    });
    
    /**
     * WordPress Content IGNORE click.
     **/
    $("#t42-assign-box .t42-wp-content .t42-button-group .t42-ignore").on( 'click', function (e){
        e.preventDefault();
        $("#t42-assign-box .t42-wp-content button").removeClass("t42-active");
        $(this).addClass("t42-active");
        $(this).closest(".t42-wp-content").removeClass("t42-green t42-red");
        $("#t42-assign-box .t42-wp-content .t42-wp-content-box").hide(200);
        SaveAssignments();
    });
    
    /**
     * WordPress Content INCLUDE click.
     **/
    $("#t42-assign-box .t42-wp-content .t42-button-group .t42-include").on( 'click', function (e){
        e.preventDefault();
        $("#t42-assign-box .t42-wp-content button").removeClass("t42-active");
        $(this).addClass("t42-active");
        $(this).closest(".t42-wp-content").removeClass("t42-red").addClass("t42-green");
        $("#t42-assign-box .t42-wp-content .t42-wp-content-box").show(200);
        SaveAssignments();
    });
    
    /**
     * WordPress Content EXCLUDE click.
     **/
    $("#t42-assign-box .t42-wp-content .t42-button-group .t42-exclude").on( 'click', function (e){
        e.preventDefault();
        $("#t42-assign-box .t42-wp-content button").removeClass("t42-active");
        $(this).addClass("t42-active");
        $(this).closest(".t42-wp-content").removeClass("t42-green").addClass("t42-red");
        $("#t42-assign-box .t42-wp-content .t42-wp-content-box").show(200);
        SaveAssignments();
    });
    
    /**
     * Devices Ignore click.
     **/
    $( '#t42-assign-box .t42-devices .t42-button-group .t42-ignore' ).on( 'click', function ( e ) {
        e.preventDefault();
        $( '#t42-assign-box .t42-devices button' ).removeClass( 't42-active' );
        $( this ).addClass( 't42-active' );
        $( '#t42-assign-box .t42-devices .t42-devices-box' ).hide( 200 );
        $( this ).closest( '.t42-devices' ).removeClass( 't42-red t42-green' );
        SaveAssignments();
    });
    
    /**
     * Devices Include click.
     **/
    $( '#t42-assign-box .t42-devices .t42-button-group .t42-include' ).on( 'click', function ( e ) {
        e.preventDefault();
        $( '#t42-assign-box .t42-devices button' ).removeClass( 't42-active' );
        $( this ).addClass( 't42-active' );
        $( '#t42-assign-box .t42-devices .t42-devices-box' ).show( 200 );
        $( this ).closest( '.t42-devices' ).removeClass( 't42-red' ).addClass( 't42-green' );
        SaveAssignments();
    });
    
    /**
     * Devices Exclude click.
     **/
    $( '#t42-assign-box .t42-devices .t42-button-group .t42-exclude' ).on( 'click', function ( e ) {
        e.preventDefault();
        $( '#t42-assign-box .t42-devices button' ).removeClass( 't42-active' );
        $( this ).addClass( 't42-active' );
        $( '#t42-assign-box .t42-devices .t42-devices-box' ).show( 200 );
        $( this ).closest( '.t42-devices' ).removeClass( 't42-green' ).addClass( 't42-red' );
        SaveAssignments();
    });

    /**
     * Custom PHP IGNORE click.
     **/
    $( '#t42-assign-box .t42-php .t42-button-group .t42-ignore' ).on( 'click', function ( e ){
        e.preventDefault();
        $( '#t42-assign-box .t42-php button' ).removeClass( 't42-active' );
        $( this ).addClass( 't42-active' );
        $( this ).closest( '.t42-php' ).removeClass( 't42-green t42-red' );
        $( '#t42-assign-box .t42-php .t42-php-box' ).hide( 200 );
        SaveAssignments();
    });
    
    /**
     *  Custom PHP INCLUDE click.
     **/
    $( '#t42-assign-box .t42-php .t42-button-group .t42-include' ).on( 'click', function ( e ){
        e.preventDefault();
        $( '#t42-assign-box .t42-php button' ).removeClass( 't42-active' );
        $( this ).addClass( 't42-active' );
        $( this ).closest( '.t42-php' ).removeClass( 't42-red' ).addClass( 't42-green' );
        $( '#t42-assign-box .t42-php .t42-php-box' ).show( 200 );
        SaveAssignments();
    });
    
    /**
     * Custom PHP EXCLUDE click.
     **/
    $( '#t42-assign-box .t42-php .t42-button-group .t42-exclude' ).on( 'click', function ( e ){
        e.preventDefault();
        $( '#t42-assign-box .t42-php button' ).removeClass( 't42-active' );
        $( this ).addClass( 't42-active' );
        $( this ).closest( '.t42-php' ).removeClass( 't42-green' ).addClass( 't42-red' );
        $( '#t42-assign-box .t42-php .t42-php-box' ).show( 200 );
        SaveAssignments();
    });
    
    /**
     * Save value in field on change.
     **/
    $( 'select.wp-content, ' +
       'select.menuitems, ' +
       '.t42-period-picker-start,  ' +
       '.t42-period-picker-end,  ' +
       'select.user-roles,  ' +
       'textarea.t42-url-field,  ' +
       'select.devices,  ' +
       'textarea.t42-php-field' ).on( 'input propertychange', function () {
       SaveAssignments();
    } );

    $( document ).ready( function () {

        ReadAssignments();
        AssignmentsInitialization();

        /** Prepare settings for Code Editor. */
        let phpEditorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
        phpEditorSettings.codemirror = _.extend(
            {},
            phpEditorSettings.codemirror,
            {
                lineNumbers: true,
                mode: "application/x-httpd-php",
                indentUnit: 2,
                tabSize: 2,
                autoRefresh: true,
            }
        );

        /** PHP Code Editor. */
        let phpEditor = '';
        let phpTextArea = $( '#t42-php-field' );

        /** Initialize Code Editor. */
        if ( phpTextArea.length ) {

            // noinspection JSUnresolvedVariable
            phpEditor = wp.codeEditor.initialize( phpTextArea , phpEditorSettings );

        }

        /** Save from PHP editor to text area. */
         $( '#submit' ).on( 'click', function() {
             if ( phpEditor ) {
                 // noinspection JSUnresolvedVariable
                 phpEditor.codemirror.save(); // Save data from CodeEditor to textarea.
                 SaveAssignments();
             }
         } );

    } );

} ( jQuery ) );
