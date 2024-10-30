<?php
// Restrict
if(!defined('ABSPATH') || !defined('IQ_RPW_CORE_DIR')) {
	die();
}

if(!isset($cIQ_RPW_ReferralClass)) {
	require_once IQ_RPW_CORE_DIR.'/includes/inc_referral_class.php';
	$cIQ_RPW_ReferralClass = new IQ_RPW_ReferralClass();
}
$WDSystemsObj = $cIQ_RPW_ReferralClass->getWithdrawSystems();
$iMaxLengthNameWS = 64;
$iMaxLengthMinWS = 6;
?>
<div class="iq_rpw_wizard_step">
	<div class="iq_rpw_wizard_atitle">
		<?php echo esc_html__('Withdrawal system', 'iq-referral-program-for-woocommerce'); ?>
	</div>
	<div class="iq_rpw_wizard_aptxt">
		<?php echo esc_html__('In order for your partners to be able to create withdrawal requests from their referral balance, you need to add at least one withdrawal system. For example: PayPal', 'iq-referral-program-for-woocommerce'); ?>
	</div>
	<div class="iq_rpw_wizard_body">
		<?php
		if($WDSystemsObj) {
			?>
			<div>
				<div class="iq_rpw_wizard_block_centered iq_rpw_wizard_align_center">
					<?php
					$img_url = IQ_RPW_WIZARD_URL . '/assets/img/okey.png';
					?>
					<img src="<?php echo esc_url($img_url); ?>" class="iq_rpw_wizard_img_okey">
				</div>
				<div class="iq_rpw_wizard_atitle">
					<?php echo esc_html__('Great', 'iq-referral-program-for-woocommerce'); ?>!
				</div>
				<div class="iq_rpw_wizard_deftxt">
					<?php echo esc_html__('You have already added systems for withdrawing funds. You can add additional ones in the IQ Referral plugin settings', 'iq-referral-program-for-woocommerce'); ?>
				</div>
			</div>
			<?php
		} else {
			?>		
			<ul id="form_data" class="iq_rpw_wizard_ulcl">
				<li class="iq_rpw_wizard_li">
					<div class="iq_rpw_wizard_li_head">
						<?php echo esc_html__('System name', 'iq-referral-program-for-woocommerce'); ?>
					</div>
					<input type="text" id="ws_name" class="iq_rpw_wizard_input iq_rpw_wizard_input_default iq_rpw_wizard_full_width_b" maxlength="<?php echo esc_attr($iMaxLengthNameWS); ?>" placeholder="<?php echo esc_html__('Name of the withdrawal system', 'iq-referral-program-for-woocommerce'); ?>" value="">
				</li>
				<li class="iq_rpw_wizard_li">
					<div class="iq_rpw_wizard_li_head">
						<?php echo esc_html__('Minimum withdraw amount', 'iq-referral-program-for-woocommerce'); ?>
					</div>
					<input type="text" id="ws_min" class="iq_rpw_wizard_input iq_rpw_wizard_input_default iq_rpw_wizard_full_width_b" maxlength="<?php echo esc_attr($iMaxLengthMinWS); ?>" placeholder="0" value="">
				</li>
				<li class="iq_rpw_wizard_li">
					<div class="iq_rpw_wizard_li_head">
						<?php echo esc_html__('Comission', 'iq-referral-program-for-woocommerce'); ?>
					</div>
					<select class="iq_rpw_wizard_select iq_rpw_wizard_select_default" id="ws_commision">
						<?php
							for($ic = 0; $ic <= 100; $ic++) {
								$szDef = '';
								?>
								<option value="<?php echo esc_attr($ic); ?>" <?php echo esc_attr($szDef); ?>>
									<?php echo esc_html($ic . '%'); ?>
								</option>
								
								<?php
							}
						?>
					</select>
				</li>
			</ul>
			
			<?php
		}
		?>
	</div>
	<div id="notice_form_data"></div>
	<div class="iq_rpw_wizard_ablock_btn">
		<div class="iq_rpw_wizard_btns_line">
			<a href="/wp-admin/admin.php?page=iq-rp-wizard&step=3" class="iq_rpw_wizard_btn_back iq_rpw_wizard_f1">
				<?php echo esc_html__('Come back', 'iq-referral-program-for-woocommerce'); ?>
			</a>
			<?php if($WDSystemsObj) { ?>
				<a class="iq_rpw_wizard_abutton iq_rpw_wizard_f2" href="/wp-admin/admin.php?page=iq-rp-settings">
					<?php echo esc_html__('Finish setup', 'iq-referral-program-for-woocommerce'); ?>
				</a>
			<?php } else { ?>
				<a href="#" class="iq_rpw_wizard_abutton iq_rpw_wizard_f2" onclick="IQ_RPW_WizardWithdrawAddDo();return false;">
					<?php echo esc_html__('Create', 'iq-referral-program-for-woocommerce'); ?>
				</a>
			<?php } ?>
			<a class="iq_rpw_wizard_btn_skip iq_rpw_wizard_f1" href="/wp-admin/admin.php?page=iq-rp-wizard&step=5">
				<?php echo esc_html__('Skip', 'iq-referral-program-for-woocommerce'); ?>
			</a>
		</div>
	</div>
</div>