<?php
// Restrict
if(!defined('ABSPATH') || !defined('IQ_RPW_CORE_DIR')) {
	die();
}

if (!function_exists('is_plugin_active')) {
	include_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

if(!isset($cIQ_RPW_ReferralClass)) {
	require_once IQ_RPW_CORE_DIR.'/includes/inc_referral_class.php';
	$cIQ_RPW_ReferralClass = new IQ_RPW_ReferralClass();
}
$WDSystemsObj = $cIQ_RPW_ReferralClass->getWithdrawSystems();

if(!isset($cur_user_id)) {
	$cur_user_id = get_current_user_id();
}

?>
	
<div class="iq_ref_core_block">
	<div class="nav_block">
		<div id="iq_ref_breadcrumb" class="nav_block">
			<ul id="iq_ref_breadcrumb">
				<li>
					<a href="/my-account/iq-referral">
						<?php echo esc_html__('Referral programm', 'iq-referral-program-for-woocommerce'); ?>
					</a>
				</li>
				<li>
					<a href="#" onclick="return false;" class="active">
						<?php echo esc_html__('Withdrawal request', 'iq-referral-program-for-woocommerce'); ?>
					</a>
				</li>
			</ul>
		</div>
	</div>
 
    <?php
	$szErr = '';
	$bErr = false;
    if(isset($_POST['withdraw_form'])) {
		$DataForm = [];
		$bPopForm = (int)sanitize_text_field($_POST['withdraw_form']);
		if($bPopForm) {
			$szPopFormTag = 'pop_';
		} else {
			$szPopFormTag = '';
		}
		
        // requisites
		if(!$bErr) {
			$Key = $szPopFormTag.'requisites';
			if(!isset($_POST[$Key]) || empty(sanitize_text_field($_POST[$Key]))) {
				$szErr = esc_html__("Enter details for withdrawal", "iq-referral-program-for-woocommerce");
				$bErr = true;
			} else {
				$DataForm['requisites'] = sanitize_text_field($_POST[$Key]);
			}
		}
		
		// system
		if(!$bErr) {
			$Key = $szPopFormTag.'system';
			if(!isset($_POST[$Key]) || (int)sanitize_text_field($_POST[$Key]) <= 0) {
				$szErr = esc_html__("Choose a withdrawal system", "iq-referral-program-for-woocommerce");
				$bErr = true;
			} else {
				$DataForm['system'] = (int)sanitize_text_field($_POST[$Key]);
				
				if(!$WDSystemsObj) {
					$szErr = esc_html__("Unsupported withdrawal system", "iq-referral-program-for-woocommerce");
					$bErr = true;
				}
				$WDSystemsArr = (array)$WDSystemsObj;
				$bFound = false;
				foreach($WDSystemsArr AS $data) {
					if($DataForm['system'] == $data->id) {
						$bFound = true;
						$DataForm['comission'] = $data->commision;
						break;
					}
				}
				if(!$bFound) {
					$szErr = esc_html__("Unsupported withdrawal system", "iq-referral-program-for-woocommerce");
					$bErr = true;
				}
			}
		}
		
		// amount
		if(!$bErr) {
			$Key = $szPopFormTag.'amount';
			if(!isset($_POST[$Key]) || (float)sanitize_text_field($_POST[$Key]) <= 0) {
				$szErr = esc_html__("Withdrawal amount must be greater than 0", "iq-referral-program-for-woocommerce");
				$bErr = true;
			} else {
				$DataForm['amount'] = (float)sanitize_text_field($_POST[$Key]);
				
				// currency
				$Key = $szPopFormTag.'currency';
				if(!isset($_POST[$Key]) || empty($_POST[$Key])) {
					$szErr = esc_html__("Select withdrawal currency", "iq-referral-program-for-woocommerce");
					$bErr = true;
				} else {
					$DataForm['currency'] = sanitize_text_field($_POST[$Key]);
					if(!isset($balance_arr)) {
						$balance_arr = json_decode($partner_info['ref_balance_json'], true);
					}
					if(!array_key_exists($DataForm['currency'], $balance_arr)) {
						$balance_arr[$DataForm['currency']] = 0;
					}
					if($DataForm['amount'] > $balance_arr[$DataForm['currency']]) {
						$szErr = esc_html__("The specified amount is more than what you have on your balance", "iq-referral-program-for-woocommerce");
						$bErr = true;
					} else {
						// check min
						$WDSystemsArr = (array)$WDSystemsObj;
						$iMin = 0;
						foreach($WDSystemsArr AS $data) {
							if($DataForm['system'] == $data->id) {
								$iMin = $data->min;
								break;
							}
						}
						
						if($DataForm['amount'] < $iMin) {
							$szErr = sprintf(esc_html__("Minimum withdrawal amount in this system: %d", "iq-referral-program-for-woocommerce"), $iMin);
							$bErr = true;
						}
					}
				}
			}
		}
		
		// comment
		$DataForm['comment'] = '';
		if(!$bErr) {
			$Key = $szPopFormTag.'comment';
			if(isset($_POST[$Key])) {
				$DataForm['comment'] = sanitize_text_field($_POST[$Key]);
			}
		}
		
		// create form
		if(!$bErr) {
			$balance_arr[$DataForm['currency']] -= $DataForm['amount'];
			$balance_json = json_encode($balance_arr);
			
			$bResult = $cIQ_RPW_ReferralClass->updateUserBalance($cur_user_id, $balance_json);
			if(!$bResult) {
				$szErr = esc_html__("Error", "iq-referral-program-for-woocommerce");
				$bErr = true;
			} else {
				$iWithdrawOrderID = $cIQ_RPW_ReferralClass->WithdrawFormSave($cur_user_id, $DataForm['requisites'], $DataForm['system'], $DataForm['amount'], $DataForm['comission'], $DataForm['currency'], $DataForm['comment']);
				if(!$iWithdrawOrderID) {
					$szErr = esc_html__("Unfortunately, we were unable to create a withdrawal request. Contact Support", "iq-referral-program-for-woocommerce");
					$bErr = true;
					
					$balance_arr[$DataForm['currency']] += $DataForm['amount'];
					$balance_json = json_encode($balance_arr);
					$bResult = $cIQ_RPW_ReferralClass->updateUserBalance($cur_user_id, $balance_json);
				}
			}
		}
    }
	if($bErr) {
		if(empty($szErr)) {
			$szErr = esc_html__("Error", "iq-referral-program-for-woocommerce");
		}
		?>
		<div class="iq_ref_alert_block iq_ref_err iq_ref_button_margin">
			<?php echo esc_html($szErr); ?>
		</div>
		<?php
	}
    ?>
	<div class="iq_ref_flexbox_tab iq_ref_flex_gap">
		<div class="iq_ref_list_block iq_ref_flexh iq_ref_flex_300 iq_ref_max_w400">
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
							<?php echo esc_html__('Create an application for the withdrawal of referral accruals. After processing the application, we will credit you with funds', 'iq-referral-program-for-woocommerce'); ?>
						</div>
					</li>
				</ul>
			</div>
		</div>

		<div class="iq_ref_list_block iq_ref_flexh iq_ref_flex_300">
			<h2 class="iq_ref_head_block">
				<?php echo esc_html__('Withdrawal request', 'iq-referral-program-for-woocommerce'); ?>
			</h2>
			<div>
				<ul class="iq_ref_ulcl">
					<li class="iq_ref_li">
						<div class="iq_ref_center">
							<button id="btn_form" class="iq_ref_button_light_blue iq_ref_button_margin" onclick="IQ_RPW_ShowForm();">
								<?php echo esc_html__('Create payout', 'iq-referral-program-for-woocommerce'); ?>
							</button>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</div>
	
	<?php
	
	$number = 10;

	$paged = 1;
	if(isset($_GET['paged']) && (int)sanitize_text_field($_GET['paged'])) {
		$paged = (int)sanitize_text_field($_GET['paged']);
	}

	$offset = ($paged - 1) * $number;
	$users = $cIQ_RPW_ReferralClass->getWithdrawApps();
	$total_users = count($users);
	$args = [
		'offset' =>  $offset,
		'number' =>  $number,
	];
	$query = $cIQ_RPW_ReferralClass->getWithdrawApps($args);
	$total_users = count($users);
	$total_query = count($query);
	$total_pages = intval($total_users / $number) + 1;

	$iCollumsCount = 0;
	
	?>
	<div class="iq_ref_list_block iq_ref_hided">
		<h2 class="iq_ref_head_block">
			<?php echo esc_html__('Withdrawal history', 'iq-referral-program-for-woocommerce'); ?>
		</h2>
		<div>
			<table class="iq_ref_tbl">
				<thead>
				<tr>
					<th scope="col">
						<?php echo esc_html__('System for payout', 'iq-referral-program-for-woocommerce'); ?>
						<?php $iCollumsCount++ ?>
					</th>
					<th scope="col">
						<?php echo esc_html__('Requisites', 'iq-referral-program-for-woocommerce'); ?>
						<?php $iCollumsCount++ ?>
					</th>
					<th scope="col">
						<?php echo esc_html__('Withdrawal amount', 'iq-referral-program-for-woocommerce'); ?>
						<?php $iCollumsCount++ ?>
					</th>
					<th scope="col">
						<?php echo esc_html__('Date', 'iq-referral-program-for-woocommerce'); ?>
						<?php $iCollumsCount++ ?>
					</th>
					<th scope="col" style="text-align: center;">
						<?php echo esc_html__('Status', 'iq-referral-program-for-woocommerce'); ?>
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
										<?php echo esc_html__('You have not created withdrawal requests yet', 'iq-referral-program-for-woocommerce'); ?>
									</div>
									<?php
								}
								?>
							</td>
						</tr>
						<?php
					} else {
						
						$status_arr = $cIQ_RPW_ReferralClass->getWithdrawStatus();
						foreach($query AS $q) {
							// Status
							$StatusArr = [
								'block_class' => '',
								'block_text' => '',
							];
							
							$q['status'] = (int)$q['status'];
							if($status_arr && array_key_exists($q['status'], $status_arr)) {
								
								$StatusArr = [
									'block_class' => $status_arr[$q['status']]['classes'],
									'block_text' => esc_html__($status_arr[$q['status']]['name'], 'iq-referral-program-for-woocommerce'),
								];
							}
							?>
							<tr>
								<td data-label="<?php echo esc_html__('System', 'iq-referral-program-for-woocommerce'); ?>" class="align_center">
									<?php
									if(empty($q['system_name'])) {
										echo esc_html('-');
									} else {
										echo esc_html($q['system_name']);
									}
									?>
								</td>
								<td data-label="<?php echo esc_html__('Requisites', 'iq-referral-program-for-woocommerce'); ?>" class="align_center">
									<?php echo esc_html($q['requisites']); ?>
								</td>
								<td data-label="<?php echo esc_html__('Sum', 'iq-referral-program-for-woocommerce'); ?>" class="align_center">
									<?php echo esc_html( $q['sum'] . ' ' . func_iq_rpw_out_protect($q['currency'], 'curr') ); ?>
								</td>
								<td data-label="<?php echo esc_html__('Date', 'iq-referral-program-for-woocommerce'); ?>">
									<ul class="iq_ref_ulcl align_center">
										<li class="iq_ref_li_clear">
											<?php echo esc_html(date('d.m.Y H:i:s', $q['date'])); ?>
										</li>
										<?php if($q['do_date']) { ?>
											<li class="iq_ref_li_clear iq_ref_do_date">
												<?php echo esc_html(date('d.m.Y H:i:s', $q['do_date'])); ?>
											</li>
										<?php } ?>
									</ul>
								</td>
								<td data-label="<?php echo esc_html__('Status', 'iq-referral-program-for-woocommerce'); ?>" class="<?php echo esc_attr($StatusArr['block_class']); ?> iq_ref_status_pos">
									<?php echo esc_html($StatusArr['block_text']); ?>
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