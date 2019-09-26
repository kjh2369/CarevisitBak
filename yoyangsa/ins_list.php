<?
	include("../inc/_header.php");
	include("../inc/_myFun.php");
	include('../inc/_ed.php');

	$mCode = $_POST["mCode"];
	$stat = $_POST["stat"];
?>
<table class="view_type1" style="width:100%; height:100%;">
<colGroup>
	<col width="5%">
	<col width="10%">
	<col width="10%">
	<col width="10%">
	<col width="10%">
	<col width="15%">
	<col width="40%">
</colGroup>
<tr>
	<th style="height:24px; padding:0px; text-align:center; border-right:1px solid #cccccc;">구분</th>
	<th style="height:24px; padding:0px; text-align:center; border-right:1px solid #cccccc;">입사일</th>
	<th style="height:24px; padding:0px; text-align:center; border-right:1px solid #cccccc;">퇴사일</th>
	<th style="height:24px; padding:0px; text-align:center; border-right:1px solid #cccccc;">보장내역</th>
	<th style="height:24px; padding:0px; text-align:center; border-right:1px solid #cccccc;">요양보호사</th>
	<th style="height:24px; padding:0px; text-align:center; border-right:1px solid #cccccc;">주민번호</th>
	<th style="height:24px; padding:0px; text-align:center;">비고</th>
</tr>
<?
	$sql = "select m02_yipsail as joinDate
			,      case when m02_ygoyong_stat = '1' then '' else m02_ytoisail end as endDate
			,      m02_yname as name
			,      m02_yjumin as jumin
			  from m02yoyangsa
			 where m02_ccode = '$mCode'
			   and m02_ins_yn = 'Y'
			   and m02_ins_code > 0
			   and m02_ins_item > 0";
	if ($stat != ""){
		$sql .= " and m02_ygoyong_stat = '$stat'";
	}
	$sql .=" order by joinDate, endDate, name";
	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();

	if ($rowCount > 0){
		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);
			$seq = $i+1;
			$joinDate = $myF->dateStyle($row["joinDate"]);
			$endDate = ($row["endDate"] != "" ? $myF->dateStyle($row["endDate"]) : "(근무중)");
			$name = $row["name"];
			$jumin = $myF->issStyle($row["jumin"]);
			echo "
				<tr>
					<td style='text-align:center;'>$seq</td>
					<td style='text-align:center;'>$joinDate</td>
					<td style='text-align:center;'>$endDate</td>
					<td style='text-align:center;'>증권참조</td>
					<td style='text-align:left;'>$name</td>
					<td style='text-align:center;'>$jumin</td>
					<td></td>
				</tr>
				 ";
		}
	}else{
		echo "<tr><td style='text-align:center;' colspan='7'>::검색된 데이타가 없습니다.::</td></tr>";
	}
	$conn->row_free();
?>
</table>
<?
	include("../inc/_footer.php");
?>