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

	$mode = $_GET['mode'];

	if ($mode == '101'){
		$lsTitle = '일정표출력(수급자)';
	}else if ($mode == '102'){
		$lsTitle = '근무현황표(직원)';
	}else if ($mode == '103'){
		$lsTitle = '일정표출력(욕구상담)';
	}else if ($mode == '104'){
		$lsTitle = '일정표출력(서비스별)';
	}else if ($mode == '105'){
		$lsTitle = '일정표출력(대상자별)';
		$mode = '101';
		$type = '105';
	}else if ($mode == '106'){
		$lsTitle = '일정표출력(직원별)';
		$mode = '105';
		$type = '105';
	}else{
		include('../inc/_http_home.php');
		exit;
	}

	$code = $_SESSION['userCenterCode'];
	$year = Date('Y');
	$date = Date('Y-m-d');
	$sr = $_GET['sr'];
?>
<script src="../js/work.js" type="text/javascript"></script>
<script type="text/javascript">
	$(document).ready(function(){
		__init_form(document.f);

		$('input:checkbox[name="chkSvc"]').unbind('click').bind('click',function(){
			var key = $(this).attr('id');
			var chk = $(this).attr('checked');

			$('input:checkbox[id^="'+key+'"]').attr('checked',chk);
		});

		$('#chkSvc').attr('checked',true).click().attr('checked',true);

		setTimeout('lfSearch()',200);
	});

	function lfMoveYear(aiPos){
		var year = __str2num($('#lblYear').text()) + aiPos;

		$('#lblYear').text(year);
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

		$('#txtChkSvc').val(chkSvc);

		return chkSvc;
	}

	function lfSearch(){
		var chkSvc = lfGetSvc();
		var optOrder = $('input:radio[name="optOrder"]:checked').val();

		if (!chkSvc){
			alert('서비스를 하나 이상선택하여 주십시오.');
			return;
		}

		$.ajax({
			type:'POST'
		,	url	:'./iljung_print_search.php'
		,	data:{
				'mode'		:'<?=$mode;?>'
			,	'year'		:$('#lblYear').text()
			,	'showGbn'	:$('#cboShowGbn option:selected').val()
			,	'name'		:$('#txtName').val()
			,	'chkSvc'	:chkSvc
			,	'optOrder'	:optOrder
			,	'teamCd'    :$('#strTeamCd').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function (data){
				if ('<?=$mode;?>' == '101'){
					lfSetClient(data);
				}else if ('<?=$mode;?>' == '102'){
					lfSetMember(data);
				}else if ('<?=$mode;?>' == '103'){
					type = 'c';
				}else if ('<?=$mode;?>' == '104'){
					type = 'service';
				}

				$('#txtShowGbn').val($('#cboShowGbn option:selected').val());

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

	function lfSetClient(data){
		var list = data.split(String.fromCharCode(1));
		var showGbn = $('#cboShowGbn option:selected').val();
		var monCnt = new Array();

		for(var i=0; i<=12; i++){
			monCnt[i] = 0;
		}

		$('.removeTr').remove();

		for(var i=0; i<list.length; i++){
			if (list[i]){
				var val  = list[i].split(String.fromCharCode(2));
				var no   = i + 1;
				var jumin = val[0];
				var svcCd = val[14], svcNm = '';
				var lvlCd = val[15], lvlNm = '';
				var kindCd = val[16], kindNm = '';

				//서비스명
				if (svcCd == '0'){
					svcNm = '재가요양';
				}else if (svcCd == '1'){
					svcNm = '가사간병';
				}else if (svcCd == '2'){
					svcNm = '노인돌봄';
				}else if (svcCd == '3'){
					svcNm = '산모신생아';
				}else if (svcCd == '4'){
					svcNm = '장애인활동보조';
				}else if (svcCd == '6'){
					svcNm = '재가지원';
				}else if (svcCd == 'A'){
					svcNm = '산모유료';
				}else if (svcCd == 'B'){
					svcNm = '병원간병';
				}else if (svcCd == 'C'){
					svcNm = '기타비급여';
				}else{
					svcNm = '';
				}

				//등급
				if (svcCd == '0' || svcCd == '4'){
					if (lvlCd >= '1' && lvlCd <= '4'){
						lvlNm = lvlCd+'등급';
					}else{
						lvlNm = '일반';
					}
				}

				//구분
				if (svcCd == '0'){
					if (kindCd == '3'){
						kindNm = '기초';
					}else if (kindCd == '2'){
						kindNm = '의료';
					}else if (kindCd == '4'){
						kindNm = '경감';
					}else if (kindCd == '1'){
						kindNm = '일반';
					}
				}

				var caln = '', cls = '', clr;

				for(var j=1; j<=12; j++){
					if (val[j] > 0){
						cls = 'my_month_y';
						clr = '#000000';

						monCnt[j] ++;
					}else{
						cls = 'my_month_1';
						clr = '#cccccc';
					}

					caln += '<div class="my_month '+cls+'" style="float:left; margin-top:2px; margin-bottom:2px; margin-left:3px; color:'+clr+'; cursor:default;" onclick="';

					if (val[j] > 0){
						caln += 'lfShowCaln(\''+jumin+'\',\''+svcCd+'\',\''+(j < 10 ? '0' : '')+j+'\',\''+showGbn+'\');';
					}

					caln += ' return false;">'+j+'월</div>';
				}

				var html = '<tr class="removeTr">'
						 + '<td class="center">'+no+'</td>'
						 + '<td class="center"><div class="left nowrap" style="width:70px;">'+val[13]+'</div></td>'
						 + '<td class="center"><div class="left nowrap" style="width:100px;">'+svcNm+'</div></td>'
						 + '<td class="center">'+lvlNm+'</td>'
						 + '<td class="center">'+kindNm+'</td>'
						 + '<td class="center"><div class="nowrap left" style="width:70px;">'+val[17]+'</div></td>'
						 + '<td class="center last">'+caln+'</td>'
						 + '</tr>';

				if ($('tr',$('#listBody')).length > 0){
					$('#listBody tr:last-child').after(html);
				}else{
					$('#listBody').html(html);
				}
			}
		}

		for(var i=1; i<=12; i++){
			if (monCnt[i] > 0){
				$('#btnAllM'+i).removeClass('my_month_2').addClass('my_month_r').css('color','#000000');
			}else{
				$('#btnAllM'+i).removeClass('my_month_r').addClass('my_month_2').css('color','#cccccc');
			}
		}
	}

	function lfSetMember(data){
		var list = data.split(String.fromCharCode(1));
		var showGbn = $('#cboShowGbn option:selected').val();
		var monCnt = new Array();

		for(var i=0; i<=12; i++){
			monCnt[i] = 0;
		}

		$('.removeTr').remove();

		for(var i=0; i<list.length; i++){
			if (list[i]){
				var val  = list[i].split(String.fromCharCode(2));
				var no   = i + 1;
				var jumin = val[0];
				var caln = '', cls = '', clr;

				for(var j=1; j<=12; j++){
					if (val[j] > 0){
						cls = 'my_month_y';
						clr = '#000000';

						monCnt[j] ++;
					}else{
						cls = 'my_month_1';
						clr = '#cccccc';
					}

					caln += '<div class="my_month '+cls+'" style="float:left; margin-top:2px; margin-bottom:2px; margin-left:3px; color:'+clr+'; cursor:default;" onclick="';

					if (val[j] > 0){
						caln += 'lfShowCaln(\''+jumin+'\',\'\',\''+(j < 10 ? '0' : '')+j+'\',\''+showGbn+'\');';
					}

					caln += ' return false;">'+j+'월</div>';
				}

				var html = '<tr class="removeTr">'
						 + '<td class="center">'+no+'</td>'
						 + '<td class="center"><div class="left nowrap" style="width:70px;">'+val[13]+'</div></td>'
						 + '<td class="center"><div class="left nowrap" style="width:100px;">'+val[14]+'</div></td>'
						 + '<td class="center last">'+caln+'</td>'
						 + '</tr>';

				if ($('tr',$('#listBody')).length > 0){
					$('#listBody tr:last-child').after(html);
				}else{
					$('#listBody').html(html);
				}
			}
		}

		for(var i=1; i<=12; i++){
			if (monCnt[i] > 0){
				$('#btnAllM'+i).removeClass('my_month_2').addClass('my_month_r').css('color','#000000');
			}else{
				$('#btnAllM'+i).removeClass('my_month_r').addClass('my_month_2').css('color','#cccccc');
			}
		}
	}

	function lfShowCaln(asJumin,asSvcCd,asMon,asShowGbn){
		var year = $('#lblYear').text();
		var paperDir = $('#cboPageDir option:selected').val();
		var printDT = $('#printDT').val();
		var name = $('#txtName').val();
		var teamCd = $('#strTeamCd').val();
		var cboSvcYn = $('#cboSvcYn option:selected').val();
		var optOrder = $('input:radio[name="optOrder"]:checked').val();
		var prtType = $('#cboPrtType').val();
		var chkSvc = '';

		if (asSvcCd){
			if (asSvcCd == '0' || asSvcCd == '4'){
				var tmpSvc = $('#txtChkSvc').val().split(String.fromCharCode(1));

				for(var i=0; i<tmpSvc.length; i++){
					if (tmpSvc[i].substring(0,2) == asSvcCd+'_'){
						chkSvc += tmpSvc[i]+String.fromCharCode(1);
					}
				}

				if (!chkSvc){
					alert('서비스를 선택하여 주십시오.');
					return false;
				}
			}else if (asSvcCd == '6'){
				chkSvc = asSvcCd;
			}else{
				chkSvc = asSvcCd;
			}
		}else{
			chkSvc = $('#txtChkSvc').val();
		}

		if (!asShowGbn){
			 asShowGbn = $('#txtShowGbn').val();
		}

		var para = 'root=iljung'
				 + '&dir='+paperDir
				 + '&fileName=iljung_print'
				 + '&fileType='+prtType
				 + '&target=show.php'
				 + '&showForm=ILJUNG_CALN'
				 + '&code=<?=$code;?>'
				 + '&year='+year
				 + '&month='+asMon
				 + '&jumin='+asJumin
				 + '&showGbn='+asShowGbn
				 + '&mode=<?=$mode;?>'
				 + '&name='+name
				 + '&teamCd='+teamCd
				 + '&chkSvc='+chkSvc
				 + '&cboSvcYn='+cboSvcYn
				 + '&printDT='+printDT
				 + '&useType='+$('#cboUseType option:selected').val()
				 + '&calnYn='+$('#cboCalnYn option:selected').val()
				 + '&dtlYn='+$('#cboDtlYn option:selected').val()
				 + '&order='+optOrder
				 + '&bipayYn='+$('#cboBipayYn option:selected').val()
				 + '&sr=<?=$sr;?>'
				 + '&param=';

		if (prtType == 'excel'){
			__ExcelMaker(para);
		}else{
			__printPDF(para);
		}
	}


	/*********************************************************

		팀장명 조회

	*********************************************************/
	function findTeam(){
		
		var result = __findTeam('<?=$code;?>');
		
		if (!result) return;
		
		$('#strTeamNm').val(result['name']);
		$('#strTeamCd').val(result['jumin']);
		$('#param').attr('value', 'jumin='+result['jumin']);
		
		lfSearch();
	}

</script>

<div class="title title_border"><?=$lsTitle;?></div>

<form id="f" name="f">

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="90px">
		<col width="85px">
		<col width="40px">
		<col width="100px">
		<col width="40px">
		<col width="100px">
		<col width="40px">
		<col>
		<col width="100px">
	</colgroup>
	<tbody>
		<tr>
			<th>년도</th>
			<td>
				<div class="left" style="padding-top:2px;">
				<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="lfMoveYear(-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
				<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYear"><?=$year;?></div>
				<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="lfMoveYear(1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<th>구분</th>
			<td>
				<select id="cboShowGbn" name="cbo" style="width:auto;">
					<option value="all">전체표시</option>
					<option value="family">가족케어만표시</option>
					<option value="conf">실적만표시</option>
				</select>
			</td>
			<th>성명</th>
			<td>
				<input id="txtName" name="txt" type="text">
			</td>
			<th class='center'>팀장명</th>
			<td class='left'><div style='float:left;  width:auto; height:100%; padding-top:1px;'><span class='btn_pack find' onclick='findTeam();'></span></div><div style='width:auto; height:100%; padding-top:2px;'><!--span id='strTeam' name="strTeam" class='bold'><?=$strTeam;?></span--><input id="strTeamNm" name="strTeamNm" type="text" style="width:75px; padding:0; background-color:#eeeeee;" value="<?=$strTeam;?>" /> <input id="strTeamCd" name="strTeamCd" type="hidden"  /></div>
			</td>
			<td class="center last" rowspan="8">
				<span class="btn_pack m"><button type="button" onclick="lfSearch();">조회</button></span>
			</td>
		</tr>
		<tr>
			<th>전체</th>
			<td colspan="7">
				<input id="chkSvc" name="chkSvc" type="checkbox" class="checkbox"><label for="chkSvc">전체</label>
			</td>
		</tr><?
		if ($type == '105'){
			$laKind['C'][0]['code'] = $sr;

			if ($sr == 'S'){
				$laKind['C'][0]['name'] = '재가지원';
			}else{
				$laKind['C'][0]['name'] = '자원연계';
			}
		}else{
			$laKind = $conn->svcKindSort($code, $gHostSvc['voucher']);
		}

		if ($gDayAndNight){
			$laKind['H'][0]['sub']['900'] = '주야간보호';
		}

		foreach($laKind as $svcIdx => $svcKind){
			if ($svcIdx == 'H'){
				if ($type == 105) continue;
				$lsSvcName = '재가요양';
			}else if ($svcIdx == 'V'){
				if ($type == 105) continue;
				$lsSvcName = '바우처';
			}else if ($svcIdx == 'C'){
				if ($type != 105) continue;
				if ($sr == 'S')
					$lsSvcName = '재가지원';
				else
					$lsSvcName = '자원연계';
			}else{
				if ($type == 105) continue;
				$lsSvcName = '기타유료';
			}

			$key = 'chkSvc_'.$svcIdx;?>
			<tr>
				<th><input id="<?=$key;?>" name="chkSvc" type="checkbox" class="checkbox" style="margin-left:0;"><label for="<?=$key;?>"><?=$lsSvcName;?></label></th>
				<td colspan="7"><?
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
			<th>출력일자</th>
			<td colspan="7"><input id="printDT" name="txt" type="text" value="<?=$date;?>" class="date"></td>
		</tr>
	</tbody>
	<tbody>
		<tr>
			<th>출력구분</th>
			<td class="last" colspan="8">
				<select id="cboPrtType" name="cbo" style="width:auto; margin-right:0;">
					<option value="pdf">PDF</option>
					<option value="html">HTML</option>
					<option value="excel">EXCEL</option>
				</select>
				<select id="cboUseType" name="cbo" style="width:auto; margin:0;">
					<option value="Y">서비스금액표시</option>
					<option value="N" <?=($mode == '102' ? 'selected' : '');?>>서비스금액미표시</option>
				</select>
				<select id="cboCalnYn" name="cbo" style="width:auto; margin:0;">
					<option value="Y">달력표시</option>
					<option value="N">달력미표시</option>
				</select>
				<select id="cboPageDir" name="cbo" style="width:auto; margin:0;">
					<option value="p">세로</option>
					<option value="l">가로</option>
				</select>
				<select id="cboDtlYn" name="cbo" style="width:auto; margin:0;">
					<option value="N">기본</option>
					<option value="Y">상세</option>
					<option value="A">상세(금액출력)</option>
				</select><?
				if ($mode == '102'){?>
					<select id="cboSvcYn" name="cbo" style="width:auto; margin:0;">
						<option value="A">전체</option>
						<option value="S" selected>서비스별</option>
					</select><?
				}?>
				<select id="cboBipayYn" name="cbo" style="width:auto; margin:0;">
					<option value="A">전체</option>
					<option value="Y">급여</option>
					<option value="N">비급여</option>
				</select>
			</td>
		</tr><?
		if ($mode == '101'){?>
			<tr>
				<th>정렬</th>
				<td class="last" colspan="8">
					<label><input id="optOrder1" name="optOrder" type="radio" class="radio" value="1" checked>고객명순</label>
					<label><input id="optOrder2" name="optOrder" type="radio" class="radio" value="2">주담당순</label>
				</td>
			</tr><?
		}?>
	</tbody>
</table>
<?
	$btnMon = '<div id="btnAllM1" class="my_month my_month_2" style="float:left; margin-left:3px; padding-top:2px; color:#cccccc; cursor:default;" onclick="lfShowCaln(\'\',\'\',\'01\'); return false;">1월</div>
			   <div id="btnAllM2" class="my_month my_month_2" style="float:left; margin-left:3px; padding-top:2px; color:#cccccc; cursor:default;" onclick="lfShowCaln(\'\',\'\',\'02\'); return false;">2월</div>
			   <div id="btnAllM3" class="my_month my_month_2" style="float:left; margin-left:3px; padding-top:2px; color:#cccccc; cursor:default;" onclick="lfShowCaln(\'\',\'\',\'03\'); return false;">3월</div>
			   <div id="btnAllM4" class="my_month my_month_2" style="float:left; margin-left:3px; padding-top:2px; color:#cccccc; cursor:default;" onclick="lfShowCaln(\'\',\'\',\'04\'); return false;">4월</div>
			   <div id="btnAllM5" class="my_month my_month_2" style="float:left; margin-left:3px; padding-top:2px; color:#cccccc; cursor:default;" onclick="lfShowCaln(\'\',\'\',\'05\'); return false;">5월</div>
			   <div id="btnAllM6" class="my_month my_month_2" style="float:left; margin-left:3px; padding-top:2px; color:#cccccc; cursor:default;" onclick="lfShowCaln(\'\',\'\',\'06\'); return false;">6월</div>
			   <div id="btnAllM7" class="my_month my_month_2" style="float:left; margin-left:3px; padding-top:2px; color:#cccccc; cursor:default;" onclick="lfShowCaln(\'\',\'\',\'07\'); return false;">7월</div>
			   <div id="btnAllM8" class="my_month my_month_2" style="float:left; margin-left:3px; padding-top:2px; color:#cccccc; cursor:default;" onclick="lfShowCaln(\'\',\'\',\'08\'); return false;">8월</div>
			   <div id="btnAllM9" class="my_month my_month_2" style="float:left; margin-left:3px; padding-top:2px; color:#cccccc; cursor:default;" onclick="lfShowCaln(\'\',\'\',\'09\'); return false;">9월</div>
			   <div id="btnAllM10" class="my_month my_month_2" style="float:left; margin-left:3px; padding-top:2px; color:#cccccc; cursor:default;" onclick="lfShowCaln(\'\',\'\',\'10\'); return false;">10월</div>
			   <div id="btnAllM11" class="my_month my_month_2" style="float:left; margin-left:3px; padding-top:2px; color:#cccccc; cursor:default;" onclick="lfShowCaln(\'\',\'\',\'11\'); return false;">11월</div>
			   <div id="btnAllM12" class="my_month my_month_2" style="float:left; margin-left:3px; padding-top:2px; color:#cccccc; cursor:default;" onclick="lfShowCaln(\'\',\'\',\'12\'); return false;">12월</div>';

	if ($mode == '101'){?>
		<table class="my_table" style="width:100%;">
			<colgroup>
				<col width="40px">
				<col width="70px">
				<col width="100px">
				<col width="50px">
				<col width="50px">
				<col width="70px">
				<col>
			</colgroup>
			<thead>
				<tr>
					<th class="head">No</th>
					<th class="head">성명</th>
					<th class="head">서비스</th>
					<th class="head">등급</th>
					<th class="head">구분</th>
					<th class="head">주담당</th>
					<th class="head last"><?=$btnMon;?></th>
				</tr>
			</thead>
			<tbody id="listBody"></tbody>
			<tfoot>
				<tr>
					<td class="bottom last" colspan="20"></td>
				</tr>
			</tfoot>
		</table><?
	}else if ($mode == '102' || $mode == '105'){?>
		<table class="my_table" style="width:100%;">
			<colgroup>
				<col width="40px">
				<col width="70px">
				<col width="100px">
				<col>
			</colgroup>
			<thead>
				<tr>
					<th class="head">No</th>
					<th class="head">성명</th>
					<th class="head">부서</th>
					<th class="head last"><?=Str_Replace('_TYPE_','y',$btnMon);?></th>
				</tr>
			</thead>
			<tbody id="listBody"></tbody>
			<tfoot>
				<tr>
					<td class="bottom last" colspan="20"></td>
				</tr>
			</tfoot>
		</table><?
	}else if ($mode == '103'){
	}else if ($mode == '104'){
	}
?>
<input id="txtChkSvc" name="hid" type="hidden">
<input id="txtShowGbn" name="hid" type="hidden">
</form>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>