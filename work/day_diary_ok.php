<?
	include('../inc/_db_open.php');
	include('../inc/_function.php');
	include('../inc/_ed.php');

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$conn->begin();

	// 수정된 확정 데이타를 저장한다.
	$listCount = sizeOf($_POST['changeFlag']);

	// 일별 마감에서 실적을 변경한다.
	// modify 플래그는 D로 저장하며
	// 실적 데이타와 확정 데이타를 같이 저장한다.
	for($i=0; $i<$listCount; $i++){
		if ($_POST['changeFlag'][$i] == 'Y'){
			if ($_POST['statusGubun'][$i] == 'C'){
				// 에러처리된 일정이라면 내역을 남겨둔다.
				$sql = "insert into e01iljung (e01_ccode,e01_mkind,e01_jumin,e01_sugup_date,e01_sugup_fmtime,e01_sugup_seq,e01_sugup_totime,e01_sugup_soyotime,e01_sugup_yoil,e01_wrk_date,e01_wrk_fmtime,e01_wrk_totime,e01_svc_subcode,e01_svc_subcd,e01_status_gbn,e01_svc_name,e01_toge_umu,e01_bipay_umu,e01_time_doub,e01_yoyangsa_id1,e01_yoyangsa_id2,e01_yoyangsa_id3,e01_yoyangsa_id4,e01_yoyangsa_id5,e01_yname1,e01_yname2,e01_yname3,e01_yname4,e01_yname5,e01_suga_code1,e01_suga,e01_suga_over,e01_suga_night,e01_suga_tot,e01_ysigup,e01_plan_work,e01_plan_sudang,e01_plan_cha,e01_act_work,e01_act_sudang,e01_act_cha,e01_del_yn,e01_trans_yn,e01_mobile_yn,e01_modify_yn,e01_modify_pos,e01_car_no,e01_e_time,e01_n_time,e01_ysudang_yn,e01_ysudang,e01_ysudang_yul1,e01_ysudang_yul2,e01_conf_date,e01_conf_fmtime,e01_conf_totime,e01_conf_soyotime,e01_conf_suga_code,e01_conf_suga_value,e01_holiday,e01_gps_x,e01_gps_y,e01_sudang_conf_yn)
						select t01_ccode,t01_mkind,t01_jumin,t01_sugup_date,t01_sugup_fmtime,t01_sugup_seq,t01_sugup_totime,t01_sugup_soyotime,t01_sugup_yoil,t01_wrk_date,t01_wrk_fmtime,t01_wrk_totime,t01_svc_subcode,t01_svc_subcd,t01_status_gbn,t01_svc_name,t01_toge_umu,t01_bipay_umu,t01_time_doub,t01_yoyangsa_id1,t01_yoyangsa_id2,t01_yoyangsa_id3,t01_yoyangsa_id4,t01_yoyangsa_id5,t01_yname1,t01_yname2,t01_yname3,t01_yname4,t01_yname5,t01_suga_code1,t01_suga,t01_suga_over,t01_suga_night,t01_suga_tot,t01_ysigup,t01_plan_work,t01_plan_sudang,t01_plan_cha,t01_act_work,t01_act_sudang,t01_act_cha,t01_del_yn,t01_trans_yn,t01_mobile_yn,t01_modify_yn,t01_modify_pos,t01_car_no,t01_e_time,t01_n_time,t01_ysudang_yn,t01_ysudang,t01_ysudang_yul1,t01_ysudang_yul2,t01_conf_date,t01_conf_fmtime,t01_conf_totime,t01_conf_soyotime,t01_conf_suga_code,t01_conf_suga_value,t01_holiday,t01_gps_x,t01_gps_y,t01_sudang_conf_yn
						  from t01iljung
						 where t01_ccode = '".$_POST['mCode']."'
						   and t01_mkind = '".$_POST['mKind']."'
						   and t01_jumin = '".$ed->de($_POST['sugupja'][$i])."'
						   and t01_sugup_date   = '".$_POST['sugupDate'][$i]."'
						   and t01_sugup_fmtime = '".$_POST['sugupFmTime'][$i]."'
						   and t01_sugup_seq    = '".$_POST['sugupSeq'][$i]."'";
				if (!$conn->query($sql)){
					$conn->rollback();
					echo '<script>alert("데이타 저장중 오류가 발생하였습니다."); history.back();</script>';
					exit;
				}
				$statusChanged = 'Y';
			}else{
				$statusChanged = 'N';
			}

			$sql = "update t01iljung"
				 . "   set t01_wrk_date        = '".$_POST['mYear'].str_replace('/', '', $_POST['workDate'][$i])
				 . "',     t01_wrk_fmtime      = '".str_replace(':', '', $_POST['workFmTime'][$i])
				 . "',     t01_wrk_totime      = '".str_replace(':', '', $_POST['workToTime'][$i])
				 . "',     t01_conf_date       = '".$_POST['mYear'].str_replace('/', '', $_POST['workDate'][$i])
				 . "',     t01_conf_fmtime     = '".str_replace(':', '', $_POST['workFmTime'][$i])
				 . "',     t01_conf_totime     = '".str_replace(':', '', $_POST['workToTime'][$i])
				 . "',     t01_conf_soyotime   = '".$_POST['workProcTime'][$i]
				 . "',     t01_conf_suga_code  = '".$_POST['sugaCode'][$i]
				 . "',     t01_conf_suga_value = '".$_POST['sugaPrice'][$i]
				 . "',     t01_status_gbn      = '1'"
				 . " ,     t01_modify_yn       = 'D'"
				 . " ,     t01_ysudang         = case when t01_svc_subcode != '200' then case when t01_ysudang_yn = 'Y' then '".$_POST['sugaPrice'][$i]."' else t01_ysudang end else t01_ysudang end";

			if ($statusChanged == 'Y'){
				$sql .= ", t01_trans_yn = 'N'";
			}

			$sql.= " where t01_ccode = '".$_POST['mCode']
				 . "'  and t01_mkind = '".$_POST['mKind']
				 . "'  and t01_jumin = '".$ed->de($_POST['sugupja'][$i])
				 . "'  and t01_sugup_date   = '".$_POST['sugupDate'][$i]
				 . "'  and t01_sugup_fmtime = '".$_POST['sugupFmTime'][$i]
				 . "'  and t01_sugup_seq    = '".$_POST['sugupSeq'][$i]
				 . "'";
			if (!$conn->query($sql)){
				$conn->rollback();
				echo '<script>alert("데이타 저장중 오류가 발생하였습니다."); history.back();</script>';
				exit;
			}
		}
	}

	$conn->commit();

	include('../inc/_db_close.php');
?>
<script language='javascript'>
	location.replace("day_conf.php?mType=DAY&mCode=<?=$_POST['mCode'];?>&mKind=<?=$_POST['mKind'];?>&mYear=<?=$_POST['mYear'];?>&mMonth=<?=$_POST['mMonth'];?>&mDay=<?=$_POST['mDay'];?>");
</script>