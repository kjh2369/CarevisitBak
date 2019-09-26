<!--<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
	<title>Head Offic Login</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv='imagetoolbar' content='no'>
	<meta http-equiv="Content-Script-Type" content="text/javascript" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<meta name="keywords" content="방문서비스 관리 시스템, 케어,돌봄, 재가, 요양보호사, 방문요양, 방문목욕, 방문간호" />
	<!--meta http-equiv="X-UA-Compatible" content="IE=8" /-->
	<!--meta http-equiv="X-UA-Compatible" content="EmulateIE8" /-->
	<!--meta http-equiv="X-UA-Compatible" content="edge" /-->
	<link rel="stylesheet" type="text/css" href="../css/jqueryslidemenu.css" /><!--menu-->
</head>

<base target="_self">
<script type="text/javascript" src="../js/prototype.js"	></script>
<script type="text/javascript" src="../js/xmlHTTP.js"	></script>
<script type="text/javascript" src="../js/script.js"	></script>
<script type='text/javascript' src='../js/pass.js'></script>
<script type="text/javascript" src="../js/center.js"	></script>
<script type="text/javascript" src="../js/iljung.js"	></script>
<script type="text/javascript" src="../js/suga.js"		></script>
<script type="text/javascript" src="../js/other.js"		></script>
<script type="text/javascript" src="../js/cal.js"		></script>
<script type="text/javascript" src="../js/kjw.work.js"	></script>
<script type="text/javascript" src="../js/table_class.js"></script>
<!--menu-->
<script type="text/javascript" src="../js/jquery.min.js"></script>
<script type="text/javascript" src="../js/jqueryslidemenu.js"></script>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/jquery.form.js"></script>
<script type='text/javascript' src='../longcare/longcare.js'></script>
<style type="text/css">
html,body{margin:0; padding:0;}
</style>

<body style="background:url('../img/admin/bg_img_bg3.jpg');">
<div style="position:absolute;right:0;"><a href="/" target="_blank"><img src="../img/admin/rankup_bt2.png" border="0"></a></div>
<table width="100%" height="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign="top">
			<table width="940" cellpadding="0" cellspacing="0" border="0"   style="background:url('../img/admin/bg_img4.jpg') no-repeat;background-position:center top; " align="center">
				<tr>
					<td align="left" valign="top">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
				 <td height="345" align="left" valign="top">&nbsp;</td>
				</tr>
				<tr>
				 <td align="center" valign="top">
				  <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
         <tr>
					<td align="center" valign="top">
							<table cellpadding="0" cellspacing="0" border="0">
								<form name="login" method="post">
										<tr>
											<td align="left" valign="top">
												<table width="190" style="background:#fff url('./../img/admin/id.gif') no-repeat 7px 7px;width:230px;border:#d6d6d6 1px solid;" border="0">
																<tr>
																	<td width="90" height="30">&nbsp;</td>
																	<td><input type=text size="14" id="admin_id" name="uCode" tabindex="1" value="" style="ime-mode:disabled; border:0px;" onkeydown="if(event.keyCode == 13 && this.value != ''){document.getElementById('uPass').focus();}"></td>
																</tr>
															</table>
														</td>
														<td rowspan="2" style="padding-left:20px"><img style="cursor:pointer;" src='../img/admin/btn_login.png' alt='로그인' onclick="_Login();"></td>
													</tr>
													<tr>
														<td align="left" valign="top">
															<table width="190" style="background:#fff url('./../img/admin/pw.gif') no-repeat 7px 7px;width:230px;border:#d6d6d6 1px solid;" border="0">
																<tr>
																	<td width="90" height="30">&nbsp;</td>
																	<td><input type="password" size="14" name="uPass" tabindex="2" value="" style="border:0px" onkeydown="if(event.keyCode == 13 && this.value != ''){_Login();}"></td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td height="15"><!--띄기--></td>
													</tr>
													<tr>
														<td align="left" valign="top" colspan="2"><img src="../img/admin/login_text.png"></td>
													</tr>
												</table>
											</td>
										</tr>
										</form>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="30"><!--띄기--></td>
	</tr>
	<tr>
		<td height="1" bgcolor="#d6d6d6"></td>
	</tr>
	<tr>
		<td height="20"><!--띄기--></td>
	</tr>
	<tr>
		<td align="center" style="font-size:12px;">Copyright forweak.net All rights reserved</td>
	</tr>
	<tr>
		<td height="20"><!--띄기--></td>
	</tr>
</table>