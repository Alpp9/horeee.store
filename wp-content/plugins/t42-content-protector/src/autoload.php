<?php
/**
 * Content Protector for WordPress. Exclusively on Envato Market: https://1.envato.market/42themeCC
 * @encoding     UTF-8
 * @version      1.0.0
 * @copyright    Copyright (C) 2016 - 2021 42Theme (https://42theme.com). All rights reserved.
 * @license      Envato Standard Licenses
 * @author       Alexander Khmelnitskiy
 * @support      support@42theme.com
 **/

/** Register Plugin Autoloader. */
spl_autoload_register( static function ( $class ) {

	$namespace = 'T42\\';

	/** Bail if the class is not in our namespace. */
	if ( 0 !== strpos( $class, $namespace ) ) { return; }

	/** Build the filename. */
	$dir = realpath( __DIR__ );

	/** Brevity is the Soul of Wit. */
	$DS = DIRECTORY_SEPARATOR;

	/** Classes from MP. */
	$mp_file = $dir . $DS . str_replace( '\\', $DS, 'MP' . $DS . substr( strrchr( $class, "\\" ), 1 ) ) . '.php';

	/** Other classes. */
	$p_file  = $dir . $DS . str_replace( '\\', $DS, str_replace( $namespace, '', $class ) ) . '.php';

	/** If the file exists for the class name, load it. */
	if ( file_exists( $mp_file ) ) {

		/** Firstly we load MP classes. */
		/** @noinspection PhpIncludeInspection */
		include_once( $mp_file );

	} elseif ( file_exists( $p_file ) ) {

		/** Secondly we load other classes. */
		/** @noinspection PhpIncludeInspection */
		include_once( $p_file );

	}

} );
