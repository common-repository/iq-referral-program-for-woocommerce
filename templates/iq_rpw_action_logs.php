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

$number = 15;
 
$paged = 1;
if(isset($_GET['paged']) && (int)sanitize_text_field($_GET['paged'])) {
    $paged = (int)sanitize_text_field($_GET['paged']);
}

$offset = ($paged - 1) * $number;
$users = $cIQ_RPW_ReferralClass->getActionsLogs();
$total_users = count($users);
$args = [
    'offset' =>  $offset,
    'number' =>  $number,
];
$query = $cIQ_RPW_ReferralClass->getActionsLogs($args);
$total_users = count($users);
$total_query = count($query);
$total_pages = intval($total_users / $number) + 1;

$iCollumsCount = 0;
?>
<div class="iq_ref_list_preblock">
	<div class="iq_ref_list_block iq_ref_hided">
		<h2 class="iq_ref_head_block">
			<?php echo esc_html__('Action logs', 'iq-referral-program-for-woocommerce'); ?>
		</h2>
		<?php
		if(!is_plugin_active('woocommerce/woocommerce.php')) {
			?>
			<div class="iq_ref_alert_block iq_ref_err">
				<?php echo esc_html__('For the affiliate program to work, you need install and activated Woocommerce plugin', 'iq-referral-program-for-woocommerce'); ?>
			</div>
			<?php
		} else {
			?>
			<table class="iq_ref_tbl">
				<div class="iq_ref_alert_block iq_ref_info">
					<?php echo esc_html__('In this section, you can view the logs of administrators who performed actions with IQ referral program', 'iq-referral-program-for-woocommerce'); ?>
				</div>
				<thead>
				<tr>
					<th scope="col">
						<?php echo esc_html__('Admin', 'iq-referral-program-for-woocommerce'); ?>
						<?php $iCollumsCount++ ?>
					</th>
					<th scope="col">
						<?php echo esc_html__('Log', 'iq-referral-program-for-woocommerce'); ?>
						<?php $iCollumsCount++ ?>
					</th>
					<th scope="col">
						<?php echo esc_html__('Date', 'iq-referral-program-for-woocommerce'); ?>
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
										<i class="icofont-investigation"></i>
									</div>
									<div class="iq_ref_nf_head">
										<?php echo esc_html__('Actions were not found', 'iq-referral-program-for-woocommerce'); ?>
									</div>
									<div class="iq_ref_nf_txt iq_ref_block_centered">
										<?php echo esc_html__('The actions of the referral program are absent', 'iq-referral-program-for-woocommerce'); ?><br>
										<?php echo esc_html__('As soon as the program settings are performed, the logs will become available', 'iq-referral-program-for-woocommerce'); ?>
									</div>
								</div>
								<?php
							}
							?>
						</td>
					</tr>
					<?php
				} else {
					foreach($query AS $q) {
						?>
						<tr>
							<td data-label="<?php echo esc_html__('User', 'iq-referral-program-for-woocommerce'); ?>">
								<?php echo esc_html($q['user_login']); ?>
							</td>
							<td data-label="<?php echo esc_html__('Log', 'iq-referral-program-for-woocommerce'); ?>">
								<?php echo esc_html($q['log']); ?>
							</td>
							<td data-label="<?php echo esc_html__('Date', 'iq-referral-program-for-woocommerce'); ?>">
								<?php echo esc_html(date('d.m.Y H:i:s', $q['date'])); ?>
							</td>
						</tr>
						<?php
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