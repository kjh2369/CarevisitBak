<?
	include("../inc/_header.php");
	include("../inc/_myFun.php");
	include('../inc/_ed.php');

	$mCode = $_POST['mCode'];
	$mKind = $_POST['mKind'];
	$date = date("Ymd", mkTime());
?>
<table class="view_type1" style="width:100%; height:100%;">
<colGroup>
	<col width="%">
	<col width="%">
	<col width="%">
	<col width="%">
</colGroup>
<thead>
<tr>
	<th style="height:30px; padding:0px; text-align:center; border-right:1px solid #cccccc;">수급자</th>
	<th style="height:30px; padding:0px; text-align:center; border-right:1px solid #cccccc;">주민번호</th>
	<th style="height:30px; padding:0px; text-align:center; border-right:1px solid #cccccc;">등급</th>
	<th style="height:30px; padding:0px; text-align:center; border-right:1px solid #cccccc;">구분</th>
</tr>
</thead>
<tbody>
<?
	$sql = "select m03_jumin as code
			,      m03_name as name
			,      LVL.m81_name as level
			,      STP.m81_name as kind
			,      m03_key as myKey
			  from m03sugupja
			 inner join m81gubun as LVL
			    on LVL.m81_gbn = 'LVL'
			   and LVL.m81_code = m03_ylvl
			 inner join m81gubun as STP
			    on STP.m81_gbn = 'STP'
			   and STP.m81_code = m03_skind
			 where m03_ccode = '$mCode'
			   and m03_mkind = '$mKind'
			   and m03_sugup_status = '1'
			   and '$date' between m03_gaeyak_fm and m03_gaeyak_to
			 order by m03_name";
	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();
	
	if ($rowCount > 0){
		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$code = $ed->en($row["code"]);
			$name = $row["name"];
			$jumin = $myF->issStyle($row["code"]);
			$level = $row["level"];
			$kind = $row["kind"];
			$key = $row["myKey"];

			echo "
				<tr id='sugupja_$i' style='cursor:pointer;' onClick=\"sugupjaPattern(myList,'$mCode','$mKind','$code','$key','$i');\">
					<td style='text-align:left; padding:0; padding-left:5px;'>$name</td>
					<td style='text-align:left; padding:0; padding-left:5px;'>$jumin</td>
					<td style='text-align:left; padding:0; padding-left:5px;'>$level</td>
					<td style='text-align:left; padding:0; padding-left:5px; border-right:1px solid #cccccc;'>$kind</td>
				</tr>
				 ";
		}
	}else{
		echo "
			<tr>
				<td style='text-align:center;' colspan='4'>-</td>
			</tr>
			 ";
	}
	$conn->row_free();

	echo "<input name='sugupjaCount' type='hidden' value='$rowCount'>";
?>
<tbody>
</table>
<?
	include("../inc/_footer.php");
?>