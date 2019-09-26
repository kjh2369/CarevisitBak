<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$title = lfGetSPName($sr);

	if (!$title) exit;

	$code	= $_SESSION['userCenterCode'];
	$cGiho	= $_SESSION['userCenterGiho'];
	$cName	= $_SESSION['userCenterName'];

	$name	= $_POST['txtName'];
	$telno	= $_POST['txtTelno'];
	$addr	= $_POST['txtAddr'];
	$grdNm	= $_POST['txtGrdNm'];

	$page	= $_POST['page'];
	$jumin	= $ed->de($_POST['jumin']);
	$today	= Date('Y-m-d');

	$sql = 'SELECT	jumin
			FROM	mst_jumin
			WHERE	org_no	= \''.$code.'\'
			AND		gbn		= \'1\'
			AND		code	= \''.$jumin.'\'';

	$strJumin = $conn->get_data($sql);

	if ($jumin){
		$lbNew = false;
	}else{
		$lbNew = true;
	}

	if (!$lbNew){
		$sql = 'SELECT	seq
				,		svc_stat
				,		from_dt
				,		to_dt
				FROM	client_his_svc
				WHERE	org_no	 = \''.$code.'\'
				AND		jumin	 = \''.$jumin.'\'
				AND		svc_cd	 = \''.$sr.'\'
				AND		from_dt <= \''.$today.'\'
				AND		to_dt	>= \''.$today.'\'';

		$svcHis = $conn->get_array($sql);
	}

	//기본정보
	$sql = 'SELECT	DISTINCT
					m03_name AS name
			,		m03_key AS fix_no
			,		m03_tel AS phone
			,		m03_hp AS mobile
			,		m03_post_no AS postno
			,		m03_juso1 AS addr
			,		m03_juso2 AS addr_dtl
			,		m03_yboho_name AS grd_nm
			,		m03_yoyangsa4_nm AS grd_addr
			,		m03_yboho_phone AS grd_tel
			,		m03_yoyangsa1 AS mem_cd
			,		SUBSTR(m03_yoyangsa5_nm,1,1) AS marry_gbn
			,		SUBSTR(m03_yoyangsa5_nm,2,1) AS cohabit_gbn
			,		SUBSTR(m03_yoyangsa5_nm,3,2) AS edu_gbn
			,		SUBSTR(m03_yoyangsa5_nm,5,1) AS rel_gbn
			FROM	m03sugupja
			WHERE	m03_ccode = \''.$code.'\'
			AND		m03_jumin = \''.$jumin.'\'';

	$basic = $conn->get_array($sql);

	if (!$lbNew){
		$key = $basic['fix_no'];
		$basic['fix_no'] = $myF->fixNo($key);

		//이용정보
		$sql = 'SELECT	seq
				,		from_dt
				,		to_dt
				,		svc_stat
				,		svc_reason
				,		stop_reason
				,		mp_gbn
				FROM	client_his_svc
				WHERE	org_no	 = \''.$code.'\'
				AND		jumin	 = \''.$jumin.'\'
				AND		svc_cd	 = \''.$sr.'\'
				AND		from_dt	<= \''.$today.'\'
				AND		to_dt	>= \''.$today.'\'';

		$cont = $conn->get_array($sql);

		if ($code == '201308006'){
		#	echo nl2br($sql);
		}

		if (!$cont){
			$sql = 'SELECT	seq
					,		from_dt
					,		to_dt
					,		svc_stat
					,		svc_reason
					,		stop_reason
					,		mp_gbn
					FROM	client_his_svc
					WHERE	org_no	 = \''.$code.'\'
					AND		jumin	 = \''.$jumin.'\'
					AND		svc_cd	 = \''.$sr.'\'
					ORDER	BY seq DESC
					LIMIT	1';

			$cont = $conn->get_array($sql);
		}

		//그외 정보
		$sql = 'SELECT	care_cost
				,		care_org_no
				,		care_org_nm
				,		care_no
				,		care_lvl
				,		care_gbn
				,		care_pic_nm
				,		care_telno
				FROM	client_his_care
				WHERE	org_no	= \''.$code.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		svc_cd	= \''.$sr.'\'
				ORDER	BY seq DESC
				LIMIT	1';

		$other = $conn->get_array($sql);

		//서비스정보
		$sql = 'SELECT	*
				FROM	care_svc_his
				WHERE	org_no	= \''.$code.'\'
				AND		jumin	= \''.$jumin.'\'
				ORDER	BY from_dt';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();?>


		<script type="text/javascript">
			var hisSvc = {};
		</script><?

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);
			$seq = $row['seq'];
			$svcCd = $row['svc_cd'];
			$fromDt = $row['from_dt'];
			$toDt = $row['to_dt'];
			$person = $row['person_nm'];
			$telno = $myF->phoneStyle($row['telno']);?>
			<script type="text/javascript">
				hisSvc['<?=$seq;?>'] = {'svcCd':'<?=$svcCd;?>','from':'<?=$fromDt;?>','to':'<?=$toDt;?>','person':'<?=$person;?>','telno':'<?=$telno;?>'};
			</script><?
		}

		$conn->row_free();
	}
