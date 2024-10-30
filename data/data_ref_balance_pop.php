<?php
if(!isset($_SERVER['HTTP_REFERER'])) { die(); }

if(!isset($_POST['TargetID'])) {
	die();
}
$iPostTargetID = sanitize_text_field($_POST['TargetID']);
$iPostTargetID = (int)$iPostTargetID;
if($iPostTargetID <= 0) {
	die();
}

if(!is_plugin_active('woocommerce/woocommerce.php')) {
	$szMsg = esc_html__('For the affiliate program to work, you need install and activated Woocommerce plugin', 'iq-referral-program-for-woocommerce');
	die($szMsg);
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
$partner_info = $cIQ_RPW_ReferralClass->getInfo($iPostTargetID);
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

$bPop = true;
if($bPop) {
	$szPreTag = 'pop_';
} else {
	$szPreTag = '';
}

$FormArr = [
	'head' => esc_html__('Change balance', 'iq-referral-program-for-woocommerce'),
	'button' => esc_html__('Save', 'iq-referral-program-for-woocommerce'),
];

$CArr = [
	'pop' => (int)$bPop,
	'target_id' => $iPostTargetID,
];
$JsonData = json_encode($CArr);
$JsonDataP = func_iq_rpw_in_protect($JsonData, IQ_RPW_SECRET_CODE);
$SignData = func_iq_rpw_get_sign_json_array_protect($JsonDataP);

$iMaxWidth = 600;
?>
<div id="pop">
	<input type="hidden" id="<?php echo esc_attr($szPreTag); ?>json_data" value="<?php echo esc_attr($JsonDataP); ?>">
	<input type="hidden" id="<?php echo esc_attr($szPreTag); ?>sign_data" value="<?php echo esc_attr($SignData); ?>">
	
	<div class="pop_overlay"></div>
	<div class="pop_modal pop_effect" id="pop_modal">
		<div class="pop_window">
			<div id="pop_pos" class="pop_pos">
				<div id="pop_window" class="pop_main" style="max-width:<?php echo esc_attr($iMaxWidth); ?>px;">
					<div class="pop_head">
						<div class="pop_head_txt">
							<?php echo esc_html($FormArr['head']); ?>
						</div>	
						<button class="pop_close_button" onclick="PopClose();" data-content="<?php echo esc_html__('Close', 'iq-referral-program-for-woocommerce'); ?>">
							<span class="dashicons dashicons-no"></span>
						</button>
					</div>
					<div id="pop_content" class="pop_body">
						<ul id="<?php echo esc_attr($szPreTag); ?>form_data" class="pop_ul">
							<?php
							$iNum = 0;
							foreach($balance_arr AS $curr_p => $val) {
								/*
								if($curr_p != $woo_curr_p) {
									if(!$val) {
										continue;
									}
								}
								*/
								$curr = func_iq_rpw_out_protect($curr_p, 'curr');
								
								?>
								<li class="iq_ref_li">
									<div class="iq_ref_flexbox_st iq_ref_flexbox_xc iq_ref_flexbox_vc iq_ref_flex_gap">
										<div>
											<input type="text" id="<?php echo esc_attr( $szPreTag . 'balance_curr_' . $iNum ); ?>" class="iq_ref_input iq_ref_input_default" maxlength="100" placeholder="" value="<?php echo esc_attr($val); ?>" autocomplete="off" onkeyup="return IQ_RPW_OnlyNumFloat(this.id);">
										</div>
										<div>
											<?php echo esc_html($curr); ?>
										</div>
									</div>
								</li>
								<?php
								$iNum++;
							}
							?>
							<li class="pop_li">
								<div id="<?php echo esc_attr($szPreTag); ?>notice_form_data"></div>
							</li>
							<li class="pop_li iq_ref_center">
								<button class="iq_ref_button_light_green" onclick="IQ_RPW_BalanceUpdateDo(<?php echo esc_js($bPop); ?>, <?php echo esc_js($iPostTargetID); ?>); return false;">
									<?php echo esc_html($FormArr['button']); ?>
								</button>
							</li>
						</ul>
					</div>
					
					<!-- Copyright -->
					<div class="iq_ref_copyright_block">
						<?php
						$copyright_url = 'https://lumpx.com/wp-plugins';
						?>
						<a href="<?php echo esc_url($copyright_url); ?>" target="_blank" class="iq_ref_copyright_a">Developed by LumpX</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>