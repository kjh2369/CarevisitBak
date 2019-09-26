<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$jumin = $_POST['jumin'];
	$para  = explode('/',$_POST['para']);

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);
	

	if (is_array($para)){
		$conn->begin();

		foreach($para as $var){
			parse_str($var, $val);

			$lsSvcCd = $val['svcCd'];
			
			$sql = 'select t01_svc_subcode as subCd
					  from t01iljung
					 where t01_ccode			= \''.$code.'\'
					   and t01_mkind			= \''.$val['svcCd'].'\'
					   and t01_jumin			= \''.$jumin.'\'
					   and t01_sugup_date		= \''.$val['date'].'\'
					   and t01_sugup_fmtime		= \''.$val['planFrom'].'\'
					   and t01_sugup_seq		= \''.$val['planSeq'].'\'';  
			
			$subCd  = $conn -> get_data($sql);
			
			
			if($subCd == '200'){
				//일정 저장
				$sql = 'update t01iljung
						   set t01_yoyangsa_id2     = \'\'
						,      t01_yname2           = \'\'
						,	   t01_mem_cd2          = \'\'
						,	   t01_mem_nm2			= \'\'
						 where t01_ccode			= \''.$code.'\'
						   and t01_mkind			= \''.$val['svcCd'].'\'
						   and t01_jumin			= \''.$jumin.'\'
						   and t01_sugup_date		= \''.$val['date'].'\'
						   and t01_sugup_fmtime		= \''.$val['planFrom'].'\'
						   and t01_sugup_seq		= \''.$val['planSeq'].'\'';

				if (!$conn->execute($sql)){
					 $conn->rollback();
					 $conn->close();
					 echo 9;
					 exit;
				}
			}
			
		}
		

		$conn->commit();

		$orgNo	= $code;
		$yymm	= $_POST['yymm'];
		//include_once('../iljung/summary.php');

		echo 1;
	}

	include_once('../inc/_db_close.php');
?>