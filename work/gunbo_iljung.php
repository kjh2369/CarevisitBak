<?
	include_once("../inc/_header.php");
	//include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}
	

	$code = $_SESSION['userCenterCode'];
	$kind = '0';
	
	$Svc = $_POST['Svc']; //케어구분
	

	$sql = "select yy, SUM(m01) as m01  , SUM(m02) as m02, SUM(m03) as m03, SUM(m04) as m04, SUM(m05) as m05, SUM(m06) as m06, SUM(m07) as m07, SUM(m08) as m08, SUM(m09) as m09, SUM(m10) as m10, SUM(m11) as m11, SUM(m12) as m12
			  from (";

	for($i=1; $i<=12; $i++){
		if($i > 1){
			$sql .= " union all ";
		}
		$query = '';
		for($j=1; $j<$i; $j++){
			$query .= "
					,      0 as m".($j<10?'0':'').$j;
		}

		$query .= "
				,      count(t01_jumin) as m".($i<10?'0':'').$i;

		for($j=$i+1; $j<=12; $j++){
			$query .= "
					,      0 as m".($j<10?'0':'').$j;
		}


		$sql .= "  SELECT left(t01_sugup_date,4) as yy $query
					 from t01iljung
					where t01_ccode  = '$code'
					  and t01_mkind  = '$kind'
					  and t01_del_yn = 'N'
					  and SUBSTRING(t01_sugup_date, 5, 2) = '".($i<10?'0':'').$i."'";

		if(!empty($Svc)) $sql .= "     and t01_svc_subcode = '".$Svc."'";

		$sql .= "	group by left(t01_sugup_date,6)";
	}

	$sql .= "	   ) as t
			 group by yy
			 order by yy DESC";
	
	//if($debug) echo nl2br($sql);

	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();


?>
<script src="../js/work.js" type="text/javascript"></script>
<script>
	function search(){
		var f = document.f;
		
		f.submit();
	}
</script>
<form name="f" method="post">
<div class="title">건보공단계획서식</div>
<table class="my_table my_border">
	<colgroup>
		<col width="60px">
		<col width="60px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">케어구분</th>
			<td>
				<select id="Svc" name="Svc" style="width:auto;">
					<option value="">전체</option>
					<option value="200" <?= ($Svc == '200' ? 'selected' : '') ?> >요양</option>
					<option value="500" <?= ($Svc == '500' ? 'selected' : '') ?> >목욕</option>
					<option value="800" <?= ($Svc == '800' ? 'selected' : '') ?> >간호</option>
				</select>
			</td>
			<td class="other" style="line-height:26px; padding-left:5px; vertical-align:top; padding-top:2px;">
				<div style="float:left; width:auto;">
					<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="search();">조회</button></span>
				</div>
			</td>
		</tr>
		<tr>
			<th class="head">년도</th>
			<th class="head last" colspan="2">월별일정</th>
		</tr>
	</thead>
	<tbody><?
		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			echo "<tr>";
			echo "<td class='center'>".$row['yy']."년</td>";
			echo "<td class='last left' colspan='2'>";
			for($j=1; $j<=12; $j++){
				$class = 'my_month ';
				$cur_i = ($j < 10 ? '0' : '').$j;
					if ($row['m'.$cur_i] > 0){
						$class .= 'my_month_y ';
						$color  = 'color:#000000;';
						$text   = '<a href="#">'.$j.'월</a>';
					}else{
						$class .= 'my_month_1 ';
						$color  = 'color:#c6c6c6;';
						$text	= $j.'월';
					}
				if ($j == 12){
					$style = 'float:left;';
				}else{
					$style = 'float:left; margin-right:2px;';
				}
				
				?>
				<div class="<?=$class;?>" style="<?=$style;?> <?=$color;?>" onclick="gunboPlanShow('<?=$code;?>', '<?=$kind;?>', '<?=$row['yy'];?>', '<?=$cur_i?>', '', '<?=$Svc?>');"><?=$text;?></div><?
			}
			echo "</td>";
			echo "</tr>";
		}?>
	</tbody>
</table>
<input type="hidden" name="code" value="<?=$code;?>">
<input type="hidden" name="kind" value="<?=$kind;?>">

</form>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>