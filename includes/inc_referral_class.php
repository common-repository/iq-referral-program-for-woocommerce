<?php
// Restrict
if(!defined('ABSPATH') || !defined('IQ_RPW_CORE_DIR')) {
	die();
}

class IQ_RPW_ReferralClass {

	/**
	 * Getting a list of partners from the database
	 * @param array $args Args list
	 * @since 1.0.0
	 * @return array
	 */
	function getList($args = [], $search = '', $filters = []) {
        $sql_search = '';
        $sql_limit = '';
		if($search) {
			$sql_search .= "
                {$search}
            ";
		}
		
	    if($args) {
            $sql_limit .= "
                LIMIT 
                    {$args['offset']}, {$args['number']}
            ";
        }
		
        $sql_order = "
			ORDER BY
				a.`uid` ASC
		";
		
		if($filters) {
			foreach($filters AS $name => $order) {
				$sql_order = "
					ORDER BY
						a.`".$name."` ".$order."
				";
			}
		}
		
		global $wpdb;
		$sql = "
			SELECT
				a.*,
			    u.`user_login`
			FROM
				`{$wpdb->prefix}iq_rpw_users` a
			INNER JOIN
				`{$wpdb->prefix}users` u
			ON
				a.`uid` = u.`ID`
			WHERE
				a.`uid` > 0
				{$sql_search}
				{$sql_order}
				{$sql_limit}
		";

		$results = $wpdb->get_results(
			$wpdb->prepare($sql),
			ARRAY_A
		);
		return $results;
	}

	/**
	 * Getting a list logs from the database
	 * @param array $args Args list
	 * @since 1.0.0
	 * @return array
	 */
	function getLogs($args = [], $search = '') {
        $sql_search = '';
        $sql_limit = '';
		if($search) {
			$sql_search .= "
                AND 
                    {$search}
            ";
		}
	    if($args) {
            $sql_limit .= "
                LIMIT 
                    {$args['offset']}, {$args['number']}
            ";
        }
		global $wpdb;
		$sql = "
			SELECT
				a.*,
			    f.`user_login` AS `father_login`,
			    t.`user_login` AS `target_login`
			FROM
				`{$wpdb->prefix}iq_rpw_logs` a
			INNER JOIN
				`{$wpdb->prefix}users` f
			ON
				a.`uid_father` = f.`ID`
			INNER JOIN
				`{$wpdb->prefix}users` t
			ON
				a.`uid_target` = t.`ID`
			WHERE
			    a.`id` > 0
			ORDER BY
				a.`id` DESC
			    {$sql_limit}
		";

		$results = $wpdb->get_results(
			$wpdb->prepare($sql),
			ARRAY_A
		);
		return $results;
	}

	/**
	 * Getting a actons logs from the database
	 * @param array $args Args list
	 * @since 1.0.0
	 * @return array
	 */
	function getActionsLogs($args = [], $search = '') {
        $sql_search = '';
        $sql_limit = '';
		if($search) {
			$sql_search .= "
                AND 
                    {$search}
            ";
		}
	    if($args) {
            $sql_limit .= "
                LIMIT 
                    {$args['offset']}, {$args['number']}
            ";
        }
		global $wpdb;
		$sql = "
			SELECT
				a.*,
			    u.`user_login` AS `user_login`
			FROM
				`{$wpdb->prefix}iq_rpw_actions_logs` a
			INNER JOIN
				`{$wpdb->prefix}users` u
			ON
				a.`uid` = u.`ID`
			WHERE
			    a.`id` > 0
			    {$sql_search}
			ORDER BY
				a.`id` DESC
			    {$sql_limit}
		";

		$results = $wpdb->get_results(
			$wpdb->prepare($sql),
			ARRAY_A
		);
		return $results;
	}
	
	function writeActionsLogs($uid, $log) {
        $iCurrentTime = time();
		global $wpdb;
		$sql = "
			INSERT INTO `{$wpdb->prefix}iq_rpw_actions_logs`
				(`uid`,
				`log`,
				`date`)
			VALUES
				(%d,
				%s,
				%d);
		";
		$results = $wpdb->query($wpdb->prepare($sql, $uid, $log, $iCurrentTime));
		return $results;
	}
	
