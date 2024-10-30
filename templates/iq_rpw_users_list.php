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

$filterArr = [];

################
##### SORT #####
################
if(isset($_GET['sort'])) {
	$sort_arr = [
		'ref_shopping',
		'ref_sell',
		'ref_visits',
	];
	$sort = sanitize_text_field($_GET['sort']);
	if(in_array($sort, $sort_arr)) {
		$order = 'DESC';
		if(isset($_GET['order'])) {
			$order = sanitize_text_field($_GET['order']);
			if($order != 'ASC' && $order != 'DESC') {
				$order = 'DESC';
			}
		}
		$filterArr[$sort] = $order;
	}
}

$offset = ($paged - 1) * $number;
$users = $cIQ_RPW_ReferralClass->getList([], '', $filterArr);
$total_users = count($users);
$args = [
    'offset' =>  $offset,
    'number' =>  $number,
];
$query = $cIQ_RPW_ReferralClass->getList($args, '', $filterArr);
$total_users = count($users);
$total_query = count($query);
$total_pages = intval($total_users / $number) + 1;

$iCollumsCount = 0;

$iPercent = 0;
$settings_arr = $cIQ_RPW_ReferralClass->get_settings();
if($settings_arr && isset($settings_arr['percent_def']) && (int)$settings_arr['percent_def']) {
	$iPercent = (int)$settings_arr['percent_def'];
}

$SettingsOptions = [
	'pyramid_enable' => 0,
];
if(func_iq_rpw_module_enable('pyramid')) {
	if($settings_arr && isset($settings_arr['pyramid_enable']) && (int)$settings_arr['pyramid_enable']) {
		$SettingsOptions = [
			'pyramid_enable' => 1,
		];
	}
}

$woo_curr_p = func_iq_rpw_in_protect(get_woocommerce_currency_symbol(), 'curr');
?>
<div class="iq_ref_list_preblock">
	<div class="iq_ref_list_block iq_ref_hided">
		<h2 class="iq_ref_head_block">
			<?php echo esc_html__('User partners', 'iq-referral-program-for-woocommerce'); ?>
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
				<div id="notice_block"></div>
				<thead>
				<tr>
					<th scope="col">
						<?php echo esc_html__('User', 'iq-referral-program-for-woocommerce'); ?>
						<?php $iCollumsCount++ ?>
					</th>
					<th scope="col">
						<?php echo esc_html__('Code', 'iq-referral-program-for-woocommerce'); ?>
						<?php $iCollumsCount++ ?>
					</th>
					<th scope="col">
						<?php echo esc_html__('Balance', 'iq-referral-program-for-woocommerce'); ?>
						<?php $iCollumsCount++ ?>
					</th>
					<th scope="col">
						<?php echo esc_html__('Invited', 'iq-referral-program-for-woocommerce'); ?>
						<?php $iCollumsCount++ ?>
					</th>
					<th scope="col" class="iq_ref_sort_th" onclick="IQ_RPW_sort('ref_shopping');">
						<div class="iq_ref_flexbox_st iq_ref_flexbox_vc iq_ref_flex_gap_2">
							<div>
								<?php echo esc_html__('Shopping', 'iq-referral-program-for-woocommerce'); ?>
							</div>
							<div>
								<span class="dashicons dashicons-sort"></span>
							</div>
						</div>
						<?php $iCollumsCount++ ?>
					</th>
					<th scope="col" class="iq_ref_sort_th" onclick="IQ_RPW_sort('ref_sell');">
						<div class="iq_ref_flexbox_st iq_ref_flexbox_vc iq_ref_flex_gap_2">
							<div>
								<?php echo esc_html__('Sales', 'iq-referral-program-for-woocommerce'); ?>
							</div>
							<div>
								<span class="dashicons dashicons-sort"></span>
							</div>
						</div>
						<?php $iCollumsCount++ ?>
					</th>
					<th scope="col">
						<?php echo esc_html__('Turnover', 'iq-referral-program-for-woocommerce'); ?>
						<?php $iCollumsCount++ ?>
					</th>
					<th scope="col" class="iq_ref_sort_th" onclick="IQ_RPW_sort('ref_visits');">
						<div class="iq_ref_flexbox_st iq_ref_flexbox_vc iq_ref_flex_gap_2">
							<div>
								<?php echo esc_html__('Visits', 'iq-referral-program-for-woocommerce'); ?>
							</div>
							<div>
								<span class="dashicons dashicons-sort"></span>
							</div>
						</div>
						<?php $iCollumsCount++ ?>
					</th>
					
					<?php
					if($SettingsOptions['pyramid_enable']) {
						?>
						<th scope="col">
							<?=esc_html__('Level', 'iq-referral-program-for-woocommerce');?>
							<?php $iCollumsCount++ ?>
						</th>
						<?php
					}
					?>
				
					<th scope="col">
						<?php echo esc_html__('Percent', 'iq-referral-program-for-woocommerce'); ?>
						<?php $iCollumsCount++ ?>
					</th>
					<th scope="col">
						<?php echo esc_html__('Enable', 'iq-referral-program-for-woocommerce'); ?>
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
											<i class="icofont-search-job"></i>
										</div>
										<div class="iq_ref_nf_head">
											<?php echo esc_html__('Partners were not found', 'iq-referral-program-for-woocommerce'); ?>
										</div>
										<div class="iq_ref_nf_txt iq_ref_block_centered">
											<?php echo esc_html__('There are no partners at the moment', 'iq-referral-program-for-woocommerce'); ?><br>
											<?php echo esc_html__('You can import existing users through', 'iq-referral-program-for-woocommerce'); ?> <a href="/wp-admin/admin.php?page=<?=IQ_RPW_MENU_TAG;?>wizard">Setup Wizard</a>
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
							include IQ_RPW_CORE_DIR.'/data/data_user_reload.php';
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