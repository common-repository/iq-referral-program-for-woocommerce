<?php
if(isset($_POST['action'])) {
	$AjaxRequest = true;
} else {
	$AjaxRequest = false;
	if(!defined("IQ_RPW_CORE_DIR")) { die(); }
}

if($AjaxRequest) {
	if(!isset($cIQ_RPW_ReferralClass)) {
		require_once IQ_RPW_CORE_DIR.'/includes/inc_referral_class.php';
		$cIQ_RPW_ReferralClass = new IQ_RPW_ReferralClass();
	}
}

############################
##### SYSTEMS WITHDRAW #####
############################
$iCollumsCount = 0;

$WDSystemsObj = $cIQ_RPW_ReferralClass->getWithdrawSystems();
$WDSystemsArr = (array)$WDSystemsObj;
?>

<h2 class="iq_ref_flextbl">
	<div class="iq_ref_flexbox_st iq_ref_flexbox_pos iq_ref_flexbox_yc iq_ref_flex_gap">
		<div>
			<?php echo esc_html__('Withdrawal systems', 'iq-referral-program-for-woocommerce'); ?> 
			
		</div>
		<div>
			<a href="#" class="a_add" onclick="IQ_RPW_WsAdd();return false;">
				<?php echo esc_html__('Add', 'iq-referral-program-for-woocommerce'); ?>
			</a>
		</div>
	</div>
</h2>

<table class="iq_ref_tbl">
	<thead>
		<tr>
			<th scope="col">
				<?php echo esc_html__('System name', 'iq-referral-program-for-woocommerce'); ?>
				<?php $iCollumsCount++ ?>
			</th>
			<th scope="col">
				<?php echo esc_html__('Minimum withdraw amount', 'iq-referral-program-for-woocommerce'); ?>
				<?php $iCollumsCount++ ?>
			</th>
			<th scope="col">
				<?php echo esc_html__('Comission', 'iq-referral-program-for-woocommerce'); ?>
				<?php $iCollumsCount++ ?>
			</th>
			<th scope="col">
				<?php echo esc_html__('Status', 'iq-referral-program-for-woocommerce'); ?>
				<?php $iCollumsCount++ ?>
			</th>
			<th scope="col">
				<?php echo esc_html__('Action', 'iq-referral-program-for-woocommerce'); ?>
				<?php $iCollumsCount++ ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php
		if(!$WDSystemsArr) {
			?>
			<tr>
				<td colspan="<?php echo esc_attr($iCollumsCount); ?>">
					<div class="iq_ref_table_no_results">
						
						<div class="iq_ref_nf_block">
							<div class="iq_ref_nf_icon">
								<i class="icofont-bag-alt"></i>
							</div>
							<div class="iq_ref_nf_head">
								<?php echo esc_html__('There are no withdrawal systems', 'iq-referral-program-for-woocommerce'); ?>
							</div>
							<div class="iq_ref_nf_txt iq_ref_block_centered">
								<?php echo esc_html__('You have not added any withdrawal systems yet', 'iq-referral-program-for-woocommerce'); ?><br>
								<?php echo esc_html__('You can add the system by pressing', 'iq-referral-program-for-woocommerce'); ?> <a href="#" class="a_add" onclick="IQ_RPW_WsAdd();return false;">«<?php echo esc_html__('Add', 'iq-referral-program-for-woocommerce'); ?>»</a>
							</div>
						</div>
						
					</div>
				</td>
			</tr>
			<?php
		} else {
			foreach($WDSystemsArr AS $data) {
				?>
				<tr id="<?php echo esc_attr( 'ws_block_' . $data->id ); ?>">
					<td data-label="<?php echo esc_html__('System name', 'iq-referral-program-for-woocommerce'); ?>">
						<?php echo esc_html($data->name); ?>
					</td>
					<td data-label="<?php echo esc_html__('Minimum withdraw amount', 'iq-referral-program-for-woocommerce'); ?>">
						<?php echo esc_html($data->min); ?>
					</td>
					<td data-label="<?php echo esc_html__('Comission', 'iq-referral-program-for-woocommerce'); ?>">
						<?php echo esc_html($data->commision . '%'); ?>
					</td>
					<td data-label="<?php echo esc_html__('Status', 'iq-referral-program-for-woocommerce'); ?>">
						<?php
						if($data->enable) {
							echo esc_html__('On', 'iq-referral-program-for-woocommerce');
						} else {
							echo esc_html__('Off', 'iq-referral-program-for-woocommerce');
						}
						?>
					</td>
					<td data-label="<?php echo esc_html__('Action', 'iq-referral-program-for-woocommerce'); ?>">
						<a href="#" onclick="IQ_RPW_WsAdd(<?php echo esc_js($data->id); ?>);return false;">
							<span class="dashicons dashicons-edit"></span>
						</a>
						<a href="#" onclick="IQ_RPW_SystemDelete(<?php echo esc_js($data->id); ?>);return false;">
							<span class="dashicons dashicons-trash"></span>
						</a>
					</td>
				</tr>
				
				<?php
			}
			?>
		<?php } ?>
	</tbody>
</table>