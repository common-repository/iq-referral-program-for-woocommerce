<?php
// Restrict
if(!defined('ABSPATH') || !defined('IQ_RPW_CORE_DIR')) {
	die();
}
require_once dirname( __FILE__ ).'/inc_menu.php';
require_once dirname( __FILE__ ).'/inc_protect.php';

function func_iq_rpw_account_menu_items( $iq_rpw_menu_links ){
	$new_link = array( 'iq-referral' => esc_html__('Referral programm', 'iq-referral-program-for-woocommerce') );

	$iq_rpw_menu_links = array_slice( $iq_rpw_menu_links, 0, 2, true )
	              + $new_link
	              + array_slice( $iq_rpw_menu_links, 2, NULL, true );

	return $iq_rpw_menu_links;
}
function func_iq_rpw_woocommerce_account_partner() {
	include IQ_RPW_CORE_DIR . '/templates/iq_rpw_account.php';
}

function func_iq_rpw_woocommerce_order_status_changed( $order_id ){
	$orderObj = wc_get_order( $order_id );
	$order_arr = (array)$orderObj;

	global $wpdb;
	$data = (array)json_decode(str_replace('\u0000*\u0000', '', json_encode($order_arr)));

	$data = (array)$data;
	if($data && isset($data['data'])) {
		$data['data'] = (array)$data['data'];
		if($data['data']['status'] == 'completed') {
			// Order completed
			if(!isset($cIQ_RPW_ReferralClass)) {
				require_once IQ_RPW_CORE_DIR.'/includes/inc_referral_class.php';
				$cIQ_RPW_ReferralClass = new IQ_RPW_ReferralClass();
			}
			$cIQ_RPW_ReferralClass->processed($data);
		}
	}

	return $order_id;
}

function func_iq_rpw_action_payment_complete( $order_id ) {
	$order = wc_get_order( $order_id );
	$order_arr = (array)$order;

	global $wpdb;

	$data = (array)json_decode(str_replace('\u0000*\u0000', '', json_encode($order_arr)));

	$data = (array)$data;
	if($data && isset($data['data'])) {
		$data['data'] = (array)$data['data'];
		if($data['data']['status'] == 'completed' || $data['data']['status'] == 'processing') {
			// Order completed
			if(!isset($cIQ_RPW_ReferralClass)) {
				require_once IQ_RPW_CORE_DIR.'/includes/inc_referral_class.php';
				$cIQ_RPW_ReferralClass = new IQ_RPW_ReferralClass();
			}
			$cIQ_RPW_ReferralClass->processed($data);
		}
	}
	return $order_id;
}
function func_iq_rpw_num($float, $iNum, $zero = false) {
	$Value = number_format($float, $iNum, '.', '');
	if(!$zero) {
		$Value = rtrim(rtrim($Value, '0'), '.');
	}
	return $Value;
}

function func_iq_rpw_generatePassword($length = 8, $symb = false) {
	$chars = 'abdefhiknrstyzABDEFGHKNQRSTYZ23456789';
	if($symb) {
		$chars .= '!@#$%^*()-+=';
	}
	$iNumChars = strlen($chars);
	$string = '';
	for ($i = 0; $i < $length; $i++) {
		$string .= substr($chars, rand(1, $iNumChars) - 1, 1);
	}
	return $string;
}

function func_iq_rpw_settings_createDB() {
    global $wpdb;
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
        return true;
    }
    return false;
}

function func_iq_rpw_settings_get_count_letters($string) {
	$string = str_replace( "\r\n","\n", $string );
	return mb_strlen($string);
}
function func_iq_rpw_settings_string_to_short($string, $start, $ended='...', $length=null, $encoding=null) {
	if(func_iq_rpw_settings_get_count_letters($string) < $start) {
		return $string;
	}
    if ($encoding == null) $encoding = mb_internal_encoding();
    if ($length == null) {
        return mb_substr($string, 0, $start, $encoding) . $ended;
    }
    else {
        if($length < 0) $length = mb_strlen($string, $encoding) - $start + $length;
        return 
            mb_substr($string, 0, $start, $encoding) . $ended .
            mb_substr($string, $start + $length, mb_strlen($string, $encoding), $encoding);
    }
}

