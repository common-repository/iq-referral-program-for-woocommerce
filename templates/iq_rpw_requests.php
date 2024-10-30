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

?>
<div class="iq_ref_list_preblock">
	<div class="iq_ref_list_block iq_ref_hided">
		<h2 class="iq_ref_head_block">
			<?php echo esc_html__('Withdrawal applications', 'iq-referral-program-for-woocommerce'); ?>
		</h2>
		<?php
		if(!is_plugin_active('woocommerce/woocommerce.php')) {
			?>
			<div class="iq_ref_alert_block iq_ref_err">
				<?php echo esc_html__('For the affiliate program to work, you need install and activated Woocommerce plugin', 'iq-referral-program-for-woocommerce'); ?>
			</div>
			<?php
		} else {
			// POSTS
			$szErr = '';
			$bErr = false;
			if(isset($_POST['id_upd_status']) && (int)sanitize_text_field($_POST['id_upd_status'])) {
				$DataArr = [];
				$DataArr['form_id'] = (int)sanitize_text_field($_POST['id_upd_status']);
				$DataArr['comment'] = '';
				if(isset($_POST['comment'])) {
					$DataArr['comment'] = sanitize_text_field($_POST['comment']);
				}
				
				if(!isset($_POST['status']) || (int)sanitize_text_field($_POST['status']) == -2) {
					$szErr = esc_html__("Select status", "iq-referral-program-for-woocommerce");
					$bErr = true;
				}
				$DataArr['status'] = (int)sanitize_text_field($_POST['status']);
				
				$bResult = $cIQ_RPW_ReferralClass->WithdrawChangeStatus($DataArr['form_id'], $DataArr['status'], $DataArr['comment']);
				if(!$bResult) {
					$szErr = esc_html__("An error occurred while trying to update the status", "iq-referral-program-for-woocommerce");
					$bErr = true;
				}
				
				if($bErr) {
					if(empty($szErr)) {
						$szErr = esc_html__("Error", "iq-referral-program-for-woocommerce");
					}
					?>
					<div class="iq_ref_alert_block iq_ref_err iq_ref_button_margin">
						<?php echo esc_html($szErr); ?>
					</div>
					<?php
				} else {
					?>
					<div class="iq_ref_alert_block iq_ref_ok iq_ref_button_margin">
						<?php echo esc_html__("Status updated successfully", "iq-referral-program-for-woocommerce"); ?>
					</div>
					<?php
				}
			}
			
					
			$number = 10;

			$paged = 1;
			if(isset($_GET['paged']) && (int)sanitize_text_field($_GET['paged'])) {
				$paged = (int)sanitize_text_field($_GET['paged']);
			}

			$offset = ($paged - 1) * $number;
			$users = $cIQ_RPW_ReferralClass->getWithdrawApps();
			$total_users = count($users);
			$args = [
				'offset' =>  $offset,
				'number' =>  $number,
			];
			$query = $cIQ_RPW_ReferralClass->getWithdrawApps($args);
			$total_users = count($users);
			$total_query = count($query);
			$total_pages = intval($total_users / $number) + 1;

			$iCollumsCount = 0;
				
			?>
			<div class="iq_ref_alert_block iq_ref_info">
				<?php echo esc_html__('In this section, applications for the withdrawal of your partners are provided', 'iq-referral-program-for-woocommerce'); ?>
			</div>
			<div id="notice_block"></div>
				
			<table class="iq_ref_tbl">
				<thead>
				<tr>
					<th scope="col">
						<?php echo esc_html__('Partner', 'iq-referral-program-for-woocommerce'); ?>
						<?php $iCollumsCount++ ?>
					</th>
					<th scope="col">
						<?php echo esc_html__('System', 'iq-referral-program-for-woocommerce'); ?>
						<?php $iCollumsCount++ ?>
					</th>
					<th scope="col">
						<?php echo esc_html__('Requisites', 'iq-referral-program-for-woocommerce'); ?>
						<?php $iCollumsCount++ ?>
					</th>
					<th scope="col">
						<?php echo esc_html__('Sum', 'iq-referral-program-for-woocommerce'); ?>
						<?php $iCollumsCount++ ?>
					</th>
					<th scope="col">
						<?php echo esc_html__('Date', 'iq-referral-program-for-woocommerce'); ?>
						<?php $iCollumsCount++ ?>
					</th>
					<th scope="col">
						<?php echo esc_html__('Status', 'iq-referral-program-for-woocommerce'); ?>
						<?php $iCollumsCount++ ?>
					</th>
				</tr>
				</thead>
				<tbody>
				<?php
				if(!$query) {
					?>
					<tr>
						<td colspan="<?php echo esc_attr($iCollumsCount); ?>">
							<?php
							if($paged <= 1) {
								?>
								<div class="iq_ref_nf_block">
									<div class="iq_ref_nf_icon">
										<i class="icofont-files-stack"></i>
									</div>
									<div class="iq_ref_nf_head">
										<?php echo esc_html__('No applications were found', 'iq-referral-program-for-woocommerce'); ?>
									</div>
									<div class="iq_ref_nf_txt iq_ref_block_centered">
										<?php echo esc_html__('Applications for the withdrawal of balance were not found', 'iq-referral-program-for-woocommerce'); ?><br>
										<?php echo esc_html__('As soon as users create applications, it will become available here', 'iq-referral-program-for-woocommerce'); ?>
									</div>
								</div>
								<?php
							}
							?>
						</td>
					</tr>
					<?php
				} else {
					
					$status_arr = $cIQ_RPW_ReferralClass->getWithdrawStatus();
					foreach($query AS $q) {
						include IQ_RPW_CORE_DIR.'/data/data_ws_item_reload.php';
					}
				}
				?>
				</tbody>
			</table>
			<?php
			if ($total_users > $total_query) {
				?>
				<div id="pagination" class="iq_ref_paged_block clearfix">
					<?php
					$current_page = max(1, $paged);
					echo paginate_links(array(
						'base'      => get_pagenum_link(1) . '%_%',
						'format'    => '&paged=%#%',
						'current'   => $current_page,
						'total'     => $total_pages,
						'prev_next' => false,
						'type'      => 'list',
					));
					?>
				</div>
				<?php
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
</div>