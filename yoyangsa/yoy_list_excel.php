<?
	include_once('../inc/_db_open.php');
	include_once("../inc/_myFun.php");
	include_once("../inc/_mySalary.php");
	include_once('../inc/_ed.php');
	include_once("../inc/_function.php");

	$conn2 = new connection();

	$find_center_code	= $_SESSION["userLevel"] == 'A' ? $_REQUEST['find_center_code'] : $_SESSION["userCenterCode"];	//기관코드 조회 시
	$find_center_name	= $_REQUEST['find_center_name'];																//기관명 조회 시
	$find_yoy_name		= $_REQUEST['find_yoy_name'];																	//이름 조회 시
	$find_yoy_name			= $_REQUEST['find_yoy_name'];																		//주민번호 조회 시 
	$find_yoy_phone		= str_replace('-', '', $_REQUEST['find_yoy_phone']);											//전화번호 조회 시
	$find_yoy_stat		= $_REQUEST['find_yoy_stat'] != '' ? $_REQUEST['find_yoy_stat'] : '1';							//고용상태 조회 시
	$find_dept          = $_REQUEST['find_dept'];																		//부서 조회 시

	//기초정보
	$name = $_POST['name'];						//성명
	$jumin = $_POST['jumin'];					//주민번호
	$manageNo = $_POST['manageNo'];				//관리번호
	$addr = $_POST['addr'];						//주소
	$tel    = $_POST['tel'];					//집전화
	$mobile = $_POST['mobile'];					//휴대폰번호
	$memNo = $_POST['memNo'];					//사번
	$userID = $_POST['userID'];					//사용자ID
	$dept = $_POST['dept'];						//부서
	$jobNm = $_POST['jobNm'];					//직무
	$yipsail = $_POST['yipsail'];				//입사
	$toisail = $_POST['toisail'];				//퇴사

	//상세정보
	$tele = $_POST['tele'];						//통신사
	$rfid_yn = $_POST['rfid_yn'];				//RFID(유,무)
	$rfid_no = $_POST['rfid_no'];				//RFID(번호)
	$jaguk_kind = $_POST['jaguk_kind'];			//자격증종류
	$jaguk_no = $_POST['jaguk_no'];				//자격증번호
	$jaguk_date = $_POST['jaguk_date'];			//발급일자
	$bank_name = $_POST['bank_name'];			//급여지급은행명
	$bank_account = $_POST['bank_account'];		//계좌번호
	$bohum = $_POST['bohum'];					//4보험정보(국민,건강,고용,산재)
	$extend = $_POST['extend'];					//특별수당(연장)
	$holiday = $_POST['holiday'];				//특별수당(휴일)
	$gikup = $_POST['gikup'];					//직급수당
	$general = $_POST['general'];				//급여산정(일반)
	$fam = $_POST['fam'];						//급여산정(동거)
	$oldman = $_POST['oldman'];					//급여산정(노인)
	$housework = $_POST['housework'];			//급여산정(가사)
	$puerperd = $_POST['puerperd'];				//급여산정(산모)
	$disability = $_POST['disability'];			//급여산정(장애)
	$from_date = $_POST['from_date'];			//배상책임보험(시작일)
	$to_date = $_POST['to_date'];				//배상책임보험(종료일)
	$mobile_work = $_POST['mobile_work'];		//폰업무
	$memo = $_POST['memo'];						//메모
	$goyong_type = $_POST['goyong_type'];		//고용형태
	$goyong_stat = $_POST['goyong_stat'];		//고용상태
	$standard_time = $_POST['standard_time'];	//기준시간
	$standard_sigup = $_POST['standard_sigup'];	//기준시급
	$week = $_POST['week'];						//주휴요일
	$resign_yn = $_POST['resign_yn'];			//퇴직금중간정산 여부
	$resign_date = $_POST['resign_date'];		//퇴직금중간정산 정산일자
	$familyYn = $_POST['familyYn'];				//가족케어여부
	$demantiaYn = $_POST['demantiaYn'];			//치매인지수료여부

	$k_list = $conn->kind_list($_SESSION["userCenterCode"], $gHostSvc['voucher']);

	header( "Content-type: application/vnd.ms-excel" );
	header( "Content-Disposition: attachment; filename=yoyangsa_list.xls" );
	header( "Content-Transfer-Encoding: binary" );
	header( "Content-Description: PHP4 Generated Data" );

	$conn->set_name('utf8');

	// 사용자 로그인정보
	$sql = "select code, jumin
			  from member
			 where org_no = '".$find_center_code."'
			   and del_yn = 'N'";

	$conn -> query($sql);
	$conn -> fetch();
	$row_count = $conn -> row_count();

	for($i=0; $i<$row_count; $i++){
		$mem[$i] = $conn -> select_row($i);
	}

	$mem_cnt = sizeof($mem);

	$html = '';
	$html2 = '';

	if($name == 'Y'){
		$html .= '<th rowspan="2" style="background-color:#efefef;">성 명</th>';
	}
	if($jumin == 'Y'){
		$html .= '<th rowspan="2" style="background-color:#efefef;">주민번호</th>';
	}
	if($manageNo == 'Y'){
		$html .= '<th rowspan="2" style="background-color:#efefef;">관리번호</th>';
	}


	if($memNo == 'Y' or $userID == 'Y' or $dept == 'Y' or $jobNm == 'Y'){
		if($memNo == 'Y'){
			$std_info[0] = $memNo;
		}
		if($userID == 'Y'){
			$std_info[1] = $userID;
		}
		if($dept == 'Y'){
			$std_info[2] = $dept;
		}
		if($jobNm == 'Y'){
			$std_info[3] = $jobNm;
		}

		$colspan = sizeof($std_info);
		$html .= '<th colspan="'.$colspan.'" style="background-color:#efefef;">기본정보</th>';
	}

	if($yipsail == 'Y' or $toisail == 'Y'){
		if($yipsail == 'Y'){
			$yip_toi[0] = $yipsail;
		}
		if($toisail == 'Y'){
			$yip_toi[1] = $toisail;
		}

		$colspan = sizeof($yip_toi);
		$html .= '<th colspan="'.$colspan.'" style="background-color:#efefef;">입사/퇴사 일자</th>';
	}

	if($tel == 'Y' or $mobile == 'Y'){
		if($tel == 'Y'){
			$contact[0] = $tel;
		}
		if($mobile == 'Y'){
			$contact[1] = $mobile;
		}

		$colspan = sizeof($contact);
		$html .= '<th colspan="'.$colspan.'" style="background-color:#efefef;">연락처</th>';
	}

	if($tele == 'Y'){
		$html .= '<th rowspan="2" style="background-color:#efefef;">통신사</th>';
		$html .= '<th rowspan="2" style="background-color:#efefef;">모델명</th>';
	}

	if($rfid_yn == 'Y' or $rfid_no == 'Y'){
		if($rfid_yn == 'Y'){
			$rfid[0] = $rfid_yn;
		}
		if($rfid_no == 'Y'){
			$rfid[1] = $rfid_no;
		}

		$colspan = sizeof($rfid);
		$html .= '<th colspan="'.$colspan.'" style="background-color:#efefef;">RFID</th>';
	}

	if($goyong_type == 'Y' or $goyong_stat == 'Y' or $standard_time == 'Y' or $standard_sigup == 'Y' or $week == 'Y'){
		if($goyong_type == 'Y'){
			$goyong[0] = $goyong_type;
		}
		if($goyong_stat == 'Y'){
			$goyong[1] = $goyong_stat;
		}
		if($standard_time == 'Y'){
			$goyong[2] = $standard_time;
		}
		if($standard_sigup == 'Y'){
			$goyong[3] = $stantdard_sigup;
		}
		if($week == 'Y'){
			$goyong[4] = $week;
		}

		$colspan = sizeof($goyong);

		$html .= '<th colspan="'.$colspan.'" style="background-color:#efefef;">고용정보</th>';
	}

	if($resign_yn == 'Y' or $resign_date == 'Y'){
		if($resign_yn == 'Y'){
			$resign[0] = $resign_yn;
		}
		if($resign_date == 'Y'){
			$resign[1] = $resign_date;
		}

		$colspan = sizeof($resign);
		$html .= '<th colspan="'.$colspan.'" style="background-color:#efefef;">퇴직금중간정산</th>';
	}

	if($bank_name == 'Y' or $bank_account == 'Y'){
		if($bank_name == 'Y'){
			$bank[0] = $bank_name;
		}
		if($bank_account == 'Y'){
			$bank[1] = $bank_account;
		}

		$colspan = sizeof($bank);
		$html .= '<th colspan="'.$colspan.'" style="background-color:#efefef;">급여지급은행</th>';
	}

	if($bohum == 'Y'){
		$html .= '<th colspan="4" style="background-color:#efefef;">4대보험</th>';
	}

	if($extend == 'Y' or $holiday == 'Y'){
		if($extend == 'Y'){
			$sudang[0] = $extend;
		}
		if($holiday == 'Y'){
			$sudang[1] = $holiday;
		}

		$colspan = sizeof($sudang);

		$html .= '<th colspan="'.$colspan.'" style="background-color:#efefef;">특별수당</th>';
	}

	if($gikup == 'Y'){
		$html .= '<th rowspan="2" style="background-color:#efefef;">직급수당</th>';
	}

	if($general == 'Y' or $fam == 'Y'){
		if($general == 'Y'){
			$pay[0] = $general;
		}
		if($fam == 'Y'){
			$pay[1] = $fam;
		}
		if($oldman == 'Y'){
			$pay[2] = $oldman;
		}
		if($housework == 'Y'){
			$pay[3] = $housework;
		}
		if($puerperd == 'Y'){
			$pay[4] = $puerperd;
		}
		if($disability == 'Y'){
			$pay[5] = $disability;
		}

		$colspan = sizeof($pay);
		$html .= '<th colspan="'.$colspan.'" style="background-color:#efefef;">급여산정방법</th>';
	}

	if($from_date == 'Y' or $to_date == 'Y'){
		if($from_date == 'Y'){
			$ins[0] = $from_date;
		}
		if($to_date == 'Y'){
			$ins[1] = $to_date;
		}

		$colspan = sizeof($ins);
		$html .= '<th colspan="'.$colspan.'" style="background-color:#efefef;">배상책임보험</th>';
	}

	if($jaguk_kind == 'Y' or $jaguk_no == 'Y' or $jaguk_date == 'Y'){
		if($jaguk_kind == 'Y'){
			$jaguk[0] = $jaguk_kind;
		}
		if($jaguk_no == 'Y'){
			$jaguk[1] = $jaguk_no;
		}
		if($jaguk_date == 'Y'){
			$jaguk[2] = $jaguk_date;
		}

		$colspan = sizeof($jaguk);
		$html .= '<th colspan="'.$colspan.'" style="background-color:#efefef;">자격증</th>';
	}
	
	if($addr == 'Y'){
		$html .= '<th colspan="2" style="background-color:#efefef;">주소</th>';
	}

	if($mobile_work == 'Y'){
		$html .= '<th rowspan="2" style="background-color:#efefef;">폰업무</th>';
	}
	
	if($familyYn == 'Y'){
		$html .= '<th rowspan="2" style="background-color:#efefef;">가족케어여부</th>';
	}

	if($demantiaYn == 'Y'){
		$html .= '<th rowspan="2" style="background-color:#efefef;">치매인지수료 여부</th>';
	}

	if($memo == 'Y'){
		$html .= '<th rowspan="2" style="background-color:#efefef;">Memo</th>';
	}

	$col = 1;

	if($name == 'Y'){
		$col += 1;
	}
	if($jumin == 'Y'){
		$col += 1;
	}

	if($memNo == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">사번</th>';
		$col += 1;
	}

	if($userID == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">사용자ID</th>';
		$col += 1;
	}
	if($dept == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">부서</th>';
		$col += 1;
	}
	if($jobNm == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">직무</th>';
		$col += 1;
	}
	if($yipsail == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">입사</th>';
		$col += 1;
	}
	if($toisail == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">퇴사</th>';
		$col += 1;
	}
	if($tel == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">유선</th>';
		$col += 1;
	}
	if($mobile == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">무선</th>';
		$col += 1;
	}

	if($tele == 'Y'){
		$col += 2;
	}

	if($rfid_yn == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">유.무</th>';
		$col += 1;
	}
	if($rfid_no == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">번호</th>';
		$col += 1;
	}
	if($goyong_type == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">형태</th>';
		$col += 1;
	}
	if($goyong_stat == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">상태</th>';
		$col += 1;
	}
	if($standard_time == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">기준시간</th>';
		$col += 1;
	}
	if($standard_sigup == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">기준시급</th>';
		$col += 1;
	}
	if($week == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">주휴요일</th>';
		$col += 1;
	}

	if($resign_yn == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">여부</th>';
		$col += 1;
	}
	if($resign_date == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">정산일자</th>';
		$col += 1;
	}

	if($bank_name == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">은행명</th>';
		$col += 1;
	}
	if($bank_account == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">계좌번호</th>';
		$col += 1;
	}
	if($bohum == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">국민</th>';
		$html2 .= '<th style="background-color:#efefef;">건강</th>';
		$html2 .= '<th style="background-color:#efefef;">고용</th>';
		$html2 .= '<th style="background-color:#efefef;">산재</th>';
		$col += 4;
	}
	if($extend == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">연장</th>';
		$col += 1;
	}

	if($holiday == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">휴일</th>';
		$col += 1;
	}

	if($gikup == 'Y'){
		$col += 1;
	}
	if($general == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">일반</th>';
		$col += 1;
	}
	if($fam == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">동거</th>';
		$col += 1;
	}
	if($oldman == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">노인</th>';
		$col += 1;
	}
	if($housework == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">가사</th>';
		$col += 1;
	}
	if($puerperd == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">산모</th>';
		$col += 1;
	}
	if($disability == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">장애</th>';
		$col += 1;
	}

	if($from_date == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">가입일자</th>';
		$col += 1;
	}
	if($to_date == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">종료일자</th>';
		$col += 1;
	}

	if($jaguk_kind == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">자격증종류</th>';
		$col += 1;
	}

	if($jaguk_no == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">자격증번호</th>';
		$col += 1;
	}
	if($jaguk_date == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">발급일자</th>';
		$col += 1;
	}
	
	if($addr == 'Y'){
		$html2 .= '<th style="background-color:#efefef;">우편번호</th>';
		$html2 .= '<th style="background-color:#efefef;">상세주소</th>';
		$col += 2;
	}

	if($mobile_work == 'Y'){
		$col += 1;
	}
	
	if($familyYn == 'Y'){
		$col += 1;
	}

	if($demantiaYn == 'Y'){
		$col += 1;
	}

	if($memo == 'Y'){
		$col += 1;
	}

