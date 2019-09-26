<?
	$stress_mode = 1; //리스트
	$stress_mem_ssn = $jumin;
?>

<script type="text/javascript" src="../js/report.js"></script>
<script language='javascript'>
<!--

function go_stress_reg(seq){
	var param = {'code':'<?=$code;?>','ssn':'<?=$ed->en($stress_mem_ssn);?>','seq':seq};
	var URL = '../counsel/mem_stress_reg.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function (responseHttpObj) {
				var obj = document.getElementById('my_stress');
					obj.innerHTML = responseHttpObj.responseText;

				document.getElementById('stress_mode').value = '0';

				set_btn_grp(0);

				__init_form(document.f);
			}
		}
	);
}

function go_stress_show(seq){
	var param = {'m_cd':document.getElementById('para_m_cd').value,'seq':seq};

	_report_show_pdf(1, param, '');
}

function go_stress_del(seq){
	if (!confirm('데이타 삭제후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

	var param = {'code':'<?=$code;?>','ssn':'<?=$ed->en($stress_mem_ssn);?>','seq':seq};
	var URL = '../counsel/mem_stress_del.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function (responseHttpObj) {
				var result = responseHttpObj.responseText;

				if (result == 'Y'){
					alert('선택하신 상담이력이 삭제되었습니다.');
					go_stress_list();
				}else{
					alert('상담이력 삭제 중 오류가 발생되었습니다. 잠시 후 다시 시도하여 주십시오.');
				}
			}
		}
	);
}

function go_stress_list(){
	var param = {'code':'<?=$code;?>','ssn':'<?=$ed->en($stress_mem_ssn);?>'};
	var URL = '../counsel/mem_stress_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function (responseHttpObj) {
				var obj = document.getElementById('my_stress');
					obj.innerHTML = responseHttpObj.responseText;

				document.getElementById('stress_mode').value = '1';

				set_btn_grp(1);
			}
		}
	);
}

function set_btn_grp(idx){
	var btn = document.getElementsByName('grp_stress_btn[]');
	var htm = getHttpRequest('../counsel/mem_stress_btn.php?stress_mode='+idx);

	for(var i=0; i<btn.length; i++){
		btn[i].innerHTML = htm;
	}
}

-->
</script>

<div id="my_stress">

</div>

<?
	###########################################################
	# 환경변수

	echo '<input id=\'stress_mode\' name=\'stress_mode\' type=\'hidden\' value=\''.$stress_mode.'\'>';

	###########################################################
?>

<script language='javascript'>
<!--

go_stress_list();

-->
</script>