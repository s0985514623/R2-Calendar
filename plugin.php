<?php

/**
 * Plugin Name:       R2 Calendar
 * Plugin URI:        https://github.com/s0985514623/R2-Calendar
 * Description:       建立一個行事曆系統.
 * Version:           1.0.0
 * Requires at least: 5.7
 * Requires PHP:      7.4
 * Author:            J7
 * Author URI:        https://github.com/s0985514623
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Tags: vite, react, tailwind, typescript, react-query, scss, WordPress, WordPress plugin
 *
 * @package J7\WpReactPlugin
 */

declare(strict_types=1);

namespace J7\WpReactPlugin;

use J7\WpReactPlugin\Utils;
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

/**
 * Class Plugin
 */
if ( ! \class_exists( 'J7\WpReactPlugin\Plugin' ) ) {
	/**
	 * Undocumented class
	 */
	final class Plugin {

		/**
		 * Instance of the class.
		 *
		 * @var self
		 */
		private static $instance;
		/**
		 * Flag indicating if all plugins are activated.
		 *
		 * @var bool
		 */
		public static $is_all_plugins_activated = false;
		// Const WOOCOMMERCE_CLASS                 = 'WooCommerce';.
		// Const WP_TOOLKIT_CLASS                  = 'J7\WpToolkit\Plugin';.

		/**
		 * Construct function
		 */
		public function __construct() {
			require_once __DIR__ . '/required_plugins/index.php';
			require_once __DIR__ . '/vendor/autoload.php';
			require_once __DIR__ . '/inc/utils/index.php';
			require_once __DIR__ . '/inc/class/index.php';

			\register_activation_hook( __FILE__, array( $this, 'activate' ) );
			\register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
			\add_action( 'tgmpa_register', array( $this, 'register_required_plugins' ) );
			\add_action( 'plugins_loaded', array( $this, 'check_required_plugins' ) );

			$this->plugin_update_checker();
		}
		/**
		 * Check required plugins function
		 *
		 * @return void
		 */
		public function check_required_plugins() {
			// if your plugin is dependent on other plugins, you can check if they are activated.
			// self::$is_all_plugins_activated = \class_exists(self::WOOCOMMERCE_CLASS) && \class_exists(self::WP_TOOLKIT_CLASS).
			self::$is_all_plugins_activated = true;

			if ( self::$is_all_plugins_activated ) {
				new Bootstrap();
				\add_action( 'init', array( $this, 'remove_notices' ), 20 );
			}
		}
		/**
		 * Instance function
		 *
		 * @return self::$instance
		 */
		public static function instance() {
			if ( empty( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Wp plugin update checker integrated with github
		 */
		public function plugin_update_checker(): void {
			$update_checker = PucFactory::buildUpdateChecker(
				Utils::GITHUB_REPO,
				__FILE__,
				Utils::KEBAB
			);
			$update_checker->setBranch( 'master' );
			// if your repo is private, you need to set authentication.
			// $Update_checker->setAuthentication(Utils::GITHUB_PAT).
			$update_checker->getVcsApi()->enableReleaseAssets();
		}
		/**
		 * Register required plugins function
		 */
		public function register_required_plugins(): void {
			/**
			 * If your plugin is dependent on other plugins, you can use this hook to notify the user
			 *
			 * @see http://tgmpluginactivation.com/
			 */
			$plugins = array(
				// [
				// 'name'     => 'WooCommerce',
				// 'slug'     => 'woocommerce',
				// 'required' => true,
				// 'version'  => '7.6.1',
				// ],
				// [
				// 'name'     => 'WP Toolkit',
				// 'slug'     => 'wp-toolkit',
				// 'source'   => 'https://github.com/j7-dev/wp-toolkit/releases/latest/download/wp-toolkit.zip',
				// 'required' => true,
				// ],
			);

			$config = array(
				'id'           => Utils::KEBAB, // Unique ID for hashing notices for multiple instances of TGMPA.
				'default_path' => '', // Default absolute path to bundled plugins.
				'menu'         => 'tgmpa-install-plugins', // Menu slug.
				'parent_slug'  => 'plugins.php', // Parent menu slug.
				'capability'   => 'manage_options', // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
				'has_notices'  => true, // Show admin notices or not.
				'dismissable'  => false, // If false, a user cannot dismiss the nag message.
				'dismiss_msg'  => __( '這個訊息將在依賴套件被安裝並啟用後消失。' . Utils::APP_NAME . ' 沒有這些依賴套件的情況下將無法運作！', Utils::TEXT_DOMAIN ), // If 'dismissable' is false, this message will be output at top of nag.
				'is_automatic' => true, // Automatically activate plugins after installation or not.
				'message'      => '', // Message to output right before the plugins table.
				'strings'      => array(
					'page_title'                      => __( '安裝依賴套件', Utils::TEXT_DOMAIN ),
					'menu_title'                      => __( '安裝依賴套件', Utils::TEXT_DOMAIN ),
					'installing'                      => __( '安裝套件: %s', Utils::TEXT_DOMAIN ), // translators: %s: plugin name.
					'updating'                        => __( '更新套件: %s', Utils::TEXT_DOMAIN ), // translators: %s: plugin name.
					'oops'                            => __( 'OOPS! plugin API 出錯了', Utils::TEXT_DOMAIN ),
					'notice_can_install_required'     => _n_noop(
						// translators: 1: plugin name(s).
						Utils::APP_NAME . ' 依賴套件: %1$s.',
						Utils::APP_NAME . ' 依賴套件: %1$s.',
						Utils::TEXT_DOMAIN
					),
					'notice_can_install_recommended'  => _n_noop(
						// translators: 1: plugin name(s).
						Utils::APP_NAME . ' 推薦套件: %1$s.',
						Utils::APP_NAME . ' 推薦套件: %1$s.',
						Utils::TEXT_DOMAIN
					),
					'notice_ask_to_update'            => _n_noop(
						// translators: 1: plugin name(s).
						'以下套件需要更新到最新版本來兼容 ' . Utils::APP_NAME . ': %1$s.',
						'以下套件需要更新到最新版本來兼容 ' . Utils::APP_NAME . ': %1$s.',
						Utils::TEXT_DOMAIN
					),
					'notice_ask_to_update_maybe'      => _n_noop(
						// translators: 1: plugin name(s).
						'以下套件有更新: %1$s.',
						'以下套件有更新: %1$s.',
						Utils::TEXT_DOMAIN
					),
					'notice_can_activate_required'    => _n_noop(
						// translators: 1: plugin name(s).
						'以下依賴套件目前為停用狀態: %1$s.',
						'以下依賴套件目前為停用狀態: %1$s.',
						Utils::TEXT_DOMAIN
					),
					'notice_can_activate_recommended' => _n_noop(
						// translators: 1: plugin name(s).
						'以下推薦套件目前為停用狀態: %1$s.',
						'以下推薦套件目前為停用狀態: %1$s.',
						Utils::TEXT_DOMAIN
					),
					'install_link'                    => _n_noop(
						'安裝套件',
						'安裝套件',
						Utils::TEXT_DOMAIN
					),
					'update_link'                     => _n_noop(
						'更新套件',
						'更新套件',
						Utils::TEXT_DOMAIN
					),
					'activate_link'                   => _n_noop(
						'啟用套件',
						'啟用套件',
						Utils::TEXT_DOMAIN
					),
					'return'                          => __( '回到安裝依賴套件', Utils::TEXT_DOMAIN ),
					'plugin_activated'                => __( '套件啟用成功', Utils::TEXT_DOMAIN ),
					'activated_successfully'          => __( '以下套件已成功啟用:', Utils::TEXT_DOMAIN ),
					// translators: 1: plugin name.
					'plugin_already_active'           => __( '沒有執行任何動作 %1$s 已啟用', Utils::TEXT_DOMAIN ),
					// translators: 1: plugin name.
					'plugin_needs_higher_version'     => __( Utils::APP_NAME . ' 未啟用。' . Utils::APP_NAME . ' 需要新版本的 %s 。請更新套件。', Utils::TEXT_DOMAIN ),
					// translators: 1: dashboard link.
					'complete'                        => __( '所有套件已成功安裝跟啟用 %1$s', Utils::TEXT_DOMAIN ),
					'dismiss'                         => __( '關閉通知', Utils::TEXT_DOMAIN ),
					'notice_cannot_install_activate'  => __( '有一個或以上的依賴/推薦套件需要安裝/更新/啟用', Utils::TEXT_DOMAIN ),
					'contact_admin'                   => __( '請聯繫網站管理員', Utils::TEXT_DOMAIN ),

					'nag_type'                        => 'error', // Determines admin notice type - can only be one of the typical WP notice classes, such as 'updated', 'update-nag', 'notice-warning', 'notice-info' or 'error'. Some of which may not work as expected in older WP versions.
				),
			);

			\tgmpa( $plugins, $config );
		}

		public function remove_notices(): void {
			\remove_action( 'admin_notices', array( \TGM_Plugin_Activation::$instance, 'notices' ) );
		}

		public function activate(): void {
		}

		public function deactivate(): void {
			// 刪除會員等級 post type 或是 transient
		}
	}

	Plugin::instance();
}
