<?php
// Restrict
if(!defined('ABSPATH') || !defined('IQ_RPW_CORE_DIR')) {
	die();
}

if (!function_exists('is_plugin_active')) {
	include_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

// errors
$bPluginEnable = false;
if(is_plugin_active(IQ_RPW_PLUGIN_DIR.'/'.IQ_RPW_PLUGIN_DIR.'.php')) {
	$bPluginEnable = true;
	$cur_user_id = get_current_user_id();
	if(!isset($partner_info)) {
		if(!isset($cIQ_RPW_ReferralClass)) {
			require_once IQ_RPW_CORE_DIR.'/includes/inc_referral_class.php';
			$cIQ_RPW_ReferralClass = new IQ_RPW_ReferralClass();
		}
		$partner_info = $cIQ_RPW_ReferralClass->getInfo($cur_user_id);
	}
}
?>
<div class="">
	
    <?php
    if(!$bPluginEnable) {
        ?>
            <div class="iq_ref_center_pos">
                <div>
                    <div class="iq_ref_notify_icon">
		                <i class="icofont-star"></i>
                    </div>
                    <div class="iq_ref_notify_msg">
		                <?php echo esc_html__('Referral program not available', 'iq-referral-program-for-woocommerce'); ?>
                    </div>
                </div>
            </div>
        <?php
    } else {
        ####################
        ##### withdraw #####
        ####################
        if(isset($_GET['withdraw'])) {
            // withdraw
	        include dirname( __FILE__ ).'/iq_rpw_account_withdraw.php';
        }
        else {
            ?>
			<div class="iq_ref_core_block">
				<div class="nav_block">
					<div id="iq_ref_breadcrumb" class="nav_block">
						<ul id="iq_ref_breadcrumb">
							<li>
								<a href="#" onclick="return false;" class="active">
									<?php echo esc_html__('Referral programm', 'iq-referral-program-for-woocommerce'); ?>
								</a>
							</li>
							<li>
								<a href="/my-account/iq-referral/?withdraw">
									<?php echo esc_html__('Withdrawal request', 'iq-referral-program-for-woocommerce'); ?>
								</a>
							</li>
						</ul>
					</div> 
				</div>

				<div class="iq_ref_flexbox_tab iq_ref_flexbox_xc iq_ref_flex_gap">

					<div class="iq_ref_list_block padd_10 iq_ref_flex_300">
						<h2 class="iq_ref_head_block">
							<?php echo esc_html__('Your referral', 'iq-referral-program-for-woocommerce'); ?>
						</h2>
						<div>
							<ul class="iq_ref_ulcl">
								<?php
								if(isset($partner_info) && $partner_info && isset($partner_info['ref_percent']) && (int)$partner_info['ref_enable']) {
								$url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . sanitize_text_field($_SERVER['HTTP_HOST']);
								$RefLink = $url.'?r='.$partner_info['ref_code'];
								?>
								<li class="iq_ref_li">
									<div class="iq_ref_center">
										<div class="iq_ref_font_2em iq_ref_color_count iq_ref_font_bold">
											<?php echo esc_html($partner_info['ref_percent'] . '%'); ?>
										</div>
										<?php /*
										<div class="iq_ref_color_gray iq_ref_font_13">
											<?php echo esc_html__('Your percentage of accrual from each purchase', 'iq-referral-program-for-woocommerce'); ?>
										</div>
										*/ ?>
									</div>
								</li>
								<?php
								}

								if(isset($partner_info) && $partner_info && isset($partner_info['ref_code']) && !empty($partner_info['ref_code']) && (int)$partner_info['ref_enable']) {
									$url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . sanitize_text_field($_SERVER['HTTP_HOST']);
									$RefLink = $url.'?r='.$partner_info['ref_code'];
								?>
									<li class="iq_ref_li">
										<div class="iq_ref_li_head iq_ref_center">
											<?php echo esc_html__('Your referral link', 'iq-referral-program-for-woocommerce'); ?>
										</div>
										<div class="iq_ref_flexbox_st iq_ref_flexbox_xc iq_ref_flexbox_yc iq_ref_flex_gap">
											<div class="iq_ref_flex_300 iq_ref_max_w300 iq_ref_full_width_b">
												<input type="text" class="iq_ref_input iq_ref_input_default iq_ref_full_width_b" placeholder="" value="<?php echo esc_url($RefLink); ?>" disabled>
											</div>
											<div class="iq_ref_icon_click" style="font-size:30px;">
												<i class="icofont-copy" onclick="IQ_RPW_CopyStr('<?php echo esc_js($RefLink); ?>');"></i>
											</div>
										</div>
									</li>
								<?php
								} else {
									?>
									<div>
										<div>
											<i class="icofont-star"></i>
										</div>
										<div>
											<?php echo esc_html__('Referral program not available', 'iq-referral-program-for-woocommerce'); ?>
										</div>
									</div>
									<?php
								}
								?>
							</ul>
						</div>
					</div>

					<div class="iq_ref_list_block padd_10 iq_ref_flex_300">
						<h2 class="iq_ref_head_block">
							<?php echo esc_html__('Your referral balance', 'iq-referral-program-for-woocommerce'); ?>
						</h2>
						<div>
							<ul class="iq_ref_ulcl">
								<li class="iq_ref_li">
									<div class="iq_ref_center">
										<div class="iq_ref_font_1_7em iq_ref_color_count iq_ref_font_bold">
											<ul class="iq_ref_ulcl">
												<?php
												if(!isset($balance_arr)) {
													$balance_arr = json_decode($partner_info['ref_balance_json'], true);
												}
												$woo_curr_p = func_iq_rpw_in_protect(get_woocommerce_currency_symbol(), 'curr');
												if(!array_key_exists($woo_curr_p, $balance_arr)) {
													$balance_arr[$woo_curr_p] = 0;
												}
												foreach($balance_arr AS $curr_p => $val) {
													if($curr_p != $woo_curr_p) {
														if(!$val) {
															continue;
														}
													}
													$curr = func_iq_rpw_out_protect($curr_p, 'curr');
													?>
													<li class="iq_ref_li">
														<?php echo esc_html( func_iq_rpw_raz_float($val, 2) . ' ' . $curr ); ?>
													</li>
													<?php
												}
												?>
											</ul>
										</div>
									</div>
								</li>
								<li class="iq_ref_li">
									<div class="iq_ref_color_gray iq_ref_font_13 iq_ref_center">
										<?php echo esc_html__('Share your referral link and get the amount to your partner balance from each purchase of an invited client', 'iq-referral-program-for-woocommerce'); ?>
									</div>
								</li>
								<li class="iq_ref_li">
									<div class="iq_ref_center">
										<a href="?withdraw" class="iq_ref_button_light_blue iq_ref_button_margin">
											<?php echo esc_html__('Withdraw', 'iq-referral-program-for-woocommerce'); ?>
										</a>
									</div>
								</li>
							</ul>
						</div>
					</div>
				</div>

				<?php
				################
				##### LOGS #####
				################
				$sql_search = "
					AND
						a.`uid_father` = {$cur_user_id}
				";
				if(!isset($cIQ_RPW_ReferralClass)) {
					require_once IQ_RPW_CORE_DIR.'/includes/inc_referral_class.php';
					$cIQ_RPW_ReferralClass = new IQ_RPW_ReferralClass();
				}

				$number = 10;
				$paged = 1;
				if(isset($_GET['paged']) && (int)sanitize_text_field($_GET['paged'])) {
					$paged = (int)sanitize_text_field($_GET['paged']);
				}
				$users = $cIQ_RPW_ReferralClass->getLogs([], $sql_search);
				$offset = ($paged - 1) * $number;
				$total_users = count($users);
				$args = [
					'offset' =>  $offset,
					'number' =>  $number,
				];
				$query = $cIQ_RPW_ReferralClass->getLogs($args, $sql_search);
				$total_users = count($users);
				$total_query = count($query);
				$total_pages = intval($total_users / $number) + 1;
				$iCollumsCount = 0;
				?>
				<div class="iq_ref_list_block padd_10">
					<h2 class="iq_ref_head_block">
						<?php echo esc_html__('Your affiliate accruals', 'iq-referral-program-for-woocommerce'); ?>
					</h2>
					<div>
						<table class="iq_ref_tbl">
							<thead>
							<tr>
								<th scope="col">
									<?php echo esc_html__('User', 'iq-referral-program-for-woocommerce'); ?>
									<?php $iCollumsCount++ ?>
								</th>
								<th scope="col">
									<?php echo esc_html__('Sum', 'iq-referral-program-for-woocommerce'); ?>
									<?php $iCollumsCount++ ?>
								</th>
								<th scope="col">
									<?php echo esc_html__('Date', 'iq-referral-program-for-woocommerce'); ?>
									<?php $iCollumsCount++ ?>

								</th>
							</tr>
							</thead>
							<tbody>
								<?php
								if(!$query) {
									?>
									<tr>
										<td colspan="<?php echo esc_attr($iCollumsCount); ?>">
											<?php
											if($paged <= 1) {
												?>
												<div class="iq_ref_table_no_results">
													<?php echo esc_html__('There are no accruals on your referral link yet', 'iq-referral-program-for-woocommerce'); ?>
												</div>
												<?php
											}
											?>
										</td>
									</tr>
									<?php
								} else {
									foreach($query AS $q) {
										?>
										<tr>
											<td data-label="<?php echo esc_html__('User', 'iq-referral-program-for-woocommerce'); ?>">
												<?php echo esc_html($q['father_login']); ?>
											</td>
											<td data-label="<?php echo esc_html__('Sum', 'iq-referral-program-for-woocommerce'); ?>">
												<?php
												if($q['up']) {
													$Symb = "+";
												} else {
													$Symb = "-";
												}
												?>
												<span class="iq_ref_sum_txt">
													<?php echo esc_html( $Symb . ' ' . func_iq_rpw_raz_float($q['sum'], 2) . ' ' . $q['currency'] ); ?>
												</span>
											</td>
											<td data-label="<?php echo esc_html__('Date', 'iq-referral-program-for-woocommerce'); ?>">
												<?php echo esc_html(date('d.m.Y H:i:s', $q['date'])); ?>
											</td>
										</tr>
										<?php
									}
								}
								?>
							</tbody>
						</table>

					</div>
				</div>
			</div>

            <?php
        }
    }
    ?>
</div>