?>
<style>
.div1{
	width:33%;
	margin-top:3px;
	float:left;
}
</style>
<script language='javascript'>
<!--
//엑셀
function excel(){
	var f = document.f;

	f.action = 'list_excel.php';
	f.submit();
}
//-->
</script>
<?
	$sql = "select m00_cname
			  from m00center
			 where m00_mcode = '$find_center_code'";
	$cname = $conn -> get_data($sql);
?>

<form name="f" method="post">
<div></div>
<table style="margin-top:-1px;" border=1>
	<thead>
		<tr>
			<td colspan="<?=($col)?>" style="font-size:20pt; font-weight:bold; text-align:center;">직원명부</td>
		</tr>
		<tr>
			<td colspan="<?=($col)?>" style="font-size:15pt; font-weight:bold;" >기관명 : <?=$cname;?></td>
		</tr>
		<tr>
			<th class="head" rowspan="2" style="background-color:#efefef;">No</th><?
			echo $html;
		echo '</tr>
			  <tr>';
			echo $html2;
		?>
		</tr>
	</thead>
	<tbody>
	<?
	
	$wsl = "";
	if ($_SESSION["userLevel"] == "A"){
		if ($find_center_code != '') $wsl .= " and m02_ccode like '$find_center_code%'";
		if ($find_center_name != '') $wsl .= " and m00_cname like '%$find_center_name%'";
	}else{
		$wsl .= " and m02_ccode = '$find_center_code'";
	}

	if ($find_yoy_name  != '')    $wsl .= " and m02_yname like '%$find_yoy_name%'";
	if ($find_yoy_ssn  != '')    $wsl .= " and m02_yjumin like '%$find_yoy_ssn%'";
	if ($find_yoy_phone != '')    $wsl .= " and m02_ytel like '%$find_yoy_phone%'";
	if ($find_yoy_stat  != 'all') $wsl .= " and m02_ygoyong_stat = '$find_yoy_stat'";
	if ($find_dept      != 'all') $wsl .= " and m02_dept_cd = '".str_replace('-','',$find_dept)."'";
	if ($find_dept      != 'all') $wsl .= " and m02_dept_cd = '$find_dept'";

	$sql = "select	   m02_ccode
				,      m02_mkind
				,      m02_key
				,      m02_yjumin
				,      m00_cname
				,      m02_yname
				,	   m02_mem_no
				,      dept.dept_nm
				,      m02_ytel
				,	   m02_ytel2
				,	   m02_model_no
				,	   m02_ins_from_date
				,	   m02_ins_to_date
				,	   m02_ins_yn
				,	   m02_yipsail
				,	   m02_ytoisail
				,	   job_kind.job_nm
				,	   case m02_mobile_kind when '1' then 'SKT' when '2' then 'KT' when '3' then 'LG U+' else ' ' end as mobile_kind
				,	   case m02_rfid_yn when 'Y' then '유' when 'N' then '' else ' ' end as rfid_yn
				,      m02_ypostno
				,      m02_yjuso1
				,      m02_yjuso2
				,	   case m02_weekly_holiday when '1' then '월' when '2' then '화' when '3' then '수' when '4' then '목' when '5' then '금' when '6' then '토' when '0' then '일' else '' end weekly_holiday
				,	   m02_stnd_work_pay
				,	   m02_stnd_work_time
				,	   case m02_ma_yn when 'Y' then 'Y' else ' ' end as ma_yn
				,	   m02_ma_dt
				,	   m02_ybank_name
				,	   m02_ygyeoja_no
				,      m02_y4bohum_umu as ins4
				,	   m02_ygnbohum_umu as gn
				,	   m02_ykmbohum_umu as km
				,	   m02_ysnbohum_umu as sn
				,	   m02_ygobohum_umu as go
				,	   m02_add_payrate
				,	   m02_holiday_payrate
				,	   m02_rank_pay
				,      m02_ygupyeo_kind
				,      m02_ygibonkup
				,	   m02_ysuga_yoyul
				,	   m02_pay_type
				,	   m02_yfamcare_type
				,	   m02_yfamcare_umu
				,	   m02_yfamcare_pay
				,	   m02_memo
				,	   case m02_ygoyong_kind when '1' then '정규직'
											 when '2' then '계약직'
											 when '3' then '단시간(60이상)'
											 when '4' then '단시간(60미만)'
											 when '5' then '특수근로' else ' ' end as m02_ygoyong_kind
				,	   case m02_yfamcare_umu when 'Y' then 'Y'
											 when 'N' then ' ' else '-' end as m02_yfamcare_umu
				,      case m02_ygoyong_stat when '1' then '재직'
				                             when '2' then '휴직'
											 when '9' then '퇴사' else '-' end as m02_ygoyong_stat
				,      case m02_jikwon_gbn when 'Y' then '요'
				                           when 'M' then '관'
										   when 'A' then '관 + 요' else ' ' end as m02_jikwon_gbn

				,     (select case when count(*) > 0 then 'Y' else ' ' end
				         from m02yoyangsa as temp_y
						where temp_y.m02_ccode = m02yoyangsa.m02_ccode
						  and temp_y.m02_mkind = '0'
						  and temp_y.m02_yjumin = m02yoyangsa.m02_yjumin
						  and temp_y.m02_del_yn = 'N') as care_yn

				,     (select case when count(*) > 0 then 'Y' else ' ' end
						 from m02yoyangsa as temp_y
						where temp_y.m02_ccode = m02yoyangsa.m02_ccode
						  and temp_y.m02_mkind >= '1'
						  and temp_y.m02_mkind <= '4'
						  and temp_y.m02_yjumin = m02yoyangsa.m02_yjumin
						  and temp_y.m02_del_yn = 'N') as voucher_yn

				,     (select license_gbn
						 from counsel_license
					    where org_no      = m02yoyangsa.m02_ccode
						  and license_ssn = m02yoyangsa.m02_yjumin
						order by license_dt desc
						limit 1) as license_nm
				,     (select case dementia_yn when 'Y' then '예' else '아니오' end as yn
						 from mem_option
					    where org_no = m02yoyangsa.m02_ccode
						 and  mo_jumin = m02yoyangsa.m02_yjumin) as dementia_yn
				,     (select case family_yn when 'Y' then '예' else '아니오' end as yn
						 from mem_option
					    where org_no = m02yoyangsa.m02_ccode
						 and  mo_jumin = m02yoyangsa.m02_yjumin) as family_yn
				  from m02yoyangsa
				  left join m00center
					on m00_mcode = m02_ccode
				   and m00_mkind = m02_mkind
				  left join dept
				    on dept.org_no  = m02_ccode
				   and dept.dept_cd = m02_dept_cd
				  left join job_kind
				    on job_kind.org_no = m02_ccode
				   and job_cd = m02_yjikjong
				 where m02_ccode = '".$find_center_code."'
				   and m02_del_yn = 'N' $wsl
				 group by m02_yjumin
				 order by m02_yname";
		
		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		if ($row_count > 0){
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);
				
				$sql = "select g07_pay_time
						  from g07minpay
						 where g07_year = '".date('Y', mktime())."'";
				$min_hourly = $conn->get_data($sql);



				$sql = 'select fw_hours as hours
						,      fw_hourly as hourly
						,	   fw_from_dt as from_dt
						,      fw_to_dt as to_dt
						  from fixed_works
						 where org_no      = \''.$row['m02_ccode'].'\'
						   and fw_jumin    = \''.$row['m02_yjumin'].'\'
						   and date_format(now(),\'%Y%m%d\') >= date_format(fw_from_dt,\'%Y%m%d\')
						   and date_format(now(),\'%Y%m%d\') <= date_format(fw_to_dt,  \'%Y%m%d\')
						   and del_flag    = \'N\'
						 order by fw_seq desc
						 limit 1';
				
				$fixedWorks = $conn->get_array($sql);
				
				if (!is_array($fixedWorks)){
					$sql = 'select min(m00_mkind) as kind
							,      m00_day_work_hour as hours
							,      m00_day_hourly as hourly
							  from m00center
							 where m00_mcode = \''.$row['m02_ccode'].'\'
							 group by m00_day_work_hour, m00_day_hourly';

					$tmpFixedWorks = $conn->get_array($sql);

					$fixedWorks['hours']   = $tmpFixedWorks['hours'];
					$fixedWorks['hourly']  = $tmpFixedWorks['hourly'];
					$fixedWorks['from_dt'] = '';
					$fixedWorks['to_dt']   = '';
				}

				$fixed = $fixedWorks['hours'];
				

				$min_hour = number_format($fixedWorks['hourly']);

				$sql = 'SELECT	annuity_yn as km
						,		health_yn as gn
						,		employ_yn as go
						,		sanje_yn as sn
						FROM	mem_insu
						WHERE	org_no	 = \''.$find_center_code.'\'
						AND		jumin	 = \''.$row['m02_yjumin'].'\'
						ORDER	BY seq DESC
						LIMIT	1';
				$insu = $conn->get_array($sql);
				
				if($row['m02_ygoyong_stat'] == '퇴사'){
					$toisails = $row['m02_ytoisail'] != '' ? $myF->dateStyle($row['m02_ytoisail'],'.') : '';
				}else{
					$toisails = '';
				}

				$user_id = '';

				for($j=0; $j<$mem_cnt; $j++){
					if ($mem[$j]['jumin'] == $row['m02_yjumin']){
						$user_id = $mem[$j]['code']; //사용자ID
						break;
					}
				}

				#자격증 정보
				$sql = "select license_gbn, license_no, license_dt
						 from counsel_license
						where org_no      = '".$row['m02_ccode']."'
						  and license_ssn = '".$row['m02_yjumin']."'
						order by license_dt desc
						limit 1";

				$license = $conn->get_array($sql);

				$sql = "select *
						  from m02yoyangsa
						 where m02_ccode  = '$find_center_code'
						   and m02_yjumin = '".$row['m02_yjumin']."'
						   and m02_del_yn = 'N'
						 order by m02_mkind";
				$conn2->query($sql);
				$conn2->fetch();
				$row_count2 = $conn2->row_count();

				for($j=0; $j<$row_count2; $j++){
					$row2 = $conn2->select_row($j);

					$mst[$row2['m02_mkind']] = $row2;
				}

				$conn2->row_free();
				
				//서비스별 급여제
				$conn2->query($mySalary->_queryNowHourly($find_center_code, $row['m02_yjumin']));
				$conn2->fetch();
				
				$rowCount2 = $conn2->row_count();

				for($j=0; $j<$rowCount2; $j++){
					$row2 = $conn2->select_row($j);
					
					//$salaryHourIf[$row2['kind']][$row2['svc_id']] = $mySalary->_setHourlyData($row2);
					
					if($row2['hourly']){
						$hourly[$row2['kind']][$row2['svc_id']] = $row2['hourly'];
					}else if($row2['vary_hourly_1']){
						$hourly[$row2['kind']][$row2['svc_id']] = $row2['vary_hourly_1'];
					}else if($row2['fixed_pay']){
						$hourly[$row2['kind']][$row2['svc_id']] = $row2['fixed_pay'];
					}else if($row2['hourly_rate']){
						$hourly[$row2['kind']][$row2['svc_id']] = $row2['hourly_rate'];
					}
				}
				
				$conn2->row_free();


				$addPayrate = $row['m02_add_payrate'] != '0' ? number_format($row['m02_add_payrate']).'%' : '';
				$holiPayrate = $row['m02_holiday_payrate'] != '0' ? number_format($row['m02_holiday_payrate']).'%' : '';
				$rankPay = $row['m02_rank_pay'] != '0' ? number_format($row['m02_rank_pay']) : '';

				$gn = $insu['gn'];
				$km = $insu['km'];
				$go = $insu['go'];
				$sn = $insu['sn'];

				echo '<tr>';
					echo '<td style="text-align:center">'.($i + 1).'</td>';
					if($name == 'Y'){
						echo '<td style="text-align:left; padding-left:5px;">'.$row['m02_yname'].'</td>';
					}
					if($jumin == 'Y'){
						echo '<td style="text-align:center;">'.$myF->issNo($row['m02_yjumin'],'.').'</td>';
					}
					if($memNo == 'Y'){
						echo '<td style="text-align:center;">'.$row['m02_mem_no'].'</td>';
					}
					if($userID == 'Y'){
						echo '<td style="text-align:center;">'.$user_id.'</td>';
					}
					if($dept == 'Y'){
						echo '<td style="text-align:center;">'.$row['dept_nm'].'</td>';
					}
					if($jobNm == 'Y'){
						echo '<td style="text-align:center;">'.$row['job_nm'].'</td>';
					}
					if($yipsail == 'Y'){
						echo '<td style="text-align:center;">'.$myF->dateStyle($row['m02_yipsail'],'.').'</td>';
					}
					if($toisail == 'Y'){
						echo '<td style="text-align:center;">'.$toisails.'</td>';
					}
					if($tel == 'Y'){
						echo '<td style="text-align:left; padding-left:5px;">'.$myF->phoneStyle($row['m02_ytel2']).'</td>';
					}
					if($mobile == 'Y'){
						echo '<td style="text-align:left; padding-left:5px;">'.$myF->phoneStyle($row['m02_ytel']).'</td>';
					}
					if($tele == 'Y'){
						echo '<td style="text-align:left; padding-left:5px;">'.$row['mobile_kind'].'</td>';
						echo '<td style="text-align:left; padding-left:5px;">'.$row['m02_model_no'].'</td>';
					}
					if($rfid_yn == 'Y'){
						echo '<td style="text-align:center; padding-left:5px;">'.$row['rfid_yn'].'</td>';
					}
					if($rfid_no == 'Y'){
						echo '<td style="text-align:left; padding-left:5px;"></td>';
					}
					if($goyong_type == 'Y'){
						echo '<td style="text-align:left; padding-left:5px;">'.$row['m02_ygoyong_kind'].'</td>';
					}
					if($goyong_stat == 'Y'){
						echo '<td style="text-align:left; padding-left:5px;">'.$row['m02_ygoyong_stat'].'</td>';
					}
					if($standard_time == 'Y'){
						echo '<td style="text-align:right; padding-left:5px;">'.$fixed.'</td>';
					}
					if($standard_sigup == 'Y'){
						echo '<td style="text-align:right; padding-left:5px;">'.$min_hourly.'</td>';
					}
					if($week == 'Y'){
						echo '<td style="text-align:center; padding-left:5px;">'.$row['weekly_holiday'].'</td>';
					}
					if($resign_yn == 'Y'){
						echo '<td style="text-align:center; padding-left:5px;">'.$row['ma_yn'].'</td>';
					}
					if($resign_date == 'Y'){
						echo '<td style="text-align:center; padding-left:5px;">'.$myF->dateStyle($row['m02_ma_dt'],'.').'</td>';
					}
					if($bank_name == 'Y'){
						echo '<td style="text-align:left;">'.$row['m02_ybank_name'].'</td>';
					}
					if($bank_account == 'Y'){ ?>
						<td style="text-align:left; mso-number-format:'\@';"><?=$row['m02_ygyeoja_no']?></td><?
					}

					if($bohum == 'Y'){
						echo '<td style="text-align:center;">'.$km.'</td>';
						echo '<td style="text-align:center;">'.$gn.'</td>';
						echo '<td style="text-align:center;">'.$go.'</td>';
						echo '<td style="text-align:center;">'.$sn.'</td>';
					}
					if($extend == 'Y'){
						echo '<td style="text-align:right;">'.$addPayrate.'</td>';
					}
					if($holiday == 'Y'){
						echo '<td style="text-align:right;">'.$holiPayrate.'</td>';
					}

					if($gikup == 'Y'){
						echo '<td style="text-align:right;">'.$rankPay.'</td>';
					}

					if($general == 'Y'){
						echo '<td style="text-align:right;">'.(number_format($hourly[0]['11']) ? number_format($hourly[0]['11']) : '').'</td>';
					}
					if($fam == 'Y'){
						echo '<td style="text-align:right;">'.(number_format($hourly[0]['12']) ? number_format($hourly[0]['12']) : '').'</td>';
					}
					if($oldman == 'Y'){
						echo '<td style="text-align:right;">'.(number_format($hourly[1]['21']) ? number_format($hourly[1]['21']) : '').'</td>';
					}
					if($housework == 'Y'){
						echo '<td style="text-align:right;">'.(number_format($hourly[2]['22']) ? number_format($hourly[2]['22']) : '').'</td>';
					}
					if($puerperd == 'Y'){
						echo '<td style="text-align:right;">'.(number_format($hourly[3]['23']) ? number_format($hourly[3]['23']) : '').'</td>';
					}
					if($disability == 'Y'){
						echo '<td style="text-align:right;">'.(number_format($hourly[4]['24']) ? number_format($hourly[4]['24']) : '').'</td>';
					}
					if($from_date == 'Y'){
						echo '<td style="text-align:center;">'.$myF->dateStyle($row['m02_ins_from_date'],'.').'</td>';
					}
					if($to_date == 'Y'){
						echo '<td style="text-align:center;">'.$myF->dateStyle($row['m02_ins_to_date'],'.').'</td>';
					}

					if($jaguk_kind == 'Y'){
						echo '<td style="text-align:left;">'.$license['license_gbn'].'</td>';
					}

					if($jaguk_no == 'Y'){
						echo '<td style="text-align:left;">'.$license['license_no'].'</td>';
					}
					if($jaguk_date == 'Y'){
						echo '<td style="text-align:center;">'.$license['license_dt'].'</td>';
					}
					
					if($addr == 'Y'){
						if(strlen($row['m02_ypostno']) == '5'){
							$postNo = $row['m02_ypostno'];
						}else {
							$postNo = getPostNoStyle($row['m02_ypostno']);
						}

						echo '<td style="text-align:center; mso-number-format:\@;" >'.$postNo.'</td>';
						echo '<td style="text-align:left; width:480px;">'.$row['m02_yjuso1'].' '.$row['m02_yjuso2'].'</td>';
					}
					
					if($mobile_work == 'Y'){
						echo '<td style="text-align:center;">'.$row['m02_jikwon_gbn'].'</td>';
					}
					
					if($familyYn == 'Y'){
						echo '<td style="text-align:center;">'.$row['family_yn'].'</td>';
					}

					if($demantiaYn == 'Y'){
						echo '<td style="text-align:center;">'.$row['dementia_yn'].'</td>';
					}

					if($memo == 'Y'){
						echo '<td style="text-align:left; width:480px;">'.$row['m02_memo'].'</td>';
					}
				echo '</tr>';
			
				unset($hourly);
			
			}
		}else{
			echo '<tr>
					<td style="text-align:center;" colspan=\''.$col.'\'>::검색된 데이타가 없습니다.::</td>
				  </tr>';
		}
		
		$conn->row_free();
	?>
	</tbody>
</table>
</form>
<?
	include_once('../inc/_db_close.php');
?>


