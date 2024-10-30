<?php
if(!isset($_SERVER['HTTP_REFERER'])) { die(); }

if(!isset($_POST['TargetID'])) {
	die();
}
$iPostTargetID = (int)sanitize_text_field($_POST['TargetID']);
if($iPostTargetID <= 0) {
	die();
}

$bConfirm = 0;
if(isset($_POST['Confirm'])) {
	$bConfirm = (int)sanitize_text_field($_POST['Confirm']);
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

	
###################
##### CONFIRM #####
###################
/*
if(!$bConfirm) {
	$szMsg = esc_html__('Are you sure you want to update the referral code for this partner?', 'iq-referral-program-for-woocommerce');
	$szBlockMsg = '
		<div id="confirm">
			'.$szMsg.'
		</div>
	';
	die($szMsg);
}
*/
	
###############
##### ADD #####
###############
global $wpdb;
$ref_code = $iPostTargetID.func_iq_rpw_generatePassword(8);

$sql = "
	UPDATE
		`{$wpdb->prefix}iq_rpw_users`
	SET
		`ref_code` = %s
	WHERE
		`uid` = %d;
";
$result = $wpdb->query($wpdb->prepare($sql,
					$ref_code,
					$iPostTargetID));
if(!$result) {
	$szMsg = esc_html__('Error', 'iq-referral-program-for-woocommerce');
	die($szMsg);
}
?>
<div id="success"></div>