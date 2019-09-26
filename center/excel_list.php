<?
	include_once("../inc/_db_open.php");

	header( "Content-type: charset=utf-8" );
	header("Content-type: application/vnd.ms-excel;"); 
	header( "Content-Disposition: attachment; filename=test.xls" );
	header( "Content-Transfer-Encoding: binary" );
	header( "Content-Description: PHP4 Generated Data" );
	header( "Pragma: no-cache" );
	header( "Expires: 0" );
?>	
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<?
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_function.php");
	include_once("../inc/_definition.php");

	/*
	 * 기능		: 기관조회//엑셀출력
	 * 작성자	: 김주완
	 * 일자		: 2012.03.30
	 */

	$comDomain = $myF->_domain();
	$companyCD = $conn->_company_code($comDomain);

	if ($_SESSION["userLevel"] == "A"){
		$mCode = $_REQUEST["mCode"];
	}else{
		$mCode = $_SESSION["userCenterCode"];
	}

	$item_count = 20;
	$page_count = 10;
	$page = $_REQUEST["page"];

	if (!is_numeric($page)) $page = 1;

	$find_center_code	= $_REQUEST['find_center_code'];
	$find_center_name	= $_REQUEST['find_center_name'];
	$find_member_cnt	= $_REQUEST['find_member_cnt'];
	$find_client_cnt	= $_REQUEST['find_client_cnt'];
	$find_iljung_cnt	= $_REQUEST['find_iljung_cnt'];
	$find_cont_date     = $_REQUEST['find_cont_date'];
	$find_from_yymm     = str_replace('-', '', $_REQUEST['find_from_yymm']);	//연결시작년월
	$find_to_yymm       = str_replace('-', '', $_REQUEST['find_to_yymm']);		//연결종료년월
	$find_branch		= $_REQUEST['find_branch'];								//지사명
	$find_person		= explode('_',$_REQUEST['find_person']);		        //person[0]:지사명 person[1]:담당자명
	$find_center_addr   = $_REQUEST['find_center_addr'];						//주소
	
	$today = date('Ym', mktime());


?>
<style>
	.title{
		font-size:20pt;
		text-align:left;
		font-weight:bold;
		text-align:center;
	}
	
	.head{
		background-color:#efefef;
		border:0.5pt solid #000000;
		font-family:굴림;
	}

	.center{
		text-align:center;
		font-family:굴림;
	}
	.left{
		text-align:left;
		font-family:굴림;
	}
	.right{
		text-align:right;
		background-color:#ffffff;
		font-family:굴림;
		font-size:12pt;
	}
