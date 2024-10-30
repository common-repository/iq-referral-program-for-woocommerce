<?php
if(!isset($_SERVER['HTTP_REFERER'])) { die(); }

$iPostItemID = 0;
if(isset($_POST['ItemID'])) {
	$iPostItemID = (int)sanitize_text_field($_POST['ItemID']);
}

global $wpdb;
$sql = "
	DELETE FROM
		`{$wpdb->prefix}iq_rpw_withdraw_systems`
	WHERE
		`id` = %d;
";
$bResult = $wpdb->query($wpdb->prepare($sql, $iPostItemID));
if(!$bResult) {
	$szMsg = esc_html__('Error', 'iq-referral-program-for-woocommerce');
	die($szMsg);
}
?>
<div id="success"></div>