<?php
if(isset($_POST['action'])) {
	$AjaxRequest = true;
} else {
	$AjaxRequest = false;
	if(!defined("IQ_RPW_CORE_DIR")) { die(); }
}

if(func_iq_rpw_module_enable('pyramid')) {
	include IQ_RPW_PYRAMID_DIR.'/data/data_pyramid_user_reload.php';
} else {
	if($AjaxRequest) {
		if(!isset($_SERVER['HTTP_REFERER'])) { die(); }

		$iPostItemID = 0;
		if(isset($_POST['ItemID'])) {
			$iPostItemID = (int)sanitize_text_field($_POST['ItemID']);
		}

		##################
		##### ACCESS #####
		##################
		if ( !is_user_logged_in() ) {
			die();
		}

		$cur_user_id = get_current_user_id();
		if($cur_user_id <= 0) {
			die();
		}
		$user_obj = get_userdata( $cur_user_id );

		if(!$user_obj) {
			die();
		}
		$roles_arr = (array)$user_obj->roles;

		$need_role = 'administrator';
		if(!in_array($need_role, $roles_arr)) {
			die();
		}

		if(!isset($cIQ_RPW_ReferralClass)) {
			require_once IQ_RPW_CORE_DIR.'/includes/inc_referral_class.php';
			$cIQ_RPW_ReferralClass = new IQ_RPW_ReferralClass();
		}

		$SQL_Search = "
			AND
				a.`uid` = '".$iPostItemID."'
		";
		$users = $cIQ_RPW_ReferralClass->getList([], $SQL_Search, []);
		$q = [];
		if($users) {
			foreach($users AS $data) {
				$q = $data;
				break;
			}
		}
		if(!$q) {
			die();
		}

		$iPercent = 0;
		$settings_arr = $cIQ_RPW_ReferralClass->get_settings();
		if($settings_arr && isset($settings_arr['percent_def']) && (int)$settings_arr['percent_def']) {
			$iPercent = (int)$settings_arr['percent_def'];
		}
		
		$woo_curr_p = func_iq_rpw_in_protect(get_woocommerce_currency_symbol(), 'curr');
	}

	?>
	<tr id="<?php echo esc_attr( 'uid_block_' . $q['uid'] ); ?>">
		<td data-label="<?php echo esc_html__('User', 'iq-referral-program-for-woocommerce'); ?>">
			<?php echo esc_html($q['user_login']); ?>
		</td>
		<td data-label="<?php echo esc_html__('Code', 'iq-referral-program-for-woocommerce'); ?>">
			<div class="iq_ref_flexbox_st iq_ref_jleft_force iq_ref_flex_gap_2 iq_ref_flexbox_vc">
				<a href="#" onclick="IQ_RPW_WsCodeUpdate(<?php echo esc_js( $q['uid'] ); ?>, 0);return false;">
					<span class="dashicons dashicons-update cursor_hover"></span>
				</a>
				<div>
					<?php echo esc_html($q['ref_code']); ?>
				</div>
			</div>
		</td>
		<td data-label="<?php echo esc_html__('Code', 'iq-referral-program-for-woocommerce'); ?>">
			<div class="iq_ref_flexbox_st iq_ref_jleft_force iq_ref_flex_gap">
				<a href="#" onclick="IQ_RPW_BalanceUpdate(<?php echo esc_js($q['uid']); ?>);return false;">
					<span class="dashicons dashicons-edit cursor_hover"></span>
				</a>
				<div>
					<span class="iq_ref_sum_txt">
						<ul class="iq_ref_ulcl">
							<?php
							$balance_arr = json_decode($q['ref_balance_json'], true);
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
								<li class="iq_ref_li_clear iq_ref_nowrap">
									<?php echo esc_html( func_iq_rpw_raz_float($val, 2) . ' ' . $curr ); ?>
								</li>
								<?php
							}
							?>
						</ul>
					</span>
				</div>
			</div>
		</td>
		<td data-label="<?php echo esc_html__('Invited', 'iq-referral-program-for-woocommerce'); ?>">
			<?php
			if($q['ref_father_uid']) {
				$ref_father_uid_data = get_userdata( $q['ref_father_uid'] );
				$ref_father_uid_data = (array)$ref_father_uid_data;
				if(!$ref_father_uid_data || !isset($ref_father_uid_data['data'])) {
					echo esc_html('-');
				} else {
					echo esc_html($ref_father_uid_data['data']->user_login);
				}
			} else {
				echo esc_html('-');
			}
			?>
		</td>
		<td data-label="<?php echo esc_html__('Shopping', 'iq-referral-program-for-woocommerce'); ?>">
			<?php echo esc_html($q['ref_shopping']); ?>
		</td>
		<td data-label="<?php echo esc_html__('Sales', 'iq-referral-program-for-woocommerce'); ?>">
			<?php echo esc_html($q['ref_sell']); ?>
		</td>
		<td data-label="<?php echo esc_html__('Turnover', 'iq-referral-program-for-woocommerce'); ?>">
			<ul class="iq_ref_ulcl">
				<?php
				$turnover_arr = json_decode($q['turnover_json'], true);
				if(!array_key_exists($woo_curr_p, $turnover_arr)) {
					$turnover_arr[$woo_curr_p] = 0;
				}
				
				foreach($turnover_arr AS $curr_p => $val) {
					if($curr_p != $woo_curr_p) {
						if(!$val) {
							continue;
						}
					}
					$curr = func_iq_rpw_out_protect($curr_p, 'curr');
					?>
					<li class="iq_ref_li_clear iq_ref_nowrap">
						<?php echo esc_html( func_iq_rpw_number_converter($val) . ' ' . $curr ); ?>
					</li>
					<?php
				}
				?>
			</ul>
		</td>
		<td data-label="<?php echo esc_html__('Visits', 'iq-referral-program-for-woocommerce'); ?>">
			<?php echo esc_html(func_iq_rpw_number_converter($q['ref_visits'])); ?>
		</td>
		<td data-label="<?php echo esc_html__('Percent', 'iq-referral-program-for-woocommerce'); ?>" class="iq_ref_block_centered_tbl">
			<?php
				echo esc_html($iPercent . '%');
			?>
		</td>
		<td data-label="<?php echo esc_html__('Enable', 'iq-referral-program-for-woocommerce'); ?>" class="iq_ref_block_centered_tbl">
			<select id="<?php echo esc_attr( 'list_status_' . $q['uid'] ); ?>" class="select iq_ref_select_default block_centered" onchange="IQ_RPW_StatusChange(<?php echo esc_js($q['uid']); ?>);">
				<?php
				$iDef = $q['ref_enable'];
				for($i = 0; $i <= 1; $i++) {
					if($i == $iDef) {
						$szDef = 'selected';
					} else {
						$szDef = '';
					}

					if($i) {
						$str = esc_html__('Enable', 'iq-referral-program-for-woocommerce');
					} else {
						$str = esc_html__('Disable', 'iq-referral-program-for-woocommerce');
					}
					?>
					<option value="<?php echo esc_attr($i); ?>" <?php echo esc_attr($szDef); ?>>
						<?php echo esc_html($str); ?>
					</option>
					<?php
				}
				?>
			</select>
		</td>
	</tr>
	<?php
}
?>