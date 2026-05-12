<?php

namespace WooDiviExtended\Divi5;

use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;

class DemoModule {
	public static $registered = false;

	public static function load() {
		if ( self::$registered ) {
			return;
		}

		$module_path = WOO_DIVI_EXTENDED_PATH . 'modules/d5-demo-module';

		if ( class_exists( ModuleRegistration::class ) ) {
			ModuleRegistration::register_module( $module_path, [] );
			self::$registered = true;
		}
	}
}
