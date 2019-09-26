<?
	session_start();

	$level = $_SESSION['userLevel'];
	$code  = $_SESSION['userCenterCode'];

	session_unset();
	session_destroy();

	if ($level != 'P'){?>
		<script type='text/javascript' src='../js/script.js'></script>
		<script>
			__setCookie('__left_menu__', '', 1);

			if (!parent.opener){
				location.href = '../index.html';
			}else{
				try{
					parent.opener.lfLogin();
				}catch(e){
				}
				top.close();
			}
		</script><?
	}else{?>
		<script type='text/javascript' src='../js/script.js'></script>
		<script>
			__setCookie('__left_menu__', '', 1);

			if (!parent.opener){
				location.href = '../index.html';
			}else{
				top.close();
			}
		</script><?
	}
?>