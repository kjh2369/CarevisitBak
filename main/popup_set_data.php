<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');


	$toDate = Date('Ymd');

	//청구년월
	$sql = 'SELECT	yymm
			FROM	cv_claim_yymm';

	$claimYymm = $conn->get_data($sql);

	//오픈일자
	$sql = 'SELECT	CASE WHEN DATEDIFF(DATE_ADD(start_dt, interval 1 month), DATE_FORMAT(NOW(),\'%Y-%m-%d\')) > 0 THEN \'Y\' ELSE \'N\' END AS pgm_open_yn
			,		CASE WHEN rs_cd = \'1\' THEN \'Y\' ELSE \'N\' END AS fee_pop_yn
			FROM	cv_reg_info
			WHERE	org_no = \''.$_SESSION['userCenterCode'].'\'
			AND		DATE_FORMAT(NOW(),\'%Y%m%d\') BETWEEN from_dt AND to_dt';

	//$pgmOpenPopYn = $conn->get_data($sql);
	$tmpRow = $conn->get_array($sql);
	$pgmOpenPopYn = $tmpRow['pgm_open_yn'];
	$feePopYn = $tmpRow['fee_pop_yn'];

	//중지설정확인
	$sql = 'SELECT	stop_gbn, stop_dt, close_dt
			FROM	stop_set
			WHERE	org_no	= \''.$_SESSION['userCenterCode'].'\'
			AND		cls_yn	= \'N\'
			AND		stop_yn = \'N\'
			AND		DATE_FORMAT(NOW(),\'%Y%m%d\') BETWEEN stop_dt AND close_dt';

	$row = $conn->get_array($sql);

	$lsStopDt = $row['stop_dt'];
	$lsClsDt = $row['close_dt'];

	if ($lsStopDt){
		if ($row['stop_gbn'] == '1'){
			if ($lsStopDt <= Date('Ymd')){
				$IsStopPop = true;
			}else{
				$IsStopPop = false;
			}
		}else{
			//if (!$lsClsDt) $lsClsDt = $myF->dateAdd('day', 13, $lsStopDt, 'Ymd');
			if ($lsStopDt <= Date('Ymd') && $lsClsDt >= Date('Ymd')){
				$IsStopPop = true;
			}else{
				$IsStopPop = false;
			}
		}
	}

	$sql = 'select count(*)
			  from counsel
			 where org_no = \''.$_SESSION['userCenterCode'].'\'
			   and c_answer_gbn = \'5\'';
	$laborCnt = $conn -> get_data($sql);


	//신규기관
	$sql = 'SELECT	from_dt, to_dt
			FROM	cv_reg_info
			WHERE	org_no = \''.$_SESSION['userCenterCode'].'\'
			AND		from_dt >= \'20151008\'
			AND		from_dt <= \''.Date('Ymd').'\'
			AND		to_dt >= \''.Date('Ymd').'\'
			AND		rs_cd = \'3\'
			AND		rs_dtl_cd != \'04\'';

	if ($gDomain != 'kacold.net') $tmpR = $conn->get_array($sql);

	if ($tmpR){
		$newUser = 'Y';
		$newUserFromDt = $myF->dateStyle($tmpR['from_dt'],'.');
		$newUserToDt = $myF->dateStyle($tmpR['to_dt'],'.');
	}else{
		$newUser = 'N';
	}


	//법인변경 팝업 구분
	$sql = 'SELECT	rs_cd
			FROM	cv_reg_info
			WHERE	org_no	 = \''.$_SESSION['userCenterCode'].'\'
			AND		pop_yn	!= \'Y\'
			AND		from_dt <= DATE_FORMAT(NOW(), \'%Y%m%d\')
			AND		to_dt	>= DATE_FORMAT(NOW(), \'%Y%m%d\')';

	$row = $conn->get_array($sql);

	$userRsCd = $row['rs_cd'];

	if ($isDemo || $gDomain == 'dwcare.com' || $gDomain == 'dolvoin.net' || Date('Ymd') >= '20160813'){
		$userRsCd = '';
	}

	Unset($row);


	//청구팝업 설정
	if ($isDemo || $gDomain == 'dwcare.com' || $gDomain == 'dolvoin.net'){
		$claimPopSet = false;
	}else{
		$sql = 'SELECT	SUM(acct_amt + tmp_amt)
				FROM	cv_svc_acct_list
				WHERE	org_no	= \''.$_SESSION['userCenterCode'].'\'
				AND		acct_ym = DATE_FORMAT(NOW(), \'%Y%m\')';

		$tmpAmt = IntVal($conn->get_data($sql));

		if ($tmpAmt > 0){
			$claimPopSet = true;
		}else{
			$claimPopSet = false;
		}
	}


	//홈페이지신청 카운트
	$sql = 'SELECT	count(*)
			FROM	homepage_request
			WHERE	org_no = \''.$_SESSION['userCenterCode'].'\'
			AND		del_flag = \'N\'';
	$hp_cnt = $conn -> get_data($sql);


	$sql = 'SELECT count(*)
			FROM   seminar_request
			WHERE  org_no = \''.$_SESSION['userCenterCode'].'\'
			AND    gbn    = \'9\'
			AND    del_flag = \'N\'';
	$faRqCnt = $conn -> get_data($sql);

?>
<script type="text/javascript">

	//재무회계신청
	/*
	if ('<?=$gDomain;?>' == 'carevisit.net'){
		if('<?=$gHostNm;?>'+'.'+'<?=$gDomain;?>' != 'cr.carevisit.net' && '<?=$pgmOpenPopYn;?>' == 'Y'){
			if('<?=$laborCnt?>' == '0' ){
				if (__getCookie("LABORPOP") != 'DONE'){
					var width = 386;
					var height = 587;
					var left = 550;
					var top = 50;

					window.open("../popup/labor/index.html","LABOR_POP","width="+width+",height="+height+",top="+top+",left="+left+",scrollbars=no,resizable=no,location=no,toolbar=no,menubar=no");
				}
			}
		}
	}
	*/


	if ('<?=$IsStopPop;?>' == '1'){
		if (__getCookie("STOPPOP") != 'DONE'){
			var width = 436;
			var height = 630;
			var left = 300;
			var top = (screen.availHeight - height) / 2;

			window.open("../popup/stop_pop/default.html","POPUP_STOPPOP","width="+width+",height="+height+",top="+top+",left="+left+",scrollbars=no,resizable=no,location=no,toolbar=no,menubar=no");
		}
	}


	if ('<?=$newUser;?>' == 'Y'){
		if (__getCookie("NEW_USER") != 'DONE'){
			var width = 430;
			var height = 415;
			var left = 800;
			var top = 450;

			window.open("../popup/new_user/period.html?fromDt=<?=$newUserFromDt;?>&toDt=<?=$newUserToDt;?>","NEW_USER","width="+width+",height="+height+",top="+top+",left="+left+",scrollbars=no,resizable=no,location=no,toolbar=no,menubar=no");
		}
	}


	if ('<?=$gHostNm;?>' != 'pr' && '<?=$gDomain;?>' != 'kacold.net' && '<?=$gDomain;?>' != 'dolvoin.net' && '<?=$feePopYn;?>' == 'Y'){
		if (__getCookie("FEEMSG") != 'DONE'){
			var width = 495;
			var height = 638;
			var left = 400;
			var top = 150;

			//window.open("../popup/fee_msg/fee.html","FEEMSG","width="+width+",height="+height+",top="+top+",left="+left+",scrollbars=no,resizable=no,location=no,toolbar=no,menubar=no");
		}
	}


	if ('<?=$userRsCd;?>' == '1'){
		var width = 600;
		var height = 800;
		var left = 400;
		var top = 150;

		window.open("../popup/new_com/pop.php","NEWCOM","width="+width+",height="+height+",top="+top+",left="+left+",scrollbars=no,resizable=no,location=no,toolbar=no,menubar=no");
	}


	if ('<?=$gHostNm;?>' != 'pr' && '<?=$gDomain;?>' == 'carevisit.net'){
		if ('<?=$hp_cnt?>' == 0){
			if (__getCookie("HOME_APP") != 'DONE'){
				var width = 690;
				var height = 642;
				var left = 0;
				var top = 0;

				//window.open("../popup/home_app/index.html","HOME_APP","width="+width+",height="+height+",top="+top+",left="+left+",scrollbars=no,resizable=no,location=no,toolbar=no,menubar=no");

			}
		}
	}


	//청구년월의 16일부터 26일까지 팝업을 실행.
	if ('<?=$claimPopSet;?>' == '1'){
		if (__getCookie("CLAIM_POP") != "DONE"){
			var date = new Date();

			if (date.getFullYear()+(date.getMonth()+1 < 10 ? '0' : '')+(date.getMonth()+1) == '<?=$claimYymm;?>'){
				if (date.getDate() >= 17 && date.getDate() <= 25) window.open('../popup/fee_msg/t.php?type=CLAIM&year='+date.getFullYear()+'&month='+(date.getMonth()+1),'CLAIM','width=700,height=900,Top=0,left=100,scrollbars=yes,resizable=no,location=no,toolbar=no,menubar=no');
			}
		}
	}

	//연말정산대행 서비스
	if('<?=$gHostNm;?>'+'.'+'<?=$gDomain;?>' == 'www.carevisit.net'){
		if (__getCookie("YEAREND_POP") != 'DONE'){
			var width = 397;
			var height = 525;
			var left = 500;
			var top = 150;

			//window.open("../popup/year_end/year_end.html","YEAREND_POP","width="+width+",height="+height+",top="+top+",left="+left+",scrollbars=no,resizable=no,location=no,toolbar=no,menubar=no");
		}
	}


	if (__getCookie("POPUP_NOTIS") != 'DONE'){
		//var width = 397;
		//var height = 445;
		/*
		if('<?=$gHostNm;?>'+'.'+'<?=$gDomain;?>' == 'www.carevisit.net' ||
		   '<?=$gHostNm;?>'+'.'+'<?=$gDomain;?>' == 'www.dwcare.com'    ){
			if('<?=$_SESSION[userLevel];?>' == 'C'){ // && '<?=$_SESSION[userArea];?>'!='02'
				var width = 496;
				var height = 625;
				var left = 200;
				var top = (screen.availHeight - height) / 2;

				window.open("../popup/pop_noti.html","POPUP_NOTIS","width="+width+",height="+height+",top="+top+",left="+left+",scrollbars=no,resizable=no,location=no,toolbar=no,menubar=no");
			}
		}
		*/
	}
	
	
	if('<?=$gHostNm;?>' != 'pr' /*&& '<?=$toDate?>' <= '20190717'*/){
		var width = 457;
		var height = 750;
		//var height = 390;
		var left = 300;
		var top = (screen.availHeight - height) / 2;
		
		//window.open("../popup/seminar/request.php","POPUP_POP","width="+width+",height="+height+",top="+top+",left="+left+",scrollbars=no,resizable=no,location=no,toolbar=no,menubar=no");
		
	}
	
	if('<?=$faRqCnt;?>' == 0){
		if('<?=$gHostNm;?>' != 'pr' /*&& '<?=$toDate?>' <= '20190717'*/){
			var width = 496;
			var height = 587;
			//var height = 390;
			var left = 300;
			var top = (screen.availHeight - height) / 2;
			
			window.open("../popup/fa/request.php","POPUP_POP","width="+width+",height="+height+",top="+top+",left="+left+",scrollbars=no,resizable=no,location=no,toolbar=no,menubar=no");
			
		}
	}
	
	if('<?=$gDomain;?>' != 'kacold.net'){
		if (__getCookie("FOOT_POPUP") != 'DONE'){
			var left = 800;
			var top = (screen.availHeight - height) / 2;

			window.open('../work/footing_mg.php?popYn=Y','FOOTING_MG','width=720, height=550, left='+left+', top='+top+', toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=yes');
		}
	}
	

	//추석명절 공지
	if (__getCookie("POPUP6") != 'DONE'){
		var width = 397;
		var height = 447;
		var left = 500;
		var top = 150;
		
		//window.open("../popup/6/6.html","POPUP6","width="+width+",height="+height+",top="+top+",left="+left+",scrollbars=no,resizable=no,location=no,toolbar=no,menubar=no");
		
	}
	
	if('<?=$gHostNm;?>'=='www'){
		if (__getCookie("LMS_POPUP") != 'DONE'){
			var width = 977;
			var height = 770;
			var left = 0;
			var top = 0;

			//window.open("../popup/lms/lms_pop.html","LMS_POPUP","width="+width+",height="+height+",top="+top+",left="+left+",scrollbars=no,resizable=no,location=no,toolbar=no,menubar=no");	
		}
	}
	

</script>
<?
	include_once('../inc/_db_close.php');
?>