<?
	include_once('../inc/_header.php');
	include_once('../inc/_body_header.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_myFun.php');


	$type = $_GET['type'] != '' ? $_GET['type'] : 'reg';
	$mode = $_GET['mode'];

	switch($mode){
		case _COM_:
			$title = '본사관리';
			break;
		case _BRAN_:
			$title = '지사등록';
			break;
		case _STORE_:
			$title = '가맹점등록';
			break;
	}
?>
<script type="text/javascript" src="../js/branch.js"></script>

<form name="f" method="post">

<div class="title title_border"><?=$title;?></div>
<div id="myBody"></div>

</form>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>
<script language="javascript">
	_branchReg('<?=$_GET["code"];?>','<?=$type;?>','<?=$mode;?>');
</script>