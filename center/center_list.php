<?
	include("../inc/_db_open.php");
	include("../inc/_http_referer.php");
	include("../inc/_function.php");
	include("../inc/_page_list.php");
	include("center_where.php");
?>
<table class="view_type1">
	<tr>
		<th style="width:76px; text-align:center; padding:0px;" scope="row">No</th>
		<th style="width:300px; text-align:center; padding:0px;" scope="row">기관명</th>
		<th style="width:150px; text-align:center; padding:0px;" scope="row">기관기호</th>
		<th style="width:150px; text-align:center; padding:0px;" scope="row">대표자명</th>
		<th style="width:150px; text-align:center; padding:0px;" scope="row">전화번호</th>
	</tr>
	<?
		$wsl = "";
		if ($_SESSION["userLevel"] == "A"){
			if ($_POST["mCode"] != ""){
				$wsl .= " and m00_mcode = '".$_POST["mCode"]."'";
			}
			if ($_POST["code1"] != ""){
				$wsl .= " and m00_code1 = '".$_POST["code1"]."'";
			}
			if ($_POST["cName"] != ""){
				$wsl .= " and m00_cname like '%".$_POST["cName"]."%'";
			}
		}else{
			$wsl .= " and m00_mcode = '".$_SESSION["userCenterCode"]."'";
		}
		if ($_POST["mKind"] != "all" and $_POST["mKind"] != ""){
			$wsl .= " and m00_mkind = '".$_POST["mKind"]."'";
		}

		$sql = "select count(*)"
		     . "  from m00center"
			 . " where m00_mcode is not null"
			 . $wsl;

		$conn->query($sql);
		$row = $conn->fetch();
		$row_count = $row[0];
		$conn->row_free();

		$params = array(
			'curMethod' => 'post',
			'curPage' => 'javascript:_centerList',
			'curPageNum' => $_POST["page"],
			'pageVar' => 'page',
			'extraVar' => '&aaa=1&bbb=abc',
			'totalItem' => $row_count,
			'perPage' => 10,
			'perItem' => 10,
			'prevPage' => '[이전]',
			'nextPage' => '[다음]',
			'prevPerPage' => '[이전10페이지]',
			'nextPerPage' => '[다음10페이지]',
			'firstPage' => '[처음]',
			'lastPage' => '[끝]',
			'pageCss' => 'page_list_1',
			'curPageCss' => 'page_list_2'
		);

		$pageCount = $_POST["page"];

		if ($pageCount == ""){
			$pageCount = "1";
		}

		$pageCount = (intVal($pageCount) - 1) * 10;

		$sql = "select *"
		     . "  from m00center"
			 . " where m00_mcode is not null"
			 . $wsl
		     . " order by m00_cname"
			 . " limit ".$pageCount.", 10";
		$conn->query($sql);
		$row = $conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);
		?>
			<tr>
				<td style="text-align:center;"><?=$pageCount + ($i + 1);?></th>
				<td><a href="#" onClick="_centerReg('search','<?=$row["m00_mcode"];?>','<?=$row["m00_mkind"];?>','<?=$row["m00_code1"];?>');"><?=$row["m00_cname"];?></a></th>
				<td style="text-align:center;"><?=$row["m00_mcode"];?></th>
				<td style="text-align:center;"><?=$row["m00_mname"];?></th>
				<td style="text-align:center;"><?=getPhoneStyle($row["m00_ctel"]);?></th>
			</tr>
		<?
		}

		$conn->row_free();
	?>
</table>
<table style="width:100%;">
	<tr>
		<td style="border:0px;">
		<?
			$paging = new YsPaging($params);
			$paging->printPaging();
		?>
		</td>
	</tr>
</table>