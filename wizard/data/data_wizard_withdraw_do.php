<?php
if(!isset($_SERVER['HTTP_REFERER'])) { die(); }

if(!isset($_POST['Values'])) {
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
$szPreTag = '';

/*
Array
(
    [0] => ws_name
    [1] => ws_min
    [2] => ws_commision
)
*/

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
	$DataValuesArr['min'] = sanitize_text_field((int)$_POST[$Key]);
	if($DataValuesArr['min'] < 0) {
		$DataValuesArr['min'] = 0;
	}
}

// ws_commision
$Key = $szPreTag.'ws_commision';
$DataValuesArr['commision'] = 0;
if(in_array($Key, $ArrayData) && isset($_POST[$Key])) {
	$DataValuesArr['commision'] = sanitize_text_field((int)$_POST[$Key]);
	if($DataValuesArr['commision'] < 0) {
		$DataValuesArr['commision'] = 0;
	}
	else if($DataValuesArr['commision'] > 100) {
		$DataValuesArr['commision'] = 100;
	}
}

$DataValuesArr['enable'] = 1;

// create
global $wpdb;
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

if(!$result) {
	$szMsg = esc_html__('Error', 'iq-referral-program-for-woocommerce');
	die($szMsg);
}

// Action logs
if(!isset($cIQ_RPW_ReferralClass)) {
	require_once IQ_RPW_CORE_DIR.'/includes/inc_referral_class.php';
	$cIQ_RPW_ReferralClass = new IQ_RPW_ReferralClass();
}
if(!isset($cur_user_id)) {
	$cur_user_id = get_current_user_id();
}
$log = esc_html__("Admin add new withdrawal system", "iq-referral-program-for-woocommerce");
$log .= ': '.json_encode($DataValuesArr, JSON_UNESCAPED_UNICODE);
$cIQ_RPW_ReferralClass->writeActionsLogs($cur_user_id, $log);

$szMsg = esc_html__('System for withdrawing has been successfully added', 'iq-referral-program-for-woocommerce');
?>
<div id="success">
	<?php echo esc_html($szMsg); ?>
</div>