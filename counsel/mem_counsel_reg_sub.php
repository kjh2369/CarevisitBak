<?
	include_once('mem_counsel_head.php');

	if ($is_path == 'counsel'){?>
		<script language='javascript'>
		<!--

		function list(){
			var f = document.f;

			f.action = 'mem_counsel.php';
			f.submit();
		}

		function form_reset(){
			if (!__message('reset')) return;

			document.f.reset();
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
			__init_form(document.f);

			_setDisabled(__object_check('counsel_religion'), document.getElementById('counsel_rel_other'));
			_setDisabled(__object_check('counsel_app_path'), document.getElementById('counsel_app_other'));
			__setEnabled('counsel_hope_other', document.getElementById('counsel_hope_work6').checked);
			__setEnabled('counsel_service_other', __object_get_value('counsel_service_work') == 'Y' ? true : false);

			document.f.counsel_name.focus();
		}

		//-->
		</script>
		<form name="f" method="post" enctype="multipart/form-data">
		<div class="title">
			<div>초기상담기록지(직원)</div>
		</div>
		<table class="my_table my_border">
			<colgroup>
				<col width="100px">
				<col width="130px">
				<col width="100px">
				<col>
			</colgroup>
			<tbody>
				<tr>
					<th>기관기호</th>
					<td class="left"><?=$_SESSION['userCenterGiho'];?></td>
					<th>기관명</th>
					<td class="left last"><?=$name;?></td>
				</tr>
			</tbody>
		</table><?
	}
?>

<script language='javascript'>

function form_save(){
	var f = document.f;
	/*
	if (f.counsel_mode.value == 1){
		if (f.ssn2.value.substring(0,1) == '5' ||
			f.ssn2.value.substring(0,1) == '6' ||
			f.ssn2.value.substring(0,1) == '7' ||
			f.ssn2.value.substring(0,1) == '8'){
			var skip = true;
		}else{
			var skip = false;
		}
	}
	*/
	//if(!skip){
		if (f.counsel_mode.value == 1){	
			if (f.ssn1.value.length == 6 && f.ssn2.value.length == 7){
			}else{
				alert('주민번호를 올바르게 입력하여 주십시오.');
				f.ssn1.focus();
				return;
			}
		}
	//}

	if (__replace(f.counsel_name.value, ' ', '') == ''){
		alert('성명을 입력하여 주십시오.');
		f.counsel_name.focus();
		return;
	}
	
	f.action = '../counsel/mem_counsel_save.php';
	f.submit();
}

function form_delete(){
	var f = document.f;
	
	if (!__message('delete')) return;
	
	f.type_mode.value = 'del';
	
	f.action = '../counsel/mem_counsel_delete.php';
	f.submit();
	
}


//주민번호 확인
function check_ssn(ssn1, ssn2){
	var code = document.getElementById('code');
	var ssn1 = document.getElementById(ssn1);
	var ssn2 = document.getElementById(ssn2);
	
	/*
	if (ssn1.value.length != 6 || ssn2.value.length != 7) return false;
	
	
	if (ssn2.value.substring(0,1) == '5' ||
		ssn2.value.substring(0,1) == '6' ||
		ssn2.value.substring(0,1) == '7' ||
		ssn2.value.substring(0,1) == '8'){
		var skip = true;
	}else{
		var skip = false;
	}
	
	
	if(!skip){
		//if (!__isSSN(ssn1.value, ssn2.value)){
			alert('올바른 형식의 주민번호를 입력하여 주십시오.');
			//ssn1.value = '';
			//ssn2.value = '';
			ssn1.focus();

			return false;
		//}
	}
	*/
	

	var rst = getHttpRequest('../inc/_chk_ssn.php?id=100&code='+code.value+'&ssn='+ssn1.value+ssn2.value);

	if (rst == 'Y'){
		//if ('<?=$is_path;?>' == 'counsel'){
			alert('입력하신 주민번호는 이미등록 주민번호입니다. 확인 후 다시 입력하여 주십시오.');
			ssn1.value = '';
			ssn2.value = '';
			ssn1.focus();

			return false;
		//}else{
		//	var ssn = getHttpRequest('../inc/_check_class.php?check=ed&cd='+ssn1.value+ssn2.value);

		//	find_counsel(ssn);
		//}
	}
}


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

</script>

<?
	if ($is_path == 'counsel'){
		include_once('../counsel/mem_counsel_info.php');
	}
