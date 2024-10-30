<?php
/**
 * Plugin Name: IQ Referral program for Woocommerce
 * Description: IQ Referral - affiliate program is the easiest and most effective way to increase your customer base by attracting them to your users through a unique referral link. Your partner will receive the percentage indicated by you from the purchase of the client he attracted, which in turn motivates him to promote your goods and services. The affiliate program works with WooCommerce and adds a separate menu under "My account". You can change the display style as you like, focusing on the design of your website
 
 * Plugin URI:  https://lumpx.com/wp-plugins/iq-referral-system
 * Author URI:  https://lumpx.com
 * Author:      LumpX
 * Network: false
 * Version:     1.0.3
 *
 * Text Domain: iq-referral-program-for-woocommerce
 * Domain Path: /lang
 * Requires at least: 5.2
 * Requires PHP: 5.4
 *
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * Network:    false

 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
**/

// Restrict
if(!defined('ABSPATH')) {
	die();
}

define('IQ_RPW_ACCESS', true );
define('IQ_RPW_PRO_VERSION', 0 );
define('IQ_RPW_CORE_URL', plugin_dir_url( __FILE__ ) );
define('IQ_RPW_CORE_DIR', plugin_dir_path( __FILE__ ) );
define('IQ_RPW_PLUGIN_TAG', 'iqref' ); // Do not change
define('IQ_RPW_MENU_TAG', 'iq-rp-' ); // Do not change
define('IQ_RPW_SECRET_CODE', '1W65px9lrORv36cPIlMRyLde0GsfksW6' );
define( 'IQ_RPW_PLUGIN_DIR', plugin_basename(dirname(__FILE__)) );

// WIZARD
define('IQ_RPW_WIZARD_PATH', IQ_RPW_CORE_DIR.'/wizard' );
define('IQ_RPW_WIZARD_URL', IQ_RPW_CORE_URL.'/wizard' );

