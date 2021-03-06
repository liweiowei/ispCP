<?php
/**
 * ispCP ω (OMEGA) a Virtual Hosting Control System
 *
 * @copyright 	2001-2006 by moleSoftware GmbH
 * @copyright 	2006-2011 by ispCP | http://isp-control.net
 * @version 	SVN: $Id$
 * @link 		http://isp-control.net
 * @author 		ispCP Team
 *
 * @license
 * The contents of this file are subject to the Mozilla Public License
 * Version 1.1 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS"
 * basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
 * License for the specific language governing rights and limitations
 * under the License.
 *
 * The Original Code is "VHCS - Virtual Hosting Control System".
 *
 * The Initial Developer of the Original Code is moleSoftware GmbH.
 * Portions created by Initial Developer are Copyright (C) 2001-2006
 * by moleSoftware GmbH. All Rights Reserved.
 * Portions created by the ispCP Team are Copyright (C) 2006-2011 by
 * isp Control Panel. All Rights Reserved.
 */

require '../include/ispcp-lib.php';

check_login(__FILE__);

$cfg = ispCP_Registry::get('Config');

$tpl = ispCP_TemplateEngine::getInstance();
$template = 'cronjobs_edit.tpl';

// static page messages
gen_logged_from($tpl);

check_permissions($tpl);

if (isset($_GET['cron_id']) && is_numeric($_GET['cron_id'])) {
	update_cron_job($tpl, $sql, $_GET['cron_id']);
}
gen_cron_job($tpl, $sql, $_SESSION['user_id']);

$tpl->assign(
	array(
		'TR_PAGE_TITLE' => tr('ispCP - Client/Cronjob Manager'),
		'TR_CRON_MANAGER' => tr('Cronjob Manager'),
		'TR_EDIT_CRONJOB' => tr('Edit Cronjob'),
		'TR_NAME' => tr('Name'),
		'TR_DESCRIPTION' => tr('Description'),
		'TR_ACTIVE' => tr('Active'),
		'YES' => tr('Yes'),
		'NO' => tr('No'),
		'TR_CRONJOB' => tr('Cronjob'),
		'TR_COMMAND' => tr('Command to run:'),
		'TR_MIN' => tr('Minute(s):'),
		'TR_HOUR' => tr('Hour(s):'),
		'TR_DAY' => tr('Day(s):'),
		'TR_MONTHS' => tr('Month(s):'),
		'TR_WEEKDAYS' => tr('Weekday(s):'),
		'TR_UPDATE' => tr('Update'),
		'TR_CANCEL' => tr('Cancel'),
	)
);

gen_client_mainmenu($tpl, 'main_menu_webtools.tpl');
gen_client_menu($tpl, 'menu_webtools.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

/**
 * @todo Implement this function
 */
function update_cron_job($tpl, $sql, $cron_id) {
} // End of update_cron_job()

/**
 * @todo Implement this function
 */
function gen_cron_job($tpl, $sql, $user_id) {
} // End of gen_cron_job()
?>