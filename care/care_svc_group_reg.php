<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$title = lfGetSPName($sr);

	if (!$title) exit;

	$orgNo	= $_SESSION['userCenterCode'];
	$sugaCd = $_GET['suga'];
	$seq	= $_GET['seq'];

	if ($sugaCd){
		$sql = 'SELECT	suga_nm
				FROM	care_suga
				WHERE	org_no	= \''.$orgNo.'\'
				AND		suga_sr	= \''.$sr.'\'
				AND		CONCAT(suga_cd,suga_sub) = \''.$sugaCd.'\'';

		$sugaNm = $conn->get_data($sql);

		if ($IsCareYoyAddon){
			//공통수가
			if (!$sugaNm){
				$sql = 'SELECT	name
						FROM	care_suga_comm
						WHERE	code = \''.$sugaCd.'\'';

				$sugaNm	= $conn->get_data($sql);
			}
		}
	}

	if (!$sugaNm){
		 $sugaCd = '';
		 $seq = '';
	}

	$sql = 'SELECT	*
			FROM	care_svc_group
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$sr.'\'
			AND		suga_cd	= \''.$sugaCd.'\'
			AND		seq		= \''.$seq.'\'';

	$row = $conn->get_array($sql);

	$grpNm = $row['group_nm'];
	$target = Explode('/',$row['target']);
	$resCd = $row['res_cd'];
	$memCd = $row['mem_cd'];
	$category = $row['category'];

	Unset($row);

	//담당자명
	$sql = 'SELECT	DISTInCT m02_yname
			FROM	m02yoyangsa
			WHERE	m02_ccode  = \''.$orgNo.'\'
			AND		m02_yjumin = \''.$memCd.'\'';

	$memNm = $conn->get_data($sql);

	//자원명
	$sql = 'SELECT	cust_nm
			FROM	care_cust
			WHERE	org_no	= \''.$orgNo.'\'
			AND		cust_cd	= \''.$resCd.'\'';

	$resNm = $conn->get_data($sql);

	if (is_array($target)){
		$t = $target;
		$target = Array();
		foreach($t as $s => $v){
			$target[$v] = $v;
		}
	}
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:checkbox[name="chkTG"]').unbind('click').bind('click',function(){
			var id = $(this).attr('id');
			var val = $(this).attr('checked');

			$('input:checkbox[id^="'+id+'"][show_flag="Y"]').attr('checked',val);

			lfChkInit();
		});

		$('input:checkbox[name="chkTG"][gbn="P"]').unbind('click').bind('click',function(){
			lfSetChk(this);
		});

		lfChkInit();
		lfCategoryFullname();
	});

	function lfChkInit(){
		$('input:checkbox[name="chkTG"][gbn="P"]').each(function(){
			lfSetChk(this);
		});
	}

	function lfSetChk(This){
		var obj = $(This).parent();
		var clr = '';

		if (This.checked){
			clr = 'EAEAEA';
		}else{
			clr = 'FFFFFF';
		}

		$(obj).css('background-color','#'+clr);
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
		$('#lblSvcCd').attr('code',cd).text(subNm);
		$('#txtGroupNm').val(subNm);
	}

	function lfResourceFind(){
		var h = 400;
		var w = 600;
		var t = (screen.availHeight - h) / 2;
		var l = (screen.availWidth - w) / 2;

		var url = '../care/care_resource_find.php';
		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
		var win = window.open('about:blank', 'FIND_RESOURCE', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'sugaCd':$('#lblSvcCd').attr('code')
			,	'resCd'	:$('#lblResCd').attr('code')
			,	'sr'	:$('#SR').val()
			,	'return':'lfResourceFindResult'
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

		form.setAttribute('target', 'FIND_RESOURCE');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfResourceFindResult(cd,cost,nm,per){
		if (!cd){
			alert('자원코드가 없습니다.\n관리자에게 문의하여 주십시오.');
			return;
		}
		$('#lblResCd').attr('code',cd).text(nm);
	}

	function lfMemberFind(){
		var h = 400;
		var w = 600;
		var t = (screen.availHeight - h) / 2;
		var l = (screen.availWidth - w) / 2;

		var url    = '../inc/_find_person.php';
		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
		var win    = window.open('about:blank', 'FIND_MEMBER', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'type'	:'member'
			,	'kind'	:'<?=$sr;?>'
			,	'return':'lfMemFindResult'
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

	function lfMemFindResult(obj){
		var col = __parseStr(obj);

		$('#lblMemCd').attr('code',col['jumin']).text(col['name']);
	}

	function lfSave(){
		if (!$('#lblSvcCd').attr('code')){
			alert('서비스를 선택하여 주십시오.');
			lfCareSvcFind();
			return;
		}

		if (!$('#lblResCd').attr('code')){
			alert('자원을 선택하여 주십시오.');
			lfResourceFind();
			return;
		}

		if (!$('#txtGroupNm').val()){
			alert('묶음명을 입력하여 주십시오.');
			$('#txtGroupNm').focus();
			return;
		}

		var cnt = $('input:checkbox[name="chkTG"][gbn="P"]:checked').length;

		if (cnt == 0){
			alert('대상자를 1명이상 선택하여 주십시오.');
			return;
		}

		var data = {};

		data['SR']		= $('#SR').val();
		data['seq']		= $('#seq').val();
		data['resCd']	= $('#lblResCd').attr('code');
		data['sugaCd']	= $('#lblSvcCd').attr('code');
		data['memCd']	= $('#lblMemCd').attr('code');
		data['groupNm']	= $('#txtGroupNm').val();
		data['category']= $('#ID_CATEGORY').attr('category');
		data['target']	= '';

		if (!data['category']) data['category'] = '';

		$('input:checkbox[name="chkTG"][gbn="P"]:checked').each(function(){
			if (data['target']) data['target'] += '/';

			data['target'] += $(this).val();
		});

		$.ajax({
			type:'POST'
		,	url:'../care/care_svc_group_reg_save.php'
		,	data:data
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
				}else if (result == 9){
					alert('데이타 처리 중 오류가 발생하였습니다.\n잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfList(){
		location.href = './care.php?sr=<?=$sr;?>&type=SVC_GROUP_LIST';
	}

	function lfCategoryFind(){
		var objModal = new Object();
		var url = './care_svc_category_find.php';
		var style = 'dialogWidth:800px; dialogHeight:600px; dialogHide:yes; scroll:no; status:no';

		objModal.win = window;
		objModal.SR	 = $('#sr').val();
		objModal.code= '';
		objModal.name= '';

		window.showModalDialog(url, objModal, style);

		if (objModal.code){
			$('#ID_CATEGORY').attr('category',objModal.code).text(objModal.name);
		}
	}

	function lfCategoryFullname(){
		if (!$('#ID_CATEGORY').attr('category')) return;

		$.ajax({
			type :'POST'
		,	url  :'./care_svc_category_fun.php'
		,	data :{
				'SR'	:$('#sr').val()
			,	'gbn'	:'FULLNAME'
			,	'code'	:$('#ID_CATEGORY').attr('category')
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				$('#ID_CATEGORY').text(data);
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfLoadList(){
		var width = 800;
		var height = 600;
		var left = (screen.availWidth - width) / 2;
		var top = (screen.availHeight - height) / 2;

		var option = 'left='+left+', top='+top+', width='+width+',height='+height+',scrollbars=no,status=no,resizable=no';
		var win = window.open('about:blank', 'FIND_MEMBER', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'SR':$('#SR').val()
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
		form.setAttribute('action', './care_svc_group_list.php');

		document.body.appendChild(form);

		form.submit();
	}

	function lfSetGroup(sugaCd,seq,name){
		$.ajax({
			type:'POST'
		,	url:'./care_svc_group_find.php'
		,	data:{
				'SR':$('#SR').val()
			,	'sugaCd':sugaCd
			,	'seq':seq
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var col = __parseVal(data);

				$('#lblSvcCd').attr('code',sugaCd).text(name);
				$('#lblResCd').attr('code',col['resCd']).text(col['resNm']);
				$('#lblMemCd').attr('code',col['memCd']).text(col['memNm']);
				$('#txtGroupNm').val(col['grpNm']);
				$('#ID_CATEGORY').attr('category',col['cateCd']).text(col['cateNm']);

				$('input:checkbox[name="chkTG"]').attr('checked',false);

				var target = col['target'].split('/');

				for(var i=0; i<target.length; i++){
					$('#chk_'+target[i]).attr('checked',true);
				}

				$('#tempLodingBar').remove();
			}
		,	error:function(error){
				alert('err');
			}
		}).responseXML;
	}

	function lfFindPns(str){
		$('#obj_body :checkbox').each(function(){
			if (!$(this).attr('checked')){
				$(this).attr('show_flag', 'N');
				$(__GetTagObject(this, 'DIV')).hide();
			}
		});

		//$('#obj_body div').hide();
		//$('#obj_body :checkbox').attr('show_flag', 'N');
		$('span[id="pns"]').each(function(){
			var txt = $(this).text(), obj;

			if (txt.indexOf(str) != -1){
				obj = __GetTagObject(this, 'DIV');

				$(':checkbox[id^="chk_"]', obj).attr('show_flag', 'Y');
				$(obj).show();
			}
		});
	}
</script>
<div class="title title_border">
	<div style="float:left; width:auto;">서비스 묶음등록(<?=$title;?>)</div>
	<div style="float:right; width:auto; padding-top:10px;">
		<span class="btn_pack m"><button type="button" class="bold" onclick="lfLoadList();">기존 서비스묶음 불러오기</button></span>
		<span class="btn_pack m"><span class="list"></span><button type="button" class="bold" onclick="lfList();">리스트</button></span>
		<span class="btn_pack m"><span class="save"></span><button type="button" class="bold" onclick="lfSave();">저장</button></span>
	</div>
</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="10px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">서비스</th>
			<td class="left last" style="padding-top:1px;"><?=(!$sugaCd ? '<span class="btn_pack find" onclick="lfCareSvcFind();">' : '&nbsp;');?></span></td>
			<td class="left last" id="lblSvcCd" code="<?=$sugaCd;?>"><?=$sugaNm;?></td>
		</tr>
		<tr>
			<th class="center">자원선택</th>
			<td class="left last" style="padding-top:1px;"><?=(!$sugaCd ? '<span class="btn_pack find" onclick="lfResourceFind();">' : '&nbsp;');?></span></td>
			<td class="left last" id="lblResCd" code="<?=$resCd;?>"><?=$resNm;?></td>
		</tr>
		<tr>
			<th class="center">담당직원</th>
			<td class="left last" style="padding-top:1px;"><span class="btn_pack find" onclick="lfMemberFind();"></span></td>
			<td class="left last" id="lblMemCd" code="<?=$ed->en($memCd);?>"><?=$memNm;?></td>
		</tr>
		<tr>
			<th class="center">묶음명</th>
			<td class="last" colspan="2"><input id="txtGroupNm" name="txt" type="text" style="width:200px;" value="<?=$grpNm;?>"></td>
		</tr>
		<tr>
			<th class="head">카테고리</th>
			<td id="ID_CATEGORY" class="left last" colspan="2" onmouseover="this.style.backgroundColor='#EAEAEA';" onmouseout="this.style.backgroundColor='#FFFFFF';" onclick="lfCategoryFind();" category="<?=$category;?>"></td>
		</tr>
		<tr>
			<th class="center">대상자</th>
			<td class="last" colspan="2"><?
				$cut = 4;?>
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="<?=100/$cut;?>%" span="<?=$cut;?>">
					</colgroup>
					<tbody>
						<tr>
							<th class="left last" colspan="<?=$cut;?>">
								성명 :<input id="find_pns" type="text" value="" style="width:70px; margin-right:0;">
								<span class="btn_pack small"><button onclick="lfFindPns($('#find_pns').val());">&nbsp;조회&nbsp;</button></span>
							</th>
						</tr>
						<tr>
							<th class="left last" colspan="<?=$cut;?>">
								<label style="cursor:default;">
									<input id="chk" name="chkTG" type="checkbox" class="checkbox" style="margin:0;" value=""><span class="bold">전체</span>
								</label>
							</th>
						</tr>
						<tr>
							<td id="obj_body" class="left bottom last" style="border-top:1px dashed #FFFFFF;"><?
								$sql = 'SELECT	b.jumin, m03_name AS name, m03_key AS cd
										,		LEFT(m03_name, 1) AS cho
										,		CASE WHEN DATE_FORMAT(NOW(), \'%Y-%m-%d\') BETWEEN a.from_dt AND a.to_dt THEN \'Y\' ELSE \'N\' END AS now_flag
										FROM	client_his_svc AS a
										INNER	JOIN	m03sugupja
												ON		m03_ccode = a.org_no
												AND		m03_mkind = \'6\'
												AND		m03_jumin = a.jumin
										LEFT    JOIN    mst_jumin as b
												ON      b.org_no = a.org_no
												AND     b.code = m03_jumin
										WHERE	a.org_no = \''.$orgNo.'\'
										AND		a.svc_cd = \''.$sr.'\'
										AND		DATE_FORMAT(NOW(), \'%Y-%m-%d\') BETWEEN a.from_dt AND a.to_dt
										ORDER	BY CASE WHEN cho >= \'가\' THEN 1 ELSE 2 END, name
										';
								$conn->query($sql);
								$conn->fetch();

								$rowCnt = $conn->row_count();
								$idx = 0;

								for($i=0; $i<$rowCnt; $i++){
									$row = $conn->select_row($i);

									$birthDay	= $myF->issToBirthDay($row['jumin'],'.');
									$gender		= $myF->issToGender($row['jumin']);
									$jumin		= $ed->en($row['jumin']);?>
									<div style="float:left; width:25%; display:;">
										<label style="cursor:default; margin-left:15px;">
											<input id="chk_<?=$row['cd'];?>" name="chkTG" type="checkbox" class="checkbox" gbn="P" show_flag="Y" style="margin:0;" value="<?=$row['cd'];?>" <?=($target[$row['cd']] == $row['cd'] ? 'checked' : '');?>>
											<span id="pns" class="bold nowrap" style="width:50px;"><?=$row['name'];?></span>
											<span class="nowrap" style="width:60px;"><?=$birthDay;?></span>
											<span class="nowrap" style="width:20px; color:#<?=($gender == '남' ? '0000FF' : 'FF0000');?>;"><?=$gender;?></span>
										</label>
									</div><?
								}

								$conn->row_free();?>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
<input id="seq" type="hidden" value="<?=$seq;?>">
<input id="SR" type="hidden" value="<?=$sr;?>">
<?
	include_once('../inc/_db_close.php');
?>