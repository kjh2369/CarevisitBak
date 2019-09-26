<?
	if ($is_path == 'counsel'){
		include_once("../inc/_header.php");
		include_once("../inc/_http_uri.php");
		include_once("../inc/_myFun.php");
		include_once('../inc/_ed.php');

		$code	= $_SESSION['userCenterCode'];
		$kind	= $conn->center_kind($code);
		$name	= $conn->center_name($code, $kind);
	}

	if ($is_path != 'counsel'){
		if (!Empty($jumin)){
			$sql = "select client_dt, client_seq
					  from counsel_client
					 where org_no     = '$code'
					   and client_ssn = '$jumin'
					 order by client_dt desc, client_seq desc
					 limit 1";

			$tmp = $conn->get_array($sql);

			$counsel_dt   = $tmp[0];
			$counsel_seq  = $tmp[1];
		}else{
			$counsel_dt   = Date('Y-m-d');
			$counsel_seq  = 0;
		}
	}else{
		$counsel_dt   = $_REQUEST['counsel_dt'];
		$counsel_seq  = $_REQUEST['counsel_seq'];
	}

	if (!$counsel_dt)
		$counsel_dt = date('Y-m-d', mktime());

	if (!$counsel_seq)
		$counsel_seq = 0;

	$sql = "select client_counsel
			  from counsel_client
			 where org_no     = '$code'
			   and client_dt  = '$counsel_dt'
			   and client_seq = '$counsel_seq'
			   and del_flag   = 'N'";

	$counsel_kind = $conn->get_data($sql);

	if (empty($counsel_kind)) $counsel_kind = 0;

	if ($is_path == 'counsel'){?>
		<script language='javascript'>
		<!--

		function list(){
			var f = document.f;

			f.action = 'client_counsel.php';
			f.submit();
		}

		function form_reset(){
			if (!__message('reset')) return;

			document.f.reset();
		}

		function form_delete(){
			if (!__message('delete')) return;
		}

		function init(){
			try{__setEnabled('normal_protect_other',__object_get_value('normal_protect_gbn')== '9' ? true : false);}catch(e){}
			try{__setEnabled('family_other',		__object_get_value('family_gbn')		== '9' ? true : false);}catch(e){}
			try{__setEnabled('normal_mind_other',	__object_get_value('normal_mind')		== '9' ? true : false);}catch(e){}
			try{__setEnabled('normal_use_other',	__object_get_value('normal_use_center')	== '1' ? false: true);}catch(e){}

			set_baby_dis_kind();
		}
		
		function lfPostno(){
			$.ajax({
				type:'POST',
				url:'../find/postno.php',
				data:{
					'rstFun':'lfPostnoRst'
				},
				beforeSend: function (){
					$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
				},
				success:function(html){
					$('#ID_LOCAL_POP_DATA').html(html);
					$('#ID_LOCAL_POP')
						.css('left','300px')
						.css('top','200px')
						.css('width','600px')
						.css('height','500px')
						.show();
					$('#tempLodingBar').remove();
				},
				error: function (request, status, error){
					$('#tempLodingBar').remove();

					alert('[ERROR]'
						 +'\nCODE : ' + request.status
						 +'\nSTAT : ' + status
						 +'\nMESSAGE : ' + request.responseText);
				}
			}).responseXML;
		}

		function lfPostnoRst(gbn, postno, lnaddr, rnaddr){
			$('#counsel_postno').val(postno);
			$('#counsel_addr').val(lnaddr+'\n'+rnaddr);
			$('#counsel_addr_dtl').val('');

			$('#ID_LOCAL_POP').hide();
			$('#ID_LOCAL_POP_DATA').html('');
		}


		window.onload = function(){
			init();
		}

		//-->
		</script>

		<form name="f" method="post" enctype="multipart/form-data">

		<div class="title">초기상담(욕구사정) 기록지(고객)</div><?
	}

	include_once('../counsel/client_counsel_head.php');
?>

<script language='javascript'>

var family_tbl = new Table();

// 상담구분 선택
function set_counsel_kind(counsel_kind){
	var f            = document.f;
	var code         = f.code.value;
	var counsel_dt   = f.counsel_dt.value;
	var counsel_seq  = f.counsel_seq.value;
	var counsel_path = f.counsel_path.value;
	var params = {'code':code, 'counsel_dt':counsel_dt, 'counsel_seq':counsel_seq, 'counsel_kind':counsel_kind, 'is_path':counsel_path};
	var URL = '../counsel/client_counsel_kind.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:params,
			onSuccess:function (responseHttpObj) {
				document.getElementById('counsel_body').innerHTML = responseHttpObj.responseText;

				var counsel_tab            = document.getElementsByName('counsel_kind');
				//var counsel_normal         = document.getElementById('normal_counsel_div');
				//var counsel_baby           = document.getElementById('baby_counsel_div');
				var counsel_protect_normal = document.getElementById('normal_protect');
				var counsel_protect_baby   = document.getElementById('baby_protect');
				var counsel_normal_talk    = document.getElementById('counsel_normal');
				var counsel_baby_talk      = document.getElementById('counsel_baby');

				var counsel_temp =  document.getElementById('counsel_kind');

				if (typeof(counsel_temp) == 'object'){
					var counsel_type = counsel_temp.getAttribute('type');
				}else{
					var counsel_type = 'radio';
				}

				if (counsel_kind != 3){
					if (counsel_type == 'radio') counsel_tab[counsel_kind].checked = true;
					//counsel_normal.style.display         = '';
					//counsel_baby.style.display           = 'none';
					counsel_protect_normal.style.display = '';
					counsel_protect_baby.style.display   = 'none';
					counsel_normal_talk.style.display    = '';
					counsel_baby_talk.style.display      = 'none';
				}else{
					if (counsel_type == 'radio') counsel_tab[counsel_kind].checked = true;
					//counsel_normal.style.display         = 'none';
					//counsel_baby.style.display           = '';
					counsel_protect_normal.style.display = 'none';
					counsel_protect_baby.style.display   = '';
					counsel_normal_talk.style.display    = 'none';
					counsel_baby_talk.style.display      = '';
				}

				var family_cnt = document.getElementById('family_cnt').value;

				//가족사항
				family_tbl.class_nm	= 'family_tbl';
				family_tbl.table_nm	= 'tbl_family';
				family_tbl.body_nm	= 'my_family';
				family_tbl.row_nm	= 'family_row';
				family_tbl.span_nm	= 'family_span';
				family_tbl.row_count= family_cnt;
				family_tbl.tabindex	= 41;
				family_tbl.column	= new Array(new Array('family_name[]', '', 'focus'),
												new Array('family_relation[]', '', 'focus'),
												new Array('family_age[]', '', 'focus'),
												new Array('family_job[]', '', 'focus'),
												new Array('family_together[]', 'select', 'focus', new Array(new Array('Y','예', 'selected'), new Array('N', '아니오', ''))),
												new Array('family_salary[]', 'number', 'add'),
												new Array('delete','button'));

				__init_form(document.f);
			}
		}
	);
}

