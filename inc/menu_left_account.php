<?
	include_once('../inc/_root_return.php');?>
	<div id="left_box">
		<h2>수입/지출관리</h2>
		<ul id="s_gnb">
			<li><a style="cursor:default;">수입내역</a>
				<ul id="sub_menu">
					<li><a href="#" onclick="location.href='../account/acct.php?type=1&menu=account';">입금내역등록</a></li>
					<li><a href="#" onclick="location.href='../account/acct.php?type=2&menu=account';">입금내역조회</a></li>
					<li><a href="#" onclick="location.href='../account/acct.php?type=3&menu=account';">입금내역집계</a></li>
				</ul>
			</li>

			<li class="top_line"><a style="cursor:default;">지출내역</a>
				<ul id="sub_menu">
					<li><a href="#" onclick="location.href='../account/acct.php?type=11&menu=account';">지출내역등록</a></li>
					<li><a href="#" onclick="location.href='../account/acct.php?type=12&menu=account';">지출내역조회</a></li>
					<li><a href="#" onclick="location.href='../account/acct.php?type=13&menu=account';">지출내역집계</a></li>
				</ul>
			</li>

			<li class="top_line"><a style="cursor:default;">수입/지출내역</a>
				<ul id="sub_menu">
					<li><a href="#" onclick="location.href='../account/income_io.php?menu=account';">수입/지출조회</a></li>
				</ul>
			</li><?

			if ($isBankTrans){?>
				<li class="top_line"><a style="cursor:default;">은행이체</a>
					<ul id="sub_menu">
						<li><a href="#" onclick="location.href='../salaryNew/salary_trans.php?type=request&menu=account';">급여이체</a></li>
						<li><a href="#" onclick="location.href='../trans/trans.php?type=other&menu=account';">기타이체</a></li>
						<li><a href="#" onclick="location.href='../trans/trans.php?type=acctno&menu=account';">이체계좌관리</a></li>
						<li><a href="#" onclick="location.href='../trans/trans.php?type=result&menu=account';">이체결과조회</a></li>
					</ul>
				</li><?
			}?>
		</ul>
	</div>