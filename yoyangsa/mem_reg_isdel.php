<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code = $_POST['code'];
	$ssn  = $ed->de($_POST['ssn']);

	$sql = 'select (select count(*)
					  from t01iljung
					 where t01_ccode   = \''.$code.'\'
					   and t01_mem_cd1 = \''.$ssn.'\'
					   and t01_del_yn  = \'N\')
			+      (select count(*)
					  from t01iljung
					 where t01_ccode   = \''.$code.'\'
					   and t01_mem_cd2 = \''.$ssn.'\'
					   and t01_del_yn  = \'N\')

			+      (select count(*)
					  from t01iljung
					 where t01_ccode        = \''.$code.'\'
					   and t01_yoyangsa_id1 = \''.$ssn.'\'
					   and t01_del_yn       = \'N\')

			+      (select count(*)
					  from t01iljung
					 where t01_ccode        = \''.$code.'\'
					   and t01_yoyangsa_id2 = \''.$ssn.'\'
					   and t01_del_yn       = \'N\') as cnt';

	$cnt = $conn->get_data($sql);
	
	$script = '';

	if ($cnt > 0){
		$script .= 'alert("모든일정을 삭제 후 삭제가 가능합니다."); return;';
	}
	
	$script .= 'if(confirm("삭제후 복구가 불가능합니다.\n\n직원 정보를 정말로 삭제하시겠습니까?")){';
	$script .= 'var xmlhttp = new Ajax.Request(';
	$script .= '"./mem_reg_isdel_ok.php", {';
	$script .= 'method:"post",';
	$script .= 'parameters:{"code":"'.$code.'","jumin":"'.$ed->en($ssn).'"},';
	$script .= 'onSuccess:function(responseHttpObj){if(responseHttpObj.responseText == "Y"){alert("직원정보를 삭제하였습니다."); location.replace("./mem_list.php");}else{alert("삭제중 오류가 발생하였습니다.\n\n나중에 다시 시도하여 주십시오.");}}';
	$script .= '}';
	$script .= ');}';

	echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\''.$script.'\'>직원정보 삭제</button></span>';
	
	echo '</br>
		  <span>※ 일정이 등록된 직원은 </br>&nbsp;&nbsp;&nbsp;삭제할 수 없습니다.</span>';

	include_once('../inc/_db_close.php');
?>