<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$today	= Date('Y-m-d');
	$year	= Date('Y');

	if ($gDomain == _KLCF_){
		$lsUrl = 'care.'.$gDomain;
	}else{
		$lsUrl = 'www.'.$gDomain;
	}

	$lbAdmin	= $_SESSION['userLevel'];
?>
<script type="text/javascript">
	$(document).ready(function(){
		__init_form(document.f);

		$('#chkCostHoldYn').unbind('click').bind('click',function(){
			lfSetHold();
		});

		lfSetHold();
		lfSearch();
		lfSetGroup();

		if ("<?=$lbAdmin;?>" == 'B'){
			lfFindManager($('#cboManager'),"<?=$_SESSION['userBranchCode'];?>");
		}

		$('input:checkbox[name="chkSvc"]').unbind('click').bind('click',function(){
			if ($(this).attr('id') == 'chkCareSvcYn'){
				if ($(this).attr('checked')){
					$('#cboCareArea').attr('disabled',false);
					$('#cboCareGroup').attr('disabled',false);
					$('input:checkbox[name="chkCare"]').attr('disabled',false);
				}else{
					$('#cboCareArea').attr('disabled',true);
					$('#cboCareGroup').attr('disabled',true);
					$('input:checkbox[name="chkCare"]').attr('disabled',true);
				}
			}
		});

		if ($('#cboBranch').val()){
			$('#cboBranch').change();
		}
	});

	function lfSetHold(){
		$('#txtClientCost').attr('disabled',$('#chkCostHoldYn').attr('checked'));
		$('#txtClientCnt').attr('disabled',$('#chkCostHoldYn').attr('checked'));
	}

	function lfFindCenter(){
		var objModal = new Object();
		var url      = '../find/_find_center.php';
		var style    = 'dialogWidth:800px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';

		objModal.mode = '<?=$type;?>';
		objModal.code = '';
		objModal.name = '';

		window.showModalDialog(url, objModal, style);

		if (!objModal.code) return;

		$('#lblLinkCode').text(objModal.code);
		$('#lblLinkName').text(objModal.name);
	}

	function lfFindManager(cbo,asBranch,asManager){
		$.ajax({
			type :'POST'
		,	url  :'../find/_find_branch_manager.php'
		,	data :{
				'branch':asBranch
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				var list = data.split(String.fromCharCode(1));
				var html = '';

				if (list){
					for(var i=0; i<list.length; i++){
						if (list[i]){
							var val = list[i].split(String.fromCharCode(2));

							html += '<option value="'+val[0]+'" '+($('#cboBranch').val()+asManager == val[0] ? 'selected' : '')+'>'+val[1]+'</option>';
						}
					}
				}

				$(cbo).html(html);
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfLinkCenter(){
		if (!$('#lblLinkCode').text()){
			lfFindCenter();
		}

		if (!$('#cboBranch').val()){
			alert('지사명을 선택하여 주십시오.');
			$('#cboBranch').focus();
			return;
		}

		if (!$('#cboManager').val()){
			alert('담당자를 선택하여 주십시오.');
			$('#cboManager').focus();
			return;
		}

		if (!$('#txtStartDt').val()){
			alert('시작일자를 입력하여 주십시오.');
			$('#txtStartDt').focus();
			return;
		}

		if ($('input:checkbox[name="chkSvc"]:checked').length == 0){
			alert('이용서비스를 하나이상 선택하여 주십시오.');
			return;
		}

		if ($('#chkCareSvcYn').attr('checked')){
			if (!$('#chkCareSupportYn').attr('checked') && !$('#chkCareResourceYn').attr('checked')){
				alert('재가관리 상세내역을 선택하여 주십시오.');
				return;
			}
		}

		if ('<?=$lbAdmin;?>' == 'A'){
			var data = {
					'mode'      :'<?=$type;?>'
				,	'code'      :$('#lblLinkCode').text()
				,	'branch'    :$('#cboBranch').val()
				,	'manager'   :$('#cboManager').val()
				,	'homeCare'  :$('#chkHomeCareYn').attr('checked') ? 'Y' : 'N'
				,	'vouNurse'  :$('#chkVouNurseYn').attr('checked') ? 'Y' : 'N'
				,	'vouOld'    :$('#chkVouOldYn').attr('checked') ? 'Y' : 'N'
				,	'vouBaby'   :$('#chkVouBabyYn').attr('checked') ? 'Y' : 'N'
				,	'vouDis'    :$('#chkVouDisYn').attr('checked') ? 'Y' : 'N'
				,	'careSvc'	:$('#chkCareSvcYn').attr('checked') ? 'Y' : 'N'
				,	'careArea'	:$('#cboCareArea option:selected').val()
				,	'careGroup'	:$('#cboCareGroup option:selected').val()
				,	'careSp'	:$('#chkCareSupportYn').attr('checked') ? 'Y' : 'N'
				,	'careRs'	:$('#chkCareResourceYn').attr('checked') ? 'Y' : 'N'
				,	'startDt'   :$('#txtStartDt').val().split('-').join('')
				,	'contDt'    :$('#txtContDt').val().split('-').join('')
				,	'fromDt'    :$('#txtFromDt').val()
				,	'toDt'      :$('#txtToDt').val()
				,	'cmsCd'     :$('#txtCMSCd').val()
				,	'holdYn'    :$('#chkCostHoldYn').attr('checked') ? 'Y' : 'N'
				,	'basicCost' :__str2num($('#txtBasicCost').val())
				,	'clientCost':__str2num($('#txtClientCost').val())
				,	'clientCnt' :__str2num($('#txtClientCnt').val())
				,	'other'     :$('#txtOther').val()
			};
		}else{
			var data = {
					'mode'      :'<?=$type;?>'
				,	'code'      :$('#lblLinkCode').text()
				,	'branch'    :$('#cboBranch').val()
				,	'manager'   :$('#cboManager').val()
				,	'homeCare'  :$('#chkHomeCareYn').attr('checked') ? 'Y' : 'N'
				,	'vouNurse'  :$('#chkVouNurseYn').attr('checked') ? 'Y' : 'N'
				,	'vouOld'    :$('#chkVouOldYn').attr('checked') ? 'Y' : 'N'
				,	'vouBaby'   :$('#chkVouBabyYn').attr('checked') ? 'Y' : 'N'
				,	'vouDis'    :$('#chkVouDisYn').attr('checked') ? 'Y' : 'N'
				,	'careSvc'	:$('#chkCareSvcYn').attr('checked') ? 'Y' : 'N'
				,	'careArea'	:$('#cboCareArea option:selected').val()
				,	'careGroup'	:$('#cboCareGroup option:selected').val()
				,	'careSp'	:$('#chkCareSupportYn').attr('checked') ? 'Y' : 'N'
				,	'careRs'	:$('#chkCareResourceYn').attr('checked') ? 'Y' : 'N'
				,	'startDt'   :$('#txtStartDt').val().split('-').join('')
				,	'contDt'    :$('#txtContDt').val().split('-').join('')
				,	'fromDt'    :$('#txtFromDt').val()
				,	'toDt'      :$('#txtToDt').val()
				,	'other'     :$('#txtOther').val()
			};
		}

		$.ajax({
			type :'POST'
		,	url  :'./add.php'
		,	data :data
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리돠었습니다.');
				}else if (result == 9){
					alert('오류가 발생하였습니다.\n잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}

				if (result == 1){
					$('#lblLinkCode').text('');
					$('#lblLinkName').text('');
					$('#cboBranch').val('');
					$('#chkHomeCareYn').attr('checked',true);
					$('#chkVouNurseYn').attr('checked',false);
					$('#chkVouOldYn').attr('checked',false);
					$('#chkVouBabyYn').attr('checked',false);
					$('#chkVouDisYn').attr('checked',false);
					$('#chkCareSvcYn').attr('checked',false);
					$('#cboCareArea').attr('disabled',true);
					$('#cboCareGroup').attr('disabled',true);
					$('#chkCareSupportYn').attr('checked',false).attr('disabled',true);
					$('#chkCareResourceYn').attr('checked',false).attr('disabled',true);

					$('#txtStartDt').val('<?=$today;?>');
					$('#txtContDt').val('');
					$('#txtFromDt').val('<?=$today;?>');
					$('#txtToDt').val('9999-12-31');
					$('#txtCMSCd').val('');
					$('#txtOther').val('');

					$('#chkCostHoldYn').attr('checked',false);
					$('#txtBasicCost').val('30,000');
					$('#txtClientCnt').val('30');
					$('#txtClientCost').val('500');

					lfFindManager($('#cboManager'));

					lfSearch();
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSearch(aiPage){
		var html   = '';
		var page   = __str2num(aiPage);
		var maxCnt = 0;

		if (page < 1) page = 1;

		$.ajax({
			type :'POST'
		,	url  :'./search.php'
		,	data :{
				'mode':'<?=$type;?>'
			,	'page':0
			,	'code':$('#txtFindCode').val()
			,	'name':$('#txtFindName').val()
			,	'manager':$('#txtManager').val()
			,	'cms':$('#txtCMSCode').val()
			,	'branch':$('#cboFindBranch').val()
			,	'contFrom':$('#txtContFrom').val().split('-').join('')
			,	'contTo':$('#txtContTo').val().split('-').join('')
			,	'contYn':$('#cboNoCont').attr('checked') ? 'N' : 'Y'
			,	'cmsYn':$('#cboNoCMS').attr('checked') ? 'N' : 'Y'
			,	'applyDt':$('#applyDt').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				maxCnt = __str2num(result);

				if (maxCnt > 0){
					$.ajax({
						type :'POST'
					,	url  :'./search.php'
					,	data :{
							'mode':'<?=$type;?>'
						,	'page':page
						,	'max' :maxCnt
						,	'code':$('#txtFindCode').val()
						,	'name':$('#txtFindName').val()
						,	'manager':$('#txtManager').val()
						,	'cms':$('#txtCMSCode').val()
						,	'branch':$('#cboFindBranch').val()
						,	'contFrom':$('#txtContFrom').val().split('-').join('')
						,	'contTo':$('#txtContTo').val().split('-').join('')
						,	'contYn':$('#cboNoCont').attr('checked') ? 'N' : 'Y'
						,	'cmsYn':$('#cboNoCMS').attr('checked') ? 'N' : 'Y'
						,	'applyDt':$('#applyDt').val()
						}
					,	beforeSend:function(){
						}
					,	success:function(data){
							var list = data.split(String.fromCharCode(1));

							for(var i=0; i<list.length; i++){
								if (list[i]){
									var val  = list[i].split(String.fromCharCode(2));
									var acct = __num2str(val[7]);

									if (val[6] == 'Y'){
										acct = '<span style="font-weight:bold; color:red;">'+acct+'</span>';
									}

									html += '<tr id="objRow_'+i+'"'
										 +  ' code="'+val[1]+'"'
										 +  ' name="'+val[2]+'"'
										 +  ' branch="'+val[10]+'"'
										 +  ' manager="'+val[12]+'"'
										 +  ' homeCareYn="'+val[14].substring(0,1)+'"'
										 +  ' vouNurseYn="'+val[14].substring(1,2)+'"'
										 +  ' vouOldYn="'+val[14].substring(2,3)+'"'
										 +  ' vouBabyYn="'+val[14].substring(3,4)+'"'
										 +  ' vouDisYn="'+val[14].substring(4,5)+'"'
										 +  ' careSvcYn="'+val[21]+'"'
										 +  ' careArea="'+val[22]+'"'
										 +  ' careGroup="'+val[25]+'"'
										 +  ' careSupport="'+val[23]+'"'
										 +  ' careResource="'+val[24]+'"'
										 +  ' startDt="'+val[15]+'"'
										 +  ' contDt="'+val[16]+'"'
										 +  ' fromDt="'+val[3]+'"'
										 +  ' toDt="'+val[4]+'"'
										 +  ' cmsCd="'+val[5]+'"'
										 +  ' holdYn="'+val[6]+'"'
										 +  ' basicCost="'+__num2str(val[7])+'"'
										 +  ' clientCost="'+__num2str(val[8])+'"'
										 +  ' clientCnt="'+__num2str(val[9])+'"'
										 +  ' other="'+val[17]+'"'
										 +  ' onmouseover="this.style.backgroundColor=\'#efefef\';"'
										 +  ' onmouseout="this.style.backgroundColor=\'#ffffff\';"'
										 +  '>'
										 +  '<td class="center">'+val[0]+'</td>'
										 +  '<td class="center"><div class="left nowrap" style="width:80px;">'+val[1]+'</div></td>'
										 +  '<td class="center"><div class="nowrap" style="width:110px; text-align:left;" title="'+val[2]+'">'+val[2]+'</div></td>'
										 +  '<td class="left">'+val[18]+'</td>'
										 +  '<td class="center">'+val[3].split('-').join('.')+'~'+val[4].split('-').join('.')+'</td>'
										 +  '<td class="center">'+val[16].split('-').join('.')+'</td>'
										 +  '<td class="center">'+val[5]+'</td>'
										 +  '<td class="right">'+acct+'</td>'
										 +  '<td class="center"><div class="nowrap" style="width:90px; text-align:left;" title="'+val[11]+'/'+val[13]+'">'+val[11]+'/'+val[13]+'</div></td>'
										 +  '<td class="left last">'
										 +  '<a href="#" onclick="lfSelected('+i+'); return false;">선택</a> | '
										 +  '<a href="#" onclick="lfShowCenterScreen(\'<?=$gDomainID;?>\',\'http://<?=$lsUrl;?>/main/login_ok.php\',\''+val[19]+'\',\''+val[20]+'\'); return false;">이동</a>';

									if (!val[5]){
										html += ' | <a href="#" onclick="lfDelete('+i+'); return false;">삭제</a>'
									}

									html += '</td>'
										 +  '</tr>';
								}
							}

							$('#list').html(html);
							$('#tempLodingBar').remove();

							_lfSetPageList(maxCnt,page);
						}
					,	error:function(){
						}
					}).responseXML;
				}else{
					html += '<tr><td class="center last" colspan="20">::검색된 데이타가 없습니다.::</td></tr>';
					$('span[id^="lblPage"]').hide();
					$('#list').html(html);
					$('#tempLodingBar').remove();
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSelected(aiIdx){
		$('#lblLinkCode').text($('#objRow_'+aiIdx).attr('code'));
		$('#lblLinkName').text($('#objRow_'+aiIdx).attr('name'));
		$('#cboBranch').val($('#objRow_'+aiIdx).attr('branch'));
		$('#chkHomeCareYn').attr('checked',$('#objRow_'+aiIdx).attr('homeCareYn') == 'Y' ? true : false);
		$('#chkVouNurseYn').attr('checked',$('#objRow_'+aiIdx).attr('vouNurseYn') == 'Y' ? true : false);
		$('#chkVouOldYn').attr('checked',$('#objRow_'+aiIdx).attr('vouOldYn') == 'Y' ? true : false);
		$('#chkVouBabyYn').attr('checked',$('#objRow_'+aiIdx).attr('vouBabyYn') == 'Y' ? true : false);
		$('#chkVouDisYn').attr('checked',$('#objRow_'+aiIdx).attr('vouDisYn') == 'Y' ? true : false);
		$('#chkCareSvcYn').attr('checked',$('#objRow_'+aiIdx).attr('careSvcYn') == 'Y' ? true : false);
		$('#cboCareArea').val($('#objRow_'+aiIdx).attr('careArea')).attr('disabled',$('#chkCareSvcYn').attr('checked') ? false : true);
		$('#cboCareGroup').attr('disabled',$('#chkCareSvcYn').attr('checked') ? false : true);
		$('#chkCareSupportYn').attr('checked',$('#objRow_'+aiIdx).attr('careSupport') == 'Y' ? true : false).attr('disabled',$('#chkCareSvcYn').attr('checked') ? false : true);
		$('#chkCareResourceYn').attr('checked',$('#objRow_'+aiIdx).attr('careResource') == 'Y' ? true : false).attr('disabled',$('#chkCareSvcYn').attr('checked') ? false : true);

		$('#txtStartDt').val($('#objRow_'+aiIdx).attr('startDt'));
		$('#txtContDt').val($('#objRow_'+aiIdx).attr('contDt'));
		$('#txtFromDt').val($('#objRow_'+aiIdx).attr('fromDt'));
		$('#txtToDt').val($('#objRow_'+aiIdx).attr('toDt'));
		$('#txtCMSCd').val($('#objRow_'+aiIdx).attr('cmsCd'));
		$('#txtOther').val($('#objRow_'+aiIdx).attr('other'));

		$('#chkCostHoldYn').attr('checked',$('#objRow_'+aiIdx).attr('holdYn') == 'Y' ? true : false);
		$('#txtBasicCost').val($('#objRow_'+aiIdx).attr('basicCost'));
		$('#txtClientCnt').val($('#objRow_'+aiIdx).attr('clientCnt'));
		$('#txtClientCost').val($('#objRow_'+aiIdx).attr('clientCost'));

		lfFindManager($('#cboManager'),$('#objRow_'+aiIdx).attr('branch'),$('#objRow_'+aiIdx).attr('manager'));

		setTimeout('lfLoasSvcCnt()',1);

		lfSetGroup($('#objRow_'+aiIdx).attr('careGroup'));
	}

	function lfDelete(aiIdx){
		if (!confirm('삭제후 복구가 불가능합니다.\n\n정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type :'POST'
		,	url  :'./delete.php'
		,	data :{
				'mode'	:'<?=$type;?>'
			,	'code'	:$('#objRow_'+aiIdx).attr('code')
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					lfSearch();
				}else if (result == 9){
					alert('오류가 발생하였습니다.\n잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfShowCenterScreen(did, url, id, pw){
		var tgt = 'WINDOW_CENTER_'+did;
		var win = window.open('about:blank',tgt);
		var frm = document.createElement('form');

		frm.appendChild(__create_input('loc', 'admin'));
		frm.appendChild(__create_input('uCode', id));
		frm.appendChild(__create_input('uPass', pw));
		frm.setAttribute('method', 'post');

		document.body.appendChild(frm);

		frm.target = tgt;
		frm.action = url;
		frm.submit();
	}

	function lfMoveYear(pos){
		$('#lblYear').text(parseInt($('#lblYear').text()) + pos);

		lfLoasSvcCnt();
	}

	function lfLoasSvcCnt(){
		var year	= $('#lblYear').text();
		var code	= $('#lblLinkCode').text();

		for(var i=1; i<=12; i++){
			$('#svcCnt_'+i).text('0');
		}

		$.ajax({
			type :'POST'
		,	url  :'./search.php'
		,	data :{
				'mode'	:'<?=$type;?>_SVCCNT'
			,	'year'	:year
			,	'code'	:code
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				var col	= data.split(String.fromCharCode(2));

				for(var i=1; i<col.length; i++){
					if (col[i]){
						$('#svcCnt_'+i).text(__num2str(col[i]));
					}
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSetGroup(val){
		if (!val) val = '';

		$.ajax({
			type :'POST'
		,	url  :'./find_group.php'
		,	data :{
				'area':$('#cboCareArea option:selected').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#cboCareGroup').html(html);

				if (val){
					$('#cboCareGroup').val(val);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	//엑셀출력
	function acct_excel(){
		var f = document.f;

		f.contYn.value = $('#cboNoCont').attr('checked') == true ? 'N' : 'Y';
		f.cmsYn.value = $('#cboNoCMS').attr('checked') == true ? 'N' : 'Y';

		f.action = "./center_manage_excel.php";
		f.submit();
	}

</script>
<form name="f" method="post">
<input id="contYn"name="contYn" type="hidden" value="">
<input id="cmsYn" name="cmsYn" type="hidden" value="">
<div class="title title_border">기관/지사연결</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="80px">
		<col width="60px">
		<col width="200px">
		<col width="25px">
		<col width="50px">
		<col width="50px">
		<col width="60px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="left">기관코드</th>
			<td class="left" id="lblLinkCode"></td>
			<th class="left">기관명</th>
			<td class="left" id="lblLinkName"></td>
			<td class="center" style="padding-left:3px;"><span class="btn_pack find" onclick="lfFindCenter();"></span></td>
			<th class="left">지사명</th>
			<td class="">
				<select id="cboBranch" name="cbo" style="width:auto;" onChange="lfFindManager($('#cboManager'),this.value);"><?
				if ($lbAdmin == 'A'){?>
					<option value="">--</option><?
				}

				$sql = 'SELECT	b00_code	AS code
						,		b00_name	AS name
						,		b00_manager	AS manager
						FROM	b00branch
						WHERE	b00_domain = \''.$gDomain.'\'';

				if ($lbAdmin == 'B'){
					$sql .= '	AND	b00_code	= \''.$_SESSION['userBranchCode'].'\'';
				}

				$sql .= '	ORDER	BY	name';
				$conn->query($sql);
				$conn->fetch();
				$rowCount = $conn->row_count();

				for($i=0; $i<$rowCount; $i++){
					$row = $conn->select_row($i);
					$selected = '';

					if ($lbAdmin == 'A'){
						if ($row['code'] == 'GE01'){
							$selected = 'selected';
						}
					}?>
					<option value="<?=$row['code'];?>" <?=$selected;?>><?=$row['name'].'['.$row['manager'].']';?></option><?
				}
				$conn->row_free();?>
				</select>
			</td>
			<th class="left">담당자명</th>
			<td class="last">
				<select id="cboManager" name="cbo" style="width:auto;"></select>
			</td>
		</tr>
		<tr>
			<th class="left">이용서비스</th>
			<td class="left last" colspan="8">
				<input id="chkHomeCareYn" name="chkSvc" type="checkbox" value="Y" class="checkbox" checked><label for="chkHomeCareYn">장기요양</label><br>

				<input id="chkVouNurseYn" name="chkSvc" type="checkbox" value="Y" class="checkbox"><label for="chkVouNurseYn">가산간병</label>
				<input id="chkVouOldYn" name="chkSvc" type="checkbox" value="Y" class="checkbox"><label for="chkVouOldYn">노인돌봄</label>
				<input id="chkVouBabyYn" name="chkSvc" type="checkbox" value="Y" class="checkbox"><label for="chkVouBabyYn">산모신생아</label>
				<input id="chkVouDisYn" name="chkSvc" type="checkbox" value="Y" class="checkbox"><label for="chkVouDisYn">장애인활동지원</label><br>

				<input id="chkCareSvcYn" name="chkSvc" type="checkbox" value="Y" class="checkbox"><label for="chkCareSvcYn">재가관리(한재협)</label>
				<select id="cboCareArea" name="cbo" style="width:auto; margin-right:0;" onchange="lfSetGroup();" disabled="true"><?
					$sql = 'SELECT	area_cd,area_nm
							FROM	care_area';

					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);?>
						<option value="<?=$row['area_cd'];?>"><?=$row['area_nm'];?></option><?
					}

					$conn->row_free();?>
				</select>
				<select id="cboCareGroup" name="cbo" style="width:auto; margin-left:0;" disabled="true">
				</select>
			</td>
		</tr>
		<tr>
			<th class="left">재가관리</th>
			<td class="left last" colspan="8">
				<input id="chkCareSupportYn" name="chkCare" type="checkbox" value="Y" class="checkbox" disabled="true"><label for="chkCareSupportYn">재가지원</label>
				<input id="chkCareResourceYn" name="chkCare" type="checkbox" value="Y" class="checkbox" disabled="true"><label for="chkCareResourceYn">재가자원연계</label>
			</td>
		</tr>
		<tr>
			<th class="left">시작일자</th>
			<td class=""><input id="txtStartDt" name="txt" type="text" value="<?=$today;?>" class="date"></td>
			<th class="left">적용기간</th>
			<td class=""><input id="txtFromDt" name="txt" type="text" value="<?=$today;?>" class="date"> ~ <input id="txtToDt" name="txt" type="text" value="9999-12-31" class="date"></td>
			<th class="center" rowspan="2">비<br>고</th>
			<td class="center" rowspan="2" colspan="3"><textarea id="txtOther" name="mtxt" style="width:100%; height:45px;"></textarea></td>
			<td class="left last" rowspan="2">
				<span class="btn_pack m"><button type="button" onclick="lfLinkCenter();">저장</button></span>
			</td>
		</tr><?
		if ($lbAdmin == 'A'){?>
			<tr>
				<th class="left">계약일자</th>
				<td class=""><input id="txtContDt" name="txt" type="text" class="date"></td>
				<th class="left">CMS코드</th>
				<td class=""><input id="txtCMSCd" name="txt" type="text" style="width:70px;" maxlength="8"></td>
			</tr>
			<tr>
				<td class="bottom last" colspan="9">
					<table class="my_table" style="width:100%;">
						<colgroup>
							<col width="70px">
							<col width="85px">
							<col width="60px">
							<col width="60px">
							<col width="70px">
							<col width="60px">
							<col width="60px">
							<col>
						</colgroup>
						<tbody>
							<tr>
								<th class="left">과금</th>
								<td class=""><input id="chkCostHoldYn" name="chk" type="checkbox" class="checkbox"><label for="chkCostHoldYn">정액여부</label></td>
								<th class="left">기본요금</th>
								<td class=""><input id="txtBasicCost" name="txt" type="text" value="30,000" class="number" style="width:60px;"></td>
								<th class="left">제한고객수</th>
								<td class=""><input id="txtClientCnt" name="txt" type="text" value="30" class="number" style="width:60px;"></td>
								<th class="left">초과단가</th>
								<td class="last"><input id="txtClientCost" name="txt" type="text" value="500" class="number" style="width:60px;"></td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr><?
		}else if ($lbAdmin == 'B'){?>
			<tr>
				<th class="left">계약일자</th>
				<td class=""><input id="txtContDt" name="txt" type="text" class="date"></td>
				<td class="" colspan="2"></td>
			</tr><?
		}?>
		<tr>
			<td class="bottom last" colspan="9">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="70px">
						<col width="50px" span="12">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="left">이용현황</th><?
							for($i=1; $i<=12; $i++){?>
								<th class="center"><?=$i;?>월</th><?
							}?>
							<th class="center last">비고</th>
						</tr>
						<tr>
							<td class="center">
								<div style="float:left; width:auto; padding-top:2px; padding-left:3px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="lfMoveYear(-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
								<div style="float:left; width:auto; padding-left:2px; padding-right:2px; font-weight:bold;" id="lblYear"><?=$year?></div>
								<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="lfMoveYear(1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
							</td><?
							for($i=1; $i<=12; $i++){?>
								<td class="right" id="svcCnt_<?=$i;?>">0</td><?
							}?>
							<td class="center last"></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>

<div class="title title_border">관리 기관리스트</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="80px">
		<col width="50px">
		<col width="110px">
		<col width="40px">
		<col width="80px">
		<col width="60px">
		<col width="80px">
		<col width="30px">
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="left">기관코드</th>
			<td class=""><input id="txtFindCode" name="txtFindCode" type="text" style="width:100%;"></td>
			<th class="left">기관명</th>
			<td class=""><input id="txtFindName" name="txtFindName" type="text" style="width:100%;" onkeydown="if(event.keyCode == 13){lfSearch();}"></td>
			<th class="left">대표자</th>
			<td class=""><input id="txtManager" name="txtManager" type="text" style="width:100%;" onkeydown="if(event.keyCode == 13){lfSearch();}"></td>
			<th class="left">CMS코드</th>
			<td class=""><input id="txtCMSCode" name="txtCMSCode" type="text" style="width:100%;" onkeydown="if(event.keyCode == 13){lfSearch();}"></td>
			<th class="left">지사</th>
			<td class="">
				<select id="cboFindBranch" name="cbo" style="width:auto;"><?
				if ($lbAdmin == 'A'){?>
					<option value="">--</option><?
				}

				$sql = 'SELECT	b00_code AS code
						,		b00_name AS name
						,		b00_manager AS manager
						FROM	b00branch
						WHERE	b00_domain	= \''.$gDomain.'\'';

				if ($lbAdmin == 'B'){
					$sql .= '	AND	b00_code	= \''.$_SESSION['userBranchCode'].'\'';
				}

				$sql .= '
						ORDER	BY	name';
				$conn->query($sql);
				$conn->fetch();
				$rowCount = $conn->row_count();

				for($i=0; $i<$rowCount; $i++){
					$row = $conn->select_row($i);?>
					<option value="<?=$row['code'];?>"><?=$row['name'].'['.$row['manager'].']';?></option><?
				}
				$conn->row_free();?>
				</select>
			</td>
			<td class="left last"><span class="btn_pack m"><button type="button" onclick="lfSearch();">조회</button></span><span class="btn_pack m" style="margin-left:2px;"><button type="button" onFocus="this.blur();" onClick="acct_excel();">엑셀</span></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="177px">
		<col width="60px">
		<col width="50px">
		<col width="40px">
		<col width="20px">
		<col width="70px">
		<col width="20px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="left">계약기간</th>
			<td class=""><input id="txtContFrom" name="txtContFrom" type="text" value="" class="date"> ~ <input id="txtContTo" name="txtContTo" type="text" value="" class="date"></td>
			<th class="left">적용일자</th>
			<td class=""><input id="applyDt" name="applyDt" type="text" class="date"></td>
			<th class="">미계약</th>
			<td class=""><input id="cboNoCont" name="cboNoCont" type="checkbox" class="checkbox"></td>
			<th class="">CMS미등록</th>
			<td class=""><input id="cboNoCMS" name="cboNoCMS" type="checkbox" class="checkbox"></td>
			<td class="last"></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="80px">
		<col width="120px">
		<col width="60px">
		<col width="130px">
		<col width="65px">
		<col width="60px">
		<col width="60px">
		<col width="100px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">기관코드</th>
			<th class="head">기관명</th>
			<th class="head">대표자</th>
			<th class="head">적용기간</th>
			<th class="head">계약일자</th>
			<th class="head">CMS코드</th>
			<th class="head">과금</th>
			<th class="head">지사</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="list"></tbody>
	<tfoot>
		<tr>
			<td class="center bottom last" colspan="20"><? include_once('../inc/_page_script.php');?></td>
		</tr>
	</tfoot>
</table>
</form>
<?
	include_once('../inc/_db_close.php');
?>