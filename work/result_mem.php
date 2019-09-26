<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}

	$code = $_SESSION['userCenterCode'];

	$laYear = $myF->year();
	$lsYear = $_POST['txtYear'] ? $_POST['txtYear'] : date('Y');
	$lsSvc  = $_POST['txtSvc'] ? $_POST['txtSvc'] : 'all';
	$lsDept = $_POST['txtDept'] ? $_POST['txtDept'] : 'all';
	$lsMemNm= $_POST['txtMemNm'];

	//부서
	$sql = 'select dept_cd as code
			,      dept_nm as name
			  from dept
			 where org_no   = \''.$code.'\'
			   and del_flag = \'N\'
			 order by order_seq';
	$laDept = $conn->_fetch_array($sql,'code');?>

	<div class="title title_border">직원 실적내역</div>
	<form id="f" name="f" method="post">

	<div id="loMst">
		<table class="my_table" style="width:100%">
			<colgroup>
				<col width="40px">
				<col width="40px">
				<col width="50px">
				<col width="40px">
				<col width="40px">
				<col width="40px">
				<col width="50px">
				<col width="70px">
				<col>
			</colgroup>
			<tbody>
				<tr>
					<th class="center">년도</th>
					<td class="center">
						<select id="txtYear" name="txtYear" style="width:auto;"><?
						for($i=$laYear[0]; $i<=$laYear[1]; $i++){?>
							<option value="<?=$i;?>" <?if($lsYear == $i){?>selected<?}?>><?=$i;?></option><?
						}?>
						</select>
					</td>
					<th class="center">서비스</th>
					<td class="center"><?
						$laSvcList = $conn->kind_list($code, $gHostSvc['voucher']);?>
						<select id="txtSvc" name="txtSvc" style="width:auto;">
							<option value="all">전체</option><?
							foreach($laSvcList as $svc){?>
								<option value="<?=$svc['code'];?>" <?if($lsSvc == $svc['code']){?>selected<?}?>><?=$svc['name'];?></option><?
							}?>
						</select>
					</td>
					<th class="center">부서</th>
					<td class="center">
						<select id="txtDept" name="txtDept" style="width:auto;">
							<option value="all">전체</option><?
							if (is_array($laDept)){
								foreach($laDept as $dept){?>
									<option value="<?=$dept['code'];?>" <?if($lsDept == $dept['code']){?>selected<?}?>><?=$dept['name'];?></option><?
								}
							}?>
						</select>
					</td>
					<th class="center">직원명</th>
					<td class="center">
						<input id="txtMemNm" name="txtMemNm" type="text" value="<?=$lsMemNm;?>" style="width:100%;" onkeydown="if(window.keyCode == 13){search();}">
					</td>
					<td class="left last">
						<span class="btn_pack m"><button type="button" onclick="search();">조회</button></span>
					</td>
				</tr>
			</tbody>
		</table>

		<table class="my_table" style="width:100%; margin-bottom:20px;">
			<colgroup>
				<col width="40px">
				<col width="70px">
				<col width="100px">
				<col width="100px">
				<col>
			</colgroup>
			<thead>
				<tr>
					<th class="head">No</th>
					<th class="head">직원명</th>
					<th class="head">부서</th>
					<th class="head">서비스</th>
					<th class="head last">월별</th>
				</tr>
			</thead>
			<tbody id="loList"></tbody>
		</table>
	</div>
	<div id="loadingBody" style="position:absolute;"></div>
	<input type="hidden" name="mode" value="">
	<input type="hidden" name="name" value="">
	<input type="hidden" name="jumin" value="">
	<input type="hidden" name="svcCd" value="">
	<input type="hidden" name="year" value="">
	<input type="hidden" name="month" value="">
	<input type="hidden" name="order" value="">
	</form>
	<script type="text/javascript">
		$(document).ready(function(){
			__init_form(document.f);
			search();
		});

		function search(){
			$.ajax({
				type: 'POST',
				url : './result_mem_list.php',
				data: {
					year   : $('#txtYear').val()
				,	svcCd  : $('#txtSvc').val()
				,	deptCd : $('#txtDept').val()
				,	memNm  : $('#txtMemNm').val()
				},
				beforeSend: function (){
					$('#loadingBody').before('<div id=\'tempLodingBar\' style=\'position:absolute; top:270px; left:170; text-align:center;\'><div style=\'width:250px; height:100px; padding-top:30px; border:2px solid #cccccc; background-color:#ffffff;\'>'+__get_loading()+'</div></div></center></div>');
				},
				success: function (result){
					$('#tempLodingBar').remove();
					$('#loList').html(result);
					//$('#loadingBody').html(result);
				},
				error: function (){
				}
			}).responseXML;
		}
	</script><?

	unset($laDept);

	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>