<?
	##################################################################
	#
	# 초기상담기록지를 보여줄 레이어를 작성한다.
	#
	##################################################################
?>
<script language='javascript'>
<!--

var family_tbl = new Table();

function init_counsel(){
	//초기화
}

function show_counsel(){
	var f = document.f;
	/*
	var count = f.elements.length;

	for(var i=0; i<count; i++){
		var el = f.elements[i];

		if (el.name.indexOf('use_svc_') >= 0){
			var el_code = __get_svc_code(el);
			var body = document.getElementById('svc_body_'+el_code);

			//el.checked = false;

			body.style.width = 0;
			body.style.display = '';
			body.style.position = 'absolute';
		}
	}
	*/

	/*
	document.getElementById('menu_1').style.fontWeight = 'bold';
	document.getElementById('menu_2').style.fontWeight = 'normal';

	document.getElementById('stnd_body').style.left = -10000;
	document.getElementById('stnd_body').style.position = 'absolute';
	document.getElementById('svc_counsel').style.display = '';
	*/

	show_svc_layer('counsel');
}

//초기상당기록지 레이어 다딕
function counsel_close(){
	var body = document.getElementById('layer_counsel_body');
	var cont = document.getElementById('layer_counsel_cont');

	body.style.left = -10000;
	cont.style.left = -10000;
}

//초기상담기록지
function counsel_view(code, counsel_dt, counsel_seq, counsel_kind){
	var params = {'code':code, 'counsel_dt':counsel_dt, 'counsel_seq':counsel_seq, 'counsel_kind':counsel_kind, 'counsel_path':'client_reg'};
	var URL = '../counsel/client_counsel_kind.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:params,
			onSuccess:function (responseHttpObj) {
				document.getElementById('layer_counsel_cont').innerHTML = responseHttpObj.responseText;

				var counsel_tab            = document.getElementsByName('counsel_kind');
				//var counsel_normal         = document.getElementById('normal_counsel_div');
				//var counsel_baby           = document.getElementById('baby_counsel_div');
				var counsel_protect_normal = document.getElementById('normal_protect');
				var counsel_protect_baby   = document.getElementById('baby_protect');
				var counsel_normal_talk    = document.getElementById('counsel_normal');
				var counsel_baby_talk      = document.getElementById('counsel_baby');

				if (counsel_kind == 1){
					//counsel_normal.style.display         = '';
					//counsel_baby.style.display           = 'none';
					counsel_protect_normal.style.display = '';
					counsel_protect_baby.style.display   = 'none';
					counsel_normal_talk.style.display    = '';
					counsel_baby_talk.style.display      = 'none';
				}else{
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
												new Array('family_age[]', 'number', 'focus'),
												new Array('family_job[]', '', 'focus'),
												new Array('family_together[]', 'select', 'focus', new Array(new Array('Y','예', 'selected'), new Array('N', '아니오', ''))),
												new Array('family_salary[]', 'number', 'add'),
												new Array('delete','button'));

				__init_form(document.f);
			}
		}
	);
}

-->
</script>

<div id="svc_counsel" style="margin-bottom:10px; display:none;">
<?
	include_once('../counsel/client_counsel_reg_sub.php');
?>
</div>