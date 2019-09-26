<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$svcCd	= $_POST['svcCd'];
	$jumin	= $ed->de($_POST['jumin']);
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$seq	= $_POST['seq'];

	$sql = 'SELECT	m03_name
			FROM	m03sugupja
			WHERE	m03_ccode = \''.$orgNo.'\'
			AND		m03_mkind = \''.($svcCd == 'S' || $svcCd == 'R' ? '6' : $svcCd).'\'
			AND		m03_jumin = \''.$jumin.'\'';

	$name = $conn->get_data($sql);

	if ($seq){
		$evtCls = true;
	}else{
		$evtCls = false;
	}
?>
<script type="text/javascript">
	$(document).ready(function(){
		if ('<?=$evtCls;?>' != '1'){
			IsCalBtnShow = false;
			defaultCalDate = '<?=$year;?>-<?=$month;?>-01';
		}
		lfSearch();

		$('#divList').height(__GetHeight($('#divList')));
	});

	window.onunload = function(){
		if ('<?=$evtCls;?>' == '1'){
			opener.lfSearch();
		}
	}

	function lfSearch(){
		$.ajax({
			type	:'POST'
		,	url		:'./iljung_memo_reg_search.php'
		,	data	:{
				'jumin'	:'<?=$ed->en($jumin);?>'
			,	'year'	:'<?=$year;?>'
			,	'month'	:'<?=$month;?>'
			,	'seq'	:'<?=$seq;?>'
			}
		,	beforeSend	:function(){
			}
		,	success	:function(html){
				$('#tbodyList').html(html);

				if (__str2num('<?=$seq;?>') < 1) lfNew();

				$('.clsData').each(function(){
					__init_object(this);
				});
			}
		,	error:function(request, status, error){
				alert(error);
			}
		});
	}

	function lfNew(){
		var html = '';

		html += '<tr>'
			 +	'<th>작성일자</th>'
			 +	'<td>'
			 +	'<div style="float:left; width:auto;"><input id="txtNewDate" name="txt" type="text" value="" class="date clsObj"></div>'
			 +	'<div style="float:left; width:auto;"><span class="btn_pack m"><button onclick="lfReg();">등록</button></span></div>'
			 +	'</td>'
			 +	'</tr>'
			 +	'<tr>'
			 +	'<th>작성내용</th>'
			 +	'<td><textarea id="txtNewMemo" name="txts" class="clsObj" style="width:100%; height:50px;"></textarea></td>'
			 +	'</tr>';

		if ($('tr', $('#tbodyList')).length > 0){
			$('tr:first', $('#tbodyList')).before(html);
		}else{
			$('#tbodyList').html(html);
		}

		$('.clsObj').each(function(){
			__init_object(this);
		});
	}

	function lfReg(){
		$.ajax({
			type	:'POST'
		,	url		:'./iljung_memo_save.php'
		,	data	:{
				'jumin'	:'<?=$ed->en($jumin);?>'
			,	'year'	:'<?=$year;?>'
			,	'month'	:'<?=$month;?>'
			,	'svcCd'	:'<?=$svcCd;?>'
			,	'date'	:$('#txtNewDate').val()
			,	'memo'	:$('#txtNewMemo').val()
			,	'seq'	:''
			}
		,	beforeSend	:function(){
			}
		,	success	:function(result){
				if (__resultMsg(result)){
					if ('<?=$evtCls;?>' == '1'){
						self.close();
					}else{
						lfSearch();
					}
				}
			}
		,	error:function(request, status, error){
				alert(error);
			}
		});
	}

	function lfModify(seq){
		$.ajax({
			type	:'POST'
		,	url		:'./iljung_memo_save.php'
		,	data	:{
				'jumin'	:'<?=$ed->en($jumin);?>'
			,	'year'	:'<?=$year;?>'
			,	'month'	:'<?=$month;?>'
			,	'svcCd'	:'<?=$svcCd;?>'
			,	'date'	:$('#txtDate_'+seq).val()
			,	'memo'	:$('#txtMemo_'+seq).val()
			,	'seq'	:seq
			}
		,	beforeSend	:function(){
			}
		,	success	:function(result){
				if (__resultMsg(result)){
					if ('<?=$evtCls;?>' == '1'){
						self.close();
					}
				}
			}
		,	error:function(request, status, error){
				alert(error);
			}
		});
	}

	function lfDelete(seq){
		if (!confirm('삭제 후 복구가 불가능합니다.\n정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type	:'POST'
		,	url		:'./iljung_memo_delete.php'
		,	data	:{
				'jumin'	:'<?=$ed->en($jumin);?>'
			,	'year'	:'<?=$year;?>'
			,	'month'	:'<?=$month;?>'
			,	'seq'	:seq
			}
		,	beforeSend	:function(){
			}
		,	success	:function(result){
				if (__resultMsg(result)){
					$('tr[id="rowId_'+seq+'"]').remove();
				}
			}
		,	error:function(request, status, error){
				alert(error);
			}
		});
	}
</script>
<div class="title title_border">메모관리</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="100px">
		<col width="35px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>수급자명</th>
			<td class="left"><?=$name;?></td>
			<th>년월</th>
			<td class="left"><?=$year;?>년 <?=IntVal($month);?>월</td>
		</tr>
	</tbody>
</table>
<div class="title title_border">메모리스트</div>
<div id="divList" style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="60px">
			<col>
		</colgroup>
		<tbody id="tbodyList"></tbody>
	</table>
</div>
<?
	include_once('../inc/_footer.php');
?>