?>
<script type="text/javascript" src="../sugupja/client.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		//주민번호
		$('input:text[name="txtJumin"]').unbind('keyup').bind('keyup',function(){
			var jumin = $('#txtJumin1').val()+$('#txtJumin2').val();

			if ($('#new').val() != '1') return false;

			if (jumin.length == 13){
				$.ajax({
					type: 'POST'
				,	aysnc:false
				,	url : './care_find.php'
				,	data: {
						'type':'FIND_CLIENT'
					,	'jumin':jumin
					}
				,	beforeSend: function (){
					}
				,	success: function(data){
						var col = __parseStr(data);

						if (col['isYn'] == 'N'){
							if ($('#linkSeq').val()) return false;

							$('#txtFixNo').text(col['fixNo']);
							$('#txtName').val(col['name']);
							$('#txtPhone').val(col['phone']);
							$('#txtMobile').val(col['mobile']);

							$('#txtPostno1').val(col['postno']);
							$('#txtPostno2').val(col['postno']);
							$('#txtAddr').val(col['addr']);
							$('#txtAddrDtl').val(col['addrDtl']);

							$('#txtGuardNm').val(col['guardNm']);
							$('#txtGuardAddr').val(col['guardAddr']);
							$('#txtGuardTel').val(col['guardTel']);

							$('#cboMarry').val(col['marry']);
							$('#cboCohabit').val(col['cohabit']);

							$('#cboEdu').val(col['edu']);
							$('#cboRel').val(col['rel']);

							if (col['appNo'] || col['lvl'] || col['kind']){
								$('#txtOrgNo').val('<?=$cGiho;?>');
								$('#txtOrgNm').val('<?=$cName;?>');
								$('#txtAppNo').val(col['appNo']);
								$('#cboLvl').val(col['lvl']);
								$('#cboGbn').val(col['kind']);
							}

							if (col['yoyNm'] || col['yoyTel']){
								$('#txtPerNm').val(col['yoyNm']);
								$('#txtPerTel').val(col['yoyTel']);
							}

							$('#txtName').focus();
						}else{
							if (confirm('입력하신 주민번호는 이미 등록된 대상자입니다.\n수정화면으로 이동하시겠습니까?')){
								$('#jumin').val(col['jumin']);

								var f = document.f;

								f.action = '../care/care.php?sr=<?=$sr;?>&type=82';
								f.submit();
							}else{
								$('input:text[name="txtJumin"]').val('');
								$('#txtJumin1').focus();
							}
						}
					}
				,	complite: function(result){
					}
				,	error: function (){
					}
				}).responseXML;
			}else if ($(this).val().length == $(this).attr('maxlength')){
				if ($(this).attr('id') == 'txtJumin1'){
					$('#txtJumin2').focus();
				}
			}
		});

		if ($('#new') != '1'){
			try{
				$(function(){
					$.each(hisSvc, function(key, val){
						lfSvcAdd(key,val['svcCd'],val['from'],val['to'],val['person'],val['telno']);
					});
				});
			}catch(e){
			}
		}
	});

	function lfSearch(){
		var f = document.f;

		var parm = new Array();
			parm = {
				'txtName':'<?=$name;?>'
			,	'txtTelno':'<?=$telno;?>'
			,	'txtAddr':'<?=$addr;?>'
			,	'txtGrdNm':'<?=$grdNm;?>'
			};

		var objs;
		for(var key in parm){
			objs = document.createElement('input');
			objs.setAttribute('type', 'hidden');
			objs.setAttribute('name', key);
			objs.setAttribute('value', parm[key]);

			f.appendChild(objs);
		}

		f.action = '../care/care.php?sr=<?=$sr;?>&page=<?=$page;?>&type=81';
		f.submit();
	}

	function lfSave(jumin){
		if (!jumin) jumin = $('#txtJumin1').val()+$('#txtJumin2').val();
		if ($('#new').val() == '1'){
			if ($('#txtJumin1').val().length == 6 && $('#txtJumin2').val().length >= 1){
			}else{
				alert('주민번호 입력오류입니다. 확인하여 주십시오.');
				if ($('#txtJumin1').val().length != 6){
					$('#txtJumin1').focus();
				}else if ($('#txtJumin2').val().length < 1){
					$('#txtJumin2').focus();
				}
				return;
			}
		}

		if (!$('#txtName').val()){
			alert('성명을 입력하여 주십시오.');
			$('#txtName').focus();
			return;
		}

		if (!$('#txtFrom_<?=$sr;?>').text() || !$('#txtTo_<?=$sr;?>').text()){
			alert('이용기간입력 오류입니다. 확인하여 주십시오.');
			return;
		}

		var his = '';
		var duplicate = false; //중복여부

		$('tr[id!="dummyRow"]', $('#tbodyList')).each(function(){
			var from = $('#txtFrom',this).val();
			var to = $('#txtTo',this).val();

			if (!from || !to){
				alert('이용기간을 입력하여 주십시오.');
				if (!from){
					$('#txtFrom',this).focus();
				}else{
					$('#txtTo',this).focus();
				}
				duplicate = true;
				return false;
			}

			if (from > to){
				alert('이용기간 입력오류입니다. 확인하여 주십시오.');
				$('#txtTo',this).focus();
				return false;
			}

			var This = this;

			$('tr[id!="dummyRow"]', $('#tbodyList')).each(function(){
				if (This != this){
					var tmpF = $('#txtFrom',this).val();
					var tmpT = $('#txtTo',this).val();

					if ((from >= tmpF && from <= tmpT) ||
						(to >= tmpF && to <= tmpT)){
						duplicate = true;
						return false;
					}
				}
			});

			if (duplicate){
				alert('중복된 이용기간이 있습니다. 확인하여 주십시오.');
				$('#txtFrom',this).focus();
				duplicate = true;
				return false;
			}

			his += '?svcCd='+$('#cboSvc',this).val();
			his += '&from='+$('#txtFrom',this).val();
			his += '&to='+$('#txtTo',this).val();
			his += '&person='+$('#txtPerson',this).val();
			his += '&telno='+$('#txtTelno',this).val();
		});

		//중복이면 진행을 중지한다.
		if (duplicate) return;

		if (lfSvcDuplicate(jumin)){
			if (!confirm('중복된 서비스가 있습니다.\n저장하시겠습니까?')) return;
		}

		var data = {};

		data['type'] = 'REG_CLIENT';
		data['txtFrom_<?=$sr;?>']	= $('#txtFrom_<?=$sr;?>').text();
		data['txtTo_<?=$sr;?>']		= $('#txtTo_<?=$sr;?>').text();
		data['txtStat_<?=$sr;?>']	= ($('#txtStat_<?=$sr;?>').text() == '이용' ? '1' : '9');
		data['txtSeq_<?=$sr;?>']	= $('#txtSeq_<?=$sr;?>').text();
		data['linkSeq']				= $('#linkSeq').val();
		data['history']				= his;

		$('input').each(function(){
			data[$(this).attr('id')] = $(this).val();
		});

		$('select').each(function(){
			data[$(this).attr('id')] = $(this).val();
		});

		$.ajax({
			type: 'POST'
		,	url : './care_apply.php'
		,	data: data
		,	beforeSend: function (){
			}
		,	success: function(result){
				if (result.substring(0,2) == 'OK'){
					alert('정상적으로 처리되었습니다.');

					$('#jumin').val(getHttpRequest('../inc/_ed_code.php?type=1&value='+$('#txtJumin1').val()+$('#txtJumin2').val()));

					var f = document.f;

					f.jumin.value = result.substring(3);
					f.action = '../care/care.php?sr=<?=$sr;?>&type=82';
					f.submit();
				}else if (result == '9'){
					alert('데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	complite: function(result){
			}
		,	error: function (){
			}
		}).responseXML;
	}

	function lfDelete(){
		if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시곘습니까?')) return;

		var data = {};

		//data['jumin']	= $('#lblJumin').attr('value');
		data['jumin']	= $('#jumin').val();
		data['sr']		= '<?=$sr;?>';

		if (!data['jumin']) data['jumin'] = $('#txtJumin1').val() + $('#txtJumin2').val();

		$.ajax({
			type: 'POST'
		,	url : './care_client_delete.php'
		,	data: data
		,	beforeSend: function (){
			}
		,	success: function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					location.replace('./care.php?sr=<?=$sr;?>&type=81');
				}else if (result == 9){
					alert('데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else if (result == 'iljung'){
					alert('현재 대상자로 등록된 일정이 있어서 삭제할 수 없습니다.\n확인하여 주십시오.');
					return;
				}else{
					alert(result);
				}
			}
		,	complite: function(result){
			}
		,	error: function (){
			}
		}).responseXML;
	}

	//고객찾기
	function lfFindClient(){
		var val = new Array();
			val['SR'] = '<?=$sr;?>';

		var result = __findClient('<?=$code;?>','',val);

		if (!result){
			return;
		}

		var col = __parseVal(result);
		var jumin = getHttpRequest('../inc/_ed_code.php?type=2&value='+col['jumin']);

		$('#txtJumin1').val(jumin.substring(0,6));
		$('#txtJumin2').val(jumin.substring(6,13)).keyup();
		$('#txtName').val(col['name']);
	}

	//일반접수대상자 찾기
	function lfFindNormal(){
		var objModal = new Object();
		var url = './care_normal_find.php';
		var style = 'dialogWidth:600px; dialogHeight:300px; dialogHide:yes; scroll:no; status:no';

		objModal.sr = '<?=$sr;?>';

		window.showModalDialog(url, objModal, style);

		if (!objModal.data) return false;

		var col = __parseVal(objModal.data);

		$('#txtJumin1').val(col['jumin'].substring(0,6));
		$('#txtJumin2').val(col['jumin'].substring(6,13));
		$('#txtName').val(col['name']);

		$('#txtPostno1').val(col['postno'].substring(0,3));
		$('#txtPostno2').val(col['postno'].substring(3,5));
		$('#txtAddr').val(col['addr']);
		$('#txtAddrDtl').val(col['addrDtl']);

		$('#txtPhone').val(col['phone']);
		$('#txtMobile').val(col['mobile']);

		$('#txtGuardNm').val(col['grdNm']);
		$('#txtGuardAddr').val(col['grdAddr']);
		$('#txtGuardTel').val(col['grdTelno']);

		$('#cboMarry').val(col['marryGbn']);
		$('#cboCohabit').val(col['cohabitGbn']);
		$('#cboEdu').val(col['eduGbn']);
		$('#cboRel').val(col['relGbn']);
		$('#cboLvl').val(col['longLvl']);
		$('#cboGbn').val(col['longGbn']);
		$('#linkSeq').val(col['seq']);
	}

	function lfSvcHis(jumin){
		if (!jumin) jumin = $('#txtJumin1').val()+$('#txtJumin2').val();

		if (!jumin){
			return;
		}

		var width = 800;
		var height = 600;
		var left = (screen.availWidth - width) / 2;
		var top = (screen.availHeight - height) / 2;

		var option = 'left='+left+', top='+top+', width='+width+',height='+height+',scrollbars=no,status=no,resizable=no';
		var url = './care_client_history.php';
		var win = window.open('about:blank', 'CLIENT_HISTORY', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'jumin':jumin
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

		form.setAttribute('target', 'CLIENT_HISTORY');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfSvcAdd(seq,svcCd,from,to,person,telno){
		if (!seq) seq = '-';
		if (!svcCd) svcCd = '';
		if (!from) from = '';
		if (!to) to = '';
		if (!person) person = '';
		if (!telno) telno = '';

		var cnt = $('tr[id!="dummyRow"]', $('#tbodyList')).length;
		var html = '';

		html += '<tr>';
		html += '<td class="center">'+seq+'</td>';
		html += '<td class="center">'
			 +	'<select id="cboSvc" style="width:auto;">'
			 +	'<option value="0" '+(svcCd == '0' ? 'selected' : '')+'>노인장기요양</option>'
			 +	'<option value="1" '+(svcCd == '1' ? 'selected' : '')+'>가사간병</option>'
			 +	'<option value="2" '+(svcCd == '2' ? 'selected' : '')+'>노인돌봄</option>'
			 +	'<option value="4" '+(svcCd == '4' ? 'selected' : '')+'>장애인활동지원</option>'
			 +	'</select>'
			 +	'</td>';
		html += '<td class="center">'
			 +	'<input id="txtFrom" type="text" value="'+from+'" class="date"> ~ '
			 +	'<input id="txtTo" type="text" value="'+to+'" class="date">'
			 +	'</td>';
		html += '<td class="center"><input id="txtPerson" type="text" value="'+person+'" style="width:100%;"></td>'
		html += '<td class="center"><input id="txtTelno" type="text" value="'+telno+'" class="phone"></td>'
		html += '<td class="left">'
			 +	'<span class="btn_pack m" style="margin-top:1px;"><span class="delete"></span><button onclick="lfSvcRemove($(this).parent().parent().parent());">삭제</button></span>'
			 +	'</td>';
		html += '</tr>';

		$('tr[id="dummyRow"]', $('#tbodyList')).hide();
		$('tr:last', $('#tbodyList')).after(html);

		var obj = $('tr:last', $('#tbodyList'));

		$('input:text',obj).each(function(){
			__init_object(this);
		});
	}

	function lfSvcRemove(obj){
		$(obj).remove();

		var cnt = $('tr[id!="dummyRow"]', $('#tbodyList')).length;

		if (cnt == 0){
			$('tr[id="dummyRow"]', $('#tbodyList')).show();
		}
	}

	function lfSvcDuplicate(jumin){
		if (!jumin) jumin = $('#txtJumin1').val()+$('#txtJumin2').val();
		if (!jumin){
			$('#hisChk').val('N');
			return;
		}

		var name = $('#txtName').attr('org');

		if (!name) name = $('#txtName').val();
		if (!name){
			$('#hisChk').val('N');
			return;
		}

		var from = $('#txtFrom_<?=$sr;?>').text().split('.').join('')
		var to = $('#txtTo_<?=$sr;?>').text().split('.').join('');

		if (!from || !to){
			$('#hisChk').val('N');
			return;
		}

		var duplicate = false;

		$.ajax({
			type:'POST'
		,	async:false
		,	url:'./care_svc_duplicate.php'
		,	data:{
				'jumin':jumin
			,	'name':name
			,	'from':from
			,	'to':to
			,	'SR':'<?=$sr;?>'
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				if (html){
					$('#hisChk').val('N');
					$('#divTemp').html(html);
					duplicate = true;
				}
			}
		,	complite:function(result){
			}
		,	error:function(){
			}
		}).responseXML;

		return duplicate;
	}
</script>
<div class="title title_border">대상자<?=($lbNew ? '등록' : '수정');?>(<?=$title;?>)</div>

<div class="my_border_blue" style="margin-top:10px; margin-left:10px;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="70px" span="2">
			<col width="150px">
			<col width="50px" span="2">
			<col width="130px">
			<col width="70px">
			<col width="40px">
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="bold last" colspan="20">1.기본정보(<span class="bold">※성별 판단을 위해서 주민번호 7자리까지는 입력하여 주십시오.</span>)</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th class="head">주민번호</th>
				<td colspan="2">
					<input id="txtJumin1" name="txtJumin" type="text" value="<?=SubStr($strJumin,0,6);?>" maxlength="6" style="width:50px;"> -
					<input id="txtJumin2" name="txtJumin" type="text" value="<?=SubStr($strJumin,6,7);?>" maxlength="7" style="width:55px; margin-right:0;">
					<!--span class="btn_pack small"><button type="button" onclick="lfFindClient();">대상자</button></span--><?
					if ($lbNew){?>
						<span class="btn_pack small"><button type="button" onclick="lfFindNormal();">일반</button></span><?
					}?>
					<!--<span id="lblJumin" class="left bold" value="<?=$ed->en($jumin);?>"><?=$myF->issNo($strJumin);?></span>-->
				</td>
				<th class="head" rowspan="2">연락처</th>
				<th class="head">유선</th>
				<td>
					<input id="txtPhone" name="txt" type="text" value="<?=$myF->phoneStyle($basic['phone']);?>" class="phone">
				</td>
				<th class="head">고유번호</th>
				<td class="left last" colspan="2"><span id="txtFixNo" class="bold"><?=$basic['fix_no'];?></span></td>
			</tr>
			<tr>
				<th class="head">성명</th>
				<td colspan="2">
					<input id="txtName" name="txt" type="text" value="<?=$basic['name'];?>" org="<?=$basic['name'];?>">
				</td>
				<th class="head">무선</th>
				<td>
					<input id="txtMobile" name="txt" type="text" value="<?=$myF->phoneStyle($basic['mobile']);?>" class="phone">
				</td>
				<th class="head" rowspan="2">결혼정보</th>
				<th class="head">결혼</th>
				<td class="last">
					<select id="cboMarry" name="cbo" style="width:auto;">
						<option value="">-</option><?
						$sql = 'SELECT	code,name
								FROM	hce_gbn
								WHERE	type	= \'MR\'
								AND		use_yn	= \'Y\'';

						$conn->query($sql);
						$conn->fetch();

						$rowCnt = $conn->row_count();

						for($i=0; $i<$rowCnt; $i++){
							$row = $conn->select_row($i);?>
							<option value="<?=$row['code'];?>" <?=($row['code'] == $basic['marry_gbn'] ? 'selected' : '');?>><?=$row['name'];?></option><?
						}

						$conn->row_free();?>
					</select>
				</td>
			</tr>
			<tr>
				<th class="head bottom" rowspan="3">주소</th>
				<th class="head">우편번호</th>
				<td>
					<input id="txtPostno1" name="txt" type="text" value="<?=SubStr($basic['postno'],0,3);?>" class="no_string" maxlength="3" style="width:30px;"> -
					<input id="txtPostno2" name="txt" type="text" value="<?=SubStr($basic['postno'],3);?>" class="no_string" maxlength="3" style="width:30px; margin-right:0;">
					<!--span class="btn_pack small"><a href="#" onclick="__helpAddress(document.f.txtPostno1, document.f.txtPostno2, document.f.txtAddr, document.f.txtAddrDtl);">찾기</a></span-->
				</td>
				<th class="head bottom" rowspan="3">보호자</th>
				<th class="head">성명</th>
				<td>
					<input id="txtGuardNm" name="txt" type="text" value="<?=$basic['grd_nm'];?>">
				</td>
				<th class="head">동거</th>
				<td class="last">
					<select id="cboCohabit" name="cbo" style="width:auto;">
						<option value="">-</option><?
						$sql = 'SELECT	code,name
								FROM	hce_gbn
								WHERE	type	= \'CB\'
								AND		use_yn	= \'Y\'';

						$conn->query($sql);
						$conn->fetch();

						$rowCnt = $conn->row_count();

						for($i=0; $i<$rowCnt; $i++){
							$row = $conn->select_row($i);?>
							<option value="<?=$row['code'];?>" <?=($basic['cohabit_gbn'] == $row['code'] ? 'selected' : '');?>><?=$row['name'];?></option><?
						}

						$conn->row_free();?>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input id="txtAddr" name="txt" type="text" value="<?=$basic['addr'];?>" style="width:100%;">
				</td>
				<th class="head">주소</th>
				<td>
					<input id="txtGuardAddr" name="txt" type="text" value="<?=$basic['grd_addr'];?>" style="width:100%;">
				</td>
				<th class="head bottom" rowspan="2">기타</th>
				<th class="head">학력</th>
				<td class="last">
					<select id="cboEdu" name="cbo" style="width:auto;">
						<option value="">-</option><?
						$sql = 'SELECT	code,name
								FROM	hce_gbn
								WHERE	type	= \'EL\'
								AND		use_yn	= \'Y\'';

						$conn->query($sql);
						$conn->fetch();

						$rowCnt = $conn->row_count();

						for($i=0; $i<$rowCnt; $i++){
							$row = $conn->select_row($i);?>
							<option value="<?=$row['code'];?>" <?=($basic['edu_gbn'] == $row['code'] ? 'selected' : '');?>><?=$row['name'];?></option><?
						}

						$conn->row_free();?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="bottom" colspan="2">
					<input id="txtAddrDtl" name="txt" type="text" value="<?=$basic['addr_dtl'];?>" style="width:100%;">
				</td>
				<th class="head bottom">연락처</th>
				<td class="bottom">
					<input id="txtGuardTel" name="txt" type="text" value="<?=$myF->phoneStyle($basic['grd_tel']);?>" class="phone">
				</td>
				<th class="head bottom">종교</th>
				<td class="bottom last">
					<select id="cboRel" name="cbo" style="width:auto;">
						<option value="">-</option><?
						$sql = 'SELECT	code,name
								FROM	hce_gbn
								WHERE	type	= \'RG\'
								AND		use_yn	= \'Y\'';

						$conn->query($sql);
						$conn->fetch();

						$rowCnt = $conn->row_count();

						for($i=0; $i<$rowCnt; $i++){
							$row = $conn->select_row($i);?>
							<option value="<?=$row['code'];?>" <?=($basic['rel_gbn'] == $row['code'] ? 'selected' : '');?>><?=$row['name'];?></option><?
						}

						$conn->row_free();?>
					</select>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<div class="my_border_blue" style="margin-top:10px; margin-left:10px;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="80px">
			<col width="135px">
			<col width="50px">
			<col width="70px">
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="bold last" colspan="20">2.이용정보</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th class="head">이용기간</th>
				<td class="left">
					<span id="txtFrom_<?=$sr;?>"><?=$myF->dateStyle($cont['from_dt'],'.');?></span> ~
					<span id="txtTo_<?=$sr;?>"><?=$myF->dateStyle($cont['to_dt'],'.');?></span>
					<span id="txtSeq_<?=$sr;?>" style="display:none;"><?=$cont['seq'];?></span>
				</td>
				<td class="center"><?
					if ($cont['svc_stat'] == 'X'){
					}else{?>
						<span class="btn_pack small"><a href="#" onclick="_clientPeriodShow('<?=$sr;?>','<?=$sr;?>'); lfSvcDuplicate('<?=$strJumin;?>');">변경</a></span><?
					}?>
				</td>
				<th class="head">이용상태</th>
				<td class="left last">
					<div style="float:left; width:auto;">
						<span id="txtStat_<?=$sr;?>"><?=($cont['svc_stat'] == '1' ? '이용' : '중지');?></span>
					</div>
					<div style="float:left; width:auto; margin-left:15px;">
						<span class="btn_pack m"><button onclick="lfSvcHis('<?=$strJumin;?>');">서비스이력</button></span>
					</div>
				</td>
			</tr>
			<!--tr>
				<th class="head bottom">서비스단가</th>
				<td class="bottom last" colspan="4">
					<input id="txtSvcCost" name="txt" type="text" value="<?=Number_Format($other['care_cost']);?>" class="number" style="width:70px;">
				</td>
			</tr-->
			<tr>
				<th class="head <?=$cont['svc_stat'] != 'X' ? 'bottom' : '';?>">관리구분</th>
				<td class="left <?=$cont['svc_stat'] != 'X' ? 'bottom' : '';?> last" colspan="4">
					<span id="lblMpGbnY"><?=$cont['mp_gbn'] == 'Y' ? '▣' : '□';?></span>
					<span>중점관리</span>
					<span id="lblMpGbnN" style="margin-left:10px;"><?=$cont['mp_gbn'] != 'Y' ? '▣' : '□';?></span>
					<span>일반</span>
				</td>
			</tr><?
			if ($cont['svc_stat'] == 'X'){?>
				<tr>
					<th class="center bottom">중지사유</th>
					<td class="left bottom last" colspan="4">
						<div style="">※관리자에 의해 중지되었습니다.</div>
						<div style="passing-left:10px;"><?=StripSlashes($cont['stop_reason']);?></div>
					</td>
				</tr><?
			}?>
		</tbody>
	</table>
</div>

<div class="my_border_blue" style="margin-top:10px; margin-left:10px; display:<?=($sr == 'S' ? 'none' : '');?>;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col width="100px">
			<col width="50px">
			<col width="150px">
			<col width="40px">
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="bold last" colspan="20">3.기관정보</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th class="head">기관기호</th>
				<td>
					<input id="txtOrgNo" name="txt" type="text" value="<?=$other['care_org_no'];?>" class="no_string" style="width:100%;">
				</td>
				<th class="head">기관명</th>
				<td class="last">
					<input id="txtOrgNm" name="txt" type="text" value="<?=$other['care_org_nm'];?>" style="width:100%;">
				</td>
				<td class="last"></td>
				<td class="last"></td>
			</tr>
			<tr>
				<th class="head">인정번호</th>
				<td>
					<input id="txtAppNo" name="txt" type="text" value="<?=$other['care_no'];?>" style="width:100%;">
				</td>
				<th class="head">등급</th>
				<td>
					<select id="cboLvl" name="cbo" style="width:auto;">
						<option value="">-</option>
						<option value="1" <?=($other['care_lvl'] == '1' ? 'selected' : '');?>>1등급</option>
						<option value="2" <?=($other['care_lvl'] == '2' ? 'selected' : '');?>>2등급</option>
						<option value="3" <?=($other['care_lvl'] == '3' ? 'selected' : '');?>>3등급</option>
						<option value="4" <?=($other['care_lvl'] == '4' ? 'selected' : '');?>>4등급</option>
						<option value="5" <?=($other['care_lvl'] == '5' ? 'selected' : '');?>>5등급</option>
						<option value="7" <?=($other['care_lvl'] == '7' ? 'selected' : '');?>>등급 외 A,B</option>
						<option value="9" <?=($other['care_lvl'] == '9' ? 'selected' : '');?>>일반</option>
					</select>
				</td>
				<th class="head">구분</th>
				<td class="last">
					<select id="cboGbn" name="cbo" style="width:auto;">
						<option value="">-</option>
						<option value="3" <?=($other['care_gbn'] == '3' ? 'selected' : '');?>>기초</option>
						<option value="2" <?=($other['care_gbn'] == '2' ? 'selected' : '');?>>의료</option>
						<option value="4" <?=($other['care_gbn'] == '4' ? 'selected' : '');?>>경감</option>
						<option value="1" <?=($other['care_gbn'] == '1' ? 'selected' : '');?>>일반</option>
					</select>
				</td>
			</tr>
			<tr>
				<th class="head bottom">담당자명</th>
				<td class="bottom">
					<input id="txtPerNm" name="txt" type="text" value="<?=$other['care_pic_nm'];?>" style="width:100%;">
				</td>
				<th class="head bottom">연락처</th>
				<td class="bottom last">
					<input id="txtPerTel" name="txt" type="text" value="<?=$myF->phoneStyle($other['care_telno']);?>" class="phone">
				</td>
				<td class="bottom last"></td>
				<td class="bottom last"></td>
			</tr>
		</tbody>
	</table>
</div>

<div class="my_border_blue" style="margin-top:10px; margin-left:10px;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="40px">
			<col width="50px">
			<col width="180px">
			<col width="100px">
			<col width="100px">
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="bold last" colspan="20"><?=($sr == 'S' ? '3' : '4');?>.추가서비스정보(재가지원은 해당없음.)</th>
			</tr>
			<tr>
				<th class="head">No</th>
				<th class="head">서비스</th>
				<th class="head">이용기간</th>
				<th class="head">담당자명</th>
				<th class="head">연락처</th>
				<th class="head last">
					<div style="float:right; width:auto; padding-right:5px;"><span class="btn_pack m"><span class="add"></span><button onclick="lfSvcAdd();">추가</button></span></div>
					<div style="float:center; width;auto;">비고</div>
				</th>
			</tr>
		</thead>
		<tbody id="tbodyList">
			<tr id="dummyRow">
				<td class="center bottom last" colspan="20">::추가 버튼을 클릭하여 등록하여 주십시오.::</td>
			</tr>
		</tbody>
	</table>
</div>

<div style="margin-top:5px; padding-left:10px;">
	<div style="float:right; width:auto;">
		<span class="btn_pack m"><button onclick="lfSvcDuplicate('<?=$strJumin;?>');">서비스이력중복확인</button></span>
		<span class="btn_pack m"><span class="save"></span><a href="#" onclick="lfSave('<?=$strJumin;?>'); return false;">저장</a></span><?
		if (!$lbNew){?>
			<span class="btn_pack m"><span class="delete"></span><a href="#" onclick="lfDelete(); return false;">삭제</a></span><?
		}?>
		<span class="btn_pack m"><span class="list"></span><a href="#" onclick="lfSearch(); return false;">리스트</a></span>
	</div>
</div>

<input id="page" name="pate" type="hidden" value="<?=$page;?>">
<input id="jumin" name="jumin" type="hidden" value="<?=$ed->en($jumin);?>">
<input id="new" type="hidden" value="<?=$lbNew;?>">
<input id="linkSeq" type="hidden" value="">
<input id="hisChk" type="hidden" value="N">

<input id="<?=$sr;?>_sugupStatus" type="hidden" value="<?=$svcHis['svc_stat'];?>">
<input id="<?=$sr;?>_gaeYakFm" type="hidden" value="<?=$svcHis['from_dt'];?>">
<input id="<?=$sr;?>_gaeYakTo" type="hidden" value="<?=$svcHis['to_dt'];?>">
<?
	Unset($basic);
	Unset($cont);
	Unset($other);
	Unset($svcHis);

	include_once('../inc/_db_close.php');
?>