?>


<?
	include('../counsel/mem_counsel_btn.php');
?>

<div id="counsel_body">
<?
	if ($is_path != 'counsel'){
		include_once('../counsel/mem_counsel_info_sub.php');
	}

	include_once('../counsel/mem_counsel_body.php');
?>

<div style="<?=$is_path == 'counsel' ? 'margin-bottom:10px;' : '';?>">
<?
	include('../counsel/mem_counsel_btn.php');
?>
</div>

</div>
<div id="ID_LOCAL_POP" style="position:absolute; left:0; top:0; width:0; height:0; display:none; z-index:11; background:url('../image/tmp_bg.png'); border:2px solid #4374D9;">
	<div style="position:absolute; text-align:right; width:100%; top:-20px; left:-5px;">
		<a href="#" onclick="$('#ID_LOCAL_POP').hide();"><img src="../image/btn_exit.png"></a>
	</div>
	<div id="ID_LOCAL_POP_DATA" style="position:absolute; width:100%;"></div>
</div>
<input name="type_mode" type="hidden" value="">
<input name="counsel_mode" type="hidden" value="<?=$counsel_mode;?>">
<input name="counsel_path" type="hidden" value="<?=$is_path;?>">
<?
	if ($is_path == 'counsel'){?>
			<input name="code" type="hidden" value="<?=$code;?>">
			<input name="kind" type="hidden" value="<?=$kind;?>">
		</form><?
	}

	if ($is_path == 'counsel'){
		include_once("../inc/_footer.php");
	}

	// 가젹증종류 리스트
	ob_start();

	echo '<script language=\'javascript\'>';
	echo 'var license = new Array();';

	for($i=0; $i<$license_cnt; $i++){
		echo 'license['.$i.'] = new Array(\''.$license_list[$i][0].'\', \''.$license_list[$i][1].'\', \'\');';
	}

	echo '</script>';

	$value = ob_get_contents();

	ob_end_clean();

	echo $value;
?>

<script language='javascript'>

//가족사항
var family_tbl = new Table();
	family_tbl.class_nm	= 'family_tbl';
	family_tbl.table_nm	= 'tbl_family';
	family_tbl.body_nm	= 'my_family';
	family_tbl.row_nm	= 'family_row';
	family_tbl.span_nm	= 'family_span';
	family_tbl.row_count= <?=$family_cnt;?>;
	family_tbl.tabindex	= 41;
	family_tbl.column	= new Array(new Array('family_name[]', '', 'focus'),
									new Array('family_relation[]', '', 'focus'),
									new Array('family_age[]', '', 'focus'),
									new Array('family_job[]', '', 'focus'),
									new Array('family_together[]', 'select', 'focus', new Array(new Array('Y','예', 'selected'), new Array('N', '아니오', ''))),
									new Array('family_salary[]', 'number', 'add'),
									new Array('delete','button'));

//교육이수
var edu_tbl = new Table();
	edu_tbl.class_nm	= 'edu_tbl';
	edu_tbl.table_nm	= 'tbl_edu';
	edu_tbl.body_nm		= 'my_edu';
	edu_tbl.row_nm		= 'edu_row';
	edu_tbl.span_nm		= 'edu_span';
	edu_tbl.row_count= <?=$edu_cnt;?>;
	edu_tbl.tabindex	= 51;
	edu_tbl.column		= new Array(new Array('edu_gbn[]', 'select', 'focus', new Array(new Array('1','돌봄관련교육', 'selected'), new Array('9', '기타교육', ''))),
									new Array('edu_center[]', '', 'focus'),
									new Array('edu_name[]', '', 'focus'),
									new Array('edu_date[]', '', 'add'),
									new Array('delete','button'));

//자격
var li_tbl = new Table();
	li_tbl.class_nm	= 'li_tbl';
	li_tbl.table_nm	= 'tbl_license';
	li_tbl.body_nm	= 'my_license';
	li_tbl.row_nm	= 'license_row';
	li_tbl.span_nm	= 'license_span';
	li_tbl.row_count= <?=$li_cnt;?>;
	li_tbl.tabindex	= 61;
	li_tbl.column	= new Array(new Array('license_type[]', '', 'focus'),
								new Array('license_no[]', '', 'focus'),
								new Array('license_center[]', '', 'focus'),
								new Array('license_date[]', 'date', 'add'),
								new Array('delete','button'));
</script>