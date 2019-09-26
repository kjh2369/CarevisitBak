<?
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
		$topImg = $gHostImgPath.'/top/bg_key.jpg';
	}else{
		$topId = 'top_sub_box';
		$topImg = $gHostImgPath.'/top/bg_sub_key.jpg';
	}

	$host = $myF->host();

	if ($debug){?>
		<span id="isCareAdmin" style="display:none;">Y</span><?
	}

	$lsStatsId = $ed->en($_SESSION['userCode']);
	$lsStatsPw = $ed->en($_SESSION['userPass']);
?>
<div id="wrap">
<!-- Head -->
<div id="header">
	<!--lnb -->
	<div id="lnb">
		<div id="lnb_box">
			<div class="top_gubun" style="width:auto;">
				<div class="top_logout"><a href="#" onClick="location.href='../main/logout_ok.php';"></a></div>
				<ul>
					<li class="icon_gubun"></li>
					<li>재가기관:<?=$_SESSION["userGubun"];?></li>
					<li class="g_margin"></li>
					<li>성명:<?=$_SESSION["userName"];?></li>
					<li class="g_margin"></li>
					<li>센터명:<?=$_SESSION["top_print_name"];?></li>
					<li class="g_margin"></li>
				</ul>
			</div>
		</div>
	</div>
	<!--//lnb -->
	<div id="<?=$topId;?>">
		<div class="top_ci" style="width:110px; height:25px;"><?
			$imgpath = $gHostImgPath.'/top/ci_'.$_SESSION['userArea'].'.png';
			if (!is_file($imgpath)) $imgpath = $gHostImgPath.'/top/ci.png';?>
			<a href="#" onclick="__go_menu('');"><img src="<?=$imgpath;?>"/></a>
		</div>

<script type="text/javascript">
	function lfCaseShow(sr){
		//사례관리
		var w = 1024;
		var h = (screen.availHeight - 150 > 768 ? screen.availHeight - 150 : 768);
		var l = (screen.availWidth - w) / 2;
		var t = (screen.availHeight - h) / 2;

		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
		var url = '../hce/hce.php?sr='+sr;
		var win = window.open('', 'WINCASE', option);
			win.opener = self;
			win.focus();

		var form = document.createElement('form');
			form.setAttribute('target', 'WINCASE');
			form.setAttribute('method', 'post');
			form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfStats(){
		var parm = new Array();
			parm = {
				'id':'<?=$lsStatsId;?>'
			,	'pw':'<?=$lsStatsPw;?>'
			,	'gbn':'STATS'
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
		form.setAttribute('action', 'http://ccss.kacold.net/main/login_ok.php');

		document.body.appendChild(form);

		form.submit();
	}
</script>

		<!-- Top Menu -->
		<div id="top_gnb">
			<ul><?
				if ($_SESSION['adminFlag'] == 'Y'){?>
					<li class=""><a href="#" onClick="__go_menu('GoodEOSCenter','../_center/center.php?menu=GoodEOSCenter&menuId=CENTER_LIST');">기관관리</a></li><?
				}else{
					$typeSR = $_SESSION['userTypeSR'];

					if ($_SESSION['userLevel'] == 'C'){
						$sql = 'SELECT	id,name,url,link_gbn,permit
								FROM	menu_top
								WHERE	show_yn = \'Y\' AND use_yn = \'Y\' '.(!$debug ? ' AND debug = \'N\' ' : '').'
								ORDER	BY seq,id';
					}else{
						$sql = 'SELECT	DISTINCT menu.id,menu.name,menu.url,menu.link_gbn,menu.permit
								FROM	menu_top AS menu
								INNER	JOIN	menu_permit AS permit
										ON		permit.org_no	= \''.$_SESSION['userCenterCode'].'\'
										AND		permit.jumin	= \''.$_SESSION['userSSN'].'\'
										AND		permit.use_yn	= \'Y\'
										AND		LEFT(permit.menu_id,1) = menu.id
								WHERE	menu.show_yn = \'Y\' AND menu.use_yn = \'Y\' '.(!$debug ? ' AND menu.debug = \'N\'' : '').'
								ORDER	BY menu.seq,menu.id';
					}

					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);



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

					$conn->row_free();


					?>
					<!--
					<li><a href="#" onClick="__go_menu('kacold_client', '../care/care.php?sr=<?=$typeSR;?>&type=81&menu=kacold_client');">대상자관리</a></li>
					<li><a href="#" onClick="__go_menu('kacold_iljung', '../iljung/iljung_list.php?sr=<?=$typeSR;?>&mode=6&menu=kacold_iljung');">일정관리</a></li>
					<li><a href="#" onClick="__go_menu('kacold_result', '../care/care.php?sr=<?=$typeSR;?>&type=RESULT_REG&menu=kacold_result');">실적관리</a></li>
					<li><a href="#" onClick="__go_menu('kacold_report', '../care/care.php?sr=<?=$typeSR;?>&type=53&menu=kacold_report');">보고서</a></li>
					<li><a href="#" onClick="lfCaseShow('<?=$typeSR;?>');">사례관리</a></li>
					<li><a href="#" onClick="lfStats();">통계</a></li>
					<li><a href="#" onClick="__go_menu('kacold_center', '../center/center_reg.php?menu=kacold_center');">기관관리</a></li>
					<li><a href="#" onClick="__go_menu('kacold_base', '../care/care.php?sr=<?=$typeSR;?>&type=1&menu=kacold_base');">기초자료</a></li>
					-->
					<!--li><a href="#" onClick="__go_menu('kacold_pay', '../claim/claim_list.php?sr=<?=$typeSR;?>&menu=kacold_pay');">사용요금안내</a></li--><?
				}?>
			</ul>
		</div>
		<!-- //Top Menu -->
	</div>
</div><?