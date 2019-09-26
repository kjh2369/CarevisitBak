<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_body_header.php");

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}

	$code = $_SESSION['userCenterCode'];
	$year = $_POST['year'] != '' ? $_POST['year'] : date('Y', mktime());

	$init_year = $myF->year();
?>
<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
<!--

function search(){
	var f = document.f;

	f.submit();
}

function detail(gbn, month){
	var f = document.f;

	f.gbn.value   = gbn;
	f.month.value = month;
	f.action      = 'result_status_detail.php';
	f.submit();
}

-->
</script>

<div class="title">마감진행상태</div>

<form name="f" method="post">

<table class="my_table my_border">
	<colgroup>
		<col width="60px">
		<col width="20px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head">마감년도</th>
			<td class="right">
				<select name="year" style="width:auto;">
				<?
					for($i=$init_year[0]; $i<=$init_year[1]; $i++){?>
						<option value="<?=$i;?>" <? if($i == $year){?>selected<?} ?>><?=$i;?></option><?
					}
				?>
				</select>년
			</td>
			<td class="left last">
				<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="search();">조회</button></span>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="62px">
		<col width="100px">
		<col width="100px">
		<col width="100px">
		<col width="100px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">마감월</th>
			<th class="head" colspan="2">실적</th>
			<th class="head" colspan="2">급여계산</th>
			<th class="head last" rowspan="2">비고</th>
		</tr>
		<tr>
			<th class="head">마감여부</th>
			<th class="head">마감일자</th>
			<th class="head">계산일자</th>
			<th class="head">마감여부</th>
		</tr>
	</thead>
	<tbody>
	<?
		$today = date('Y-m-d', mktime());

		$sql = "select cast(right(closing_yymm, 2) as unsigned)  as yymm
				,      act_cls_flag
				,      act_cls_dt_from
				,      act_cls_ent_dt
				,      act_bat_conf_flag
				,      act_bat_conf_dt
				,      act_bat_can_flag
				,      act_bat_can_dt
				,      salary_cls_flag
				,      salary_bat_calc_flag
				,      salary_bat_calc_dt
				,      salary_bat_can_flag
				,      salary_bat_can_dt
				,      salary_cls_dt_from
				,      salary_cls_ent_dt
				  from closing_progress
				 where org_no          = '$code'
				   and closing_yymm like '$year%'";

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		if ($row_count > 0){
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);

				if ($row['act_cls_flag'] == 'Y'){
					$closing_yn = '<font color="ff0000">마감</font>';
				}else{
					$closing_yn = '-';
				}

				if ($row['act_bat_conf_flag'] == 'Y' && $row['act_bat_can_flag'] == 'N'){
					$conf_dt = $row['act_bat_conf_dt'];
				}else{
					$conf_dt = '-';
				}

				if ($row['salary_bat_calc_flag'] == 'Y' && $row['salary_bat_can_flag'] == 'N'){
					$calc_dt = $row['salary_bat_calc_dt'];
				}else{
					$calc_dt = '-';
				}

				if ($row['salary_cls_flag'] == 'Y'){
					$salary_yn = '마감';
				}else{
					$salary_yn    = '-';
				}?>
				<tr>
					<td class="center"><?=$row['yymm'];?>월</td>
					<td class="center"><?=$closing_yn;?></td>
					<td class="center"><?=$conf_dt;?></td>
					<td class="center"><?=$calc_dt;?></td>
					<td class="center"><?=$salary_yn;?></td>
					<td class="left last">
					<?
						if ($row['act_bat_conf_dt']    != '' ||
							$row['act_bat_can_dt']     != '' ||
							$row['salary_bat_calc_dt'] != '' ||
							$row['salary_bat_can_dt']  != ''){?>
							<span class="btn_pack m"><button type="button" onclick="detail('all', '<?=$row['yymm'];?>');">상세</button></span><?
						}else{?>
							<span>&nbsp;</span><?
						}
					?>
					</td>
				</tr><?
			}
		}else{?>
			<tr>
				<td class="center last" colspan="8"><?=$myF->message('nodata', 'N');?></td>
			</tr><?
		}

		$conn->row_free();
	?>
	</tbody>
	<tbody>
		<tr>
			<td class="bottom last" colspan="9">&nbsp;</td>
		</tr>
	</tbody>
</table>

<input type="hidden" name="code"  value="<?=$code;?>">
<input type="hidden" name="month" value="">
<input type="hidden" name="gbn"   value="">

</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>