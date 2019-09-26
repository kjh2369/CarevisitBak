<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$title = lfGetSPName($sr);

	if (!$title) exit;

	$year	= Date('Y');
	$month	= IntVal(Date('m'));
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./care_client_find_search.php'
		,	data :{
				'SR':'<?=$sr;?>'
			,	'name':$('#txtName').val()
			,	'fromDt':$('#txtFromDt').val()
			,	'toDt':$('#txtToDt').val()
			,	'sugaCd':$('#ID_SUGA').attr('code')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#tbodyClientList').html(html);
				$('#tempLodingBar').remove();

				$('tr',$('#tbodyClientList')).attr('selYn','N').css('cursor','default').unbind('mouseover').bind('mouseover',function(){
					if ($(this).attr('selYn') == 'Y') return;
					$(this).css('background-color','#EAEAEA');
				}).unbind('mouseout').bind('mouseout',function(){
					if ($(this).attr('selYn') == 'Y') return;
					$(this).css('background-color','#FFFFFF');
				}).unbind('click').bind('click',function(){
					/*
					if('<?=$debug;?>' != '1'){
						$('tr',$('#tbodyClientList')).attr('selYn','N').css('background-color','#FFFFFF');
						$(this).attr('selYn','Y').css('background-color','#FAF4C0');

						lfClientInfo(this);
					}
					*/		
				});

				
				$('#tbodyClientList td').unbind('click').bind('click',function(){
						var obj = $(this).parent();
						var index = $('td', obj).index(this);
						
						
						if(index == 0){
							//$(':checkbox[name="chkIn"]', obj).attr('checked') 

						}else {
							$('tr',$('#tbodyClientList')).attr('selYn','N').css('background-color','#FFFFFF');
							$(obj).attr('selYn','Y').css('background-color','#FAF4C0');

							lfClientInfo(obj);
						}
					}
				);
			
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfClientInfo(obj){
		var jumin = $(obj).attr('jumin');
		var no = $(obj).attr('no');
		var obj = $('td',obj);
		var name = $(obj).eq(1).text();
		var gender = $(obj).eq(3).html();
		var birth = $(obj).eq(2).text();
		var addr = $(obj).eq(5).text();
		var tel = $(obj).eq(6).text();

		if (tel.substr(0,1) == '/'){
			tel = tel.substr(1);
		}
		
		//if('<?=$debug?>') alert($('#chkIn'+no).is(':checked'));
		
		/*
		if($('#chkIn'+no).is(':checked') == true){
			$('#chkIn'+no).attr('checked',false);
		}else {
			$('#chkIn'+no).attr('checked',true);
		}
		*/
		
		$('input:checkbox[name="chkIn"]').each(function(){
			$(this).attr('checked',false);
		});
		
		$('#chkIn'+no).attr('checked',true);
		

		$('#lblNameGender').html(name+' / '+gender);
		$('#lblBirthday').text(birth);
		$('#lblAddr').text(addr);
		$('#lblTelno').text(tel);

		$.ajax({
			type :'POST'
		,	url  :'./care_client_find_period.php'
		,	data :{
				'SR':'<?=$sr;?>'
			,	'jumin':jumin
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var col = __parseVal(data);
				var str = '';

				if (col['from'] && col['to']){
					str = col['from']+' ~ '+col['to'];

					if (col['gbn'] == '1'){
						str += '(진행중)';
					}else{
						str += '(중지)';
					}
				}

				$('#lblPeriod').text(str);
				$('#tempLodingBar').remove();

				lfLoadIljung(jumin);
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfLoadIljung(jumin){
		if (!jumin) jumin = $('tr[selYn="Y"]',$('#tbodyClientList')).attr('jumin');
		
		var align = $('input:radio[name="optAlign"]:checked').val();

		$.ajax({
			type :'POST'
		,	url  :'./care_client_find_iljung.php'
		,	data :{
				'SR':'<?=$sr;?>'
			,	'jumin':jumin
			,	'year':$('#lblYYMM').attr('year')
			,	'month':$('#lblYYMM').attr('month')
			,	'align':align
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#tbodySvcList').html(html);
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfExcel(gbn){
		if ($('tr[selYn="Y"]',$('#tbodyClientList')).length == 0){
			alert('대상자를 선택하여 주십시오.');
			return;
		}
		
		if (gbn){
			var jumin = gbn;
		}else{
			var jumin = $('tr[selYn="Y"]',$('#tbodyClientList')).attr('jumin');
		}


		location.href = './care_client_find_excel.php?SR=<?=$sr;?>&jumin='+jumin;
	}

	//재가관리 서비스 조회
	function lfCareSvcFind(){
		var h = 600;
		var w = 800;
		var t = (screen.availHeight - h) / 2;
		var l = (screen.availWidth - w) / 2;

		var url = '../care/care_suga_find.php';
		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
		var win = window.open('about:blank', 'FIND_CARESVC', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'sr':'<?=$sr;?>'
			,	'type':'CUST_SEL'
			,	'return':'lfCareSvcFindResult'
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

		form.setAttribute('target', 'FIND_CARESVC');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfCareSvcFindResult(cd,mstNm,proNm,svcNm,subNm){
		$('#ID_SUGA').attr('code',cd).text(subNm);
	}

	function lfCareSvcRemove(){
		$('#ID_SUGA').attr('code','').text('');
	}


	//전체 체크
	function lfChkAll(){
		var chk = $('#chkAll').attr('checked');

		$('input:checkbox[name="chkIn"]').each(function(){
			$(this).attr('checked',chk);
		});
	}

</script>
<div class="title title_border">대상자조회(<?=$title;?>)</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="60px">
		<col width="177px">
		<col width="50px">
		<col width="150px">
		<col width="30px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">성명</th>
			<td class="">
				<input id="txtName" type="text" style="width:70px;">
			</td>
			<th class="center">이용기간</th>
			<td class="">
				<input id="txtFromDt" type="text" value="" class="date"> ~
				<input id="txtToDt" type="text" value="" class="date">
			</td>
			<th class="center">서비스</th>
			<td class="last" onclick="lfCareSvcFind();" style="cursor:pointer;" onmouseover="this.style.backgroundColor='#EAEAEA';" onmouseout="this.style.backgroundColor='';" title="서비스 선택 조회를 원하시면 클릭하여 주십시오.">
				<div class="left" id="ID_SUGA" code=""></div>
			</td>
			<td class="center"><a href="#" style="font-weight:bold;" onclick="lfCareSvcRemove();">X</a></td>
			<td class="left last">
				<div style="float:left; width:auto;"><span class="btn_pack m"><button onclick="lfSearch();">조회</button></span></div>
				<div style="float:right; width:auto;">
					<!--<span class="btn_pack m"><span class="excel"></span><button onclick="lfExcel();">선택 대상자 출력</button></span>-->
				</div>
			</td>
		</tr>
	</tbody>
</table><?
$colgroup = '
	<col width="40px">
	<col width="70px">
	<col width="80px">
	<col width="40px">
	<col width="40px">
	<col width="200px">
	<col width="170px">
	<col>';?>
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head"><input id="chkAll" name="chk" type="checkbox" class="checkbox" onclick="lfChkAll();"></th>
			<th class="head">성명</th>
			<th class="head">생년월일</th>
			<th class="head">성별</th>
			<th class="head">중점</th>
			<th class="head">주소</th>
			<th class="head">전화번호/핸드폰</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="top last" colspan="8">
				<div style="width:100%; height:150px; overflow-x:hidden; overflow-y:scroll;">
					<table class="my_table" style="width:100%;">
						<colgroup><?=$colgroup;?></colgroup>
						<tbody id="tbodyClientList"></tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="450px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">개별정보</th>
			<th class="center last">결재란설정</th>
		</tr>
		<tr>
			<td class="left" style="padding:5px; line-height:1.5em;">
				<div style="clear:both;">
					<div style="float:left; width:60px;">성명/성별</div>
					<div style="float:left; width:auto;">:</div>
					<div style="float:left; width:auto; padding-left:5px;" id="lblNameGender"></div>
				</div>
				<div style="clear:both;">
					<div style="float:left; width:60px;">생년월일</div>
					<div style="float:left; width:auto;">:</div>
					<div style="float:left; width:auto; padding-left:5px;" id="lblBirthday"></div>
				</div>
				<div style="clear:both;">
					<div style="float:left; width:60px;">주소</div>
					<div style="float:left; width:auto;">:</div>
					<div style="float:left; width:auto; padding-left:5px;" id="lblAddr"></div>
				</div>
				<div style="clear:both;">
					<div style="float:left; width:60px;">연락처</div>
					<div style="float:left; width:auto;">:</div>
					<div style="float:left; width:auto; padding-left:5px;" id="lblTelno"></div>
				</div>
				<div style="clear:both;">
					<div style="float:left; width:60px;">계약기간</div>
					<div style="float:left; width:auto;">:</div>
					<div style="float:left; width:auto; padding-left:5px;" id="lblPeriod"></div>
				</div>
			</td>
			<td class="left top last"><?
				$sql = 'SELECT	*
						FROM	apprline_set
						WHERE	org_no	= \''.$orgNo.'\'
						AND		gbn		= \'01\'';
				$row = $conn->get_array($sql);

				if ($row){
					$lineName = Explode('|',$row['line_name']);
				}else{
					$lineName[] = '담당';
					$lineName[] = '팀장';
					$lineName[] = '센터장';
				}?>
				<script type="text/javascript">
					function lfApprSet(){
						$.ajax({
							type :'POST'
						,	url  :'./care_client_find_apprset.php'
						,	data :{
								'SR':'<?=$sr;?>'
							,	'prtYn':$('input:radio[name="optAppr"]:checked').val()
							,	'cnt':$('#cboApprCnt').val()
							,	'name1':$('#txtAppr1').val()
							,	'name2':$('#txtAppr2').val()
							,	'name3':$('#txtAppr3').val()
							,	'name4':$('#txtAppr4').val()
							,	'name5':$('#txtAppr5').val()
							}
						,	beforeSend:function(){
								$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
							}
						,	success:function(result){
								if (!result){
									alert('정상적으로 처리되었습니다.');
								}else{
									alert(result);
								}
								$('#tempLodingBar').remove();
							}
						,	error:function(){
							}
						}).responseXML;
					}
				</script>
				<div>
					출력여부 :
					<label><input id="optApprY" name="optAppr" type="radio" class="radio" value="Y" <?=$row['prt_yn'] != 'N' ? 'checked' : '';?>>예</label>
					<label><input id="optApprN" name="optAppr" type="radio" class="radio" value="N" <?=$row['prt_yn'] == 'N' ? 'checked' : '';?>>아니오</label>
				</div>
				<div style="padding-top:3px;">
					결재자명 :
					<input id="txtAppr1" type="text" value="<?=$lineName[0];?>" style="width:45px;">,
					<input id="txtAppr2" type="text" value="<?=$lineName[1];?>" style="width:45px;">,
					<input id="txtAppr3" type="text" value="<?=$lineName[2];?>" style="width:45px;">,
					<input id="txtAppr4" type="text" value="<?=$lineName[3];?>" style="width:45px;">,
					<input id="txtAppr5" type="text" value="<?=$lineName[4];?>" style="width:45px;">
				</div>
				<div style="padding-top:3px;">
					결재자수 :
					<select id="cboApprCnt" style="width:auto;">
						<option value="3" <?=$row['line_cnt'] == '3' ? 'selected' : '';?>>3명</option>
						<option value="4" <?=$row['line_cnt'] == '4' ? 'selected' : '';?>>4명</option>
						<option value="5" <?=$row['line_cnt'] == '5' ? 'selected' : '';?>>5명</option>
					</select>
				</div>
				<div style="float:right; width:auto; margin-top:5px;">
					<label><input id="chkSvcStatPrtYn" type="checkbox" class="checkbox" value="Y" <?=$gDomain != 'kacold.net' ? 'checked' : '';?>>서비스현황집계 출력</label>
				</div>
				<div style="float:left; width:auto; margin-top:5px;">
					<span class="btn_pack m"><button onclick="lfApprSet();">적용</button></span>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<script type="text/javascript">
	function lfExcelLog(gbn){
		
		var align = $('input:radio[name="optAlign"]:checked').val();
		
		var data = '';
		
		
		
		
		$('input:checkbox[name="chkIn"]').each(function(){
			var obj = $(this).parent().parent();
			
			data += (data ? '?' : '');

			if ($(this).attr('checked')){
				data += 'jumin='+$(this).attr('jumin');
			}
		});

		var parm = new Array();
			parm = {
				'SR'	:'<?=$sr;?>'
			,	'year'	:$('#lblYYMM').attr('year')
			,	'month'	:$('#lblYYMM').attr('month')
			,	'jumin'	:$('tr[selYn="Y"]',$('#tbodyClientList')).attr('jumin')
			,	'svcStatPrtYn':$('#chkSvcStatPrtYn').attr('checked') ? 'Y' : 'N'
			,	'align' :align
			,   'datas'  :data
			};

		if (gbn == 'Y') parm['month'] = '';

		var form = document.createElement('form');
		var objs;
		for(var key in parm){
			objs = document.createElement('input');
			objs.setAttribute('type', 'hidden');
			objs.setAttribute('name', key);
			objs.setAttribute('value', parm[key]);

			form.appendChild(objs);
		}

		form.setAttribute('target', '_self');
		form.setAttribute('method', 'post');
		form.setAttribute('action', './care_client_find_log_excel.php');

		document.body.appendChild(form);

		form.submit();
	}

	function lfPDF(gbn){

		if (gbn == 'Y'){
			var month = '';
		}else {
			var month = $('#lblYYMM').attr('month');
		}
		
		if($('#chkSvcStatPrtYn').attr('checked')){
			var chkSvc = 'Y';
		}else {
			var chkSvc = 'N';
		}
		
		
		var data = '';
		
		
		
		
		$('input:checkbox[name="chkIn"]').each(function(){
			var obj = $(this).parent().parent();
			
			data += (data ? '?' : '');

			if ($(this).attr('checked')){
				data += 'jumin='+$(this).attr('jumin');
			}
		});
		
		if(!data){
			alert('대상자를 선택하여주십시오.');
			return;
		}

		/*
		if(!$('tr[selYn="Y"]',$('#tbodyClientList')).attr('jumin')){
			alert('대상자를 선택하여주십시오.');
			return;
		}
		*/

		
		var dir = $('input:radio[name="optPrt"]:checked').val();
		var align = $('input:radio[name="optAlign"]:checked').val();
		var file = 'care_client_find_log';
		var data = data;
		

		

		var arguments	= 'root=care'
						+ '&dir='+dir
						+ '&align='+align
						+ '&fileName='+file
						+ '&fileType=pdf'
						+ '&target=show.php'
						+ '&SR='+'<?=$sr;?>'
						+ '&year='+$('#lblYYMM').attr('year')
						+ '&month='+month
						/*+ '&jumin='+$('tr[selYn="Y"]',$('#tbodyClientList')).attr('jumin')*/
						+ '&svcStatPrtYn='+chkSvc
						+ '&data='+data;

		__printPDF(arguments);
	}

</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="51px">
		<col width="530px"></col>
		<col width="100px">
		<col width="120px">
	</colgroup>
	<tbody>
		<tr>
			<th>일자정렬:</th>
			<td><input id="optAlign1" name="optAlign" type="radio" class="radio" value="1" onclick="lfLoadIljung();" checked >내림차순 
				<input id="optAlign2" name="optAlign" type="radio" class="radio" value="2" onclick="lfLoadIljung();" >오름차순</td>
			<th class="last">PDF 출력구분 :</th>
			<td class="last" >
				<label><input id="optPrtP" name="optPrt" type="radio" class="radio" value="P" checked>세로</label>
				<label><input id="optPrtL" name="optPrt" type="radio" class="radio" value="L" >가로</label>
			</td>
		</tr>
		<tr>
			<th class="center">조회년월</th>
			<td class="last" colspan="3">
				<div class="left" style="float:left; width:auto; padding-top:2px;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),-1); lfLoadIljung();" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYYMM" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),1); lfLoadIljung();" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
				<div class="left" style="float:left; width:auto; padding-top:1px;"><?=$myF->_btn_month($month,'__moveMonth(',',$("#lblYYMM")); lfLoadIljung();');?></div>
				<div class="left" style="float:left; width:auto; padding-top:2px;"><?
					if($debug){ ?>
					<span class="btn_pack m"><span class="excel"></span><button onclick="lfExcelLog('Y');">년별</button></span><? } ?>
					<span class="btn_pack m"><span class="excel"></span><button onclick="lfExcelLog('M');">월별</button></span>
					<span class="btn_pack m"><span class="pdf"></span><button onclick="lfPDF('Y');">년별</button></span>
					<span class="btn_pack m"><span class="pdf"></span><button onclick="lfPDF('M');">월별</button></span>
				</div>
			</td>
		</tr>
		
	</tbody>
</table><?

$colgroup = '
	<col width="70px">
	<col width="50px">
	<col width="50px">
	<col width="200px">
	<col width="150px">
	<col width="100px">
	<col>';?>

<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">년</th>
			<th class="head">월</th>
			<th class="head">일</th>
			<th class="head">서비스</th>
			<th class="head">자원</th>
			<th class="head">담당자</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="tbodySvcList"></tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once('../inc/_db_close.php');
?>