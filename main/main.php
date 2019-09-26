<?
	include_once("../inc/_header.php");
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	if (isset($_SESSION["userCode"])){?>
		<script type="text/javascript">
			//window.open("../popup/3/popup1.html","POP","width=400,height=450,left=0,top=0,scrollbars=no,resizable=no,location=no,toolbar=no,menubar=no");
		</script><?
		include_once('body.php');
	}else{
		$host = $myF->host();
		?>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<script type="text/javascript" src="../js/goodeos.js"></script>
			<script type="text/javascript" src="../js/member.js"></script>
			<script language="javascript">
			<!--
			function _Login(){
				if (document.login.uCode.value == ''){
					alert('아이디를 입력하여 주십시오.');
					document.login.uCode.focus();
					return;
				}

				if (document.login.uPass.value == ''){
					alert('비밀번호를 입력하여 주십시오.');
					document.login.uPass.focus();
					return;
				}

				document.login.action = 'login_ok.php';
				document.login.submit();
			}

			function open_counsel(){
				var w = 600;
				var h = 400;
				var l = (window.screen.width  - w)  / 2;
				var t = (window.screen.height - h) / 2;

				window.open('../counsel/counsel.php','COUNSEL','width='+w+',height='+h+',left='+l+',top='+t+',scrollbars=no,resizable=no,location=no,toolbar=no,menubar=no');
			}

			function counsel(){
				var w = 600;
				var h = 340;
				var l = (window.screen.width  - w)  / 2;
				var t = (window.screen.height - h) / 2;

				window.open('../counsel/counsel1.php','COUNSEL','width='+w+',height='+h+',left='+l+',top='+t+',scrollbars=no,resizable=no,location=no,toolbar=no,menubar=no');
			}	
			
			

			window.onload = function(){
				
				if ('<?=$host;?>' == 'pr'){
					
					var code_cookie = __getCookie('code');
					var id_cookie = __getCookie('id');
					
					if(code_cookie == null) code_cookie = '';
					if(id_cookie == null) id_cookie = '';

					if (code_cookie != ''){
						document.getElementById('mCode').value      = code_cookie;
						document.getElementById('uCode').value		= id_cookie;
						document.getElementById('id_check').checked = true;
					}
				}

				document.login.uCode.focus();
				__init_form(document.login);
			}
			//-->
			</script>
		<?
		include_once('login.php');
	}
	include_once("../inc/_footer.php");
?>