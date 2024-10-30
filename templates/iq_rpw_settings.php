<?php
// Restrict
if(!defined('ABSPATH') || !defined('IQ_RPW_CORE_DIR')) {
	die();
}
if (!function_exists('is_plugin_active')) {
	include_once(ABSPATH . 'wp-admin/includes/plugin.php');
}
if(!isset($cIQ_RPW_ReferralClass)) {
	require_once IQ_RPW_CORE_DIR.'/includes/inc_referral_class.php';
	$cIQ_RPW_ReferralClass = new IQ_RPW_ReferralClass();
}
$settings_arr = $cIQ_RPW_ReferralClass->get_settings();

$iMaxLengthNameWS = 64;
$iMaxLengthMinWS = 6;
?> 
<div class="iq_ref_core_block">
    <div class="iq_ref_list_block iq_ref_hided">
        <h2 class="iq_ref_head_block">
            <?php echo esc_html__('Settings', 'iq-referral-program-for-woocommerce'); ?>
        </h2>
		
        <?php
		$szNoticeMsg = '';
		$szErr = '';
		$bErr = false;
		
		if(!is_plugin_active('woocommerce/woocommerce.php')) {
			?>
			<div class="iq_ref_alert_block iq_ref_err">
				<?php echo esc_html__('For the affiliate program to work, you need install and activated Woocommerce plugin', 'iq-referral-program-for-woocommerce'); ?>
			</div>
			<?php
		} else {
			if(isset($_POST['form_save'])) {
				// status_def
				$settings_arr['status_def'] = 1;
				if(isset($_POST['status_def']) && (int)sanitize_text_field($_POST['status_def']) == 0) {
					$settings_arr['status_def'] = 0;
				}

				// percent_def
				$settings_arr['percent_def'] = 0;
				if(isset($_POST['percent_def'])) {
					$settings_arr['percent_def'] = (int)sanitize_text_field($_POST['percent_def']);
					if($settings_arr['percent_def'] < 0) {
						$settings_arr['percent_def'] = 0;
					}
					else if($settings_arr['percent_def'] > 100) {
						$settings_arr['percent_def'] = 100;
					}
				}
				
				if(func_iq_rpw_module_enable('pyramid')) {
					include IQ_RPW_PYRAMID_DIR.'/data/data_pyramid_settings_save.php';
				}

				if(!$bErr) {
					$bSave = $cIQ_RPW_ReferralClass->updateSettings($settings_arr);
					if(!$bSave) {
						$szErr = esc_html__("An error has occurred", "iq-referral-program-for-woocommerce");
						$bErr = true;
					} else {
						// Action logs
						if(!isset($cur_user_id)) {
							$cur_user_id = get_current_user_id();
						}
						$log = esc_html__("Admin save new settings", "iq-referral-program-for-woocommerce");
						$log .= ': '.json_encode($settings_arr, JSON_UNESCAPED_UNICODE);
						$cIQ_RPW_ReferralClass->writeActionsLogs($cur_user_id, $log);
					}
				}
				
				if($bErr) {
					if(empty($szErr)) {
						$szNoticeMsg = esc_html__("Error", "iq-referral-program-for-woocommerce");
					}
					$szNoticeMsg = $szErr;
				} else {
					$szNoticeMsg = esc_html__("Settings saved successfully", "iq-referral-program-for-woocommerce");
				}
			}
        }
        ?>
		<?php
		if($szNoticeMsg) {
			if($bErr) {
				?>
				<div class="iq_ref_alert_block iq_ref_err">
					<?php echo esc_html($szNoticeMsg); ?>
				</div>
				<?php
			} else {
				?>
				<div class="iq_ref_alert_block iq_ref_ok">
					<?php echo esc_html($szNoticeMsg); ?>
				</div>
				<?php
			}
		}
		?>
		<div id="notice_block"></div>
    </div>
	
	<div class="iq_ref_flexbox_tab iq_ref_flex_gap">

		<div class="iq_ref_list_block iq_ref_flex_300">
			<form  method="POST">
			<input type="hidden" name="form_save" value="1">
				<h2 class="iq_ref_flextbl">
					<?php echo esc_html__('Settings for a new partner', 'iq-referral-program-for-woocommerce'); ?>
				</h2>
				<div class="iq_ref_atxt iq_ref_align_center iq_ref_mb15">
					<?php echo esc_html__('When registering or adding a new user, he automatically enters the IQ Referral system. Set the default settings for the new user', 'iq-referral-program-for-woocommerce'); ?>
				</div>
				<ul class="iq_ref_ulcl">
					<li class="iq_ref_li">
						<div class="iq_ref_li_head">
							<?php echo esc_html__('Default status', 'iq-referral-program-for-woocommerce'); ?>
						</div>
						<?php
						$szDef0 = '';
						$szDef1 = 'selected';
						if($settings_arr && isset($settings_arr['status_def'])) {
							if($settings_arr['status_def']) {
								$szDef0 = '';
								$szDef1 = 'selected';
							} else {
								$szDef0 = 'selected';
								$szDef1 = '';
							}
						}
						?>
						<select name="status_def" id="status_def" class="select iq_ref_select_default block_centered">
							<option value="0" <?php echo esc_attr($szDef0); ?>>
								<?php echo esc_html__('Off', 'iq-referral-program-for-woocommerce'); ?>
							</option>
							<option value="1" <?php echo esc_attr($szDef1); ?>>
								<?php echo esc_html__('On', 'iq-referral-program-for-woocommerce'); ?>
							</option>
						</select>
					</li>
					<li class="iq_ref_li">
						<div class="iq_ref_li_head">
							<?php echo esc_html__('Default percent', 'iq-referral-program-for-woocommerce'); ?>
						</div>
						<?php
						$iDef = 0;
						if($settings_arr && isset($settings_arr['percent_def'])) {
							$iDef = (int)$settings_arr['percent_def'];
						}
						?>
						<select name="percent_def" id="percent_def" class="select iq_ref_select_default block_centered">
							<?php
							for($i = 0; $i <= 100; $i++) {
								if($iDef == $i) {
									$szDef = 'selected';
								} else {
									$szDef = '';
								}
								?>
								<option value="<?php echo esc_attr($i); ?>" <?php echo esc_attr($szDef); ?>>
									<?php echo esc_html($i); ?>%
								</option>
								<?php
							}
							?>
						</select>
					</li>
					<li class="iq_ref_li">
						<div class="iq_ref_li_head">
							<?php echo esc_html__('On/Off pyramid levels', 'iq-referral-program-for-woocommerce'); ?>
						</div>
						
						<?php
						if(func_iq_rpw_module_enable('pyramid')) {
							
							$szDef = '';
							if($settings_arr && isset($settings_arr['pyramid_enable']) && (int)$settings_arr['pyramid_enable']) {
								$szDef = 'checked';
							}
							?>
							<input class="iq_ref_tgl iq_ref_tgl_ios" id="pyramid_enable" name="pyramid_enable" type="checkbox" <?php echo esc_attr($szDef); ?>>
							<label class="iq_ref_tgl_btn" for="pyramid_enable"></label>
							
							<?php
						} else {
							?>
							<div class="iq_ref_color_red">
								<?php echo esc_html__('Available in PRO version', 'iq-referral-program-for-woocommerce'); ?>
							</div>
							<?php
						}
						?>
					</li>
				</ul>
		
				<button type="submit" class="iq_ref_button_light_blue iq_ref_button_margin">
					<?php echo esc_html__('Save', 'iq-referral-program-for-woocommerce'); ?>
				</button>
			</form>
		</div>

		<div class="iq_ref_list_block iq_ref_flex_300">
			<div class="iq_ref_block_setup">
				<div class="iq_ref_block_logo">
					<div class="iq_ref_block_centered iq_ref_align_center">
						<?php
						$img_url = IQ_RPW_CORE_URL . '/assets/img/iq_logo.png';
						?>
						<img src="<?php echo esc_url($img_url); ?>" class="iq_ref_img_logo">
					</div>
				</div>
				<div class="iq_ref_block_btn_setup">
					<a href="/wp-admin/admin.php?page=iq-rp-wizard" class="iq_ref_btn_setup">
						<?php echo esc_html__('Run setup wizard', 'iq-referral-program-for-woocommerce'); ?>
					</a>
				</div>
			</div>
		</div>

	</div>

	<div class="iq_ref_list_block iq_ref_flex_300">
		<div id="ws_content">
			<?php include dirname( __FILE__ ).'/IQ_RPW_settings_withdrawal_systems.php'; ?>
		</div>
		
		<!-- Copyright -->
		<div class="iq_ref_copyright_block">
			<?php
			$copyright_url = 'https://lumpx.com/wp-plugins';
			?>
			<a href="<?php echo esc_url($copyright_url); ?>" target="_blank" class="iq_ref_copyright_a">Developed by LumpX</a>
		</div>
	</div>
</div>