<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_page_list.php');
	include_once('../inc/_body_header.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$fromDt	= Date('Y-m-d');
	$toDt	= $fromDt; //$myF->dateAdd('day',-1,$myF->dateAdd('month',1,$fromDt,'Y-m-d'),'Y-m-d');
	$year	= Date('Y');
	$month	= IntVal(Date('m'));
?>
<script type="text/javascript">
	var para = '';
	var fromDt = '', toDt = '', appNo = '';

	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
		});

		lfLoadYYMM('<?=$month;?>');
		lfSearch();
	});

	function lfFindClient(){
		var result = __findClient($('#code').val(),'0');

		if (result){
			var val = __parseVal(result);

			$('#lblCode').text(val['jumin']);
			$('#lblName').text(val['name']);
			$('#lblAppNo').text(val['app_no'].split(' ').join('').substring(0,11));
			$('.tmpStr').show();
		}
	}

	function lfClearAppNo(){
		$('#lblCode').text('');
		$('#lblName').text('');
		$('#lblAppNo').text('');
		$('.tmpStr').hide();
	}

	function lfLongTermSearch(page){
		if (!page){
			if (!$('#txtFromDt').val()){
				alert('조회기간을 입력하여 주십시오.');
				$('#txtFromDt').focus();
				return;
			}

			if (!$('#txtToDt').val()){
				alert('조회기간을 입력하여 주십시오.');
				$('#txtToDt').focus();
				return;
			}

			if ($('#txtFromDt').val().substr(0,7) != $('#txtToDt').val().substr(0,7)){
				alert('같은 년월의 기간으로 조회하여 주십시오.');
				$('#txtToDt').focus();
				return;
			}

			if ($('#txtFromDt').val() > $('#txtToDt').val()){
				alert('조회기간 입력오류입니다. 확인하여 주십시오.');
				$('#txtToDt').focus();
				return;
			}

			para = '';
			page = 1;

			fromDt = '';
			toDt = '';
			appNo = '';
		}


		if (!fromDt && !toDt){
			fromDt = $('#txtFromDt').val();
			toDt = $('#txtToDt').val();
			appNo = $('#lblAppNo').text();

			$('#txtFromDt').attr('disabled',true);
			$('#txtToDt').attr('disabled',true);
			$('#btnFindClient').attr('disabled',true);
		}


		var data = {};

		data['pageIndex']	= page;
		data['serviceKind'] = '';
		data['searchFrDt']	= fromDt;
		data['searchToDt']	= toDt;
		data['searchValue']	= $('#lblAppNo').text();
		data['searchGbn']	= (data['searchValue'] != '' ? '5' : '');
		data['delYn']		= '';


		$.ajax({
			type:'POST'
		,	url:'http://dmd.longtermcare.or.kr/autodmd/nypk/nypk_autoDmdList.do?method=nypkRfidmodify'
		,	data:data
		,	beforeSend:function (){
				if (page == 1){
					$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
				}
			}
		,	success:function(html){
				if ($('.npaging', html).html()){
					var nextPage = $('.npaging b + a', html).text();
					var nextTag = $('.npaging', html).find('a:last').text();
					var tbl = $('table[@background="/autodmd/ny/img/common/table_nemo_bg.gif"]', html);
					var i = 0;

					$('tr', tbl).each(function(){
						if (isNaN($('td',this).eq(0).text())) return;

						para += (para ? '?' : '');

						//인정번호
						para += ('appNo='+$('td',this).eq(4).text());

						//서비스 년월
						para += ('&yymm='+$('#svcYyyymm_'+i,this).val());

						//시작일시
						var tmpVal	= $('td',this).eq(9).text().split(' ');
						para += ('&fromDate='+tmpVal[0].split('.').join(''));
						para += ('&fromTime='+tmpVal[1].split(':').join(''));

						//종료일시
						var tmpVal	= $('td',this).eq(10).text().split(' ');
						para += ('&toDate='+(tmpVal[0] ? tmpVal[0].split('.').join('') : ''));
						para += ('&toTime='+(tmpVal[1] ? tmpVal[1].split(':').join('') : ''));

						//총시간
						para += ('&procTime='+$('td',this).eq(8).text().replace('분',''));

						//수급자명
						var ppNm = $('td',this).eq(3).text().replace('(5등급)','');
						para += ('&name='+ppNm);

						//요양보호사명
						var recuSffNm = $('td',this).eq(5).text();
						para += ('&memNm='+recuSffNm);

						//요양보호사 핸드폰
						para += ('&memHP='+$('td',this).eq(6).text().split('-').join(''));

						//전송구분
						para += ('&sendGbn='+$('#svcTransType_'+i,this).val());

						//오류정정일
						var tmpVal	= $('td',this).eq(12).text().split(' ');
						para += ('&errDate='+(tmpVal[0] ? tmpVal[0].split('.').join('') : ''));
						para += ('&errTime='+(tmpVal[1] ? tmpVal[1].split(':').join('') : ''));

						//사용여부
						para += ('&useYn='+($('#delYn_'+i,this).val() == 'Y' ? 'N' : 'Y'));

						//서비스 상세내역
						para += ('&detail='+$('#svcDetail_'+i,this).html());


						/*
						document.popFrm.svcYyyymm.value 	= svcYyyymm_1;
						document.popFrm.tagId.value 		= tagId_1;
						document.popFrm.readerNo.value 		= readerNo_1;
						document.popFrm.aprovalNo.value 	= aprovalNo_1;
						document.popFrm.ppNm.value 			= 고객명;
						document.popFrm.recuSffNm.value 	= 요양보호사;
						document.popFrm.action = "nypk_autoDmdList.do?method=popAutoDmdDetailSave";
						*/
						$.ajax({
							type:'POST'
						,	async:false
						,	url:'http://dmd.longtermcare.or.kr/autodmd/nypk/nypk_autoDmdList.do?method=popAutoDmdDetailSave'
						,	data:{
								'svcYyyymm'	:$('#svcYyyymm_'+i,this).val()
							,	'tagId'		:$('#tagId_'+i,this).val()
							,	'readerNo'	:$('#readerNo_'+i,this).val()
							,	'aprovalNo'	:$('#aprovalNo_'+i,this).val()
							,	'ppNm'		:''
							,	'recuSffNm'	:''
							}
						,	beforeSend:function (){
							}
						,	success:function(dtlData){
								//신체횔동 RsvcTm01
								//인지활동 RsvcTm02
								//가사,일상생활 RsvcTm03
								//정서지원 RsvcTm04

								//신체기능 RaltrBdy 1:호전, 2:유지, 3:악화
								//식사기능 RaltrEat 1:호전, 2:유지, 3:악화
								//인지기능 RaltrPct 1:호전, 2:유지, 3:악화

								//대변 실수 횟수 RaltrDfctnL
								//소변 실수 횟수 RaltrDfctnS

								//특이 사항 RpcrlComt

								var RsvcTm01 = $('#RsvcTm01',dtlData).val();
								var RsvcTm02 = $('#RsvcTm02',dtlData).val();
								var RsvcTm03 = $('#RsvcTm03',dtlData).val();
								var RsvcTm04 = $('#RsvcTm04',dtlData).val();

								var RaltrBdy = $('input:radio[name="RaltrBdy"]:checked',dtlData).val();
								var RaltrEat = $('input:radio[name="RaltrEat"]:checked',dtlData).val();
								var RaltrPct = $('input:radio[name="RaltrPct"]:checked',dtlData).val();

								var RaltrDfctnL = $('input:text[name="RaltrDfctnL"]',dtlData).val();
								var RaltrDfctnS = $('input:text[name="RaltrDfctnS"]',dtlData).val();

								var RpcrlComt = $('input:text[name="RpcrlComt"]',dtlData).val();

								para += ('&RsvcTm01='+(RsvcTm01 ? RsvcTm01 : ''));
								para += ('&RsvcTm02='+(RsvcTm02 ? RsvcTm02 : ''));
								para += ('&RsvcTm03='+(RsvcTm03 ? RsvcTm03 : ''));
								para += ('&RsvcTm04='+(RsvcTm04 ? RsvcTm04 : ''));

								para += ('&RaltrBdy='+(RaltrBdy ? RaltrBdy : ''));
								para += ('&RaltrEat='+(RaltrEat ? RaltrEat : ''));
								para += ('&RaltrPct='+(RaltrPct ? RaltrPct : ''));

								para += ('&RaltrDfctnL='+(RaltrDfctnL ? RaltrDfctnL : ''));
								para += ('&RaltrDfctnS='+(RaltrDfctnS ? RaltrDfctnS : ''));

								para += ('&RpcrlComt='+(RpcrlComt ? RpcrlComt : ''));
							}
						,	complete: function(){
							}
						,	error:function(request, status, error){
								alert('[ERROR No.03]'
									 +'\nCODE : ' + request.status
									 +'\nSTAT : ' + status
									 +'\nMESSAGE : ' + request.responseText);

								return false;
							}
						}).responseXML;


						//서비스구분
						switch($('td',this).eq(7).text()){
							case '요양':
								para += ('&svcGbn=200');
								break;

							case '목욕':
								para += ('&svcGbn=500');
								break;

							case '간호':
								para += ('&svcGbn=800');
								break;

							default:
								para += ('&svcGbn=');
						}

						i ++;
					});

					//다음페이지
					if (pageIndex == 1 && !nextPage && !nextTag){
						var moveNext = false;
					}else if (!nextTag){
						var moveNext = true;
					}else{
						var moveNext = false;
					}

					if (moveNext){
						lfLongTermSearch(__str2num(page)+1);
					}else{
						lfLG2CV(para);
					}
				}else{
					$('#tempLodingBar').remove();
					alert('건보');
				}
			}
		,	complete: function(){
			}
		,	error:function(request, status, error){
				alert('[ERROR No.03]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);

				return false;
			}
		}).responseXML;
	}

	function lfLG2CV(para){
		$.ajax({
			type:'POST'
		,	url:'./svc_list_save.php'
		,	data:{
				'fromDt':fromDt.split('-').join('')
			,	'toDt'	:toDt.split('-').join('')
			,	'appNo'	:appNo
			,	'para'	:para
			}
		,	beforeSend:function (){
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					lfSearch();
				}else if (result == 9){
					alert('데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}

				$('#txtFromDt').attr('disabled',false);
				$('#txtToDt').attr('disabled',false);
				$('#btnFindClient').attr('disabled',false);
				$('#tempLodingBar').remove();
			}
		,	complete: function(){
			}
		,	error:function(request, status, error){
				alert('[ERROR No.03]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);

				return false;
			}
		}).responseXML;
	}

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./svc_list_search.php'
		,	data:{
				'fromDt':$('#txtFromDt').val()
			,	'toDt'	:$('#txtToDt').val()
			,	'appNo'	:$('#lblAppNo').text()
			}
		,	beforeSend:function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				html = html.split('<!-- CUT_LINE -->');
				$('#tbodySum').html(html[1]);
				$('#tbodyList').html(html[0]);
				$('#tempLodingBar').remove();
			}
		,	complete: function(){
			}
		,	error:function(request, status, error){
				alert('[ERROR No.03]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);

				return false;
			}
		}).responseXML;
	}

	function lfExcel(type,fromDt,toDt,appNo,svcGbn){
		if (!appNo) appNo = '';
		if (!svcGbn) svcGbn = '';

		var parm = new Array();
			parm = {
				'type'	:type
			,	'fromDt':fromDt
			,	'toDt'	:toDt
			,	'appNo'	:appNo
			,	'svcGbn':svcGbn
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

		form.setAttribute('method', 'post');
		form.setAttribute('action', './svc_list_excel.php');

		document.body.appendChild(form);

		form.submit();
	}

	function lfSetYYMM(m){
		var year = $('#lblYYMM').attr('year');

		month = (m < 10 ? '0' : '') + m;

		var date = __addDate('d', -1, __addDate('m', 1, year+'-'+month+'-01'));

		$('#txtFromDt').val(year+'-'+month+'-01');
		$('#txtToDt').val(date);

		var val = $('#btnMonth_'+m).attr('lastDt');

		if (val){
			$('#ID_CELL_LASTDATE').html('※마지막 작성일자 : '+__getDate(val,'.'));
		}else{
			$('#ID_CELL_LASTDATE').html('&nbsp;');
		}
	}

	function lfLoadYYMM(month){
		$.ajax({
			type:'POST'
		,	url:'./svc_list_year.php'
		,	data:{
				'year':$('#lblYYMM').attr('year')
			}
		,	beforeSend:function (){
			}
		,	success:function(data){
				if (!data) return;

				var col = __parseVal(data);

				$('div[id^="btnMonth_"]').removeClass('my_month_r').addClass('my_month_1');

				for(var i=1; i<=12; i++){
					if (col['m'+i] > 0){
						$('#btnMonth_'+i).removeClass('my_month_1').addClass('my_month_r').attr('lastDt',col['d'+i]);
					}
				}

				if (month){
					lfSetYYMM(month);
				}
			}
		,	complete: function(){
			}
		,	error:function(request, status, error){
				alert('[ERROR No.03]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);

				return false;
			}
		}).responseXML;
	}
</script>
<div class="title title_border">서비스내역</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="85px">
		<col width="550px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년도</th>
			<td class="last">
				<div class="left" style="padding-top:2px;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),-1); lfLoadYYMM();" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYYMM" year="<?=$year;?>"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),1); lfLoadYYMM();" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<td class="last"><?=$myF->_btn_month(0,'lfSetYYMM(');?></td>
			<td class="last">
				<span id="ID_CELL_LASTDATE">&nbsp;</span>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="177px">
		<col width="70px">
		<col width="200px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">기간</th>
			<td>
				<input id="txtFromDt" type="text" value="<?=$fromDt;?>" class="date"> ~
				<input id="txtToDt" type="text" value="<?=$toDt;?>" class="date">
			</td>
			<th class="center">인정번호</th>
			<td class="" style="padding-left:2px; padding-top:1px;">
				<span id="btnFindClient" class='btn_pack find' onclick="lfFindClient();"></span>
				<span id='lblName' style='height:23px; line-height:23px; font-weight:bold; margin-left:3px;'></span>
				<span id='lblCode' style='display:none;'></span>
				<span class='tmpStr' style='display:none;'>[</span>
				<span id='lblAppNo' style='height:23px; line-height:23px; color:#0000ff; font-weight:bold;'></span>
				<span class='tmpStr' style='display:none; margin-left:5px;'>]</span>
				<span class='tmpStr' style='display:none; margin-left:5px; font-weight:bold; cursor:pointer;' onclick="lfClearAppNo();">X</span>
			</td>
			<td class="left last">
				<!--span class="btn_pack m"><button onclick="lfLongTermSearch();">공단내역가져오기</button></span-->
				<span class="btn_pack m"><button onclick="lfSearch();">내역조회</button></span>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="80px">
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">대상자</th>
			<th class="head">인정번호</th>
			<th class="head">일정건수</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="tbodySum"></tbody>
	<tbody id="tbodyList"></tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>