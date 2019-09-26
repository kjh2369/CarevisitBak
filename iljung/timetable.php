<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$orgNo	= $_SESSION['userCenterCode'];
	$orgNm	= $conn->_storeName($orgNo);
	$orgGiho= $_SESSION['userCenterGiho'];
	$svcList= $conn->svcKindSort($orgNo, $gHostSvc['voucher']);
	$fromDt	= Date('Y-m-01');
	$lastday= $myF->lastDay(Date('Y'), Date('m'));
	$toDt	= Date('Y-m-').$lastday;
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:checkbox[name="chkSvc"]').unbind('click').bind('click',function(){
			var key = $(this).attr('id');
			var chk = $(this).attr('checked');

			$('input:checkbox[id^="'+key+'"]').attr('checked',chk);
		});

		$('#chkSvc').attr('checked',true).click().attr('checked',true);

		//setTimeout('lfSearch()',200);
		__init_form(document.f);
	});

	function lfSearch(page){
		if (!page) page = 1;

		$.ajax({
			type:'POST'
		,	url	:'./timetable_list.php'
		,	data:{
				'page':page
			,	'clientName':$('#txtClientName').val()
			,	'memberName':$('#txtMemberName').val()
			,	'fromDt':$('#txtFrom').val()
			,	'toDt':$('#txtTo').val()
			,	'chkSvc':lfGetSvc()
			,	'orderBy':$('input:radio[name="optOrder"]:checked').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function (html){
				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();
			}
		,	error:function (request, status, error){
				alert('[ERROR]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfGetSvc(){
		var chkSvc = '';

		$('input:checkbox[name="chkSvc"]').each(function(){
			if ($(this).attr('checked') && $(this).val() != 'on'){
				var key  = $(this).val().split('H_').join('').split('V_').join('').split('C_').join('').split('O_').join('');
					key += String.fromCharCode(1);

				chkSvc += key;
			}
		});

		return chkSvc;
	}

	function lfExcel(){
		var parm = new Array();
			parm = {
				'page'		:'ALL'
			,	'clientName':$('#txtClientName').val()
			,	'memberName':$('#txtMemberName').val()
			,	'fromDt'	:$('#txtFrom').val()
			,	'toDt'		:$('#txtTo').val()
			,	'chkSvc'	:lfGetSvc()
			,	'orderBy'	:$('input:radio[name="optOrder"]:checked').val()
			};

		var form = document.createElement('form');
		var objs;
		for(var key in parm){
			objs = document.createElement('input');
			objs.setAttribute('type', 'hidden');
			objs.setAttribute('name', key);
			objs.setAttribute('value', parm[key]);

			form.appendChild(objs);
		}

		//form.setAttribute('target', 'PLANREG');
		form.setAttribute('method', 'post');
		form.setAttribute('action', './timetable_excel.php');

		document.body.appendChild(form);

		form.submit();
	}
</script>
<div class="title title_border">업무일정표</div>
<form name="f" method="post">
<table class="my_table" style="width:100%">
	<colgroup>
		<col width="65px">
		<col width="150px">
		<col width="45px">
		<col>
		<col width="100px">
	</colgroup>
	<tbody>
		<tr>
			<th>기관기호</th>
			<td class="left"><?=$orgGiho;?></td>
			<th>기관명</th>
			<td class="left"><?=$orgNm;?></td>
			<td class="left bottom last" rowspan="4">
				<span class="btn_pack m"><button type="button" onclick="lfSearch();">조회</button></span>
				<span class="btn_pack m"><button type="button" onclick="lfExcel();">엑셀</button></span>
			</td>
		</tr>
		<tr>
			<th>고객명</th>
			<td><input id="txtClientName" type="text" value="" style="width:145px;"></td>
			<th>직원명</th>
			<td><input id="txtMemberName" type="text" value="" style="width:145px;"></td>
		</tr>
		<tr>
			<th class="">서비스일자</th>
			<td class="" colspan="3">
				<input id="txtFrom" type="text" value="<?=$fromDt;?>" class="date"> ~
				<input id="txtTo" type="text" value="<?=$toDt;?>" class="date">
			</td>
		</tr>
		<tr>
			<th>서비스선택</th>
			<td colspan="3">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="80px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th>전체</th>
							<td class="last" colspan="5">
								<input id="chkSvc" name="chkSvc" type="checkbox" class="checkbox"><label for="chkSvc">전체</label>
							</td>
						</tr><?
						foreach($svcList as $svcIdx => $svcKind){
							if ($svcIdx == 'H'){
								$lsSvcName = '재가요양';
							}else if ($svcIdx == 'V'){
								$lsSvcName = '바우처';
							}else if ($svcIdx == 'C'){
								if ($sr == 'S')
									$lsSvcName = '재가지원';
								else
									$lsSvcName = '자원연계';
							}else{
								$lsSvcName = '기타유료';
							}

							$key = 'chkSvc_'.$svcIdx;?>
							<tr>
								<th class="bottom" style="border-top:1px solid #CCCCCC;"><input id="<?=$key;?>" name="chkSvc" type="checkbox" class="checkbox" style="margin-left:0;"><label for="<?=$key;?>"><?=$lsSvcName;?></label></th>
								<td class="bottom last" style="border-top:1px solid #CCCCCC;" colspan="5"><?
								foreach($svcKind as $subIdx => $subKind){
									$key1 = $key.'_'.$subKind['code'];

									if (Is_Array($subKind['sub'])){
										$lbFirst = true;

										foreach($subKind['sub'] as $svcSubKindCd => $svcSubKindNm){
											$key2 = $key1.'_'.$svcSubKindCd;

											if ($svcIdx == 'V' && $lbFirst){?>
												<div style="float:left; width:auto; height:100%; margin-left:5px; padding-right:5px; background-color:#f7faff; border-left:1px solid #a6c0f3; border-right:1px solid #a6c0f3;">
													<input id="<?=$key1;?>" name="chkSvc" type="checkbox" class="checkbox"><label for="<?=$key1;?>"><?=$subKind['name'];?></label>
												</div><?
											}?>
											<div style="float:left; width:auto;"><input id="<?=$key2;?>" name="chkSvc" type="checkbox" value="<?=$svcIdx.'_'.$subKind['code'].'_'.$svcSubKindCd;?>" class="checkbox"><label for="<?=$key2;?>"><?=$svcSubKindNm;?></label></div><?

											$lbFirst = false;
										}
									}else{?>
										<div style="float:left; width:auto;"><input id="<?=$key1;?>" name="chkSvc" type="checkbox" value="<?=$svcIdx.'_'.$subKind['code'];?>" class="checkbox"><label for="<?=$key1;?>"><?=$subKind['name'];?></label></div><?
									}
								}?>
								</td>
							</tr><?
						}?>
						<tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<th class="bottom">정렬조건</th>
			<td class="bottom" colspan="3">
				<label><input id="optOrderDesc" name="optOrder" type="radio" value="DESC" class="radio">최근일자순별</label>
				<label><input id="optOrderAsc" name="optOrder" type="radio" value="ASC" class="radio" checked>작성순서순별</label>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%; border-top:1px solid #0e69b0;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="80px">
		<col width="150px">
		<col width="90px">
		<col width="250px">
		<col width="50px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">일자</th>
			<th class="head">시간</th>
			<th class="head">서비스</th>
			<th class="head">고객명</th>
			<th class="head">직원명</th>
			<th class="head">상태</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="tbodyList">
		<tr>
			<td class="center last" colspan="20">::검색된 데이타가 없습니다.::</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last">&nbsp;</td>
		</tr>
	</tfoot>
</table>
</form>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>