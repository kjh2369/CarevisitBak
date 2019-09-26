<?
	include_once('../inc/_header.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$popId	= $_GET['pop_id'];

	if ($popId){
		$sql = 'SELECT	contents
				FROM	center_popup
				WHERE	pop_id	= \''.$popId.'\'
				AND		org_no	= \''.$orgNo.'\'';

		$contents = $conn->get_data($sql);
	}else{
		$contents = StripSlashes($_POST['contents']);

	}
?>
<script type="text/javascript">
	$(document).ready(function(){

	});
</script>
<div style="width:100%; height:100%; padding-top:40px; padding-left:25px; padding-right:25px; text-align:justify; background:url('../image/popup_bg.jpg') no-repeat;"><?=$contents;?></div>
<?
	include_once('../inc/_footer.php');
?>