<?php
if(!isset($_SERVER['HTTP_REFERER'])) { die(); }

$iPostItemID = 0;
if(isset($_POST['ItemID'])) {
	$iPostItemID = (int)sanitize_text_field($_POST['ItemID']);
}
$iMaxWidth = 500;

$ItemData = [];
if($iPostItemID) {
	global $wpdb;
	$sql = "
		SELECT
			*
		FROM
			`{$wpdb->prefix}iq_rpw_withdraw_systems`
		WHERE
			`id` = %d;
	";
	$ItemData = $wpdb->get_row($wpdb->prepare($sql, $iPostItemID), ARRAY_A);
}

if($ItemData) {
	// edit
	$FormArr = [
		'head' => esc_html__('Edit withdrawal system', 'iq-referral-program-for-woocommerce'),
		'commision' => $ItemData['commision'],
		'button' => esc_html__('Save', 'iq-referral-program-for-woocommerce'),
		'ws_name_val' => $ItemData['name'],
		'ws_min_val' => $ItemData['min'],
	];
	
	if($ItemData['enable']) {
		$FormArr['status_val'] = 'checked';
	} else {
		$FormArr['status_val'] = '';
	}
} else {
	// create
	$FormArr = [
		'head' => esc_html__('Add a withdrawal system', 'iq-referral-program-for-woocommerce'),
		'commision' => 0,
		'status_val' => 'checked',
		'button' => esc_html__('Add', 'iq-referral-program-for-woocommerce'),
		'ws_name_val' => '',
		'ws_min_val' => '',
	];
}

$iMaxLengthNameWS = 64;
$iMaxLengthMinWS = 6;
$bPop = true;
if($bPop) {
	$szPreTag = 'pop_';
} else {
	$szPreTag = '';
}

$CArr = [
	'pop' => (int)$bPop,
	'item_id' => $iPostItemID,
];
$JsonData = json_encode($CArr);
$JsonDataP = func_iq_rpw_in_protect($JsonData, IQ_RPW_SECRET_CODE);
$SignData = func_iq_rpw_get_sign_json_array_protect($JsonDataP);
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
						
						
							<!-- ws_name -->
							<li class="pop_li">
								<div class="iq_ref_li_head">
									<?php echo esc_html__('System name', 'iq-referral-program-for-woocommerce'); ?>
								</div>
								<input type="text" id="<?php echo esc_attr($szPreTag); ?>ws_name" class="iq_ref_input iq_ref_input_default iq_ref_full_width_b" maxlength="<?php echo esc_attr($iMaxLengthNameWS); ?>" placeholder="<?php echo esc_html__('Name of the withdrawal system', 'iq-referral-program-for-woocommerce'); ?>" value="<?php echo esc_attr($FormArr['ws_name_val']); ?>">
							</li>
	
							<!-- ws_min -->
							<li class="pop_li">
								<div class="iq_ref_li_head">
									<?php echo esc_html__('Minimum withdraw amount', 'iq-referral-program-for-woocommerce'); ?>
								</div>
								<input type="text" id="<?php echo esc_attr($szPreTag); ?>ws_min" class="iq_ref_input iq_ref_input_default iq_ref_full_width_b" maxlength="<?php echo esc_attr($iMaxLengthMinWS); ?>" placeholder="0" value="<?php echo esc_attr($FormArr['ws_min_val']); ?>">
							</li>
							
							<!-- ws_commision -->
							<li class="pop_li">
								<div class="iq_ref_li_head">
									<?php echo esc_html__('Comission', 'iq-referral-program-for-woocommerce'); ?>
								</div>
								<select class="iq_ref_select iq_ref_select_default" id="<?php echo esc_attr($szPreTag); ?>ws_commision">
									<?php
									for($ic = 0; $ic <= 100; $ic++) {
										if($FormArr['commision'] == $ic) {
											$szDef = 'selected';
										} else {
											$szDef = '';
										}
										?>
										<option value="<?php echo esc_attr($ic); ?>" <?php echo esc_attr($szDef); ?>>
											<?php echo esc_html($ic); ?>%
										</option>
										<?php
									}
									?>
								</select>
							</li>
							
							<!-- ws_status -->
							<li class="pop_li">
								<div class="iq_ref_li_head">
									<?php echo esc_html__('Status', 'iq-referral-program-for-woocommerce'); ?>
								</div>
								<input class="iq_ref_tgl iq_ref_tgl_ios" id="<?php echo esc_attr($szPreTag); ?>ws_status" type="checkbox" <?php echo esc_attr($FormArr['status_val']); ?>>
								<label class="iq_ref_tgl_btn" for="<?php echo esc_attr($szPreTag); ?>ws_status"></label>
							</li>
							
							<li class="pop_li">
								<div id="<?php echo esc_attr($szPreTag); ?>notice_form_data"></div>
							</li>
							<li class="pop_li iq_ref_center">
								<button id="<?php echo esc_attr($szPreTag); ?>ws_btn_add" class="iq_ref_button_light_green" onclick="IQ_RPW_WsAddDo(<?php echo esc_js($bPop); ?>);return false;">
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