// 년월 중복입력 확인
function check_yymm(row_id){
	var other_dt   = document.getElementsByName('other_dt[]');
	var tbl_row_id = document.getElementsByName('back_row_id[]');
	var delete_yn  = document.getElementsByName('delete_yn[]');

	var index = 0;

	for(var i=0; i<tbl_row_id.length; i++){
		if (tbl_row_id[i].value == row_id){
			index = i;
			break;
		}
	}

	for(var i=0; i<other_dt.length; i++){
		if (i != index){
			if (other_dt[i].value == other_dt[index].value){
				alert('입력하신 년월은 이미 등록된 년월입니다. 확인 후 다시 입력하여 주십시오.');
				other_dt[index].value = '';
				other_dt[index].focus();
				break;
			}
		}
	}
}

// 장애구분
function set_baby_dis_kind(){
	var baby_dis_kind = document.getElementsByName('baby_dis_kind');
	var baby_dis_lvl  = __object_get_value('baby_dis_lvl');

	for(var i=0; i<baby_dis_kind.length; i++)
		try{__setEnabled(baby_dis_kind[i], baby_dis_lvl == 'N' ? false : true);}catch(e){}
}

// 리스트
function list(){
	var f = document.f;

	f.action = 'client_counsel.php';
	f.submit();
}

function form_reset(){
	if (!__message('reset')) return;

	document.f.reset();
}

function form_delete(){
	if (!__message('delete')) return;
}

function form_copy(){
	var f = document.f;
	
	if (__replace(f.counsel_name.value, ' ', '') == ''){
		alert('성명을 입력하여 주십시오.');
		f.counsel_name.focus();
		return;
	}

	f.action = '../counsel/client_counsel_copy.php';
	f.submit();
}

function form_save(){
	var f = document.f;

	if (__replace(f.counsel_name.value, ' ', '') == ''){
		alert('성명을 입력하여 주십시오.');
		f.counsel_name.focus();
		return;
	}

	/*
	try{
		if (!__isSSN(f.counsel_ssn1.value, f.counsel_ssn2.value)){
			alert('주민번호를 올바르게 입력하여 주십시오.');
			f.ssn1.focus();
			return;
		}
	}catch(e){
	}
	*/

	f.action = '../counsel/client_counsel_save.php';
	f.submit();
}

</script>

<?
	echo '<div style=\'margin:10px;\'>';

	include('../counsel/client_counsel_btn.php');

	echo '<div id=\'counsel_body\'></div>';

	include('../counsel/client_counsel_btn.php');

	echo '</div>';
?>

<div id="ID_LOCAL_POP" style="position:absolute; left:0; top:0; width:0; height:0; display:none; z-index:11; background:url('../image/tmp_bg.png'); border:2px solid #4374D9;">
	<div style="position:absolute; text-align:right; width:100%; top:-20px; left:-5px;">
		<a href="#" onclick="$('#ID_LOCAL_POP').hide();"><img src="../image/btn_exit.png"></a>
	</div>
	<div id="ID_LOCAL_POP_DATA" style="position:absolute; width:100%;"></div>
</div>
<input name="counsel_mode" type="hidden" value="<?=$counsel_mode;?>">
<input name="counsel_path" type="hidden" value="<?=$is_path;?>">

<input id="counsel_dt" name="counsel_dt" type="hidden" value="<?=$counsel_dt;?>">
<input id="counsel_seq" name="counsel_seq" type="hidden" value="<?=$counsel_seq;?>">
<input id="counsel_kind" name="counsel_kind" type="hidden" value="<?=$counsel_kind;?>">

<input name="find_nm" type="hidden" value="<?=$_POST['find_nm'];?>">

<?
	if (Is_Array($_POST['find_type'])){
		foreach($_POST['find_type'] as $tmpFindType){?>
			<input name="find_type[]" type="hidden" value="<?=$tmpFindType;?>"><?
		}
	}

	if ($is_path == 'counsel'){?>
			<input name="code" type="hidden" value="<?=$code;?>">
			<input name="kind" type="hidden" value="<?=$kind;?>">
		</form><?
	}

	if ($is_path == 'counsel'){
		include_once("../inc/_footer.php");
	}

?>
<script language='javascript'>
	set_counsel_kind($('#counsel_kind').attr('value'));
</script>