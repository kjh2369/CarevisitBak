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
	$from	= $_POST['from'];
	$to		= $_POST['to'];
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:text[name="txtTime"]').unbind('keyup').keyup(function(){
			if ($(this).val().length == $(this).attr('maxlength')){
				if ($(this).attr('id') == 'txtFromH'){
					$('#txtFromM').focus();
				}else if ($(this).attr('id') == 'txtFromM'){
					$('#txtToH').focus();
				}else if ($(this).attr('id') == 'txtToH'){
					$('#txtToM').focus();
				}else if ($(this).attr('id') == 'txtToM'){
				}
			}
		});

		$('input:radio[name="optTime"]').unbind('click').bind('click',function(){
			var time = $(this).val();

			$('#txtFromH').val(time.substr(0,2));
			$('#txtFromM').val(time.substr(2,2));
		});
	});

	function lfResourceFind(){
		var h = 400;
		var w = 600;
		var t = (screen.availHeight - h) / 2;
		var l = (screen.availWidth - w) / 2;

		var url = '../care/care_resource_find.php';
		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
		var win = window.open('about:blank', 'FIND_RESOURCE', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'sugaCd':$('#suga').val() //'<?=$suga;?>'
			,	'year'	:'<?=$year;?>'
			,	'month'	:'<?=$month;?>'
			,	'sr'	:'<?=$sr;?>'
			,	'return':'lfResourceFindResult'
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

		form.setAttribute('target', 'FIND_RESOURCE');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfResourceFindResult(cd,cost,nm,per){
		if (!cd){
			alert('자원코드가 없습니다.\n관리자에게 문의하여 주십시오.');
			return;
		}
		$('#lblResource').attr('code',cd).attr('cost',cost).text(nm);
		$('#lblSugaCost').text(__num2str(cost));
		$('#txtFromH').focus();
	}

	function lfMemberFind(){
		var h = 400;
		var w = 600;
		var t = (screen.availHeight - h) / 2;
		var l = (screen.availWidth - w) / 2;

		var url    = '../inc/_find_person.php';
		var option = 'left='+l+', top='+t+', width='+w+',height='+h+',scrollbars=no,status=no,resizable=no';
		var win    = window.open('about:blank', 'FIND_MEMBER', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'type'	:'member'
			,	'code'	:'<?=$code;?>'
			,	'kind'	:'<?=$sr;?>'
			,	'return':'lfMemFindResult'
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

		form.setAttribute('target', 'FIND_MEMBER');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfMemFindResult(obj){
		var col = __parseStr(obj);

		$('#lblMemName').attr('jumin',col['jumin']).text(col['name']);
	}
</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="20px">
		<col width="130px">
		<col width="65px">
		<col width="20px">
		<col width="130px">
		<col width="65px">
		<!--
		<col width="120px">
		<col width="40px">
		-->
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center bottom">자원</th>
			<td class="bottom last"><span class="btn_pack find" style="margin-left:2px; margin-top:1px;" onclick="lfResourceFind(); return false;"></span></td>
			<td class="bottom"><span id="lblResource" code="" cost=""></span></td>
			<th class="bottom center">담당직원</th>
			<td class="bottom last"><span class="btn_pack find" style="margin-left:2px; margin-top:1px;" onclick="lfMemberFind(); return false;"></span></td>
			<td class="bottom left"><span id="lblMemName" jumin=""></span></td>
			<th class="bottom center">시작시간</th>
			<td class="bottom last"><? 
					
					//시간으로 표시 여푸
					$sql = 'select kacold_time_yn
							from   center_comm
							where  org_no = \''.$code.'\'';
					$kacoldTimeYn = $conn->get_data($sql);
					
					if($kacoldTimeYn!='Y'){ ?>
						<label><input id="optTimeFrom" name="optTime" type="radio" class="radio" value="1000" checked>오전</label>
						<label><input id="optTimeTo" name="optTime" type="radio" class="radio" value="1300">오후</label>
						<input id="txtFromH" name="txtTime" type="text" value="10" style="display:none;">
						<input id="txtFromM" name="txtTime" type="text" value="00" style="display:none;"><?
					}else { ?>
						<input id="txtFromH" name="txtTime" type="text" value="" class="no_string" maxlength="2" style="width:25px; text-align:center;"> :
						<input id="txtFromM" name="txtTime" type="text" value="" class="no_string" maxlength="2" style="width:25px; text-align:center;"><?
					} ?>
			</td>
			<!--
			<th class="bottom center">수가</th>
			<td class="left bottom last"><span id="lblSugaCost">0</span></td>
			-->
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_db_close.php');
?>