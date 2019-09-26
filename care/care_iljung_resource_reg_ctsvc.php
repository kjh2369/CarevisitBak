<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$sr		= $_POST['sr'];
	$suga	= $_POST['suga'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];

	//기관명
	$storeNm	= $conn->_storeName($code);

	//서비스명
	$sql = 'SELECT	suga_nm
			FROM	care_suga
			WHERE	org_no  = \''.$code.'\'
			AND		suga_sr = \''.$sr.'\'
			AND		DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
			AND		DATE_FORMAT(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
			AND		CONCAT(suga_cd,suga_sub) = \''.$suga.'\'
			ORDER	BY from_dt DESC
			LIMIT	1';
	$svcNm	= $conn->get_data($sql);

	if ($IsCareYoyAddon){
		//공통수가
		if (!$svcNm){
			$sql = 'SELECT	name
					FROM	care_suga_comm
					WHERE	code = \''.$suga.'\'
					AND		DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
					AND		DATE_FORMAT(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'';

			$svcNm	= $conn->get_data($sql);
		}
	}
?>
<script type="text/javascript">
	//재가관리 서비스 조회
	function lfCareSvcFind(){
		if ($('div[id^="loCal_"][ynSave="N"]').length > 0){
			if (!confirm('저장되지 않은 일정이 있습니다.\n서비스변경시 저장되지 않은 일정은 삭제됩니다.\n서비스를 변경하시겠습니까?')) return;
		}

		var h = 600;
		var w = 800;
		var t = (screen.availHeight - h) / 2;
		var l = (screen.availWidth - w) / 2;

		var url = '../care/care_suga_find.php';
		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
		var win = window.open('about:blank', 'FIND_CARESVC', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'year':'<?=$year;?>'
			,	'month':'<?=$month;?>'
			,	'sr':'<?=$sr;?>'
			,	'return':'lfCareSvcFindResult'
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

		form.setAttribute('target', 'FIND_CARESVC');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfCareSvcFindResult(cd,mstNm,proNm,svcNm,subNm){
		$('#ID_CELL_STR_SVC').text(subNm);
		$('#suga').val(cd);
		$('#lblResource').attr('code','').attr('cost','').text('');
		lfLoadIljung();
	}
</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>기관명</th>
			<td class="last"><div class="left nowrap" style="width:190px;"><?=$storeNm;?></div></td>
		</tr>
		<tr>
			<th class="bottom">서비스명</th>
			<td class="bottom last">
				<div style="float:left; width:auto;">
					<span class="btn_pack find" style="margin-left:5px; margin-top:1px; height:25px;" onclick="lfCareSvcFind(); return false;"></span>
				</div>
				<div class="nowrap" style="float:left; width:auto; padding-top:2px; width:160px;">
					<span id="ID_CELL_STR_SVC"><?=$svcNm;?></span>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_db_close.php');
?>