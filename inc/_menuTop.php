<?
	include_once('../inc/_login.php');
	include_once('../inc/_ed.php');

	//배상책임보험 연결여부
	$insFlag = 'N';

	if ($_SERVER['REMOTE_ADDR'] == '115.90.90.146'){
		$showMensFlag[0] = true;
	}else{
		$showMensFlag[0] = false;
	}

	$temp_uri = explode('/', $_SERVER["REQUEST_URI"]);

	if ($temp_uri[sizeOf($temp_uri)-1] == 'main.php'){
		$topId = 'top_box';

		if ($gDomain == 'kacold.net'){
			$topImg = $gHostImgPath.'/top/bg_key.jpg';
		}else{
			$topImg = '../img/top/bg_key.jpg';
		}
	}else{
		$topId = 'top_sub_box';

		if ($gDomain == 'kacold.net'){
			$topImg = $gHostImgPath.'/top/bg_sub_key.jpg';
		}else{
			$topImg = '../img/top/bg_sub_key.jpg';
		}
	}

	$host = $myF->host();


	if ($debug){?>
		<span id="isCareAdmin" style="display:none;">Y</span><?
	}
?>
<!-- Head -->
<div id="<?=$topId;?>" style="background:#1391f0 url('<?=$topImg;?>') no-repeat left top;">
	<div class="top_ci"><?
		if ($gDomain == 'kacold.net'){
			$imgpath = $gHostImgPath.'/top/ci_'.$_SESSION['userArea'].'.png';
			if (!is_file($imgpath)) $imgpath = $gHostImgPath.'/top/ci.png';?>
			<a href="#" onclick="__go_menu('');"><img src="<?=$imgpath;?>"/></a><?
		}else{
			if ($gDomain == 'dolvoin.net' && $_SESSION['userCode'] == 'carevisit'){
				$ci_img = '../admin_img/sso/ci.png';
			}else{
				$ci_img = $gHostImgPath.'/top/ci.png';
			}?>
			<a href="#" onclick="__go_menu('');"><img src="<?=$ci_img;?>"/></a><?
		}?>
	</div><?
	if ($_SESSION['USER_IS_ADMIN'] == true){?>
		<div class="top_ci" style="color:RED; font-weight:bold; font-size:17px; margin-left:-30px; margin-top:60px;">계약이 종료된 기관입니다.</div><?
	}?>
	<div class="top_gubun" style="width:auto;">
		<div class="top_logout"><a href="#" onClick="location.href='../main/logout_ok.php';"></a></div>
		<ul>
			<li class="icon_gubun"></li><?
			if ($gDomain == 'kacold.net'){?>
				<li>재가기관:<?=$_SESSION["userGubun"];?></li><?
			}else{?>
				<li>요양기관:<?=$_SESSION["userGubun"];?></li><?
			}?>
			<li class="g_margin"></li>
			<li>성명:<?=$_SESSION["userName"];?></li>
			<li class="g_margin"></li>
			<li>센터명:<?=$_SESSION["top_print_name"];?></li>
			<li class="g_margin"></li>
		</ul>

		<div style='float:right; text-align:right; margin-top:5px;'><?

			if (/*$gDomain == 'kacold.net' ||*/
				$gDomain == 'forweak.net' ||
				$gHostNm == 'dan'){
			}else {
				if ($_SESSION['userLevel'] != 'A'){

					if($gDomain == 'carevisit.net' && !$isDemo){ ?>
						<div style="position:absolute; top:50px; left:27px; width:146px; height:31px;"><img src='../shop/shop_img/btn_qna.png' alt='케어비지트 질의응답' style='cursor:pointer;' onclick="link_qna();"></div>
						<!--div style="position:absolute; top:50px; left:200px; width:146px; height:31px;"><img src='../shop/shop_img/btn_set.png' alt='한가위선물세트' style='cursor:pointer;' onclick="_set_pop();"></div--><?
					}

					if ($gDomain != 'dolvoin.net' && !$isDemo){?>
						<div style="position:absolute; <?=$gDomain == 'kacold.net' ? 'top:55px; left:75px;' : 'top:50px; left:200px;';?> width:146px; height:31px;"><img src='../popup/fee_msg/top_btn_fee.png' style='cursor:pointer;' alt="청구요금안내" onclick="window.open('../popup/fee_msg/t.php?type=CLAIM','CLAIM','width=700,height=850,Top=0,left=100,scrollbars=yes,resizable=no,location=no,toolbar=no,menubar=no');"></div><?
					}

					if (!$isDemo){
						/*********************************
						#
						#	2014평가자료 팝업
						#
						**********************************/
						/*
						$sql = 'select count(*)
								  from report2014_request
								 where org_no = \''.$_SESSION['userCenterCode'].'\'
								   and use_yn = \'Y\'';
						$r_cnt = $conn -> get_data($sql);
						*/
						//평가자료 무료 오픈 20151215?>
						<img src='../popup/report2014/img/btn_2014.png' alt='표준평가자료' style='cursor:pointer;' onclick="location.href='../report2014/index.php?report_menu=10&menuTopId=M';">
						<!--
						<img src='../popup/report2014/img/btn_2014.png' alt='표준평가자료' style='cursor:pointer;' onclick="_Report_pop();">
						--><?
					}?>


					<!--img src='../image/btn_staff.png' alt='직원 조회' style='cursor:pointer;' onclick="_find_pop('M');"--><?
				}
			}
			if($_SESSION['userCenterCode'] == '34812000178'){ ?>
				<img src='../image/btn_appra.png' alt='2012평가대비' style='cursor:pointer;' onclick="__go_menu('eval_data','../eval_data/eval_data.php');"><?
			}

			if (!$isDemo){?>
				<img src='../image/btn_remote.png' alt='원격지원' style='cursor:pointer;' onclick="window.open('http://939.co.kr/goodeos/','HELP_WIN','left=0,top=0,width=830,height=580,toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no');">
				<img src='../image/btn_geonbo_go.gif' alt='건보공단 바로가기' style='cursor:pointer;' onclick='window.open("http://www.longtermcare.or.kr/npbs/", "", "top=0, left=0, width=500, height=500, toolbar=yes,location=yes,status=yes,menubar=yes,scrollbars=yes,resizable=yes");'><?
				if ($IsLongtermCng2016){
					if($gDomain != 'kacold.net'){?>
						<img src='../image/btn_nhic_login.gif' alt='진도관리' style='cursor:pointer;' onclick="window.open('../work/footing_mg.php','FOOTING_MG','width=720, height=530, toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=yes')"><?
					} ?>

					<!--<img src='../image/btn_nhic_login.gif' alt='건보공단 로그인' style='cursor:pointer;' onclick='window.open("http://www.longtermcare.or.kr/npbs/auth/login/loginForm.web?menuId=npe0000002160&rtnUrl=", "", "top=0, left=0, width=500, height=500, toolbar=yes,location=yes,status=yes,menubar=yes,scrollbars=yes,resizable=yes");'>--><?

				}else{?>
					<!--<img src='../image/btn_nhic_login.gif' alt='건보공단 로그인' style='cursor:pointer;' onclick='__longcareLogin("<?=$_SESSION['userCenterGiho'];?>");'>--><?
				}
			}?>
		</div>
	</div>
