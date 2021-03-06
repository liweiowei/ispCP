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
$template = 'mail_edit.tpl';

// dynamic page data.
edit_mail_account($tpl, $sql);

if (update_email_pass($sql) && update_email_forward($tpl, $sql)) {
	set_page_message(tr("Mail were updated successfully!"), 'success');
	send_request();
	user_goto('mail_accounts.php');
}

// static page messages.
gen_logged_from($tpl);

check_permissions($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'			=> tr('ispCP - Manage Mail and FTP / Edit mail account'),
		'TR_EDIT_EMAIL_ACCOUNT'	=> tr('Edit email account'),
		'TR_SAVE'				=> tr('Save'),
		'TR_PASSWORD'			=> tr('Password'),
		'TR_PASSWORD_REPEAT'	=> tr('Repeat password'),
		'TR_FORWARD_MAIL'		=> tr('Forward mail'),
		'TR_FORWARD_TO'			=> tr('Forward to'),
		'TR_FWD_HELP'			=> tr("Separate multiple email addresses with a line-break."),
		'TR_EDIT'				=> tr('Edit')
	)
);

gen_client_mainmenu($tpl, 'main_menu_email_accounts.tpl');
gen_client_menu($tpl, 'menu_email_accounts.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

// page functions

/**
 * @param ispCP_TemplateEngine $tpl
 * @param ispCP_Database $sql
 */
function edit_mail_account($tpl, $sql) {

	$cfg = ispCP_Registry::get('Config');

	if (!isset($_GET['id']) || $_GET['id'] === '' || !is_numeric($_GET['id'])) {
		set_page_message(tr('Email account not found!'), 'error');
		user_goto('mail_accounts.php');
	} else {
		$mail_id = $_GET['id'];
	}

	$dmn_name = $_SESSION['user_logged'];

	$query = "
		SELECT
			t1.*, t2.`domain_id`, t2.`domain_name`
		FROM
			`mail_users` AS t1,
			`domain` AS t2
		WHERE
			t1.`mail_id` = ?
		AND
			t2.`domain_id` = t1.`domain_id`
		AND
			t2.`domain_name` = ?
	";

	$rs = exec_query($sql, $query, array($mail_id, $dmn_name));

	if ($rs->recordCount() == 0) {
		set_page_message(
			tr('User does not exist or you do not have permission to access this interface!'),
			'error'
		);
		user_goto('mail_accounts.php');
	} else {
		$mail_acc = $rs->fields['mail_acc'];
		$domain_id = $rs->fields['domain_id'];
		$mail_type_list = $rs->fields['mail_type'];
		$mail_forward = $rs->fields['mail_forward'];
		$sub_id = $rs->fields['sub_id'];

		foreach (explode(',', $mail_type_list) as $mail_type) {
			if ($mail_type == MT_NORMAL_MAIL) {
				$mtype[] = 1;
				$res1 = exec_query($sql, "SELECT `domain_name` FROM `domain` WHERE `domain_id` = ?", $domain_id);
				$tmp1 = $res1->fetchRow(0);
				$maildomain = $tmp1['domain_name'];
			} else if ($mail_type == MT_NORMAL_FORWARD) {
				$mtype[] = 4;
				$res1 = exec_query($sql, "SELECT `domain_name` FROM `domain` WHERE `domain_id` = ?", $domain_id);
				$tmp1 = $res1->fetchRow(0);
				$maildomain = $tmp1['domain_name'];
			} else if ($mail_type == MT_ALIAS_MAIL) {
				$mtype[] = 2;
				$res1 = exec_query($sql, "SELECT `alias_name` FROM `domain_aliasses` WHERE `alias_id` = ?", $sub_id);
				$tmp1 = $res1->fetchRow(0);
				$maildomain = $tmp1['alias_name'];
			} else if ($mail_type == MT_ALIAS_FORWARD) {
				$mtype[] = 5;
				$res1 = exec_query($sql, "SELECT `alias_name` FROM `domain_aliasses` WHERE `alias_id` = ?", $sub_id);
				$tmp1 = $res1->fetchRow();
				$maildomain = $tmp1['alias_name'];
			} else if ($mail_type == MT_SUBDOM_MAIL) {
				$mtype[] = 3;
				$res1 = exec_query($sql, "SELECT `subdomain_name` FROM `subdomain` WHERE `subdomain_id` = ?", $sub_id);
				$tmp1 = $res1->fetchRow();
				$maildomain = $tmp1['subdomain_name'];
				$res1 = exec_query($sql, "SELECT `domain_name` FROM `domain` WHERE `domain_id` = ?", $domain_id);
				$tmp1 = $res1->fetchRow(0);
				$maildomain = $maildomain . "." . $tmp1['domain_name'];
			} else if ($mail_type == MT_SUBDOM_FORWARD) {
				$mtype[] = 6;
				$res1 = exec_query($sql, "SELECT `subdomain_name` FROM `subdomain` WHERE `subdomain_id` = ?", $sub_id);
				$tmp1 = $res1->fetchRow();
				$maildomain = $tmp1['subdomain_name'];
				$res1 = exec_query($sql, "SELECT `domain_name` FROM `domain` WHERE `domain_id` = ?", $domain_id);
				$tmp1 = $res1->fetchRow(0);
				$maildomain = $maildomain . "." . $tmp1['domain_name'];
			} else if ($mail_type == MT_ALSSUB_MAIL) {
				$mtype[] = 7;
				$res1 = exec_query($sql, "SELECT `subdomain_alias_name`, `alias_id` FROM `subdomain_alias` WHERE `subdomain_alias_id` = ?", $sub_id);
				$tmp1 = $res1->fetchRow();
				$maildomain = $tmp1['subdomain_alias_name'];
				$alias_id = $tmp1['alias_id'];
				$res1 = exec_query($sql, "SELECT `alias_name` FROM `domain_aliasses` WHERE `alias_id` = ?", $alias_id);
				$tmp1 = $res1->fetchRow(0);
				$maildomain = $maildomain . "." . $tmp1['alias_name'];
			} else if ($mail_type == MT_ALSSUB_FORWARD) {
				$mtype[] = 8;
				$res1 = exec_query($sql, "SELECT `subdomain_alias_name`, `alias_id` FROM `subdomain_alias` WHERE `subdomain_alias_id` = ?", $sub_id);
				$tmp1 = $res1->fetchRow();
				$maildomain = $tmp1['subdomain_alias_name'];
				$alias_id = $tmp1['alias_id'];
				$res1 = exec_query($sql, "SELECT `alias_name` FROM `domain_aliasses` WHERE `alias_id` = ?", $alias_id);
				$tmp1 = $res1->fetchRow(0);
				$maildomain = $maildomain . "." . $tmp1['alias_name'];
			}
		}

		if (isset($_POST['forward_list'])) {
			$mail_forward = clean_input($_POST['forward_list']);
		}
		$mail_acc = decode_idna($mail_acc);
		$maildomain = decode_idna($maildomain);
		$tpl->assign(
			array(
				'EMAIL_ACCOUNT'	=> tohtml($mail_acc . "@" . $maildomain),
				'FORWARD_LIST'	=> str_replace(',', "\n", tohtml($mail_forward)),
				'MTYPE'			=> implode(',', $mtype),
				'MAIL_TYPE'		=> $mail_type_list,
				'MAIL_ID'		=> $mail_id
			)
		);

		if (($mail_forward !== '_no_') && (count($mtype) > 1)) {
			$tpl->assign(
				array(
					'ACTION'				=> 'update_pass,update_forward',
					'FORWARD_MAIL'			=> true,
					'FORWARD_MAIL_CHECKED'	=> $cfg->HTML_CHECKED,
					'FORWARD_LIST_DISABLED'	=> 'false'
				)
			);
		} else if ($mail_forward === '_no_') {
			$tpl->assign(
				array(
					'ACTION'				=> 'update_pass',
					'NORMAL_MAIL'			=> true
				)
			);
		} else {
			$tpl->assign(
				array(
					'ACTION'				=> 'update_forward',
					'FORWARD_MAIL'			=> true,
					'FORWARD_MAIL_CHECKED'	=> $cfg->HTML_CHECKED,
					'FORWARD_LIST_DISABLED'	=> 'false'
				)
			);
		}
	}
}

function update_email_pass($sql) {

	$cfg = ispCP_Registry::get('Config');

	if (!isset($_POST['uaction'])) {
		return false;
	}
	if (preg_match('/update_pass/', $_POST['uaction']) == 0) {
		return true;
	}
	if (preg_match('/update_forward/', $_POST['uaction']) == 1 || isset($_POST['mail_forward'])) {
		// The user only wants to update the forward list, not the password
		if ($_POST['pass'] === '' && $_POST['pass_rep'] === '') {
			return true;
		}
	}

	$pass = clean_input($_POST['pass']);
	$pass_rep = clean_input($_POST['pass_rep']);
	$mail_id = $_GET['id'];
	$mail_account = clean_input($_POST['mail_account']);

	if (trim($pass) === '' || trim($pass_rep) === '' || $mail_id === '' || !is_numeric($mail_id)) {
		set_page_message(tr('Password data is missing!'), 'warning');
		return false;
	} else if ($pass !== $pass_rep) {
		set_page_message(tr('Entered passwords differ!'), 'warning');
		return false;
	} else if (!chk_password($pass, 50, "/[`\xb4'\"\\\\\x01-\x1f\015\012|<>^]/i")) { // Not permitted chars
		if ($cfg->PASSWD_STRONG) {
			set_page_message(
				sprintf(
					tr('The password must be at least %s chars long and contain letters and numbers to be valid.'),
					$cfg->PASSWD_CHARS
				),
				'warning'
			);
		} else {
			set_page_message(
				sprintf(
					tr('Password data is shorter than %s signs or includes not permitted signs!'),
					$cfg->PASSWD_CHARS
				),
				'warning'
			);
		}
		return false;
	} else {
		$pass = encrypt_db_password($pass);
		$status = $cfg->ITEM_CHANGE_STATUS;
		$query = "UPDATE `mail_users` SET `mail_pass` = ?, `status` = ? WHERE `mail_id` = ?";
		exec_query($sql, $query, array($pass, $status, $mail_id));
		write_log($_SESSION['user_logged'] . ": change mail account password: $mail_account");
		return true;
	}
}

function update_email_forward($tpl, $sql) {

	$cfg = ispCP_Registry::get('Config');

	if (!isset($_POST['uaction'])) {
		return false;
	}
	if (preg_match('/update_forward/', $_POST['uaction']) == 0
		&& !isset($_POST['mail_forward'])) {
		return true;
	}

	$mail_account = $_POST['mail_account'];
	$mail_id = $_GET['id'];
	$forward_list = clean_input($_POST['forward_list']);
	$mail_accs = array();

	if (isset($_POST['mail_forward'])
		|| $_POST['uaction'] == 'update_forward') {
		$faray = preg_split ('/[\n\s,]+/', $forward_list);

		foreach ($faray as $value) {
			$value = trim($value);
			if (!chk_email($value) && $value !== '' || $value === '') {
				set_page_message(tr("Mail forward list error!"), 'error');
				return false;
			}
			$mail_accs[] = $value;
		}

		$forward_list = implode(',', $mail_accs);

		// Check if the mail type doesn't contain xxx_forward and append it
		if (preg_match('/_forward/', $_POST['mail_type']) == 0) {
			// Get mail account type and append the corresponding xxx_forward
			if ($_POST['mail_type'] == MT_NORMAL_MAIL) {
				$mail_type = $_POST['mail_type'] . ',' . MT_NORMAL_FORWARD;
			} else if ($_POST['mail_type'] == MT_ALIAS_MAIL) {
				$mail_type = $_POST['mail_type'] . ',' . MT_ALIAS_FORWARD;
			} else if ($_POST['mail_type'] == MT_SUBDOM_MAIL) {
				$mail_type = $_POST['mail_type'] . ',' . MT_SUBDOM_FORWARD;
			} else if ($_POST['mail_type'] == MT_ALSSUB_MAIL) {
				$mail_type = $_POST['mail_type'] . ',' . MT_ALSSUB_FORWARD;
			}
		} else {
			// The mail type already contains xxx_forward, so we can use $_POST['mail_type']
			$mail_type = $_POST['mail_type'];
		}
	} else {
		$forward_list = '_no_';
		// Check if mail type was a forward type and remove it
		if (preg_match('/_forward/', $_POST['mail_type']) == 1) {
			$mail_type = preg_replace('/,[a-z]+_forward$/', '', $_POST['mail_type']);
		}
	}

	$status = $cfg->ITEM_CHANGE_STATUS;

	$query = "UPDATE `mail_users` SET `mail_forward` = ?, `mail_type` = ?, `status` = ? WHERE `mail_id` = ?";

	exec_query($sql, $query, array($forward_list, $mail_type, $status, $mail_id));

	write_log($_SESSION['user_logged'] . ": change mail forward: $mail_account");
	return true;
}
?>
