<?php
// Restrict
if(!defined('ABSPATH') || !defined('IQ_RPW_CORE_DIR')) {
	die();
}

if(!isset($cIQ_RPW_ReferralClass)) {
	require_once IQ_RPW_CORE_DIR.'/includes/inc_referral_class.php';
	$cIQ_RPW_ReferralClass = new IQ_RPW_ReferralClass();
}
$settings_arr = $cIQ_RPW_ReferralClass->get_settings();
?>
<div class="iq_rpw_wizard_step">
	<div class="iq_rpw_wizard_atitle">
		<?php echo esc_html__('Default options', 'iq-referral-program-for-woocommerce'); ?>
	</div>
	<div class="iq_rpw_wizard_aptxt">
		<?php echo esc_html__('Set the referral program values that will be enabled by default. You can always change the values for a specific user in the plugin settings IQ Referral', 'iq-referral-program-for-woocommerce'); ?>
	</div>
	<div class="iq_rpw_wizard_body">
		<table id="form_data" class="iq_rpw_wizard_tbl_list iq_rpw_wizard_block_centered">
			<tbody>
				<tr class="">
					<td class="iq_rpw_wizard_td_left">
						<?php echo esc_html__('Default status', 'iq-referral-program-for-woocommerce'); ?>
					</td>
					<td class="iq_rpw_wizard_td_right">
						<select name="status_def" id="status_def" class="iq_rpw_wizard_select iq_rpw_wizard_select_default">
							<option value="0">
								<?php echo esc_html__('Off', 'iq-referral-program-for-woocommerce'); ?>
							</option>
							<option value="1" selected>
								<?php echo esc_html__('On', 'iq-referral-program-for-woocommerce'); ?>
							</option>
						</select>
					</td>
				</tr>
				<tr class="">
					<td class="iq_rpw_wizard_td_left">
						<?php echo esc_html__('Default percent', 'iq-referral-program-for-woocommerce'); ?>
					</td>
					<td class="iq_rpw_wizard_td_right">
						<select name="percent_def" id="percent_def" class="iq_rpw_wizard_select iq_rpw_wizard_select_default">
							<?php
							$iDef = 5;
							if($settings_arr && isset($settings_arr['percent_def'])) {
								$iDef = (int)$settings_arr['percent_def'];
							}
							for($i = 0; $i <= 100; $i++) {
								if($iDef == $i) {
									$szDef = 'selected';
								} else {
									$szDef = '';
								}
								?>
								<option value="<?php echo esc_attr($i); ?>" <?php echo esc_attr($szDef); ?>>
									<?php echo esc_html($i . '%'); ?>
								</option>
								<?php
							}
							?>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="notice_form_data"></div>
	<div class="iq_rpw_wizard_ablock_btn">
		<div class="iq_rpw_wizard_btns_line">
			<a href="/wp-admin/admin.php?page=iq-rp-wizard&step=2" class="iq_rpw_wizard_btn_back iq_rpw_wizard_f1">
				<?php echo esc_html__('Come back', 'iq-referral-program-for-woocommerce'); ?>
			</a>
			<a href="#" class="iq_rpw_wizard_abutton iq_rpw_wizard_f2" onclick="IQ_RPW_WizardSettingsApply();return false;">
				<?php echo esc_html__('Apply settings', 'iq-referral-program-for-woocommerce'); ?>
			</a>
			<a href="/wp-admin/admin.php?page=iq-rp-wizard&step=4" class="iq_rpw_wizard_btn_skip iq_rpw_wizard_f1">
				<?php echo esc_html__('Skip', 'iq-referral-program-for-woocommerce'); ?>
			</a>
		</div>
	</div>
</div>