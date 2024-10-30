<?php
if(!isset($_SERVER['HTTP_REFERER'])) { die(); }

$iPostItemID = 0;
if(isset($_POST['ItemID'])) {
	$iPostItemID = (int)sanitize_text_field($_POST['ItemID']);
}
if($iPostItemID <= 0) {
	die();
}

##################
##### ACCESS #####
##################
if ( !is_user_logged_in() ) {
	$szMsg = esc_html__('Authorization required', 'iq-referral-program-for-woocommerce');
	die($szMsg);
}

$cur_user_id = get_current_user_id();
if($cur_user_id <= 0) {
	$szMsg = esc_html__('Authorization required', 'iq-referral-program-for-woocommerce');
	die($szMsg);
}
$user_obj = get_userdata( $cur_user_id );

if(!$user_obj) {
	wp_logout();
	$szMsg = esc_html__('Authorization required', 'iq-referral-program-for-woocommerce');
	die($szMsg);
}
$roles_arr = (array)$user_obj->roles;

$need_role = 'administrator';
if(!in_array($need_role, $roles_arr)) {
	$szMsg = esc_html__('Access denied', 'iq-referral-program-for-woocommerce');
	die($szMsg);
}


if(!isset($cIQ_RPW_ReferralClass)) {
	require_once IQ_RPW_CORE_DIR.'/includes/inc_referral_class.php';
	$cIQ_RPW_ReferralClass = new IQ_RPW_ReferralClass();
}

$status_arr = $cIQ_RPW_ReferralClass->getWithdrawStatus();
if(!$status_arr) {
	$szMsg = esc_html__('Available statuses not found', 'iq-referral-program-for-woocommerce');
	die($szMsg);
}

// get info
$SQL = "
	AND
		a.`id` = '".$iPostItemID."'
";
$query = $cIQ_RPW_ReferralClass->getWithdrawApps([], $SQL);
var_dump($query);
$q = [];
if($query) {
	foreach($query AS $data) {
		$q = $data;
		break;
	}
}
if(!$q) {
	die();
}

$bPop = true;
if($bPop) {
	$szPreTag = 'pop_';
} else {
	$szPreTag = '';
}

$iMaxWidth = 600;

// protect
$CArr = [
	'pop' => (int)$bPop,
	'item_id' => $iPostItemID,
];
$JsonData = json_encode($CArr);
$JsonDataP = func_iq_rpw_in_protect($JsonData, IQ_RPW_SECRET_CODE);
$SignData = func_iq_rpw_get_sign_json_array_protect($JsonDataP);

esc_html__('In process...', 'iq-referral-program-for-woocommerce');
esc_html__('Completed', 'iq-referral-program-for-woocommerce');
esc_html__('Error', 'iq-referral-program-for-woocommerce');
?>
<div id="pop">
	<input type="hidden" id="<?php echo esc_attr($szPreTag . 'json_data'); ?>" value="<?php echo esc_attr($JsonDataP); ?>">
	<input type="hidden" id="<?php echo esc_attr($szPreTag . 'sign_data'); ?>" value="<?php echo esc_attr($SignData); ?>">
	
	<div class="pop_overlay"></div>
	<div class="pop_modal pop_effect" id="pop_modal">
		<div class="pop_window">
			<div id="pop_pos" class="pop_pos">
				<div id="pop_window" class="pop_main" style="max-width:<?php echo esc_attr($iMaxWidth); ?>px;">
					<div class="pop_head">
						<div class="pop_head_txt">
							<?php echo esc_html__('Change status', 'iq-referral-program-for-woocommerce'); ?>
						</div>	
						<button class="pop_close_button" onclick="PopClose();" data-content="<?php echo esc_html__('Close', 'iq-referral-program-for-woocommerce'); ?>">
							<span class="dashicons dashicons-no"></span>
						</button>
					</div>
					<div id="pop_content" class="pop_body">

						<ul id="<?php echo esc_attr($szPreTag); ?>form_data" class="pop_ul">
						
							<li class="pop_li">
								<div class="iq_ref_li_head iq_ref_align_center">
									<?php echo esc_html__('New status', 'iq-referral-program-for-woocommerce'); ?>
								</div>
								
								<select name="<?php echo esc_attr($szPreTag); ?>status" id="<?php echo esc_attr($szPreTag); ?>status" class="iq_ref_select iq_ref_select_default iq_ref_block_centered">
								
									<?php
									foreach($status_arr AS $status_id => $data) {
										if($status_id == (int)$q['status']) {
											$szDef = 'selected';
										} else {
											$szDef = '';
										}
										?>
										
										<option value="<?php echo esc_attr($status_id); ?>" <?php echo esc_attr($szDef); ?>>
											<?php echo esc_html__($data['name'], 'iq-referral-program-for-woocommerce'); ?>
										</option>
										
										<?php
									}
									?>
									
								</select>
								
							</li>
								
							<li class="pop_li">
								<div id="<?php echo esc_attr($szPreTag . 'notice_form_data'); ?>"></div>
							</li>
							
							<li class="pop_li iq_ref_center">
								<button id="<?php echo esc_attr($szPreTag . 'ws_btn_add'); ?>" class="iq_ref_button_light_green" onclick="IQ_RPW_WsStatusDo(<?php echo (int)esc_js($bPop); ?>, <?php echo (int)esc_js($iPostItemID); ?>);return false;">
									<?php echo esc_html__('Save', 'iq-referral-program-for-woocommerce'); ?>
								</button>
							</li>
								
						</ul>
						
						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>