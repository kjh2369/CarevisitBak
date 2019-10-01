<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	$code = $_SESSION['userCenterCode'];
	$kind = $conn->center_kind($code);
	$name = $conn->center_name($code, $kind);

	$find_nm = $_POST['find_nm'];
?>
<script language='javascript'>
<!--

function counsel_list(page){
	var f = document.f;

	f.page.value = page;
	f.submit();
}

function counsel_find(){
	var f = document.f;

	f.action = 'mem_counsel.php';
	f.submit();
}

function councel_print(code, ssn){
	var URL = 'mem_counsel_print.php?code='+code+'&ssn='+ssn;
	var popup = window.open(URL,'REPORT','width=700,height=900,scrollbars=yes,resizable=yes,location=no,toolbar=no,menubar=no');
}

function counsel_reg(ssn){
	var f = document.f;

	f.ssn.value = ssn;
	f.action = 'mem_counsel_reg.php';
	f.submit();
}

function counsel_delete(code, ssn){
	if (!confirm('선택하신 초기상담기록지를 삭제하시겠습니까?')) return;

	var URL  = 'mem_counsel_delete.php';
	var para = {'code':code,'ssn':ssn};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:para,
			onSuccess:function (responseHttpObj) {
				if (responseHttpObj.responseText == 'OK'){
					counsel_list(document.getElementById('page').value);
				}
			}
		}
	);
}

window.onload = function(){
	__init_form(document.f);
}

//-->
</script>

<form name="f" method="post">


<div class="title">
	<div>초기상담기록지(직원)</div>
</div>
<table class="my_table my_border">
	<colgroup>
		<col width="80px">
		<col width="130px">
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>기관기호</th>
			<td class="left"><?=$code;?></td>
			<th>기관명</th>
			<td class="left last" colspan="2"><?=$name;?></td>
		</tr>
		<tr>
			<th>성명</th>
			<td class="last"><input name="find_nm" type="text" value="<?=$find_nm;?>" style="width:100%;"></td>
			<td class="last" colspan="3"><span class="btn_pack m icon">
				<span class="refresh"></span><button type="button" onclick="counsel_find();">조회</button></span>
				<span class="btn_pack m"><button type="button" onclick="counsel_reg('');">등록</button></span>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="90px">
		<col width="80px">
		<col width="100px">
		<col width="100px">
		<col width="120px">
		<col width="150px">
		<col width="80px">
		<col>
	</colgroup>
	<thead>
	</thead>
	<tbody>
	<tr>
		<th class="head">No</th>
		<th class="head">상담일</th>
		<th class="head">상담유형</th>
		<th class="head">상담자</th>
		<th class="head">성명</th>
		<th class="head">연락처</th>
		<th class="head">이메일</th>
		<th class="head">채용일자</th>
		<th class="head last">비고</th>
	</tr>
	<tr>
		<td class="pd0 last" colspan="9">
			<div id='listBody' style='overflow-x:hidden; overflow-y:scroll; width:100%; height:500px;'>
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="60px">
						<col width="90px">
						<col width="80px">
						<col width="100px">
						<col width="100px">
						<col width="120px">
						<col width="150px">
						<col width="80px">
						<col >
					</colgroup>
					<tbody>
					<?
					$wsl = " where org_no   = '$code'
							   and del_flag = 'N'";

					if (!empty($find_nm))
						$wsl .= " and mem_nm like '%$find_nm%'";

					$sql = "select mem_ssn
							,      mem_counsel_dt
							,      case mem_counsel_gbn when '1' then '내방'
														when '2' then '방문'
														when '3' then '전화'
														else '-' end as mem_counsel_gbn
							,      mem_talker_nm
							,      mem_nm
							,      mem_phone
							,	   mem_mobile
							,      mem_email
							,      mem_addr
							,     (select m02_yipsail from m02yoyangsa where m02_ccode = org_no and m02_yjumin = mem_ssn and m02_del_yn = 'N' limit 1) as recruit_dt
							  from counsel_mem $wsl
							 order by mem_counsel_dt desc";

					$conn->fetch_type = 'assoc';
					$conn->query($sql);
					$conn->fetch();

					$row_count = $conn->row_count();

					for($i=0; $i<$row_count; $i++){
						$row = $conn->select_row($i);
						//$total_count - ($pageCount + $i)
						
						if($row['mem_mobile'] != ''){
							$tel = $row['mem_mobile'];
						}else {
							$tel = $row['mem_phone'];
						}
						
						
						?>
						<tr>
							<td class="center"	><?=$pageCount + ($i + 1);?></td>
							<td class="center"	><?=str_replace('-', '.', $row['mem_counsel_dt']);?></td>
							<td class="center"	><?=$row['mem_counsel_gbn'];?></td>
							<td class="left"	><div class="nowrap" style="width:95px;" class="left"><?=$row['mem_talker_nm'];?></div></td>
							<td class="left"	><div class="nowrap" style="width:95px;" class="left"><?=$row['mem_nm'];?></div></td>
							<td class="left"	><?=$myF->phoneStyle($tel,'.');?></td>
							<td class="left"	><?=$row['mem_email'];?></td>
							<td class="center"	><?=$myF->dateStyle($row['recruit_dt'],'.');?></td>
							<td class="left last">
								<span class="btn_pack m"><button type="button" onclick="counsel_reg('<?=$ed->en($row['mem_ssn']);?>');">수정</button></span>
								<span class="btn_pack m"><button type="button" onclick="councel_print('<?=$code;?>','<?=$ed->en($row['mem_ssn']);?>');">출력</button></span>
								<?
									if (empty($row['recruit_dt'])){?>
										 <span class="btn_pack m"><button type="button" onclick='counsel_delete("<?=$code;?>","<?=$ed->en($row['mem_ssn']);?>");'>삭제</button></span><?
									}
								?>
							</td>
						</tr><?
					}

					$conn->row_free();
				?>
				</tbody>
				</table>
			</div>
		</td>
	</tr>
	
	</tbody>
</table>

<input name="code" type="hidden" value="<?=$code;?>">
<input name="kind" type="hidden" value="<?=$kind;?>">
<input name="ssn" type="hidden" value="">
<input name="page" type="hidden" value="<?=$page;?>">

</form>

<?
	include_once("../inc/_db_close.php");
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>