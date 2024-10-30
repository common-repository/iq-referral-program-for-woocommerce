<?php
// Restrict
if(!defined('ABSPATH') || !defined('IQ_RPW_CORE_DIR')) {
	die();
}
?>
<div class="iq_rpw_wizard_step">
	<div class="iq_rpw_wizard_atitle">
		<?php echo esc_html__('Welcome to the referral program', 'iq-referral-program-for-woocommerce'); ?><br>
		IQ Referral System
	</div>
	<div class="iq_rpw_wizard_aptxt">
		<?php echo esc_html__('This setup wizard will help you quickly set up your referral program in order to attract more customers', 'iq-referral-program-for-woocommerce'); ?>
	</div>
	<div class="iq_rpw_wizard_ablock_btn">
		<a href="/wp-admin/admin.php?page=iq-rp-wizard&step=2" class="iq_rpw_wizard_abutton">
			<?php echo esc_html__('Let`s start setting up', 'iq-referral-program-for-woocommerce'); ?>
		</a>
	</div>
</div>