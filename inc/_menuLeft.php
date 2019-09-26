<?
	include_once('../inc/_ed.php');

	if ($_GET['menuTopId']){
		$_SESSION['MENU_TOP'] = $_GET['menuTopId'];
	}

	$menuId = $_SESSION['MENU_TOP'];

	if ($topId != 'top_box'){
		if ($menuId == 'K' || $menuId == 'L' || $menuId == 'Q'){?>
			<script type="text/javascript">

				function lfCaseShow(sr){

					//사례관리
					var w = 1024;
					var h = (screen.availHeight > 768 ? screen.availHeight : 768);
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

				function lfSvcContShow(SR){
					var w = 1024;
					var h = (screen.availHeight > 768 ? screen.availHeight : 768);
					var l = (screen.availWidth - w) / 2;
					var t = (screen.availHeight - h) / 2;

					var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
					var url = '../care/care_svc_cont.php?SR='+SR;
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

				function lfAcctShow(){
					//재무회계(수입지출관리);
					var w = 1500;
					var h = 760; //(screen.availHeight > 500 ? screen.availHeight : 500);
					var l = (screen.availWidth - w) / 2;
					var t = (screen.availHeight - h) / 2;

					var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
					var url = '../fa/account_book.php';
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

				function lfAcctShowNew(subCd){
					//재무회계(수입지출관리);
					var w = 1500;
					var h = 760; //(screen.availHeight > 500 ? screen.availHeight : 500);
					var l = (screen.availWidth - w) / 2;
					var t = (screen.availHeight - h) / 2;

					var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
					var url = '../fa/account_book_new.php?subCd='+subCd;
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



				function lfglShow(){
					//재무회계(총계정원장)
					var w = 1500;
					var h = 680; //(screen.availHeight > 500 ? screen.availHeight : 500);
					var l = (screen.availWidth - w) / 2;
					var t = (screen.availHeight - h) / 2;

					var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
					var url = '../fa/gl.php';
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

			</script><?
		}

		$sql = 'SELECT	name
				FROM	menu_top
				WHERE	id		= \''.$menuId.'\'
				AND		use_yn	= \'Y\' '.(!$debug ? ' AND debug = \'N\' ' : '').($isDemo ? ' AND demo_yn = \'Y\' ' : '');
		$menuNm = $conn->get_data($sql);?>

		<div id="left_box">
		<h2><?=$menuNm;?></h2>
		<ul id="sidnav"><?

		if ($_SESSION['userLevel'] == 'C'){
			$sql = 'SELECT	id,name,permit,debug,val
					FROM	menu_left
					WHERE	m_top	= \''.$menuId.'\'
					AND		use_yn	= \'Y\' '.(!$debug ? ' AND debug = \'N\'' : '').($isDemo ? ' AND demo_yn = \'Y\' ' : '').'
					ORDER	BY seq,id';
		}else{
			$sql = 'SELECT	DISTINCT menu.id,menu.name,menu.permit,menu.debug,menu.val
					FROM	menu_left AS menu
					INNER	JOIN	menu_permit AS permit
							ON		permit.org_no	= \''.$_SESSION['userCenterCode'].'\'
							AND		permit.jumin	= \''.$_SESSION['userSSN'].'\'
							AND		permit.use_yn	= \'Y\'
							AND		LEFT(permit.menu_id,1) = menu.m_top
							AND		SUBSTR(permit.menu_id,2,1) = menu.id
					WHERE	menu.m_top	= \''.$menuId.'\'
					AND		menu.use_yn	= \'Y\' '.(!$debug ? ' AND menu.debug = \'N\'' : '').($isDemo ? ' AND menu.demo_yn = \'Y\' ' : '').'
					ORDER	BY menu.seq,menu.id';
		}

		$arrMenu = $conn->_fetch_array($sql);

		if (Is_Array($arrMenu)){
			$first = true;

			foreach($arrMenu as $menu){
				$tmpNm = $menu['name'];?>

				<li class="<?=(!$first ? 'top_line' : '');?>"><a style="cursor:default;"><span style="<?=($menu['debug'] == 'Y' ? 'color:red;' : '');?>"><?=$tmpNm;?></span></a>
				<ul id="sub_menu"><?

				if ($_SESSION['userLevel'] == 'C'){
					$sql = 'SELECT	m_top, m_left, id,name,url,link_gbn,permit, CASE WHEN IFNULL(name,\'\') != \'\' THEN debug ELSE \'Y\' END AS debug,val
							FROM	menu_list
							WHERE	m_top	= \''.$menuId.'\'
							AND		m_left	= \''.$menu['id'].'\'
							AND		use_yn	= \'Y\' '.(!$debug ? ' AND debug = \'N\'' : '').'
							ORDER	BY seq,id';
				}else{
					$sql = 'SELECT	DISTINCT menu.id,menu.name,menu.url,menu.link_gbn,menu.permit, CASE WHEN IFNULL(menu.name,\'\') != \'\' THEN debug ELSE \'Y\' END AS debug,menu.val
							FROM	menu_list AS menu
							INNER	JOIN	menu_permit AS permit
									ON		permit.org_no	= \''.$_SESSION['userCenterCode'].'\'
									AND		permit.jumin	= \''.$_SESSION['userSSN'].'\'
									AND		permit.use_yn	= \'Y\'
									AND		LEFT(permit.menu_id,1)		= menu.m_top
									AND		SUBSTR(permit.menu_id,2,1)	= menu.m_left
									AND		SUBSTR(permit.menu_id,3)	= menu.id
							WHERE	menu.m_top	= \''.$menuId.'\'
							AND		menu.m_left	= \''.$menu['id'].'\'
							AND		menu.use_yn	= \'Y\' '.(!$debug ? ' AND menu.debug = \'N\'' : '').'
							ORDER	BY menu.seq,menu.id';
				}

				$conn->query($sql);
				$conn->fetch();

				$rowCnt = $conn->row_count();

				for($i=0; $i<$rowCnt; $i++){
					$row = $conn->select_row($i);

					if ($row['link_gbn'] == '1'){
						$url = $row['url'];

						if (Is_Numeric(StrPos($url,'?'))){
							$url .= '&';
						}else{
							$url .= '?';
						}

						$url .= 'menuTopId='.$menuId;

						$link = 'location.href=\''.$url.'\';';
					}else{
						$link = StripSlashes($row['url']).';';
					}

					if ($row['val']){
						parse_str($row['val'],$tmpStr);

						if ($tmpStr['orgNo'] || $tmpStr['domain']){
							if (is_numeric(StrPos($tmpStr['orgNo'],'/'.$_SESSION['userCenterCode'])) || is_numeric(StrPos($tmpStr['domain'],'/'.$gDomain))){
								$tmpNm = $row['name'];
							}else{
								$tmpNm = '';
							}
						}else if ($tmpStr['nursingYN']){
							/********************************************
								방문간호서비스 이용하는 센터만 보이도록설정
								기관관리
								-방문간호지시서관리
								-타기관고객정보복사
							********************************************/
							if ($tmpStr['nursingYN'] == $nursingYN){
								if($tmpStr['requestYN']=='Y'){
									//지시서 요청승인여부에 따라 방문간호지시서관리 메뉴 보이게함.
									if($mcRqCnt){
										$tmpNm = $row['name'];
									}else {
										$tmpNm = '';
									}
								}else {
									$tmpNm = $row['name'];
								}
							}else {
								$tmpNm = '';
							}
						}else{
							if ($tmpStr[str_replace('.','_',$gDomain)]){
								$tmpNm = $tmpStr[str_replace('.','_',$gDomain)];
							}else{
								$tmpNm = $row['name'];
							}
						}
					}else{
						$tmpNm = $row['name'];
					}

					if($gDomain != 'kacold.net'){
						if($row['m_top'] == 'P' && $row['m_left'] == '2' && $row['id'] == '02'){
							$tmpNm = '';
						}
					}

					//급여관리->복지사인건비관리(월별)
					if($row['m_top'] == 'F' && $row['m_left'] == '7' && $row['id'] == '11'){
						$fontColor = 'color:green;';
					}else {
						$fontColor = '';
					}

					if ($tmpNm){?>
						<li><a href="#" onclick="<?=$link;?>return false;" style="<?=($row['debug'] == 'Y' ? 'color:red;' : '');?><?=$fontColor;?>"><?=$tmpNm;?></a></li><?
					}
				}

				if ($_SESSION['userLevel'] == 'P'){?>
					<script type="text/javascript">
						function lfChangePassword(){
							var width = 300;
							var height = 200;
							var left = window.screenLeft + ($(window).width() - width) / 2;
							var top = window.screenTop + ($(window).height() - height) / 2;

							var option = 'left='+left+', top='+top+', width='+width+',height='+height+',scrollbars=mp,status=no,resizable=no';
							var url = '../member/pwd_change.php';
							var win = window.open('', 'PWD_CHANGE', option);
								win.opener = self;
								win.focus();

							var parm = new Array();
								parm = {

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

							form.setAttribute('target', 'PWD_CHANGE');
							form.setAttribute('method', 'post');
							form.setAttribute('action', url);

							document.body.appendChild(form);

							form.submit();
						}
					</script>
					<li><a href="#" onclick="lfChangePassword(); return false;" style="">비밀번호 관리</a></li><?
				}

				$conn->row_free();?>
				</ul>
				</li><?

				$first = false;
			}
		}?>
		</ul>
		</div><?
	}
?>