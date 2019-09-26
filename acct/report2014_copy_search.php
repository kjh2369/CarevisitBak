<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_ed.php');

	/*********************************************************
	 *	고객조회
	 *********************************************************/
	$code	= $_POST['code'];
	$ssn    = $ed->de($_POST['ssn']);
	$seq    = $_POST['seq'];
	
	/* 낙상위험도평가도구 */
	$sql = 'select count(*) 
			  from report_falltest
			 where org_no   = \''.$code.'\'
			   and jumin    = \''.$ssn.'\'
			   and seq      = \''.$seq.'\'
			   and del_flag = \'N\'';
	
	$ddt_cnt = $conn -> get_data($sql);			

	/* 욕창위험도평가도구 */
	$sql = 'select count(*) 
			  from r_cltpst
			 where org_no   = \''.$code.'\'
			   and r_c_id   =  \''.$ssn.'\'
			   and r_seq    = \''.$seq.'\'
			   and del_flag = \'N\'';
	$pst_cnt = $conn -> get_data($sql);	

	/* 욕구사정 */
	$sql = 'select count(*) 
			  from report_na
			 where org_no   = \''.$code.'\'
			   and jumin    = \''.$ssn.'\'
			   and seq      = \''.$seq.'\'
			   and del_flag = \'N\'';
	$bsr_cnt = $conn -> get_data($sql);

	/* 급여계획 */
	$sql = 'select count(*) 
			  from report_plan_mst
			 where org_no   = \''.$code.'\'
			   and jumin    = \''.$ssn.'\'
			   and seq      = \''.$seq.'\'
			   and del_flag = \'N\'';
	$plan_cnt = $conn -> get_data($sql);

	/* 표준장기이용계획서 */
	$sql = 'select count(*) 
			  from report_sppc_mst
			 where org_no   = \''.$code.'\'
			   and jumin    = \''.$ssn.'\'
			   and seq      = \''.$seq.'\'
			   and del_flag = \'N\'';
	$agr_cnt = $conn -> get_data($sql);


	echo $ddt_cnt.'//'.$pst_cnt.'//'.$bsr_cnt.'//'.$plan_cnt.'//'.$agr_cnt;

?>