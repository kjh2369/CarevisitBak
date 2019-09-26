<?
	include_once('../inc/_root_return.php');

	$today = date('Ymd');
?>
<div id="left_box">
	<h2>청구관리</h2>
	<ul id="s_gnb">
		<li><a style="cursor:default;">본인부담금 계산</a>
			<ul id="sub_menu">
				<li><a href="#" onclick="location.href='../sugupja/client_expense.php?menu=claim';">본인부담금계산</a></li>
			</ul>
		</li>
		<li class="top_line"><a style="cursor:default;">실적관리(현시점)</a>
			<ul id="sub_menu">
				<li><a href="#" onclick="location.href='../supply/supply_stat.php?menu=claim';">수급내역현황(전체)</a></li>
				<li><a href="#" onclick="location.href='../supply/supply_stat_month.php?menu=claim';">수급내역현황(월별)</a></li>
				<li><a href="#" onclick="location.href='../supply/supply_stat_day.php?menu=claim';">수급내역현황(일별)</a></li>
			</ul>
		</li>

		<li class="top_line"><a style="cursor:default;">청구관리(실적확정후)</a>
			<ul id="sub_menu">
				<li><a href="#" onclick="location.href='../claim/public_amt_list.php?menu=claim';">공단부담금내역</a></li>
				<li><a href="#" onclick="location.href='../claim/public_amt_detail.php?menu=claim';">공단부담금상세내역</a></li>
				<!--li><a href="#" onclick="location.href='../claim/my_amt_list.php';">본인부담금내역</a></li-->
			</ul>
		</li>

		<!--li class="top_line"><a style="cursor:default;">미수금관리</a>
			<ul id="sub_menu">
				<li><a href="#" onclick="location.href='../account/unpaid_list.php';">미수금입금등록</a></li>
				<li><a href="#" onclick="location.href='../account/deposit_list.php';">입금내역조회</a></li>
			</ul>
		</li-->

		<li class="top_line"><a style="cursor:default;">본인부담금관리</a>
			<ul id="sub_menu">
				<li><a href="#" onclick="location.href='../expenses/expenses.php?mode=center&menu=claim';">연간내역(기관)</a></li>
				<li><a href="#" onclick="location.href='../expenses/expenses.php?mode=client&menu=claim';">월별내역(기관)</a></li>
				<li><a href="#" onclick="location.href='../expenses/expenses.php?mode=client_month&menu=claim';">연간내역(수급자)</a></li>
				<li><a href="#" onclick="location.href='../expenses/expenses.php?mode=summly_month&menu=claim';">수급자별(연간)</a></li>
				<li><a href="#" onclick="location.href='../expenses/expenses.php?mode=summly_month_detail&menu=claim';">수급자별(월별)</a></li>
				<li><a href="#" onclick="location.href='../expenses/expenses.php?mode=client_detail&menu=claim';">수급자별(발급대장)</a></li>
				<li><a href="#" onclick="location.href='../expenses/expenses.php?mode=receive&menu=claim';">본인부담금(수납대장)</a></li>
				<li><a href="#" onclick="location.href='../expenses/expenses.php?mode=report&menu=claim';">영수증 및 명세서</a></li>
				<li><a href="#" onclick="location.href='../expenses/expenses.php?mode=compare&menu=claim';">건보실적비교</a></li>
			</ul>
		</li>

		<li class="top_line"><a style="cursor:default;">본인부담금입금</a>
			<ul id="sub_menu">
				<li><a href="#" onclick="location.href='../expenses/expenses.php?mode=deposit_reg&menu=claim';">입금등록</a></li>
				<li><a href="#" onclick="location.href='../expenses/expenses.php?mode=in&menu=claim';">입금등록(월별)</a></li>
				<li><a href="#" onclick="location.href='../expenses/expenses.php?mode=deposit_modify&menu=claim';">입금수정</a></li>
				<li><a href="#" onclick="location.href='../expenses/expenses.php?mode=deposit_list&menu=claim';">입금내역</a></li>
				<li><a href="#" onclick="location.href='../expenses/expenses.php?mode=in_reciept&menu=claim';">입금영수증</a></li>
			</ul>
		</li>

	</ul>
</div>