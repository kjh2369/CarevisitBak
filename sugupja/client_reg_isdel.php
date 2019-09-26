<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$jumin = $ed->de($_POST['jumin']);
	
	$sql = 'select count(*)
			  from t01iljung
			 where t01_ccode = \''.$code.'\'
			   and t01_jumin = \''.$jumin.'\'
			   and t01_del_yn  = \'N\'';

	$cnt = $conn->get_data($sql);
	
	$script  = '';

	if ($cnt > 0){	
		$script .= 'alert("모든일정을 삭제 후 삭제가 가능합니다."); return;';
	}
		
	$script .= 'if(confirm("삭제후 복구가 불가능합니다.\n\n고객 정보를 정말로 삭제하시겠습니까?")){';
	$script .= 'var xmlhttp = new Ajax.Request(';
	$script .= '"./client_reg_isdel_ok.php", {';
	$script .= 'method:"post",';
	$script .= 'parameters:{"code":"'.$code.'","jumin":"'.$ed->en($jumin).'"},';
	$script .= 'onSuccess:function(responseHttpObj){if(responseHttpObj.responseText == "Y"){alert("고객정보를 삭제하였습니다."); location.replace("./client_list.php");}else{alert(responseHttpObj.responseText+".삭제중 오류가 발생하였습니다.\n\n나중에 다시 시도하여 주십시오.");}}';
	$script .= '}';
	$script .= ');}';

	echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\''.$script.'\'>고객정보 삭제</button></span>';
	echo '</br>
		  <span>※ 일정이 등록된 고객은 삭제할 수 없습니다.</span>';
	
	include_once('../inc/_db_close.php');
?>