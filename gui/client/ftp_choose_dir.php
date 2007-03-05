<?php
//   -------------------------------------------------------------------------------
//  |             VHCS(tm) - Virtual Hosting Control System                         |
//  |              Copyright (c) 2001-2005 by moleSoftware	|
//  |			http://vhcs.net | http://www.molesoftware.com		           		|
//  |                                                                               |
//  | This program is free software; you can redistribute it and/or                 |
//  | modify it under the terms of the MPL General Public License                   |
//  | as published by the Free Software Foundation; either version 1.1              |
//  | of the License, or (at your option) any later version.                        |
//  |                                                                               |
//  | You should have received a copy of the MPL Mozilla Public License             |
//  | along with this program; if not, write to the Open Source Initiative (OSI)    |
//  | http://opensource.org | osi@opensource.org								    |
//  |                                                                               |
//   -------------------------------------------------------------------------------
require '../include/vfs.php';
include '../include/vhcs-lib.php';

check_login();

$tpl = new pTemplate();

$tpl -> define_dynamic('page_message', 'page');

$tpl -> define_dynamic('logged_from', 'page');

$tpl -> define_dynamic('dir_item', 'page');

$tpl -> define_dynamic('action_link', 'page');

$tpl -> define_dynamic('list_item', 'page');

$tpl -> define_dynamic('page', $cfg['CLIENT_TEMPLATE_PATH'].'/ftp_choose_dir.tpl');

$theme_color = $cfg['USER_INITIAL_THEME'];


function gen_directories( &$tpl ) {
	global $sql;
	$path   = isset($_GET['cur_dir']) ? $_GET['cur_dir'] : '';
	$domain = $_SESSION['user_logged'];
	
	$vfs = new vfs($domain);
	$vfs->setDb($sql);
	$vfs->open();
	$list = $vfs->ls($path,true);
	
	if (!$list) {
		set_page_message( tr('Can not open directory !<br>Please contact your administrator !'));
		return;
	}

	// Show parent directory link
	$parent = explode('/',$path);
	array_pop($parent);
	$parent = implode('/',$parent);
	$tpl -> assign('ACTION_LINK', '');
	$tpl -> assign( array(
				'ACTION' => tr(''),
				'ICON' => "parent",
				'DIR_NAME' => tr('Parent Directory'),
				'LINK' => 'ftp_choose_dir.php?cur_dir=' . $parent,
			));
	$tpl -> parse('DIR_ITEM', '.dir_item');
	
	// Show directories
	foreach ($list as $entry) {
		
		if ( $entry['type'] != VFS_TYPE_DIR )
			continue;
	
		$dr = $path.'/'.$entry['file'];
		//$tfile = $real_dir.$entry.'/'.'.htaccess';
	
		/*if (file_exists($tfile)) {
			$image = "locked";
		}
		else {*/
			$image = "folder";
		/*}*/
	
		// Create directory link
		$tpl->assign( array(
			'ACTION' => tr('Protect it'),
			'PROTECT_IT' => "protect_it.php?file=$dr",
			'ICON' => $image,
			'DIR_NAME' => $entry['file'],
			'CHOOSE_IT' => $dr,
			'LINK' => "ftp_choose_dir.php?cur_dir=$dr",
		));
		$tpl->parse('ACTION_LINK', 'action_link');
		$tpl->parse('DIR_ITEM'   , '.dir_item');
	}
}



// functions end


$tpl -> assign(
                array(
                        'TR_CLIENT_WEBTOOLS_PAGE_TITLE' => tr('VHCS - Client/Webtools'),
                        'THEME_COLOR_PATH' => "../themes/$theme_color",
                        'THEME_CHARSET' => tr('encoding'),
						'TID' => $_SESSION['layout_id'],
                        'VHCS_LICENSE' => $cfg['VHCS_LICENSE'],
						'ISP_LOGO' => get_logo($_SESSION['user_id'])
                     )
              );


gen_directories($tpl);

			  
$tpl -> assign(
                array(
						'TR_DIRECTORY_TREE' => tr('Directory tree'),
						'TR_DIRS' => tr('Directories'),
						'TR__ACTION' => tr('Action'),
						'CHOOSE' => tr('Choose'),
						

						
					  )
				);

gen_page_message($tpl);

$tpl -> parse('PAGE', 'page');

$tpl -> prnt();

if (isset($cfg['DUMP_GUI_DEBUG'])) dump_gui_debug();

unset_messages();
?>