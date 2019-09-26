<?
	include_once("../inc/_header.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_myFun.php");

	$domain = $myF->_domain();
?>
<style>
body{
	margin-top:0px;
	margin-left:0px;
}
</style>
<script src="../js/center.js" type="text/javascript"></script>
<script language='javascript'>
<!--
var retVal = 'cancel';

function _currnetRow(value1, value2, value3, value4, value5, value6, value7, value8, value9){
	var currentItem = new Array();

	currentItem[0] = value1;
	currentItem[1] = value2;
	currentItem[2] = value3;
	currentItem[3] = value4;
	currentItem[4] = value5;
	currentItem[5] = value6;
	currentItem[6] = value7;
	currentItem[7] = value8;
	currentItem[8] = value9;

	window.returnValue = currentItem;
	window.close();
}

function _pageList(page){
	document.center.page.value = page;
	_submit();
}

function _submit(){
	document.center.action = "_help.php";
	document.center.submit();
}

function _findAddres(){
	if (document.center.dong.value == ''){
		alert('동명을 입력하여 주십시오.');
		document.center.dong.focus();
		return;
	}
	document.center.action = "_help.php";
	document.center.submit();
}

function _findYoy(){
	document.center.submit();
}

function _check(gubun, object){
	var check = document.getElementsByName('check[]');
	var check_count = 0;

	for(var i=0; i<check.length; i++){
		if(check[i].checked){
			check_count++;
			break;
		}
	}

	if(check_count == 0){
		if (gubun == 'submit'){
			alert('선택된 요양보호사가 없습니다. 확인하여 주십시오.');
		}
		return false;
	}

	if (check_count > 5){
		alert('요양보호사는 초대 5명까지 선택가능합니다. 확인하여 주십시오.');
		if (gubun == 'check'){
			object.checked = false;
		}
		return false;
	}

	return true;
}

function _checkedT(value){
	var check = document.getElementsByName('check[]');

	for(var i=0; i<check.length; i++){
		if(check[i].value == value){
			check[i].checked = true;
			break;
		}
	}
}

