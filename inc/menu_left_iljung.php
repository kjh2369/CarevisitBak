<?
	include_once('../inc/_root_return.php');
?>
<div id="left_box">
	<h2>일정관리</h2>
	<ul id="s_gnb">
		<li><a style="cursor:default;">일정관리</a>
			<ul id="sub_menu">
			<?
				if ($_SESSION['userLevel'] == 'P'){
					switch($_SESSION['userSmart']){
						case 'Y':
							echo '<li><a href=\'#\' onClick=\'location.href="../iljung/iljung_list.php?mode=4";\'>일정표조회/출력</a></li>';
							break;

						case 'M':
							if ($gHostSvc['voucher'])
								echo '<li><a href=\'#\' onclick=\'location.href="../iljung/iljung_list.php?mode=3";\'>바우처생성내역등록</a></li>';

							echo '<li><a href=\'#\' onclick=\'location.href="../iljung/iljung_list.php?mode=1";\'>방문일정등록/조회</a></li>
								  <li><a href=\'#\' onClick=\'location.href="../iljung/iljung.php?gubun=day";\'>일별일정조회/출력</a></li>
								  <li><a href=\'#\' onClick=\'location.href="../iljung/iljung_print.php?type=s";\'>일정표출력(수급자)</a></li>
								  <li><a href=\'#\' onClick=\'location.href="../iljung/iljung_print.php?type=y";\'>근무현황표(요양보호사)</a></li>
								  <li><a href=\'#\' onClick=\'location.href="../counsel/client_desire.php";\'>수급자욕구상담</a></li>
								  <li><a href=\'#\' onClick=\'location.href="../iljung/iljung_print.php?type=c";\'>일정표출력(욕구상담)</a></li>
								  <li><a href=\'#\' onClick=\'location.href="../work/gunbo_iljung.php?type=s";\'>건보공단계획 입력서식</a></li>';
							break;
					}
				}else{
					if ($gHostSvc['voucher'])
						echo '<li><a href=\'#\' onclick=\'location.href="../iljung/iljung_list.php?mode=3";\'>바우처생성내역등록</a></li>';

					echo '<li><a href=\'#\' onclick=\'location.href="../iljung/iljung_list.php?mode=1";\'>방문일정등록/조회</a></li>
						  <li><a href=\'#\' onClick=\'location.href="../iljung/iljung_weekly.php";\'>주간별일정조회/출력</a></li>
						  <li><a href=\'#\' onClick=\'location.href="../iljung/iljung.php?gubun=day";\'>일별일정조회/출력</a></li>';

					//echo '<li><a href=\'#\' onClick=\'location.href="../iljung/iljung_print.php?type=s";\'>일정표출력(수급자)</a></li>';
					echo '<li><a href=\'#\' onClick=\'location.href="../iljung/iljung_print_new.php?mode=101";\'>일정표출력(수급자)</a></li>';

					//echo '<li><a href=\'#\' onClick=\'location.href="../iljung/iljung_print.php?type=y";\'>일정표출력(요양보호사)</a></li>';
					echo '<li><a href=\'#\' onClick=\'location.href="../iljung/iljung_print_new.php?mode=102";\'>근무현황표(요양보호사)</a></li>';

					echo '<li><a href=\'#\' onClick=\'location.href="../counsel/client_desire.php";\'>수급자욕구상담</a></li>
						  <li><a href=\'#\' onClick=\'location.href="../iljung/iljung_print.php?type=c";\'>급여제공계획표(욕구)</a></li>';

					echo '<li><a href=\'#\' onClick=\'location.href="../work/gunbo_iljung.php?type=s";\'>건보공단계획 입력서식</a></li>
						  <li><a href=\'#\' onclick=\'location.href="../iljung/iljung_use_bill.php";\'>서비스제공계획서</a></li>';
				}
			?>
			</ul>
		</li><?

		if ($_SESSION['userLevel'] == 'C'){?>
			<li class="top_line"><a style="cursor:default;">공단메뉴</a>
				<ul id="sub_menu">
					<li><a href="#" onclick="location.href='../ltc/ltc.php?mode=30';">일정조회(공단)</a></li>
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
			}

			if ($debug){?>
				<li class="top_line"><a style="cursor:default;">테스트 메뉴</a>
					<ul id="sub_menu">
						<li><a href="#" onclick="location.href='../iljung/iljung_excel.php';">일정계획 업로드(엑셀)</a></li>
						<li><a href="#" onclick="location.href='../iljung/iljung_list.php?mode=5';">일정표출력(실적)</a></li>
					</ul>
				</li><?
			}
		?>
	</ul>
</div>