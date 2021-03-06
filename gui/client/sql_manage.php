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
$template = 'sql_manage.tpl';

$count = -1;

// common page data.

// check User sql permission
if (isset($_SESSION['sql_support']) && $_SESSION['sql_support'] == "no") {
	user_goto('index.php');
}


// dynamic page data.

gen_db_list($tpl, $sql, $_SESSION['user_id']);

// static page messages.
gen_logged_from($tpl);

check_permissions($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'			=> tr('ispCP - Client/Manage SQL'),
		'TR_MANAGE_SQL'			=> tr('Manage SQL'),
		'TR_DELETE'				=> tr('Delete'),
		'TR_DATABASE'			=> tr('Database Name and Users'),
		'TR_CHANGE_PASSWORD'	=> tr('Change password'),
		'TR_ACTION'				=> tr('Action'),
		'TR_PHP_MYADMIN'		=> tr('phpMyAdmin'),
		'TR_DATABASE_USERS'		=> tr('Database users'),
		'TR_ADD_USER'			=> tr('Add SQL user'),
		'TR_EXECUTE_QUERY'		=> tr('Execute query'),
		'TR_CHANGE_PASSWORD'	=> tr('Change password'),
		'TR_LOGIN_PMA'			=> tr('Login phpMyAdmin'),
		'TR_MESSAGE_DELETE'		=> tr('This database will be permanently deleted. This process cannot be recovered. All users linked to this database will also be deleted if not linked to another database. Are you sure you want to delete %s?', true, '%s')
	)
);

gen_client_mainmenu($tpl, 'main_menu_manage_sql.tpl');
gen_client_menu($tpl, 'menu_manage_sql.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

// page functions.

/**
 * @param ispCP_TemplateEngine $tpl
 * @param ispCP_Database $sql
 * @param int $db_id
 */
function gen_db_user_list($tpl, $sql, $db_id) {

	global $count;

	$query = "
		SELECT
			`sqlu_id`, `sqlu_name`
		FROM
			`sql_user`
		WHERE
			`sqld_id` = ?
		ORDER BY
			`sqlu_name`
	";

	$rs = exec_query($sql, $query, $db_id);

	$users = array();
	if ($rs->recordCount() > 0) {
		while (!$rs->EOF) {
			$count++;
			$user_id = $rs->fields['sqlu_id'];
			$user_mysql = $rs->fields['sqlu_name'];
			$users[] =
				array(
					'DB_USER'	=> tohtml($user_mysql),
					'DB_USER_JS'=> tojs($user_mysql),
					'USER_ID'	=> $user_id
				);
			$rs->moveNext();
		}
	}
	$tpl->append( 'DB_USERLIST', $users );
	return count($users);
}

/**
 * @param ispCP_TemplateEngine $tpl
 * @param ispCP_Database $sql
 * @param int $user_id
 */
function gen_db_list($tpl, $sql, $user_id) {

	$dmn_id = get_user_domain_id($sql, $user_id);

	$query = "
		SELECT
			`sqld_id`, `sqld_name`
		FROM
			`sql_database`
		WHERE
			`domain_id` = ?
		ORDER BY
			`sqld_name`
	";

	$rs = exec_query($sql, $query, $dmn_id);

	if ($rs->recordCount() == 0) {
		set_page_message(tr('Database list is empty!'), 'notice');
		$tpl->assign('DB_LIST', '');
	} else {
		while (!$rs->EOF) {
			$db_id = $rs->fields['sqld_id'];
			$db_name = $rs->fields['sqld_name'];
			$num = gen_db_user_list($tpl, $sql, $db_id);
			$tpl->append(
				array(
					'DB_ID'			=> $db_id,
					'DB_NAME'		=> tohtml($db_name),
					'DB_NAME_JS'	=> tojs($db_name),
					'DB_MSG'		=> $num ? '' : tr('Database user list is empty!')
				)
			);
			$rs->moveNext();
		}
	}
}
?>
