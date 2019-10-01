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
	$cGiho	= $_SESSION['userCenterGiho'];
	$cName	= $_SESSION['userCenterName'];

	$name	= $_POST['txtName'];
	$telno	= $_POST['txtTelno'];
	$addr	= $_POST['txtAddr'];
	$grdNm	= $_POST['txtGrdNm'];
	$addrMent = $_POST['txtAddrMent'];
	$gender = $_POST['optGender'];
	$income = $_POST['cboIncome'];
	$generation = $_POST['cboGeneration'];


	$rcptFrom = $_POST['txtRcptFrom'];
	$rcptTo = $_POST['txtRcptTo'];

	$page	= $_POST['page'];
	$jumin	= $ed->de($_POST['jumin']);
	$gbn	= $_POST['normalSeq'];
	$today	= Date('Y-m-d');

	if ($gbn == '0'){
		$regGbn = '1'; //대상자
	}else if ($gbn > '0'){
		$regGbn = '2'; //일반
	}else{
		$regGbn = '1'; //등록전
	}

	if ($regGbn == '1'){
		$sql = 'SELECT	jumin
				FROM	mst_jumin
				WHERE	org_no	= \''.$orgNo.'\'
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
					WHERE	org_no	 = \''.$orgNo.'\'
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
				WHERE	m03_ccode = \''.$orgNo.'\'
				AND		m03_jumin = \''.$jumin.'\'';
		$basic = $conn->get_array($sql);

		//추가정보
		$sql = 'SELECT	addr_ment,reg_dt,rst_dt,rst_reason,reason_str,end_dt,end_reason,kind_gbn
				FROM	client_option
				WHERE	org_no	= \''.$orgNo.'\'
				AND		jumin	= \''.$jumin.'\'';

		$row = $conn->get_array($sql);
		$basic['addr_ment']	 = $row['addr_ment'];
		$basic['reg_dt']	 = $row['reg_dt'];
		$basic['rst_dt']	 = $row['rst_dt'];
		$basic['rst_reason'] = $row['rst_reason'];
		$basic['reason_str'] = $row['reason_str'];
		$basic['end_dt']	 = $row['end_dt'];
		$basic['end_reason'] = $row['end_reason'];
		$basic['kind_gbn']	 = $row['kind_gbn'];
		Unset($row);

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
					WHERE	org_no	 = \''.$orgNo.'\'
					AND		jumin	 = \''.$jumin.'\'
					AND		svc_cd	 = \''.$sr.'\'
					AND		from_dt	<= \''.$today.'\'
					AND		to_dt	>= \''.$today.'\'';

			$cont = $conn->get_array($sql);

			if (!$cont){
				$sql = 'SELECT	seq
						,		from_dt
						,		to_dt
						,		svc_stat
						,		svc_reason
						,		stop_reason
						,		mp_gbn
						FROM	client_his_svc
						WHERE	org_no	 = \''.$orgNo.'\'
						AND		jumin	 = \''.$jumin.'\'
						AND		svc_cd	 = \''.$sr.'\'
						ORDER	BY seq DESC
						LIMIT	1';

				$cont = $conn->get_array($sql);
			}

			//서비스정보
			$sql = 'SELECT	*
					FROM	care_svc_his
					WHERE	org_no	= \''.$orgNo.'\'
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
	}else if ($regGbn == '2'){


		if ($jumin){
			$lbNew = false;
		}else{
			$lbNew = true;
		}

		//기본정보
		if(!$lbNew){
			$sql = 'SELECT	jumin,name,postno,addr,addr_dtl,addr_ment,phone,mobile,grd_nm,grd_addr,grd_telno,marry_gbn,cohabit_gbn,edu_gbn,rel_gbn,kind_gbn,rst_reason,reg_dt, grd_telno AS grd_tel, end_dt, end_reason, rst_dt
					FROM	care_client_normal
					WHERE	org_no		= \''.$orgNo.'\'
					AND		normal_sr	= \''.$sr.'\'
					AND		normal_seq	= \''.$gbn.'\'';

			$basic = $conn->get_array($sql);
			$strJumin = $basic['jumin'];
		}
	}
