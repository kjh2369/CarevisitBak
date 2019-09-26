<?
	include_once('../inc/_root_return.php');
	include_once('../inc/_ed.php');
?>
<div id="left_box">
	<h2>급여관리</h2>
	<ul id="s_gnb"><?
	if ($_SESSION['userLevel'] == 'P'){?>
		<li class="top_line"><a style="cursor:default;">급여관리</a>
			<ul id="sub_menu">
				<li><a href="#" onclick="location.href='../salaryNew/salary_edit_2.php?code=<?=$_SESSION['userCenterCode'];?>&year=<?=date('Y', mktime());?>&month=<?=date('m', mktime());?>&jumin=<?=$ed->en($_SESSION['userSSN']);?>';">급여내역</a></li>
				<li><a href="#" onclick="location.href='../salaryNew/salary_preview.php';">예상급여(실적)</a></li>
			</ul>
		</li><?
	}else{
		if (!$isDemo){
			if ($debug ||
				$_SESSION['userCenterCode'] == '31130500063'){?>
				<li class="top_line"><a style="cursor:default;">보수비교</a>
					<ul id="sub_menu">
						<li><a href="#" onclick="location.href='../salaryNew/salary_deal.php?type=501';">보수비교대상자조회</a></li>
						<!--li><a href="#" onclick="location.href='../salaryNew/salary_deal.php?type=401';">보수비교표생성</a></li-->
						<li><a href="#" onclick="location.href='../salaryNew/salary_deal.php?type=201';">종사자보수비교표(월급)</a></li>
						<li><a href="#" onclick="location.href='../salaryNew/salary_deal.php?type=202';">종사자보수비교표(시급)</a></li>
						<li><a href="#" onclick="location.href='../salaryNew/salary_deal.php?type=203';">신규보수표(월급)</a></li>
						<li><a href="#" onclick="location.href='../salaryNew/salary_deal.php?type=204';">신규보수표(시급)</a></li>
					</ul>
				</li><?
			}?>
			<li class="top_line"><a style="cursor:default;">처우개선비</a>
				<ul id="sub_menu">
					<li><a href="#" onclick="location.href='../salaryNew/salary_deal.php?type=101';">선지급 처우개선비 계산</a></li>
					<li><a href="#" onclick="location.href='../salaryNew/salary_deal.php?type=102';">처우개선비내역</a></li>
				</ul>
			</li><?
		}?>
		<li class="top_line"><a style="cursor:default;">급여기초자료</a>
			<ul id="sub_menu"><?
				if ($debug || $_SESSION['userCenterCode'] == '31135000074'){?>
					<!--li><a href="#" onclick="location.href='../salaryNew/salary_deal.php?type=101';">처우개선비계산</a></li--><?
				}?>
				<li><a href="#" onclick="location.href='../salaryNew/salary_subject.php';">수당 및 공제등록(전체)</a></li>
				<li><a href="#" onclick="location.href='../salaryNew/salary_basic_data.php';">급여기초자료등록(개별)</a></li>
			</ul>
		</li>

		<li class="top_line"><a style="cursor:default;">급여계산 및 조정</a>
			<ul id="sub_menu">
				<li><a href="#" onclick="location.href='../salaryNew/salary_finish_confirm.php?mode=2';">급여일괄계산</a></li>
				<li><a href="#" onclick="location.href='../salaryNew/salary_edit_list.php';">급여조정 및 명세</a></li>
				<li><a href="#" onclick="location.href='../salaryNew/salary_finish_confirm_cancel.php?mode=2';">급여일괄계산취소</a></li>
				<li><a href="#" onclick="location.href='../salaryNew/salary_edit_list.php?recome=Y';">급여재계산</a></li>
				<li><a href="#" onclick="location.href='../salaryNew/salary_preview.php';">예상급여(실적)</a></li>
				<li><a href="#" onclick="location.href='../work/result_finish.php?mode=2';">급여 마감/취소</a></li>
				<li><a href="#" onclick="location.href='../account/unpaid_auto.php';">본인부담금공제</a></li><?
				if ($debug){?>
					<li><a href="#" onclick="location.href='../account/expense.php';">본인부담금공제(TEST)</a></li><?
				}?>
			</ul>
		</li><?

		if ($isBankTrans){?>
			<li class="top_line"><a style="cursor:default;">급여이체</a>
				<ul id="sub_menu">
					<li><a href="#" onclick="location.href='../salaryNew/salary_trans.php?type=request';">급여이체요청</a></li>
				</ul>
			</li><?
		}
	}

	if ($_SESSION['userLevel'] == 'P'){
	}else{?>
		<li class="top_line"><a style="cursor:default;">급여내역</a>
			<ul id="sub_menu">
				<li><a href="#" onclick="location.href='../salaryNew/salary_list.php?mode=year_c';">급여내역(기관 년간)</a></li>
				<li><a href="#" onclick="location.href='../salaryNew/salary_list.php?mode=mem_d';">월 급여내역</a></li>
				<li><a href="#" onclick="location.href='../salaryNew/salary_list.php?mode=year_m';">월 급여내역(개별 년간)</a></li>
				<!--li><a href="#" onclick="location.href='../salaryNew/salary_svc.php';">급여내역(서비스별)</a></li-->
				<li><a href="#" onclick="location.href='../salaryNew/salary_report_svc.php';">서비스별 급여내역</a></li>
			</ul>
		</li>

		<li class="top_line"><a style="cursor:default;">급여리포트</a>
			<ul id="sub_menu">
				<li><a href="#" onclick="location.href='../salaryNew/salary_table.php';">급여대장 출력</a></li>
				<li><a href="#" onclick="location.href='../claim/center_amt_list.php';">4대보험료내역</a></li>
				<li><a href="#" onclick="location.href='../salaryNew/salary_account_list.php';">급여계좌 이체대장</a></li><?
				if ($debug){?>
					<li><a href="#" onclick="location.href='../salaryNew/salary_annual.php';">연차휴가 지급대장</a></li><?
				}?>
				<li><a href="#" onclick="location.href='../salaryNew/retire_account_list.php';">퇴직적립금명세</a></li>
			</ul>
		</li><?
	}

	if ($_SESSION['userLevel'] == 'P'){
	}else{?>
		<li class="top_line"><a style="cursor:default;">마감관리</a>
			<ul id="sub_menu">
				<li><a href="#" onclick="location.href='../work/result_status.php';">마감진행상태</a></li>
			</ul>
		</li><?
	}?>
	</ul>
</div>