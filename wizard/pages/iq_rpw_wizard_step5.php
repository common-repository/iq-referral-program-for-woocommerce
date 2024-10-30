<?php
// Restrict
if(!defined('ABSPATH') || !defined('IQ_RPW_CORE_DIR')) {
	die();
}

?>
<div class="iq_rpw_wizard_step">
	<div class="iq_rpw_wizard_atitle">
		<?php echo esc_html__('Setup completed', 'iq-referral-program-for-woocommerce'); ?>
		
	</div>
	<div class="iq_rpw_wizard_body">
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
				 
				 <?php echo esc_html__('Basic IQ Referral setup completed successfully', 'iq-referral-program-for-woocommerce'); ?>
			</div>
		</div>
	</div>
	<div id="notice_form_data"></div>
	<div class="iq_rpw_wizard_ablock_btn">
		<div class="iq_rpw_wizard_btns_line">
			<a href="/wp-admin/admin.php?page=iq-rp-wizard&step=4" class="iq_rpw_wizard_btn_back iq_rpw_wizard_f1">
				<?php echo esc_html__('Come back', 'iq-referral-program-for-woocommerce'); ?>
			</a>
			<a class="iq_rpw_wizard_abutton iq_rpw_wizard_f2" href="/wp-admin/admin.php?page=iq-rp-settings">
				<?php echo esc_html__('Finish setup', 'iq-referral-program-for-woocommerce'); ?>
			</a>
			<a class="iq_rpw_wizard_btn_skip iq_rpw_wizard_f1" href="/wp-admin/admin.php?page=iq-rp-settings">
				<?php echo esc_html__('Skip', 'iq-referral-program-for-woocommerce'); ?>
			</a>
		</div>
	</div>
</div>