<?
	include_once('../inc/_root_return.php');
	$menu = $_GET['menu'];
	$typeSR = $_SESSION['userTypeSR'];
?>
<script type="text/javascript">
	function lfSvcContShow(SR){
		var w = 1024;
		var h = (screen.availHeight > 768 ? screen.availHeight : 768);
		var l = (screen.availWidth - w) / 2;
		var t = (screen.availHeight - h) / 2;

		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
		var url = '../care/care_svc_cont.php?SR='+SR;
		var win = window.open('', 'WINCASE', option);
			win.opener = self;
			win.focus();

		var form = document.createElement('form');
			form.setAttribute('target', 'WINCASE');
			form.setAttribute('method', 'post');
			form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}
</script>
<div id="left_box">
	<h2>보고서</h2>
	<ul id="sidnav">
		<li><a>보고서</a>
			<ul id="sub_menu">
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=53&menu=<?=$menu;?>'; return false;">중분류</a></li>
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=51&menu=<?=$menu;?>'; return false;">보고서</a></li>
				<li><a href="#" onClick="lfSvcContShow('<?=$typeSR;?>'); return false;">서비스내역</a></li>
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=RSLS&menu=<?=$menu;?>'; return false;">자원연계서비스</a></li>
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=RSPL&menu=<?=$menu;?>'; return false;">자원봉사자연결</a></li>
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=LHCT&menu=<?=$menu;?>'; return false;">지역재가협의체구성</a></li>
				<li><a href="#" onClick="location.href='../care/care.php?sr=<?=$typeSR;?>&type=RPT&menu=<?=$menu;?>'; return false;">기타</a></li>
			</ul>
		</li>
	</ul>
</div>