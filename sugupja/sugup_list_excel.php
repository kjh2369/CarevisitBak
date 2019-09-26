<?
	include_once('../inc/_db_open.php');
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');
	include_once("../inc/_function.php");

	header( "Content-type: application/vnd.ms-excel" );
	header( "Content-Disposition: attachment; filename=test.xls" );
	header( "Content-Transfer-Encoding: binary" );
	header( "Content-Description: PHP4 Generated Data" );

	//$conn->set_name('euckr');

	#####################################################################
	#
	# 가사간병 소득등급
		$voucher_income[1] = get_voucher_income_list($conn, "'21', '22', '99'");

	# 노인돌봄 소득등급
		$voucher_income[2] = get_voucher_income_list($conn, "'21', '22', '23', '99'");

	# 산모신생아 소득등급
		$voucher_income[3] = get_voucher_income_list($conn, "'24', '25', '99'");

	# 장애인보조 소득등급
		$voucher_income[4] = get_voucher_income_list($conn, "'21', '22', '26', '27', '28', '29', '99'");

		function get_voucher_income_list($conn, $lvl_cd){
			$sql = "select lvl_cd, lvl_id, lvl_nm
				  from income_lvl
				 where lvl_cd in ($lvl_cd)";

			$conn->query($sql);
			$conn->fetch();

			$row_count = $conn->row_count();

			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);

				$list[$i] = array('cd'=>$row['lvl_id'], 'nm'=>$row['lvl_nm']);
			}

			$conn->row_free();

			return $list;
		}
	#
	#####################################################################

	$sugupName = $_POST['sugupName'];					//성명
	$sugupJumin = $_POST['sugupJumin'];					//주민번호
	$manageNo = $_POST['manageNo'];						//관리번호
	$addr = $_POST['addr'];								//주소
	$sugupMobile = $_POST['sugupMobile'];				//휴대폰번호
	$sugupTel = $_POST['sugupTel'];						//수급자일반전화
	$svcFm = $_POST['svcFm'];							//서비스계약일자
	$svcTo = $_POST['svcTo'];							//서비스계약일자
	$bohojaName = $_POST['bohojaName'];					//보호자이름
	$bohojaRel = $_POST['bohojaRel'];					//보호자관계
	$bohojaTel = $_POST['bohojaTel'];					//보호자연락처
	$useService = $_POST['useService'];					//이용상태(구분)
	$useStatGbn = $_POST['useStatGbn'];					//이용상태(구분)
	$useStatStop = $_POST['useStatStop'];				//이용상태(중지사유)
	$level = $_POST['level'];							//요양등급
	$kupyeoMax = $_POST['kupyeoMax'];					//급여한도액
	$chunguMax = $_POST['chunguMax'];					//청구한도액
	$boninYul = $_POST['boninYul'];						//본인부담율
	$boninGum = $_POST['boninGum'];						//본인부담금
	$injungNo = $_POST['injungNo'];					//보험인정번호
	$injungDt = $_POST['injungDt'];					//보험유효기간
	$bungName = $_POST['bungName'];						//병명
	$otherBungName = $_POST['otherBungName'];			//기타병명
	$mainYoy = $_POST['mainYoy'];						//주담당보호사
	$partner = $_POST['partner'];						//바우자
	$buYoy = $_POST['buYoy'];							//부담당보호사
	$nintyExceed = $_POST['nintyExceed'];				//90분초과
	$bathExceed = $_POST['bathExceed'];					//목욕초과
	$clientGbn  = $_POST['clientGbn'];					//고객구분
	$familyGbn  = $_POST['familyGbn'];					//동거구분
	$contractType	= $_POST['contractType'];			//계약유형
	$billPhone  = $_POST['billPhone'];					//현금영수증발행 연락처
	$memo		= $_POST['memo'];						//메모
	$memTeam	= $_POST['memTeam'];					//담당팀장

	$find_center_code	= $_SESSION["userCenterCode"];
	$find_center_name	= $_REQUEST['find_center_name'];
	$find_su_name		= $_REQUEST['find_su_name'];
	$find_su_ssn		= $_REQUEST['find_su_ssn'];
	$find_su_phone		= str_replace('-', '', $_REQUEST['find_su_phone']);
	$find_su_stat		= $_REQUEST['find_su_stat'] != '' ? $_REQUEST['find_su_stat'] : '1';
	$find_center_kind	= $_POST['find_center_kind'];
	$sst	= $_POST['order_sst'];
	$sod	= $_POST['order_sod'];
	$sfl	= $_POST['order_sfl'];
	$team	= $_POST['find_team'];
	//echo $find_su_stat.'/';
	
	$html = '';
	$html2 = '';

	if($sugupName == 'Y'){
		$html .= '<th rowspan="2" style="font-family:굴림; background-color:#efefef;">성 명</th>';
	}
	if($sugupJumin == 'Y'){
		$html .= '<th rowspan="2" style="font-family:굴림; background-color:#efefef;">주민번호</th>';
	}
	if($manageNo == 'Y'){
		$html .= '<th rowspan="2" style="font-family:굴림; background-color:#efefef;">관리번호</th>';
	}

	if($sugupTel == 'Y' or $sugupMobile == 'Y'){
		if($sugupTel == 'Y'){
			$contact[0] = $sugupTel;
		}
		if($sugupMobile == 'Y'){
			$contact[1] = $sugupMobile;
		}

		$colspan = sizeof($contact);
		$html .= '<th colspan="'.$colspan.'" style="font-family:굴림; background-color:#efefef;">연락처</th>';
	}

	if($bohojaRel == 'Y' or $bohojaName == 'Y' or $bohojaTel == 'Y'){
		if($bohojaRel == 'Y'){
			$bohoja[0] = $bohojaRel;
		}
		if($bohojaName == 'Y'){
			$bohoja[1] = $bohojaName;
			$bohoja[2] = 'Y';
		}

		if($bohojaTel == 'Y'){
			$bohoja[3] = $bohojaTel;
		}

		$colspan = sizeof($bohoja);
		$html .= '<th colspan="'.$colspan.'" style="font-family:굴림; background-color:#efefef;">보호자</th>';
	}

	if($useStatGbn == 'Y' or $useStatStop == 'Y'){
		if($useStatGbn == 'Y'){
			$useStatus[0] = $useStatGbn;
		}
		if($useStatStop == 'Y'){
			$useStatus[1] = $useStatStop;
		}

		$colspan = sizeof($useStatus);
		$html .= '<th colspan="'.$colspan.'" style="font-family:굴림; background-color:#efefef;">이용상태</th>';
	}

	if($svcFm == 'Y' or $svcTo == 'Y'){
		if($svcFm == 'Y'){
			$svcContract[0] = $svcFm;
		}
		if($svcTo == 'Y'){
			$svcContract[1] = $svcTo;
		}

		$colspan = sizeof($svcContract);
		$html .= '<th colspan="'.$colspan.'" style="font-family:굴림; background-color:#efefef;">서비스계약기간</th>';
	}
	

	if($useService == 'Y'){
		$html .= '<th rowspan="2" style="font-family:굴림; background-color:#efefef;">이용서비스</th>';
	}

	if($clientGbn == 'Y'){
		$html .= '<th rowspan="2" style="font-family:굴림; background-color:#efefef;">고객구분</th>';
	}

	if($familyGbn == 'Y'){
		$html .= '<th rowspan="2" style="font-family:굴림; background-color:#efefef;">동거구분</th>';
	}

	if($level == 'Y'){
		$html .= '<th rowspan="2" style="font-family:굴림; background-color:#efefef;">등급</th>';
	}

	if($kupyeoMax == 'Y'){
		$html .= '<th rowspan="2" style="font-family:굴림; background-color:#efefef;">급여한도</th>';
	}

	if($chunguMax == 'Y'){
		$html .= '<th rowspan="2" style="font-family:굴림; background-color:#efefef;">청구한도</th>';
	}

	if($boninYul == 'Y'){
		$html .= '<th rowspan="2" style="font-family:굴림; background-color:#efefef;">본인부담율</th>';
	}

	if($boninGum == 'Y'){
		$html .= '<th rowspan="2" style="font-family:굴림; background-color:#efefef;">본인부담금</th>';
	}

	if($mainYoy == 'Y' or $partner == 'Y' or $buYoy == 'Y'){
		if($mainYoy == 'Y'){
			$svcSupply[0] = $mainYoy;
		}
		if($partner == 'Y'){
			$svcSupply[1] = $partner;
		}
		if($buYoy == 'Y'){
			$svcSupply[2] = $buYoy;
		}

		$colspan = sizeof($svcSupply);
		$html .= '<th colspan="'.$colspan.'" style="font-family:굴림; background-color:#efefef;">서비스제공자</th>';
	}

	if($injungNo == 'Y' or $injungDt == 'Y'){
		if($injungNo == 'Y'){
			$bohum[0] = $injungNo;
		}
		if($injungDt == 'Y'){
			$bohum[1] = $injungDt;
		}

		$colspan = (sizeof($bohum)+1);
		$html .= '<th colspan="'.$colspan.'" style="font-family:굴림; background-color:#efefef;">장기요양보험</th>';
	}

	if($nintyExceed == 'Y'){
		$html .= '<th rowspan="2" style="font-family:굴림; background-color:#efefef;">90분초과</th>';
	}

	if($bathExceed == 'Y'){
		$html .= '<th rowspan="2" style="font-family:굴림; background-color:#efefef;">목욕초과</th>';
	}

	if($bungName == 'Y'){
		$html .= '<th rowspan="2" style="font-family:굴림; background-color:#efefef;">병명</th>';
	}

	if($otherBungName == 'Y'){
		$html .= '<th rowspan="2" style="font-family:굴림; background-color:#efefef;">기타병명</th>';
	}

	if($addr == 'Y'){
		$html .= '<th colspan="2" style="font-family:굴림; background-color:#efefef;">주소</th>';
	}
	
	if($memTeam == 'Y'){
		$html .= '<th rowspan="2" style="font-family:굴림; background-color:#efefef;">담당팀장</th>';
	}

	if($contractType == 'Y'){
		$html .= '<th rowspan="2" style="font-family:굴림; background-color:#efefef;">계약유형</th>';
	}
	
	if($billPhone == 'Y'){
		$html .= '<th rowspan="2" style="font-family:굴림; background-color:#efefef;">현금영수증발행 연락처</th>';
	}
	
	if($memo == 'Y'){
		$html .= '<th rowspan="2" style="font-family:굴림; background-color:#efefef;">Memo</th>';
	}

	/*
	if($injungNo == 'Y'){
		echo '<th style="font-family:굴림; background-color:#efefef;">보험인증번호</th>';
	}
	if($injungDay == 'Y'){
		echo '<th style="font-family:굴림; background-color:#efefef;">유효기간</th>';
	}
	*/

	$col = 1;

	if($sugupName == 'Y'){
		$col += 1;
	}

	if($sugupJumin == 'Y'){
		$col += 1;
	}

	if($manageNo == 'Y'){
		$col += 1;
	}

	if($sugupTel == 'Y'){
		$html2 .= '<th style="font-family:굴림; background-color:#efefef;">유선</th>';
		$col += 1;
	}
	if($sugupMobile == 'Y'){
		$html2 .= '<th style="font-family:굴림; background-color:#efefef;">무선</th>';
		$col += 1;
	}


	if($bohojaRel == 'Y'){
		$html2 .= '<th style="font-family:굴림; background-color:#efefef;">관계</th>';
		$col += 1;
	}
	if($bohojaName == 'Y'){
		$html2 .= '<th style="font-family:굴림; background-color:#efefef;">성명</th>
				   <th style="font-family:굴림; background-color:#efefef;">생년월일</th>';
		$col += 2;
	}
	if($bohojaTel == 'Y'){
		$html2 .= '<th style="font-family:굴림; background-color:#efefef;">연락처</th>';
		$col += 1;
	}

	if($useStatGbn == 'Y'){
		$html2 .= '<th style="font-family:굴림; background-color:#efefef;">구분</th>';
		$col += 1;
	}
	if($useStatStop == 'Y'){
		$html2 .= '<th style="font-family:굴림; background-color:#efefef;">중지사유</th>';
		$col += 1;
	}

	if($svcFm == 'Y'){
		$html2 .= '<th style="font-family:굴림; background-color:#efefef;">시작</th>';
		$col += 1;
	}
	if($svcTo == 'Y'){
		$html2 .= '<th style="font-family:굴림; background-color:#efefef;">종료</th>';
		$col += 1;
	}

	if($useService == 'Y'){
		$col += 1;
	}

	if($clientGbn == 'Y'){
		$col += 1;
	}

	if($familyGbn == 'Y'){
		$col += 1;
	}

	if($level == 'Y'){
		$col += 1;
	}

	if($kupyeoMax == 'Y'){
		$col += 1;
	}

	if($chunguMax == 'Y'){
		$col += 1;
	}

	if($boninYul == 'Y'){
		$col += 1;
	}

	if($boninGum == 'Y'){
		$col += 1;
	}

	if($mainYoy == 'Y'){
		$html2 .= '<th style="font-family:굴림; background-color:#efefef;">주담당</th>';
		$col += 1;
	}
	if($partner == 'Y'){
		$html2 .= '<th style="font-family:굴림; background-color:#efefef;">배우자</th>';
		$col += 1;
	}
	if($buYoy == 'Y'){
		$html2 .= '<th style="font-family:굴림; background-color:#efefef;">부담당</th>';
		$col += 1;
	}

	if($injungNo == 'Y'){
		$html2 .= '<th style="font-family:굴림; background-color:#efefef;">인정번호</th>';
		$col += 1;
	}
	if($injungDt == 'Y'){
		$html2 .= '<th style="font-family:굴림; background-color:#efefef;">유효기간(시작)</th>';
		$html2 .= '<th style="font-family:굴림; background-color:#efefef;">유효기간(종료)</th>';
		$col += 2;
	}

	if($nintyExceed == 'Y'){
		$col += 1;
	}

	if($bathExceed == 'Y'){
		$col += 1;
	}

	if($bungName == 'Y'){
		$col += 1;
	}

	if($otherBungName == 'Y'){
		$col += 1;
	}

	if($addr == 'Y'){
		$html2 .= '<th style="font-family:굴림; background-color:#efefef;">우편번호</th>';
		$html2 .= '<th style="font-family:굴림; background-color:#efefef;">상세주소</th>';
		$col += 2;
	}
	
	if($memTeam == 'Y'){
		$col += 1;
	}

	if($billPhone == 'Y'){
		$col += 1;
	}

	if($contractType == 'Y'){
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
<form name="f" method="post">
<?
	$sql = "select m00_cname
			  from m00center
			 where m00_mcode = '$find_center_code'";
	$cname = $conn -> get_data($sql);
?>
<table style="font-family:굴림; margin-top:-1px;" border=1>
	<thead>
		<tr>
			<td colspan="<?=($col)?>" style="font-family:굴림; font-size:20pt; font-weight:bold; text-align:left;">고객명부</td>
		</tr>
		<tr>
			<td colspan="<?=($col)?>" style="font-family:굴림; font-size:15pt; font-weight:bold;" >기관명 : <?=$cname;?></td>
		</tr>
		<tr>
			<th class="head" rowspan="2" style="font-family:굴림; background-color:#efefef;">No</th><?
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
		$wsl2 = "";

		
		if ($find_center_kind != 'all')
			$wsl .= ' and m03_mkind = \''.$find_center_kind.'\'';

		if (!empty($find_su_ssn))
			$wsl .= ' and left(m03_jumin,'.strlen($find_su_ssn).') = \''.$find_su_ssn.'\'';

		if (!empty($find_su_phone))
			$wsl .= ' and left(m03_hp,'.strlen($find_su_phone).') = \''.$find_su_phone.'\'';

		if (!empty($find_su_name))
			$wsl .= ' and m03_name >= \''.$find_su_name.'\'';

		if ($find_su_stat != 'all'){
			if ($find_su_stat == '1')
				$wsl2 .= ' and svc_stat = \'1\'';
			else
				$wsl2 .= ' and svc_stat != \'1\'';
		}
		
		$sql = 'select m91_code as cd
				,      m91_kupyeo as pay
				  from m91maxkupyeo 
				  where date_format(now(),\'%Y%m%d\') >= date_format(m91_sdate,\'%Y%m%d\')
				  and date_format(now(),\'%Y%m%d\') <= date_format(m91_edate,  \'%Y%m%d\')';
		
		$arrLimitPay = $conn->_fetch_array($sql);

		//고객 인정번호 시작일자
		$sql = 'SELECT jumin
				,      MAX(from_dt) AS dt
				  FROM client_his_lvl
				 WHERE org_no = \''.$_SESSION["userCenterCode"].'\'
				   AND svc_cd = \'0\'
				 GROUP BY jumin';

		$loInjungStartDt = $conn->_fetch_array($sql, 'jumin');

		//고객 인정번호 만료일자
		$sql = 'SELECT jumin
				,      MAX(to_dt) AS dt
				  FROM client_his_lvl
				 WHERE org_no = \''.$_SESSION["userCenterCode"].'\'
				   AND svc_cd = \'0\'
				 GROUP BY jumin';

		$loInjungLastDt = $conn->_fetch_array($sql, 'jumin');

		if($lbTestMode){
			
			if($find_su_stat == 'all'){
				if($find_center_kind != 'all'){
					$join = 'inner';
				}else {
					$join = 'left';
				}

				if (!Empty($find_su_name) ||
					!Empty($find_su_ssn) ||
					!Empty($find_su_phone)){
					$join = 'inner';
				}
			}

			$sql = "select jumin							
						, name							
						, svc_cd as mkind							
						, svc_stat as sugup_stat						
						, svc_reason as stop_reason
						, client_no
						, post_no
						, juso1
						, juso2
						, hp
						, tel
						, yboho_name
						, yboho_jumin
						, yboho_gwange	
						, yboho_phone
						, kupyeo_max
						, kupyeo_1
						, kupyeo_2
						, byungmung
						, disease_nm
						, yoyangsa1_cd							
						, yoyangsa1_nm							
						, yoyangsa2_nm													
						, partner
						, bath_add_yn
						, stat_nogood
						, memo
						, from_dt as gaeyak_fm							
						, to_dt as gaeyak_to
						, dayCount
						, injung_from
						, injung_to
						, injung_no
						, Level							
						, skind as sugupGubun
						, bonin_yul
						, bill_phone
						, contType
						, teamName
						from (select mst.jumin							
						, mst.name							
						, svc.svc_cd							
						, svc.svc_stat						
						, svc.svc_reason
						, mst.client_no
						, mst.post_no
						, mst.juso1
						, mst.juso2
						, mst.hp
						, mst.tel
						, mst.yboho_name
						, mst.yboho_jumin
						, mst.yboho_gwange	
						, mst.yboho_phone
						, mst.kupyeo_max
						, mst.kupyeo_1
						, mst.kupyeo_2
						, DAS.m81_name as byungmung
						, mst.disease_nm
						, mst.yoyangsa1_cd							
						, mst.yoyangsa1_nm							
						, mst.yoyangsa2_nm													
						, mst.partner
						, mst.bath_add_yn
						, mst.stat_nogood
						, mst.memo
						, svc.from_dt 							
						, svc.to_dt
						, datediff( date_add(date_format(lvl.app_no, '%Y-%m-%d'), interval -3 month), date_format(now(), '%Y-%m-%d')) as dayCount
						, lvl.app_no as injung_no
						, lvl.from_dt as injung_from
						, lvl.to_dt as injung_to							
						, case lvl.svc_cd when '0' then case lvl.level when '9' then '일반' else lvl.level end
						when '4' then dis.svc_lvl else '' end as Level							
						, case kind.kind when '3' then '기초'
						when '2' then '의료'
						when '4' then '경감' else '일반' end as skind
						, kind.rate as bonin_yul
						, opt.bill_phone
						, case opt.cont_type when '01' then '전화,인터넷' when '02' then '직접 발굴' when '03' then '지인소개' when '04' then '근무자를 통한 소개' when '05' then '공단자료' when '06' then '외부인수' when '07' then '간병연계' when '08' then '지점연계' else ' ' end as contType
						, team.teamName
						, team.deduct_amt
						, team.deduct_rate
						from (							
						select org_no							
						, min(svc_cd) as svc_cd							
						, jumin							
						, case when date_format(now(),'%Y%m%d') >= date_format(from_dt,'%Y%m%d')
						and date_format(now(),'%Y%m%d') <= date_format(to_dt, '%Y%m%d') then from_dt else max(from_dt) end as from_dt
						, case when date_format(now(),'%Y%m%d') >= date_format(from_dt,'%Y%m%d')
						and date_format(now(),'%Y%m%d') <= date_format(to_dt, '%Y%m%d') then to_dt else max(to_dt) end as to_dt				
						, (select svc_stat
							 from client_his_svc as tmp     
							where tmp.org_no = '".$_SESSION["userCenterCode"]."'    
							  and tmp.jumin = svc.jumin
							  order by case when date_format(now(),'%Y%m%d') >= date_format(from_dt,'%Y%m%d')									
							  and date_format(now(),'%Y%m%d') <= date_format(to_dt, '%Y%m%d') then 1 else 2 end, seq desc      
							limit 1) as svc_stat 							
						, (select svc_reason
							 from client_his_svc as tmp     
							where tmp.org_no = '".$_SESSION["userCenterCode"]."'    
							  and tmp.jumin = svc.jumin
							  order by case when date_format(now(),'%Y%m%d') >= date_format(from_dt,'%Y%m%d')									
							  and date_format(now(),'%Y%m%d') <= date_format(to_dt, '%Y%m%d') then 1 else 2 end, seq desc      
							limit 1) as svc_reason 							
						from client_his_svc	as svc						
						where org_no = '".$_SESSION["userCenterCode"]."'";
					
				if($find_su_stat == '1'){		
					$sql .= " and date_format(now(),'%Y%m%d') >= date_format(from_dt,'%Y%m%d')							
							  and date_format(now(),'%Y%m%d') <= date_format(to_dt, '%Y%m%d')"; 
				}
			  
				if ($find_su_stat != 'all'){
					if ($find_su_stat == '1')
						$sql .= ' and svc_stat = \'1\'';
					else
						$sql .= ' and svc_stat != \'1\'';
				}

			   $sql .= " $wsl2							
						group by jumin							
						order by case when date_format(now(),'%Y%m%d') >= date_format(from_dt,'%Y%m%d')							
						and date_format(now(),'%Y%m%d') <= date_format(to_dt, '%Y%m%d') then 1 else 2 end, seq desc) as svc							
						$join join (							
						select m03_mkind as kind							
						, m03_jumin as jumin							
						, m03_name as name							
						, m03_client_no as client_no
						, m03_post_no as post_no
						, m03_juso1 as juso1
						, m03_juso2 as juso2
						, m03_hp as hp
						, m03_tel as tel			
						, m03_yboho_name as yboho_name
						, m03_yboho_juminno as yboho_jumin
						, m03_yboho_gwange as yboho_gwange
						, m03_yboho_phone as yboho_phone
						, m03_kupyeo_max as kupyeo_max
						, m03_kupyeo_1 as kupyeo_1
						, m03_kupyeo_2 as kupyeo_2
						, m03_byungmung as byungmung
						, m03_disease_nm as disease_nm
						, m03_partner as partner
						, m03_yoyangsa1_nm as yoyangsa1_nm
						, m03_yoyangsa2_nm as yoyangsa2_nm
						, m03_yoyangsa1 as yoyangsa1_cd										
						, m03_stat_nogood as stat_nogood
						, m03_bath_add_yn as bath_add_yn
						, m03_memo as memo
						from m03sugupja							
						where m03_ccode = '".$_SESSION["userCenterCode"]."'
						  $wsl
						) as mst							
						on svc.jumin = mst.jumin							
						and svc.svc_cd = mst.kind							
						left join (							
						select jumin
						, app_no
						, svc_cd							
						, (select level										
						from client_his_lvl as tmp										
						where tmp.org_no = '".$_SESSION["userCenterCode"]."'							
						and tmp.jumin   = lvl.jumin
						and tmp.svc_cd  = lvl.svc_cd
						order by case when date_format(now(),'%Y%m%d') >= date_format(from_dt,'%Y%m%d')										
						and date_format(now(),'%Y%m%d') <= date_format(to_dt, '%Y%m%d') then 1 else 2 end, seq desc		
						limit 1) as level							
						, MAX(from_dt) AS from_dt							
						, MAX(to_dt) AS to_dt							
						from client_his_lvl	as lvl						
						where org_no = '".$_SESSION["userCenterCode"]."'									
						GROUP BY jumin, svc_cd
						ORDER BY case when date_format(now(),'%Y%m%d') >= date_format(from_dt,'%Y%m%d')										
						AND date_format(now(),'%Y%m%d') <= date_format(to_dt, '%Y%m%d') then 1 else 2 end, seq desc	
						) as lvl							
						on svc.jumin = lvl.jumin							
						and svc.svc_cd = lvl.svc_cd left join (							
						select jumin
						, (select rate										
						from client_his_kind as tmp										
						where tmp.org_no = '".$_SESSION["userCenterCode"]."'							
						and tmp.jumin = kind.jumin										
						order by case when date_format(now(),'%Y%m%d') >= date_format(from_dt,'%Y%m%d')										
						and date_format(now(),'%Y%m%d') <= date_format(to_dt, '%Y%m%d') then 1 else 2 end, seq desc		
						limit 1) as rate
						, (select kind										
						from client_his_kind as tmp										
						where tmp.org_no = '".$_SESSION["userCenterCode"]."'							
						and tmp.jumin = kind.jumin										
						order by case when date_format(now(),'%Y%m%d') >= date_format(from_dt,'%Y%m%d')										
						and date_format(now(),'%Y%m%d') <= date_format(to_dt, '%Y%m%d') then 1 else 2 end, seq desc		
						limit 1) as kind							
						, from_dt							
						, to_dt							
						from client_his_kind as kind							
						where org_no = '".$_SESSION["userCenterCode"]."'		
						group by jumin
						ORDER BY case when date_format(now(),'%Y%m%d') >= date_format(from_dt,'%Y%m%d')										
						AND date_format(now(),'%Y%m%d') <= date_format(to_dt, '%Y%m%d') then 1 else 2 end, seq desc	
						) as kind							
						on svc.jumin = kind.jumin							
						left join (							
						select jumin							
						, svc_lvl							
						, from_dt							
						, to_dt							
						from client_his_dis							
						where org_no = '".$_SESSION["userCenterCode"]."'				
						and date_format(now(),'%Y%m%d') >= date_format(from_dt,'%Y%m%d')							
						and date_format(now(),'%Y%m%d') <= date_format(to_dt, '%Y%m%d')	
						group by jumin
						) as dis							
						on svc.jumin = dis.jumin
						left join (							
						select jumin
						, teamName
						, svc_cd
						, deduct_amt
						, deduct_rate
						from client_his_team
						left join (select m02_yjumin
									,	   m02_yname as teamName
									  from m02yoyangsa
									 where m02_ccode = '".$_SESSION["userCenterCode"]."') as mem
						on mem.m02_yjumin = team_cd
						where org_no = '".$_SESSION["userCenterCode"]."'			
						and date_format(now(),'%Y%m') >= from_ym							
						and date_format(now(),'%Y%m') <= to_ym	
						and del_flag = 'N'
						GROUP BY jumin, svc_cd							
						) as team
						on team.jumin = svc.jumin
						and team.svc_cd = svc.svc_cd
						left join m81gubun as DAS
						on DAS.m81_gbn  = 'DAS'
						and DAS.m81_code = byungmung
						left join client_option as opt
						on opt.org_no = '".$_SESSION["userCenterCode"]."'
						and opt.jumin = svc.jumin
						) as t 
						";
				
				if ($team){
					$sql .= ' where teamName = \''.$team.'\'';
				}
				
				if (!$sst) {
					$sql .=	 ' order by name, jumin,svc_cd';
				}else {
					$sql .= " order by $sst $sod ";
				}
	
				
			//if($debug) echo nl2br($sql); 
			
		}else {
			$sql = "select m03_mkind as mkind
					,	   m03_name as name
					,	   m03_jumin as jumin
					,      m03_client_no as client_no
					,      m03_post_no as post_no
					,      m03_juso1 as juso1
					,      m03_juso2 as juso2
					,      m03_hp as hp
					,      m03_tel as tel
					,      m03_gaeyak_fm as gaeyak_fm
					,	   m03_gaeyak_to as gaeyak_to
					,      m03_yboho_name as yboho_name
					,      m03_yboho_juminno as yboho_jumin
					,      m03_yboho_gwange as yboho_gwange
					,      m03_yboho_phone as yboho_phone
					,      case m03_sugup_status when '1' then '수급중' when '2' then '계약해지' when '3' then '보류' when '4' then '사망' when '5' then '타 기관 이전' when '6' then '등외판정' when '7' then '입원' else ' ' end  as sugup_stat
					,      case m03_sugup_status when '1' then '이용' when '2' then '중지' else ' ' end  as use_stat
					,	   m03_stop_reason as stop_reson
					,      LVL.m81_name as Level
					,      m03_kupyeo_max as kupyeo_max
					,      case when m03_mkind = '0' then STP.m81_name else m03_skind end as sugupGubun
					,      m03_kupyeo_1 as kupyeo_1
					,      m03_bonin_yul as bonin_yul
					,      m03_kupyeo_2 as kupyeo_2
					,      m03_injung_no as injung_no
					,	   m03_injung_from as injung_from
					,	   m03_injung_to as injung_to
					,      DAS.m81_name as Byungmung
					,      m03_disease_nm as disease_nm
					,	   m03_partner as partner
					,      m03_yoyangsa1_nm as yoyangsa1_nm
					,      m03_yoyangsa2_nm as yoyangsa2_nm
					,	   m03_stat_nogood as stat_nogood
					,	   m03_bath_add_yn as bath_add_yn
					,      datediff( date_add(date_format(m03_injung_to, '%Y-%m-%d'), interval -3 month), date_format(now(), '%Y-%m-%d')) as dayCount
					,	   m03_memo as memo
					  from m03sugupja
					  left join m00center
						on m00_mcode = m03_ccode
					   and m00_mkind = ".$conn->_client_kind()."
					  left join m81gubun as LVL
						on LVL.m81_gbn  = 'LVL'
					   and LVL.m81_code = m03_ylvl
					  left join m81gubun as DAS
						on DAS.m81_gbn  = 'DAS'
					   and DAS.m81_code = byungmung
					  left join m81gubun as STP
						on STP.m81_gbn  = 'STP'
					   and STP.m81_code = m03_skind
					 where m03_ccode is not null
					   and m03_del_yn = 'N' $wsl
					 order by ".($_SESSION["userLevel"] == "A" ? "m00_cname," : "")." m03_name";
		}
		
		//if($debug) echo nl2br($sql); exit;

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();
		
		$lsChkDt = $myF->dateAdd('month', 3, date('Y-m-d'), 'Y-m-d');

		if ($row_count > 0){
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);
				
				$boho_birth = $myF->issToBirthday((strlen($row['yboho_jumin']) == '7' ? $row['yboho_jumin'].'000000' : ''),'.');					
				
				foreach($arrLimitPay as $pay){
					
					if ($pay['cd'] == $row['Level']){
						$limitPay = $pay['pay'];
						break;
					}
				}
				
				$expenseAmt = $myF->cutOff($limitPay * $row['bonin_yul'] * 0.01);

				//계약유형
				$sql = 'SELECT  case cont_type when \'01\' then \'전화,인터넷\' when \'02\' then \'직접 발굴\' when \'03\' then \'지인소개\' when \'04\' then \'근무자를 통한 소개\' when \'05\' then \'공단자료\' else \' \' end as contType 
						FROM	client_option
						WHERE	org_no = \''.$_SESSION["userCenterCode"].'\'
						AND		jumin  = \''.$row['jumin'].'\'';

				$contType = $conn -> get_data($sql);
				

				$kind = $conn->kind_name_svc($row['mkind']);
				
				//$from_dt = $loInjungLastDt[$row['jumin']]['dt'];
				//$no_dt[$i] = $loInjungLastDt[$row['jumin']]['dt'];
				
				#인정시작일자
				$fromDt = $myF->dateStyle($loInjungStartDt[$row['jumin']]['dt']);
				
				#인정만료일자
				$no_dt[$i] = $loInjungLastDt[$row['jumin']]['dt'];
				
				
				$toDt = $myF->dateStyle($loInjungLastDt[$row['jumin']]['dt']);

				if ($row['mkind'] == 0){
					$incomes = $row['sugupGubun'];
				}else{
					$incomes = '';

					for($j=0; $j<sizeof($voucher_income[$row['mkind']]); $j++){
						if ($voucher_income[$row['mkind']][$j]['cd'] == $row['sugupGubun']){
							$incomes = $voucher_income[$row['mkind']][$j]['nm'];
							break;
						}
					}
				}

				$year = date('Y', mktime());
				$month = date('m', mktime());

				//요양사의 나이
				$yoyAge = $myF->man_age($row['yoyangsa1_cd'], $month, $year);


				/***************************************
					가족보호사 테이블 존재여부
				***************************************/
				$sql = 'select count(*)
						  from client_family
						 where org_no   = \''.$_SESSION["userCenterCode"].'\'
						   and cf_jumin = \''.$row['jumin'].'\'';
				$family = $conn -> get_data($sql);

				//동거구분
				if($family > 0){
					$family_gbn = '60분(동거)';

					if($yoyAge >= 65){
						if($row['partner']=='Y'){
							$family_gbn = '90분(동거)';
						}
					}

					if($row['stat_nogood'] == 'Y'){
						$family_gbn = '90분(동거)';
					}

				}else {
					$family_gbn = '';
				}

				if ($row['sugup_stat'] == '1'){
					$stat = '이용';
					$stop_reason = '';
				}else{
					if ($row['mkind'] == '0'){
						$reason = array(
								'01'=>'계약해지'
							,	'02'=>'보류'
							,	'03'=>'사망'
							,	'04'=>'타업체이용'
							,	'05'=>'등외판정'
							,	'06'=>'입원'
							,	'07'=>'무리한요구'
							,	'08'=>'단순서비스종료'
							,	'09'=>'근무자미투입'
							,	'10'=>'거주지이전'
							,	'11'=>'건강호전'
							,	'12'=>'부담금미납'
							,	'13'=>'지점이동'
							,	'14'=>'요양입소'
							,	'99'=>'기타'
						);
					}else{
						$reason = array(
								'01'=>'본인포기'
							,	'02'=>'사망'
							,	'03'=>'말소'
							,	'04'=>'전출'
							,	'05'=>'미사용'
							,	'06'=>'본인부담금미납'
							,	'07'=>'사업종료'
							,	'08'=>'자격종료'
							,	'09'=>'판정결과반영'
							,	'10'=>'자격정지'
							,	'99'=>'기타'
						);
					}
					
					


					$stat = '중지';
					$stop_reason = $reason[$row['stop_reason']];

				}
				
				echo '<tr>';
					echo '<td style="font-family:굴림; text-align:center">'.($i + 1).'</td>';

					#성명
					if($sugupName == 'Y'){
						echo '<td style="font-family:굴림; text-align:left; padding-left:5px;">'.$row['name'].'</td>';
					}
					#주민번호
					if($sugupJumin == 'Y'){
						echo '<td style="font-family:굴림; text-align:center;">'.$myF->issNo($row['jumin'],'.').'</td>';
					}
					#관리번호
					if($manageNo == 'Y'){
						echo '<td style="font-family:굴림; text-align:center;">'.$row['client_no'].'</td>';
					}
					#유선
					if($sugupTel == 'Y'){
						echo '<td style="font-family:굴림; text-align:left; padding-left:5px;">'.$myF->phoneStyle($row['tel']).'</td>';
					}
					#무선
					if($sugupMobile == 'Y'){
						echo '<td style="font-family:굴림; text-align:left; padding-left:5px;">'.$myF->phoneStyle($row['hp']).'</td>';
					}
					#보호자관계
					if($bohojaRel == 'Y'){
						echo '<td style="font-family:굴림; text-align:left; padding-left:5px;">'.$row['yboho_gwange'].'</td>';
					}
					#보호자성명
					if($bohojaName == 'Y'){
						echo '<td style="font-family:굴림; text-align:left; padding-left:5px;">'.$row['yboho_name'].'</td>';
						echo '<td style="font-family:굴림; text-align:left; padding-left:5px;">'.$boho_birth.'</td>';
					}
					#보호자연락처
					if($bohojaTel == 'Y'){
						echo '<td style="font-family:굴림; text-align:left; padding-left:5px;">'.$myF->phoneStyle($row['yboho_phone']).'</td>';
					}

					#이용상태(구분)
					if($useStatGbn == 'Y'){
						if($lbTestMode){
							echo '<td style="font-family:굴림; text-align:left; padding-left:5px;">'.$stat.'</td>';
						}else {
							echo '<td style="font-family:굴림; text-align:left; padding-left:5px;">'.($row['mkind'] == 0 ? $row['sugup_stat'] : $row['use_stat']).'</td>';
						}
					}

					#이용상태(중지사유)
					if($useStatStop == 'Y'){
						if($lbTestMode){
							echo '<td style="font-family:굴림; text-align:left; padding-left:5px;">'.$stop_reason.'</td>';
						}else {
							echo '<td style="font-family:굴림; text-align:left; padding-left:5px;">'.$row['stop_reason'].'</td>';
						}
					}

					#계약시작일
					if($svcFm == 'Y'){
						echo '<td style="font-family:굴림; text-align:center; padding-left:5px;">'.$myF->dateStyle($row['gaeyak_fm'],'.').'</td>';
					}

					#계약종료일
					if($svcTo == 'Y'){
						echo '<td style="font-family:굴림; text-align:center; padding-left:5px;">'.$myF->dateStyle($row['gaeyak_to'],'.').'</td>';
					}

					#이용서비스
					if($useService == 'Y'){
						echo '<td style="font-family:굴림; text-align:left; padding-left:5px;">'.$kind.'</td>';
					}

					#고객구분
					if($clientGbn == 'Y'){
						echo '<td style="font-family:굴림; text-align:left; padding-left:5px;">'.$incomes.'</td>';
					}

					#고객구분
					if($familyGbn == 'Y'){
						echo '<td style="font-family:굴림; text-align:left; padding-left:5px;">'.$family_gbn.'</td>';
					}

					#요양등급
					if($level == 'Y'){
						echo '<td style="font-family:굴림; text-align:left; padding-left:5px;">'.$myF->_lvlNm($row['Level']).'</td>';
					}

					#급여한도
					if($kupyeoMax == 'Y'){
						$KupyeoMax = ($limitPay != '0' ? number_format($limitPay) : '');
						echo '<td style="font-family:굴림; text-align:right; padding-right:5px;">'.$KupyeoMax.'</td>';
					}
					#청구한도
					if($chunguMax == 'Y'){
						$ChunguMax = ($row['mkind'] == 0 ? $row['kupyeo_1'] : '');
						$ChunguMax = ($ChunguMax != 0 ? number_format($ChunguMax) : '');
						echo '<td style="font-family:굴림; text-align:right; padding-right:5px;">'.$ChunguMax.'</td>';
					}

					#본인부담율
					if($boninYul == 'Y'){
						$BoninYul = ($row['bonin_yul'] != 0 ? $row['bonin_yul'] : '');
						echo '<td style="font-family:굴림; text-align:right; padding-right:5px;">'.$BoninYul.'%</td>';
					}

					#본인부담금
					if($boninGum == 'Y'){

						$BoninGum = ($row['mkind'] == 0 ? $expenseAmt : $row['kupyeo_1']);
						$BoninGum = ($BoninGum != '0' ? number_format($BoninGum) : '');

						echo '<td style="font-family:굴림; text-align:right; padding-right:5px;">'.$BoninGum.'</td>';
					}

					#주담당
					if($mainYoy == 'Y'){
						echo '<td style="font-family:굴림; text-align:left; padding-left:5px;">'.$row['yoyangsa1_nm'].'</td>';
					}
					#배우자
					if($partner == 'Y'){
						$Partner = ($row['partner'] == 'Y' ? $row['partner'] : '');
						echo '<td style="font-family:굴림; text-align:center; padding-left:5px;">'.$Partner.'</td>';
					}
					#부담당
					if($buYoy == 'Y'){
						echo '<td style="font-family:굴림; text-align:left; padding-left:5px;">'.$row['yoyangsa2_nm'].'</td>';
					}
				

					#장기요양보험(인증번호)
					if($injungNo == 'Y'){
						echo '<td style="font-family:굴림; text-align:left; padding-left:5px;">'.$row['injung_no'].'</td>';
					}

					#장기요양보험(유효기간)
					if($injungDt == 'Y'){
						echo '<td style="font-family:굴림; text-align:center; padding-left:5px;">'.$fromDt.'</td>';  //시작
						echo '<td style="font-family:굴림; text-align:center; padding-left:5px;">'.$toDt.'</td>';	//종료
					}


					#90분초과
					if($nintyExceed == 'Y'){
						$NintyExceed = ($row['stat_nogood'] == 'Y' ? $row['stat_nogood'] : '');
						echo '<td style="font-family:굴림; text-align:center; padding-right:5px;">'.$NintyExceed.'</td>';
					}

					#목욕초과
					if($bathExceed == 'Y'){
						$BathExceed = ($row['bath_add_yn'] == 'Y' ? $row['bath_add_yn'] : '');
						echo '<td style="font-family:굴림; text-align:center; padding-right:5px;">'.$BathExceed.'</td>';
					}

					#병명
					if($bungName == 'Y'){
						echo '<td style="font-family:굴림; text-align:left; padding-left:5px;">'.$row['byungmung'].'</td>';
					}
					#기타병명
					if($otherBungName == 'Y'){
						echo '<td style="font-family:굴림; text-align:left; padding-left:5px;">'.$row['disease_nm'].'</td>';
					}

					if($addr == 'Y'){
						if(strlen($row['post_no']) == '5'){
							$postNo = $row['post_no'];
						}else {
							$postNo = getPostNoStyle($row['post_no']);
						}

						echo '<td style="font-family:굴림; text-align:left; padding-left:5px; mso-number-format:\@;">'.$postNo.'</td>';
						echo '<td style="font-family:굴림; text-align:left; padding-left:5px; width:500px;">'.$row['juso1'].' '.$row['juso2'].'</td>';
					}
					
					if($memTeam == 'Y'){
						echo '<td style="font-family:굴림; text-align:left; padding-left:5px;">'.$row['teamName'].'</td>';
					}

					if($contractType == 'Y'){
						echo '<td style="font-family:굴림; text-align:left; padding-left:5px;">'.$row['contType'].'</td>';
					}
					
					if($billPhone == 'Y'){
						echo '<td style="font-family:굴림; text-align:left; padding-left:5px;">'.$myF->phoneStyle($row['bill_phone']).'</td>';
					}


					if($memo == 'Y'){
						echo '<td style="font-family:굴림; text-align:left; padding-left:5px; width:600px;">'.$row['memo'].'</td>';
					}

				echo '</tr>';


				}
			}else{
			?>	<tr>
					<td style="font-family:굴림; text-align:center;" colspan="<?=$col?>">::검색된 데이타가 없습니다.::</td>
				</tr><?
			}

		$conn->row_free();
	?>
	</tbody>
</table>
</form>
<?
	include_once('../inc/_db_close.php');
?>