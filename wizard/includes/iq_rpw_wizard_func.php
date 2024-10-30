<?php
// Restrict
if(!defined('ABSPATH') || !defined('IQ_RPW_WIZARD_ACCESS')) {
	die();
}

if(!class_exists('WizardClass')) {
	class WizardClass {
		/**
		 * Import users in partners
		 * @since 1.0.0
		 * @return boolean
		 */
		function updateUsers() {
			global $wpdb;

			$args = [];
			$usersObj = get_users( $args );
			if($usersObj) {
				$usersArr = (array)$usersObj;
				if($usersArr) {
					// get settings
					if(!isset($cIQ_RPW_ReferralClass)) {
						require_once IQ_RPW_CORE_DIR.'/includes/inc_referral_class.php';
						$cIQ_RPW_ReferralClass = new IQ_RPW_ReferralClass();
					}
					$settings_arr = $cIQ_RPW_ReferralClass->get_settings();
					
					if(!isset($settings_arr['status_def'])) {
						$settings_arr['status_def'] = 1;
					}
					if(!isset($settings_arr['percent_def'])) {
						$settings_arr['percent_def'] = 0;
					}

					foreach($usersArr AS $user_obj) {
						$user_data = (array)$user_obj->data;

						$sql = "
						SELECT 
							`uid`
						FROM
							`{$wpdb->prefix}iq_rpw_users`
						WHERE
							`uid` = %d
					";
						$object = $wpdb->get_row(
							$wpdb->prepare($sql,
								$user_data['ID'])
							, ARRAY_A);

						$balance_arr = [];
						$woo_curr_p = func_iq_rpw_in_protect(get_woocommerce_currency_symbol(), 'curr');
						$balance_arr[$woo_curr_p] = 0;
						
						if(!$object) {
							$ref_code = $user_data['ID'].func_iq_rpw_generatePassword(8);
							$sql = "
								INSERT INTO `{$wpdb->prefix}iq_rpw_users`
									(`uid`,
									`ref_uid`,
									`ref_visits`,
									`ref_balance_json`,
									`ref_shopping`,
									`ref_percent`,
									`ref_code`,
									`ref_enable`)
								VALUES
									(%d,
									0,
									0,
									%s,
									0,
									%d,
									%s,
									%d)
							";
							$wpdb->query($wpdb->prepare($sql,
								$user_data['ID'],
								json_encode($balance_arr),
								$settings_arr['percent_def'],
								$ref_code,
								$settings_arr['status_def']));
						}
					}
				}
			}
			return true;
		}
	}
}