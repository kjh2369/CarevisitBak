<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo = $_SESSION['userCenterCode'];
	$year = $_POST['year'];
	$month = $_POST['month'];
	$result = $_POST['result'];
?>
<script type="text/javascript">
	$(document).ready(function(){
		var top = $('#divList').offset().top;
		var height = $(this).height();
		var h = height - top - 3;

		$('#divList').height(h);

		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./sw_mem_list_search.php'
		,	data:{
				'year':'<?=$year;?>'
			,	'month':'<?=$month;?>'
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#tbodyList').html(html);
			}
		});
	}

	function lfSetMem(jumin, name, from, to){
		eval('opener.<?=$result;?>(\''+jumin+'\',\''+name+'\',\''+from+'\',\''+to+'\')');
		self.close();
	}
</script>
<div class="title title_border">사회복지사 조회</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="100px">
		<col width="90px" span="2">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">성명</th>
			<th class="head">입사일</th>
			<th class="head">퇴사일</th>
			<th class="head">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center top" colspan="5">
				<div id="divList" style="width:100%; height:100px; overflow-x:hidden; overflow-y:auto;">
					<table class="my_table" style="width:100%;">
						<colgroup>
							<col width="40px">
							<col width="100px">
							<col width="90px" span="2">
							<col>
						</colgroup>
						<tbody id="tbodyList"></tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_footer.php');
?>