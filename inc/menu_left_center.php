<?
	include_once('../inc/_root_return.php');
?>
<div id="left_box">
	<h2>기관관리</h2>
	<ul id="s_gnb"><?
		if ($_SESSION['userLevel'] == 'HAN'){?>
			<li>
				<a style="cursor:default;">기관관리</a>
				<ul id="sub_menu">
					<li><a href="#" onClick="location.href='../care/care.php?type=61&menu=center'; return false;">기관관리</a></li>
				</ul>
			</li><?
		}else{
			if($myF->host() != 'care'){ ?>
				<li><a style="cursor:default;">기관관리</a>
					<ul id="sub_menu">
						<?
							if ($_SESSION["userLevel"] == "A"){
								if ($gDomain != 'vaerp.com'){
									//echo '<li><a href=\'#\' onclick=\'location.href="../center/center_reg.php?menu=center"; return false;\'>기관등록</a></li>';
								}
								echo '<li><a href=\'#\' onclick=\'location.href="../center/list.php?menu=center";  return false;\'>기관조회</a></li>';
							}else if ($_SESSION["userLevel"] == "B"){
								//echo '<li><a href=\'#\' onclick=\'location.href="../center/center_reg.php?menu=center"; return false;\'>기관등록</a></li>';
								echo '<li><a href=\'#\' onclick=\'location.href="../_center/center.php?menu=GoodEOSCenter&menuId=CENTER_REG"; return false;\'>기관등록</a></li>';
								echo '<li><a href=\'#\' onclick=\'location.href="../center/list.php?menu=center"; return false;\'>기관조회</a></li>';
								echo '<li><a href=\'#\' onclick=\'location.href="../_center/center.php?menu=GoodEOSCenter&menuId=DOC";\'>계약서/등록증 관리</a></li>';
							}else{
								echo '<li><a href=\'#\' onclick=\'location.href="../center/center_reg.php?menu=center"; return false;\'>기관조회</a></li>';
							}

							if ($_SESSION["userLevel"] != "A" && $_SESSION["userLevel"] != "B"){
								echo '<li><a href=\'#\' onClick=\'location.href="../dept/dept_list.php?menu=center"; return false;\'>부서관리</a></li>
									  <li><a href=\'#\' onClick=\'location.href="../job/job_list.php?menu=center"; return false;\'>직무관리</a></li>';?>

								<li><a href="#" onClick="location.href='../pos/pos.php'; return false;">직위관리</a></li><?

								if ($gHostSvc['homecare']){
									echo '<li><a href=\'#\' onClick=\'location.href="../other/suga.php?gubun=list&menu=center"; return false;\'>장기요양 비급여</a></li>
										  <li><a href=\'#\' onClick=\'location.href="../other/sudang.php?gubun=list&menu=center"; return false;\'>장기요양 목욕/간호수당</a></li>';
								}

								if ($gHostSvc['voucher']){
									echo '<li><a href=\'#\' onClick=\'location.href="../other/suga_voucher.php?menu=center"; return false;\'>바우처 비급여</a></li>
										  <li><a href=\'#\' onClick=\'location.href="../other/voucher_extra_pay.php?menu=center"; return false;\'>바우처 목욕/간호수당</a></li>';
								}

								echo '<li><a href=\'#\' onClick=\'location.href="../center/holiday_list.php?menu=center"; return false;\'>기관약정휴일</a></li>';

								if ($_SESSION['userLevel'] == 'C'){
									echo '<li><a href=\'#\' onClick=\'changePassword(); return false;\'>비밀번호관리</a></li>';
								}

								if ($debug){?>
									<li><a href="#" onclick="location.href='../permission/permission.php?menu=center'; return false;">권환관리</a></li><?
								}
							}else if ($_SESSION["userLevel"] == "AAA"){?>
								<li><a href="#" onClick="location.href='../other/suga_batch.php?menu=center'; return false;">장기요양수가관리</a></li>
								<li><a href="#" onClick="location.href='../other/maxpay_batch.php?menu=center'; return false;">한도금액일괄수정</a></li>
								<!--li><a href="#" onclick="location.href='../branch/branch2center.php';">기관연결</a></li--><?
								if ($debug){?>
									<li><a href="#" onClick="location.href='../center/popup.php'; return false;">팝업등록</a></li>
									<li><a href="#" onClick="location.href='../center/popup_list.php'; return false;">팝업조회</a></li><?
								}
							}
						?>
					</ul>
				</li><?
			}

			if ($_SESSION['userLevel'] == 'AAA' && $gDomain != 'vaerp.com'){?>
				<li class="top_line"><a style="cursor:default;">기관메뉴관리</a>
					<ul id="sub_menu">
						<li><a href="#" onClick="location.href='../acct/menu.php?type=10&menu=center'; return false;">메뉴관리</a></li>
					</ul>
				</li>
				<li class="top_line"><a style="cursor:default;">기관데이타관리</a>
					<ul id="sub_menu">
						<li><a href="#" onClick="location.href='../data/data.php?type=101&menu=center'; return false;">기관데이타복사</a></li>
					</ul>
				</li><?
			}

			if ($_SESSION["userLevel"] != "A" && $_SESSION["userLevel"] != "B"){?>
				<li class="top_line"><a style="cursor:default;">직원관리</a>
					<ul id="sub_menu">
						<li><a href="#" onClick="location.href='../counsel/mem_counsel.php?menu=center'; return false;">초기상담기록지</a></li>
						<li><a href="#" onClick="location.href='../yoyangsa/counsel_member.php?menu=center'; return false;">과정상담리스트</a></li>
						<li><a href="#" onClick="location.href='../yoyangsa/mem_reg.php?menu=center'; return false;">직원등록</a></li>
						<li><a href="#" onClick="location.href='../yoyangsa/mem_list.php?menu=center'; return false;">직원조회</a></li>
						<!-- 2012.01.31 수정 li><a href="#" onClick="location.href='../yoyangsa/status.php?gubun=search';">직원현황</a></li-->
						<!--li><a href="#" onClick="location.href='../yoyangsa/manage.php';">직원평가관리</a></li-->
						<li><a href="#" onClick="location.href='../yoyangsa/mem_app.php?menu=center'; return false;">직원평가관리</a></li>
						<!--li><a href="#" onClick="location.href='../yoyangsa/excel.php';">직원정보 일괄등록</a></li-->
						<!--li><a href="#" onClick="location.href='../yoyangsa/mem_modify.php';">직원정보 일괄수정</a></li-->

						<!--li><a href="#" onClick="location.href='../yoyangsa/mem_basic.php';">기초자료등록</a></li-->
						<!--li><a href="#" onClick="location.href='../yoyangsa/longcare.php?mode=101';">건보등록비교</a></li-->
						<li><a href="#" onClick="location.href='../yoyangsa/mem_4insu.php?menu=center';return false;">4대보험가입내역(현재)</a></li><?
						if ($debug){?>
							<li><a href="#" onClick="location.href='../yoyangsa/mem_4insu_his.php?menu=center'; return false;">4대보험 취득/상실내역</a></li><?
						}?>
						<li><a href="#" onClick="location.href='../ltc/ltc.php?mode=20&menu=center'; return false;">요양보호사조회(공단)</a></li><?

						$lbInsuMenuShow = false;

						$sql = 'SELECT insu_cd
								  FROM insu_center
								 WHERE org_no = \''.$_SESSION['userCenterCode'].'\'
								   AND svc_cd = \'0\'
								 ORDER BY seq DESC';

						$lsInsuCd = $conn->get_data($sql);

						if ($debug ||
							$lsInsuCd == '0' ||
							($gDomain == 'kdolbom.net' && $lsInsuCd == '2') ||
							$lsInsuCd == '8'){
							$lbInsuMenuShow = true;
						}

						$lbInsuMenuShow = false;

						if ($lbInsuMenuShow){?>
							<!--li><a href="#" onClick="location.href='../yoyangsa/mem_insu.php?mode=1';">배상책임보험 가입신청</a></li-->
							<li><a href="#" onClick="location.href='../yoyangsa/mem_insu.php?mode=3&menu=center'; return false;">배상책임보험 가입진행중</a></li>
							<li><a href="#" onClick="location.href='../yoyangsa/mem_insu.php?mode=5&menu=center'; return false;">배상책임보험 가입완료</a></li><?
						}?>
					</ul>
				</li>
				<li class="top_line"><a style="cursor:default;">고객관리</a>
					<ul id="sub_menu">
						<li><a href="#" onClick="location.href='../counsel/client_counsel.php?menu=center'; return false;">초기상담(욕구사정)기록</a></li>
						<li><a href="#" onClick="location.href='../sugupja/counsel_client.php?menu=center'; return false;">과정상담리스트</a></li>
						<?
							if ($lbTestMode){?>
								<li><a href="#" onClick="location.href='../sugupja/client_new.php?menu=center'; return false;">고객등록</a></li><?
							}else{?>
								<li><a href="#" onClick="location.href='../sugupja/client_reg.php?menu=center'; return false;">고객등록</a></li><?
							}
						?>
						<li><a href="#" onClick="location.href='../sugupja/client_list.php?menu=center'; return false;">고객조회</a></li>
						<!-- 2012.01.31 수정 li><a href="#" onClick="location.href='../sugupja/condition.php?gubun=search';">고객현황</a></li-->
						<!--li><a href="#" onClick="location.href='../sugupja/manage.php';">고객평가관리</a></li-->
						<li><a href="#" onClick="location.href='../sugupja/client_app.php?menu=center'; return false;">고객평가관리</a></li>
						<!--li><a href="#" onClick="location.href='../sugupja/excel.php';">고객정보 일괄등록</a></li-->
						<li><a href="#" onClick="location.href='../ltc/ltc.php?mode=10&menu=center'; return false;">수급자조회(공단)</a></li>
						<li><a href="#" onClick="location.href='../sugupja/client_state.php?menu=center'; return false;">수급자현황(재가요양)</a></li>
					</ul>
				</li><?
			}
		}
		?>
	</ul>
</div>