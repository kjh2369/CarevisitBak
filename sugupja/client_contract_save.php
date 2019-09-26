<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');
	
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
	$page = $_POST['page']; 
	$current_menu = $_POST['current_menu'];		
	$code = $_POST['code'];										//기관기호	
	$seq  = $_POST['seq'];										//순번
	$ssn  = $ed->de($_POST['ssn']);								//주민	
	$kind = $_POST['kind'];										//서비스코드
	$reg_id = $_SESSION['userCode'];							//작성자아이디
	$reg_dt = $_POST['reg_dt'];									//작성일자
	$insert_dt = date('Y-m-d', mktime());						//삽입일자
	$from_dt = $_POST['from_dt'];								//계약시작기간
	$to_dt = $_POST['to_dt'];									//계약종료기간
	$svc_key  = $_POST['svc_key'];								//계약기간(키)
	$from_time1 = str_replace(':', '', $_POST['from_time1']);	//이용시간1(시작)
	$to_time1 = str_replace(':', '', $_POST['to_time1']);		//이용시간1(종료)
	$from_time2 = str_replace(':', '', $_POST['from_time2']);	//이용시간2(시작)
	$to_time2 = str_replace(':', '', $_POST['to_time2']);		//이용시간2(종료)
	$from_time4 = str_replace(':', '', $_POST['from_time4']);	//이용시간(주야간보호)(시작)
	$to_time4 = str_replace(':', '', $_POST['to_time4']);		//이용시간(주야간보호)(종료)
	$from_time1_nurse = str_replace(':', '', $_POST['from_time1_nurse']);	//간호이용시간1(시작)
	$to_time1_nurse = str_replace(':', '', $_POST['to_time1_nurse']);		//간호이용시간1(종료)
	$from_time2_nurse = str_replace(':', '', $_POST['from_time2_nurse']);	//간호이용시간2(시작)
	$to_time2_nurse = str_replace(':', '', $_POST['to_time2_nurse']);		//간호이용시간2(종료)
	
	$use_yoil1 = '';
	for($i=1; $i<=7; $i++) $use_yoil1 .= $_POST['use_yoil1_'.$i] == 'Y' ? 'Y' : 'N';	//이용요일1

	$use_yoil2 = '';
	for($i=1; $i<=7; $i++) $use_yoil2 .= $_POST['use_yoil2_'.$i] == 'Y' ? 'Y' : 'N';	//이용요일2
	
	//방문목욕 계약서
	$use_yoil3 = '';
	for($i=1; $i<=7; $i++) $use_yoil3 .= $_POST['use_yoil3_'.$i] == 'Y' ? 'Y' : 'N';	//이용요일2(목욕)
	
	$use_yoil1_nurse = '';
	for($i=1; $i<=7; $i++) $use_yoil1_nurse .= $_POST['use_yoil1_nurse'.$i] == 'Y' ? 'Y' : 'N';	//이용요일1(간호)

	$use_yoil2_nurse = '';
	for($i=1; $i<=7; $i++) $use_yoil2_nurse .= $_POST['use_yoil2_nurse'.$i] == 'Y' ? 'Y' : 'N';	//이용요일2(간호)
	
	$use_yoil4 = '';
	for($i=1; $i<=7; $i++) $use_yoil4 .= $_POST['use_yoil4_'.$i] == 'Y' ? 'Y' : 'N';	//이용요일(주야간보호)
	
	$from_time = str_replace(':', '', $_POST['from_time']);		//이용시간(시작)
	$to_time = str_replace(':', '', $_POST['to_time']);			//이용시간(종료)
	$use_type = $_POST['use_type'];								//이용방법(월)
	
	$pay_day1  = $_POST['pay_day1'];	//이용납부일
	$pay_day2  = $_POST['pay_day2'];	//이용세부내역서제출만료일
	$pay_day3  = $_POST['pay_day3'];	//본인부담금납부일
	
	$otherTxt_1 = addslashes($_POST['otherTxt_1']); //별첨(요양)
	$otherTxt_2 = addslashes($_POST['otherTxt_2']); //별첨(목욕)
	

	if ($seq == 0){
		
		$sql = 'select ifnull(max(seq), 0) + 1
				  from client_contract
				 where org_no   = \''.$code.'\'
				   and svc_cd   = \''.$kind.'\'
				   and jumin	= \''.$ssn.'\'';

		$seq = $conn->get_data($sql);

		$sql = 'insert into client_contract (
				 org_no
				,svc_cd
				,seq
				,jumin
				,reg_dt
				,svc_seq
				,from_dt
				,to_dt
				,use_yoil1
				,from_time1
				,to_time1
				,use_yoil2
				,from_time2
				,to_time2
				,use_yoil3
				,from_time3
				,to_time3
				,bath_weekly 
				,from_time
				,to_time
				,use_yoil1_nurse
				,from_time1_nurse
				,to_time1_nurse
				,use_yoil2_nurse
				,from_time2_nurse
				,to_time2_nurse
				,use_type
				,pay_day1
				,pay_day2
				,pay_day3
				,other_text1
				,other_text2
				,insert_dt
				,insert_id) values (
				 \''.$code.'\'
				,\''.$kind.'\'
				,\''.$seq.'\'
				,\''.$ssn.'\'
				,\''.$reg_dt.'\'
				,\''.$svc_key.'\'
				,\''.$from_dt.'\'
				,\''.$to_dt.'\'
				,\''.$use_yoil1.'\'
				,\''.$from_time1.'\'
				,\''.$to_time1.'\'
				,\''.$use_yoil2.'\'
				,\''.$from_time2.'\'
				,\''.$to_time2.'\'
				,\''.$use_yoil4.'\'
				,\''.$from_time4.'\'
				,\''.$to_time4.'\'
				,\''.$use_yoil3.'\'
				,\''.$from_time.'\'
				,\''.$to_time.'\'
				,\''.$use_yoil1_nurse.'\'
				,\''.$from_time1_nurse.'\'
				,\''.$to_time1_nurse.'\'
				,\''.$use_yoil2_nurse.'\'
				,\''.$from_time2_nurse.'\'
				,\''.$to_time2.'\'
				,\''.$use_type.'\'
				,\''.$pay_day1.'\'
				,\''.$pay_day2.'\'
				,\''.$pay_day3.'\'
				,\''.$otherTxt_1.'\'
				,\''.$otherTxt_2.'\'
				,\''.$insert_dt.'\'
				,\''.$reg_id.'\')';
		
		if (!$conn->execute($sql)){
			$conn->rollback();
			echo $conn->err_back();
			if ($conn->mode == 1) exit;
		}
	
	}else{
		
		$sql = 'update client_contract
				   set reg_dt					= \''.$reg_dt.'\'
				,      svc_seq					= \''.$svc_key.'\'
				,      from_dt					= \''.$from_dt.'\'
				,      to_dt					= \''.$to_dt.'\'
				,      use_yoil1				= \''.$use_yoil1.'\'
				,      from_time1				= \''.$from_time1.'\'
				,      to_time1					= \''.$to_time1.'\'
				,      use_yoil2				= \''.$use_yoil2.'\'
				,      from_time2				= \''.$from_time2.'\'
				,      to_time2					= \''.$to_time2.'\'
				,      use_yoil3				= \''.$use_yoil4.'\'
				,      from_time3				= \''.$from_time4.'\'
				,      to_time3					= \''.$to_time4.'\'
				,      bath_weekly				= \''.$use_yoil3.'\'
				,      from_time				= \''.$from_time.'\'
				,      to_time					= \''.$to_time.'\'
				,      use_yoil1_nurse			= \''.$use_yoil1_nurse.'\'
				,      from_time1_nurse			= \''.$from_time1_nurse.'\'
				,      to_time1_nurse			= \''.$to_time1_nurse.'\'
				,      use_yoil2_nurse			= \''.$use_yoil2_nurse.'\'
				,      from_time2_nurse			= \''.$from_time2_nurse.'\'
				,      to_time2_nurse			= \''.$to_time2_nurse.'\'
				,      use_type					= \''.$use_type.'\'
				,      pay_day1					= \''.$pay_day1.'\'
				,      pay_day2					= \''.$pay_day2.'\'
				,      pay_day3					= \''.$pay_day3.'\'
				,      other_text1				= \''.$otherTxt_1.'\'
				,      other_text2				= \''.$otherTxt_2.'\'
				,	   update_dt				= \''.$insert_dt.'\'
				,	   update_id				= \''.$reg_id.'\'
				 where org_no					= \''.$code.'\'
				   and svc_cd                   = \''.$kind.'\'
				   and jumin					= \''.$ssn.'\'
				   and seq						= \''.$seq.'\'';
		
		if (!$conn->execute($sql)){
			$conn->rollback();
			echo $conn->err_back();
			if ($conn->mode == 1) exit;
		}
	}
	
	echo '<script>';
	echo 'alert(\''.$myF->message('ok','N').'\');';

	
	echo 'location.replace(\'../sugupja/client_new.php?code='.$code.'&kind='.$kind.'&jumin='.$ed->en($ssn).'&page='.$page.'&current_menu='.$current_menu.'\');';
	
	echo '</script>';

?>