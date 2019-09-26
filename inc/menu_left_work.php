<?
	include_once('../inc/_root_return.php');

	$today = date('Ymd');
?>
<div id="left_box">
	<h2>실적관리</h2>
	<ul id="s_gnb">
	<?
		if ($_SESSION['userLevel'] == 'P' && $_SESSION['userSmart'] == 'Y'){?>
			<li class=""><a style="cursor:default;">실적내역</a>
				<ul id="sub_menu">
					<li><a href="#" onClick="location.href='../iljung/member_iljung.php';">수급내역(요양보호사)</a></li>
					<li><a href="#" onClick="location.href='../work/work_status.php';">근무현황표</a></li>
				</ul>
			</li><?
		}else{?>
			<li class=""><a style="cursor:default;">실적내역</a>
				<ul id="sub_menu">
					<li><a href="#" onClick="location.href='../iljung/iljung_result.php?gubun=client';">수급내역(수급자)</a></li>
					<li><a href="#" onClick="location.href='../iljung/iljung_result.php?gubun=member';">수급내역(요양보호사)</a></li>
					<li><a href="#" onClick="window.open('../work/work_stat.php?mode=1', 'WORK_STATUS', 'width=950, height=600, toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=yes');">근무현황(요양보호사)</a></li>
					<li><a href="#" onClick="window.open('../work/work_stat.php?mode=2', 'WORK_STATUS', 'width=950, height=600, toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=yes');">근무현황(노동부제출용)</a></li>
					<li><a href="#" onClick="location.href='../work/table.php';">근무현황(계획or실적)</a></li>
					<li><a href="#" onClick="location.href='../work/work_status.php';">근무현황표</a></li>
					<li><a href="#" onclick="location.href='../work/result_mem.php';">요양보호사 실적내역</a></li>
				</ul>
			</li><?
		}

		if ($_SESSION['userLevel'] == 'P' && $_SESSION['userSmart'] == 'Y'){
		}else{?>
			<li class="top_line"><a style="cursor:default;">실적등록</a>
				<ul id="sub_menu"><?
					if (!$isDemo){?>
						<li><a href="#" onclick="location.href='../longcare/longcare.php';">RFID실적등록</a></li>
						<li><a href="#" onclick="location.href='../longcare/longcare_log.php';">RFID LOG</a></li><?

						if ($gHostSvc['voucher']){?>
							<li><a href="#" onclick="location.href='../voucher/voucher_excel.php';">바우처실적등록(EXCEL)</a></li><?
						}
					}?>
					<li><a href="#" onclick="location.href='../work/result_day.php';">일 실적 등록(수급자)</a></li>
					<li><a href="#" onclick="location.href='../work/result_month.php?mode=2';">월 실적 등록(수급자)</a></li>
					<!--li><a href="#" onclick="location.href='../work/result_month.php?mode=3';">월 실적 등록(요양보호사)</a></li-->
					<li><a href="#" onclick="location.href='../work/result_month_all.php?mode=1';">월 실적 일괄등록</a></li>
					<li><a href="#" onclick="location.href='../work/result_month_all.php?mode=2';">월 실적 일괄취소</a></li>
				</ul>
			</li>
			<li class="top_line"><a style="cursor:default;">본인부담금 계산</a>
				<ul id="sub_menu">
					<li><a href="#" onclick="location.href='../sugupja/client_expense.php';">본인부담금계산</a></li>
				</ul>
			</li>
			<li class="top_line"><a style="cursor:default;">계획/실적차이</a>
				<ul id="sub_menu">
					<li><a href="#" onClick="location.href='../work/result_a_plan.php';">계획, 실적비교</a></li>
					<li><a href="#" onClick="location.href='../work/result_plan_list.php';">계획>실적 List</a></li>
					<li><a href="#" onClick="location.href='../work/result_plan_conf.php';">계획/실적 차이 리스트</a></li>
					<?
						if ($debug){?>
							<li><a href="#" onclick="location.href='../work/result_plan_conf_csv.php';">계획,실적일괄등록(TEXT)</a></li><?
						}
					?>
				</ul>
			</li>
			<li class="top_line"><a style="cursor:default;">건보공단바로가기</a>
				<ul id="sub_menu">
					<li><a href="#" onclick="_checkRFIDReady('http://dmd.longtermcare.or.kr/autodmd/nypk/nypk_autoDmdList.do?method=nypkRfidmodify');">급여서비스 내역</a></li>
					<li><a href="#" onclick="_checkRFIDReady('http://dmd.longtermcare.or.kr/autodmd/nypk/nypk_PayDmdList.do?method=selectPayDmdList');">급여비용 청구관리</a></li>
					<li><a href="#" onclick="_checkRFIDReady('http://dmd.longtermcare.or.kr/autodmd/nypk/nypk_reader.do?method=openReaderRegistMain');">리더기 등록</a></li>
					<li><a href="#" onclick="_checkRFIDReady('http://dmd.longtermcare.or.kr/autodmd/nypk/nypk_reader.do?method=selectInVisitList');">리더기 관리</a></li>
				</ul>
			</li>
			<?
		}

		if ($_SESSION['userLevel'] == 'P' && $_SESSION['userSmart'] == 'Y'){
		}else{
			if ($today >= '20120816'){
			}else{?>
				<li class="top_line"><a style="cursor:default;">마감관리</a>
					<ul id="sub_menu">
						<li><a href="#" onclick="location.href='../work/result_status.php';">마감진행상태</a></li>
						<li><a href="#" onclick="location.href='../work/result_finish_confirm.php?mode=1';">실적마감</a></li>
						<li><a href="#" onclick="location.href='../work/result_finish_confirm_cancel.php?mode=1';">실적마감취소</a></li>
					</ul>
				</li><?
			}
		}

		if ($debug){?>
			<li class="top_line"><a style="cursor:default;">건보실적등록(TEXT)</a>
				<ul id="sub_menu">
					<li><a href="#" onclick="location.href='../nhic/nhic_index.php';">건보 실적 등록</a></li>
					<li><a href="#" onclick="location.href='../nhic/nhic_log.php';">건보 실적 LOG</a></li>
				</ul>
			</li><?
		}
	?>
	</ul>
</div>