if ( ! class_exists( 'IQ_RPW_CoreClass' ) ) {
	class IQ_RPW_CoreClass {
		function initialize() {
			add_action('admin_enqueue_scripts', array($this, 'IQ_RPW_enqueue_admin'));
			add_action('wp_enqueue_scripts', array($this, 'IQ_RPW_enqueue_front'));
			add_action('admin_menu', 'IQ_RPW_pre_create_menu');
			add_action( 'plugins_loaded', array($this, 'IQ_RPW_load_plugin_textdomain' ));
			add_action( 'init', array($this, 'iq_init' ));
			add_action( 'init', array($this, 'IQ_RPW_load_partner_info' ));
			add_action( 'init', array($this, 'IQ_RPW_load_ajax' ));
			add_action( 'user_register', array($this, 'func_iq_rpw_user_register'), 10, 1 );
			add_action('admin_init', array($this, 'IQ_RPFW_redirect'));

			require_once IQ_RPW_CORE_DIR.'/includes/inc_func.php';
			add_action( 'woocommerce_order_status_changed', 'func_iq_rpw_woocommerce_order_status_changed', 10, 1 );
			add_action( 'woocommerce_payment_complete', 'func_iq_rpw_action_payment_complete', 10, 1 );
		}

		function iq_init() {
			ob_start();

			$modules_arr = func_iq_rpw_get_modules();
			if($modules_arr) {
				for($i = 0; $i < count($modules_arr); $i++) {
					$name = $modules_arr[$i];
					$file = IQ_RPW_CORE_DIR.'/modules/'.$name.'/'.$name.'.php';
					if(file_exists($file)) {
						$type = 'define';
						include $file;
					}
				}
			}
		}

		function func_iq_rpw_user_register($user_id) {
			if(!isset($cIQ_RPW_ReferralClass)) {
				require_once IQ_RPW_CORE_DIR.'/includes/inc_referral_class.php';
				$cIQ_RPW_ReferralClass = new IQ_RPW_ReferralClass();
			}
			$cIQ_RPW_ReferralClass->createPartner($user_id);
		}

		function IQ_RPW_load_ajax() {
			$modules_arr = func_iq_rpw_get_modules();
			
			if(!isset($cIQ_RPW_AjaxClass)) {
				require_once IQ_RPW_CORE_DIR.'/includes/inc_ajax_class.php';
				$cIQ_RPW_AjaxClass = new IQ_RPW_AjaxClass();
			}
			
			// admin
			$names_admin = [
				'admin_change_status',
				'admin_system_index',
				'admin_WsStatusIndex',
				'admin_WsStatusDo',
				'admin_WsItemReload',
				'admin_system_add_do',
				'admin_system_delete',
				'admin_systems_load',
				'admin_ref_code_update',
				'admin_user_reload',
				'admin_BalanceUpdate',
				'admin_BalanceUpdateDo',
				
				'admin_WizardImportUsers',
				'admin_WizardSettingsApply',
				'admin_WizardWithdrawAddDo',
			];
			for($i = 0; $i < count($names_admin); $i++) {
				$tmp = trim($names_admin[$i]);
				if(empty($tmp)) {
					continue;
				}
				add_action( 'wp_ajax_'.$tmp, array($cIQ_RPW_AjaxClass, $tmp) );
			}
			
			if($modules_arr) {
				for($i = 0; $i < count($modules_arr); $i++) {
					$name = $modules_arr[$i];
					$file = IQ_RPW_CORE_DIR.'/modules/'.$name.'/'.$name.'.php';
					if(file_exists($file)) {
						$type = 'adm_ajax';
						include $file;
						
						$type = 'web_ajax';
						include $file;
					}
				}
			}
			
			// front
			$names_front = [
				'web_ShowForm',
			];
			for($i = 0; $i < count($names_front); $i++) {
				$tmp = trim($names_front[$i]);
				if(empty($tmp)) {
					continue;
				}
				add_action( 'wp_ajax_'.$tmp, array($cIQ_RPW_AjaxClass, $tmp) );
				add_action( 'wp_ajax_nopriv_'.$tmp, array($cIQ_RPW_AjaxClass, $tmp) );
			}
		}

		function IQ_RPW_load_partner_info() {
			if(isset($_GET['r'])) {
				$RefCode = sanitize_text_field($_GET['r']);

				if(!isset($cIQ_RPW_ReferralClass)) {
					require_once IQ_RPW_CORE_DIR.'/includes/inc_referral_class.php';
					$cIQ_RPW_ReferralClass = new IQ_RPW_ReferralClass();
				}
				$iRefFatherUID = $cIQ_RPW_ReferralClass->getFather($RefCode);

				if($iRefFatherUID) {
                    $cur_user_id = get_current_user_id();

                    if($cur_user_id != $iRefFatherUID) {
                        setcookie("ref_father", $iRefFatherUID, time() + (86400 * 30), "/");
                        $cIQ_RPW_ReferralClass->updateVisit($iRefFatherUID, 1);

                        if($cur_user_id) {
                            $cIQ_RPW_ReferralClass->setFather($cur_user_id, $iRefFatherUID);
                        }
                    }

					$url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . sanitize_text_field($_SERVER['HTTP_HOST']) . sanitize_text_field($_SERVER['REQUEST_URI']);
					$url = explode('?', $url);
					$url = $url[0];
					header('Location: '.esc_url($url));
					die();
				}
			}
            if(isset($_SERVER['REQUEST_URI'])) {
                $Path = sanitize_text_field($_SERVER['REQUEST_URI']);
                if (strpos($Path, '/my-account') !== false) {
                    include IQ_RPW_CORE_DIR.'/includes/inc_control.php';
                }
            }
		}

		static function IQ_RPW_activation() {
			if(!is_plugin_active('woocommerce/woocommerce.php')) {
				echo esc_html__('For the affiliate program to work, you need install and activated Woocommerce plugin', 'iq-referral-program-for-woocommerce');
				die();
			}
			
            func_iq_rpw_settings_createDB();
			flush_rewrite_rules();
 
			if(!isset($cIQ_RPW_ReferralClass)) {
				require_once IQ_RPW_CORE_DIR.'/includes/inc_referral_class.php';
				$cIQ_RPW_ReferralClass = new IQ_RPW_ReferralClass();
			}
			$result = $cIQ_RPW_ReferralClass->createDB();
			
			
			$modules_arr = func_iq_rpw_get_modules();
			if($modules_arr) {
				for($i = 0; $i < count($modules_arr); $i++) {
					$name = $modules_arr[$i];
					$file = IQ_RPW_CORE_DIR.'/modules/'.$name.'/'.$name.'.php';
					if(file_exists($file)) {
						$type = 'define';
						include $file;
						$type = 'activation';
						include $file;
					}
				}
			}
			
			add_option('iq-referral-program-for-woocommerce_do_activation_redirect', true);
		}
		
		function IQ_RPFW_redirect() {
			if (get_option('iq-referral-program-for-woocommerce_do_activation_redirect', false)) {
				delete_option('iq-referral-program-for-woocommerce_do_activation_redirect');
				wp_redirect( '/wp-admin/admin.php?page='.IQ_RPW_MENU_TAG.'wizard' );
			}
		}

		public static function IQ_RPW_deactivation() {
			flush_rewrite_rules();
		}

		public static function IQ_RPW_uninstall() {
			if(!isset($cIQ_RPW_ReferralClass)) {
				require_once IQ_RPW_CORE_DIR.'/includes/inc_referral_class.php';
				$cIQ_RPW_ReferralClass = new IQ_RPW_ReferralClass();
			}
			$cIQ_RPW_ReferralClass->removeDB();
		}

		function IQ_RPW_enqueue_front() {
			wp_enqueue_style('IQ_RPW_FrontViewStyle', IQ_RPW_CORE_URL.'/assets/front/css/styles.css', false, '1.0');
			wp_enqueue_script('IQ_RPW_FrontViewScript', IQ_RPW_CORE_URL.'/assets/front/js/scripts.js', false, '1.0');
			
			// localize
			wp_localize_script( 'IQ_RPW_FrontViewScript', 'iq_rpw_front_unique', array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
				)
			);
			
			// icofont
			wp_enqueue_style('IQ_RPW_FrontIconsViewScript', IQ_RPW_CORE_URL. '/assets/icofont/icofont.min.css', false, '1.0');
			
			wp_enqueue_style('IQ_RPW_FrontPopStyle', IQ_RPW_CORE_URL.'/assets/front/css/pop.css', false, '1.0');
			wp_enqueue_script('IQ_RPW_FrontPopScript', IQ_RPW_CORE_URL.'/assets/front/js/pop.js', false, '1.0');
		}

		function IQ_RPW_enqueue_admin() {
			wp_enqueue_style('IQ_RPW_AdminViewStyle', IQ_RPW_CORE_URL.'/assets/admin/css/styles.css', false, '1.1');
			wp_enqueue_style('IQ_RPW_AdminLoaderStyle', IQ_RPW_CORE_URL.'/assets/admin/css/loader.css', false, '1.0');
			wp_enqueue_script('IQ_RPW_AdminViewScript', IQ_RPW_CORE_URL.'/assets/admin/js/scripts.js', false, '1.0');
			
			// localize
			wp_localize_script( 'IQ_RPW_AdminViewScript', 'iq_rpw_unique', array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
				)
			);
			

			wp_enqueue_style('IQ_RPW_AdminPopStyle', IQ_RPW_CORE_URL.'/assets/admin/css/pop.css', false, '1.0');
			wp_enqueue_script('IQ_RPW_AdminPopScript', IQ_RPW_CORE_URL.'/assets/admin/js/pop.js', false, '1.0');
			
			// icofont
			wp_enqueue_style('IQ_RPW_AdminIconsViewScript', IQ_RPW_CORE_URL. '/assets/icofont/icofont.min.css', false, '1.0');
			
			// WIZARD
			wp_enqueue_style('iq_referral_AdminIQ_RPW_WizardLoaderStyle', IQ_RPW_WIZARD_URL.'/assets/css/wizard_loader.css', false, '1.0');
			wp_enqueue_style('iq_referral_AdminWizardStyle', IQ_RPW_WIZARD_URL.'/assets/css/wizard.css', false, '1.0');
			wp_enqueue_script('iq_referral_AdminWizardScript', IQ_RPW_WIZARD_URL.'/assets/js/wizard.js', false, '1.0');
			
			// localize
			wp_localize_script( 'iq_referral_AdminWizardScript', 'iq_rpw_wizard_unique', array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
				)
			);
			
			$modules_arr = func_iq_rpw_get_modules();
			if($modules_arr) {
				for($i = 0; $i < count($modules_arr); $i++) {
					$name = $modules_arr[$i];
					$file = IQ_RPW_CORE_DIR.'/modules/'.$name.'/'.$name.'.php';
					if(file_exists($file)) {
						$type = 'enqueue';
						include $file;
					}
				}
			}
		}
		function IQ_RPW_load_plugin_textdomain() {
			load_plugin_textdomain( 'iq-referral-program-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ). '/lang/' );
		}
	}
}

register_activation_hook( __FILE__, array( 'IQ_RPW_CoreClass', 'IQ_RPW_activation' ) );
register_deactivation_hook( __FILE__, array( 'IQ_RPW_CoreClass', 'IQ_RPW_deactivation' ) );
register_uninstall_hook( __FILE__, array( 'IQ_RPW_CoreClass', 'IQ_RPW_uninstall' ) );

$cIQ_RPW_CoreClass = new IQ_RPW_CoreClass();
$cIQ_RPW_CoreClass->initialize();