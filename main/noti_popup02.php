<?
	include_once('../inc/_header.php');

	if (!isset($_SESSION['userCode']) || $_SESSION['userCode'] == ''){
		echo '<script language=\'javascript\'>
				self.close();
			  </script>';
	}
?>

  <script type='text/javascript'>
	function setCookie(){
		__setCookie('noti2','done',1);
	}

	function setGbn(){
		self.close();
	}
  </script>
 </HEAD>

	<div style="width:400px; height:455px;"><img src="../file/notice_pop.jpg" alt="공지"></div>
	<div style="background-color:#ccc;">
		<div style="float:left; width:auto;"><input id="notiYn" name="insurYn" type="checkbox" class="checkbox" onclick="setCookie();"><label for="insurYn">오늘 더이상 표시하지 않음</label></div>
		<div style="float:right; width:auto; padding-right:5px;"><a id="btnGbn" href="#" onclick="setGbn();">닫기</a></div>
	</div>
<?
	include_once('../inc/_footer.php');
?>