	/**
	 * Getting withdraw from the database
	 * @param array $args Args list
	 * @since 1.0.0
	 * @return array
	 */
	function getWithdrawApps($args = [], $search = '') {
        $sql_search = '';
        $sql_limit = '';
		if($search) {
			$sql_search .= "
                {$search}
            ";
		}
	    if($args) {
            $sql_limit .= "
                LIMIT 
                    {$args['offset']}, {$args['number']}
            ";
        }
		global $wpdb;
		$sql = "
			SELECT
				a.*,
			    u.`user_login` AS `user_login`,
			    s.`name` AS `system_name`
			FROM
				`{$wpdb->prefix}iq_rpw_withdraw_forms` a
			INNER JOIN
				`{$wpdb->prefix}users` u
			ON
				a.`uid` = u.`ID`
			LEFT JOIN
				`{$wpdb->prefix}iq_rpw_withdraw_systems` s
			ON
				a.`system_id` = s.`id`
			WHERE
			    a.`id` > 0
			    {$sql_search}
			ORDER BY
				a.`date` DESC
			    {$sql_limit}
		";

		$results = $wpdb->get_results(
			$wpdb->prepare($sql),
			ARRAY_A
		);
		return $results;
	}

	/**
	 * Create plugin database
	 * @since 1.0.0
	 * @return boolean
	 */
	function createDB() {
		global $wpdb;
		$sql = "
			CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}iq_rpw_users` (
				`uid` int(20) NOT NULL DEFAULT 0,
				`ref_uid` int(20) NOT NULL DEFAULT 0,
				`ref_visits` int(20) NOT NULL DEFAULT 0,
				`ref_balance_json` text(0) DEFAULT '[]',
				`ref_sell` int(20) NOT NULL DEFAULT 0,
				`turnover_json` text(0) NOT NULL DEFAULT '[]',
				`ref_shopping` int(20) NOT NULL DEFAULT 0,
				`ref_percent` tinyint(4) NOT NULL DEFAULT 0,
				`ref_code` varchar(255) NOT NULL,
				`ref_father_uid` int(20) NOT NULL DEFAULT 0,
				`ref_enable` tinyint(2) NOT NULL DEFAULT 1,
			PRIMARY KEY uid (`uid`)
		);";
		$wpdb->query($wpdb->prepare($sql));

		// settings
        $sql = "
                CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}iq_rpw_settings` (
                        `key` varchar (32) NOT NULL DEFAULT '',
                        `params_json` text(0) NOT NULL DEFAULT '',
                    PRIMARY KEY `key` (`key`)
                );
        ";

        $index = $wpdb->query($wpdb->prepare($sql));
        if($index) {
            $sql = "
			SELECT
				*
			FROM
				`{$wpdb->prefix}iq_rpw_settings`
			WHERE
				`key` = 'settings';
		";
            $result = $wpdb->get_row($wpdb->prepare($sql));
            if(!$result) {
                $params_def_arr = [

                ];

                $sql = "
				INSERT INTO `{$wpdb->prefix}iq_rpw_settings`
					(`key`,
				    `params_json`)
				VALUES
				    ('settings',
				    %s);
			";
                $wpdb->query($wpdb->prepare($sql, json_encode($params_def_arr)));
            }
        }

