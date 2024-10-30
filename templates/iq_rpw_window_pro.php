<?php
// Restrict
if(!defined('ABSPATH') || !defined('IQ_RPW_CORE_DIR')) {
	die();
}

$iCollumsCount = 0;
?>
<div class="iq_ref_pro_acenter">
	<div class="iq_ref_pro_block">
		<div class="iq_ref_pro_head">
			<div class="iq_ref_pro_icon_block">
				<!--
				<i class="icofont-star iq_ref_pro_icon"></i>
				-->
				<?php
				$img_url = IQ_RPW_CORE_URL . '/assets/img/pro.gif';
				?>
				<img src="<?php echo esc_url($img_url); ?>" class="iq_ref_pro_img" alt="IQ Referral System PRO">
			</div>
			<div class="iq_ref_pro_head_txt">
				<?php echo esc_html__('Upgrade to PRO', 'iq-referral-program-for-woocommerce'); ?>
			</div>
			<div class="iq_ref_pro_txt">
				<?php echo esc_html__('Getting the most out of a', 'iq-referral-program-for-woocommerce'); ?> IQ Referral System<br>
				<?php echo esc_html__('Upgrade to PRO and unlock all features', 'iq-referral-program-for-woocommerce'); ?>
			</div>
		
			<div class="iq_ref_mt15 iq_ref_align_center iq_ref_pro_padd">
				<a href="https://lumpx.com/wp-plugins/iq-referral-system" target="_blank" class="iq_ref_pro_btn">
					<?php echo esc_html__('More information', 'iq-referral-program-for-woocommerce'); ?>
				</a>
			</div>
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