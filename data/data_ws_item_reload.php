<?php
if(isset($_POST['action'])) {
	$AjaxRequest = true;
} else {
	$AjaxRequest = false;
	if(!defined("IQ_RPW_CORE_DIR")) { die(); }
}

if($AjaxRequest) {
	if(!isset($_SERVER['HTTP_REFERER'])) { die(); }

	$iPostItemID = 0;
	if(isset($_POST['ItemID'])) {
		$iPostItemID = (int)sanitize_text_field($_POST['ItemID']);
	}

	##################
	##### ACCESS #####
	##################
	if ( !is_user_logged_in() ) {
		die();
	}

	$cur_user_id = get_current_user_id();
	if($cur_user_id <= 0) {
		die();
	}
	$user_obj = get_userdata( $cur_user_id );

	if(!$user_obj) {
		die();
	}
	$roles_arr = (array)$user_obj->roles;

	$need_role = 'administrator';
	if(!in_array($need_role, $roles_arr)) {
		die();
	}
	
	if(!isset($cIQ_RPW_ReferralClass)) {
		require_once IQ_RPW_CORE_DIR.'/includes/inc_referral_class.php';
		$cIQ_RPW_ReferralClass = new IQ_RPW_ReferralClass();
	}

	$SQL_Search = "
		AND
			a.`id` = '".$iPostItemID."'
	";
	$users = $cIQ_RPW_ReferralClass->getWithdrawApps([], $SQL_Search);
	$q = [];
	if($users) {
		foreach($users AS $data) {
			$q = $data;
			break;
		}
	}
	if(!$q) {
		die();
	}
	
	$status_arr = $cIQ_RPW_ReferralClass->getWithdrawStatus();
}	
// Status
$StatusArr = [
	'block_class' => '',
	'block_text' => '',
];

$q['status'] = (int)$q['status'];
if($status_arr && array_key_exists($q['status'], $status_arr)) {
	$StatusArr = [
		'block_class' => $status_arr[$q['status']]['classes'],
		'block_text' => esc_html__($status_arr[$q['status']]['name'], 'iq-referral-program-for-woocommerce'),
	];
}

?>
<tr id="<?php echo esc_attr('item_block_' . $q['id']); ?>">
	<td data-label="<?php echo esc_html__('Partner', 'iq-referral-program-for-woocommerce'); ?>">
		<?php echo esc_html($q['user_login']); ?>
	</td>
	<td data-label="<?php echo esc_html__('System', 'iq-referral-program-for-woocommerce'); ?>">
		<?php echo esc_html($q['system_name']); ?>
	</td>
	<td data-label="<?php echo esc_html__('Requisites', 'iq-referral-program-for-woocommerce'); ?>">
		<?php echo esc_html($q['requisites']); ?>
	</td>
	<td data-label="<?php echo esc_html__('Sum', 'iq-referral-program-for-woocommerce'); ?>">
		<?php
		// check comission
		if($q['comission']) {
			$q['sum_total'] = $q['sum'] - ($q['comission'] * $q['sum'] / 100);
		} else {
			$q['sum_total'] = $q['sum'];
		}
		?>
		<ul class="iq_ref_ulcl">
			<li class="iq_ref_li_clear">
				<span class="iq_ref_sum_txt">
					<?php echo esc_html( func_iq_rpw_raz_float($q['sum_total'], 2) . ' ' . func_iq_rpw_out_protect($q['currency'], 'curr') ); ?>
				</span>
			</li>
			<li class="iq_ref_li_clear iq_ref_font_11 iq_ref_color_gray">
				<?php if($q['comission']) { ?>
					( <?php echo esc_html( '-' . $q['comission'] . '%'); ?> <?php echo esc_html__('comission', 'iq-referral-program-for-woocommerce'); ?> )
				<?php } ?>
			</li>
		</ul>
	</td>
	<td data-label="<?php echo esc_html__('Date', 'iq-referral-program-for-woocommerce'); ?>">
		 <?php echo esc_html__(date('d.m.Y H:i:s', $q['date'])); ?>
	</td>
	<td data-label="<?php echo esc_html__('Status', 'iq-referral-program-for-woocommerce'); ?>" class="iq_ref_status_pos <?php echo esc_attr($StatusArr['block_class']); ?>">
	
		<ul class="iq_ref_ulcl">
			<li class="iq_ref_li_clear">
				<?php echo esc_html($StatusArr['block_text']); ?>
			</li>
			<li class="iq_ref_li_clear">
				<a href="#" onclick="IQ_RPW_WsStatusIndex(<? echo esc_js($q['id']); ?>);return false;" class="iq_ref_status_a">
					<?php echo esc_html__('Change', 'iq-referral-program-for-woocommerce'); ?>
				</a>
			</li>
		</ul>
		
	</td>
</tr>