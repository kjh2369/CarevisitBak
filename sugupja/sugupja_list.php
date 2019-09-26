<?
	include("../inc/_db_open.php");
	include("../inc/_http_referer.php");
	include("../inc/_function.php");
	include("../inc/_page_list.php");
	include('../inc/_ed.php');
?>
<form name="center" method="post">
<table style="width:100%">
	<tr>
		<td class="title" colspan="4">수급자 조회</td>
	</tr>
	<?
		$target = "su";
		include("../main/center_info.php");
	?>
</table>
<table class="view_type1">
	
	<tr>
		<th style="width:5%; text-align:center; padding:0px;" scope="row">No</th>
		<th style="width:15%; text-align:center; padding:0px;" scope="row">기관명</th>
		<th style="width:7%; text-align:center; padding:0px;" scope="row">수급자명</th>
		<th style="width:13%; text-align:center; padding:0px;" scope="row">주민번호</th>
		<th style="width:11%; text-align:center; padding:0px;" scope="row">연락처</th>
		<th style="width:12%; text-align:center; padding:0px;" scope="row">대표요양보호사</th>
		<th style="width:20%; text-align:center; padding:0px;" scope="row">담당요양보호사</th>
		<th style="width:7%; text-align:center; padding:0px;" scope="row">수급현황</th>
	</tr>
	<?
		$wsl = "";
		if ($curMcode != ""){
			$wsl .= " and m00_mcode = '".$curMcode."'";
		}
		if ($curMkind != "all" and $curMkind != ""){
			$wsl .= " and m00_mkind = '".$curMkind."'";
		}
		if ($curCode1 != ""){
			$wsl .= " and m00_code1 = '".$curCode1."'";
		}
		if ($curCname != ""){
			$wsl .= " and m00_cname like '%".$curCname."%'";
		}
		if ($_POST["jName"] != ""){
			$wsl .= " and m03_name like '%".$_POST["jName"]."%'";
		}
		if ($_POST["cTel"] != ""){
			$wsl .= "  and (m03_tel like '".str_replace('-', '', $_POST["cTel"])
				 .  "%' or  m03_hp  like '".str_replace('-', '', $_POST["cTel"])
				 .  "%')";
		}
		if ($_POST["stat"] != ""){
			$wsl .= " and m03_sugup_status = '".$_POST["stat"]
				 .  "'";
		}

		$pageCount = $_POST["page"];

		if ($pageCount == ""){
			$pageCount = "1";
		}

		$pageCount = (intVal($pageCount) - 1) * 20;

		$sql = "select *"
		     . "  from m03sugupja"
			 . " inner join m00center"
			 . "    on m00_mcode = m03_ccode"
			 . "   and m00_mkind = m03_mkind"
			 . " where m03_ccode is not null"
			 .$wsl
			 . " order by m00_cname"
			 . ",         m03_name"
			 . " limit ".$pageCount.", 20";

		$conn->query($sql);
		$row = $conn->fetch();
		$row_count = $conn->row_count();

		if ($row_count > 0){
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);
				$yoyangsa = $row['m03_yoyangsa2_nm'].($row['m03_yoyangsa3_nm'] != '' ? ',' :'').$row['m03_yoyangsa3_nm'].($row['m03_yoyangsa4_nm'] != '' ? ',' :'').$row['m03_yoyangsa4_nm'].($row['m03_yoyangsa5_nm'] != '' ? ',' :'').$row['m03_yoyangsa5_nm'];
				?>
				<tr>
					<td style="text-align:center;"><?=$pageCount + ($i + 1);?></td>
					<td><?=left($row["m00_cname"], 9);?></td>
					<td style="text-align:left;"><a href="#" onClick="_sugupjaReg('suSearch','<?=$row["m03_ccode"];?>','<?=$row["m03_mkind"];?>','<?=$ed->en($row["m03_jumin"]);?>','<?=$_POST["jName"];?>','<?=$_POST["cTel"];?>');"><?=$row["m03_name"];?></a></td>
					<td style="text-align:center;"><?=getSSNStyle($row["m03_jumin"]);?></td>
					<td style="text-align:left;"><?=getPhoneStyle($row["m03_hp"]);?></td>
					<td style="text-align:left;"><?=$row['m03_yoyangsa1_nm'];?></td>
					<td style="text-align:left;" title="<?=$yoyangsa;?>"><?=left($yoyangsa, 15);?></td>
					<td style="text-align:left; padding-left:10px;"><?=$definition->SugupjaStatusGbn($row['m03_sugup_status']);?></td>
				</tr>
			<?
			}
		}else{
			echo "<tr><td style='text-align:center;' colspan='8'>::검색된 데이타가 없습니다.::</td></tr>";
		}

		$conn->row_free();
	?>
</table>
<?
	if ($row_count > 0){
		$sql = "select count(*)"
		     . "  from m03sugupja"
			 . " inner join m00center"
			 . "    on m00_mcode = m03_ccode"
			 . "   and m00_mkind = m03_mkind"
			 . " where m03_ccode is not null"
			 .$wsl;

		$conn->query($sql);
		$row = $conn->fetch();
		$row_count = $row[0];
		$conn->row_free();

		$params = array(
			'curMethod' => 'post',
			'curPage' => 'javascript:_sugupjaList',
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