        // logs
        $sql = "
            CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}iq_rpw_logs` (
                    `id` int (20) NOT NULL AUTO_INCREMENT,
                    `uid_father` int(20) NOT NULL DEFAULT 0,
                    `uid_target` int(20) NOT NULL DEFAULT 0,
                    `sum` double DEFAULT 0,
                    `currency` varchar(32) NOT NULL DEFAULT '',
                    `up` tinyint(2) NOT NULL DEFAULT 1,
                    `date` int(20) NOT NULL DEFAULT 0,
                PRIMARY KEY `id` (`id`)
            );
        ";
        $index = $wpdb->query($wpdb->prepare($sql));

        // action logs
        $sql = "
            CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}iq_rpw_actions_logs` (
                    `id` int (20) NOT NULL AUTO_INCREMENT,
                    `uid` int(20) NOT NULL DEFAULT 0,
                    `log` text(0) NOT NULL DEFAULT '',
                    `date` int(20) NOT NULL DEFAULT 0,
                PRIMARY KEY `id` (`id`)
            );
        ";
        $index = $wpdb->query($wpdb->prepare($sql));
		
        // withdraw_forms
        $sql = "
            CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}iq_rpw_withdraw_status` (
                    `id` int (20) NOT NULL DEFAULT 0,
                    `name` varchar(64) NOT NULL DEFAULT '',
                    `classes` varchar(255) NOT NULL DEFAULT '',
                PRIMARY KEY `id` (`id`)
            );
        ";
        $index = $wpdb->query($wpdb->prepare($sql));
		if($index) {
			$sql = "
				INSERT INTO `{$wpdb->prefix}iq_rpw_withdraw_status` VALUES ('0', 'In process...', 'mo_ref_status_ws_process');
			";
			$wpdb->query($wpdb->prepare($sql));
			
			$sql = "
				INSERT INTO `{$wpdb->prefix}iq_rpw_withdraw_status` VALUES ('1', 'Completed', 'mo_ref_status_ws_success');
			";
			$wpdb->query($wpdb->prepare($sql));
			
			$sql = "
				INSERT INTO `{$wpdb->prefix}iq_rpw_withdraw_status` VALUES ('2', 'Error', 'mo_ref_status_ws_err');
			";
			$wpdb->query($wpdb->prepare($sql));
		}
		
        // withdraw_forms
        $sql = "
            CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}iq_rpw_withdraw_forms` (
                    `id` int (20) NOT NULL AUTO_INCREMENT,
                    `uid` int(20) NOT NULL DEFAULT 0,
                    `system_id` int(5) NOT NULL DEFAULT 0,
                    `requisites` varchar(100) NOT NULL DEFAULT '',
                    `sum` double DEFAULT 0,
                    `comission` int(5) DEFAULT 0,
                    `currency` varchar(32) DEFAULT '',
                    `comment` text(0) DEFAULT '',
                    `comment_admin` text(0) DEFAULT '',
                    `status` tinyint(2) NOT NULL DEFAULT 0,
                    `date` int(20) NOT NULL DEFAULT 0,
                    `do_date` int(20) NOT NULL DEFAULT 0,
                PRIMARY KEY `id` (`id`)
            );
        ";
        $index = $wpdb->query($wpdb->prepare($sql));

        // withdraw_systems
        $sql = "
            CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}iq_rpw_withdraw_systems` (
                    `id` int (20) NOT NULL AUTO_INCREMENT,
                    `name` varchar(255) NOT NULL DEFAULT '',
                    `min` double DEFAULT 0,
                    `commision` int(4) DEFAULT 0,
                    `enable` tinyint(2) NOT NULL DEFAULT 1,
                PRIMARY KEY `id` (`id`)
            );
        ";
        $index = $wpdb->query($wpdb->prepare($sql));
        if($index) {
            return true;
        }
		return false;
	}

	/**
	 * Insert to log db
	 * @since 1.0.0
	 * @return array
	 */
	function writeLog($uid_father, $uid_target, $sum, $currency, $is_up = 1) {
		global $wpdb;
		$sql = "
			INSERT INTO `{$wpdb->prefix}iq_rpw_logs`
                (`uid_father`,
                 `uid_target`,
                 `sum`,
                 `currency`,
                 `up`,
                 `date`)
            VALUES
                ('%d',
                 '%d',
                 '%f',
                 '%s',
                 '%d',
                 '%d');
		";

        $iCurrentTime = time();
        $index = $wpdb->query($wpdb->prepare($sql,
                        $uid_father,
                        $uid_target,
                        $sum,
                        $currency,
                        $is_up,
                        $iCurrentTime));
		if($index) {
			return true;
		}
		return false;
	}

	/**
	 * Get partner info from db
	 * @since 1.0.0
	 * @return array
	 */
	function getInfo($uid) {
		global $wpdb;
		$sql = "
			SELECT
				*
			FROM
				`{$wpdb->prefix}iq_rpw_users`
			WHERE
				`uid` = %d
		";
		$result = $wpdb->get_row($wpdb->prepare($sql, $uid), ARRAY_A);
		return $result;
	}

	/**
	 * Get father info from db
	 * @since 1.0.0
	 * @return integer
	 */
	function getFather($code) {
		global $wpdb;
		$sql = "
			SELECT
				`uid`
			FROM
				`{$wpdb->prefix}iq_rpw_users`
			WHERE
				`ref_code` = %s
		";
		$result = $wpdb->get_row($wpdb->prepare($sql, $code), ARRAY_A);
		if($result && isset($result['uid'])) {
			return (int)$result['uid'];
		}
		return 0;
	}

	/**
	 * Set father info from db
	 * @since 1.0.0
	 * @return boolean
	 */
	function setFather($uid, $father_uid) {
		global $wpdb;
		$sql = "
			UPDATE
				`{$wpdb->prefix}iq_rpw_users`
			SET 
				`ref_father_uid` = %d
			WHERE
				`uid` = %d
		";
		$index = $wpdb->query($wpdb->prepare($sql, $father_uid, $uid));
		if($index) {
			return true;
		}
		return false;
	}

	/**
	 * Remove plugin database
	 * @since 1.0.0
	 * @return boolean
	 */
	function removeDB() {
		global $wpdb;
		$sql = "
			DROP TABLE `{$wpdb->prefix}iq_rpw_users`;
		";
		$index = $wpdb->query($wpdb->prepare($sql));


        $sql = "
            DROP TABLE `{$wpdb->prefix}iq_rpw_settings`;
        ";
        $index = $wpdb->query($wpdb->prepare($sql));

        $sql = "
            DROP TABLE `{$wpdb->prefix}iq_rpw_withdraw_forms`;
        ";
        $index = $wpdb->query($wpdb->prepare($sql));

        $sql = "
            DROP TABLE `{$wpdb->prefix}iq_rpw_withdraw_systems`;
        ";
        $index = $wpdb->query($wpdb->prepare($sql));

        $sql = "
            DROP TABLE `{$wpdb->prefix}iq_rpw_logs`;
        ";
        $index = $wpdb->query($wpdb->prepare($sql));

        $sql = "
            DROP TABLE `{$wpdb->prefix}iq_rpw_actions_logs`;
        ";
        $index = $wpdb->query($wpdb->prepare($sql));

        $sql = "
            DROP TABLE `{$wpdb->prefix}iq_rpw_withdraw_levels`;
        ";
        $index = $wpdb->query($wpdb->prepare($sql));

        $sql = "
            DROP TABLE `{$wpdb->prefix}iq_rpw_withdraw_status`;
        ";
        $index = $wpdb->query($wpdb->prepare($sql));
        if($index) {
            return true;
        }
		return false;
	}

	/**
	 * Remove plugin database
	 * @since 1.0.0
	 * @return boolean
	 */
	function updateVisit($iFathetUID, $iCount = 1) {
		global $wpdb;
		$sql = "
			UPDATE
				`{$wpdb->prefix}iq_rpw_users`
			SET
				`ref_visits` = `ref_visits`+{$iCount}
			WHERE
				`uid` = %d
		";
		$index = $wpdb->query($wpdb->prepare($sql, $iFathetUID));
		if($index) {
			return true;
		}
		return false;
	}

	/**
	 * Processed plugin
	 * @since 1.0.0
	 * @return boolean
	 */
	function processed($orderData) {
		$orderData['data'] = (array)$orderData['data'];
		if(!(float)$orderData['data']['total']) {
			return false;
		}
		global $wpdb;

		$iFatherID = 0;
		if((int)$orderData['data']['customer_id']) {
			$sql = "
				SELECT
					`ref_father_uid`
				FROM
					`{$wpdb->prefix}iq_rpw_users`
				WHERE
					`uid` = %d
			";
			$resultObj = $wpdb->get_row($wpdb->prepare($sql, (int)$orderData['data']['customer_id']));

			if($resultObj) {
				$iFatherID = $resultObj->ref_father_uid;
			}
		} else {
			
		}
		if($iFatherID <= 0) {
			if(isset($_COOKIE['ref_father']) && (int)$_COOKIE['ref_father']) {
				$iFatherID = (int)$_COOKIE['ref_father'];
				if($iFatherID && (int)$orderData['data']['customer_id'] != $iFatherID) {
					$sql = "
						UPDATE
							`{$wpdb->prefix}iq_rpw_users`
						SET
							`ref_father_uid` = %d
						WHERE
							`uid` = %d
					";
					$wpdb->query($wpdb->prepare($sql, $iFatherID, (int)$orderData['data']['customer_id']));
				}
			}
		}

		if($iFatherID <= 0) {
			return false;
		}
		if($iFatherID == (int)$orderData['data']['customer_id']) {
			// yourself
			return false;
		}
		
		$sql = "
			SELECT
				*
			FROM
				`{$wpdb->prefix}iq_rpw_users`
			WHERE
				`uid` = %d
		";
		$resultObj = $wpdb->get_row($wpdb->prepare($sql, $iFatherID));
		if(!$resultObj->ref_enable) {
			return false;
		}
		$woo_curr_p = func_iq_rpw_in_protect(get_woocommerce_currency_symbol(), 'curr');
		
		$settings_arr = $this->get_settings();
		
		$turnover_arr = json_decode($resultObj->turnover_json, true);
		if(!array_key_exists($woo_curr_p, $turnover_arr)) {
			$turnover_arr[$woo_curr_p] = 0;
		}
		
		$iPercent = 0;
		if(func_iq_rpw_module_enable('pyramid')) {
			include IQ_RPW_PYRAMID_DIR.'/data/data_pyramid_processed.php';
		} else {
			if($settings_arr && isset($settings_arr['percent_def']) && (int)$settings_arr['percent_def']) {
				$iPercent = (int)$settings_arr['percent_def'];
			}
		}
		if(!$iPercent) {
			return false;
		}
		
		$fTotal = (float)$orderData['data']['total'];
		$ref_sum = $iPercent / 100 * (float)$orderData['data']['total'];
		if(!$ref_sum) {
			return false;
		}
		
		
		$balance_arr = json_decode($resultObj->ref_balance_json, true);
		if(!array_key_exists($woo_curr_p, $balance_arr)) {
			$balance_arr[$woo_curr_p] = 0;
		}
		$balance_arr[$woo_curr_p] += $ref_sum;
		$turnover_arr[$woo_curr_p] += $fTotal;
		
		$sql = "
			UPDATE
				`{$wpdb->prefix}iq_rpw_users`
			SET
			    `ref_balance_json` = %s,
			    `ref_sell` = `ref_sell`+1,
			    `turnover_json` = %s
			WHERE
				`uid` = %d
		";
		$wpdb->query($wpdb->prepare($sql, 
								json_encode($balance_arr), 
								json_encode($turnover_arr), 
								$iFatherID));

		if((int)$orderData['data']['customer_id']) {
			$sql = "
				UPDATE
					`{$wpdb->prefix}iq_rpw_users`
				SET
				    `ref_shopping` = `ref_shopping`+1
				WHERE
					`uid` = %d
			";
			$wpdb->query($wpdb->prepare($sql, (int)$orderData['data']['customer_id']));
		}

		// logs
        $index = $this->writeLog($iFatherID, (int)$orderData['data']['customer_id'], $ref_sum, $orderData['data']['currency'], 1);
		if(!$index) {
		    return false;
        }
		return true;
	}

	function createPartner($user_id) {
		$balance_arr = [];
		$woo_curr_p = func_iq_rpw_in_protect(get_woocommerce_currency_symbol(), 'curr');
		$balance_arr[$woo_curr_p] = 0;

		$settings_arr = $this->get_settings();
		if(!$settings_arr || !isset($settings_arr['percent_def'])) {
			$settings_arr['percent_def'] = 0;
		}
		if(!$settings_arr || !isset($settings_arr['status_def'])) {
			$settings_arr['status_def'] = 0;
		}
		
		global $wpdb;
		$ref_code = $user_id.func_iq_rpw_generatePassword(8);
		$sql = "
			INSERT INTO `{$wpdb->prefix}iq_rpw_users`
				(`uid`,
				`ref_uid`,
				`ref_visits`,
				`ref_balance_json`,
				`ref_shopping`,
				`ref_percent`,
				`ref_code`,
				`ref_enable`)
			VALUES
				(%d,
				0,
				0,
				%s,
				0,
				%d,
				%s,
				%d)
		";
		$index = $wpdb->query($wpdb->prepare($sql,
									$user_id,
									json_encode($balance_arr),
									$settings_arr['percent_def'],
									$ref_code,
									$settings_arr['status_def']));
		if($index) {
			return true;
		}
		return false;
	}
	
	function getWithdrawSystems() {
		global $wpdb;
		
		$sql = "
			SELECT
				*
			FROM
				`{$wpdb->prefix}iq_rpw_withdraw_systems`;
		";
		$obj = $wpdb->get_results($wpdb->prepare($sql));
		return $obj;
	}
	
	function WithdrawChaangeStatus($form_id, $new_status, $comment_admin = '') {
		global $wpdb;
		
		$iCurrentTime = time();
		$sql = "
			UPDATE
				`{$wpdb->prefix}iq_rpw_withdraw_forms`
			SET
				`status` = %d,
				`do_date` = %d,
				`comment_admin` = %s
			WHERE
				`id` = %d
		";
		$index = $wpdb->query($wpdb->prepare($sql,
									$new_status,
									$iCurrentTime,
									$comment_admin,
									$form_id));
									
		return $index;
	}
	
	function WithdrawFormSave($user_id, $requisites, $system, $amount, $comission, $currency, $comment = '') {
		global $wpdb;
		
		$iCurrentTime = time();
		$sql = "
			INSERT INTO `{$wpdb->prefix}iq_rpw_withdraw_forms`
				(`uid`,
				`system_id`,
				`requisites`,
				`sum`,
				`comission`,
				`currency`,
				`comment`,
				`status`,
				`date`,
				`do_date`)
			VALUES
				(%d,
				%d,
				%s,
				%f,
				%d,
				%s,
				%s,
				0,
				%d,
				0);
		";
		
		$index = $wpdb->query($wpdb->prepare($sql,
									$user_id,
									$system,
									$requisites,
									$amount,
									$comission,
									$currency,
									$comment,
									$iCurrentTime));
									
		return $index;
	}
	
	function updateUserRow($uid, $sql_set) {
		global $wpdb;
		
		$sql = "
			UPDATE
				`{$wpdb->prefix}iq_rpw_users`
			SET
				{$sql_set}
			WHERE
				`uid` = %d
		";
		$index = $wpdb->query($wpdb->prepare($sql, $uid));
		return $index;
	}
	
	function updateUserBalance($uid, $balance_json) {
		global $wpdb;
		
		$sql = "
			UPDATE
				`{$wpdb->prefix}iq_rpw_users`
			SET
				`ref_balance_json` = %s
			WHERE
				`uid` = %d
		";
		$index = $wpdb->query($wpdb->prepare($sql, $balance_json, $uid));
		return $index;
	}
	
	function WithdrawChangeStatus($form_id, $status, $comment = '') {
		global $wpdb;
		
		if($status == 1) {
			$iCurrentTime = time();
		} else {
			$iCurrentTime = 0;
		}
		$sql = "
			UPDATE
				`{$wpdb->prefix}iq_rpw_withdraw_forms`
			SET
				`status` = %d,
				`comment_admin` = %s,
				`do_date` = %d
			WHERE
				`id` = %d
		";
		$index = $wpdb->query($wpdb->prepare($sql, 
									$status, 
									$comment,
									$iCurrentTime,									
									$form_id));
		return $index;
	}

	/**
	 * Update settings plugin database
	 * @since 1.0.0
	 * @return boolean
	 */
	function updateSettings($arr) {
		global $wpdb;
		$sql = "
				UPDATE
					`{$wpdb->prefix}iq_rpw_settings`
				SET
					`params_json` = %s
				WHERE
					`key` = 'settings'
			";
		$index = $wpdb->query($wpdb->prepare($sql, json_encode($arr)));
		if($index) {
			return true;
		}
		return true;
	}

	/**
	 * Get settings plugin database
	 * @since 1.0.0
	 * @return array
	 */
	function get_settings() {
		global $wpdb;
		$sql = "
				SELECT
					`params_json`
				FROM
					`{$wpdb->prefix}iq_rpw_settings`
				WHERE
					`key` = 'settings';
			";
		$result = $wpdb->get_row($wpdb->prepare($sql));
		if($result) {
			$result = (array)$result;
			return json_decode($result['params_json'], true);
		}
		return [];
	}

	/**
	 * Get partner info from db
	 * @since 1.0.0
	 * @return array
	 */
	function getWithdrawStatus() {
		global $wpdb;
		$sql = "
			SELECT
				*
			FROM
				`{$wpdb->prefix}iq_rpw_withdraw_status`
			ORDER BY
				`id` ASC
		";
		$result = $wpdb->get_results($wpdb->prepare($sql), ARRAY_A);
		
		$result_arr = [];
		if($result) {
			foreach($result AS $data) {
				$result_arr[(int)$data['id']] = $data;
			}
		}
		return $result_arr;
	}
}