<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_mySuga.php');

	$code	= $_SESSION['userCenterCode'];
	$jumin	= $_POST['jumin'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$sr		= $_POST['sr'];
?>
<script type='text/javascript' src='./care.js'></script>
<script type="text/javascript">
	$(document).ready(function(){
		setTimeout('lfLoadInfo()',10);
		setTimeout('lfLoadSuga()',20);
		setTimeout('lfLoadCaln()',30);
		setTimeout('lfLoadBtn()',40);
		setTimeout('lfLoadIljung()',50);
	});

	function lfLoadInfo(){
		var jumin = $('#jumin').val();
		var year = $('#year').val();
		var month = $('#month').val();

		_careLoadInfo(jumin,year,month);
	}

	function lfLoadSuga(){
		var year = $('#year').val();
		var month = $('#month').val();

		_careLoadSuga(year,month);
	}

	function lfLoadCaln(){
		var jumin = $('#jumin').val();
		var year = $('#year').val();
		var month = $('#month').val();

		_careLoadCaln(jumin,year,month);
	}

	function lfLoadBtn(){
		var year	= $('#year').val();
		var month	= $('#month').val();
		var sr		= $('#sr').val();

		_careLoadBtn(year,month,sr);
	}

	function lfLoadIljung(){
		var jumin	= $('#jumin').val();
		var year	= $('#year').val();
		var month	= $('#month').val();
		var sr		= $('#sr').val();

		_careLoadIljung(jumin,year,month,sr);
	}
</script>
<form id="f" name="f" method="post">
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="250px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td style="border:none; border-right:1px solid #0e69b0;">
				<div id="divInfo">&nbsp;</div>
			</td>
			<td style="border:none;">
				<div id="divSuga">&nbsp;</div>
			</td>
		</tr>
		<tr>
			<td style="border:none; border-top:2px solid #0e69b0; border-right:1px solid #0e69b0;">
				<div id="divCaln">&nbsp;</div>
			</td>
			<td style="border:none; border-top:2px solid #0e69b0;">
				<div id="divSvc">&nbsp;</div>
			</td>
		</tr>
		<tr>
			<td style="border:none; border-top:2px solid #0e69b0;" colspan="2">
				<div id="divBtn">&nbsp;</div>
			</td>
		</tr>
		<tr>
			<td style="border:none;" colspan="2">
				<div id="divIljung">&nbsp;</div>
			</td>
		</tr>
	</tbody>
</table>
<input id="jumin" type="hidden" value="<?=$jumin;?>">
<input id="year" type="hidden" value="<?=$year;?>">
<input id="month" type="hidden" value="<?=$month;?>">
<input id="sr" type="hidden" value="<?=$sr;?>">
</form>
<?
	include_once('../inc/_footer.php');
?>