<?php
// Restrict
if(!defined('ABSPATH') || !defined('IQ_RPW_CORE_DIR')) {
	die();
}
set_time_limit(15*60);
?>
<div class="iq_rpw_wizard_step">
	<div class="iq_rpw_wizard_atitle">
		<?php echo esc_html__('Importing Users', 'iq-referral-program-for-woocommerce'); ?>
	</div>
	<div class="iq_rpw_wizard_aptxt">
		<?php echo esc_html__('In order to enable current users to act as a partner, you need to import them into the IQ Referral referral program', 'iq-referral-program-for-woocommerce'); ?>
	</div>
	<div id="notice_block"></div>
	<div class="iq_rpw_wizard_ablock_btn">
		<div class="iq_rpw_wizard_btns_line">
			<a href="/wp-admin/admin.php?page=iq-rp-wizard" class="iq_rpw_wizard_btn_back iq_rpw_wizard_f1">
				<?php echo esc_html__('Come back', 'iq-referral-program-for-woocommerce'); ?>
			</a>
			<a href="#" class="iq_rpw_wizard_abutton iq_rpw_wizard_f2" onclick="IQ_RPW_WizardImportUsers();return false;">
				<?php echo esc_html__('Import users', 'iq-referral-program-for-woocommerce'); ?>
			</a>
			<a href="/wp-admin/admin.php?page=iq-rp-wizard&step=3" class="iq_rpw_wizard_btn_skip iq_rpw_wizard_f1">
				<?php echo esc_html__('Skip', 'iq-referral-program-for-woocommerce'); ?>
			</a>
		</div>
	</div>
</div>