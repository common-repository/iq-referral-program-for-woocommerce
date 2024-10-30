<?php
if(!isset($_SERVER['HTTP_REFERER'])) { die(); }

if(!isset($_POST['Values'])) {
	die();
}

if(!isset($_POST['JsonData']) ||
!isset($_POST['SignData'])) {
	die();
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

// item_id
if(!isset($CArr['item_id'])) {
	die();
}
$CArr['item_id'] = (int)$CArr['item_id'];

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
	$bPop = (int)sanitize_text_field($_POST['Pop']);
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

// ws_name
$Key = $szPreTag.'ws_name';
if(!in_array($Key, $ArrayData) || !isset($_POST[$Key])) {
	$szMsg = esc_html__('You must enter a «%s»', 'iq-referral-program-for-woocommerce');
	die(sprintf($szMsg, esc_html__('Name of the withdrawal system', 'iq-referral-program-for-woocommerce')));
}
$DataValuesArr['name'] = sanitize_text_field($_POST[$Key]);
if(empty($DataValuesArr['name'])) {
	$szMsg = esc_html__('You must enter a «%s»', 'iq-referral-program-for-woocommerce');
	die(sprintf($szMsg, esc_html__('Name of the withdrawal system', 'iq-referral-program-for-woocommerce')));
}

// ws_min
$Key = $szPreTag.'ws_min';
$DataValuesArr['min'] = 0;
if(in_array($Key, $ArrayData) && isset($_POST[$Key])) {
	$DataValuesArr['min'] = (int)sanitize_text_field($_POST[$Key]);
	if($DataValuesArr['min'] < 0) {
		$DataValuesArr['min'] = 0;
	}
}

// ws_commision
$Key = $szPreTag.'ws_commision';
$DataValuesArr['commision'] = 0;
if(in_array($Key, $ArrayData) && isset($_POST[$Key])) {
	$DataValuesArr['commision'] = (int)sanitize_text_field($_POST[$Key]);
	if($DataValuesArr['commision'] < 0) {
		$DataValuesArr['commision'] = 0;
	}
	else if($DataValuesArr['commision'] > 100) {
		$DataValuesArr['commision'] = 100;
	}
}

// ws_status
$Key = $szPreTag.'ws_status';
$DataValuesArr['enable'] = 1;
if(in_array($Key, $ArrayData) && isset($_POST[$Key]) && sanitize_text_field($_POST[$Key]) == "false") {
	$DataValuesArr['enable'] = 0;
}

###############
##### ADD #####
###############
global $wpdb;

if($CArr['item_id']) {
	// edit
	$sql = "
		UPDATE
			`{$wpdb->prefix}iq_rpw_withdraw_systems`
		SET
			`name` = %s,
			`min` = %d,
			`commision` = %d,
			`enable` = %d
		WHERE
			`id` = %d;
	";
	$result = $wpdb->query($wpdb->prepare($sql,
						$DataValuesArr['name'],
						$DataValuesArr['min'],
						$DataValuesArr['commision'],
						$DataValuesArr['enable'],
						$CArr['item_id']));
		
	if($result) {
		if(!isset($cIQ_RPW_ReferralClass)) {
			require_once IQ_RPW_CORE_DIR.'/includes/inc_referral_class.php';
			$cIQ_RPW_ReferralClass = new IQ_RPW_ReferralClass();
		}
		
		// Action logs
		if(!isset($cur_user_id)) {
			$cur_user_id = get_current_user_id();
		}
		$log = esc_html__("Admin update withdrawal system", "iq-referral-program-for-woocommerce");
		$log .= ': '.json_encode($DataValuesArr, JSON_UNESCAPED_UNICODE);
		$cIQ_RPW_ReferralClass->writeActionsLogs($cur_user_id, $log);	
	}
} else {
	// create
	$sql = "
		INSERT INTO `{$wpdb->prefix}iq_rpw_withdraw_systems`
			(`name`,
			`min`,
			`commision`,
			`enable`)
		VALUES
			(%s,
			%d,
			%d,
			%d);
	";
	$result = $wpdb->query($wpdb->prepare($sql,
						$DataValuesArr['name'],
						$DataValuesArr['min'],
						$DataValuesArr['commision'],
						$DataValuesArr['enable']));
						
	if($result) {
		if(!isset($cIQ_RPW_ReferralClass)) {
			require_once IQ_RPW_CORE_DIR.'/includes/inc_referral_class.php';
			$cIQ_RPW_ReferralClass = new IQ_RPW_ReferralClass();
		}
		
		// Action logs
		if(!isset($cur_user_id)) {
			$cur_user_id = get_current_user_id();
		}
		$log = esc_html__("Admin add new withdrawal system", "iq-referral-program-for-woocommerce");
		$log .= ': '.json_encode($DataValuesArr, JSON_UNESCAPED_UNICODE);
		$cIQ_RPW_ReferralClass->writeActionsLogs($cur_user_id, $log);	
	}
}
if(!$result) {
	$szMsg = esc_html__('Error', 'iq-referral-program-for-woocommerce');
	die($szMsg);
}
?>
<div id="success"></div>