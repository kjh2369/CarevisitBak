<?
	include_once('../inc/_root_return.php');
?>
<div id="left_box">
	<h2>리포트</h2>
	<ul id="s_gnb"><?
		if ($_SESSION['userLevel'] == 'HAN'){?>
			<li>
				<a style="cursor:default;">리포트</a>
				<ul id="sub_menu">
					<li><a href="#" onClick="location.href='../care/care.php?sr=S&type=53&menu=report'; return false;">중분류(재가지원)</a></li>
					<li><a href="#" onClick="location.href='../care/care.php?sr=R&type=53&menu=report'; return false;">중분류(자원연계)</a></li>
					<li><a href="#" onClick="location.href='../care/care.php?sr=S&type=52&menu=report'; return false;">세부사업(재가지원)</a></li>
					<li><a href="#" onClick="location.href='../care/care.php?sr=R&type=52&menu=report'; return false;">세부사업(자원연계)</a></li>
					<li><a href="#" onClick="location.href='../care/care.php?sr=S&type=55&menu=report'; return false;">지역별기관(재가지원)</a></li>
					<li><a href="#" onClick="location.href='../care/care.php?sr=R&type=55&menu=report'; return false;">지역별기관(자원연계)</a></li>
					<!--li><a href="#" onClick="location.href='../care/care.php?type=54'; return false;">기관별(서비스별)</a></li-->
				</ul>
			</li><?
		}else{?>
			<li><a style="cursor:default;">리포트</a>
				<ul id="sub_menu"><?
					if ($_SESSION['userLevel'] == 'P'){?>
						<li><a href="#" onclick="location.href='../reportMenu/report.php?report_menu=30&menu=report';">고객관리</a></li><?
					}else{?>
						<li><a href="#" onclick="location.href='../reportMenu/report.php?report_menu=10&menu=report';">기관관리</a></li>
						<li><a href="#" onclick="location.href='../reportMenu/report.php?report_menu=20&menu=report';">직원관리</a></li>
						<li><a href="#" onclick="location.href='../reportMenu/report.php?report_menu=30&menu=report';">고객관리</a></li><?
					}?>

				</ul>
			</li><?
			if ($debug){?>
				<li class="top_line"><a style="cursor:default;">사회복지서비스</a>
					<ul id="sub_menu">
						<li><a href="#" onclick="location.href='../reportMenu/report.php?report_menu=50&menu=report';">사회복지서비스</a></li>
					</ul>
				</li><?
			}
		}?>
	</ul>
</div>