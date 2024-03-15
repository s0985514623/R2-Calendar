<?php

declare (strict_types = 1);

namespace J7\WpReactPlugin;

abstract class Utils {

	const APP_NAME    = 'R2 Calendar';
	const KEBAB       = 'r2-calendar';
	const SNAKE       = 'r2_calendar';
	const TEXT_DOMAIN = self::SNAKE;

	const BASE_URL    = '/';
	const RENDER_ID_1 = 'r2_calendar';
	const RENDER_ID_2 = 'r2_calendar_metabox';
	const API_TIMEOUT = '30000';

	const DEFAULT_IMAGE = 'http://1.gravatar.com/avatar/1c39955b5fe5ae1bf51a77642f052848?s=96&d=mm&r=g';
	const GITHUB_REPO   = 'https://github.com/j7-dev/wp-react-plugin';

	public static function get_plugin_dir(): string {
		$plugin_dir = \untrailingslashit( \wp_normalize_path( ABSPATH . 'wp-content/plugins/' . self::KEBAB ) );
		return $plugin_dir;
	}

	public static function get_plugin_url(): string {
		$plugin_url = \untrailingslashit( \plugin_dir_url( self::get_plugin_dir() . '/plugin.php' ) );
		return $plugin_url;
	}

	public static function get_plugin_ver(): string {
		$plugin_data = \get_plugin_data( self::get_plugin_dir() . '/plugin.php' );
		$plugin_ver  = $plugin_data['Version'];
		return $plugin_ver;
	}
}

require_once __DIR__ . '/includes.php';
