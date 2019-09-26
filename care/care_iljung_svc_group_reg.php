<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$grpCd	= $_POST['grpCd'];
	$sugaCd	= $_POST['sugaCd'];
	$seq	= $_POST['seq'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	
	
	if ($SR == 'S'){
		$title = '재가지원';
	}else if ($SR == 'R'){
		$title = '자원연계';
	}else{?>
		<script type="text/javascript">
			self.close();
		</script><?
		exit;
	}

	//서비스 조회
	$sql = 'SELECT	CONCAT(suga_cd,suga_sub) AS cd
			,		suga_nm AS nm
			FROM	care_suga
			WHERE	org_no	= \''.$orgNo.'\'
			AND		suga_sr	= \''.$SR.'\'
			ORDER	BY suga_cd,suga_sub';

	$arrSvc = $conn->_fetch_array($sql,'cd');

	if ($grpCd){
		$sql = 'SELECT	*
				FROM	care_svc_iljung
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		grp_cd	= \''.$grpCd.'\'';

		$grp = $conn->get_array($sql);
	}else{
		$grp['date'] = $year.'-'.$month.'-01';
	}
?>
<script type="text/javascript">
	var returnObj;

	//달력 년월이동 버튼을 숨긴다.
	IsCalBtnShow = false;
	defaultCalDate = '<?=$year;?>-<?=$month;?>-01';

	$(document).ready(function(){
		lfInit();
		lfSetHeight($('#divGroup'));
		lfSetHeight($('#divTarget'));

		if ($('#grpCd').val() != ''){
			$('div:first',$('#divGroup')).click();
		}
	});

	window.onunload = function(){
		opener.lfSearch();
	}

	function lfInit(){
		$('input').each(function(){
			__init_object(this);
		});
	}

	function lfSetHeight(obj,gab){
		var top = $(obj).offset().top;
		var height = $(document).height() - top;

		if (!gab) gab = 0;

		$(obj).height(height+gab);
	}

	function lfGetGroup(){
		$.ajax({
			type:'POST'
		,	url:'./care_iljung_svc_group_reg_search.php'
		,	data:{
				'SR':'<?=$SR;?>'
			,	'category':$('#ID_CATEGORY').attr('category')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#divGroup').html(html);
				$('#tempLodingBar').remove();
			}
		}).responseXML;
	}

	function lfSetGroup(suga,res,seq,memCd){
		$('#objVal').attr('sugaCd',suga).attr('resCd',res).attr('seq',seq).attr('memCd',memCd);

		$.ajax({
			type:'POST'
		,	url:'./care_iljung_svc_group_info.php'
		,	data:{
				'SR'	:'<?=$SR;?>',
				'suga'	:suga,
				'seq'	:seq,
				'year'	:'<?=$year;?>',
				'month'	:'<?=$month;?>',
				'grpCd'	:$('#grpCd').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				var col = __parseVal(data);

				$('#lblGroupName').text(col['grpNm']);
				$('#lblSugaName').text(col['svcNm']);
				$('#lblResName').text(col['resNm']);
				$('#lblMemName').text(col['memNm']);

				var tg = col['target'].split('/');
				var html = '';
				var no = 1;

				for(var i in tg){
					if (i > 0){
						var val = tg[i].split(':');

						html += lfTGHtml(no,val);
						no ++;
					}

				}

				$('#tbodyTarget').html(html);
				lfInit();
			}
		}).responseXML;
	}

	function lfRemoveRow(no){
		$('#rowId_'+no).remove();
	}

	function lfTGAdd(){
		if (!$('#objVal').attr('sugaCd') || !$('#objVal').attr('sugaCd') || !$('#objVal').attr('seq')){
			alert('묶음을 선택하여 주십시오.');
			return;
		}
		
		var h = 400;
		var w = 600;
		var t = (screen.availHeight - h) / 2;
		var l = (screen.availWidth - w) / 2;

		var url = '../inc/_find_person.php';
		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
		var win = window.open('about:blank', 'FIND_CLIENT', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'type':'sugupja'
			,	'year':$('#lblYYMM').attr('year')
			,	'month':$('#lblYYMM').attr('month')
			,	'svcCd':'<?=$SR;?>'
			,	'rtnType':'key'
			,	'return':'lfTGAddResult'
			};

		var form = document.createElement('form');
		var objs;
		for(var key in parm){
			objs = document.createElement('input');
			objs.setAttribute('type','hidden');
			objs.setAttribute('name',key);
			objs.setAttribute('value',parm[key]);

			form.appendChild(objs);
		}

		form.setAttribute('target','FIND_CLIENT');
		form.setAttribute('method','post');
		form.setAttribute('action',url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfTGAddResult(){
		var val = new Array();

		val[0] = returnObj[0];
		val[1] = returnObj[1];
		val[2] = returnObj[3];
		val[3] = returnObj[2];

		var html = lfTGHtml('-',val);
		var isYn = false;
		//if('<?=$debug;?>') alert(val[0]);
		
		$('tr',$('#tbodyTarget')).each(function(){
			var key = $(this).attr('key');
			if(key == val[0]){
				alert('이미 추가된 대상입니다. 다시 확인해 주십시오.');
				isYn = true;
			}
		});

		if(isYn != true){
			if ($('tr',$('#tbodyTarget')).length > 0){
				$('tr:last',$('#tbodyTarget')).after(html);
			}else{
				$('#tbodyTarget').html(html);
			}
		}
	}

	function lfTGHtml(no,val){
		var html = '';
		var tmpNo = '';
		var dt = $('#txtDate').val();

		if (no == '-'){
			tmpNo = no+$('tr',$('#tbodyTarget')).length;
		}else{
			tmpNo = no;
		}

		if (val[8] == undefined){
		}else if (val[8] == ''){
			val[8] = $('#objVal').attr('memCd');
			val[9] = $('#lblMemName').text();
		}

		if (!val[8]){
			 val[8] = '';
			 val[9] = '';
		}

		if (!val[4]) val[4] = '';
		if (!val[10]) val[10] = '';
		if (!val[11]) val[11] = '';
		if (!val[12]) val[12] = '';
		if (val[12] != 'Y') val[12] = 'N';
		if (val[12] == 'Y'){
			val[12] = '';
		}else{
			val[12] = 'none';
		}

		html += '<tr id="rowId_'+tmpNo+'" key="'+val[0]+'" cd="'+val[4]+'">'
			 +	'<td class="center">'+no+'</td>'
			 +	'<td class="center"><div id="tgtName" class="left nowrap" style="width:70px;">'+val[1]+'</div></td>'
			 +	'<td class="center">'+val[2]+'</td>'
			 +	'<td class="center"><span style="color:'+(val[3] == '남' ? 'BLUE' : 'RED')+';">'+val[3]+'</span></td>'
			 +	'<td class="center"><input id="txtDate_'+tmpNo+'" type="text" class="date" value="'+(val[5] ? val[5] : dt)+'"></td>'
			 +	'<td class="center">'
			 +	'<label><input id="optTime1_'+tmpNo+'" name="optTime_'+tmpNo+'" type="radio" value="1" class="radio" time="10:00" '+(val[6] != '2' ? 'checked' : '')+'>오전</label>'
			 +	'<label><input id="optTime2_'+tmpNo+'" name="optTime_'+tmpNo+'" type="radio" value="2" class="radio" time="13:00" '+(val[6] == '2' ? 'checked' : '')+'>오후</label>'
			 +	'</td>';
		
		if('<?=$SR;?>' == 'R'){
			html +=	'<td class="center">'
				 +	'<label><input id="chkConf_'+tmpNo+'" type="checkbox" value="Y" class="checkbox" '+(val[7] == 'Y' ? 'checked' : '')+'></label>'
				 +	'</td>';
		}
		
		html +=	'<td class="center">'
			 +	'<div style="float:left; width:auto; height:25px; padding-left:2px; padding-top:1px;"><span class="btn_pack find" onclick="lfMemberFind(\''+tmpNo+'\');"></span></div>'
			 +	'<div style="float:left; width:60px;" class="nowrap" id="lblMem_'+tmpNo+'" code="'+val[8]+'">'+val[9]+'</div>'
			 +	'<div style="float:right; width:auto; padding-right:5px;"><a href="#" onclick="lfRemoveMem(\''+tmpNo+'\');"><span class="bold">X</span></a></div>'
			 +	'</td>'
			 +	'<td class="center last">'
			 +	'<div class="left" style="float:left; width:auto; padding-top:2px;">';

		if (val[10]){
			html +=	'<span class="btn_pack m"><button onclick="lfWorkLogReg(this,{\'date\':\''+val[5]+'\',\'time\':\''+val[11]+'\',\'seq\':\''+val[10]+'\',\'jumin\':\''+val[4]+'\',\'id\':\''+tmpNo+'\'});">업무일지</button></span> ';
		}

		html +=	'<span class="btn_pack m"><button onclick="lfRemoveRow(\''+tmpNo+'\');" style="color:RED;">삭제</button></span>';

		html +=	'</div>'
			 +	'<div id="ID_WORKLOG_RESULT" class="left bold" style="float:left; width:auto; color:BLUE; display:'+val[12]+';">-업무일지 작성됨.</div>'
			 +	'</td>'
			 +	'</tr>';

		return html;
	}

	function lfSetDate(obj){
		var id = $(obj).attr('id');
		$('input:text[id^="'+id+'"]').val($(obj).val());
	}

	function lfSetTime(obj){
		var id = $(obj).attr('name');
		var val = $(obj).val();
		$('input:radio[name^="'+id+'"][value="'+val+'"]').attr('checked',true);
	}

	function lfSetConf(obj){
		var id = $(obj).attr('id');
		var chk = $(obj).attr('checked');
		$('input:checkbox[id^="'+id+'"]').attr('checked',chk);
	}

	function lfSave(){
		if (!$('#objVal').attr('sugaCd') || !$('#objVal').attr('sugaCd') || !$('#objVal').attr('seq')){
			alert('묶음을 선택하여 주십시오.');
			return;
		}

		if ($('tr', $('#tbodyTarget')).length < 1){
			alert('대상자가 없습니다. 확인하여 주십시오.');
			return;
		}

		var IsNext = true;

		$('input:text[id^="txtDate_"][value=""]').each(function(){
			alert('일자를 입력하여 주십시오.');
			$(this).focus();
			IsNext = false;
			return false;
		});

		
		$('div[id^="lblMem_"][code=""]').each(function(){
			alert('담당직원을 선택하여 주십시오.');
			IsNext = false;
			return false;
		});


		if (!IsNext) return;

		var data = {};

		data['SR']		= '<?=$SR;?>';
		data['year']	= '<?=$year;?>';
		data['month']	= '<?=$month;?>';
		data['grpCd']	= $('#grpCd').val();
		data['suga']	= $('#objVal').attr('sugaCd');
		data['res']		= $('#objVal').attr('resCd');
		data['seq']		= $('#objVal').attr('seq');
		data['memCd']	= $('#objVal').attr('memCd');
		data['date']	= $('#txtDate').val().split('-').join('');
		data['time']	= $('input:radio[name="optTime"]:checked').val();
		
		if('<?=$SR;?>' == 'R'){ 
			data['conf']	= $('#chkConf').attr('checked') ? 'Y' : 'N';
		}else {
			data['conf'] = 'Y';
		}

		data['target']	= '';

		$('tr',$('#tbodyTarget')).each(function(){
			var id = $(this).attr('id').split('rowId_').join('');
			var key = $(this).attr('key');
			var cd = $(this).attr('cd');
			var date = $('#txtDate_'+id).val().split('-').join('');
			var time = $('input:radio[name="optTime_'+id+'"]:checked').val();
			
			if('<?=$SR?>' == 'R'){ 
				var conf = $('#chkConf_'+id).attr('checked') ? 'Y' : 'N';
			}else {
				var conf = 'Y';
			}
			

			var memCd = $('#lblMem_'+id).attr('code');
			var memNm = $('#lblMem_'+id).text();
			var tgtNm = $('#tgtName', this).text();
			
			

			data['target'] += ('?key='+key+'&jumin='+cd+'&date='+date+'&time='+time+'&conf='+conf+'&memCd='+memCd+'&memNm='+memNm+'&tgtNm='+tgtNm);
		});

		$.ajax({
			type:'POST'
		,	url:'./care_iljung_svc_group_save.php'
		,	data:data
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
				}else if (result == 9){
					alert('데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else if (result.length == 10){
					$('#grpCd').val(result);
					alert('정상적으로 처리되었습니다.');
				}else{
					alert(result);
				}
			}
		}).responseXML;
	}

	function lfMemberFind(id){
		var h = 400;
		var w = 600;
		var t = (screen.availHeight - h) / 2;
		var l = (screen.availWidth - w) / 2;

		var url    = '../inc/_find_person.php';
		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
		var win    = window.open('about:blank', 'FIND_MEMBER', option);
			win.opener = self;
			win.focus();

		if (!id) id = '';

		var parm = new Array();
			parm = {
				'type':'member'
			,	'kind':'<?=$SR;?>'
			,	'openerId':id
			,	'return':'lfMemResult'
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

		form.setAttribute('target', 'FIND_MEMBER');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfMemResult(){
		var col = __parseStr(returnObj);

		if (col['openerId']){
			$('#lblMem_'+col['openerId']).attr('code',col['jumin']).text(col['name']);
		}else{
			$('#objVal').attr('memCd',col['jumin']);
			$('#lblMemName').text(col['name']);
			$('div[id^="lblMem_"]').attr('code',col['jumin']).text(col['name']);
		}
	}

	function lfRemoveMem(id){
		if (!id){
			$('#objVal').attr('memCd','');
			$('#lblMemName').text('');
		}else{
			$('#lblMem_'+id).attr('code','').text('');
		}
	}

	function lfCategoryFind(){
		var objModal = new Object();
		var url = './care_svc_category_find.php';
		var style = 'dialogWidth:800px; dialogHeight:600px; dialogHide:yes; scroll:no; status:no';

		objModal.win = window;
		objModal.SR	 = '<?=$SR;?>';
		objModal.code= '';
		objModal.name= '';

		window.showModalDialog(url, objModal, style);

		if (objModal.code){
			$('#ID_CATEGORY').attr('category',objModal.code).text(objModal.name);
			lfGetGroup();
		}
	}

	//재가지원 업무내용등록
	function lfWorkLogReg(obj, val){
		var objModal = new Object();
		var url = './care_works_log_reg.php';
		var style = 'dialogWidth:800px; dialogHeight:550px; dialogHide:yes; scroll:no; status:no';

		objModal.svcCd = '<?=$SR;?>';
		objModal.date = val['date'].split('-').join('');
		objModal.time = val['time'];
		objModal.seq = val['seq'];
		objModal.jumin = val['jumin'];
		objModal.suga = $('#objVal').attr('sugaCd');
		objModal.resource = $('#objVal').attr('resCd');
		//objModal.mem = $('#objVal').attr('memCd');
		objModal.mem = $('#lblMem_'+val['id']).attr('code');
		objModal.result = true;


		/*if ('<?=$debug;?>' == '1'){
			alert(objModal.svcCd
				+'\n'+objModal.date
				+'\n'+objModal.time
				+'\n'+objModal.seq
				+'\n'+objModal.jumin
				+'\n'+objModal.suga
				+'\n'+objModal.resource
				+'\n'+objModal.mem);
		}*/

		window.showModalDialog(url, objModal, style);

		var obj = __GetTagObject($(obj),'TR');

		if (objModal.worklog){
			$('#ID_WORKLOG_RESULT',obj).show();
		}else{
			$('#ID_WORKLOG_RESULT',obj).hide();
		}
	}
</script>
<div class="title title_border">묶음서비스 일정등록(<?=$title;?>)</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head">카테고리 선택</th>
			<td id="ID_CATEGORY" class="left last" onmouseover="this.style.backgroundColor='#EAEAEA';" onmouseout="this.style.backgroundColor='#FFFFFF';" onclick="lfCategoryFind();" category=""></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head">묶음명</th>
			<td class="top" rowspan="2">
				<div id="objVal" sugaCd="" resCd="" seq="" memCd="" style="display:none;"></div>
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="70px">
						<col width="70px">
						<col width="50px">
						<col width="110px">
						<col width="70px">
						<col width="30px">
						<col width="70px">
						<col width="10px">
						<col width="60px">
						<col width="20px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="center">그룹명</th>
							<td class="left last" colspan="10" id="lblGroupName"></td>
						</tr>
						<tr>
							<th class="center">서비스명</th>
							<td class="left last" colspan="10" id="lblSugaName"></td>
						</tr>
						<tr>
							<th class="center">자원명</th>
							<td class="left last" colspan="10" id="lblResName"></td>
						</tr>
						<tr>
							<th class="center">일자</th>
							<td><input id="txtDate" name="txt" type="text" class="date" value="<?=$myF->dateStyle($grp['date']);?>" onchange="lfSetDate(this);" />
							<th class="center">시간</th>
							<td class="">
								<label><input id="optTime1" name="optTime" type="radio" value="1" class="radio" time="10:00" onclick="lfSetTime(this);" <?=($grp['time'] != '2' ? 'checked' : '');?>>오전</label>
								<label><input id="optTime2" name="optTime" type="radio" value="2" class="radio" time="13:00" onclick="lfSetTime(this);" <?=($grp['time'] == '2' ? 'checked' : '');?>>오후</label>
							</td><?
							if($SR == 'R'){ ?>
								<th class="center">실적등록</th>
								<td class="center">
									<label><input id="chkConf" type="checkbox" value="Y" class="checkbox" onclick="lfSetConf(this);" <?=($grp['conf_yn'] == 'Y' ? 'checked' : '');?>></label>
								</td><?
							} ?>
							<th class="center">담당직원</th>
							<td class="center last" style="padding-left:2px; padding-top:1px;"><span class="btn_pack find" onclick="lfMemberFind();"></span></td>
							<td class="last" id="lblMemName"></td>
							<td class="center"><a href="#" onclick="lfRemoveMem();"><span class="bold">X</span></a></td>
							<td class="left last">
								<span class="btn_pack m"><span class="save"></span><button onclick="lfSave();">저장</button></span>
							</td>
						</tr>
						<tr>
							<th class="center top bottom">
								<div style="padding-top:5px;">대상자</div>
								<div><span class="btn_pack small"><button id="btnTGAdd" onclick="lfTGAdd();">추가</button></span></div>
							</th>
							<td class="bottom last" colspan="10"><?
								if($SR == 'R'){  
									$colgroup = '
										<col width="40px">
										<col width="70px">
										<col width="70px">
										<col width="40px">
										<col width="80px">
										<col width="110px">
										<col width="30px">
										<col width="100px">
										<col>';	
								}else {
									$colgroup = '
										<col width="40px">
										<col width="70px">
										<col width="70px">
										<col width="40px">
										<col width="80px">
										<col width="110px">
										<col width="100px">
										<col>';	
								}
								?>
								<table class="my_table" style="width:100%;">
									<colgroup><?=$colgroup;?></colgroup>
									<thead>
										<tr>
											<th class="head">No</th>
											<th class="head">성명</th>
											<th class="head">생년월일</th>
											<th class="head">성별</th>
											<th class="head">일자</th>
											<th class="head">시간</th>
											<? if($SR == 'R'){ ?> <th class="head">실적</th><? } ?>
											
											<th class="head">담당직원</th>
											<th class="head last">비고</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td class="last" colspan="20">
												<div id="divTarget" style="width:100%; height:100px; overflow-x:hidden; overflow-y:auto;">
													<table class="my_table" style="width:100%;">
														<colgroup><?=$colgroup;?></colgroup>
														<tbody id="tbodyTarget"></tbody>
													</table>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td class="top">
				<div id="divGroup" style="width:100%; height:100px; overflow-x:hidden; overflow-y:auto;"><?
					$sql = 'SELECT	group_nm
							,		suga_cd
							,		res_cd
							,		mem_cd
							,		seq
							FROM	care_svc_group
							WHERE	org_no	= \''.$orgNo.'\'
							AND		org_type= \''.$SR.'\'';
					
					if(!$grpCd){
						$sql .=' AND		del_flag= \'N\'';
					}

					$sql .=' AND		suga_cd = (	SELECT	CONCAT(suga_cd, suga_sub)
												FROM	care_suga
												WHERE	org_no	= care_svc_group.org_no
												AND		suga_sr = care_svc_group.org_type
												AND		CONCAT(suga_cd, suga_sub) = care_svc_group.suga_cd
												AND		LEFT(from_dt,4) <= \''.$year.'\'
												AND		LEFT(to_dt,  4) >= \''.$year.'\'';

					if ($IsCareYoyAddon){
						$sql .= '				UNION	ALL
												SELECT	code
												FROM	care_suga_comm';
					}

					$sql .= '					LIMIT	1)';

					if ($grpCd){
						$sql .= '
							AND		suga_cd	= \''.$sugaCd.'\'
							AND		seq		= \''.$seq.'\'';
					}

					$sql .= '
							ORDER	BY group_nm';
					
					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);
						
						?>
						<div onclick="lfSetGroup('<?=$row['suga_cd'];?>','<?=$row['res_cd'];?>','<?=$row['seq'];?>','<?=$ed->en($row['mem_cd']);?>');" onmouseover="this.style.backgroundColor='#EFEFEF';" onmouseout="this.style.backgroundColor='#FFFFFF';" style="cursor:pointer; line-height:1.3em; padding:5px; border-top:<?=($i > 0 ? '1px solid #CCCCCC;' : 'none');?>;">
							<div class="nowrap"><?=$row['group_nm'];?></div>
						</div><?
					}

					$conn->row_free();?>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<input id="grpCd" type="hidden" value="<?=$grpCd;?>">
<?
	Unset($grp);
	include_once('../inc/_footer.php');
?>