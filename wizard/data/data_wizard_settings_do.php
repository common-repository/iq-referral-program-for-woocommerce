<?php
if(!isset($_SERVER['HTTP_REFERER'])) { die(); }

if(!isset($_POST['Values'])) {
	die();
}

if(!isset($cIQ_RPW_ReferralClass)) {
	require_once IQ_RPW_CORE_DIR.'/includes/inc_referral_class.php';
	$cIQ_RPW_ReferralClass = new IQ_RPW_ReferralClass();
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

// status_def
$Key = $szPreTag.'status_def';
$DataValuesArr['status_def'] = 1;
if(in_array($Key, $ArrayData) && isset($_POST[$Key]) && sanitize_text_field($_POST[$Key]) == '0') {
	$DataValuesArr['status_def'] = 0;
}

// percent_def
$Key = $szPreTag.'percent_def';
$DataValuesArr['percent_def'] = 0;
if(in_array($Key, $ArrayData) && isset($_POST[$Key])) {
	$DataValuesArr['percent_def'] = sanitize_text_field($_POST[$Key]);
	$DataValuesArr['percent_def'] = (int)$DataValuesArr['percent_def'];
	if($DataValuesArr['percent_def'] < 0) {
		$DataValuesArr['percent_def'] = 0;
	}
	else if($DataValuesArr['percent_def'] > 100) {
		$DataValuesArr['percent_def'] = 100;
	}
}

// Apply current users
global $wpdb;

$sql = "
	UPDATE
		`{$wpdb->prefix}iq_rpw_users`
	SET
		`ref_percent` = %d,
		`ref_enable` = %d
";
$result = $wpdb->query($wpdb->prepare($sql,
					$DataValuesArr['percent_def'],
					$DataValuesArr['status_def']));
					
// Save in settings
$result = $cIQ_RPW_ReferralClass->updateSettings($DataValuesArr);
if(!$result) {
	$szMsg = esc_html__('Error', 'iq-referral-program-for-woocommerce');
	die($szMsg);
}

// Action logs
if(!isset($cur_user_id)) {
	$cur_user_id = get_current_user_id();
}
$log = esc_html__("Admin save new settings", "iq-referral-program-for-woocommerce");
$log .= ': '.json_encode($DataValuesArr, JSON_UNESCAPED_UNICODE);
$cIQ_RPW_ReferralClass->writeActionsLogs($cur_user_id, $log);

$szMsg = esc_html__('Settings applied successfully', 'iq-referral-program-for-woocommerce');
?>
<div id="success">
	<?php echo esc_html($szMsg); ?>
</div>
