<?
	include("../inc/_db_open.php");
	include("../inc/_http_referer.php");
	include("../inc/_function.php");
	include("../inc/_page_list.php");
?>
<form name="center" method="post">
<table style="width:100%;">
	<tr>
		<td class="title" colspan="4">요양보호사/직원 조회</td>
	</tr>
	<?
		$target = "yoy";
		include("../main/center_info.php");

		if ($curMkind == ''){
			$curMkind = 'all';
		}
	?>
</table>
<table class="view_type1">
<tr>
<th style="width:5%; text-align:center; padding:0px;" scope="row">No</th>
<?
	if ($curMkind == 'all'){
	?>
		<th style="width:16%; text-align:center; padding:0px;" scope="row">기관명</th>
	<?
	}
?>
<th style="width:12%; text-align:center; padding:0px;" scope="row">요양보호사명</th>
<th style="width:12%; text-align:center; padding:0px;" scope="row">요양보호사코드</th>
<th style="width:12%; text-align:center; padding:0px;" scope="row">연락처</th>
<th style="width:8%; text-align:center; padding:0px;" scope="row">주민번호</th>
<th style="width:7%; text-align:center; padding:0px;" scope="row">고용상태</th>
<th style="width:11%; text-align:center; padding:0px;" scope="row">직원구분</th>
<th style="width:6%; text-align:center; padding:0px;" scope="row">바우처</th>
<th style="width:7%; text-align:center; padding:0px;" scope="row">재가요양</th>
<th style="width:4%; text-align:center; padding:0px;" scope="row">보험</th>
</tr>
<?
	$wsl = "";

	if ($curMcode != ""){
		$wsl .= " and m00_mcode = '".$curMcode."'";
	}
	if ($curMkind != "all"){
		$wsl .= " and m00_mkind = '".$curMkind."'";
	}
	if ($curCode1 != ""){
		$wsl .= " and m00_code1 = '".$curCode1."'";
	}
	if ($curCname != ""){
		$wsl .= " and m00_cname like '%".$curCname."%'";
	}
	if ($_POST["jName"] != ""){
		$wsl .= " and m02_yname like '%".$_POST["jName"]."%'";
	}
	if ($_POST["cTel"] != ""){
		$wsl .= "  and (m02_ytel  like '".str_replace('-', '', $_POST["cTel"])
			 .  "%' or  m02_ytel2 like '".str_replace('-', '', $_POST["cTel"])
			 .  "%')";
	}
	if ($temp_stat != "all"){
		$wsl .= " and m02_ygoyong_stat = '".$temp_stat
			 .  "'";
	}

	$pageCount = $_POST["page"];

	if ($pageCount == ""){
		$pageCount = "1";
	}

	$pageCount = (intVal($pageCount) - 1) * 20;

	$sql = "select *"
		 . "  from m02yoyangsa"
		 . " inner join m00center"
		 . "    on m00_mcode = m02_ccode"
		 . "   and m00_mkind = m02_mkind"
		 . " where m02_ccode is not null"
		 .$wsl
		 . " order by m00_cname"
		 . ",         m02_yname"
		 . " limit ".$pageCount.", 20";
	$conn->query($sql);
	$row = $conn->fetch();
	$row_count = $conn->row_count();

	if ($row_count > 0){
		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);
			?>
				<tr>
				<td style="text-align:center;"><?=$pageCount + ($i + 1);?></td>
				<?
					if ($curMkind == 'all'){
					?>
						<td><?=left($row["m00_cname"], 8);?></td>
					<?
					}

					switch($row['m02_ygoyong_stat']){
					case '1':
						$soyongStat = '활동';
						break;
					case '2':
						$soyongStat = '휴직';
						break;
					case '9':
						$soyongStat = '퇴사';
						break;
					}

					switch($row['m02_jikwon_gbn']){
					case 'Y':
						$jikwonGbn = '요양사';
						break;
					case 'M':
						$jikwonGbn = '관리자';
						break;
					case 'A':
						$jikwonGbn = '관리자+요양사';
						break;
					}
				?>
				<td style="text-align:left;"><a href="#" onClick="_centerYoyReg('yoySearch','<?=$row["m02_ccode"];?>','<?=$row["m02_mkind"];?>','','','<?=$row["m02_key"];?>','<?=$_POST["jName"];?>','<?=$_POST["cTel"];?>');"><?=$row["m02_yname"];?></a></td>
				<td style="text-align:center;"><?=$row["m02_ycode"];?></td>
				<td style="text-align:center;"><?=getPhoneStyle($row["m02_ytel"]);?></td>
				<td style="text-align:center;"><?=subStr($row['m02_yjumin'],0,6).'-'.subStr($row['m02_yjumin'],6,1);?></td>
				<td style="text-align:center;"><?=$soyongStat;?></td>
				<td style="text-align:left;"><?=$jikwonGbn;?></td>
				<td style="text-align:center;"><?=($row["m02_sign1"] == 'Y' ? '유' : '무');?></td>
				<td style="text-align:center;"><?=($row["m02_sign2"] == 'Y' ? '유' : '무');?></td>
				<td style="text-align:center;"><?=($row["m02_ins_yn"] == 'Y' ? '유' : '무');?></td>
				</tr>
			<?
		}
	}else{
		echo "<tr><td style='text-align:center;' colspan='10'>::검색된 데이타가 없습니다.::</td></tr>";
	}

	$conn->row_free();
?>
</table>
<?
	if ($row_count > 0){
		$sql = "select count(*)"
			 . "  from m02yoyangsa"
			 . " inner join m00center"
			 . "    on m00_mcode = m02_ccode"
			 . "   and m00_mkind = m02_mkind"
			 . " where m02_ccode is not null"
			 .$wsl;

		$conn->query($sql);
		$row = $conn->fetch();
		$row_count = $row[0];
		$conn->row_free();

		$params = array(
			'curMethod' => 'post',
			'curPage' => 'javascript:_centerYoyList',
			'curPageNum' => $_POST["page"],
			'pageVar' => 'page',
			'extraVar' => '&aaa=1&bbb=abc',
			'totalItem' => $row_count,
			'perPage' => 10,
			'perItem' => 20,
			'prevPage' => '[이전]',
			'nextPage' => '[다음]',
			'prevPerPage' => '[이전10페이지]',
			'nextPerPage' => '[다음10페이지]',
			'firstPage' => '[처음]',
			'lastPage' => '[끝]',
			'pageCss' => 'page_list_1',
			'curPageCss' => 'page_list_2'
		);

		echo "<table><tr><td style='border:0px;'>";

		$paging = new YsPaging($params);
		$paging->printPaging();

		echo "</td></tr></table>";
	}
?>
</form>
