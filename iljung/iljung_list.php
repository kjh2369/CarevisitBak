<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	//ini_set('memory_limit', '128M');
	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}
	
	/*
	 * mode 설정
	 * 1 : 방문일정등록
	 * 2 : 방문일정조회
	 * 3 : 바우처생성내역등록
	 * 4 : 개인회원일정조회
	 * 5 : 실적리스트 조회
	 * 6 : 재가지원 조회
	 */

	$arrSvcKind = Array('0','1','2','3','4','A','B','C');

	$mode = $_REQUEST['mode'];
	$con2 = new connection();
	$code = $_SESSION["userCenterCode"];
	$year_min_max = $myF->year();
	$sr = $_GET['sr'];
	
	
	$year      = $_REQUEST['year'] != '' ? $_REQUEST['year'] : date('Y');
	$kind      = $_REQUEST['kind'] != '' ? $_REQUEST['kind'] : $_SESSION["userCenterKind"][0];
	//$stat_gbn  = $_REQUEST['stat_gbn'] != '' ? $_REQUEST['stat_gbn'] : 'all';
	$find_kind = $_REQUEST['find_kind'];
	$find_sugupja = $_POST['find_sugupja'];
	$strTeam	= $_REQUEST['strTeam'];		//팀장으로 조회

	if (!Empty($_COOKIE['statGbn'])){
		$stat_gbn = $_COOKIE['statGbn'];
	}else{
		if (!Empty($_REQUEST['stat_gbn'])){
			$stat_gbn = $_REQUEST['stat_gbn'];
		}else{
			$stat_gbn = 'all';
		}
	}
	
	if (!isset($find_kind)) $find_kind = 'all';

	if ($mode == 1 || $mode == 2){
		$title = '방문일정(수급자)';
	}else if ($mode == 3){
		$title = '바우처생성내역등록';
	}else if ($mode == 4){
		$title = '방문일정조회';
	}else if ($mode == 5){
		$title = '일정조회(실적)';
	}else if ($mode == 6){
		$title = '일정관리(';

		if ($sr == 'S'){
			$title .= '재가지원';
		}else{
			$title .= '자원연계';
		}

		$title .= ')';
	}else if ($mode == 7){
		$title = '상담지원(';

		if ($sr == 'S'){
			$title .= '재가지원';
		}else{
			$title .= '자원연계';
		}

		$title .= ')';
	}

	if ($mode == 6){
		$sql = 'SELECT	jumin
				,		care_org_no AS org_no
				,		care_org_nm	AS org_nm
				FROM	client_his_care
				WHERE	org_no = \''.$code.'\'
				AND		svc_cd = \''.$sr.'\'';

		$careRow = $conn->_fetch_array($sql,'jumin');
	}

	//정렬순서
	$orderBy = $_POST['optOrder'];

	if (!$orderBy) $orderBy = '1';
	
	//요양,목욕,간호 일정 유무 색 표시
	$sql = 'select DISTINCT t01_jumin as jumin
			,      concat(t01_jumin,\'_\',left(t01_sugup_date, 6),\'_\',t01_svc_subcode) as arr
			,      left(t01_sugup_date, 6) as yymm
			,      t01_svc_subcode as subcd
			from t01iljung
			where t01_ccode = \''.$code.'\'
			and   left(t01_sugup_date, 4) = \''.$year.'\'
			group by yymm, jumin, subcd';
	$sub = $conn->_fetch_array($sql,'arr');
	
	//페이지리스트 조회
	if($code == '34119000298'){
		$newPageList = true;
	}

?>
<style>
.div1{
	width:33%;
	margin-top:3px;
	float:left;
}
</style>
<script type='text/javascript' src='../js/iljung.js'></script>
<script type='text/javascript' src='../js/iljung.reg.js'></script>
<script type='text/javascript' src='../js/work.js'></script>
<script type='text/javascript' src='./plan.js'></script>
<script type='text/javascript' src='./conf.js'></script>
<script type='text/javascript' src='./iljung.js'></script>
<script language='javascript'>
<!--
$(document).ready(function(){
	$('tr[id="ID_ROW"]').unbind('mouseover').bind('mouseover',function(){
		if ($(this).attr('selYn') == 'Y') return;
		$(this).css('background-color','#EAEAEA');
	}).unbind('mouseout').bind('mouseout',function(){
		if ($(this).attr('selYn') == 'Y') return;
		$(this).css('background-color','#FFFFFF');
	}).unbind('click').bind('click',function(){
		$('tr[id="ID_ROW"]').css('background-color','#FFFFFF').attr('selYn','N');
		$(this).css('background-color','#FAF4C0').attr('selYn','Y');
	});
});


function search(page){
	
	if(!page) page = 1;

	document.f.page.value = page;
	
	//if ('<?=$mode;?>' == '6' || '<?=$mode;?>' == '7'){
	//}else{
		__setCookie('statGbn', $('#cboStat').val(), 30);
	//}

	document.f.submit();
}

function showClient(jumin){
	//if ($('#code').val() == '1234'){
		var h = 670;
		var w = 872;
		var t = (screen.availHeight - h) / 2;
		var l = (screen.availWidth - w) / 2;

		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=yes,status=no,resizable=yes';
		var url    = '../sugupja/client_show.php';
			gPlanWin = window.open('', 'CLIENTSHOW', option);
			gPlanWin.opener = self;
			gPlanWin.focus();

		var parm = new Array();
			parm = {
				'code'	: $('#code').attr('value')
			,	'jumin'	: jumin
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

		form.setAttribute('target', 'CLIENTSHOW');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();

		return false;
	//}else{
	//	var code = document.f.code.value;
	//	var width  = 840;
	//	var height = 670;
	//	var left = (window.screen.width  - width)  / 2;
	//	var top  = (window.screen.height - height) / 2;

	//	window.open('../sugupja/client_view.php?code='+code+'&client_cd='+jumin, 'CLIENT_VIEW', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=yes, menubar=no, scrollbars=yes, resizable=no');
	//}
}

function showMember(jumin){
	//if ($('#code').val() == '1234'){
		var h = 670;
		var w = 872;
		var t = (screen.availHeight - h) / 2;
		var l = (screen.availWidth - w) / 2;

		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=yes,status=no,resizable=yes';
		var url    = '../yoyangsa/mem_show.php';
			gPlanWin = window.open('', 'MEMSHOW', option);
			gPlanWin.opener = self;
			gPlanWin.focus();

		var parm = new Array();
			parm = {
				'code'	: $('#code').attr('value')
			,	'jumin'	: jumin
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

		form.setAttribute('target', 'MEMSHOW');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	//}else{
	//	var code = document.f.code.value;
	//	var width  = 840;
	//	var height = 600;
	//	var left = (window.screen.width  - width)  / 2;
	//	var top  = (window.screen.height - height) / 2;

	//	window.open('../yoyangsa/member_view.php?code='+code+'&member_cd='+jumin, 'CLIENT_VIEW', 'left='+left+', top='+top+', width='+width+', height='+height+', toolbar=no, location=no, status=yes, menubar=no, scrollbars=yes, resizable=no');
	//}

	return false;
}

function copy_voucher(month){
	var year = document.f.year.value;

	month = (parseInt(month, 10) < 10 ? '0' : '') + parseInt(month, 10);

	if (!confirm(year+'년 '+month+'월 이전에 생성된 내역중 가장 최근의 내역을 복사합니다.\n\n        ******    실행하시려면 "확인"를 클릭하여 주십시오.    ******\n\n단, 이미 생성된 내역이 있을 경우 바우처내역을 생성하지 않습니다.\n\n')) return;

	var code = document.f.code.value;

	f.action = 'iljung_voucher_make_auto.php?code='+code+'&year='+year+'&month='+month;
	f.submit();
}

function voucherCopy(month){
	/*
	var year = document.f.year.value;

	month = (parseInt(month, 10) < 10 ? '0' : '') + parseInt(month, 10);

	if (!confirm(year+'년 '+month+'월 이전에 생성된 내역중 가장 최근의 내역을 복사합니다.\n\n        ******    실행하시려면 "확인"를 클릭하여 주십시오.    ******\n\n단, 이미 생성된 내역이 있을 경우 바우처내역을 생성하지 않습니다.\n\n')) return;

	var code = document.f.code.value;

	f.action = 'iljung_voucher_make_auto.php?code='+code+'&year='+year+'&month='+month;
	f.submit();
	*/

	if ($('input:checkbox[name="chkSvc[]"]:checked').length == 0){
		alert('생성할 바우처("서비스")를 선택하여 주십시오.');
		return;
	}

	var year = document.f.year.value;

	month = (parseInt(month, 10) < 10 ? '0' : '') + parseInt(month, 10);

	var gbn = $('input:radio[name="optMakeGbn"]:checked').val();
	var str = '';

	if (gbn == '1'){
		str = '전월의 내역을 복사합니다.';
	}else{
		str = year+'년 '+month+'월 이전에 생성된 내역중 가장 최근의 내역을 복사합니다.';
	}

	str += '\n\n        ******    실행하시려면 "확인"를 클릭하여 주십시오.    ******\n\n단, 이미 생성된 내역이 있을 경우 바우처내역을 생성하지 않습니다.\n\n';

	if (!confirm(str)) return;


	//if (!confirm(year+'년 '+month+'월 이전에 생성된 내역중 가장 최근의 내역을 복사합니다.\n\n        ******    실행하시려면 "확인"를 클릭하여 주십시오.    ******\n\n단, 이미 생성된 내역이 있을 경우 바우처내역을 생성하지 않습니다.\n\n')) return;


	var code = document.f.code.value;

	f.action = './iljung_voucher_make_auto.php?code='+code+'&year='+year+'&month='+month+'&gbn='+gbn;
	f.submit();
}

function lfShowIljung(asId, asCode, asYear, asMonth, asSvcCd, asJumin, asKey){
	if ($('input:radio[name="ver"]:checked').val() == '2'){
		_setSugupjaReg(asCode,asSvcCd,asKey,asYear,asMonth,true);
	}else{
		_planReg(asId,asYear,asMonth,asJumin,asSvcCd,'','<?=$sr;?>');
	}
}

function lfShowConf(asYear, asMonth, asJumin, asSvcCd){
	_confShow(asYear,asMonth,asJumin,asSvcCd);
}

function lfCareIljung(jumin,year,month,sr){
	var h = 750; //screen.availHeight;
	var w = 1065;
	var t = 0;
	var l = (screen.availWidth - w) / 2;

	var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=yes,status=no,resizable=yes';
	var url = './care_reg.php';
	var win = window.open('', 'WIN_CARE_ILJUNG', option);
		win.opener = self;
		win.focus();

	var parm = new Array();
		parm = {
			'jumin':jumin
		,	'year':year
		,	'month':month
		,	'sr':sr
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

	form.setAttribute('target', 'WIN_CARE_ILJUNG');
	form.setAttribute('method', 'post');
	form.setAttribute('action', url);

	document.body.appendChild(form);

	form.submit();
}


/*********************************************************

	팀장명 조회

*********************************************************/
function findTeam(){
	
	var result = __findTeam('<?=$code;?>');
	
	if (!result) return;

	$('#strTeam').val(result['name']);
	$('#param').attr('value', 'jumin='+result['jumin']);
	
	search('<?=$page;?>');
}

//-->
</script>
<form name="f" method="post">
<div class="title" style="width:200px; float:left;"><?=$title;?></div><?
if($mode == 1){ ?>
<div style="width:220px; float:right;  margin-top:10px;">일정 표시 : 요양: <font color="#15ef00">■</font>&nbsp; 목욕: <font color="#3435ff">■</font>&nbsp; 간호: <font color="fa6789">■</font></div>
<? } ?>
<table class="my_table my_border">
	<colgroup>
		<col width="45px">
		<col width="80px"><?
		if ($mode != 5 &&
			$mode != 7){?>
			<col width="60px">
			<col width="50px"><?
		}

		if ($mode != 7 && $mode != 6){?>
			<col width="60px">
			<col width="50px"><?
		}?>
		<col width="60px">
		<col width="100px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년도</th>
			<td>
				<select id="year" name="year" style="width:auto;"><?
				for($i=$year_min_max[0]; $i<=$year_min_max[1]; $i++){?>
					<option value="<?=$i;?>"<? if($i == $year){echo "selected";}?>><?=$i;?></option><?
				}?>
				</select>년
			</td><?
			if ($mode != 5 &&
				$mode != 7){?>
				<th>수급상태</th>
				<td>
					<select id="cboStat" name="stat_gbn" style="width:auto;">
						<!--option value="all" <?if($stat_gbn == 'all'){?>selected<?}?>>전체</option-->
						<option value="1" <?if($stat_gbn == '1'){?>selected<?}?>>이용</option>
						<option value="9" <?if($stat_gbn == '9'){?>selected<?}?>>중지</option>
					</select>
				</td><?
			}

			if ($mode != 7 && $mode != 6){?>
				<th>서비스</th>
				<td><?
					if($mode == 3){
						$kind_list = $conn->kind_list($code);
					}else {
						$kind_list = $conn->kind_list($code, $gHostSvc['voucher']);
					}

					echo '<select name=\'find_kind\' style=\'width:80px;\'>';
					echo '<option value=\'all\'>전체</option>';

					foreach($kind_list as $i => $k){
						if (($mode != 3) || ($mode == 3 && $k['code'] != '0')){
							if ($k['code'] != '6')
								echo '<option value=\''.$k['code'].'\' '.($find_kind == $k['code'] ? 'selected' : '').'>'.$k['name'].'</option>';
						}
					}

					echo '</select>';?>
				</td><?
			}

			if ($mode == 7){?>
				<th>직원명</th><?
			}else if ($mode == 6){?>
				<th>대상자</th><?
			}else{?>
				<th>수급자명</th><?
			}?>
			<td>
				<input name="find_sugupja" type="text" value="<?=$find_sugupja;?>" maxlength="20" style="width:95%; ime-mode:active;" onFocus="this.select();">
			</td><?
			if($mode == 1){ ?>
				<th class='center bottom'>팀장명</th>
				<td class='left bottom last'><div style='float:left;  width:auto; height:100%; padding-top:1px;'><span class='btn_pack find' onclick='findTeam();'></span></div><div style='width:auto; height:100%; padding-top:2px;'><!--span id='strTeam' name="strTeam" class='bold'><?=$strTeam;?></span--><input id="strTeam" name="strTeam" type="text" style="width:75px; padding:0; background-color:#eeeeee;" value="<?=$strTeam;?>" readonly /></div></td><?
			} ?>
			<td class="last" style="line-height:26px; padding-left:5px; vertical-align:top; padding-top:2px;"><?
				if ($mode == 1){?>
					<!--div style="float:right; width:auto;">
						<input id="ver1" name="ver" type="radio" value="1" class="radio" checked><label for="ver1">신</label>
						<input id="ver2" name="ver" type="radio" value="2" class="radio"><label for="ver2">구</label>
					</div--><?
				}?>
				<div style="float:left; width:auto;">
					<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="search();">조회</button></span>
				</div>
			</td>
		</tr><?
		if ($mode == 6){
			if($sr == 'R'){ ?>
				<tr>
					<th class="center">정렬</th>
					<td class="last" colspan="6">
						<label><input id="optOrder1" name="optOrder" type="radio" class="radio" value="1" onclick="search();" <?=($orderBy == '1' ? 'checked' : '');?>>수급자순</label>
						<label><input id="optOrder2" name="optOrder" type="radio" class="radio" value="2" onclick="search();" <?=($orderBy == '2' ? 'checked' : '');?>>소속기관순</label>
					</td>
				</tr><?
			}
		}?>
	</tbody>
</table>

<?
	if ($mode == 3){?>
		<table class="my_table" style="width:100%;">
			<colgroup>
				<col width="121px">
				<col width="450px">
				<col>
			</colgroup>
			<tbody>
				<tr>
					<th rowspan="2">바우처내역 월별 생성</th>
					<td class="last bottom" style="padding-left:2px;" colspan="2">
					<?
						echo '<div>
								<label><input name="optMakeGbn" type="radio" class="radio" value="1" checked>전월 내역으로 생성</label>
								<label><input name="optMakeGbn" type="radio" class="radio" value="2">최근 내역으로 생성</label>
							  </div>';

						$svcList = $conn->kind_list($code);
						$svcCnt  = 0;

						foreach($svcList as $svcCD => $svcArr){
							if ($svcArr['id'] >= '21' && $svcArr['id'] <= '24'){
								echo '<input id=\'chkSvc_'.$svcArr['id'].'\' name=\'chkSvc[]\' type=\'checkbox\' value=\''.$svcArr['code'].'\' class=\'checkbox\'><label for=\'chkSvc_'.$svcArr['id'].'\'>'.$svcArr['name'].'</label>';
								$svcCnt ++;
							}
						}

						if ($svcCnt > 0){
							echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<input id="chkReVou" name="chkReVou" type="checkbox" value="Y" class="checkbox"><label for="chkReVou">재생성</label>';
						}

						for($i=1; $i<=12; $i++){
							$class = 'my_month ';

							if ($i == intval($month)){
								$class .= 'my_month_y ';
								$color  = 'color:#000000;';
							}else{
								$class .= 'my_month_1 ';
								$color  = 'color:#666666;';
							}

							#$text = '<a href="#" onclick="copy_voucher('.$i.');">'.$i.'월</a>';
							$text = '<a href="#" onclick="voucherCopy('.$i.');">'.$i.'월</a>';

							$style = 'float:left; margin-top:1px;';

							if ($i == 12){
							}else{
								$style .= 'margin-right:2px;';
							}?>
							<div class="<?=$class;?>" style="<?=$style;?>"><?=$text;?></div><?
						}
					?>
					</td>
					<td class="last bottom">&nbsp;</td>
				</tr>
				<tr>
					<td class="left last bold" colspan="2">
						※바우처가 생성되어 있다면 생성되지 않습니다.<br>
						※이월시간이 생성시간보다 많이 남을경우 생성되지 않습니다.<br>
					</td>
				</tr>
			</tbody>
		</table><?
	}
?>

<table class="my_table" style="width:100%; border-bottom:none;">
	<colgroup>
		<col width="40px">
		<col width="70px"><?

		if ($mode == 6){?>
			<col width="70px"><?
		}else if ($mode != 7){?>
			<col width="90px"><?
		}

		if ($mode == 3 ||
			$mode == 7){
		}else if ($mode == 6){
			if($sr == 'R'){ ?>
				<col width="180px"><?
			}
		}else{?>
			<col width="50px">
			<col width="60px">
			<col width="70px"><?
		}?>
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th><?

			if ($mode == 7){?>
				<th class="head">직원명</th><?
			}else if ($mode == 6){?>
				<th class="head">대상자</th>
				<th class="head">제공서비스</th><?
			}else{?>
				<th class="head">수급자명</th>
				<th class="head">제공서비스</th><?
			}

			if ($mode == 3 ||
				$mode == 7){
			}else if ($mode == 6){
				if($sr == 'R'){?>
					<th class="head">소속기관</th><?
				}
			}else{?>
				<th class="head">등급</th>
				<th class="head">구분</th>
				<th class="head">주담당</th><?
			}?>
			<th class="head last">월별일정</th>
		</tr>
	</thead>
	<tbody>
	<?
		/**************************************************

			일정리스트

		**************************************************/
		if ($mode == 1 || $mode == 2 || $mode == 4 || $mode == 6){
			//계획
			$sql = 'select t01_jumin as cd
					,      t01_mkind as kind
					,      count(*) as cnt
					,      sum(case substring(t01_sugup_date, 5, 2) when \'01\' then 1 else 0 end) as mon01
					,      sum(case substring(t01_sugup_date, 5, 2) when \'02\' then 1 else 0 end) as mon02
					,      sum(case substring(t01_sugup_date, 5, 2) when \'03\' then 1 else 0 end) as mon03
					,      sum(case substring(t01_sugup_date, 5, 2) when \'04\' then 1 else 0 end) as mon04
					,      sum(case substring(t01_sugup_date, 5, 2) when \'05\' then 1 else 0 end) as mon05
					,      sum(case substring(t01_sugup_date, 5, 2) when \'06\' then 1 else 0 end) as mon06
					,      sum(case substring(t01_sugup_date, 5, 2) when \'07\' then 1 else 0 end) as mon07
					,      sum(case substring(t01_sugup_date, 5, 2) when \'08\' then 1 else 0 end) as mon08
					,      sum(case substring(t01_sugup_date, 5, 2) when \'09\' then 1 else 0 end) as mon09
					,      sum(case substring(t01_sugup_date, 5, 2) when \'10\' then 1 else 0 end) as mon10
					,      sum(case substring(t01_sugup_date, 5, 2) when \'11\' then 1 else 0 end) as mon11
					,      sum(case substring(t01_sugup_date, 5, 2) when \'12\' then 1 else 0 end) as mon12
					  from t01iljung
					 where t01_ccode               = \''.$code.'\'
					   and left(t01_sugup_date, 4) = \''.$year.'\'
					   and t01_del_yn              = \'N\'';


			if ($mode == 4){
				$sql .= ' and \''.$_SESSION['userSSN'].'\' in (t01_mem_cd1, t01_mem_cd2)';
			}

			if ($mode == 6){
				$sql .= ' AND t01_mkind = \''.$sr.'\'';
			}else{
				$sql .= ' AND t01_mkind != \'6\'
						  AND t01_mkind != \'S\'
						  AND t01_mkind != \'R\'';
			}
			
			
			$sql .= ' group by t01_jumin, t01_mkind ';
			
			$conn->query($sql);
			$conn->fetch();

			$row_count = $conn->row_count();

			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);
				$iljung[$row['cd']][$row['kind']] = $row;
			}

			$conn->row_free();

		}else if ($mode == 3){
			//바우처
			$sql = 'select voucher_jumin as cd
					,      count(*) as cnt
					,      min(voucher_kind) as voucher_kind
					,      sum(case when substring(voucher_yymm, 5, 2) = \'01\' and del_flag = \'N\' then ifnull(voucher_seq, 0) else 0 end) as mon01
					,      sum(case when substring(voucher_yymm, 5, 2) = \'02\' and del_flag = \'N\' then ifnull(voucher_seq, 0) else 0 end) as mon02
					,      sum(case when substring(voucher_yymm, 5, 2) = \'03\' and del_flag = \'N\' then ifnull(voucher_seq, 0) else 0 end) as mon03
					,      sum(case when substring(voucher_yymm, 5, 2) = \'04\' and del_flag = \'N\' then ifnull(voucher_seq, 0) else 0 end) as mon04
					,      sum(case when substring(voucher_yymm, 5, 2) = \'05\' and del_flag = \'N\' then ifnull(voucher_seq, 0) else 0 end) as mon05
					,      sum(case when substring(voucher_yymm, 5, 2) = \'06\' and del_flag = \'N\' then ifnull(voucher_seq, 0) else 0 end) as mon06
					,      sum(case when substring(voucher_yymm, 5, 2) = \'07\' and del_flag = \'N\' then ifnull(voucher_seq, 0) else 0 end) as mon07
					,      sum(case when substring(voucher_yymm, 5, 2) = \'08\' and del_flag = \'N\' then ifnull(voucher_seq, 0) else 0 end) as mon08
					,      sum(case when substring(voucher_yymm, 5, 2) = \'09\' and del_flag = \'N\' then ifnull(voucher_seq, 0) else 0 end) as mon09
					,      sum(case when substring(voucher_yymm, 5, 2) = \'10\' and del_flag = \'N\' then ifnull(voucher_seq, 0) else 0 end) as mon10
					,      sum(case when substring(voucher_yymm, 5, 2) = \'11\' and del_flag = \'N\' then ifnull(voucher_seq, 0) else 0 end) as mon11
					,      sum(case when substring(voucher_yymm, 5, 2) = \'12\' and del_flag = \'N\' then ifnull(voucher_seq, 0) else 0 end) as mon12
					  from voucher_make
					 where org_no                = \''.$code.'\'
					   and left(voucher_yymm, 4) = \''.$year.'\'
					 group by voucher_jumin';


			$conn->query($sql);
			$conn->fetch();

			$row_count = $conn->row_count();

			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);
				$iljung[$row['cd']] = $row;
			}

			$conn->row_free();

		}else if ($mode == 5){
			//실적
			if ($code == '31138000044' /*엔젤*/||
				$code == '31174000065' /*웃음드림방문요양센터*/){
				$sql = 'select t01_jumin as cd
					,      t01_mkind as kind
					,      sum(case when t01_status_gbn = \'1\' then 1 else 0 end) as cnt
					,      sum(case when substring(t01_sugup_date, 5, 2) = \'01\' then 1 else 0 end) as mon01
					,      sum(case when substring(t01_sugup_date, 5, 2) = \'02\' then 1 else 0 end) as mon02
					,      sum(case when substring(t01_sugup_date, 5, 2) = \'03\' then 1 else 0 end) as mon03
					,      sum(case when substring(t01_sugup_date, 5, 2) = \'04\' then 1 else 0 end) as mon04
					,      sum(case when substring(t01_sugup_date, 5, 2) = \'05\' then 1 else 0 end) as mon05
					,      sum(case when substring(t01_sugup_date, 5, 2) = \'06\' then 1 else 0 end) as mon06
					,      sum(case when substring(t01_sugup_date, 5, 2) = \'07\' then 1 else 0 end) as mon07
					,      sum(case when substring(t01_sugup_date, 5, 2) = \'08\' then 1 else 0 end) as mon08
					,      sum(case when substring(t01_sugup_date, 5, 2) = \'09\' then 1 else 0 end) as mon09
					,      sum(case when substring(t01_sugup_date, 5, 2) = \'10\' then 1 else 0 end) as mon10
					,      sum(case when substring(t01_sugup_date, 5, 2) = \'11\' then 1 else 0 end) as mon11
					,      sum(case when substring(t01_sugup_date, 5, 2) = \'12\' then 1 else 0 end) as mon12
					  from t01iljung
					 where t01_ccode               = \''.$code.'\'
					   and left(t01_sugup_date, 4) = \''.$year.'\'
					   and t01_del_yn              = \'N\'
					 group by t01_jumin, t01_mkind';
			}else{
				$sql = 'select t01_jumin as cd
						,      t01_mkind as kind
						,      sum(case when t01_status_gbn = \'1\' then 1 else 0 end) as cnt
						,      sum(case when substring(t01_sugup_date, 5, 2) = \'01\' and t01_status_gbn = \'1\' then 1 else 0 end) as mon01
						,      sum(case when substring(t01_sugup_date, 5, 2) = \'02\' and t01_status_gbn = \'1\' then 1 else 0 end) as mon02
						,      sum(case when substring(t01_sugup_date, 5, 2) = \'03\' and t01_status_gbn = \'1\' then 1 else 0 end) as mon03
						,      sum(case when substring(t01_sugup_date, 5, 2) = \'04\' and t01_status_gbn = \'1\' then 1 else 0 end) as mon04
						,      sum(case when substring(t01_sugup_date, 5, 2) = \'05\' and t01_status_gbn = \'1\' then 1 else 0 end) as mon05
						,      sum(case when substring(t01_sugup_date, 5, 2) = \'06\' and t01_status_gbn = \'1\' then 1 else 0 end) as mon06
						,      sum(case when substring(t01_sugup_date, 5, 2) = \'07\' and t01_status_gbn = \'1\' then 1 else 0 end) as mon07
						,      sum(case when substring(t01_sugup_date, 5, 2) = \'08\' and t01_status_gbn = \'1\' then 1 else 0 end) as mon08
						,      sum(case when substring(t01_sugup_date, 5, 2) = \'09\' and t01_status_gbn = \'1\' then 1 else 0 end) as mon09
						,      sum(case when substring(t01_sugup_date, 5, 2) = \'10\' and t01_status_gbn = \'1\' then 1 else 0 end) as mon10
						,      sum(case when substring(t01_sugup_date, 5, 2) = \'11\' and t01_status_gbn = \'1\' then 1 else 0 end) as mon11
						,      sum(case when substring(t01_sugup_date, 5, 2) = \'12\' and t01_status_gbn = \'1\' then 1 else 0 end) as mon12
						  from t01iljung
						 where t01_ccode               = \''.$code.'\'
						   and left(t01_sugup_date, 4) = \''.$year.'\'
						   and t01_del_yn              = \'N\'
						 group by t01_jumin, t01_mkind';
			}

			$conn->query($sql);
			$conn->fetch();

			$row_count = $conn->row_count();

			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);

				if ($row['cnt'] > 0){
					$iljung[$row['cd']][$row['kind']] = $row;
				}
			}

			$conn->row_free();

		}else if ($mode == 7){
			//재가지원(상담)
			$sql = 'SELECT	jumin AS cd
					,		SUM(CASE WHEN SUBSTR(iljung_dt,5,2) = \'01\' THEN 1 ELSE 0 END) AS m1
					,		SUM(CASE WHEN SUBSTR(iljung_dt,5,2) = \'02\' THEN 1 ELSE 0 END) AS m2
					,		SUM(CASE WHEN SUBSTR(iljung_dt,5,2) = \'03\' THEN 1 ELSE 0 END) AS m3
					,		SUM(CASE WHEN SUBSTR(iljung_dt,5,2) = \'04\' THEN 1 ELSE 0 END) AS m4
					,		SUM(CASE WHEN SUBSTR(iljung_dt,5,2) = \'05\' THEN 1 ELSE 0 END) AS m5
					,		SUM(CASE WHEN SUBSTR(iljung_dt,5,2) = \'06\' THEN 1 ELSE 0 END) AS m6
					,		SUM(CASE WHEN SUBSTR(iljung_dt,5,2) = \'07\' THEN 1 ELSE 0 END) AS m7
					,		SUM(CASE WHEN SUBSTR(iljung_dt,5,2) = \'08\' THEN 1 ELSE 0 END) AS m8
					,		SUM(CASE WHEN SUBSTR(iljung_dt,5,2) = \'09\' THEN 1 ELSE 0 END) AS m9
					,		SUM(CASE WHEN SUBSTR(iljung_dt,5,2) = \'10\' THEN 1 ELSE 0 END) AS m10
					,		SUM(CASE WHEN SUBSTR(iljung_dt,5,2) = \'11\' THEN 1 ELSE 0 END) AS m11
					,		SUM(CASE WHEN SUBSTR(iljung_dt,5,2) = \'12\' THEN 1 ELSE 0 END) AS m12
					FROM	care_counsel_iljung
					WHERE	org_no				= \''.$code.'\'
					AND		iljung_sr			= \''.$sr.'\'
					AND		LEFT(iljung_dt,4)	= \''.$year.'\'
					GROUP	BY jumin';

			$iljung = $conn->_fetch_array($sql,'cd');

		}

		if ($mode == 1 || $mode == 6){
			//수급자 마지막 상태
			$sql = 'SELECT	CONCAT(jumin, \'_\', svc_cd) AS cd, svc_stat
					FROM	client_his_svc
					WHERE	org_no = \''.$code.'\'
					AND		LEFT(from_dt,4) <= \''.$year.'\'
					AND		LEFT(to_dt,4) >= \''.$year.'\'
					ORDER	BY jumin, from_dt, to_dt';
			$tgtLastStat = $conn->_fetch_array($sql,'cd');
		}

		if ($mode == 1 || $mode == 4 || $mode == 6){
			//담당요야보호사 리스트
			$sql = 'select	concat(m03_jumin,\'_\',m03_mkind) as cd
					,		m03_yoyangsa1 as mem_cd
					,		m03_yoyangsa1_nm as mem_nm
					from	m03sugupja
					where	m03_ccode = \''.$code.'\'
					and		ifnull(m03_yoyangsa1,\'\') != \'\'';
			$personList = $conn->_fetch_array($sql,'cd');



			//건보 업로드 로그
			if ($mode == 1){
				$sql = 'SELECT jumin
						,      yymm
						,      care
						,      bath
						,      nurse
						  FROM longcare_his
						 WHERE org_no       = \''.$code.'\'
						   AND LEFT(yymm,4) = \''.$year.'\'';

				$conn->query($sql);
				$conn->fetch();

				$rowCount = $conn->row_count();

				for($i=0; $i<$rowCount; $i++){
					$row = $conn->select_row($i);

					$liLCHis[$row['jumin']][$row['yymm']] = array('care'=>$row['care'],'bath'=>$row['bath'],'nurs'=>$row['nurse']);
				}

				$conn->row_free();
			}

			//일정리스트
			$sql = 'select his.jumin
					,      mst.nm
					,      mst.mem_cd
					,      case his.svc_cd when mst.kind then mst.mem_nm else \'\' end as mem_nm
					,      mst.c_key
					,      case his.svc_cd when \'0\' then case mst.lvl1 when \'9\' then \'일반\' else concat(mst.lvl1,\'등급\') end
										   when \'4\' then concat(mst.lvl2,\'등급\') else \'\' end as ylvl
					,		case his.svc_cd when \'0\' then case mst.skind when \'1\' then \'일반\' when \'2\' then \'의료\' when \'3\' then \'기초\' when \'4\' then \'경감\' else \'\' end else \'\' end as kindNm
					,	   mst.rate as rate
					,      his.svc_cd as svc_cd
					,      date_format(his.from_dt,\'%Y%m\') as from_dt
					,      date_format(his.to_dt,\'%Y%m\') as to_dt';

			if ($mode == 6){
				$sql .= '
					, care.care_org_no
					, care.care_org_nm';
			}

			$sql .= ' from (
						   select jumin
						   ,      svc_cd
						   ,      from_dt
						   ,      to_dt
							 from client_his_svc as his
							where org_no           = \''.$code.'\'
							  and left(from_dt,4) <= \''.$year.'\'
							  and left(to_dt,4)   >= \''.$year.'\'';

			if ($find_kind != 'all'){
				$sql .= ' and svc_cd = \''.$find_kind.'\'';
			}

			if ($mode == 6){
				$sql .= ' AND svc_cd = \''.$sr.'\'';
			}else{
				$sql .= ' AND svc_cd != \'6\'
						  AND svc_cd != \'S\'
						  AND svc_cd != \'R\'';
			}

			/*
			if ($stat_gbn != 'all'){
				if ($stat_gbn == '1'){
					#$sql .= ' and ((date_format(now(),\'%Y%m%d\') >= date_format(from_dt,\'%Y%m%d\') and date_format(now(),\'%Y%m%d\') <= date_format(to_dt,\'%Y%m%d\'))
					#		  and   svc_stat = \'1\')';
					$sql .= ' and   svc_stat = \'1\'';
				}else{
					$sql .= ' and svc_stat != \'1\'';
				}
			}
			*/

			$sql .= '	   ) as his';

			if ($mode == 4){
				$sl = '';

				foreach($arrSvcKind as $lsSvcCd){
					if (!Empty($sl)) $sl .= ' UNION ALL ';
					$sl .= 'SELECT DISTINCT t01_jumin AS jumin
							  FROM t01iljung
							 WHERE t01_ccode   = \''.$code.'\'
							   AND t01_mkind   = \''.$lsSvcCd.'\'
							   AND t01_mem_cd1 = \''.$_SESSION['userSSN'].'\'
							   AND LEFT(t01_sugup_date,4) = \''.$year.'\'';

					if ($lsSvcCd == '6'){
						$sl .= ' AND t01_svc_subcd = \''.$sr.'\'';
					}

					if ($lsSvcCd == '0' || $lsSvcCd == '4'){
						$sl .= ' UNION ALL
								SELECT DISTINCT t01_jumin AS jumin
								  FROM t01iljung
								 WHERE t01_ccode   = \''.$code.'\'
								   AND t01_mkind   = \''.$lsSvcCd.'\'
								   AND t01_mem_cd2 = \''.$_SESSION['userSSN'].'\'
								   AND LEFT(t01_sugup_date,4) = \''.$year.'\'';
					}
				}
				$sql .= ' INNER JOIN ('.$sl.') AS iljung
						     ON iljung.jumin = his.jumin ';
			}else{
				$sql .= ' ';
			}

			$sql .= 'inner join (
						   select min(m03_mkind) as kind
						   ,      m03_jumin as jumin
						   ,      m03_name as nm
						   ,      m03_yoyangsa1 as mem_cd
						   ,      m03_yoyangsa1_nm as mem_nm
						   ,      m03_key as c_key
						   ,      lvl.level as lvl1
						   ,      dis.svc_lvl as lvl2
						   ,	  skind.skind as skind
						   ,	  skind.rate as rate
							 from m03sugupja
							 left join (
								  select jumin
								  ,      svc_cd
								  ,      level
								  ,      from_dt
								  ,      to_dt
									from client_his_lvl
								   where org_no = \''.$code.'\'
								   and date_format(now(),\'%Y%m%d\') >= date_format(from_dt,\'%Y%m%d\')
								   and date_format(now(),\'%Y%m%d\') <= date_format(to_dt,  \'%Y%m%d\')
								  ';

			if ($find_kind != 'all')
				$sql .= ' and svc_cd = \''.$find_kind.'\'';

			if ($mode == 6){
				$sql .= ' AND svc_cd = \'6\'';
			}

			$sql .= '			    GROUP BY jumin, svc_cd
									order by jumin, from_dt desc, to_dt desc
								  ) as lvl
							   on lvl.svc_cd = m03_mkind
							  and lvl.jumin = m03_jumin
							 left join (
							 	select jumin
								, kind as skind
								, rate
								, from_dt
								, to_dt
								from client_his_kind
								where org_no = \''.$code.'\'
								and date_format(now(),\'%Y%m%d\') >= date_format(from_dt,\'%Y%m%d\')
								and date_format(now(),\'%Y%m%d\') <= date_format(to_dt,  \'%Y%m%d\')
								GROUP BY jumin
								order by jumin, from_dt desc, to_dt desc
								) as skind
								on skind.jumin = m03_jumin
							 left join (
								  select jumin
								  ,      svc_val
								  ,      svc_lvl
								  ,      from_dt
								  ,      to_dt
									from client_his_dis
								   where org_no = \''.$code.'\'
								  ) as dis
							   on dis.jumin = m03_jumin
							left join ( select jumin, yname
							from client_his_team as team
							left join ( select min(m02_mkind) as kind, m02_yjumin, m02_yname as yname
										from   m02yoyangsa            
										where  m02_ccode = \''.$code.'\'
										group by m02_yjumin) as mem             
							on    mem.m02_yjumin = team.team_cd 
							where  team.org_no = \''.$code.'\'
							and    date_format(now(),\'%Y%m\') >= team.from_ym
							and    date_format(now(),\'%Y%m\') <= team.to_ym 
							and    team.del_flag = \'N\'
							group  by jumin) as yoy
							on   yoy.jumin  = m03_jumin  
							where m03_ccode = \''.$code.'\'';

			if ($find_sugupja != '')
				$sql .= ' and m03_name >= \''.$find_sugupja.'\'';

			if($strTeam)		
				$sql .= ' and yname = \''.$strTeam.'\'';
			

			$sql .= '		group by m03_jumin) as mst
						on mst.jumin = his.jumin';

			if ($mode == 6){
				#$sql .= '
				#		inner join client_his_care as care
				#			on care.org_no = \''.$code.'\'
				#			and care.svc_cd = \''.$sr.'\'
				#			and care.jumin = his.jumin';
				$sql .= '
						left join client_his_care as care
							on care.org_no = \''.$code.'\'
							and care.svc_cd = \''.$sr.'\'
							and care.jumin = his.jumin';

				if ($orderBy == '2'){
					$sql .= '
						 order by case when care_org_nm != \'\' then 1 else 2 end, care_org_nm, nm, jumin, svc_cd';
				}else{
					$sql .= '
						 order by nm, jumin, svc_cd';
				}
			}else{
				$sql .= '
						 order by nm, jumin, svc_cd';
			}
			

			//if ($debug) echo nl2br($sql);

			$conn->query($sql);
			$conn->fetch();

			$rowCount = $conn->row_count();

			$no = 1;
			for($i=0; $i<$rowCount; $i++){
				$row = $conn->select_row($i);

				//이용, 중지
				if ($stat_gbn == '1'){
					if ($tgtLastStat[$row['jumin'].'_'.$row['svc_cd']]['svc_stat'] != '1') continue;
				}else if ($stat_gbn != 'all'){
					if ($tgtLastStat[$row['jumin'].'_'.$row['svc_cd']]['svc_stat'] == '1') continue;
				}

				if ($tmpKey != $row['jumin']){
					$tmpKey  = $row['jumin'];
					$idx1 = sizeof($data);

					$data[$idx1] = array(
						'no'	=>$no
					,	'jumin'	=>$row['jumin']
					,	'key'	=>$row['c_key']
					,	'name'	=>$row['nm']
					,	'orgnm'	=>$row['care_org_nm']
					);

					$no ++;
				}

				if ($tmpSvc != $tmpKey.'_'.$row['svc_cd']){
					$tmpSvc  = $tmpKey.'_'.$row['svc_cd'];
					$idx2 = sizeof($data[$idx1]['svcList']);

					//재가요양 및 장애인활동지원만 등급을 표시한다.
					if ($row['svc_cd'] == 0 || $row['svc_cd'] == 4){
						$lvl = $row['ylvl'];
						if($row['svc_cd'] == 0 && $row['kindNm']){
							$sKind = $row['kindNm'].'('.$row['rate'].')';
						}else {
							$sKind = '';
						}
					}else {
						$lvl = '';
						$sKind = '';
					}


					$data[$idx1]['svcList'][$idx2] = array(
						'memNm' =>$personList[$row['jumin'].'_'.$row['svc_cd']]['mem_nm'] //$row['mem_nm']
					,	'memCd' =>$personList[$row['jumin'].'_'.$row['svc_cd']]['mem_cd'] //$ed->en($row['mem_cd'])
					,	'lvlNm'	=>$lvl
					,	'kindNm'=>$sKind
					,	'svcCd'	=>$row['svc_cd']
					,	'svcNm'	=>$conn->kind_name_svc($row['svc_cd'])
					,	'period'=>array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0,12=>0)
					);
				}

				for($j=1; $j<=12; $j++){
					$mon = ($j < 10 ? '0' : '').$j;

					if ($row['from_dt'] <= $year.$mon && $row['to_dt'] >= $year.$mon){
						if ($row['svc_cd'] >= 1 && $row['svc_cd'] <= 4){
							//바우처 생된될 월만 등록가능
							#if ($laMakeVou[$row['jumin']][$row['svc_cd']][$j] == 'Y'){
								$data[$idx1]['svcList'][$idx2]['period'][$j] = 1;
							#}
						}else{
							//재가요양 및 기타유료는 계약기간내 모두 등록가능
							$data[$idx1]['svcList'][$idx2]['period'][$j] = 1;
						}
					}
				}
			}
			
			$conn->row_free();
			
			$item_count = 100;
			$page_count = 10;
			$page = $_REQUEST["page"];

			if (!is_numeric($page)) $page = 1;
			if ($page < 1) $page = 1;

			$total_count = ($no-1);

			// 전체 갯수가 현재페이지 리스트 갯수보다 작으면
			if ($total_count < (intVal($page) - 1) * $item_count) $page = 1;

			$params = array(
				'curMethod'		=> 'post',
				'curPage'		=> 'javascript:search',
				'curPageNum'	=> $page,
				'pageVar'		=> 'page',
				'extraVar'		=> '',
				'totalItem'		=> $total_count,
				'perPage'		=> $page_count,
				'perItem'		=> $item_count,
				'prevPage'		=> '[이전]',
				'nextPage'		=> '[다음]',
				'prevPerPage'	=> '[이전'.$page_count.'페이지]',
				'nextPerPage'	=> '[다음'.$page_count.'페이지]',
				'firstPage'		=> '[처음]',
				'lastPage'		=> '[끝]',
				'pageCss'		=> 'page_list_1',
				'curPageCss'	=> 'page_list_2'
			);

			$pageCount = $page;

			if ($pageCount == "") $pageCount = 1;
			
			$pageCnt = $item_count * $pageCount;

			$pageCount = (intVal($pageCount) - 1) * $item_count;
			
			$liRowIdx = 0;
			if (is_array($data)){
				foreach($data as $row){
					
					if($newPageList){
						if ($row['no'] <= $pageCount) continue;
						
						if($row['no']<=$pageCnt){
							
							$rowCnt = sizeof($row['svcList']);

							for($i=0; $i<$rowCnt; $i++){
								if ($mode == 1){?>
									<tr id="ID_ROW" setYn="N"><?
								}else{?>
									<tr><?
								}
								if ($i == 0){?>
									<td class="center" rowspan="<?=$rowCnt;?>"><?=$row['no'];?></td>
									<td class="left" rowspan="<?=$rowCnt;?>"><a href="#" onclick="return showClient('<?=$ed->en($row['jumin']);?>');"><?=$row['name'];?></a></td><?
								}?>
								<td class="left"><?=$row['svcList'][$i]['svcNm'];?></td><?
								if ($mode == 6){
									if($sr == 'R'){ ?>
										<td class="center"><div class="nowrap left" style="width:180px;"><?=$row['orgnm'];?></div></td><?
									}
								}else{?>
									<td class="center"><?=$row['svcList'][$i]['lvlNm'];?></td>
									<td class="center"><?=$row['svcList'][$i]['kindNm'];?></td>
									<td class="left"><a href="#" onclick="return showMember('<?=$ed->en($row['svcList'][$i]['memCd']);?>');"><?=$row['svcList'][$i]['memNm'];?></a></td><?
								}?>
								<td class="left last"><?
									for($j=1; $j<=12; $j++){
										$class = 'my_month ';
										$mon   = ($j<10?'0':'').$j;
										$text  = $j.'월';
										$lsId  = 'lo'.$row['svcList'][$i]['svcCd'].'_'.$liRowIdx.'_'.$j;

										if ($j == 12){
											$style = 'float:left;';
										}else{
											$style = 'float:left; margin-right:2px;';
										}

										if ($row['svcList'][$i]['period'][$j] > 0){
											if ($lbPlanMode){
												$link = 'lfShowIljung(\''.$lsId.'\',\''.$code.'\',\''.$year.'\',\''.$mon.'\',\''.$row['svcList'][$i]['svcCd'].'\',\''.$ed->en($row['jumin']).'\',\''.$row['key'].'\');';
											}else{
												$link  = '_setSugupjaReg(\''.$code.'\',\''.$row['svcList'][$i]['svcCd'].'\',\''.$row['key'].'\',$(\'#year\').val(),\''.$mon.'\',true);';
											}

											$color = 'color:#000000; cursor:pointer;';

											if ($iljung[$row['jumin']][$row['svcList'][$i]['svcCd']]['mon'.$mon] > 0){
												$class .= 'my_month_y';
											}else{
												$class .= 'my_month_1';
											}
										}else{
											$link   = '';
											$color  = 'color:#cccccc; cursor:default;';
											$class .= 'my_month_1';
										}

										$style .= $color;?>
										<div id="<?=$lsId;?>" class="<?=$class;?>" style="<?=$style;?>" onclick="<?=$link;?>">
											<div style="position:absolute;"><?=$text;?></div>
											<div style="clear:both;">
												<div style="float:left; width:11px; height:5px; <?=($sub[$row['jumin'].'_'.$year.$mon.'_200']['subcd'] == '200' ? 'background:url(../image/bg_cal_g.gif) no-repeat;' : '');?>"></div>
												<div style="float:left; width:11px; height:5px; <?=($sub[$row['jumin'].'_'.$year.$mon.'_500']['subcd'] == '500' ? 'background:url(../image/bg_cal_b.gif) no-repeat;' : '');?>"></div>
												<div style="float:left; width:11px; height:5px; <?=($sub[$row['jumin'].'_'.$year.$mon.'_800']['subcd'] == '800' ? 'background:url(../image/bg_cal_r.gif) no-repeat;' : '');?>"></div>
											</div>
										</div><?
									}?>
								</td>
								</tr><?
								$liRowIdx ++;
							}
						}
					}else {
						$rowCnt = sizeof($row['svcList']);

						for($i=0; $i<$rowCnt; $i++){
							if ($mode == 1){?>
								<tr id="ID_ROW" setYn="N"><?
							}else{?>
								<tr><?
							}
							if ($i == 0){?>
								<td class="center" rowspan="<?=$rowCnt;?>"><?=$row['no'];?></td>
								<td class="left" rowspan="<?=$rowCnt;?>"><a href="#" onclick="return showClient('<?=$ed->en($row['jumin']);?>');"><?=$row['name'];?></a></td><?
							}?>
							<td class="left"><?=$row['svcList'][$i]['svcNm'];?></td><?
							if ($mode == 6){
								if($sr == 'R'){ ?>
									<td class="center"><div class="nowrap left" style="width:180px;"><?=$row['orgnm'];?></div></td><?
								}
							}else{?>
								<td class="center"><?=$row['svcList'][$i]['lvlNm'];?></td>
								<td class="center"><?=$row['svcList'][$i]['kindNm'];?></td>
								<td class="left"><a href="#" onclick="return showMember('<?=$ed->en($row['svcList'][$i]['memCd']);?>');"><?=$row['svcList'][$i]['memNm'];?></a></td><?
							}?>
							<td class="left last"><?
								for($j=1; $j<=12; $j++){
									$class = 'my_month ';
									$mon   = ($j<10?'0':'').$j;
									$text  = $j.'월';
									$lsId  = 'lo'.$row['svcList'][$i]['svcCd'].'_'.$liRowIdx.'_'.$j;

									if ($j == 12){
										$style = 'float:left;';
									}else{
										$style = 'float:left; margin-right:2px;';
									}

									if ($row['svcList'][$i]['period'][$j] > 0){
										if ($lbPlanMode){
											$link = 'lfShowIljung(\''.$lsId.'\',\''.$code.'\',\''.$year.'\',\''.$mon.'\',\''.$row['svcList'][$i]['svcCd'].'\',\''.$ed->en($row['jumin']).'\',\''.$row['key'].'\');';
										}else{
											$link  = '_setSugupjaReg(\''.$code.'\',\''.$row['svcList'][$i]['svcCd'].'\',\''.$row['key'].'\',$(\'#year\').val(),\''.$mon.'\',true);';
										}

										$color = 'color:#000000; cursor:pointer;';

										if ($iljung[$row['jumin']][$row['svcList'][$i]['svcCd']]['mon'.$mon] > 0){
											$class .= 'my_month_y';
										}else{
											$class .= 'my_month_1';
										}
									}else{
										$link   = '';
										$color  = 'color:#cccccc; cursor:default;';
										$class .= 'my_month_1';
									}

									$style .= $color;?>
									<div id="<?=$lsId;?>" class="<?=$class;?>" style="<?=$style;?>" onclick="<?=$link;?>">
										<div style="position:absolute;"><?=$text;?></div>
										<div style="clear:both;">
											<div style="float:left; width:11px; height:5px; <?=($sub[$row['jumin'].'_'.$year.$mon.'_200']['subcd'] == '200' ? 'background:url(../image/bg_cal_g.gif) no-repeat;' : '');?>"></div>
											<div style="float:left; width:11px; height:5px; <?=($sub[$row['jumin'].'_'.$year.$mon.'_500']['subcd'] == '500' ? 'background:url(../image/bg_cal_b.gif) no-repeat;' : '');?>"></div>
											<div style="float:left; width:11px; height:5px; <?=($sub[$row['jumin'].'_'.$year.$mon.'_800']['subcd'] == '800' ? 'background:url(../image/bg_cal_r.gif) no-repeat;' : '');?>"></div>
										</div>
									</div><?
								}?>
							</td>
							</tr><?
							$liRowIdx ++;
						}
					}
				}
			}
		}else if ($mode == 3){
			$sql = 'select his.jumin
					,      mst.nm
					,      mst.c_key
					,      his.svc_cd
					,      date_format(his.from_dt,\'%Y%m\') as from_dt
					,      date_format(his.to_dt,\'%Y%m\') as to_dt
					  from (
						   select jumin
						   ,      svc_cd
						   ,      from_dt
						   ,      to_dt
							 from client_his_svc as his
							where org_no           = \''.$code.'\'
							  and left(from_dt,4) <= \''.$year.'\'
							  and left(to_dt,4)   >= \''.$year.'\'';

			if ($find_kind != 'all'){
				$sql .= ' and svc_cd = \''.$find_kind.'\'';
			}else{
				$sql .= ' and svc_cd >= \'1\'
						  and svc_cd <= \'4\'';
			}

			if ($stat_gbn != 'all'){
				$sql .= ' and svc_stat = \''.$stat_gbn.'\'';
			}

			$sql .= '	   ) as his
					 inner join (
						   select min(m03_mkind) as kind
						   ,      m03_jumin as jumin
						   ,      m03_name as nm
						   ,      m03_key as c_key
							 from m03sugupja
							where m03_ccode = \''.$code.'\'';


			$sql .= '		group by m03_jumin) as mst
						on mst.jumin = his.jumin
					 order by nm, jumin, svc_cd';


			$conn->query($sql);
			$conn->fetch();

			$rowCount = $conn->row_count();

			$no = 1;
			for($i=0; $i<$rowCount; $i++){
				$row = $conn->select_row($i);

				if ($tmpKey != $row['jumin']){
					$tmpKey  = $row['jumin'];
					$idx1 = sizeof($data);

					$data[$idx1] = array(
						'no'	=>$no
					,	'jumin'	=>$row['jumin']
					,	'key'	=>$row['c_key']
					,	'name'	=>$row['nm']
					);

					$no ++;
				}

				if ($tmpSvc != $tmpKey.'_'.$row['svc_cd']){
					$tmpSvc  = $tmpKey.'_'.$row['svc_cd'];
					$idx2 = sizeof($data[$idx1]['svcList']);

					$data[$idx1]['svcList'][$idx2] = array(
						'svcCd'	=>$row['svc_cd']
					,	'svcNm'	=>$conn->kind_name_svc($row['svc_cd'])
					,	'period'=>array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0,12=>0)
					);
				}

				for($j=1; $j<=12; $j++){
					$mon = ($j < 10 ? '0' : '').$j;

					if ($row['from_dt'] <= $year.$mon && $row['to_dt'] >= $year.$mon)
						$data[$idx1]['svcList'][$idx2]['period'][$j] = 1;
				}
			}

			$conn->row_free();

			if (Is_Array($data)){
				foreach($data as $row){
					$rowCnt = sizeof($row['svcList']);

					for($i=0; $i<$rowCnt; $i++){?>
						<tr><?
							if ($i == 0){?>
								<td class="center" rowspan="<?=$rowCnt;?>"><?=$row['no'];?></td>
								<td class="left" rowspan="<?=$rowCnt;?>"><?=$row['name'];?></td><?
							}?>
							<td class="left"><?=$row['svcList'][$i]['svcNm'];?></td>
							<td class="left last"><?
								for($j=1; $j<=12; $j++){
									$class = 'my_month ';
									$mon   = ($j<10?'0':'').$j;
									$text  = $j.'월';

									if ($j == 12){
										$style = 'float:left;';
									}else{
										$style = 'float:left; margin-right:2px;';
									}

									if ($row['svcList'][$i]['period'][$j] > 0){
										//$link  = '_voucher_make(\''.$code.'\',\''.$row['svcCd'].'\',\''.$ed->en($row['jumin']).'\',\''.$row['key'].'\',$(\'#year\').val(),\''.$mon.'\',\''.(!empty($iljung[$row['jumin']]['mon'.$mon]) ? $iljung[$row['jumin']]['mon'.$mon] : 0).'\');';
										$link  = '_iljungMakeVoucher(\'make\',\''.$code.'\',\''.$row['svcList'][$i]['svcCd'].'\',\''.$ed->en($row['jumin']).'\',\''.$year.'\',\''.$mon.'\');';
										$color = 'color:#000000; cursor:pointer;';

										if ($iljung[$row['jumin']]['mon'.$mon] > 0){
											$class .= 'my_month_y';
										}else{
											$class .= 'my_month_1';
										}
									}else{
										$link   = '';
										$color  = 'color:#cccccc; cursor:default;';
										$class .= 'my_month_1';
									}

									$style .= $color;?>
									<div class="<?=$class;?>" style="<?=$style;?>" onclick="<?=$link;?>"><?=$text;?></div><?
								}?>
							</td>
						</tr><?
					}
				}
			}
		}else if ($mode == 5){
			$sql = 'select his.jumin
					,      mst.nm
					,      mst.mem_cd
					,      case his.svc_cd when mst.kind then mst.mem_nm else \'\' end as mem_nm
					,      mst.c_key
					,      case his.svc_cd when \'0\' then case mst.lvl1 when \'9\' then \'일반\' else concat(mst.lvl1,\'등급\') end
										   when \'4\' then concat(mst.lvl2,\'등급\') else \'\' end as ylvl
					,		case his.svc_cd when \'0\' then case mst.skind when \'1\' then \'일반\' when \'2\' then \'의료\' when \'3\' then \'기초\' when \'4\' then \'경감\' else \'\' end else \'\' end as kindNm
					,	   mst.rate as rate
					,      his.svc_cd as svc_cd
					,      date_format(his.from_dt,\'%Y%m\') as from_dt
					,      date_format(his.to_dt,\'%Y%m\') as to_dt
					  from (
						   select jumin
						   ,      svc_cd
						   ,      from_dt
						   ,      to_dt
							 from client_his_svc as his
							where org_no           = \''.$code.'\'
							  and left(from_dt,4) <= \''.$year.'\'
							  and left(to_dt,4)   >= \''.$year.'\'';

			if ($find_kind != 'all'){
				$sql .= ' and svc_cd = \''.$find_kind.'\'';
			}

			$sql .= '	   ) as his
					 inner join (
						   select min(m03_mkind) as kind
						   ,      m03_jumin as jumin
						   ,      m03_name as nm
						   ,      m03_yoyangsa1 as mem_cd
						   ,      m03_yoyangsa1_nm as mem_nm
						   ,      m03_key as c_key
						   ,      lvl.level as lvl1
						   ,      dis.svc_lvl as lvl2
						   ,	  skind.skind as skind
						   ,	  skind.rate as rate
							 from m03sugupja
							 left join (
								  select jumin
								  ,      svc_cd
								  ,      level
								  ,      from_dt
								  ,      to_dt
									from client_his_lvl
								   where org_no = \''.$code.'\'';

			if ($find_kind != 'all')
				$sql .= ' and svc_cd = \''.$find_kind.'\'';

			$sql .= '			   order by jumin, from_dt desc, to_dt desc
								  ) as lvl
							   on lvl.jumin = m03_jumin
							 left join (
									select jumin
									, kind as skind
									, rate
									, from_dt
									, to_dt
									from client_his_kind
									where org_no = \''.$code.'\'
									and date_format(now(),\'%Y%m%d\') >= date_format(from_dt,\'%Y%m%d\')
									and date_format(now(),\'%Y%m%d\') <= date_format(to_dt,  \'%Y%m%d\')
									GROUP BY jumin
									order by jumin, from_dt desc, to_dt desc
									) as skind
									on skind.jumin = m03_jumin
							 left join (
								  select jumin
								  ,      svc_val
								  ,      svc_lvl
								  ,      from_dt
								  ,      to_dt
									from client_his_dis
								   where org_no = \''.$code.'\'
								  ) as dis
							   on dis.jumin = m03_jumin
							where m03_ccode = \''.$code.'\'';

			if ($find_sugupja != '')
				$sql .= ' and m03_name >= \''.$find_sugupja.'\'';

			$sql .= '		group by m03_jumin) as mst
						on mst.jumin = his.jumin
					 order by nm, jumin, svc_cd';
			
			
			$conn->query($sql);
			$conn->fetch();

			$rowCount = $conn->row_count();

			$no = 1;
			for($i=0; $i<$rowCount; $i++){
				$row = $conn->select_row($i);

				if ($tmpKey != $row['jumin']){
					$tmpKey  = $row['jumin'];
					$idx1 = sizeof($data);

					$data[$idx1] = array(
						'no'	=>$no
					,	'jumin'	=>$row['jumin']
					,	'key'	=>$row['c_key']
					,	'name'	=>$row['nm']
					);

					$no ++;
				}

				if ($tmpSvc != $tmpKey.'_'.$row['svc_cd']){
					$tmpSvc  = $tmpKey.'_'.$row['svc_cd'];

					if ($iljung[$row['jumin']][$row['svc_cd']]['cnt'] > 0){
						$idx2 = sizeof($data[$idx1]['svcList']);

						//재가요양 및 장애인활동지원만 등급을 포시한다.
						if ($row['svc_cd'] == 0 || $row['svc_cd'] == 4){
							$lvl = $row['ylvl'];
							if($row['svc_cd'] == 0 && $row['kindNm']){
								$sKind = $row['kindNm'].'('.$row['rate'].')';
							}else {
								$sKind = '';
							}
						}else {
							$lvl = '';
							$sKind = '';
						}

						$data[$idx1]['svcList'][$idx2] = array(
							'memNm' =>$row['mem_nm']
						,	'memCd' =>$ed->en($row['mem_cd'])
						,	'lvlNm'	=>$lvl
						,	'kindNm'=>$sKind
						,	'svcCd'	=>$row['svc_cd']
						,	'svcNm'	=>$conn->kind_name_svc($row['svc_cd'])
						,	'period'=>array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0,12=>0)
						);
					}
				}

				for($j=1; $j<=12; $j++){
					$mon = ($j < 10 ? '0' : '').$j;

					if ($row['from_dt'] <= $year.$mon && $row['to_dt'] >= $year.$mon){
						if ($row['svc_cd'] >= 1 && $row['svc_cd'] <= 4){
							//바우처 생된될 월만 등록가능
							$data[$idx1]['svcList'][$idx2]['period'][$j] = 1;
						}else{
							//재가요양 및 기타유료는 계약기간내 모두 등록가능
							$data[$idx1]['svcList'][$idx2]['period'][$j] = 1;
						}
					}
				}
			}

			$conn->row_free();

			$liRowIdx = 0;
			if (is_array($data)){
				foreach($data as $row){
					$rowCnt = sizeof($row['svcList']);

					if (is_array($iljung[$row['jumin']])){
						for($i=0; $i<$rowCnt; $i++){?>
							<tr><?
								if ($i == 0){?>
									<td class="center" rowspan="<?=$rowCnt;?>"><?=$row['no'];?></td>
									<td class="left" rowspan="<?=$rowCnt;?>"><a href="#" onclick="return showClient('<?=$ed->en($row['jumin']);?>');"><?=$row['name'];?></a></td><?
								}?>
								<td class="left"><?=$row['svcList'][$i]['svcNm'];?></td>
								<td class="center"><?=$row['svcList'][$i]['lvlNm'];?></td>
								<td class="center"><?=$row['svcList'][$i]['kindNm'];?></td>
								<td class="left"><a href="#" onclick="return showMember('<?=$ed->en($row['svcList'][$i]['memCd']);?>');"><?=$row['svcList'][$i]['memNm'];?></a></td>
								<td class="left last"><?
									for($j=1; $j<=12; $j++){
										$class = 'my_month ';
										$mon   = ($j<10?'0':'').$j;
										$text  = $j.'월';
										$lsId  = 'lo'.$row['svcList'][$i]['svcCd'].'_'.$liRowIdx.'_'.$j;

										if ($j == 12){
											$style = 'float:left;';
										}else{
											$style = 'float:left; margin-right:2px;';
										}

										if ($iljung[$row['jumin']][$row['svcList'][$i]['svcCd']]['mon'.$mon] > 0){
											$link   = 'lfShowConf(\''.$year.'\',\''.$mon.'\',\''.$ed->en($row['jumin']).'\',\''.$row['svcList'][$i]['svcCd'].'\');';
											$color  = 'color:#000000; cursor:pointer;';
											$class .= 'my_month_y';
										}else{
											$link   = '';
											$color  = 'color:#cccccc; cursor:default;';
											$class .= 'my_month_1';
										}

										$style .= $color;?>
										<div id="<?=$lsId;?>" class="<?=$class;?>" style="<?=$style;?>" onclick="<?=$link;?>"><?=$text;?></div><?
									}?>
								</td>
							</tr><?
							$liRowIdx ++;
						}
					}
				}
			}
		}else if ($mode == 7){
			$sql = 'SELECT	m02_yjumin AS jumin
					,		m02_yname AS name
					,		DATE_FORMAT(MIN(mem_his.join_dt),\'%Y%m\') AS from_dt
					,		DATE_FORMAT(MAX(IFNULL(mem_his.quit_dt,\'9999-12-31\')),\'%Y%m\') AS to_dt
					FROM	m02yoyangsa
					INNER	JOIN mem_option
							ON   mem_option.org_no = m02_ccode
							AND  mem_option.mo_jumin = m02_yjumin
							AND  mem_option.counsel_yn = \'Y\'
					INNER	JOIN mem_his
							ON mem_his.org_no = m02_ccode
							AND mem_his.jumin = m02_yjumin
							AND LEFT(mem_his.join_dt,4) <= \''.$year.'\'
							AND LEFT(IFNULL(mem_his.quit_dt,\'99991231\'),4) >= \''.$year.'\'
					WHERE	m02_ccode =\''.$code.'\'';

			if ($find_sugupja){
				$sql .= ' AND m02_yname LIKE \''.$find_sugupja.'%\'';
			}

			$sql .= '	GROUP	BY m02_yjumin,m02_yname
						ORDER	BY name';

			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count();

			$no = 1;

			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);?>
				<tr onmouseover="this.style.backgroundColor='#efefef';" onmouseout="this.style.backgroundColor='#ffffff';">
					<td class="center"><?=$no;?></td>
					<td class="center"><div class="left"><?=$row['name'];?></div></td>
					<td class="center last"><?
						for($j=1; $j<=12; $j++){
							$class = 'my_month ';
							$mon = ($j<10?'0':'').$j;
							$text = $j.'월';
							$lsId = 'lo'.$no.'_'.$j;

							$style = 'float:left; margin-left:3px;';

							if ($iljung[$row['jumin']]['m'.$j] > 0){
								$class .= 'my_month_y';
							}else{
								$class .= 'my_month_1';
							}

							if ($year.$mon >= $row['from_dt'] &&
								$year.$mon <= $row['to_dt']){
								$color = 'color:#000000; cursor:pointer;';
								$link = 'lfCareIljung(\''.$ed->en($row['jumin']).'\',\''.$year.'\',\''.$mon.'\',\''.$sr.'\');';
							}else{
								$color = 'color:#cccccc; cursor:default;';
								$link = '';
							}

							$style .= $color;?>
							<div id="<?=$lsId;?>" class="<?=$class;?>" style="<?=$style;?>" onclick="<?=$link;?>"><?=$text;?></div><?
						}?>
					</td>
				</tr><?

				$no ++;
			}

			$conn->row_free();
		}
	?>
	</tbody><? 
	if(($mode==1 || $mode==4 || $mode==6) && $newPageList){
	}else { ?>
		<tfoot>
			<tr>
				<td class="left last bottom" colspan="5"></td>
			</tr>
		</tfoot><?
	} ?>
</table>
<? if(($mode==1 || $mode==4 || $mode==6) && $newPageList){ ?>
<div style="text-align:left;">
	<div style="position:absolute; width:auto; padding-left:10px;">검색된 전체 갯수 : <?=number_format($total_count);?></div>
	<div style="width:100%; text-align:center;">
	<?
		$paging = new YsPaging($params);
		$paging->printPaging();
	?>
	</div>
</div>
<? } ?>
<input id="code" name="code" type="hidden" value="<?=$code;?>">
<input id="lbTestMode" name="lbTestMode" type="hidden" value="<?=$lbTestMode;?>">
<input id="page" name="page" type="hidden" value="<?=$page;?>">
</form>
<?
	$con2->close();
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>