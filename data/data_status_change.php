<?php
if(!isset($_SERVER['HTTP_REFERER'])) { die(); }

if(!isset($_POST['Value'])) { die(); }
$iPostValue = (int)sanitize_text_field($_POST['Value']);
if($iPostValue < 0) { $iPostValue = 0; }

if(!isset($_POST['UserID'])) { die(); }
$iPostUserID = (int)sanitize_text_field($_POST['UserID']);
if($iPostUserID <= 0) { die(); }

if(!isset($cIQ_RPW_ReferralClass)) {
	require_once IQ_RPW_CORE_DIR.'/includes/inc_referral_class.php';
	$cIQ_RPW_ReferralClass = new IQ_RPW_ReferralClass();
}
$partner_info = $cIQ_RPW_ReferralClass->getInfo($iPostUserID);

if(!$partner_info) {
	$szMsg = esc_html__('Partner not found', 'iq-referral-program-for-woocommerce');
	die($szMsg);
}

if($iPostValue) {
	$iPostValue = 1;
} else {
	$iPostValue = 0;
}

global $wpdb;
$sql = "
	UPDATE 
		`{$wpdb->prefix}iq_rpw_users`
	SET
		`ref_enable` = %d
	WHERE
		`uid` = %d
";
$wpdb->query($wpdb->prepare($sql, $iPostValue, $iPostUserID));
