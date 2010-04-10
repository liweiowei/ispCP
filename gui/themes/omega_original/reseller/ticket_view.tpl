<?xml version="1.0" encoding="{THEME_CHARSET}" ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{TR_CLIENT_VIEW_TICKET_PAGE_TITLE}</title>
<meta name="robots" content="nofollow, noindex" />
<meta http-equiv="Content-Type" content="text/html; charset={THEME_CHARSET}" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<link href="{THEME_COLOR_PATH}/css/ispcp.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="{THEME_COLOR_PATH}/css/ispcp.js"></script>
</head>

<body onload="MM_preloadImages('{THEME_COLOR_PATH}/images/icons/database_a.gif','{THEME_COLOR_PATH}/images/icons/hosting_plans_a.gif','{THEME_COLOR_PATH}/images/icons/domains_a.gif','{THEME_COLOR_PATH}/images/icons/general_a.gif' ,'{THEME_COLOR_PATH}/images/icons/manage_users_a.gif','{THEME_COLOR_PATH}/images/icons/webtools_a.gif','{THEME_COLOR_PATH}/images/icons/statistics_a.gif','{THEME_COLOR_PATH}/images/icons/support_a.gif')">
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="height:100%;padding:0;margin:0 auto;">
<!-- BDP: logged_from -->
<tr>
 <td colspan="3" height="20" nowrap="nowrap" class="backButton">&nbsp;&nbsp;&nbsp;<a href="change_user_interface.php?action=go_back"><img src="{THEME_COLOR_PATH}/images/icons/close_interface.png" width="16" height="16" border="0" style="vertical-align:middle" alt="" /></a> {YOU_ARE_LOGGED_AS}</td>
</tr>
<!-- EDP: logged_from -->
<tr>
<td align="left" valign="top" style="vertical-align: top; width: 195px; height: 56px;"><img src="{THEME_COLOR_PATH}/images/top/top_left.jpg" width="195" height="56" border="0" alt="ispCP Logogram" /></td>
<td style="height: 56px; width:100%; background-color: #0f0f0f"><img src="{THEME_COLOR_PATH}/images/top/top_left_bg.jpg" width="582" height="56" border="0" alt="" /></td>
<td style="width: 73px; height: 56px;"><img src="{THEME_COLOR_PATH}/images/top/top_right.jpg" width="73" height="56" border="0" alt="" /></td>
</tr>
	<tr>
		<td style="width: 195px; vertical-align: top;">{MENU}</td>
	    <td colspan="2" style="vertical-align: top;"><table style="width: 100%; padding:0;margin:0;" cellspacing="0">
          <tr style="height:95px;">
            <td style="padding-left:30px; width: 100%; background-image: url({THEME_COLOR_PATH}/images/top/middle_bg.jpg);">{MAIN_MENU}</td>
            <td style="padding:0;margin:0;text-align: right; width: 73px;vertical-align: top;"><img src="{THEME_COLOR_PATH}/images/top/middle_right.jpg" width="73" height="95" border="0" alt="" /></td>
          </tr>
          <tr>
            <td colspan="3">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left">
<table width="100%" cellpadding="5" cellspacing="5">
	<tr>
		<td width="25"><img src="{THEME_COLOR_PATH}/images/content/table_icon_support.png" width="25" height="25" alt="" /></td>
		<td colspan="2" class="title">{TR_VIEW_SUPPORT_TICKET}</td>
	</tr>
</table>
	</td>
    <td width="27" align="right">&nbsp;</td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="40">&nbsp;</td>
        <td valign="top"><table width="100%" cellpadding="5" cellspacing="5">
          <!-- BDP: page_message -->
          <tr>
            <td class="title"><span class="message">{MESSAGE}</span></td>
          </tr>
          <!-- EDP: page_message -->
          <!-- BDP: tickets_list -->
          <tr>
            <td nowrap="nowrap" class="content3">{TR_TICKET_URGENCY}: {URGENCY}<br />
              {TR_TICKET_SUBJECT}: {SUBJECT}</td>
          </tr>
          <!-- BDP: tickets_item -->
          <tr>
            <td nowrap="nowrap" class="content2"><span class="content">{TR_TICKET_FROM}: {FROM}</span><br />
              {TR_TICKET_DATE}: {DATE}</td>
          </tr>
          <tr>
            <td nowrap="nowrap" class="content">{TICKET_CONTENT}</td>
          </tr>
          <!-- EDP: tickets_item -->
        </table></td>
      </tr>
    </table></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left">
<table width="100%" cellpadding="5" cellspacing="5">
	<tr>
		<td width="25"><img src="{THEME_COLOR_PATH}/images/content/table_icon_doc.png" width="25" height="25" alt="" /></td>
		<td colspan="2" class="title">{TR_NEW_TICKET_REPLY}</td>
	</tr>
</table>
	</td>
    <td width="27" align="right">&nbsp;</td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="40">&nbsp;</td>
        <td valign="top"><form name="question_frm" method="post" action="ticket_view.php?ticket_id={ID}">
          <table width="100%" cellspacing="5">
            <tr>
              <td colspan="2" class="content"><textarea name="user_message" style="width:80%" class="textinput2" cols="80" rows="20"></textarea>
                      <input name="subject" type="hidden" value="{SUBJECT}" />
                      <input name="urgency" type="hidden" value="{URGENCY_ID}" />
              </td>
            </tr>
            <tr>
              <td width="100"><input name="Button" type="button" class="button" value="{TR_REPLY}" onclick="return sbmt(document.forms[0],'send_msg');" />
              </td>
              <td width="383"><input name="Button" type="button" class="button" value="{TR_ACTION}" onclick="return sbmt(document.forms[0],'{ACTION}');" />
              </td>
            </tr>
            <!-- EDP: tickets_list -->
          </table>
          <!-- end of content -->
          <input name="uaction" type="hidden" value="" />
          <input name="screenwidth" type="hidden" value="{SCREENWIDTH}" />
        </form></td>
      </tr>
    </table></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table></td>
          </tr>
        </table></td>
	</tr>
</table>
</body>
</html>
