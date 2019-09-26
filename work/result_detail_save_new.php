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

			//히스토리 내역 저장
			$sql = 'UPDATE plan_conf_his
					   SET conf_date  = \''.$val['date'].'\'
					,      conf_from  = \''.$val['from'].'\'
					,      conf_to    = \''.$val['to'].'\'
					,      conf_time  = \''.$val['time'].'\'
					,      conf_suga  = \''.$val['sugaCd'].'\'
					,      conf_value = \''.$val['sugaVal'].'\'
					 WHERE org_no   = \''.$code.'\'
					   AND svc_kind = \''.$val['svcCd'].'\'
					   AND jumin    = \''.$jumin.'\'
					   AND date     = \''.$val['date'].'\'
					   AND time     = \''.$val['planFrom'].'\'
					   AND seq      = \''.$val['planSeq'].'\'';

			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->close();
				 echo 9;
				 exit;
			}

			//일정 저장
			$sql = 'update t01iljung
					   set t01_conf_date		= \''.$val['date'].'\'
					,      t01_conf_fmtime		= \''.$val['from'].'\'
					,      t01_conf_totime		= \''.$val['to'].'\'
					,      t01_conf_soyotime	= \''.$val['time'].'\'
					,      t01_conf_suga_code	= \''.$val['sugaCd'].'\'
					,      t01_conf_suga_value	= \''.$val['sugaVal'].'\'
					,      t01_yoyangsa_id1     = \''.$ed->de($val['memCd1']).'\'
					,      t01_yname1           = \''.$val['memNm1'].'\'
					,      t01_yoyangsa_id2     = \''.$ed->de($val['memCd2']).'\'
					,      t01_yname2           = \''.$val['memNm2'].'\'
					,      t01_status_gbn		= \''.$val['stat'].'\'
					,      t01_yoyangsa_id5     = \''.$val['cutGbn'].'\'
					,      t01_modify_yn		= \'M\'
					,      t01_trans_yn			= \'N\'
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

		$conn->commit();

		$orgNo	= $code;
		$yymm	= $_POST['yymm'];
		include_once('../iljung/summary.php');

		echo 1;
	}

	include_once('../inc/_db_close.php');
?>