function _submitSuYoyFind(){
	if (!_check('submit')){
		return;
	}

	var currentItem = new Array();
	var check = document.getElementsByName('check[]');

	for(var i=0; i<check.length; i++){
		if(check[i].checked){
			currentItem[i] = check[i].value;
		}
	}

	window.returnValue = currentItem;
	window.close();
}
//-->
</script>
<?
	if ($_REQUEST["r_gubun"] == "centerList" || $_REQUEST["r_gubun"] == "addCenterList"){
		$yoy = true;
		include("../center/center_where.php");
	?>
		<table class="my_table my_border" style="width:100%;">
			<tr>
				<th style="width:5%; text-align:center; padding:0px;">NO</th>
				<th style="width:20%; text-align:center; padding:0px;">기관명</th>
				<th style="width:15%; text-align:center; padding:0px;">기관기호</th>
				<th style="width:10%; text-align:center; padding:0px;">대표자명</th>
				<th style="width:15%; text-align:center; padding:0px;">대표전화번호</th>
				<th style="width:35%; text-align:center; padding:0px;">기관주소</th>
			</tr>
			<?
				$wsl   = "";
				$order = "";

				if ($_POST["searchMcode"] != ""){
					$wsl .= " and m00_mcode >= '".$_POST["searchMcode"]."'";
					$order .= " m00_mcode ";
				}
				/*
				if ($_POST["searchMkind"] != "all" and $_POST["searchMkind"] != ""){
					$wsl .= " and m00_mkind = '".$_POST["searchMkind"]."'";
				}
				*/
				if ($_POST["searchCode1"] != ""){
					$wsl .= " and m00_code1 = '".$_POST["searchCode1"]."'";
					$order .= ($order != '' ? "," : "")." m00_code1 ";
				}

				if ($_POST["searchCname"] != ""){
					$wsl .= " and m00_store_nm >= '".$_POST["searchCname"]."'";
					$order .= ($order != '' ? "," : "")." m00_store_nm ";
				}

				if ($_REQUEST["r_gubun"] == "addCenterList"){
					$sql = " select b02_center from b02center ";

					$conn->query($sql);
					$conn->fetch();

					$row_count = $conn->row_count();

					for($i=0; $i<$row_count; $i++){
						$row = $conn->select_row($i);

						$wsl .= " and m00_mcode != '".$row['b02_center']."'";
					}

					$conn->row_free();

					$wsl .= " and m00_domain = '".$domain."'";
				}

				if($order == ''){
					$order = " m00_store_nm ";
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
					'curMethod'		=> 'post',
					'curPage'		=> 'javascript:_pageList',
					'curPageNum'	=> $_POST["page"],
					'pageVar'		=> 'page',
					'extraVar'		=> '&aaa=1&bbb=abc',
					'totalItem'		=> $row_count,
					'perPage'		=> 10,
					'perItem'		=> 10,
					'prevPage'		=> '[이전]',
					'nextPage'		=> '[다음]',
					'prevPerPage'	=> '[이전10페이지]',
					'nextPerPage'	=> '[다음10페이지]',
					'firstPage'		=> '[처음]',
					'lastPage'		=> '[끝]',
					'pageCss'		=> 'page_list_1',
					'curPageCss'	=> 'page_list_2'
				);

				$pageCount = $_POST["page"];

				if ($pageCount == ""){
					$pageCount = "1";
				}

				$pageCount = (intVal($pageCount) - 1) * 10;

				$sql = "select m00_mcode
						,      m00_mkind
						,      m00_code1
						,      m00_cname
						,      m00_mname
						,      m00_store_nm
						,      m00_cpostno
						,      m00_caddr1
						,      m00_caddr2
						,      m00_cont_date
						,      m00_close_cond
						,      m00_ctel"
					 . "  from m00center"
					 . " where m00_mcode is not null"
					 . $wsl
					 . " order by $order"
					 . " limit ".$pageCount.", 10";

				$conn->query($sql);
				$row = $conn->fetch();
				$row_count = $conn->row_count();

				for($i=0; $i<$row_count; $i++){
					$row = $conn->select_row($i);

					$cName = left($row["m00_store_nm"], 10);
					$cAddr = "[".substr($row["m00_cpostno"],0,3)."-".substr($row["m00_cpostno"],3,6)."] ".$row["m00_caddr1"]." ".$row["m00_caddr2"];

					if ($row["m00_mkind"] == "0"){
						$mKind = "재가요양기관";
					}else if($row["m00_mkind"] == "1"){
						$mKind = "바우처1";
					}else if($row["m00_mkind"] == "2"){
						$mKind = "바우처2";
					}else if($row["m00_mkind"] == "3"){
						$mKind = "바우처3";
					}else if($row["m00_mkind"] == "4"){
						$mKind = "바우처4";
					}
				?>
					<tr>
						<td style="padding:0px; text-align:center;"><?=$pageCount + ($i + 1);?></th>
						<td style="padding:0px;"><a href="#" onClick="_currnetRow('<?=$row["m00_mcode"];?>','<?=$row["m00_mkind"];?>','<?=$row["m00_code1"];?>','<?=addslashes($row["m00_cname"]);?>','<?=$mKind;?>','<?=$row["m00_mname"];?>','<?=$row["m00_cont_date"];?>','<?=$row["m00_close_cond"];?>','<?=$cAddr;?>');"><?=$cName;?></a></th>
						<td style="text-align:center; padding:0px;"><?=$row["m00_mcode"];?></th>
						<td style="text-align:center; padding:0px;"><?=$row["m00_mname"];?></th>
						<td style="text-align:center; padding:0px;"><?=getPhoneStyle($row["m00_ctel"]);?></th>
						<td style="padding:0px;"><?=left($cAddr, 28);?></th>
					</tr>
				<?
				}
				$conn->row_free();
			?>
		</table>
		<table style="width:100%;">
			<tr>
				<td style="text-align:center; border:0px;">
				<?
					$paging = new YsPaging($params);
					$paging->printPaging();
				?>
				</td>
			</tr>
		</table>
	<?
	}else{
		$submit = false;
		$submitSub = "";

		switch($_REQUEST["r_gubun"]){
			case "address":
				$title = "주소검색";
				$findTitle = "동명";
				$findSub = "_findAddres();";
				$colStr1 = "우편번호";
				$colStr2 = "동명";
				$colPer1 = "25";
				$colPer2 = "75";
				break;
			case "yoyFind":
				$title = "담당직원검색";
				$findTitle = "성명";
				$findSub = "_findYoy();";
				$colStr1 = "생년월일";
				$colStr2 = "성명";
				$colPer1 = "40";
				$colPer2 = "60";
				break;
			case "suYoyFind":
				$title = "담당요양보호사검색";
				$findTitle = "성명";
				$findSub = "_findYoy();";
				$colStr1 = "생년월일";
				$colStr2 = "성명";
				$submit = true;
				$submitSub = "_submitSuYoyFind();";
				break;
			case "sugaFind":
				$title = "수가검색";
				$findTitle = "명칭";
				$findSub = "_findYoy();";
				$colStr1 = "대응코드";
				$colStr2 = "명칭";
				$colPer1 = "30";
				$colPer2 = "70";
				break;
		}
		?>
		<form name="center" method="post">
		<table class="view_type1" style="width:100%;">
			<tr>
				<th style="text-align:center; padding:0px;" colspan="3">::<?=$title;?>::</th>
			</tr>
			<tr>
				<th style="width:15%; text-align:center; padding:0px;"><?=$findTitle;?></th>
				<th style="width:75%; text-align:center; padding:0px;"><input name="dong" type="text" value="<?=$_POST["dong"];?>" style="width:96%;"></th>
				<th style="width:10%; text-align:center; padding:0px;"><a onClick="<?=$findSub;?>"><img src="../image/btn_find.png"></a></th>
			</tr>
			<tr>
				<td style="width:100%; text-align:center; vertical-align:top; padding:0px;" colspan="3">
					<table class="view_type1" style="width:100%;">
						<tr>
						<?
							if ($_REQUEST["r_gubun"] == "suYoyFind"){
							?>
								<th style="width:20%; text-align:center; padding:0px;">선택</th>
								<th style="width:30%; text-align:center; padding:0px;"><?=$colStr1;?></th>
								<th style="width:50%; text-align:center; padding:0px;"><?=$colStr2;?></th>
							<?
							}else{
							?>
								<th style="width:<?=$colPer1;?>%; text-align:center; padding:0px;"><?=$colStr1;?></th>
								<th style="width:<?=$colPer2;?>%; text-align:center; padding:0px;"><?=$colStr2;?></th>
							<?
							}
						?>
						</tr>
					</table>
					<div style="overflow-x:hidden; overflow-y:scroll; width:100%; height:<?if($submit){echo "175px";}else{echo "205px";}?>;">
					<table style="width:100%;">
					<?
						switch($_REQUEST["r_gubun"]){
							case "address":
								if ($_POST["dong"] != ''){
									$sql = "select zipcode"
										 . ",      concat(sido, ' ', gubun, ' ', dong)"
										 . ",      bunji"
										 . "  from zipcode"
										 . " where concat(gubun, ' ', dong) like '%".$_POST["dong"]
										 . "%'"
										 . " order by zipcode";

									$conn->query($sql);
									$row = $conn->fetch();
									$row_count = $conn->row_count();

									for($i=0; $i<$row_count; $i++){
										$row = $conn->select_row($i)
										?>
											<tr>
												<td style="width:25%; text-align:center; padding:0px;"><?=$row[0];?></td>
												<td style="width:75%; text-align:left; padding:0px;"><a href="#" onClick="_currnetRow('<?=$row[0];?>','<?=$row[1];?>','<?=$row[2];?>');"><?=$row[1];?> <?=$row[2];?></a></td>
											</tr>
										<?
									}
									$conn->row_free();
								}
								break;

							case "yoyFind":
								$family_yn = $_GET['family_yn'];
								$svc_cd = $_GET['svcSubCode'];

								if ($_GET["mKey"] != ""){
									$sql = "select m03_yoyangsa1"
										 . ",      m03_yoyangsa2"
										 . ",      m03_ylvl
											,      m03_partner
											,      m03_stat_nogood"
										 . "  from m03sugupja"
										 . " where m03_ccode = '".$_GET["mCode"]
										 . "'  and m03_key   = '".$_GET["mKey"]
										 . "'";
									$conn->query($sql);
									$row = $conn->fetch();
									$yoy[1] = $row["m03_yoyangsa1"];
									$yoy[2] = $row["m03_yoyangsa2"];
									$partner_yn = $row['m03_partner'];
									$stat_nogood = $row['m03_stat_nogood'];
									$sugupjaLevel = $row['m03_ylvl'];
									$conn->row_free();

									$caseSql = "";
									$yoyIndex = 1;

									for($i=1; $i<=2; $i++){
										if ($yoy[$i] != ""){
											$caseSql .= " when m02_yjumin = '".$yoy[$i]."' then ".$yoyIndex;
											$yoyIndex++;
										}
									}

									if ($caseSql != ""){
										$orderSql = " order by case ".$caseSql." else 6 end, m02_yname";
									}else{
										$orderSql = " order by m02_yname";
									}
								}else{
									$orderSql = " order by m02_yname";
								}
								$sql = "select distinct m02_ycode"
									 . ",      m02_yjumin"
									 . ",      m02_yname"
									 . ",      m02_ygupyeo_kind"
									 . ",      case when m02_ygupyeo_kind in ('1','2') then m02_ygibonkup else 0 end"
									 . "  from m02yoyangsa"
									 . " where m02_ccode = '".$_GET["mCode"]
									 . "'  and m02_ygoyong_stat = '1'
									       and m02_del_yn = 'N'";

								if (!empty($_GET['mKind'])) $sql .= ' and m02_mkind = \''.$_GET['mKind'].'\'';

								if ($svc_cd == '200'){
									if ($family_yn == 'Y'){
										#$sql .= " and m02_yfamcare_umu = 'Y'";
									}else if ($family_yn == 'N'){
										#$sql .= " and m02_ygupyeo_kind != '0'";
									}
								}else{
									#$sql .= " and m02_ygupyeo_kind != '0'";
								}

								if ($_GET["yoy"] != ""){
									$sql .= " and m02_yjumin not in(".stripSlashes($_GET["yoy"]).")";
									/*
									$sql .= " and m02_yjumin not in(
											  select distinct yoyCode
											    from (
													 select t01_yoyangsa_id1 as yoyCode
													   from t01iljung
													  where t01_ccode = '".$_GET["mCode"]."'
													    and t01_sugup_date like '".$_GET["mDate"]."%'
													    and (date_format(concat(t01_sugup_date, t01_sugup_fmtime, '00'), '%Y-%m-%d %H:%i') between date_format(concat(t01_sugup_date, '".$_GET["mFromTime"]."00'), '%Y-%m-%d %H:%i') and date_format(concat(t01_sugup_date, '".$_GET["mToTime"]."00'), '%Y-%m-%d %H:%i')
													     or  date_format(concat(t01_sugup_date, t01_sugup_totime, '00'), '%Y-%m-%d %H:%i') between date_format(concat(t01_sugup_date, '".$_GET["mFromTime"]."00'), '%Y-%m-%d %H:%i') and date_format(concat(t01_sugup_date, '".$_GET["mToTime"]."00'), '%Y-%m-%d %H:%i'))
													    and t01_del_yn = 'N'
														and t01_status_gbn in (case when t01_sugup_date >= '".date('Ymd',mkTime())."' then '0' else '' end, '9')
													  union all
													 select t01_yoyangsa_id2 as yoyCode
													   from t01iljung
													  where t01_ccode = '".$_GET["mCode"]."'
													    and t01_sugup_date like '".$_GET["mDate"]."%'
													    and (date_format(concat(t01_sugup_date, t01_sugup_fmtime, '00'), '%Y-%m-%d %H:%i') between date_format(concat(t01_sugup_date, '".$_GET["mFromTime"]."00'), '%Y-%m-%d %H:%i') and date_format(concat(t01_sugup_date, '".$_GET["mToTime"]."00'), '%Y-%m-%d %H:%i')
													     or  date_format(concat(t01_sugup_date, t01_sugup_totime, '00'), '%Y-%m-%d %H:%i') between date_format(concat(t01_sugup_date, '".$_GET["mFromTime"]."00'), '%Y-%m-%d %H:%i') and date_format(concat(t01_sugup_date, '".$_GET["mToTime"]."00'), '%Y-%m-%d %H:%i'))
													    and t01_del_yn = 'N'
														and t01_status_gbn in (case when t01_sugup_date >= '".date('Ymd',mkTime())."' then '0' else '' end, '9')
													 ) as t
											   where yoyCode != '')";
									*/
								}

								if ($_POST["dong"] != ''){
									$sql .= " and m02_yname like'%".$_POST["dong"]."%'";
								}

								$sql .= " group by m02_yjumin, m02_yname";
								$sql .= $orderSql;

								$conn->query($sql);
								$row = $conn->fetch();
								$row_count = $conn->row_count();

								for($i=0; $i<$row_count; $i++){
									$row = $conn->select_row($i);

									if ($row['m02_ygupyeo_kind'] == '1' or $row['m02_ygupyeo_kind'] == '2'){
										$timePay = $conn->get_time_pay($_GET["mCode"], $_GET["mKind"], $row['m02_yjumin'], $sugupjaLevel);

										if ($timePay == ''){
											$timePay = $row[4];
										}
									}else{
										$timePay = $row[4];
									}

									if ($yoy[1] == $row[1]){
										$p_yn = $partner_yn;
									}else{
										$p_yn = $stat_nogood;
									}
									?>
										<tr>
											<td style="width:40%; text-align:center; padding:0px;"><?=$myF->issToBirthday($row[1],'.');?></td>
											<td style="width:60%; text-align:left; padding:0px;"><a href="#" onClick="_currnetRow('<?=$row[0];?>','<?=$row[1];?>','<?=$row[2];?>','<?=$timePay;?>', '<?=$p_yn;?>');"><?=$row[2];?></a></td>
										</tr>
									<?
								}
								$conn->row_free();
								break;

							case "suYoyFind":
								$sql = "select m03_yoyangsa1"
									 . ",      m03_yoyangsa2"
									 . ",      m03_yoyangsa3"
									 . ",      m03_yoyangsa4"
									 . ",      m03_yoyangsa5"
									 . ",      m03_ylvl"
									 . "  from m03sugupja"
									 . " where m03_ccode = '".$_GET["mCode"]
									 . "'  and m03_mkind = '".$_GET["mKind"]
									 . "'  and m03_key   = '".$_GET["mKey"]
									 . "'";
								$conn->query($sql);
								$row = $conn->fetch();
								$yoy[1] = $row["m03_yoyangsa1"];
								$yoy[2] = $row["m03_yoyangsa2"];
								$yoy[3] = $row["m03_yoyangsa3"];
								$yoy[4] = $row["m03_yoyangsa4"];
								$yoy[5] = $row["m03_yoyangsa5"];
								$sugupjaLevel = $row['m03_ylvl'];
								$conn->row_free();

								$sql = "select m02_ycode"
									 . ",      m02_yjumin"
									 . ",      m02_yname"
									 . ",      m02_ygupyeo_kind"
									 . ",      case when m02_ygupyeo_kind in ('1','2') then m02_ygibonkup else 0 end"
									 . "  from m02yoyangsa"
									 . " where m02_ccode = '".$_GET["mCode"]
									 . "'  and m02_mkind = '0' /*(select min(m02_mkind) from m02yoyangsa as tmp where tmp.m02_ccode = m02yoyangsa.m02_ccode and tmp.m02_yjumin = m02yoyangsa.m02_yjumin and tmp.m02_del_yn = 'N')*/"
									 . "   and m02_ygoyong_stat = '1'";

								$sql .= " and m02_yjumin not in(
											  select distinct yoyCode
											    from (
													 select t01_yoyangsa_id1 as yoyCode
													   from t01iljung
													  where t01_ccode = '".$_GET["mCode"]."'
													    and t01_mkind = '".$_GET["mKind"]."'
													    and t01_sugup_date like '".$_GET["mDate"]."%'
													    and (date_format(concat(t01_sugup_date, t01_sugup_fmtime, '00'), '%Y-%m-%d %H:%i') between date_format(concat(t01_sugup_date, '".$_GET["mFromTime"]."00'), '%Y-%m-%d %H:%i') and date_format(concat(t01_sugup_date, '".$_GET["mToTime"]."00'), '%Y-%m-%d %H:%i')
													     or  date_format(concat(t01_sugup_date, t01_sugup_totime, '00'), '%Y-%m-%d %H:%i') between date_format(concat(t01_sugup_date, '".$_GET["mFromTime"]."00'), '%Y-%m-%d %H:%i') and date_format(concat(t01_sugup_date, '".$_GET["mToTime"]."00'), '%Y-%m-%d %H:%i'))
													    and t01_del_yn = 'N'
														and t01_status_gbn in (case when t01_sugup_date >= '".date('Ymd',mkTime())."' then '0' else '' end, '9')
													  union all
													 select t01_yoyangsa_id2 as yoyCode
													   from t01iljung
													  where t01_ccode = '".$_GET["mCode"]."'
													    and t01_mkind = '".$_GET["mKind"]."'
													    and t01_sugup_date like '".$_GET["mDate"]."%'
													    and (date_format(concat(t01_sugup_date, t01_sugup_fmtime, '00'), '%Y-%m-%d %H:%i') between date_format(concat(t01_sugup_date, '".$_GET["mFromTime"]."00'), '%Y-%m-%d %H:%i') and date_format(concat(t01_sugup_date, '".$_GET["mToTime"]."00'), '%Y-%m-%d %H:%i')
													     or  date_format(concat(t01_sugup_date, t01_sugup_totime, '00'), '%Y-%m-%d %H:%i') between date_format(concat(t01_sugup_date, '".$_GET["mFromTime"]."00'), '%Y-%m-%d %H:%i') and date_format(concat(t01_sugup_date, '".$_GET["mToTime"]."00'), '%Y-%m-%d %H:%i'))
													    and t01_del_yn = 'N'
														and t01_status_gbn in (case when t01_sugup_date >= '".date('Ymd',mkTime())."' then '0' else '' end, '9')
													  union all
													 select t01_yoyangsa_id3 as yoyCode
													   from t01iljung
													  where t01_ccode = '".$_GET["mCode"]."'
													    and t01_mkind = '".$_GET["mKind"]."'
													    and t01_sugup_date like '".$_GET["mDate"]."%'
													    and (date_format(concat(t01_sugup_date, t01_sugup_fmtime, '00'), '%Y-%m-%d %H:%i') between date_format(concat(t01_sugup_date, '".$_GET["mFromTime"]."00'), '%Y-%m-%d %H:%i') and date_format(concat(t01_sugup_date, '".$_GET["mToTime"]."00'), '%Y-%m-%d %H:%i')
													     or  date_format(concat(t01_sugup_date, t01_sugup_totime, '00'), '%Y-%m-%d %H:%i') between date_format(concat(t01_sugup_date, '".$_GET["mFromTime"]."00'), '%Y-%m-%d %H:%i') and date_format(concat(t01_sugup_date, '".$_GET["mToTime"]."00'), '%Y-%m-%d %H:%i'))
													    and t01_del_yn = 'N'
														and t01_status_gbn in (case when t01_sugup_date >= '".date('Ymd',mkTime())."' then '0' else '' end, '9')
													  union all
													 select t01_yoyangsa_id4 as yoyCode
													   from t01iljung
													  where t01_ccode = '".$_GET["mCode"]."'
													    and t01_mkind = '".$_GET["mKind"]."'
													    and t01_sugup_date like '".$_GET["mDate"]."%'
													    and (date_format(concat(t01_sugup_date, t01_sugup_fmtime, '00'), '%Y-%m-%d %H:%i') between date_format(concat(t01_sugup_date, '".$_GET["mFromTime"]."00'), '%Y-%m-%d %H:%i') and date_format(concat(t01_sugup_date, '".$_GET["mToTime"]."00'), '%Y-%m-%d %H:%i')
													     or  date_format(concat(t01_sugup_date, t01_sugup_totime, '00'), '%Y-%m-%d %H:%i') between date_format(concat(t01_sugup_date, '".$_GET["mFromTime"]."00'), '%Y-%m-%d %H:%i') and date_format(concat(t01_sugup_date, '".$_GET["mToTime"]."00'), '%Y-%m-%d %H:%i'))
													    and t01_del_yn = 'N'
														and t01_status_gbn in (case when t01_sugup_date >= '".date('Ymd',mkTime())."' then '0' else '' end, '9')
													  union all
													 select t01_yoyangsa_id5 as yoyCode
													   from t01iljung
													  where t01_ccode = '".$_GET["mCode"]."'
													    and t01_mkind = '".$_GET["mKind"]."'
													    and t01_sugup_date like '".$_GET["mDate"]."%'
													    and (date_format(concat(t01_sugup_date, t01_sugup_fmtime, '00'), '%Y-%m-%d %H:%i') between date_format(concat(t01_sugup_date, '".$_GET["mFromTime"]."00'), '%Y-%m-%d %H:%i') and date_format(concat(t01_sugup_date, '".$_GET["mToTime"]."00'), '%Y-%m-%d %H:%i')
													     or  date_format(concat(t01_sugup_date, t01_sugup_totime, '00'), '%Y-%m-%d %H:%i') between date_format(concat(t01_sugup_date, '".$_GET["mFromTime"]."00'), '%Y-%m-%d %H:%i') and date_format(concat(t01_sugup_date, '".$_GET["mToTime"]."00'), '%Y-%m-%d %H:%i'))
													    and t01_del_yn = 'N'
														and t01_status_gbn in (case when t01_sugup_date >= '".date('Ymd',mkTime())."' then '0' else '' end, '9')
													 ) as t
											   where yoyCode != '')";

								if ($_POST["dong"] != ''){
									$sql .= " and m02_yname like'%".$_POST["dong"]."%'";
								}

								$caseSql = "";
								$yoyIndex = 1;

								for($i=1; $i<=5; $i++){
									if ($yoy[$i] != ""){
										$caseSql .= " when m02_yjumin = '".$yoy[$i]."' then ".$yoyIndex;
										$yoyIndex++;
									}
								}

								if ($caseSql != ""){
									$sql .= " order by case ".$caseSql." else 6 end, m02_yname";
								}else{
									$sql .= " order by m02_yname";
								}

								$conn->query($sql);
								$row = $conn->fetch();
								$row_count = $conn->row_count();

								for($i=0; $i<$row_count; $i++){
									$row = $conn->select_row($i);

									if ($row['m02_ygupyeo_kind'] == '1' or $row['m02_ygupyeo_kind'] == '2'){
										$timePay = $conn->get_time_pay($_GET["mCode"], $_GET["mKind"], $row['m02_yjumin'], $sugupjaLevel);

										if ($timePay == ''){
											$timePay = $row[4];
										}
									}else{
										$timePay = $row[4];
									}
									?>
										<tr>
											<td style="width:20%; text-align:center; padding:0px;"><input name="check[]" type="checkbox" class="checkbox" value="<?=$row[1];?>//<?=$row[2];?>//<?=$timePay;?>" onClick="_check('check', this);"></td>
											<td style="width:30%; text-align:left; padding:0px;"><?=$myF->issToBirthday($row[1],'.');?></td>
											<td style="width:50%; text-align:left; padding:0px; padding-left:5px;"><a href="#" onClick="_checkedT('<?=$row[1];?>//<?=$row[2];?>//<?=$timePay;?>');"><?=$row[2];?></a></td>
										</tr>
									<?
								}
								break;

							case "sugaFind":
								$sql = "select m01_mcode"
									 . ",      m01_suga_cont"
									 . "  from m01suga"
									 . " where m01_mcode != ''";

								if ($_POST["dong"] != ''){
									$sql .= " and m01_suga_cont like'%".$_POST["dong"]."%'";
								}

								$sql .= " order by m01_scode";

								$conn->query($sql);
								$row = $conn->fetch();
								$row_count = $conn->row_count();

								for($i=0; $i<$row_count; $i++){
									$row = $conn->select_row($i)
									?>
										<tr>
											<td style="width:40%; text-align:center; padding:0px;"><?=$row[0];?></td>
											<td style="width:60%; text-align:left; padding:0px;"><a href="#" onClick="_currnetRow('<?=$row[0];?>','<?=$row[1];?>');"><?=$row[1];?></a></td>
										</tr>
									<?
								}
								$conn->row_free();
								break;
						}
					?>
					</table>
					</div>
				</td>
			</tr>
			<?
				if ($submit){
				?>
					<tr>
						<td style="border-bottom:0px; text-align:right;" colspan="3">
							<a href="#" onClick="<?=$submitSub;?>">확인</a>
						</td>
					</tr>
				<?
				}
			?>
		</table>
		<input name="r_gubun" type="hidden" value="<?=$_REQUEST["r_gubun"];?>">
		</form>
	<?
	}
	include_once("../inc/_footer.php");
?>