</div>

<!-- Top Menu -->
<div id="top_gnb">
	<ul><?
		if ($_SESSION['userLevel'] == 'A'){
			if ($gDomain == 'carevisit.net'){?>
				<li class=""><a href="#" onClick="__go_menu('GoodEOSCenter','../_center/center.php?menu=GoodEOSCenter&menuId=CENTER_LIST');">기관관리</a></li><?
				if ($host == 'admin' && $_SESSION["userCode"] != 'geecare'){?>
					<li class=""><a href="#" onClick="__go_menu('GoodEOSCenter','../_center/center.php?menu=GoodEOSClaim&menuId=PAY_IN_LIST');">요금 및 청구관리</a></li><?
				}
			}else{?>
				<li><a href="#" onClick="__go_menu('center', '../center/list.php?menu=center');">기관관리</a></li><?
			}?>
			<li><a href="#" onClick="__go_menu('company');">본사관리</a></li>
			<li><a href="#" onClick="__go_menu('branch');">지사관리</a></li>
			<!--<li><a href="#" onClick="__go_menu('store');">가맹점관리</a></li>-->
			<li><a href="#" onClick="__go_menu('goodeos');">커뮤니티</a></li>
			<!--li class="gnb_end"><a href="#" onClick="location.href='../mall/customer.php';"	>물류</a></li--><?
			if ($debug || $gDomain != 'vaerp.com'){?>
				<!--li><a href="#" onClick="__go_menu('GoodEOSAcct');">기타관리</a></li--><?
			}

			if ($gDomain == 'dolvoin.net'){
				$tmpcd = $_SESSION['userCode'];?>
				<script type="text/javascript">
					function lfShowDolvoinStat(){
						var parm = new Array();
							parm = {
								'id':'<?=$ed->en("dolvoin");?>'
							,	'pw':'<?=$ed->en("dolvoin00");?>'
							,	'loc':'<?=$ed->en("ADMIN");?>'
							,	'mem':'<?=$ed->en($tmpcd);?>'
							};

						var form = document.createElement('form');
						var objs;
						for(var key in parm){
							objs = document.createElement('input');
							objs.setAttribute('type', 'hidden');
							objs.setAttribute('name', key);
							objs.setAttribute('value', parm[key]);

							form.appendChild(objs);
						}

						form.setAttribute('target', '_blank');
						form.setAttribute('method', 'post');
						form.setAttribute('action', 'http://mg.<?=$gDomain;?>/main/login_ok.php');

						document.body.appendChild(form);

						form.submit();
					}
				</script>
				<li><a href="#" onClick="__go_menu('BranchManagement','../account/acct.php?menu=BranchManagement&type=BM_ADMIN');">지점관리</a></li>
				<li class="gnb_end"><a href="#" onClick="lfShowDolvoinStat();">통계</a></li><?
			}
		}else if ($_SESSION['userLevel'] == 'B'){
			if ($_SESSION["userCode"] != 'chi001'){?>
				<li><a href="#" onClick="__go_menu('center', '../center/list.php?menu=center');">기관관리</a></li>
				<li><a href="#" onClick="__go_menu('branch','../branch/branch_reg.php?mode=branch&menu=branch');">지사관리</a></li>
				<!--<li><a href="#" onClick="__go_menu('store');">가맹점관리</a></li>-->
				<li class="gnb_end"><a href="#" onClick="__go_menu('goodeos');">커뮤니티</a></li>
				<!--li class="gnb_end"><a href="#" onClick="__go_menu('GoodEOSAcct');">기타관리</a></li--><?
			}else{?>
				<li><a href="#" onClick="__go_menu('goodeos');">커뮤니티</a></li><?
			}
		}else if ($_SESSION['userLevel'] == 'HAN'){?>
			<li><a href="#" onClick="__go_menu('center','../care/care.php?type=61&menu=center');">기관관리</a></li>
			<li><a href="#" onClick="__go_menu('care_sr','../acct/acct.php?type=71&sr=S&menu=care_sr');">재가지원</a></li>
			<li><a href="#" onClick="__go_menu('care_sr','../acct/acct.php?type=71&sr=R&menu=care_sr');">자원연계</a></li>
			<li><a href="#" onClick="__go_menu('report','../care/care.php?sr=S&type=53&menu=report');">보고서</a></li><?
		}else{

			//재무회계신청한기관조회
			$sql = 'SELECT  count(*)
					FROM    seminar_request
					WHERE   org_no = \''.$_SESSION['userCenterCode'].'\'
					AND     gbn    = \'3\'';
			$fa_cnt = $conn -> get_data($sql);

			$sql = 'SELECT	COUNT(*)
					FROM	cv_reg_info
					WHERE	org_no = \''.$_SESSION['userCenterCode'].'\'
					AND		rs_cd IN (\'1\', \'2\', \'3\')
					AND		from_dt <= DATE_FORMAT(NOW(), \'%Y%m%d\')
					AND		to_dt >= DATE_FORMAT(NOW(), \'%Y%m%d\')';

			$regInfoCnt = $conn->get_data($sql);

			if ($_SESSION['userLevel'] == 'C'){
				$sql = 'SELECT	id,name,url,link_gbn,permit
						FROM	menu_top
						WHERE	show_yn = \'Y\' AND use_yn = \'Y\' '.(!$debug ? ' AND debug = \'N\' ' : '').($isDemo ? ' AND demo_yn = \'Y\' ' : '').'
						ORDER	BY seq,id';
			}else{
				$sql = 'SELECT	DISTINCT menu.id,menu.name,menu.url,menu.link_gbn,menu.permit
						FROM	menu_top AS menu
						INNER	JOIN	menu_permit AS permit
								ON		permit.org_no	= \''.$_SESSION['userCenterCode'].'\'
								AND		permit.jumin	= \''.$_SESSION['userSSN'].'\'
								AND		permit.use_yn	= \'Y\'
								AND		LEFT(permit.menu_id,1) = menu.id
						WHERE	menu.show_yn = \'Y\' AND menu.use_yn = \'Y\' '.(!$debug ? ' AND menu.debug = \'N\'' : '').($isDemo ? ' AND menu.demo_yn = \'Y\' ' : '').'
						ORDER	BY menu.seq,menu.id';
			}

			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count();

			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);

				if ($row['id'] == 'R'){
					if ($_SESSION['userCenterCode'] == '1234' ||
						$_SESSION['userCenterCode'] == '31117000066'){
					}else{
						continue;
					}
				}

				if ($_SESSION['userLevel'] == 'C'){
					$tmp = $row;
				}else{
					$sql = 'SELECT	menu.id,menu.name,menu.url,menu.link_gbn,menu.permit
							FROM	menu_list AS menu
							INNER	JOIN	menu_permit AS permit
									ON		permit.org_no	= \''.$_SESSION['userCenterCode'].'\'
									AND		permit.jumin	= \''.$_SESSION['userSSN'].'\'
									AND		permit.menu_id	= CONCAT(menu.m_top,menu.m_left,menu.id)
									AND		permit.use_yn	= \'Y\'
							WHERE	menu.m_top = \''.$row['id'].'\'
							ORDER	BY menu.m_left,menu.seq,menu.id
							LIMIT	1';
					$tmp = $conn->get_array($sql);
				}

				//해지한 기관은 진행관리 메뉴를 볼 수 없도록 막는다.
				if ($tmp['id'] == 'C' && $regInfoCnt < 1) continue;

				if (($tmp['permit'] == 'S' && $gHostSvc['careSupport']) ||
					($tmp['permit'] == 'R' && $gHostSvc['careResource']) ||
					($tmp['permit'] == 'C' && $gHostSvc['homecare']) ||
					($tmp['permit'] == 'V' && $gHostSvc['voucher']) ||
					($tmp['permit'] == 'D' && $gDayAndNight) ||
					($tmp['permit'] == 'W' && $gWMD) ||
					($tmp['permit'] == 'A')){
					if ($tmp['link_gbn'] == '1'){
						$url = $tmp['url'];

						if (!Is_Numeric(StrPos($url,'?'))){
							$url .= '?';
						}else{
							$url .= '&';
						}

						$url .= 'menuTopId='.$row['id'];

						$link = 'location.href=\''.$url.'\';';
					}else{
						$link = $tmp['url'];
					}?>
					<li><div class="nowrap" style="width:auto;"><a href="#" onclick="<?=$link;?>return false;"><?=$row['name'];?></a></div></li><?

				}
			}

			$conn->row_free();
		}?>
	</ul>
</div><?