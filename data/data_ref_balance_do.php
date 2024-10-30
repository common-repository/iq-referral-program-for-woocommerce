<?php
if(!isset($_SERVER['HTTP_REFERER'])) { die(); }

if(!isset($_POST['Values'])) {
	die();
}

if(!isset($_POST['JsonData']) ||
!isset($_POST['SignData'])) {
	die();
}

if(!is_plugin_active('woocommerce/woocommerce.php')) {
	$szMsg = esc_html__('For the affiliate program to work, you need install and activated Woocommerce plugin', 'iq-referral-program-for-woocommerce');
	die($szMsg);
}

// Protect
$JsonProtect = sanitize_text_field($_POST['JsonData']);
$SignCheck = func_iq_rpw_get_sign_json_array_protect($JsonProtect);
$szPostSign = sanitize_text_field($_POST['SignData']);
if($szPostSign != $SignCheck) { die(); }
$JsonVal = func_iq_rpw_out_protect($JsonProtect, IQ_RPW_SECRET_CODE);

########################
##### DATA PROTECT #####
########################
$CArr = json_decode($JsonVal, true);
if(empty($CArr)) {
	return false;
}

// target_id
if(!isset($CArr['target_id'])) {
	die();
}
$CArr['target_id'] = (int)$CArr['target_id'];
if($CArr['target_id'] <= 0) {
	die();
}

// pop
if(!isset($CArr['pop'])) {
	die();
}
$CArr['pop'] = (int)$CArr['pop'];

###############
##### POP #####
###############
$bPop = 0;
if(isset($_POST['Pop'])) {
	$bPop = sanitize_text_field($_POST['Pop']);
	$bPop = (int)$bPop;
}
if($bPop) {
	$szPreTag = 'pop_';
} else {
	$szPreTag = '';
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

#######################
##### DATA VALUES #####
#######################
$DataValuesArr = array();
$DataJsonStrArr = array();
$PostValues = sanitize_text_field($_POST['Values']);
$Exp = explode(',', $PostValues);
$ArrayData = array();
for($i = 0; $i < count($Exp); $i++) {
	$TempParam = trim($Exp[$i]);
	if(empty($TempParam)) { continue; }
	$ArrayData[] = $TempParam;
}

if(empty($ArrayData)) {
	die();
}

if(!isset($cIQ_RPW_ReferralClass)) {
	require_once IQ_RPW_CORE_DIR.'/includes/inc_referral_class.php';
	$cIQ_RPW_ReferralClass = new IQ_RPW_ReferralClass();
}
$partner_info = $cIQ_RPW_ReferralClass->getInfo($CArr['target_id']);
if(!$partner_info) {
	$szMsg = esc_html__('Partner not found', 'iq-referral-program-for-woocommerce');
	die($szMsg);
}

$balance_arr = json_decode($partner_info['ref_balance_json'], true);
$woo_curr_p = func_iq_rpw_in_protect(get_woocommerce_currency_symbol(), 'curr');
if(!$balance_arr) {
	$balance_arr[$woo_curr_p] = 0;
} else {
	if(!array_key_exists($woo_curr_p, $balance_arr)) {
		$balance_arr[$woo_curr_p] = 0;
	}
}

$iNum = 0;

$rebalance_arr = [];
foreach($balance_arr AS $curr_p => $val) {
	$Key = $szPreTag.'balance_curr_'.$iNum;
	if(in_array($Key, $ArrayData) && isset($_POST[$Key])) {
		$fVal = sanitize_text_field($_POST[$Key]);
		$fVal = (float)$fVal;
		if($fVal < 0) {
			$fVal = 0;
		}
		$rebalance_arr[$curr_p] = $fVal;
	}
	$iNum++;
}

$balance_json = json_encode($rebalance_arr);
$result = $cIQ_RPW_ReferralClass->updateUserBalance($CArr['target_id'], $balance_json);
if(!$result) {
	$szMsg = esc_html__('Error', 'iq-referral-program-for-woocommerce');
	die($szMsg);
}
?>
<div id="success"></div>