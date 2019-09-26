<?
	if (isSet($_SESSION["userCode"])){
		$sql = "select count(*)
				  from m00center
				 inner join g01ins
					on g01_code = m00_ins_code
				   and g01_use  = 'Y'
				 where m00_mcode = '".$_SESSION["userCode"]."'";
		if ($conn->get_data($sql) > 0){
			$insFlag = 'Y';
		}else{
			$insFlag = 'N';
		}
	}

	if ($_SERVER['REMOTE_ADDR'] == '115.90.90.146'){
		$showMensFlag[0] = true;
	}else{
		$showMensFlag[0] = false;
	}

	$temp_uri = explode('/', $_SERVER["REQUEST_URI"]);

	if ($temp_uri[sizeOf($temp_uri)-1] == 'main.php'){
		$top_id = 'top_box';
	}else{
		$top_id = 'top_sub_box';
	}


	$host = $myF->host();

	if ($debug){
		echo '<span id=\'isCareAdmin\' style=\'display:none;\'>Y</span>';
	}


	/*********************************************************

		기관 공통 설정

	 *********************************************************/
	$sql = 'SELECT salary_yn
			  FROM center_comm
			 WHERE org_no = \''.$_SESSION['userCenterCode'].'\'';
	$row = $conn->get_array($sql);
	$gSalaryYn = $row['salary_yn'];
	UnSet($row);

	if($debug) 'aa';
