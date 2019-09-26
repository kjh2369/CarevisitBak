<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_body_header.php');

	$code = $_SESSION['userCenterCode'];
	$sr   = $_GET['sr'];
	$type = $_GET['type'];
	
?>
<script type="text/javascript">
	$(document).ready(function(){
		__init_form(document.f);
	});
</script>
<form id="f" name="f" method="post" enctype="multipart/form-data">
<?
	if ($type == '1'){
		include_once('./care_suga.php');
	}else if ($type == '2'){
		include_once('./care_suga_unit.php');
	}else if ($type == '11'){
		include_once('./care_resource.php');
	}else if ($type == '21'){
		if ($sr == 'S'){	
			include_once('./care_plan_new.php');
		}else{
			include_once('./care_plan.php');
		}
	}else if ($type == '31'){
		include_once('./care_print.php');
	}else if ($type == '41' || $type == '42'){
		include_once('./care_conf.php');
	}else if ($type == '43'){
		include_once('./care_close.php');
	}else if ($type == '51'){
		if ($sr == 'S'){
			include_once('./care_report_new.php');
		}else{
			include_once('./care_report.php');
		}
	}else if ($type == '52'){
		include_once('./care_report_ass.php');
	}else if ($type == '53'){
		include_once('./care_report_ass_middle.php');
	}else if ($type == '54'){
		include_once('./care_report_center.php');
	}else if ($type == '55'){
		include_once('./care_report_area.php');
	}else if ($type == '56'){
		include_once('./care_report_inc_center.php');
	}else if ($type == '61'){
		include_once('./care_center.php');
	}else if ($type == '71'){
		include_once('./care_cust.php');
	}else if ($type == '81'){
		if ($sr == 'S'){
			include_once('./care_client_s.php');
		}else{
			include_once('./care_client.php');
		}
	}else if ($type == '82'){
		if ($sr == 'S'){
			include_once('./care_client_s_reg.php');
		}else{
			include_once('./care_client_reg.php');
		}
	}else if ($type == '83'){
		include_once('./care_normal.php');
	}else if ($type == '84'){
		include_once('./care_normal_reg.php');
	}else if ($type == '91'){
		include_once('./care_iljung_resource.php');
	}else if ($type == 'RESULT_REG'){
		//실적등록
		if ($sr == 'S'){
			include_once('./care_works_log_list.php');
		}else{
			include_once('./care_result.php');
		}
	}else if ($type == 'INTERVIEW_LIST'
			||$type == 'INTERVIEW_LIST_N'){
		//초기상담기록지
		include_once('./care_interview.php');
	}else if ($type == 'INTERVIEW_REG'
			||$type == 'INTERVIEW_REG_N'){


		//초기상담기록지?>
		<script type="text/javascript">
			function lfPDF(type,subId,idx){
				var dir = 'P';
				var file = 'hce_print';

				if (type == '1'){
					dir = 'L';
				}

				if (!subId) subId = '';
				if (!idx) idx = '';


				var arguments	= 'root=hce'
								+ '&dir='+dir
								+ '&fileName='+file
								+ '&fileType=pdf'
								+ '&target=show.php'
								+ '&mode='+type
								+ '&gbn=care'
								+ '&wrkType=<?=$type;?>'
								+ '&sr=<?=$sr;?>'
								+ '&subId='+subId
								+ '&idx='+idx
								+ '&key='+$('#txtTGer').attr('key')
								+ '&showForm=HCE';

				__printPDF(arguments);
			}
		</script><?
		include_once('../inc/_hce.php');
		include_once('../hce/hce_interview.php');
	}else if ($type == 'INTERVIEW_DEL'){
		//초기상담기록지 삭제
		$IPIN = $_POST['IPIN'];

		$sql = 'DELETE
				FROM	hce_interview
				WHERE	org_no	= \''.$code.'\'
				AND		org_type= \''.$sr.'\'
				AND		IPIN	= \''.$IPIN.'\'
				AND		rcpt_seq= \'0\'';

		$conn->begin();

		if (!$conn->execute($sql)){
			 $conn->rollback();
		}

		$conn->commit();

		include_once('./care_interview.php');

	}else if ($type == 'ACTUAL_RESEARCH'){
		//지원대상 실태조사표
		include_once('./care_actual_research.php');

	}else if ($type == 'ACTUAL_RESEARCH_REG'){
		//지원대상 실태조사표 등록
		include_once('./care_actual_research_reg.php');

	}else if ($type == 'USE_FORM'){
		//이용 신청서
		include_once('./care_use_form.php');

	}else if ($type == 'USE_FORM_REG'){
		//이용신청서 작성
		include_once('./care_use_form_reg.php');

	}else if ($type == 'PROVIDE_PLAN'){
		//제공계획서
		include_once('./care_provide_plan.php');

	}else if ($type == 'PROVIDE_PLAN_REG'){
		//제공계획서 작성
		include_once('./care_provide_plan_reg.php');

	}else if ($type == 'SVC_GROUP_REG'){
		//서비스 묶음등록
		include_once('./care_svc_group_reg.php');

	}else if ($type == 'SVC_GROUP_LIST'){
		//서비스 묶음조회
		include_once('./care_svc_group.php');

	}else if ($type == 'ILJUNG_SVC_GROUP'){
		//일정관리 묶음서비스
		include_once('./care_iljung_svc_group.php');

	}else if ($type == 'ILJUNG_SVC_GROUP_REG'){
		//일정관리 묶음서비스 등록
		include_once('./care_iljung_svc_group_reg.php');

	}else if ($type == 'CLIENT_FIND'){
		//고객조회
		include_once('./care_client_find.php');

	}else if ($type == 'SVC_USE_STAT'){
		//서비스 이용현황
		include_once('./care_svc_use_stat.php');

	}else if ($type == 'USER_SVC_STAT'){
		//이용자별 서비스현황
		include_once('./care_user_svc_stat.php');

	}else if ($type == 'REC_PROVIDE_ITEM'){
		//제공기록지 항목관리
		include_once('./care_rec_provide_item.php');

	}else if ($type == 'REC_PROVIDE'){
		//제공기록지 조회 및 작성
		include_once('./care_rec_provide.php');

	}else if ($type == 'SVC_OPERATE_STAT'){
		//서비스 운영현황
		include_once('./care_svc_operate_stat.php');

	}else if ($type == 'WORK_LOG_ITEM'){
		//업무일지 항목관리
		include_once('./care_work_log_item.php');

	}else if ($type == 'WORK_LOG'){
		//업무일지 조회 및 작성
		include_once('./care_work_log.php');

	}else if ($type == 'SVC_CATEGORY'){
		//서비스 묶음별 카테고리
		include_once('./care_svc_category.php');

	}else if ($type == 'SVC_USE_YEAR'){
		//서비스 이용현황 년별
		include_once('./care_svc_use_year.php');

	}else if ($type == 'TGT_CONF'){
		//대상자별 실적
		include_once('./care_tgt_conf.php');

	}else if ($type == 'RPT' //기타
		|| $type == 'RSLS' //자원연계서비스
		|| $type == 'RSPL' //자원봉사자연결
		|| $type == 'LHCT' //지역재가협의체구성
		){
		include_once('./care_rpt.php');

	}else{
		//include('../inc/_http_home.php');
		echo 'TYPE : '.$type;
		exit;
	}

	function lfGetSPName($sr){
		if ($sr == 'S'){
			return '노인맞춤돌봄서비스';
		}else if ($sr == 'R'){
			return '자원연계';
		}else{
			return '';
		}
	}
?>
<input id="sr" type="hidden" value="<?=$sr;?>">
</form>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>