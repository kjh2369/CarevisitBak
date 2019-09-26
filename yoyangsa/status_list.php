<?
	include("../inc/_header.php");
	include("../inc/_myFun.php");
	include("../inc/_page_list.php");
	include('../inc/_ed.php');
	
	
	if ($_SESSION["userLevel"] == "A"){
		$mCode = $_REQUEST["mCode"];
	}else{
		$mCode = $_SESSION["userCenterCode"];
	}

	$item_count = 20;
	$page_count = 10;
	$page = $_REQUEST["page"];

	if (!is_numeric($page)) $page = 1;

	$find_center_code = $_REQUEST['find_center_code'];
	$find_center_name = $_REQUEST['find_center_name'];


	$con2 = new connection();

	$mCode = $_POST["mCode"];
	$mKind = $_POST["mKind"];
	$mFamily = $_POST["mFamily"];
	$mEmployment = $_POST["mEmployment"];
	$mInsurance = $_POST["mInsurance"];

	if ($_SESSION["userLevel"] == "A"){
		$mCode = $_REQUEST["mCode"];
	}else{
		$mCode = $_SESSION["userCenterCode"];
	}

	$item_count = 20;
	$page_count = 10;
	$page = $_REQUEST["page"];

	if (!is_numeric($page)) $page = 1;

	$find_center_code = $_REQUEST['find_center_code'];
	$find_center_name = $_REQUEST['find_center_name'];
?>

<!--<table class="view_type1" style="width:100%; height:100%;">-->
<table class="my_table my_border">
<colGroup>
	<?if ($mKind != ""){?>
			<col width='5%'>
			<col width='12%'>
			<col width='8%'>
			<col width='9%'>
			<col width='14%'>
			<col width='10%'>
			<col width='7%'>
			<col width='5%'>
			<col width='16%'>
			<col width='5%'>
			<col width='5%'>
	<? }else{ ?>
			<col width='5%'>
			<col width='12%'>
			<col width='8%'>
			<col width='9%'>
			<col width='14%'>
			<col width='10%'>
			<col width='7%'>
			<col width='5%'>
			<col width='16%'>
			<col width='5%'>
			<col width='5%'>
	<?}?>
</colGroup>
<thead>
	
		<tr>
			<th class="head">No</th>
			<th class="head">기관</th>
			<th class="head">이름</th>
			<th class="head">입사일</th>
			<th class="head">자격증</th>
			<th class="head">연락처</th>
			<th class="head">담당</th>
			<th class="head">가족</th>
			<th class="head">4대보험</th>
			<th class="last">고용</th>
		</tr>
</thead>

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
	if ($mEmployment != "") $sql .= " and m02_ygoyong_stat = '$mEmployment'";
	if ($mInsurance != "") $sql .= " and m02_y4bohum_umu = '$mInsurance'";

	$sql .= " order by kind, joinDate, name";

	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();

	if ($rowCount > 0){
	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$seq = $i + 1;
		$kind = $row["kind"];
		$kindName = $myF->splits($row["kindName"], 5);
		$titleKindName = $myF->len($row["kindName"]) > 3 ? $row["kindName"] : "";
		$name = $myF->splits($row["name"], 3);
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
		 where m03_ccode = '$mCode'
		   and m03_mkind = '$kind'
		   and m03_yoyangsa1 = '$jumin'";
		$con2->query($sql);
		$con2->fetch();
		$rows = $con2->row_count();
		$sugupja = "";
		$respond = "";

		for($j=0; $j<$rows; $j++){
			$r = $con2->select_row($j);
			$sugupja .= ($j > 0 ? "," : "").$r["name"];
		}

		if ($rows > 1){
			$respond = $rows."명";
			$titleRespond = $sugupja;
		}else{
			$respond = $sugupja;
			$titleRespond = "";
		}

		$con2->row_free();
		
		?>
		<tr>
			<td class="center"><?=$seq;?></td>
			<td class="left"><?=$kindName;?></td>
		
			<td class="left"><?=$name;?></td>
			<td class="left"><?=$joinDate;?></td>
			<td class="left"><?=$license;?></td>
			<td class="left"><?=$phone;?></td>
			
			<td class="left"><?=$respond;?></td>
			<td class="center"><?=$family;?></td>
			<td class="left"><?=$ins4;?></td>
			<td class="last"><?=$stat;?></td>
		</tr>
		<?
			}
		}else{
			?>
			<tr><td class="last" style='text-align:center;' colspan='11'>::검색된 데이타가 없습니다.::</td></tr>
			<?
		}

		$conn->row_free();
?>
</table>
<?
	include("../inc/_footer.php");
?>