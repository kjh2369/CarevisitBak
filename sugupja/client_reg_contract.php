<?
	include_once('../inc/_login.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$tmpKey = $ed->de($_REQUEST['jumin']);

	$sql = 'SELECT	m03_key
			FROM	m03sugupja
			WHERE	m03_ccode = \''.$orgNo.'\'
			AND		m03_jumin = \''.$tmpKey.'\'';

	$tmpKey = $conn->get_data($sql);

?>
<script type="text/javascript">
<!--
	function show_contract(){
		go_contract_list();
		show_svc_layer('contract');
		document.getElementsByName('grp_btn')[0].style.display = 'none';
		document.getElementsByName('grp_btn')[1].style.display = 'none';
	}

	//리스트
	function go_contract_list(){

		if (document.getElementById('write_mode').value == 1) return;

		var code  = document.getElementById('code').value;
		var ssn   = document.getElementById('jumin').value;
		var kind   = document.getElementById('kind').value;
		var param = {'code':code,'ssn':ssn,'kind':kind};
		var URL = './client_contract_list.php';
		var xmlhttp = new Ajax.Request(
			URL, {
				method:'post',
				parameters:param,
				onSuccess:function (responseHttpObj) {
					var obj = document.getElementById('my_contract');
						obj.innerHTML = responseHttpObj.responseText;

						document.getElementsByName('saves')[0].style.display = 'none';
						document.getElementsByName('reset')[0].style.display = 'none';
						document.getElementsByName('lists')[0].style.display = 'none';
						document.getElementsByName('inputs')[0].style.display = '';
						//document.getElementsByName('emp_print')[0].style.display = '';
						document.getElementsByName('saves')[1].style.display = 'none';
						document.getElementsByName('reset')[1].style.display = 'none';
						document.getElementsByName('lists')[1].style.display = 'none';
						document.getElementsByName('inputs')[1].style.display = '';
						//document.getElementsByName('emp_print')[1].style.display = '';
				}
			}
		);
	}

	//입력
	function go_contract_reg(seq){
		if (document.getElementById('write_mode').value == 1) return;


		var code  = document.getElementById('code').value;
		var jumin = document.getElementById('jumin').value;
		var kind   = document.getElementById('kind').value;

		var param = {'code':code,'kind':kind,'seq':seq,'ssn':jumin};
		var URL = './client_contract_reg.php';
		var xmlhttp = new Ajax.Request(
			URL, {
				method:'post',
				parameters:param,
				onSuccess:function (responseHttpObj) {
					var obj = document.getElementById('my_contract');
						obj.innerHTML = responseHttpObj.responseText;

						document.getElementsByName('saves')[0].style.display = '';
						document.getElementsByName('reset')[0].style.display = '';
						document.getElementsByName('lists')[0].style.display = '';
						document.getElementsByName('inputs')[0].style.display = 'none';
						//document.getElementsByName('emp_print')[0].style.display = 'none';
						document.getElementsByName('saves')[1].style.display = '';
						document.getElementsByName('reset')[1].style.display = '';
						document.getElementsByName('lists')[1].style.display = '';
						document.getElementsByName('inputs')[1].style.display = 'none';
						//document.getElementsByName('emp_print')[1].style.display = 'none';
						check_only();
					__init_form(document.f);

				}
			}
		);
	}
	//삭제
	function go_contract_delete(seq, svc_kind){
		if (!confirm('데이타 삭제후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

		var code  = document.getElementById('code').value;
		var jumin = document.getElementById('jumin').value;
		var kind   = document.getElementById('kind').value;

		var param = {'code':code,'kind':kind,'seq':seq,'ssn':jumin, 'svc_kind':svc_kind};
		var URL = './client_contract_delete.php';
		var xmlhttp = new Ajax.Request(
			URL, {
				method:'post',
				parameters:param,
				onSuccess:function (responseHttpObj) {
					if (responseHttpObj.responseText == 'ok'){
						alert('정상적으로 처리되었습니다.');
						go_contract_list();
					}else{
						alert('데이타 처리중 오류가 발생하였습니다. 관리자에게 문의하여 주십시오.');
					}
				}
			}
		);
	}

	//출력
	function go_contract_show(seq, svc_seq, svc_kind){
		var f = document.f;

		var w = 700;
		var h = 900;
		var l = (window.screen.width  - w) / 2;
		var t = (window.screen.height - h) / 2;

		var code  = document.getElementById('code').value;
		var jumin = document.getElementById('jumin').value;
		var kind   = document.getElementById('kind').value;

		var win = window.open('about:blank','SHOW_PDF','top='+t+',left='+l+',width='+w+',height='+h+',scrollbars=no,resizable=yes,location=no,toolbar=no,menubar=no,directories=no');
		var f   = document.f;


		f.code.value = code;

		f.kind.value  = kind;
		f.ssn.value  = jumin;
		f.seq.value  = seq;
		f.svc_seq.value  = svc_seq;


		f.target = 'SHOW_PDF';
		f.action = '../counsel/counsel_show.php?type='+svc_kind;
		f.submit();
		f.target = '_self';
	}


	//저장
	function contract_save(){

		//f.svc_dt.value = document.getElementById('svc_date').innerHTML;

		if(!f.reg_dt.value){
			alert("작성일자를 입력해주세요.");
			f.reg_dt.focus();
			return false;
		}

		if(!f.from_dt.value){
			alert("계약시작일자를 입력해주세요.");
			from_dt.focus();
			//find_svc_date(f.code.value,f.ssn.value,["from_dt","to_dt","svc_key"]);
			return false;
		}

		if(!f.to_dt.value){
			alert("계약종료일자를 입력해주세요.");
			to_dt.focus();
			//find_svc_date(f.code.value,f.ssn.value,["from_dt","to_dt","svc_key"]);
			return false;
		}

		for(var i=1; i<=7; i++){
			var yoil1 = document.getElementById('use_yoil1_'+i).checked;


			if(yoil1 == true){
				var yoil1_chk = true;
				break;
			}else {
				var yoil1_chk = false;
			}
		}

		if(yoil1_chk == true){
			if(f.from_time1.value == ''){
				alert("이용시작시간을 입력해주세요.");
				f.from_time1.focus();
				return false;
			}

			if(f.to_time1.value == ''){
				alert("이용종료시간을 입력해주세요.");
				f.to_time1.focus();
				return false;
			}
		}

		for(var i=1; i<=7; i++){
			var yoil2 = document.getElementById('use_yoil2_'+i).checked;


			if(yoil2 == true){
				var yoil2_chk = true;
				break;
			}else {
				var yoil2_chk = false;
			}
		}

		if(yoil2_chk == true){
			if(f.from_time2.value == ''){
				alert("이용시작시간을 입력해주세요.");
				f.from_time2.focus();
				return false;
			}

			if(f.to_time2.value == ''){
				alert("이용종료시간을 입력해주세요.");
				f.to_time2.focus();
				return false;
			}
		}

		for(var i=1; i<=7; i++){
			var yoil3 = document.getElementById('use_yoil3_'+i).checked;


			if(yoil3 == true){
				var yoil3_chk = true;
				break;
			}else {
				var yoil3_chk = false;
			}
		}

		if(yoil3_chk == true){
			if(f.from_time.value == ''){
				alert("이용시작시간을 입력해주세요.");
				f.from_time.focus();
				return false;
			}

			if(f.to_time.value == ''){
				alert("이용종료시간을 입력해주세요.");
				f.to_time.focus();
				return false;
			}

			for(var i=0; i<4; i++){
				var use_type = document.getElementsByName('use_type')[i];
				if(use_type.checked){
					break;
				}
			}

			if(!use_type.checked){
				alert("이용방법(월)을 선택해주세요.");
				document.getElementsByName('use_type')[0].focus();
				return false;
			}
		}

		/*
		for(var i=1; i<=7; i++){
			var yoil1 = document.getElementById('use_yoil1_'+i).checked;


			if(yoil1 == true){
				var yoil1_chk = true;
				break;
			}else {
				var yoil1_chk = false;
			}
		}

		if(yoil1_chk == false){
			alert("이용요일1(방문요양)을 선택해주십시오.");
			document.getElementById('use_yoil1_1').focus();
			return false;
		}

		for(var i=0; i<2; i++){
			var svc_kind = document.getElementsByName('svc_kind')[i];
			if(svc_kind.checked){
				break;
			}
		}

		if(!svc_kind.checked){
			alert("케어구분을 선택해주세요.");
			document.getElementsByName('svc_kind')[0].focus();
			return false;
		}

		if(f.svc_kind.value == '500'){

			for(var i=0; i<3; i++){
				var use_type = document.getElementsByName('use_type')[i];
				if(use_type.checked){
					break;
				}
			}

			if(!use_type.checked){
				alert("이용방법(월)을 선택해주세요.");
				document.getElementsByName('use_type')[0].focus();
				return false;
			}
		}
		*/

		f.action = './client_contract_save.php';
		f.submit();
	}

	//이용일1 체크 시 이용일2 선택가능
	function check_umu(){
		for(var i=1; i<=7; i++){
			var yoil1 = document.getElementById('use_yoil1_'+i).checked;

			if(yoil1 == true){
				var yoil1_chk = true;
				break;
			}else {
				var yoil1_chk = false;
			}
		}

		if(yoil1_chk == true){
			for(var i=1; i<=7; i++){
				document.getElementById('use_yoil2_'+i).disabled = false;
			}
		}else {
			for(var i=1; i<=7; i++){
				document.getElementById('use_yoil2_'+i).disabled = true;
			}
		}
	}


	function chk_yoil(svc_kind, mode, obj){

		if(svc_kind == '200'){
			if(mode == '1'){
				for(var i=1; i<=7; i++){
					var yoil1 = document.getElementById('use_yoil1_'+i).checked;


					if(yoil1 == true){
						var yoil1_chk = true;
						break;
					}else {
						var yoil1_chk = false;
					}
				}

				if(yoil1_chk == false){
					alert("이용시간1(요양)을 선택해주십시오.");
					document.getElementById('use_yoil1_1').focus();
					return false;
				}
			}else if(mode == '2'){

				for(var i=1; i<=7; i++){
					var yoil2 = document.getElementById('use_yoil2_'+i).checked;


					if(yoil2 == true){
						var yoil2_chk = true;
						break;
					}else {
						var yoil2_chk = false;
					}
				}

				if(yoil2_chk == false){
					alert("이용시간2(요양)을 선택해주십시오.");
					document.getElementById('use_yoil2_1').focus();
					return false;
				}
			}else {

				for(var i=1; i<=7; i++){
					var yoil1 = document.getElementById('use_yoil1_'+i).checked;


					if(yoil1 == true){
						var yoil1_chk = true;
						break;
					}else {
						var yoil1_chk = false;
					}
				}

				if(yoil1_chk == false){
					obj.checked = false;
					alert("이용시간1(요양)을 선택해주십시오.");
					document.getElementById('use_yoil1_1').focus();
					return false;
				}

				if(f.from_time1.value == ''){
					obj.checked = false;
					alert("이용시작시간을 입력해주세요.");
					f.from_time1.focus();
					return false;
				}

				if(f.to_time1.value == ''){
					obj.checked = false;
					alert("이용종료시간을 입력해주세요.");
					f.to_time1.focus();
					return false;
				}
			}
		}else if(svc_kind == '500'){
			for(var i=1; i<=7; i++){
				var yoil3 = document.getElementById('use_yoil3_'+i).checked;


				if(yoil3 == true){
					var yoil3_chk = true;
					break;
				}else {
					var yoil3_chk = false;
				}
			}

			if(yoil3_chk == false){
				alert("이용요일(목욕)을 선택해주십시오.");
				document.getElementById('use_yoil3_1').focus();
				return false;
			}
		}else if(svc_kind == '800'){
			if(mode == '1'){
				for(var i=1; i<=7; i++){
					var yoil1 = document.getElementById('use_yoil1_nurse'+i).checked;


					if(yoil1 == true){
						var yoil1_chk = true;
						break;
					}else {
						var yoil1_chk = false;
					}
				}

				if(yoil1_chk == false){
					alert("이용시간1(간호)을 선택해주십시오.");
					document.getElementById('use_yoil1_nurse1').focus();
					return false;
				}
			}else if(mode == '2'){

				for(var i=1; i<=7; i++){
					var yoil2 = document.getElementById('use_yoil2_nurse'+i).checked;


					if(yoil2 == true){
						var yoil2_chk = true;
						break;
					}else {
						var yoil2_chk = false;
					}
				}

				if(yoil2_chk == false){
					alert("이용시간2(간호)을 선택해주십시오.");
					document.getElementById('use_yoil2_nurse1').focus();
					return false;
				}
			}else {

				for(var i=1; i<=7; i++){
					var yoil1 = document.getElementById('use_yoil1_nurse'+i).checked;


					if(yoil1 == true){
						var yoil1_chk = true;
						break;
					}else {
						var yoil1_chk = false;
					}
				}

				if(yoil1_chk == false){
					obj.checked = false;
					alert("이용시간1(간호)을 선택해주십시오.");
					document.getElementById('use_yoil1_nurse1').focus();
					return false;
				}

				if(f.from_time1_nurse.value == ''){
					obj.checked = false;
					alert("이용시작시간을 입력해주세요.");
					f.from_time1_nurse.focus();
					return false;
				}

				if(f.to_time1_nurse.value == ''){
					obj.checked = false;
					alert("이용종료시간을 입력해주세요.");
					f.to_time1_nurse.focus();
					return false;
				}
			}
		}else {

			for(var i=1; i<=7; i++){
				var yoil4 = document.getElementById('use_yoil4_'+i).checked;


				if(yoil4 == true){
					var yoil4_chk = true;
					break;
				}else {
					var yoil4_chk = false;
				}
			}

			if(yoil4_chk == false){
				alert("이용시간(주간야간보호)을 선택해주십시오.");
				document.getElementById('use_yoil4_1').focus();
				return false;
			}
		}
	}

	function check_only(){
		var chk_no  = 0;
		for(var i=1; i<=7; i++){

            if(document.getElementById('use_yoil3_'+i).checked == true){
				chk_no = (chk_no+1);
			}
        }
		
		for(var i=1; i<=7; i++){
			//if(chk_no > 1){
			//	if(document.getElementById('use_yoil3_'+i).checked == false){
			//		document.getElementById('use_yoil3_'+i).disabled = true;
			//	}
			//}else {
				document.getElementById('use_yoil3_'+i).disabled = false;
			//}
		}
    }


	function find_svc_date(code, ssn, target){
		var modal = showModalDialog('../inc/_find_person.php?type=svc_date&code='+code+'&ssn='+ssn, window, 'dialogWidth:180px; dialogHeight:140px; dialogHide:yes; scroll:no; status:yes');

		if (!modal) return;
		if (!target) return modal;

		for(var i=0; i<target.length; i++){
			if (target[i] != ''){

				var from = document.getElementById(target[0]);
				var to = document.getElementById(target[1]);
				var seq  = document.getElementById(target[2]);

				var val = modal[0].split('~');

				if(modal[0]){
					from.value = val[0];
					to.value = val[1];
				}else if(modal[1]){
					seq.value = modal[1];
				}
			}
		}
	}

	/*
	function svc_gbn(cd,nocd){
		document.getElementById('svc_'+cd).style.display = '';
		document.getElementById('svc_'+nocd).style.display = 'none';
	}
	*/


	function lfSetSign(obj,gbn,seq){
		if ('<?=$tmpKey;?>' == '') return;

		var key = 'CT_'+gbn+'_<?=$tmpKey;?>_0_'+seq;

		__SetSign(obj, key, 'client');
	}


	function lfShowCont(conSeq, svcSeq, subCd){
		var width = 800;
		var height = 600;
		var top = (screen.availHeight - height) / 2;
		var left = (screen.availWidth - width) / 2;

		var target = 'CONT';
		var option = 'left='+left+', top='+top+', width='+width+',height='+height+',scrollbars=yes,status=no,resizable=no';
		var url    = './cont_prt.php';
		var win = window.open('', target, option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'conSeq':conSeq
			,	'svcSeq':svcSeq
			,	'subCd'	:subCd
			,	'jumin'	:document.getElementById('jumin').value
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

		form.setAttribute('target', target);
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}


	function lfPrtHwp(conSeq){
		
		location.replace = '../hwp/enter_app_prt.php?conSeq='+conSeq+'&jumin='+document.getElementById('jumin').value;

	}



	function lfExcel(conSeq, svcSeq){
		var target = 'CONT';
		var url    = './enter_app_excel.php';
		
		var parm = new Array();
			parm = {
				'conSeq':conSeq
			,	'svcSeq':svcSeq
			,	'jumin'	:document.getElementById('jumin').value
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

		form.setAttribute('target', target);
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}
-->
</script>

<div id="contract_body" style="margin-top:10px; margin-bottom:10px; margin-left:10px; margin-right:10px; display:none;">
	<table class="my_table my_border_blue" style="width:100%; margin-bottom:10px;">
		<th class="right last" style="text-align:right; padding-right:5px; border-top:0; border-left:0; border-right:0;">
		<span id="inputs" class="btn_pack m icon"><span class="add"></span><button type="button" onFocus="this.blur();" onclick="go_contract_reg('',0); return false;">입력</button></span>
		<span id="emp_print" class="btn_pack m"><button type="button" onFocus="this.blur();" onClick="go_contract_show('','', '200');">빈양식[요양]</button></span>
		<span id="emp_print" class="btn_pack m"><button type="button" onFocus="this.blur();" onClick="go_contract_show('','', '500');">빈양식[목욕]</button></span>
		<span id="emp_print" class="btn_pack m"><button type="button" onFocus="this.blur();" onClick="go_contract_show('','', '800');">빈양식[간호]</button></span><?
		if($gDayAndNight){ ?>
			<span id="emp_print" class="btn_pack m"><button type="button" onFocus="this.blur();" onClick="go_contract_show('','', '900');">빈양식[주야간보호]</button></span><?
		} ?>
		<span id="saves" class="btn_pack m icon"><span class="save"></span><button type="button" onFocus="this.blur();" onClick="contract_save(); return false;">저장</button></span>
		<span id="reset" class="btn_pack m"><button type="button" onFocus="this.blur();" onClick="if(confirm('입력하신 내용이 사라집니다. 리셋하시겠습니까?')){document.f.reset();}">리셋</button></span>
		<span id="lists" class="btn_pack m"><button type="button" onFocus="this.blur();"onclick="go_contract_list('',0); return false;">리스트</button></span>
		</th>
	</table>
	<div id="my_contract" style=""></div>
	<table class="my_table my_border_blue" style="width:100%; margin-top:10px;">
		<th class="right last" style="text-align:right; padding-right:5px; border-top:0; border-left:0; border-right:0;">
		<span id="inputs" class="btn_pack m icon"><span class="add"></span><button type="button" onFocus="this.blur();" onclick="go_contract_reg('',0); return false;">입력</button></span>
		<span id="emp_print" class="btn_pack m"><button type="button" onFocus="this.blur();" onClick="go_contract_show('','', '200');">빈양식[요양]</button></span>
		<span id="emp_print" class="btn_pack m"><button type="button" onFocus="this.blur();" onClick="go_contract_show('','', '500');">빈양식[목욕]</button></span>
		<span id="emp_print" class="btn_pack m"><button type="button" onFocus="this.blur();" onClick="go_contract_show('','', '800');">빈양식[간호]</button></span><?
		if($gDayAndNight){ ?>
			<span id="emp_print" class="btn_pack m"><button type="button" onFocus="this.blur();" onClick="go_contract_show('','', '900');">빈양식[주야간보호]</button></span><?
		} ?>
		<span id="saves" class="btn_pack m icon"><span class="save"></span><button type="button" onFocus="this.blur();" onClick="contract_save(); return false;">저장</button></span>
		<span id="reset" class="btn_pack m"><button type="button" onFocus="this.blur();" onClick="if(confirm('입력하신 내용이 사라집니다. 리셋하시겠습니까?')){document.f.reset();}">리셋</button></span>
		<span id="lists" class="btn_pack m"><button type="button" onFocus="this.blur();"onclick="go_contract_list('',0); return false;">리스트</button></span>
		</th>
	</table>
</div>