</style>
<?
$r_dt = date('Y.m.d',mktime());
?>
<div class="title">기관조회</div>
<div class="right">일자 : <?=$r_dt?></div>
<table border="1">
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">기관명</th>
			<th class="head">기관기호</th>
			<th class="head">대표자</th>
			<th class="head">담당자</th>
			<th class="head">CMS</th>
			<th class="head">은행명</th>
			<th class="head">계좌번호</th>
			<th class="head">예금주</th>
			<th class="head">연락처</th>
			<th class="head">주소</th>
			<?
				if ($_SESSION['userLevel'] == 'A'){?>
					<th class="head">사용일자</th>
					<th class="head">계약일자</th>
					<th class="head">직원</th>
					<th class="head">수급자</th>
					<th class="head">일정</th><?
				}
			?>
		</tr>
	</thead>
	<tbody>
	<?
		$wsl = '';
		if ($_SESSION["userLevel"] == "A" || $_SESSION["userLevel"] == "B"){
			if ($find_center_code != '')  $wsl .= " and m00_mcode like '$find_center_code%'";		//기관코드 검색
			if ($find_center_name != '')  $wsl .= " and m00_store_nm like '%$find_center_name%'";	//기관명 검색
			if ($find_center_addr != '')  $wsl .= " and m00_caddr1 like '%$find_center_addr%'";		//주소 검색


			if ($find_cont_date   == 'Y' and $find_cont_no_date   == 'Y'){
				//계약,미계약 둘다 체크했을 시
			}else {

				//그 외일 경우
				if ($find_cont_date   == 'Y') $wsl .= " and ifnull(m00_cont_date, '') != ''";		//계약 기관 검색
				if ($find_cont_no_date   == 'Y') $wsl .= " and ifnull(m00_cont_date, '') = ''";		//미계약 기관 검색
			}

			if ($find_from_yymm != '') $wsl .= " and left(m00_start_date, 6) between '$find_from_yymm' and '".($find_to_yymm != '' ? $find_to_yymm : '999912')."'";		//연결년월 검색
			if ($find_branch != '') $wsl .= " and b02_branch = '$find_branch'";																							//지사명 검색
			if ($find_branch != '' and $find_person != '')  $wsl .= " and b02_person = '$find_person[1]'";																	//지사명있을 시 담당자 검색

			if ($find_member_cnt == 'Y'){			//직원있는기관만 검색
				$wsl .= ' and  (select count(m02_yjumin)
								  from m02yoyangsa
							     where m02_ccode  = m00_mcode
								   and m02_del_yn = \'N\') > 0';
			}

			if ($find_client_cnt == 'Y'){		   //수급자있는기관만 검색
				$wsl .= ' and  (select count(m03_jumin)
								  from m03sugupja
							     where m03_ccode  = m00_mcode
								   and m03_del_yn = \'N\') > 0';
			}

			if ($find_iljung_cnt == 'Y'){		  //일정있는거만 검색
				$wsl .= ' and  (select count(distinct t01_jumin)
								  from t01iljung
							     where t01_ccode  = m00_mcode
								   and t01_del_yn = \'N\'
								   and t01_sugup_date like \''.$today.'%\') > 0';
			}

		/*************************/
		//기관조회 : 사용일자안보여서 추가였습니다.
		//	$wsl .= ' and m00_mkind = (select min(m00_mkind) from m00center where m00_del_yn = \'N\')';
		/************************/

			$wsl .= ' and m00_mkind = (select min(m00_mkind) from m00center where m00_del_yn = \'N\')';

		}else{
			$wsl .= ' and m00_mcode = \''.$_SESSION["userCenterCode"].'\'';
		}

		$sql = 'select code, kind, nm, cd, m_nm, tel, post, addr1, addr2, use_dt, cont_dt, bank_no, bank_name, bank_depos, id, pw, manager';

		if ($_SESSION['userLevel'] == 'A'){
			$sql .= ', (select count(distinct concat(m02_ccode, \'_\', m02_yjumin))
						  from m02yoyangsa
						 where m02_ccode        = t.code
						   and m02_ygoyong_stat = \'1\'
						   and m02_del_yn       = \'N\') as member_cnt
					 , (select count(distinct concat(m03_ccode, \'_\', m03_jumin))
						  from m03sugupja
						 where m03_ccode        = t.code
						   and m03_sugup_status = \'1\'
						   and m03_del_yn       = \'N\') as client_cnt';


			if ($find_iljung_cnt == 'Y'){
				$sql .= ', (select count(distinct t01_jumin)
							  from t01iljung
							 where t01_ccode  = t.code
							   and t01_del_yn = \'N\'
							   and t01_sugup_date like \''.$today.'%\') as iljung_cnt';
			}else{
				$sql .= ', 0 as iljung_cnt';
			}
		}

		$sql .= ' from (
					   select m00_mcode as code
					   ,      min(m00_mkind) as kind
					   ,      m00_store_nm as nm
					   ,      m00_mcode as cd
					   ,      m00_mname as m_nm
					   ,      m00_ctel as tel
					   ,	  m00_cpostno as post
					   ,      m00_caddr1 as addr1
					   ,      m00_caddr2 as addr2
					   ,      m00_start_date as use_dt
					   ,      m00_cont_date as cont_dt
					   ,	  m00_bank_no as bank_no
					   ,	  m00_bank_name as bank_name
					   ,	  m00_bank_depos as bank_depos
					   ,      m97_id as id
					   ,      m97_pass as pw
					   ,	  b01_name as manager
						 from m00center
						inner join b00branch
						   on b00_domain = \''.$comDomain.'\'';

		if ($_SESSION['userLevel'] == 'B'){
			$sql .= ' and b00_code = \''.$mark_val.'\'';
		}

		$sql .= '		inner join b02center
						   on b02_center = m00_mcode
						  and b02_branch = b00_code
						inner join b01person
						   on b01_branch = b02_branch
						  and b01_code   = b02_person
						inner join m97user
						   on m97_user = m00_mcode
						where m00_mcode is not null '.$wsl.'
						group by m00_mcode
					   ) as t
				 order by nm';

		
		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		if ($row_count > 0){
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);
				
			
				if ($gDomain == _KLCF_){
					$url = 'care.'.$gDomain;
				}else{
					$url = 'www.'.$gDomain;
				}
				?>
				<tr>
					<td class="center"><?=$pageCount + ($i + 1);?></td>
					<td class="left"><?=$row['nm'];?></td>
					<td class="left"><?=$row['code'];?></td>
					<td class="left"><?=$row['m_nm'];?></td>
					<td class="left"><?=$row['manager'];?></td>
					<td class="left"></td>
					<td class="left" style="mso-number-format:\@;"><?=$definition->GetBankName($row['bank_name']);?></td>
					<td class="left" style="mso-number-format:\@;"><?=$row['bank_no'];?></td>
					<td class="left"><?=$row['bank_depos'];?></td>
					<td class="left"><?=$myF->phoneStyle($row['tel']);?></td>
					<td class="left" style="width:500px;"><?=' ('.getPostNoStyle($row['post']).') '.$row['addr1'].' '.$row['addr2']?></td>
					<?
						if ($_SESSION['userLevel'] == 'A'){?>
							<td class="center"><?=$myF->dateStyle($row['use_dt'],'.');?></td>
							<td class="center"><?=$myF->dateStyle($row['cont_dt'],'.');?></td>
							<td class="right"><?=$row['member_cnt'];?></td>
							<td class="right"><?=$row['client_cnt'];?></td>
							<td class="right"><?=$row['iljung_cnt'];?></td>
							<?
						}else{?>
							<td class="other">&nbsp;</td><?
						}
					?>
				</tr>
			<?
			}
		}else{
		?>	<tr>
				<td class="center last" colspan="7">::검색된 데이타가 없습니다.::</td>
			</tr><?
		}

		$conn->row_free();
	?>
	</tbody>
</table>