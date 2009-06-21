<?php
/**
 * ispCP ω (OMEGA) a Virtual Hosting Control System
 *
 * @copyright	2001-2006 by moleSoftware GmbH
 * @copyright	2006-2009 by ispCP | http://isp-control.net
 * @version		SVN: $Id$
 * @link		http://isp-control.net
 * @author		ispCP Team
 *
 * @license
 *   This program is free software; you can redistribute it and/or modify it under
 *   the terms of the MPL General Public License as published by the Free Software
 *   Foundation; either version 1.1 of the License, or (at your option) any later
 *   version.
 *   You should have received a copy of the MPL Mozilla Public License along with
 *   this program; if not, write to the Open Source Initiative (OSI)
 *   http://opensource.org | osi@opensource.org
 */

require '../include/ispcp-lib.php';
check_login(__FILE__);

if (!isset($_GET['domain_id'])) {
	user_goto('manage_users.php');
}

if (!is_numeric($_GET['domain_id'])) {
	user_goto('manage_users.php');
}

$domain_id = $_GET['domain_id'];

$query = <<<SQL_QUERY
	SELECT
		`domain_name`,
		`domain_status`
	FROM
		`domain`
	WHERE
		`domain_id` = ?
SQL_QUERY;

$rs = exec_query($sql, $query, array($domain_id));

$location = 'admin';

if ($rs->fields['domain_status'] == Config::get('ITEM_OK_STATUS')) {
	$action = "disable";
	change_domain_status(&$sql, $domain_id, $rs->fields['domain_name'], $action, $location);
} else if ($rs->fields['domain_status'] == Config::get('ITEM_DISABLED_STATUS')) {
	$action = "enable";
	change_domain_status(&$sql, $domain_id, $rs->fields['domain_name'], $action, $location);
} else {
	user_goto('manage_users.php');
}