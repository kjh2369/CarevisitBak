<?	
	include_once('../inc/_db_open.php');
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');
	
	$con2 = new connection();

	//echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
	
	/*
	header( "Content-type: application/vnd.ms-word" ); 
	header( "Content-Disposition: attachment; filename=test.doc" );
	header( "Content-Transfer-Encoding: binary" ); 
	header( "Content-Description: PHP4 Generated Data" ); 
	*/
	header( "Content-type: application/vnd.ms-excel" ); 
	header( "Content-Disposition: attachment; filename=test.xls" ); 
	header( "Content-Transfer-Encoding: binary" ); 
	header( "Content-Description: PHP4 Generated Data" ); 

	$conn->set_name('euckr');

	$mCode = $_POST["mCode"];
	$mKind = $_POST["mKind"];
	$mFamily = $_POST["mFamily"];
	$mEmployment = $_POST["mEmployment"];
	$mInsurance = $_POST["mInsurance"];

	//print_r($_POST);

	
?>
<table width="600" cellspacing="0" cellpadding="0" border="1">
	<!-- <td style="text-align:center; font-size:20pt; font-weight:bold; padding-bottom:10px; border-bottom:2px solid #000;" colspan="9">직원현황</td> -->
	<td style="text-align:center; font-size:20pt; font-weight:bold;" colspan="9">직원현황</td>
<tr>
	<td style="text-align:center; font-size:10pt; padding-bottom:10px;">No</td>
	<td style="text-align:center; font-size:10pt; padding-bottom:10px;">이  름</td>
	<td style="text-align:center; font-size:10pt; padding-bottom:10px;">입사일</td>
	<td style="text-align:center; font-size:10pt; padding-bottom:10px;">자격증</td>
	<td style="text-align:center; font-size:10pt; padding-bottom:10px;">연락처</td>
	<td style="text-align:center; font-size:10pt; padding-bottom:10px;">주담당</td>
	<td style="text-align:center; font-size:10pt; padding-bottom:10px;">가  족</td>
	<td style="text-align:center; font-size:10pt; padding-bottom:10px;">4대보험</td>
	<td style="text-align:center; font-size:10pt; padding-bottom:10px;">고  용</td>
</tr>
<?
	$sql = "select m02_ccode as code
			,      m02_mkind as kind
			,      m00_cname as kindName
			,      m02_yname as name
			,      m02_yipsail as joinDate
			,      m99_name as license
			,      m02_ytel as mobile
			,      m02_ytel2 as phone
			,      m02_yjumin as jumin
			,      case m02_yfamcare_umu when 'Y' then '유' else '무' end as family
			,      case m02_ygoyong_stat when '1' then '활동' when '2' then '휴직' when '9' then '퇴사' else '-' end as stat
			,      m02_y4bohum_umu as ins4
			,      m02_ygobohum_umu as go
			,      m02_ysnbohum_umu as sn
			,      m02_ygnbohum_umu as gn
			,      m02_ykmbohum_umu as km
			  from m02yoyangsa
			 inner join m00center
			    on m00_mcode = m02_ccode
			   and m00_mkind = m02_mkind
			 inner join m99license
				on m99_code = m02_yjakuk_kind
			 where m02_ccode = '$mCode'";

	
	if ($mKind != "") $sql .= " and m02_mkind = '$mKind'";
	if ($mFamily != "") $sql .= " and m02_yfamcare_umu = '$mFamily'";
	if ($mEmployment != "all") $sql .= " and m02_ygoyong_stat = '$mEmployment'";
	if ($mInsurance != "") $sql .= " and m02_y4bohum_umu = '$mInsurance'";

	$sql .= " order by kind, joinDate, name";

	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();

	if($rowCount > 0){
		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$seq = $i + 1;
			$code = $row["code"];
			$kind = $row["kind"];
			$kindName = $myF->splits($row["kindName"], 5);
			$titleKindName = $myF->len($row["kindName"]) > 3 ? $row["kindName"] : "";
			$name = $row["name"];
			$titleName = $myF->len($row["name"]) > 3 ? $row["name"] : "";
			$joinDate = $myF->dateStyle($row["joinDate"]);
			$license = $mKind != "" ? $row["license"] : $myF->splits($row["license"], 8);
			$titleLicense = ($mKind == "" && $myF->len($row["license"]) > 8 ? $myF->splits($row["license"], 8) : "");
			$phone = $myF->phoneStyle($row["mobile"]);
			$phone2 = $myF->phoneStyle($row["phone"]);
			$juminW = subStr($row["jumin"], 0, 6)."-".subStr($row["jumin"], 6, 1);
			$jumin = $row["jumin"];
			$family = $row["family"];
			$stat = $row["stat"];
			$ins4 = $row["ins4"];
			$go = $ins4 == "Y" ? $row["go"] == "Y" ? "고용" : "" : "";
			$sn = $ins4 == "Y" ? $row["sn"] == "Y" ? "산재" : "" : "";
			$gn = $ins4 == "Y" ? $row["gn"] == "Y" ? "건강" : "" : "";
			$km = $ins4 == "Y" ? $row["km"] == "Y" ? "국민" : "" : "";

			$ins4 = "";
			$ins4 .= ($ins4 != "" ? ", " : "").$go;
			$ins4 .= ($ins4 != "" ? ", " : "").$sn;
			$ins4 .= ($ins4 != "" ? ", " : "").$gn;
			$ins4 .= ($ins4 != "" ? ", " : "").$km;

			$sql = "select m03_name as name
					  from m03sugupja
					 where m03_ccode = '$code'
					   and m03_mkind = '$kind'
					   and m03_yoyangsa1 = '$jumin'";
			$con2->query($sql);
			$con2->fetch();
			$rows = $con2->row_count();
			$sugupja = "";
			
	
			for($j=0; $j<$rows; $j++){
				$r = $con2->select_row($j);
				//$sugupja .= ($j > 0 ? "," : "").$r["name"];
				$sugupja = $r["name"];
			}
			
		
			$con2->row_free();
			
			?>
				<tr>
					<td style='text-align:center; font-size:10pt;'><?=$seq?></td>
					<td style='text-align:center; font-size:10pt;'><?=$name?></td>
					<td style='text-align:center; font-size:10pt;'><?=$joinDate;?></td>
					<td style='text-align:center; font-size:10pt;'><?=$license;?></td>
					<td style='text-align:center; font-size:10pt;'><?=$phone;?></td>
					<td style='text-align:center; font-size:10pt;'><?=$sugupja;?></td>
					<td style='text-align:center; font-size:10pt;'><?=$family?></td>
					<td style='text-align:center; font-size:10pt;'><?=$ins4;?></td>
					<td style='text-align:center; font-size:10pt;'><?=$stat;?></td>
				</tr>
			<?
	
		}
	}else{
		echo "<tr><td style='text-align:center;' colspan='9'>::검색된 데이타가 없습니다.::</td></tr>";
	}
	$conn->row_free();

?>	
</table>
<?
	include_once('../inc/_db_close.php');
?>

