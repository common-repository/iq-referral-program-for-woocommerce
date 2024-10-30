<?php
if(!isset($_SERVER['HTTP_REFERER'])) { die(); }

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

if(!isset($cIQ_RPW_ReferralClass)) {
	require_once IQ_RPW_CORE_DIR.'/includes/inc_referral_class.php';
	$cIQ_RPW_ReferralClass = new IQ_RPW_ReferralClass();
}
$partner_info = $cIQ_RPW_ReferralClass->getInfo($cur_user_id);
if(!$partner_info) {
	$szMsg = esc_html__('Partner not found', 'iq-referral-program-for-woocommerce');
	die($szMsg);
}
$WDSystemsObj = $cIQ_RPW_ReferralClass->getWithdrawSystems();

$balance_arr = json_decode($partner_info['ref_balance_json'], true);
$woo_curr_p = func_iq_rpw_in_protect(get_woocommerce_currency_symbol(), 'curr');
if(!$balance_arr) {
	$balance_arr[$woo_curr_p] = 0;
}

$FormArr = [
	'head' => esc_html__('Withdrawal request', 'iq-referral-program-for-woocommerce'),
	'button' => esc_html__('Create payout', 'iq-referral-program-for-woocommerce'),
];

$bPop = true;
if($bPop) {
	$szPreTag = 'pop_';
} else {
	$szPreTag = '';
}
	
// wr_amount
$amount_recommend = 0;
$curr_recommend = '';
if(!isset($balance_arr)) {
	$balance_arr = json_decode($partner_info['ref_balance_json'], true);
}
if(!array_key_exists($woo_curr_p, $balance_arr)) {
	$balance_arr[$woo_curr_p] = 0;
}
if($balance_arr) {
	foreach($balance_arr AS $curr_p => $val) {
		if($curr_p != $woo_curr_p) {
			if(!$val) {
				continue;
			}
		}

		$amount_recommend = $val;
		$curr_recommend = func_iq_rpw_out_protect($curr_p, 'curr');
		break;
	}
}

$iMaxWidth = 600;
?>
<div id="pop">
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
						<form method="POST">
							<input type="hidden" name="withdraw_form" value="<?php echo esc_attr($bPop); ?>">
							<ul id="<?php echo esc_attr($szPreTag); ?>form_data" class="pop_ul">
							
							
								<li class="pop_li">
									<div class="iq_ref_li_head">
										<?php echo esc_html__('Requisites for withdrawing', 'iq-referral-program-for-woocommerce'); ?>
									</div>
									<input type="text" id="<?php echo esc_attr($szPreTag); ?>requisites" name="<?php echo esc_attr($szPreTag); ?>requisites" class="iq_ref_input iq_ref_input_default iq_ref_full_width_b" maxlength="100" placeholder="" value="">
								</li>
								
								
								<li class="pop_li">
									<div class="iq_ref_li_head">
										<?php echo esc_html__('Payout system', 'iq-referral-program-for-woocommerce'); ?>
									</div>
									<?php
									if($WDSystemsObj) {
										$WDSystemsArr = (array)$WDSystemsObj;
										foreach($WDSystemsArr AS $data) {
											if(!$data->enable) {
												continue;
											}
											$szinfo = $data->min.':'.$data->commision;
											?>
											<input type="hidden" id="<?php echo esc_attr($szPreTag); ?>system_info_<?php echo esc_attr($data->id); ?>" value="<?php echo esc_attr($szinfo); ?>">
											<?php
										}
									}
									?>
									<select name="<?php echo esc_attr($szPreTag); ?>system" id="<?php echo esc_attr($szPreTag); ?>system" class="iq_ref_select iq_ref_select_default" onchange="IQ_RPW_SystemChange(<?php echo esc_js($bPop); ?>);">
										<option value="0" selected>
											<?php echo esc_html__('- Choose a system for withdrawing - ', 'iq-referral-program-for-woocommerce'); ?>
										</option>
										<?php
										if($WDSystemsObj) {
											$WDSystemsArr = (array)$WDSystemsObj;
											foreach($WDSystemsArr AS $data) {
												if(!$data->enable) {
													continue;
												}
												?>
												<option value="<?php echo esc_attr($data->id); ?>">
													<?php echo esc_html($data->name); ?>
												</option>
												
												<?php
											}
										}
										?>
									</select>
								</li>
								
								
								<li class="iq_ref_li">
									<div id="<?php echo esc_attr($szPreTag); ?>block_system_info" class="iq_ref_hide iq_ref_font_11 iq_ref_color_gray"></div>
								</li>
										
								<!-- amount -->
								<li class="pop_li">
									<div class="iq_ref_li_head">
										<?php echo esc_html__('Withdrawal amount', 'iq-referral-program-for-woocommerce'); ?>
									</div>
									<div class="iq_ref_flexbox_tab iq_ref_flex_gap">
										<div>
											<input type="text" name="<?php echo esc_attr($szPreTag); ?>amount" id="<?php echo esc_attr($szPreTag); ?>amount" class="iq_ref_input iq_ref_input_default iq_ref_full_width_b iq_ref_max_w500" placeholder="<?php echo esc_attr($amount_recommend); ?>" onkeyup="return IQ_RPW_OnlyNumFloat(this.id);">
										</div>
										<div>
											<select name="<?php echo esc_attr($szPreTag); ?>currency" id="<?php echo esc_attr($szPreTag); ?>currency" class="iq_ref_select iq_ref_select_default">
												<?php
												foreach($balance_arr AS $curr_p => $val) {
													if($curr_p != $woo_curr_p) {
														if(!$val) {
															continue;
														}
													}
													$curr = func_iq_rpw_out_protect($curr_p, 'curr');
													
													if($curr_recommend == $curr) {
														$def = 'selected';
													} else {
														$def = '';
													}
													?>
														<option value="<?php echo esc_attr($curr_p); ?>" <?php echo esc_attr($def); ?>>
															<?php echo esc_html($curr); ?>
														</option>
													
													<?php
												}
												?>
											</select>
										</div>
									</div>
								</li>
								
								<!-- comment -->
								<li class="pop_li">
									<div class="iq_ref_li_head">
										<?php echo esc_html__('Payout comment', 'iq-referral-program-for-woocommerce'); ?>
									</div>
									<textarea class="iq_ref_textarea" rows="5" name="<?php echo esc_attr($szPreTag); ?>comment" id="<?php echo esc_attr($szPreTag); ?>comment"></textarea>
								</li>
								
								
								<li class="pop_li">
									<div id="<?php echo esc_attr($szPreTag . 'notice_form_data'); ?>"></div>
								</li>
								<li class="pop_li iq_ref_center">
									<button id="<?php echo esc_attr($szPreTag . 'ws_btn_add'); ?>" class="iq_ref_button_light_green">
										<?php echo esc_html($FormArr['button']); ?>
									</button>
								</li>
							</ul>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>