?>
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
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

		//등록구분
		$('input:radio[name="optRctGbn"]').unbind('click').bind('click',function(){
			if ($(this).val() == '1'){
				$('#ID_BODY_USE').show();
				$('#ID_BODY_CENTER').show();
			}else{
				$('#ID_BODY_USE').hide();
				$('#ID_BODY_CENTER').hide();
			}
		});
		$('input:radio[name="optRctGbn"]:checked').click();
		lfSetRst('<?=$basic["rst_reason"];?>');

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

	//우편번호 검색
	function lfPostCode(postno, addr, addr_dtl){
		var width = 500; //팝업의 너비
		var height = 600; //팝업의 높이

		new daum.Postcode({
			oncomplete: function(data) {
				// 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분입니다.
				// 예제를 참고하여 다양한 활용법을 확인해 보세요.
				// 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

				// 도로명 주소의 노출 규칙에 따라 주소를 조합한다.
                // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
				var fullRoadAddr = data.roadAddress;
				var extraRoadAddr = '';

				// 법정동명이 있을 경우 추가한다. (법정리는 제외)
                // 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
				if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
					extraRoadAddr += data.bname;
				}
				// 건물명이 있고, 공동주택일 경우 추가한다.
				if(data.buildingName !== '' && data.apartment === 'Y'){
				   extraRoadAddr += (extraRoadAddr !== '' ? ', ' + data.buildingName : data.buildingName);
				}
				// 도로명, 지번 조합형 주소가 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
				if(extraRoadAddr !== ''){
					extraRoadAddr = ' (' + extraRoadAddr + ')';
				}
				// 도로명, 지번 주소의 유무에 따라 해당 조합형 주소를 추가한다.
				if(fullRoadAddr !== ''){
					fullRoadAddr += extraRoadAddr;
				}

				// 우편번호와 주소 정보를 해당 필드에 넣는다.
				if ($('input:text[name="'+postno+'"]').length > 0){
					$('input:text[name="'+postno+'"]').val(data.zonecode);
				}

				if ($('input:text[name="'+addr+'"]').length > 0){
					$('input:text[name="'+addr+'"]').val(fullRoadAddr);
				}

				document.getElementById(addr_dtl).focus();
			}
		}).open({
			left: (window.screen.width / 2) - (width / 2),
			top: (window.screen.height / 2) - (height / 2)
		});
	}

	function lfSearch(){
		var f = document.f;

		var parm = new Array();
			parm = {
				'txtName':'<?=$name;?>'
			,	'txtAddr':'<?=$addr;?>'
			,	'txtGrdNm':'<?=$grdNm;?>'
			,	'txtAddrMent':'<?=$addrMent;?>'
			,	'txtRcptFrom':'<?=$rcptFrom;?>'
			,	'txtRcptTo':'<?=$rcptTo;?>'
			,	'optGender':'<?=$gender;?>'
			,	'cboIncome':'<?=$income;?>'
			,	'cboGeneration':'<?=$generation;?>'
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
		var regGbn = $('input:radio[name="optRctGbn"]:checked').val();

		if (!regGbn) regGbn = '1';
		if (regGbn == '1'){
			//대상자만 주민번호를 확인한다.
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
		}

		if (!$('#txtName').val()){
			alert('성명을 입력하여 주십시오.');
			$('#txtName').focus();
			return;
		}

		var his = '';

		if (regGbn == '1'){
			if (!$('#txtFrom_<?=$sr;?>').text() || !$('#txtTo_<?=$sr;?>').text()){
				alert('이용기간입력 오류입니다. 확인하여 주십시오.');
				return;
			}

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
		}

		var data = {};

		data['regGbn']	= regGbn;
		data['txtFrom']	= $('#txtFrom_<?=$sr;?>').text();
		data['txtTo']	= $('#txtTo_<?=$sr;?>').text();
		data['txtStat']	= ($('#txtStat_<?=$sr;?>').text() == '이용' ? '1' : '9');
		data['txtSeq']	= $('#txtSeq_<?=$sr;?>').text();
		data['linkSeq']	= $('#linkSeq').val();
		data['history']	= his;

		if ($('#lblMpGbnY').text() == '▣'){
 			data['mp'] = 'Y';
		}else{
			data['mp'] = 'N';
		}

		$('input').each(function(){
			data[$(this).attr('id')] = $(this).val();
		});

		$('select').each(function(){
			data[$(this).attr('id')] = $(this).val();
		});

		$.ajax({
			type: 'POST'
		,	url : './care_client_s_reg_save.php'
		,	data: data
		,	beforeSend: function (){
			}
		,	success: function(result){
				if (result.substring(0,2) == 'OK'){
					alert('정상적으로 처리되었습니다.');
					/*
					var f = document.f;
					var obj = __parseVal(result.substring(3));

					f.jumin.value = obj['jumin'];
					f.normalSeq.value = obj['normalSeq'];
					f.action = '../care/care.php?sr=<?=$sr;?>&type=82';
					f.submit();
					*/
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
		data['normalSeq']= $('#normalSeq').val();

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

	function lfSetRst(gbn){
		$('#trEnd').hide();
		$('#trRst').hide();

		if (gbn == '03'){
			$('#trEnd').show();
			$('.CLS_RST').removeClass('bottom');
		}else if (gbn == '99'){
			$('#trRst').show();
			$('.CLS_RST').removeClass('bottom');
		}else{
			$('.CLS_RST').addClass('bottom');
		}
	}
</script>
<div class="title"><div>대상자<?=($lbNew ? '등록' : '수정');?>(<?=$title;?>)</div></div>
<div class="my_border_blue" style="">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="70px" span="2">
			<col width="150px">
			<col width="70px">
			<col width="60px">
			<col width="130px">
			<col width="70px">
			<col width="50px">
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="bold last" colspan="9" style="text-align:left;">1.기본정보(<span class="bold">※성별 판단을 위해서 주민번호 7자리까지는 입력하여 주십시오.</span>)</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th class="head">주민번호</th>
				<td colspan="2">
					<input id="txtJumin1" name="txtJumin" type="text" value="<?=SubStr($strJumin,0,6);?>" class="no_string" maxlength="6" style="width:50px;"> -
					<input id="txtJumin2" name="txtJumin" type="text" value="<?=SubStr($strJumin,6,7);?>" class="no_string" maxlength="7" style="width:55px; margin-right:0;">
				</td><?
				if ($regGbn == '1'){?>
					<td colspan="3"></td><?
				}else{?>
					<th class="head">등록구분</th>
					<td colspan="2">
						<label><input id="optRctGbn1" name="optRctGbn" type="radio" class="radio" value="1" <?=$regGbn == '3' ? 'checked' : '';?>>대상자</label>
						<label><input id="optRctGbn2" name="optRctGbn" type="radio" class="radio" value="2" <?=$regGbn == '2' ? 'checked' : '';?>>일반</label>
					</td><?
				}?>
				<th class="head">고유번호</th>
				<td class="left last" colspan="2"><span id="txtFixNo" class="bold"><?=$basic['fix_no'];?></span></td>
			</tr>
			<tr>
				<th class="head">성명</th>
				<td colspan="2">
					<input id="txtName" name="txt" type="text" value="<?=$basic['name'];?>" org="<?=$basic['name'];?>">
				</td>
				<th class="head" rowspan="2">연락처</th>
				<th class="head">유선</th>
				<td>
					<input id="txtPhone" name="txt" type="text" value="<?=$myF->phoneStyle($basic['phone']);?>" class="phone">
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
				<th class="head" rowspan="3">주소</th>
				<th class="head">우편번호</th>
				<td>
					<input id="txtPostno" name="txtPostno" type="text" value="<?=$basic['postno'];?>" class="no_string" maxlength="6" style="width:50px;">
					<span class="btn_pack small"><a href="#" onclick="lfPostCode('txtPostno', 'txtAddr', 'txtAddrDtl');">찾기</a></span>
				</td>
				<th class="head">무선</th>
				<td>
					<input id="txtMobile" name="txt" type="text" value="<?=$myF->phoneStyle($basic['mobile']);?>" class="phone">
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
					<input id="txtAddr" name="txtAddr" type="text" value="<?=$basic['addr'];?>" style="width:100%;">
				</td>
				<th class="head" rowspan="3">보호자</th>
				<th class="head">성명</th>
				<td>
					<input id="txtGuardNm" name="txt" type="text" value="<?=$basic['grd_nm'];?>">
				</td>
				<th class="head" rowspan="2">기타</th>
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
				<td class="" colspan="2">
					<input id="txtAddrDtl" name="txtAddrDtl" type="text" value="<?=$basic['addr_dtl'];?>" style="width:100%;">
				</td>
				<th class="head">주소</th>
				<td>
					<input id="txtGuardAddr" name="txt" type="text" value="<?=$basic['grd_addr'];?>" style="width:100%;">
				</td>
				<th class="head">종교</th>
				<td class="last">
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
			<tr>
				<th class="head">관리주소</th>
				<td colspan="2">
					<input id="txtAddrMent" name="txt" type="text" value="<?=$basic['addr_ment'];?>" style="width:100%;">
				</td>
				<th class="head">연락처</th>
				<td class="">
					<input id="txtGuardTel" name="txt" type="text" value="<?=$myF->phoneStyle($basic['grd_tel']);?>" class="phone">
				</td><?
				if ($debug){?>
					<th class="center">유형</th>
					<td colspan="2">
						<select id="cboKindGbn" style="width:auto;">
							<option value=""></option>
							<option value="1" <?=$basic['kind_gbn'] == '1' ? 'selected' : '';?>>수급자</option>
							<option value="2" <?=$basic['kind_gbn'] == '2' ? 'selected' : '';?>>차상위</option>
							<option value="3" <?=$basic['kind_gbn'] == '3' ? 'selected' : '';?>>150%</option>
						</select>
					</td><?
				}else{?>
					<td colspan="3"></td><?
				}?>
			</tr>
			<tr>
				<th class="CLS_RST center bottom">접수일자</th>
				<td class="CLS_RST bottom" colspan="2"><input id="txtRegDt" type="text" value="<?=$myF->dateStyle($basic['reg_dt']);?>" class="date"></td>
				<th class="CLS_RST center bottom">처리일자</th>
				<td class="CLS_RST bottom" colspan="2"><input id="txtRstDt" type="text" value="<?=$myF->dateStyle($basic['rst_dt']);?>" class="date"></td>
				<th class="CLS_RST center bottom">처리결과</th>
				<td class="CLS_RST bottom last" colspan="2">
					<select id="cboRstReason" style="width:auto;" onchange="lfSetRst($(this).val());">
						<option value="">-선택하여 주십시오.-</option>
						<option value="01" <?=$basic['rst_reason'] == '01' ? 'selected' : '';?>>서비스대상등록</option>
						<option value="02" <?=$basic['rst_reason'] == '02' ? 'selected' : '';?>>타기관이전</option>
						<option value="03" <?=$basic['rst_reason'] == '03' ? 'selected' : '';?>>종결</option>
						<option value="99" <?=$basic['rst_reason'] == '99' ? 'selected' : '';?>>기타</option>
					</select>
				</td>
			</tr>
			<tr id="trEnd" style="display:<?=$basic['rst_reason'] != '03' ? 'none' : '';?>;">
				<th class="center bottom">종결일</th>
				<td class="bottom" colspan="2"><input id="txtEndDt" type="text" value="<?=$myF->dateStyle($basic['end_dt']);?>" class="date"></td>
				<th class="center bottom">종결사유</th>
				<td class="bottom last" colspan="8"><input id="txtEndReason" type="text" value="<?=$basic['end_reason'];?>" style="width:100%;"></td>
			</tr>
			<tr id="trRst" style="display:<?=$basic['rst_reason'] != '99' ? 'none' : '';?>;">
				<th class="center bottom"></th>
				<td class="bottom" colspan="2"></td>
				<th class="center bottom">사유내용</th>
				<td class="bottom last" colspan="8"><input id="txtReasonStr" type="text" value="<?=$basic['reason_str'];?>" style="width:100%;"></td>
			</tr>
		</tbody>
	</table>
</div>

<div id="ID_BODY_USE" class="my_border_blue" style="margin-top:10px; margin-left:10px;">
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

<div id="ID_BODY_CENTER" class="my_border_blue" style="margin-top:10px; margin-left:10px;">
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
				<th class="bold last" colspan="20">3.추가서비스정보(재가지원은 해당없음.)</th>
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

<input id="page" name="page" type="hidden" value="<?=$page;?>">
<input id="jumin" name="jumin" type="hidden" value="<?=$ed->en($jumin);?>">
<input id="normalSeq" name="normalSeq" type="hidden" value="<?=$gbn;?>">
<input id="new" type="hidden" value="<?=$lbNew;?>">
<input id="linkSeq" type="hidden" value="">
<input id="hisChk" type="hidden" value="N">

<input id="<?=$sr;?>_sugupStatus" type="hidden" value="<?=$svcHis['svc_stat'];?>">
<input id="<?=$sr;?>_gaeYakFm" type="hidden" value="<?=$svcHis['from_dt'];?>">
<input id="<?=$sr;?>_gaeYakTo" type="hidden" value="<?=$svcHis['to_dt'];?>">
<?
	Unset($basic);
	Unset($cont);
	Unset($svcHis);

	include_once('../inc/_db_close.php');
?>