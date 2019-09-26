<?
	include("../inc/_header.php");

	$code = $_GET["mCode"];
	$kind = $_GET["mKind"];
	$key  = $_GET["mKey"];
	$year = $_GET["year"];
	$month= $_GET["month"];

	$sql = "select m00_cname
			,      m00_code1
			  from m00center
			 where m00_mcode = '$code'
			   and m00_mkind = '$kind'";
	$center_array = $conn->get_array($sql);

	$centerName = $center_array[0];
	$centerCode = $center_array[1];

	/*
	$sql = $conn->get_query("00", $_GET["mCode"], $_GET["mKind"]);
	$conn->query($sql);
	$row = $conn->fetch();
	$centerName = $row["m00_cname"];
	$centerCode = $row["m00_code1"];
	$conn->row_free();
	*/

	/*
	$sql = "select m03_ylvl"
		 . ",      LVL.m81_name"
		 . ",      m03_skind"
		 . ",      STP.m81_name"
		 . ",      m03_name"
		 . ",      m03_jumin"
	     . "  from m03sugupja"
		 . " inner join m81gubun as LVL"
		 . "    on LVL.m81_gbn = 'LVL'"
		 . "   and LVL.m81_code = m03_ylvl"
		 . " inner join m81gubun as STP"
		 . "    on STP.m81_gbn = 'STP'"
		 . "   and STP.m81_code = m03_skind"
		 . " where m03_ccode = '".$_GET["mCode"]
		 . "'  and m03_mkind = '".$_GET["mKind"]
		 . "'  and m03_key   = '".$_GET["mKey"]
		 . "'";
	$conn->query($sql);
	$row = $conn->fetch();
	$suLevel = $row[1];
	$suBohum = $row[3];
	$suName = $row[4];
	$suBirthDay = $row[5];
	$suJuminNo = $row[5];
	$conn->row_free();
	*/

	$jumin = $conn->get_data("select m03_jumin from m03sugupja where m03_ccode = '$code' and m03_mkind = '$kind' and m03_key = '$key'");

	$sql = "select m03_ylvl
			,      LVL.m81_name
			,      m03_skind
			,      STP.m81_name
			,      m03_name
			,      m03_jumin
			,      m03_sdate
			,      m03_edate
			  from (
				   select m03_ylvl
				   ,      m03_skind
				   ,      m03_name
				   ,      m03_jumin
				   ,      m03_sdate
				   ,      m03_edate
					 from m03sugupja
					where m03_ccode = '$code'
					  and m03_mkind = '$kind'
					  and m03_jumin = '$jumin'
					union all
				   select m31_level
				   ,      m31_kind
				   ,      m03_name
				   ,      m31_jumin
				   ,      m31_sdate
				   ,      m31_edate
					 from m31sugupja
					inner join m03sugupja
					   on m03_ccode = m31_ccode
					  and m03_mkind = m31_mkind
					  and m03_jumin = m31_jumin
					where m31_ccode = '$code'
					  and m31_mkind = '$kind'
					  and m03_jumin = '$jumin'
				   ) as sugupja
			 inner join m81gubun as LVL
				on LVL.m81_gbn = 'LVL'
			   and LVL.m81_code = m03_ylvl
			 inner join m81gubun as STP
				on STP.m81_gbn = 'STP'
			   and STP.m81_code = m03_skind
			 where '$year$month' between left(m03_sdate, 6) and left(m03_edate, 6)
			 order by m03_sdate, m03_edate";
	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		if ($suLevel != $row[1]){
			$suLevel .= ($suLevel != '' ? '->' : '').$row[1];
		}
		$suLevel	= $row[1];
		$suBohum	= $row[3];
		$suName		= $row[4];
		$suBirthDay = $row[5];
		$suJuminNo	= $row[5];
	}

	$conn->row_free();
?>
<table style="width:900px;">
	<tr>
		<td style="width:80px; font-weight:bold; background-color:#eeeeee;">기관명</td>
		<td style="width:244px; text-align:left;">&nbsp;<?=$centerName;?>&nbsp;</td>
		<td style="width:106px;"><?=$centerCode;?></td>
		<td style="width:80px; font-weight:bold; background-color:#eeeeee;">수급자</td>
		<td style="width:80px;"><?=$suLevel;?></td>
		<td style="width:130px;"><?=$suBohum;?></td>
		<td style="width:80px;"><?=$suName;?></td>
		<td style="width:100px;"><?=getBirthDay($suBirthDay);?>生</td>
	</tr>
</table>
<input name="mCode"		type="hidden" value="<?=$code;?>">
<input name="mKind"		type="hidden" value="<?=$kind;?>">
<input name="mJuminNo"	type="hidden" value="<?=$jumin;?>">
<input name="mKey"		type="hidden" value="<?=$key;?>">
<?
	include("../inc/_footer.php");
?>