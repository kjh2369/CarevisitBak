<?
	include_once('../inc/_header.php');
	include_once('../inc/_body_header.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_myFun.php');

	$branch = $_GET['branch'];
	$person = $_GET['person'];
	$type   = $_GET['type'] != '' ? $_GET['type'] : 'reg';
	$mode   = $_GET['mode'];
?>
<script type="text/javascript" src="../js/branch.js"></script>
<form name="f" method="post">

<div class="title title_border">담당자등록</div>
<div id="myBody"></div>

</form>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>
<script language="javascript">
	_inchageReg('<?=$branch;?>','<?=$person;?>','<?=$type;?>','<?=$mode;?>');
</script>