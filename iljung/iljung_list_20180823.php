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
	 * mode ����
	 * 1 : �湮�������
	 * 2 : �湮������ȸ
	 * 3 : �ٿ�ó�����������
	 * 4 : ����ȸ��������ȸ
	 * 5 : ��������Ʈ ��ȸ
	 * 6 : �簡���� ��ȸ
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
		$title = '�湮����(������)';
	}else if ($mode == 3){
		$title = '�ٿ�ó�����������';
	}else if ($mode == 4){
		$title = '�湮������ȸ';
	}else if ($mode == 5){
		$title = '������ȸ(����)';
	}else if ($mode == 6){
		$title = '��������(';

		if ($sr == 'S'){
			$title .= '�簡����';
		}else{
			$title .= '�ڿ�����';
		}

		$title .= ')';
	}else if ($mode == 7){
		$title = '�������(';

		if ($sr == 'S'){
			$title .= '�簡����';
		}else{
			$title .= '�ڿ�����';
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

	//���ļ���
	$orderBy = $_POST['optOrder'];

	if (!$orderBy) $orderBy = '1';
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


function search(){
	if ('<?=$mode;?>' == '6' || '<?=$mode;?>' == '7'){
	}else{
		__setCookie('statGbn', $('#cboStat').val(), 30);
	}
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

	if (!confirm(year+'�� '+month+'�� ������ ������ ������ ���� �ֱ��� ������ �����մϴ�.\n\n        ******    �����Ͻ÷��� "Ȯ��"�� Ŭ���Ͽ� �ֽʽÿ�.    ******\n\n��, �̹� ������ ������ ���� ��� �ٿ�ó������ �������� �ʽ��ϴ�.\n\n')) return;

	var code = document.f.code.value;

	f.action = 'iljung_voucher_make_auto.php?code='+code+'&year='+year+'&month='+month;
	f.submit();
}

function voucherCopy(month){
	/*
	var year = document.f.year.value;

	month = (parseInt(month, 10) < 10 ? '0' : '') + parseInt(month, 10);

	if (!confirm(year+'�� '+month+'�� ������ ������ ������ ���� �ֱ��� ������ �����մϴ�.\n\n        ******    �����Ͻ÷��� "Ȯ��"�� Ŭ���Ͽ� �ֽʽÿ�.    ******\n\n��, �̹� ������ ������ ���� ��� �ٿ�ó������ �������� �ʽ��ϴ�.\n\n')) return;

	var code = document.f.code.value;

	f.action = 'iljung_voucher_make_auto.php?code='+code+'&year='+year+'&month='+month;
	f.submit();
	*/

	if ($('input:checkbox[name="chkSvc[]"]:checked').length == 0){
		alert('������ �ٿ�ó("����")�� �����Ͽ� �ֽʽÿ�.');
		return;
	}

	var year = document.f.year.value;

	month = (parseInt(month, 10) < 10 ? '0' : '') + parseInt(month, 10);

	var gbn = $('input:radio[name="optMakeGbn"]:checked').val();
	var str = '';

	if (gbn == '1'){
		str = '������ ������ �����մϴ�.';
	}else{
		str = year+'�� '+month+'�� ������ ������ ������ ���� �ֱ��� ������ �����մϴ�.';
	}

	str += '\n\n        ******    �����Ͻ÷��� "Ȯ��"�� Ŭ���Ͽ� �ֽʽÿ�.    ******\n\n��, �̹� ������ ������ ���� ��� �ٿ�ó������ �������� �ʽ��ϴ�.\n\n';

	if (!confirm(str)) return;


	//if (!confirm(year+'�� '+month+'�� ������ ������ ������ ���� �ֱ��� ������ �����մϴ�.\n\n        ******    �����Ͻ÷��� "Ȯ��"�� Ŭ���Ͽ� �ֽʽÿ�.    ******\n\n��, �̹� ������ ������ ���� ��� �ٿ�ó������ �������� �ʽ��ϴ�.\n\n')) return;


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
//-->
</script>
<form name="f" method="post">
<div class="title" style="width:auto; float:left;"><?=$title;?></div>

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
			<th class="center">�⵵</th>
			<td>
				<select id="year" name="year" style="width:auto;"><?
				for($i=$year_min_max[0]; $i<=$year_min_max[1]; $i++){?>
					<option value="<?=$i;?>"<? if($i == $year){echo "selected";}?>><?=$i;?></option><?
				}?>
				</select>��
			</td><?
			if ($mode != 5 &&
				$mode != 7){?>
				<th>���޻���</th>
				<td>
					<select id="cboStat" name="stat_gbn" style="width:auto;">
						<!--option value="all" <?if($stat_gbn == 'all'){?>selected<?}?>>��ü</option-->
						<option value="1" <?if($stat_gbn == '1'){?>selected<?}?>>�̿�</option>
						<option value="9" <?if($stat_gbn == '9'){?>selected<?}?>>����</option>
					</select>
				</td><?
			}

			if ($mode != 7 && $mode != 6){?>
				<th>����</th>
				<td><?
					if($mode == 3){
						$kind_list = $conn->kind_list($code);
					}else {
						$kind_list = $conn->kind_list($code, $gHostSvc['voucher']);
					}

					echo '<select name=\'find_kind\' style=\'width:auto;\'>';
					echo '<option value=\'all\'>��ü</option>';

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
				<th>������</th><?
			}else if ($mode == 6){?>
				<th>�����</th><?
			}else{?>
				<th>�����ڸ�</th><?
			}?>
			<td>
				<input name="find_sugupja" type="text" value="<?=$find_sugupja;?>" maxlength="20" style="width:100%; ime-mode:active;" onFocus="this.select();">
			</td>
			<td class="last" style="line-height:26px; padding-left:5px; vertical-align:top; padding-top:2px;"><?
				if ($mode == 1){?>
					<!--div style="float:right; width:auto;">
						<input id="ver1" name="ver" type="radio" value="1" class="radio" checked><label for="ver1">��</label>
						<input id="ver2" name="ver" type="radio" value="2" class="radio"><label for="ver2">��</label>
					</div--><?
				}?>
				<div style="float:left; width:auto;">
					<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="search();">��ȸ</button></span>
				</div>
			</td>
		</tr><?
		if ($mode == 6){
			if($sr == 'R'){ ?>
				<tr>
					<th class="center">����</th>
					<td class="last" colspan="6">
						<label><input id="optOrder1" name="optOrder" type="radio" class="radio" value="1" onclick="search();" <?=($orderBy == '1' ? 'checked' : '');?>>�����ڼ�</label>
						<label><input id="optOrder2" name="optOrder" type="radio" class="radio" value="2" onclick="search();" <?=($orderBy == '2' ? 'checked' : '');?>>�Ҽӱ����</label>
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
					<th rowspan="2">�ٿ�ó���� ���� ����</th>
					<td class="last bottom" style="padding-left:2px;" colspan="2">
					<?
						echo '<div>
								<label><input name="optMakeGbn" type="radio" class="radio" value="1" checked>���� �������� ����</label>
								<label><input name="optMakeGbn" type="radio" class="radio" value="2">�ֱ� �������� ����</label>
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
							echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<input id="chkReVou" name="chkReVou" type="checkbox" value="Y" class="checkbox"><label for="chkReVou">�����</label>';
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

							#$text = '<a href="#" onclick="copy_voucher('.$i.');">'.$i.'��</a>';
							$text = '<a href="#" onclick="voucherCopy('.$i.');">'.$i.'��</a>';

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
						�عٿ�ó�� �����Ǿ� �ִٸ� �������� �ʽ��ϴ�.<br>
						���̿��ð��� �����ð����� ���� ������� �������� �ʽ��ϴ�.<br>
					</td>
				</tr>
			</tbody>
		</table><?
	}
?>

<table class="my_table" style="width:100%; border-bottom:none;">
	<colgroup>
		<col width="50px">
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
			<col width="70px">
			<col width="100px"><?
		}?>
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th><?

			if ($mode == 7){?>
				<th class="head">������</th><?
			}else if ($mode == 6){?>
				<th class="head">�����</th>
				<th class="head">��������</th><?
			}else{?>
				<th class="head">�����ڸ�</th>
				<th class="head">��������</th><?
			}

			if ($mode == 3 ||
				$mode == 7){
			}else if ($mode == 6){
				if($sr == 'R'){?>
					<th class="head">�Ҽӱ��</th><?
				}
			}else{?>
				<th class="head">���</th>
				<th class="head">��纸ȣ��</th><?
			}?>
			<th class="head last">��������</th>
		</tr>
	</thead>
	<tbody>
	<?
		/**************************************************

			��������Ʈ

		**************************************************/
		if ($mode == 1 || $mode == 2 || $mode == 4 || $mode == 6){
			//��ȹ
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
			//�ٿ�ó
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
			//����
			if ($code == '31138000044' /*����*/||
				$code == '31174000065' /*�����帲�湮��缾��*/){
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
			//�簡����(���)
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
			//������ ������ ����
			$sql = 'SELECT	CONCAT(jumin, \'_\', svc_cd) AS cd, svc_stat
					FROM	client_his_svc
					WHERE	org_no = \''.$code.'\'
					AND		LEFT(from_dt,4) <= \''.$year.'\'
					AND		LEFT(to_dt,4) >= \''.$year.'\'
					ORDER	BY jumin, from_dt, to_dt';

			$tgtLastStat = $conn->_fetch_array($sql,'cd');
		}

		if ($mode == 1 || $mode == 4 || $mode == 6){
			//����ߺ�ȣ�� ����Ʈ
			$sql = 'select	concat(m03_jumin,\'_\',m03_mkind) as cd
					,		m03_yoyangsa1 as mem_cd
					,		m03_yoyangsa1_nm as mem_nm
					from	m03sugupja
					where	m03_ccode = \''.$code.'\'
					and		ifnull(m03_yoyangsa1,\'\') != \'\'';
			$personList = $conn->_fetch_array($sql,'cd');



			//�Ǻ� ���ε� �α�
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

			//��������Ʈ
			$sql = 'select his.jumin
					,      mst.nm
					,      mst.mem_cd
					,      case his.svc_cd when mst.kind then mst.mem_nm else \'\' end as mem_nm
					,      mst.c_key
					,      case his.svc_cd when \'0\' then case mst.lvl1 when \'9\' then \'�Ϲ�\' else concat(mst.lvl1,\'���\') end
										   when \'4\' then concat(mst.lvl2,\'���\') else \'\' end as ylvl
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

				//�̿�, ����
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

					//�簡��� �� �����Ȱ�������� ����� �����Ѵ�.
					if ($row['svc_cd'] == 0 || $row['svc_cd'] == 4)
						$lvl = $row['ylvl'];
					else
						$lvl = '';

					$data[$idx1]['svcList'][$idx2] = array(
						'memNm' =>$personList[$row['jumin'].'_'.$row['svc_cd']]['mem_nm'] //$row['mem_nm']
					,	'memCd' =>$personList[$row['jumin'].'_'.$row['svc_cd']]['mem_cd'] //$ed->en($row['mem_cd'])
					,	'lvlNm'	=>$lvl
					,	'svcCd'	=>$row['svc_cd']
					,	'svcNm'	=>$conn->kind_name_svc($row['svc_cd'])
					,	'period'=>array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0,12=>0)
					);
				}

				for($j=1; $j<=12; $j++){
					$mon = ($j < 10 ? '0' : '').$j;

					if ($row['from_dt'] <= $year.$mon && $row['to_dt'] >= $year.$mon){
						if ($row['svc_cd'] >= 1 && $row['svc_cd'] <= 4){
							//�ٿ�ó ���ȵ� ���� ��ϰ���
							#if ($laMakeVou[$row['jumin']][$row['svc_cd']][$j] == 'Y'){
								$data[$idx1]['svcList'][$idx2]['period'][$j] = 1;
							#}
						}else{
							//�簡��� �� ��Ÿ����� ���Ⱓ�� ��� ��ϰ���
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
							<td class="left"><a href="#" onclick="return showMember('<?=$ed->en($row['svcList'][$i]['memCd']);?>');"><?=$row['svcList'][$i]['memNm'];?></a></td><?
						}?>
						<td class="left last"><?
							for($j=1; $j<=12; $j++){
								$class = 'my_month ';
								$mon   = ($j<10?'0':'').$j;
								$text  = $j.'��';
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
										<div id="divCare_<?=$lsId;?>" style="float:left; width:11px; height:5px; <?=($liLCHis[$row['jumin']][$year.$mon]['care'] == 'Y' ? 'background:url(../image/bg_cal_g.gif) no-repeat;' : '');?>"></div>
										<div id="divBath_<?=$lsId;?>" style="float:left; width:11px; height:5px; <?=($liLCHis[$row['jumin']][$year.$mon]['bath'] == 'Y' ? 'background:url(../image/bg_cal_b.gif) no-repeat;' : '');?>"></div>
										<div id="divNurs_<?=$lsId;?>" style="float:left; width:11px; height:5px; <?=($liLCHis[$row['jumin']][$year.$mon]['nurs'] == 'Y' ? 'background:url(../image/bg_cal_r.gif) no-repeat;' : '');?>"></div>
									</div>
								</div><?
							}?>
						</td>
						</tr><?
						$liRowIdx ++;
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
									$text  = $j.'��';

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
					,      case his.svc_cd when \'0\' then case mst.lvl1 when \'9\' then \'�Ϲ�\' else concat(mst.lvl1,\'���\') end
										   when \'4\' then concat(mst.lvl2,\'���\') else \'\' end as ylvl
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

						//�簡��� �� �����Ȱ�������� ����� �����Ѵ�.
						if ($row['svc_cd'] == 0 || $row['svc_cd'] == 4)
							$lvl = $row['ylvl'];
						else
							$lvl = '';

						$data[$idx1]['svcList'][$idx2] = array(
							'memNm' =>$row['mem_nm']
						,	'memCd' =>$ed->en($row['mem_cd'])
						,	'lvlNm'	=>$lvl
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
							//�ٿ�ó ���ȵ� ���� ��ϰ���
							$data[$idx1]['svcList'][$idx2]['period'][$j] = 1;
						}else{
							//�簡��� �� ��Ÿ����� ���Ⱓ�� ��� ��ϰ���
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
								<td class="left"><a href="#" onclick="return showMember('<?=$ed->en($row['svcList'][$i]['memCd']);?>');"><?=$row['svcList'][$i]['memNm'];?></a></td>
								<td class="left last"><?
									for($j=1; $j<=12; $j++){
										$class = 'my_month ';
										$mon   = ($j<10?'0':'').$j;
										$text  = $j.'��';
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
							$text = $j.'��';
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
	</tbody>
	<tfoot>
		<tr>
			<td class="left last bottom" colspan="5"></td>
		</tr>
	</tfoot>
</table>

<input id="code" name="code" type="hidden" value="<?=$code;?>">
<input id="lbTestMode" name="lbTestMode" type="hidden" value="<?=$lbTestMode;?>">
</form>
<?
	$con2->close();
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>