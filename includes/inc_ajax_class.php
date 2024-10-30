<?php
// Restrict
if(!defined('ABSPATH') || !defined('IQ_RPW_CORE_DIR')) {
	die();
}

class IQ_RPW_AjaxClass {
	// ADMIN
	function admin_change_status() {
		include IQ_RPW_CORE_DIR.'/data/data_status_change.php';
		wp_die();
	}
	
	function admin_system_index() {
		include IQ_RPW_CORE_DIR.'/data/data_system_add.php';
		wp_die();
	}
	
	function admin_WsStatusIndex() {
		include IQ_RPW_CORE_DIR.'/data/data_ws_status_index.php';
		wp_die();
	}
	
	function admin_WsStatusDo() {
		include IQ_RPW_CORE_DIR.'/data/data_ws_status_do.php';
		wp_die();
	}
	
	function admin_WsItemReload() {
		include IQ_RPW_CORE_DIR.'/data/data_ws_item_reload.php';
		wp_die();
	}
	
	function admin_system_add_do() {
		include IQ_RPW_CORE_DIR.'/data/data_system_add_do.php';
		wp_die();
	}
	
	function admin_system_delete() {
		include IQ_RPW_CORE_DIR.'/data/data_system_delete.php';
		wp_die();
	}
	
	function admin_systems_load() {
		include IQ_RPW_CORE_DIR.'/templates/iq_rpw_settings_withdrawal_systems.php';
		wp_die();
	}
	
	function admin_ref_code_update() {
		include IQ_RPW_CORE_DIR.'/data/data_ws_code_update.php';
		wp_die();
	}
	
	function admin_user_reload() {
		include IQ_RPW_CORE_DIR.'/data/data_user_reload.php';
		wp_die();
	}
	
	function admin_BalanceUpdateDo() {
		include IQ_RPW_CORE_DIR.'/data/data_ref_balance_do.php';
		wp_die();
	}
	
	function admin_BalanceUpdate() {
		include IQ_RPW_CORE_DIR.'/data/data_ref_balance_pop.php';
		wp_die();
	}
	
	// WIZARD
	function admin_WizardImportUsers() {
		include IQ_RPW_WIZARD_PATH.'/data/data_wizard_import.php';
		wp_die();
	}
	function admin_WizardSettingsApply() {
		include IQ_RPW_WIZARD_PATH.'/data/data_wizard_settings_do.php';
		wp_die();
	}
	function admin_WizardWithdrawAddDo() {
		include IQ_RPW_WIZARD_PATH.'/data/data_wizard_withdraw_do.php';
		wp_die();
	}
	
	// WEB
	function web_ShowForm() {
		include IQ_RPW_CORE_DIR.'/data/data_form_pop.php';
		wp_die();
	}
	
	// PYRAMID
	function admin_PyramidLevelPop() {
		if(func_iq_rpw_module_enable('pyramid')) {
			include IQ_RPW_PYRAMID_DIR.'/data/data_pyramid_level_pop.php';
		}
		wp_die();
	}
	function admin_PyramidLevelDo() {
		if(func_iq_rpw_module_enable('pyramid')) {
			include IQ_RPW_PYRAMID_DIR.'/data/data_pyramid_level_do.php';
		}
		wp_die();
	}
	function admin_PyramidLevelItemReload() {
		if(func_iq_rpw_module_enable('pyramid')) {
			include IQ_RPW_PYRAMID_DIR.'/data/data_pyramid_level_item_reload.php';
		}
		wp_die();
	}
	function admin_PyramidProcChange() {
		if(func_iq_rpw_module_enable('pyramid')) {
			include IQ_RPW_PYRAMID_DIR.'/data/data_pyramid_proc_change.php';
		}
		wp_die();
	}
	function admin_PyramidLevelDelete() {
		if(func_iq_rpw_module_enable('pyramid')) {
			include IQ_RPW_PYRAMID_DIR.'/data/data_pyramid_level_delete.php';
		}
		wp_die();
	}
}