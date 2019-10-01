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

	$lsStatsId = $ed->en($_SESSION['userCode']);
	$lsStatsPw = $ed->en($_SESSION['userPass']);
?>
<div id="wrap">
<!-- Head -->
<div id="header">
	<!--lnb -->
	<div id="lnb">
		<div id="lnb_box">
			<!--로그인 기관정보-->
			<div style="float:left; width:242px; height:50px; border-left:1px solid #667c91; border-right:1px solid #667c91;">
				<table class="t_table" border="1" cellspacing="0" >
				<colgroup>
					<col width="120px">
					<col width="120px">
				</colgroup>
				<tbody class="txt_center">
					<tr>
						<th colspan="2">기관명</th>
					</tr>
					<tr>
						<td>성명</td>
						<td>직위</td>
					</tr>
				</tbody>
				</table>
			</div>
			<!--//로그인 기관정보-->
			<!--기관현황-->
			<div style="float:left; width:530px; height:50px; border-left:1px solid #667c91; border-right:1px solid #667c91; margin-left:3px;">
				<table class="t_table" border="1" cellspacing="0" style="width:100%">
				<colgroup>
					<col width="28px">
					<col width="*">
				</colgroup>
				<tbody class="txt_center">
					<tr>
						<th rowspan="2" style="padding:0; font-size:14px; line-height:1.3em; background-color:#014687; border-bottom:1px solid #014687;">현<br />황</th>
						<th scope="col">총원</th>
						<th colspan="3"  scope="col">중점(J)</th>
						<th colspan="3"  scope="col">일반(I)</th>
						<th scope="col">독거(D)</th>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>A</td>
						<td>B</td>
						<td>C</td>
						<td>A</td>
						<td>B</td>
						<td>C</td>
						<td>&nbsp;</td>
					</tr>
				</tbody>
				</table>
			</div>
			<!--//기관현황-->
			<div style="float:right">
				<!--바로가기-->
				<div style="float:left; width:272px; height:50px; border-left:1px solid #667c91; border-right:1px solid #667c91; margin-left:3px;">
					<table class="t_table" border="1" cellspacing="0" >
					<colgroup>
						<col width="90px">
						<col width="90px">
						<col width="90px">
					</colgroup>
					<tbody class="txt_center">
						<tr>
							<th scope="col">응급모니터</th>
							<th scope="col">게시판(00)</th>
							<th scope="col">자료실</th>
						</tr>
						<tr>
							<td style="background-color:#014687;"><a href="#" style="font-size:11px;">바로가기</a></td>
							<td style="background-color:#014687;"><a href="#" style="font-size:11px;">바로가기</a></td>
							<td style="background-color:#014687;"><a href="#" style="font-size:11px;">바로가기</a></td>
						</tr>
					</tbody>
					</table>
				</div>
				<!--//바로가기-->
				<!--링크-->
				<div style="float:left; width:142px; height:50px; border-left:1px solid #667c91; border-right:1px solid #667c91; margin-left:3px;">
					<table class="t_table" border="1" cellspacing="0" style=" height:50px; ">
					<colgroup>
						<col width="70px">
						<col width="70px">
					</colgroup>
					<tbody class="txt_center">
						<tr>
							<th style="background-color:#430187; border-bottom:1px solid #430187; padding:0;"><a href="#" style="width:68px; height:48px;  line-height:1.3em;">원격<br />지원</a></th>
							<th style="background-color:#018759; border-bottom:1px solid #018759; padding:0;"><a href="#" style="width:68px; height:48px;  line-height:1.3em;">사회보장<br />정보원</a></th>
						</tr>
					</tbody>
					</table>
				</div>
				<!--//링크-->
				<!--일자/시간-->
				<div style="float:left; width:152px; height:50px; border-left:1px solid #667c91; border-right:1px solid #667c91; margin-left:3px;">
					<table class="t_table" border="1" cellspacing="0" style=" height:50px; ">
					<colgroup>
						<col width="150px">
					</colgroup>
					<tbody class="txt_center">
						<tr>
							<td style="font-size:13px;">2019.09.30</td>
						</tr>
						<tr>
							<td style="font-size:13px;">13:59:00</td>
						</tr>
					</tbody>
					</table>
				</div>
				<!--//일자/시간-->
				<!--로그아웃-->
				<div style="float:left; width:72px; height:50px; border-left:1px solid #667c91; border-right:1px solid #667c91; margin-left:3px;">
					<a href="#" onClick="location.href='../main/logout_ok.php';" style="display:block; width:70px; height:50px; line-height:50px;  text-align:center; color:#fff; padding:0; background:#de272c; font-weight:600; font-size:13px; letter-spacing:-1px;">로그아웃</a>
				</div>
				<!--//로그아웃-->
			</div>




			<!--div class="top_gubun" style="width:auto;">
				<div class="top_logout"><a href="#" onClick="location.href='../main/logout_ok.php';">로그아웃</a></div>
				<ul>
					<li class="icon_gubun"></li>
					<li>재가기관:<?=$_SESSION["userGubun"];?></li>
					<li class="g_margin"></li>
					<li>성명:<?=$_SESSION["userName"];?></li>
					<li class="g_margin"></li>
					<li>센터명:<?=$_SESSION["top_print_name"];?></li>
					<li class="g_margin"></li>
				</ul>
			</div-->



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
					<li class=""><a href="#" onClick="location.href='../_center/center.php?menu=GoodEOSCenter&menuId=CENTER_LIST'; return false;">기관관리</a></li>
					<li class=""><a href="#" onClick="location.href='../acct/acct.php?type=71&sr=S&menu=care_sr'; return false;">기초관리</a></li><?
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
				}?>
			</ul>
		</div>
		<!-- //Top Menu -->
	</div>
</div><?