<?php
// Restrict
if(!defined('ABSPATH') || !defined('IQ_RPW_CORE_DIR')) {
	die();
}

if (!function_exists('is_plugin_active')) {
	include_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

?>
<div class="iq_rpw_wizard_acenter">
	<div class="iq_rpw_wizard_full_width_b">
		<div class="iq_rpw_wizard_block_logo">
			<div class="iq_rpw_wizard_block_centered iq_rpw_wizard_align_center">
				<?php
				$img_url = IQ_RPW_CORE_URL . '/assets/img/iq_logo.png';
				?>
				<img src="<?php echo esc_url($img_url); ?>" class="iq_rpw_wizard_img_logo">
			</div>
		</div>
		<div class="iq_rpw_wizard_ablock iq_rpw_wizard_hided">
			<?php
				$iStep = 1;
				if(isset($_GET['step'])) {
					$iStep = (int)sanitize_text_field($_GET['step']);
				}
				switch($iStep) {
					case 1: {
						include dirname( __FILE__ ).'/pages/iq_rpw_wizard_step1.php';
						break;
					}
					case 2: {
						include dirname( __FILE__ ).'/pages/iq_rpw_wizard_step2.php';
						break;
					}
					case 3: {
						include dirname( __FILE__ ).'/pages/iq_rpw_wizard_step3.php';
						break;
					}
					case 4: {
						include dirname( __FILE__ ).'/pages/iq_rpw_wizard_step4.php';
						break;
					}
					case 5: {
						include dirname( __FILE__ ).'/pages/iq_rpw_wizard_step5.php';
						break;
					}
					default: {
						include dirname( __FILE__ ).'/pages/iq_rpw_wizard_step1.php';
					}
				}
			?>
			
			<!-- Copyright -->
			<div class="iq_ref_copyright_block">
				<?php
				$copyright_url = 'https://lumpx.com/wp-plugins';
				?>
				<a href="<?php echo esc_url($copyright_url); ?>" target="_blank" class="iq_ref_copyright_a">Developed by LumpX</a>
			</div>
	
		</div>
		<div class="iq_rpw_wizard_afooter">
			<a href="/wp-admin/admin.php?page=iq-rp-settings" class="iq_rpw_wizard_a">
				<?php echo esc_html__('Back to control panel', 'iq-referral-program-for-woocommerce'); ?>
			</a>
		</div>
	</div>
</div>

<script>
	jQuery('a').click(function(e){ 
		if(e.target.id !== 'copyright') {
			IQ_RPW_WizardLoader(1); 
		}
	});
</script>