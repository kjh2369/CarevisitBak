<?
	include("../inc/_header.php");

	if ($_GET['manager'] != 'true'){
		include("../inc/_body_header.php");
	}

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}

	$mCode = $_SESSION["userCenterCode"];
	$mKind = $_POST["myKind"] != "" ? $_POST["myKind"] : $_SESSION["userCenterKind"][0];

	if ($_GET["gubun"] == 'reg'){
		$title = '방문일정등록/조회';
	}else if ($_GET["gubun"] == 'search'){
		$title = '방문일정(수급자)';
	}else{
		$title = '일별일정조회/출력';
	}

	if ($_GET['manager'] == 'true'){
	?>
		<style>
		body{
			margin-left:10px;
			margin-right:10px;
		}
		</style>
	<?
	}

	if ($showMensFlag[0]){?>
		<div class="title"><?=$title;?></div> <?
		if ($_GET["gubun"] == 'reg' or $_GET["gubun"] == 'search'){ ?>
			<div id="myBody"></div><?
		}else{ ?>
			<table style="width:100%;">
			<tr>
				<td style="border:none; width:160px; text-align:left; vertical-align:top;">
					<div id="myCalendar"></div>
				</td>
				<td style="border:none; text-align:left; vertical-align:top;">
					<div id="myBody"></div>
				</td>
			</tr>
			</table><?
		}
	}else{?>
		<table style="width:100%;">
		<tr>
		<td class="title" colspan="2"><?=$title;?></td>
		</tr>
		<tr>
		<?
			if ($_GET["gubun"] == 'reg' or $_GET["gubun"] == 'search'){
			?>
				<td class="noborder" colspan="2">
				<div id="myBody"></div>
				</td>
			<?
			}else{
			?>
				<td style="border:none; width:160px; text-align:left; vertical-align:top;">
				<div id="myCalendar"></div>
				</td>
				<td style="border:none; text-align:left; vertical-align:top;">
				<div id="myBody"></div>
				</td>
			<?
			}
		?>
		</tr>
		</table><?
	}

	if ($_GET['manager'] != 'true'){
		include("../inc/_body_footer.php");
	}
	include("../inc/_footer.php");

	if ($_GET["gubun"] == 'reg' or $_GET["gubun"] == 'search'){
	?>
		<script language="javascript">
			_getSugupjaList(myBody, '', '<?=$_GET["gubun"];?>', '<?=$mCode;?>', '<?=$mKind;?>');
		</script>
	<?
	}else{
	?>
		<script language="javascript">
			_getCalendar(myCalendar, '', '','','');
		</script>
	<?
	}
?>