function func_iq_rpw_settings_short_text($str, $lenght = 30, $end = '') {
	if(func_iq_rpw_settings_get_count_letters($str) > $lenght) {
		$str = func_iq_rpw_settings_string_to_short($str, $lenght, $end);
	}
	return $str;
}

function func_iq_rpw_raz($str) {
	$str = number_format($str, 0, '', ' ');
	return $str;
}

function func_iq_rpw_raz_float($str, $iNum) {
	$str = number_format($str, $iNum, '.', ' ');
	return $str;
}

function func_iq_rpw_get_sign_json_array_protect($json_arr, $secret = IQ_RPW_SECRET_CODE) {
	$params1 = [
		'json' => $json_arr,
		'sole' => 'kgOxLL2zl60Ogb8G14gD8W7xjK9eqcvH',
		'code' => $secret,
	];	
	$hash1 = hash('sha256', join('{sign1_protect}', $params1));
	$hash2 = md5($json_arr . $secret . $hash1);
	$params3 = [
		'json' => $json_arr,
		'sole' => 'C9CmSZ0lvCF09ttg637SOcAzTcYXgnZr',
		'sole2' => 'e5OD3R2WtFc9JqNOLYoQL8yU6GwUXVmc',
	];	
	$hash3 = hash('sha256', join('{sign2_protect}', $params3));
	$hash4 = md5($json_arr . $secret . $hash3);
	$hash = $hash1.'-'.$hash2.'-'.$hash3.'-'.$hash4;
	return $hash;
}

/**
 * Get count referral requests
 * @since 1.0.0
 * @return int
 */
function func_iq_rpw_get_count_requests($status) {
    global $wpdb;
    $sql = "
			SELECT
				COUNT(*) AS `count`
			FROM
				`{$wpdb->prefix}iq_rpw_withdraw_forms`
			WHERE
				`status` = %d
		";
    $result = $wpdb->get_row($wpdb->prepare($sql, $status), ARRAY_A);
    return (int)$result['count'];
}

function func_iq_rpw_in_range($iNumber, $min, $max, $inclusive = false) {
	if(is_int($iNumber) && is_int($min) && is_int($max)) {
		return $inclusive ? ($iNumber >= $min && $iNumber <= $max) : ($iNumber > $min && $iNumber < $max);
	}
	return false;
}

function func_iq_rpw_number_converter($n, $precision = 1) {
    if ($n < 1000) {
        $n_format = number_format($n, 0);
        $suffix = '';
    } else {
        if ($n < 1000000) {
            $n_format = number_format($n / 1000, $precision);
            $suffix = 'K';
        } else {
            if ($n < 1000000000) {
                $n_format = number_format($n / 1000000, $precision);
                $suffix = 'M';
            } else {
                if ($n < 1000000000000) {
                    $n_format = number_format($n / 1000000000, $precision);
                    $suffix = 'B';
                } else {
                    $n_format = number_format($n / 1000000000000, $precision);
                    $suffix = 'T';
                }
                if ($precision > 0) {
                    $dotzero = '.' . str_repeat('0', $precision);
                    $n_format = str_replace($dotzero, '', $n_format);
                }
			}
		}
	}
    return $n_format . $suffix;
}

function func_iq_ref_removeDirectory($dir, $rootdir_del = true){
	if ($objs = glob($dir."/*")) {
		foreach($objs as $obj) {
			is_dir($obj) ? func_iq_ref_removeDirectory($obj) : unlink($obj);
		}
	}
	if($rootdir_del) {
		if(file_exists($dir)) {
			rmdir($dir);
		}
	}
	return true;
}

function func_iq_rpw_getAjaxDir()
{
    $plugins_path = str_replace(ABSPATH, "/" , WP_PLUGIN_DIR)."/";
    return $plugins_path.IQ_RPW_PLUGIN_DIR;
}

function func_iq_rpw_module_enable($name) {
	if(file_exists(IQ_RPW_CORE_DIR.'/modules/'.$name.'/'.$name.'.php')) {
		return true;
	}
	return false;
}

function func_iq_rpw_get_modules() {
	$arr = [];
	$dir = opendir(IQ_RPW_CORE_DIR.'/modules');
	while($file = readdir($dir)) {
	   if (is_dir(IQ_RPW_CORE_DIR.'/modules/'.$file) && $file != '.' && $file != '..') {
		   $arr[] = trim($file);
	   }
	}
	return $arr;
}
