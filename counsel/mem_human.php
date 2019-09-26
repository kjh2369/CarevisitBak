<?
	$human_mode = 1; //리스트
	$human_ssn  = $jumin;
?>

<script type="text/javascript" src="../js/report.js"></script>
<script language='javascript'>
<!--

var human_rec = new Table();
var human_edu = new Table();
var human_lcs = new Table();
var human_rnp = new Table();

function go_human_reg(seq){
	var param = {'code':'<?=$code;?>','ssn':'<?=$ed->en($human_ssn);?>','seq':seq};
	var URL = '../counsel/mem_human_reg.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function (responseHttpObj) {
				var obj = document.getElementById('my_human');
					obj.innerHTML = responseHttpObj.responseText;

				document.getElementById('human_mode').value = '0';
				set_class_human_edu();

				__init_form(document.f);
			}
		}
	);
}

function go_human_show(){
	var param = {'m_cd':document.getElementById('para_m_cd').value,'seq':seq};

	_report_show_pdf(1, param, '');
}

function go_human_del(seq){
	var param = {'code':'<?=$code;?>','ssn':'<?=$ed->en($human_ssn);?>','seq':seq};
	var URL = '../counsel/mem_human_del.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function (responseHttpObj) {
				var result = responseHttpObj.responseText;

				if (result == 'Y'){
					alert('선택하신 상담이력이 삭제되었습니다.');
					go_human_list();
				}else{
					alert('상담이력 삭제 중 오류가 발생되었습니다. 잠시 후 다시 시도하여 주십시오.');
				}
			}
		}
	);
}

function go_human_list(){
	var param = {'code':'<?=$code;?>','ssn':'<?=$ed->en($human_ssn);?>'};
	var URL = '../counsel/mem_human_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function (responseHttpObj) {
				var obj = document.getElementById('my_human');
					obj.innerHTML = responseHttpObj.responseText;

				document.getElementById('human_mode').value = '1';
			}
		}
	);
}

/******************************

2012.08.30 추가수정
입사전기록
변수 : rec_cnt, rec_head_cnt

*******************************/

function set_class_human_edu(){
	var edu_cnt      = 0;
	var edu_head_cnt = 1;	
	var lcs_cnt      = 0;
	var lcs_head_cnt = 1;
	var rnp_cnt      = 0;
	var rec_cnt      = 0;	

	try{
		edu_cnt = parseInt(document.getElementById('human_edu_cnt').value, 10);
	}catch(e){
	}

	try{
		edu_head_cnt = parseInt(document.getElementById('human_head_edu_cnt').value, 10);
	}catch(e){
	}

	try{
		lcs_cnt = parseInt(document.getElementById('human_lcs_cnt').value, 10);
	}catch(e){
	}

	try{
		lcs_head_cnt = parseInt(document.getElementById('human_head_lcs_cnt').value, 10);
	}catch(e){
	}

	try{
		rnp_cnt = parseInt(document.getElementById('human_rnp_cnt').value, 10);
	}catch(e){
	}
	
	try{
		rec_cnt = parseInt(document.getElementById('human_rec_cnt').value, 10);
	}catch(e){
	}


	//교육이수
	human_edu.class_nm	= 'human_edu';
	human_edu.table_nm	= 'human_edu_tbl';
	human_edu.body_nm	= 'edu_human_my';
	human_edu.row_nm	= 'edu_human_row';
	human_edu.span_nm	= 'edu_human_span';
	human_edu.head_cnt  = edu_head_cnt;
	human_edu.row_count = edu_cnt;
	human_edu.tabindex	= 51;
	human_edu.first_add = false;
	human_edu.column	= new Array(new Array('edu_human_gbn[]', 'select', 'focus', new Array(new Array('1','돌봄관련교육', 'selected'), new Array('9', '기타교육', ''))),
									new Array('edu_human_center[]', '', 'focus'),
									new Array('edu_human_name[]', '', 'focus'),
									new Array('edu_human_from_date[]', 'date', 'focus'),
									new Array('edu_human_to_date[]', 'date', 'focus'),
									new Array('edu_human_date[]', '', 'add'),
									new Array('delete','button'));


	
	/******************************

	2012.08.30 입사전기록 추가 
	
	*******************************/

	human_rec.class_nm	= 'human_rec';
	human_rec.table_nm	= 'human_rec_tbl';
	human_rec.body_nm	= 'rec_human_my';
	human_rec.row_nm	= 'rec_human_row';
	human_rec.span_nm	= 'rec_human_span';
	human_rec.row_count = rec_cnt;
	human_rec.tabindex	= 61;
	human_rec.first_add = false;
	human_rec.column	= new Array(new Array('rec_human_fm_dt[]', 'date', 'focus'),
									new Array('rec_human_to_dt[]', 'date', 'focus'),
									new Array('rec_human_job_nm[]', '', 'focus'),
									new Array('rec_human_position[]', '', 'focus'),
									new Array('rec_human_task[]', '', 'focus'),
									new Array('rec_human_salary[]', '', 'add'),
									new Array('delete','button'));

	
	//자격
	human_lcs.class_nm	= 'human_lcs';
	human_lcs.table_nm	= 'human_lcs_tbl';
	human_lcs.body_nm	= 'lcs_human_my';
	human_lcs.row_nm	= 'lcs_human_row';
	human_lcs.span_nm	= 'lcs_human_span';
	human_lcs.head_cnt  = lcs_head_cnt;
	human_lcs.row_count = lcs_cnt;
	human_lcs.tabindex	= 61;
	human_lcs.first_add = false;
	human_lcs.column	= new Array(new Array('lcs_human_type[]', '', 'focus'),
									new Array('lcs_human_no[]', '', 'focus'),
									new Array('lcs_human_center[]', '', 'focus'),
									new Array('lcs_human_date[]', 'date', 'add'),
									new Array('delete','button'));


	//상벌
	human_rnp.class_nm	= 'human_rnp';
	human_rnp.table_nm	= 'human_rnp_tbl';
	human_rnp.body_nm	= 'rnp_human_my';
	human_rnp.row_nm	= 'rnp_human_row';
	human_rnp.span_nm	= 'rnp_human_span';
	human_rnp.row_count = rnp_cnt;
	human_rnp.tabindex	= 61;
	human_rnp.first_add = false;
	human_rnp.column	= new Array(new Array('rnp_human_date[]', 'date', 'focus'),
									new Array('rnp_human_kind', 'radio', 'focus', new Array(new Array('R','포상','checked'),new Array('P','징계',''))),
									new Array('rnp_human_cont[]', '', 'focus'),
									new Array('delete','button'));
}

-->
</script>

<div id="my_human">

</div>

<?
	###########################################################
	# 환경변수

	echo '<input name=\'human_mode\' type=\'hidden\' value=\''.$human_mode.'\'>';

	###########################################################
?>

<script language='javascript'>
<!--

go_human_reg(0);

-->
</script>