<?
	include_once('../inc/_root_return.php');

	if ($gDomain == 'carevisit.net' || $debug){?>
		<div id="left_box">
			<h2>기타</h2>
			<ul id="s_gnb">
				<li><a style="cursor:default;">기관관리</a>
					<ul id="sub_menu"><?
						if ($_SESSION['userLevel'] == 'A' ||
							$_SESSION['userLevel'] == 'B'){?>
							<li><a href="#" onclick="location.href='../acct/acct.php?type=31&menu=GoodEOSAcct';">기관관리업무</a></li>
							<li><a href="#" onclick="location.href='../acct/acct.php?type=SEMINAR_REQUEST&menu=GoodEOSAcct';">세미나신청내역</a></li><?
						}

						if ($_SESSION['userLevel'] == 'A'){?>
							<li><a href="#" onclick="location.href='../acct/acct.php?type=CENTER_USE_STATE&menu=GoodEOSAcct';">기관이용현황</a></li>
							<li><a href="#" onclick="location.href='../acct/acct.php?type=32&menu=GoodEOSAcct';">기관요금관리</a></li>
							<li><a href="#" onclick="location.href='../acct/acct.php?type=33&menu=GoodEOSAcct';">기관교육비관리</a></li><?
						}?>
					</ul>
				</li>
			</ul><?

			if ($_SESSION['userLevel'] == 'A'){?>
				<ul id="s_gnb">
					<li class="top_line"><a style="cursor:default;">추가서비스관리</a>
						<ul id="sub_menu">
							<li><a href="#" onclick="location.href='../acct/acct.php?type=DAN_LIST&menu=GoodEOSAcct';">주야간보호 기관관리</a></li>
							<li><a href="#" onclick="location.href='../acct/acct.php?type=WMD_LIST&menu=GoodEOSAcct';">복지용구 기관관리</a></li>
						</ul>
					</li>
				</ul>
				<ul id="s_gnb">
					<li class="top_line"><a style="cursor:default;">SMS</a>
						<ul id="sub_menu">
							<li><a href="#" onclick="location.href='../acct/acct.php?type=1&menu=GoodEOSAcct';">SMS 기관관리</a></li>
							<li><a href="#" onclick="location.href='../acct/acct.php?type=2&menu=GoodEOSAcct';">SMS 요금관리</a></li>
							<!--li><a href="#" onclick="location.href='../acct/acct.php?type=3';">SMS 입금관리</a></li-->
						</ul>
					</li>
				</ul>
				<ul id="s_gnb">
					<li class="top_line"><a style="cursor:default;">스마트폰</a>
						<ul id="sub_menu">
							<li><a href="#" onclick="location.href='../acct/acct.php?type=11&menu=GoodEOSAcct';">스마트폰 기관관리</a></li>
							<li><a href="#" onclick="location.href='../acct/acct.php?type=12&menu=GoodEOSAcct';">스마트폰 요금관리</a></li>
							<!--li><a href="#" onclick="location.href='../acct/acct.php?type=13';">스마트폰 입금관리</a></li-->
						</ul>
					</li>
				</ul>
				<ul id="s_gnb">
					<li class="top_line"><a style="cursor:default;">모바일</a>
						<ul id="sub_menu">
							<li><a href="#" onclick="location.href='../acct/acct.php?type=51&menu=GoodEOSAcct';">모바일 기관관리</a></li>
						</ul>
					</li>
				</ul>
				<ul id="s_gnb">
					<li class="top_line"><a style="cursor:default;">입금관리</a>
						<ul id="sub_menu">
							<li><a href="#" onclick="location.href='../acct/acct.php?type=41&menu=GoodEOSAcct';">입금관리</a></li>
							<li><a href="#" onclick="location.href='../acct/acct.php?type=42&menu=GoodEOSAcct';">입금등록(엑셀)</a></li><?
							if ($gDomain == 'carevisit.net'){?>
								<li><a href="#" onclick="location.href='../acct/acct.php?type=UNREG_CMS_VISIT_LOG&menu=GoodEOSAcct';">미등록 기관기록</a></li><?
							}?>
						</ul>
					</li>
				</ul>
				<ul id="s_gnb">
					<li class="top_line"><a style="cursor:default;">회계/노무</a>
						<ul id="sub_menu">
							<li><a href="#" onclick="location.href='../acct/acct.php?type=64&menu=GoodEOSAcct';">재무회계 커뮤니티</a></li>
							<li><a href="#" onclick="location.href='../acct/acct.php?type=62&menu=GoodEOSAcct';">인사노무 기관관리</a></li>
							<li><a href="#" onclick="location.href='../acct/acct.php?type=63&menu=GoodEOSAcct';">회계세무 기관관리(한림)</a></li>
							<li><a href="#" onclick="location.href='../acct/acct.php?type=61&menu=GoodEOSAcct';">회계세무 기관관리(대영)</a></li>
						</ul>
					</li>
				</ul>
				<ul id="s_gnb">
					<li class="top_line"><a style="cursor:default;">은행업무</a>
						<ul id="sub_menu">
							<li><a href="#" onclick="location.href='../acct/acct.php?type=21&menu=GoodEOSAcct';">은행업무 기관관리</a></li>
						</ul>
					</li>
				</ul>
				<ul id="s_gnb">
					<li class="top_line"><a style="cursor:default;">재가지원</a>
						<ul id="sub_menu">
							<li><a href="#" onclick="location.href='../acct/acct.php?type=71&menu=GoodEOSAcct';">수가관리</a></li>
						</ul>
					</li>
				</ul><?
				if ($gDomain == 'kacold.net' ||
					$gDomain == 'dwcare.com' ){
				}else { ?>
					<ul id="s_gnb">
						<li class="top_line"><a style="cursor:default;">기타업무</a>
							<ul id="sub_menu">
								<li><a href="#" onclick="location.href='../acct/acct.php?type=SEMINAR_REQUEST&menu=GoodEOSAcct';">세미나신청내역</a></li>
								<li><a href="#" onclick="location.href='../acct/acct.php?type=TABLET_REQUEST&menu=GoodEOSAcct';">테블릿신청내역</a></li>
								<li><a href="#" onclick="location.href='../acct/acct.php?type=REPORT2014_REQUEST&menu=GoodEOSAcct';">평가자료신청내역</a></li>
								<li><a href="#" onclick="location.href='../acct/acct.php?type=REPORT2014_COPY&menu=GoodEOSAcct';">기관평가자료(복사)</a></li>
								<li><a href="#" onclick="location.href='../acct/acct.php?type=KACOLD&menu=GoodEOSAcct';">경제협 계약서 등록현황</a></li>
							</ul>
						</li>
					</ul><?
				}
			}else if ($_SESSION['userLevel'] == 'B'){?>
				<ul id="s_gnb">
					<li class="top_line"><a style="cursor:default;">추가서비스관리</a>
						<ul id="sub_menu">
							<li><a href="#" onclick="location.href='../acct/acct.php?type=DAN_LIST&menu=GoodEOSAcct';">주야간보호 기관관리</a></li>
							<li><a href="#" onclick="location.href='../acct/acct.php?type=WMD_LIST&menu=GoodEOSAcct';">복지용구 기관관리</a></li>
						</ul>
					</li>
				</ul><?
			}?>
		</div><?
	}else{?>
		<div id="left_box">
			<h2>기타</h2>
			<ul id="s_gnb">
				<li><a style="cursor:default;">기관관리</a>
					<ul id="sub_menu"><?
						if ($_SESSION['userLevel'] == 'A' ||
							$_SESSION['userLevel'] == 'B'){?>
							<li><a href="#" onclick="location.href='../acct/acct.php?type=31&menu=GoodEOSAcct';">기관관리업무</a></li><?
						}?>
					</ul>
				</li>
			</ul>
		</div><?
	}