?>

	<!-- Head -->
	<div id="<?=$top_id;?>">
		<div class="top_ci">
			<a href="#" onclick="__go_menu('');"><img src="<?=$gHostImgPath;?>/top/ci.png" /></a>
		</div>
		<div class="top_gubun">
			<div class="top_logout"><a href="#" onClick="location.href='../main/logout_ok.php';"></a></div>
			<ul>
				<li class="icon_gubun"></li>
				<li>요양기관:<?=$_SESSION["userGubun"];?></li>
				<li class="g_margin"></li>
				<li>성명:<?=$_SESSION["userName"];?></li>
				<li class="g_margin"></li>
				<li>센터명:<?=$_SESSION["top_print_name"];?></li>
				<li class="g_margin"></li>
			</ul>
			<div style='float:right; text-align:right; margin-top:5px;'>
				<!--img src='../image/btn_appra.png' alt='2012평가대비' style='cursor:pointer;' onclick="__go_menu('eval_data','../eval_data/eval_data.php');"--><?

				if (Date('Ymd') >= '20130411'){?>
					<img src='../image/btn_remote.png' alt='원격지원' style='cursor:pointer;' onclick="window.open('http://939.co.kr/goodeos/','HELP_WIN','left=0,top=0,width=830,height=580,toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');"><?
				}else{?>
					<img src='../image/btn_remote.png' alt='원격지원' style='cursor:pointer;' onclick="window.open('http://helpu.kr/goodeos/','HELP_WIN','left=0,top=0,width=860,height=320,toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');"><?
				}?>
				<img src='../image/btn_geonbo_go.gif' alt='건보공단 바로가기' style='cursor:pointer;' onclick='window.open("http://www.longtermcare.or.kr/portal/site/nydev/", "", "top=0, left=0, width=500, height=500, toolbar=yes,location=yes,status=yes,menubar=yes,scrollbars=yes,resizable=yes");'>
				<img src='../image/btn_nhic_login.gif' alt='건보공단 로그인' style='cursor:pointer;' onclick='__longcareLogin("<?=$_SESSION['userCenterGiho'];?>");'>
			</div>
		</div>
	</div>

	<!-- Top Menu -->
	<div id="top_gnb">
		<ul>
		<?
			if ($_SESSION['userLevel'] == 'A'){?>
				<li><a href="#" onClick="__go_menu('center', '../center/list.php');">기관관리</a></li>
				<li><a href="#" onClick="__go_menu('company');">본사관리</a></li>
				<li><a href="#" onClick="__go_menu('branch');">지사관리</a></li>
				<!--<li><a href="#" onClick="__go_menu('store');">가맹점관리</a></li>-->
				<li><a href="#" onClick="__go_menu('goodeos');">커뮤니티</a></li>
				<!--li class="gnb_end"><a href="#" onClick="location.href='../mall/customer.php';"	>물류</a></li-->
				<li class="gnb_end"><a href="#" onClick="__go_menu('GoodEOSAcct');">기타관리</a></li><?
			}else if ($_SESSION['userLevel'] == 'B'){
				if ($_SESSION["userCode"] != 'chi001'){?>
					<li><a href="#" onClick="__go_menu('center', '../center/list.php');">기관관리</a></li>
					<li><a href="#" onClick="__go_menu('branch','../branch/branch_reg.php?mode=branch');">지사관리</a></li>
					<!--<li><a href="#" onClick="__go_menu('store');">가맹점관리</a></li>-->
					<li><a href="#" onClick="__go_menu('goodeos');">커뮤니티</a></li>
					<li class="gnb_end"><a href="#" onClick="__go_menu('GoodEOSAcct');">기타관리</a></li><?
				}else{?>
					<li><a href="#" onClick="__go_menu('goodeos');">커뮤니티</a></li><?
				}
			}else if ($_SESSION['userLevel'] == 'P'){
				if ($debug){
					//권환설정
					if ($_SESSION["userStmar"] == 'M'){?>
						<li><a href="#" onClick="__go_menu('center', '../center/center_reg.php');">기관관리</a></li>
						<li><a href="#" onClick="__go_menu('iljung');">일정관리</a></li>
						<li><a href="#" onClick="__go_menu('proc');">진행관리</a></li>
						<li><a href="#" onClick="__go_menu('work');">실적관리</a></li>
						<li><a href="#" onClick="__go_menu('claim');">청구관리</a></li><?
						if ($gSalaryYn == 'Y'){?>
							<li><a href="#" onClick="__go_menu('salary','../salary/salary_person.php');">급여관리</a></li><?
						}?>
						<li><a href="#" onClick="__go_menu('report');">리포트</a></li>
						<li class="gnb_end"><a href="#" onClick="__go_menu('other');">커뮤니티</a></li><?
					}else{?>
						<li><a href="#" onClick="__go_menu('iljung','../iljung/iljung_list.php?mode=4');">일정관리</a></li>
						<li><a href="#" onClick="__go_menu('work','../work/work_status.php');">실적관리</a></li><?
						if ($gSalaryYn == 'Y'){?>
							<li><a href="#" onClick="__go_menu('salary','../salary/salary_person.php');">급여관리</a></li><?
						}?>
						<li><a href="#" onClick="__go_menu('report','../reportMenu/report.php?report_menu=30');">리포트</a></li>
						<li class="gnb_end"><a href="#" onClick="__go_menu('other');">커뮤니티</a></li><?
					}
				}else{
					if ($_SESSION["userStmar"] == 'M'){?>
						<li><a href="#" onClick="__go_menu('center', '../center/center_reg.php');">기관관리</a></li>
						<li><a href="#" onClick="__go_menu('iljung');">일정관리</a></li>
						<li><a href="#" onClick="__go_menu('proc');">진행관리</a></li>
						<li><a href="#" onClick="__go_menu('work');">실적관리</a></li>
						<li><a href="#" onClick="__go_menu('claim');">청구관리</a></li><?
						if ($gSalaryYn == 'Y'){?>
							<li><a href="#" onClick="__go_menu('salary','../salary/salary_person.php');">급여관리</a></li><?
						}?>
						<li><a href="#" onClick="__go_menu('report');">리포트</a></li>
						<li class="gnb_end"><a href="#" onClick="__go_menu('other');">커뮤니티</a></li><?
					}else{?>
						<li><a href="#" onClick="__go_menu('iljung','../iljung/iljung_list.php?mode=4');">일정관리</a></li>
						<li><a href="#" onClick="__go_menu('work','../work/work_status.php');">실적관리</a></li><?
						if ($gSalaryYn == 'Y'){?>
							<li><a href="#" onClick="__go_menu('salary','../salary/salary_person.php');">급여관리</a></li><?
						}?>
						<li><a href="#" onClick="__go_menu('report','../reportMenu/report.php?report_menu=30');">리포트</a></li>
						<li class="gnb_end"><a href="#" onClick="__go_menu('other');">커뮤니티</a></li><?
					}
				}
			}else if ($_SESSION['userLevel'] == 'HAN'){?>
				<li><a href="#" onClick="__go_menu('center','../care/care.php?type=61');">기관관리</a></li>
				<li><a href="#" onClick="__go_menu('care_sr','../acct/acct.php?type=71&sr=S');">재가지원</a></li>
				<li><a href="#" onClick="__go_menu('care_sr','../acct/acct.php?type=71&sr=R');">자원연계</a></li>
				<li><a href="#" onClick="__go_menu('report','../care/care.php?sr=S&type=53');">보고서</a></li><?
			}else{
				if($myF->host() == 'care'){ ?>
					<!-- care.klcf.kr 접속 -->
					<li><a href="#" onClick="__go_menu('center', '../yoyangsa/mem_list.php');">기관관리</a></li><?
				}else { ?>
					<li><a href="#" onClick="__go_menu('center', '../center/center_reg.php');">기관관리</a></li><?
				}

				if ($gHostSvc['careSupport']){?>
					<li><a href="#" onClick="__go_menu('support','../care/care.php?sr=S&type=1');">재가지원</a></li><?
				}

				if ($gHostSvc['careResource']){?>
					<li><a href="#" onClick="__go_menu('resource','../care/care.php?sr=R&type=1');">자원연계</a></li><?
				}?>
				<li><a href="#" onClick="__go_menu('iljung');">일정관리</a></li>
				<li><a href="#" onClick="__go_menu('proc');">진행관리</a></li>
				<li><a href="#" onClick="__go_menu('work');">실적관리</a></li>
				<li><a href="#" onClick="__go_menu('claim');">청구관리</a></li>
				<li><a href="#" onClick="__go_menu('salary');">급여관리</a></li>
				<li><a href="#" onClick="__go_menu('account','../account/acct.php?type=2');">수입/지출</a></li>
				<li><a href="#" onClick="__go_menu('report','../reportMenu/report.php?report_menu=10');">리포트</a></li>
				<li><a href="#" onClick="__go_menu('other');">커뮤니티</a></li>
				<li class="gnb_end" onClick="__go_menu('help');"><a href="#">도움말</a></li><?
			}
		?>
		</ul>
	</div>
<?