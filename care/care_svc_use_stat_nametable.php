<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');

	$grpCd	= $_SESSION['userCenterCode'];
	$SR     = $_GET['SR'];
	$code   = $_GET['code'];
	$fromDt = $_GET['fromDt'];
	$toDt   = $_GET['toDt'];
	
?>
<script type="text/javascript">
	//var opener = null;
	$(document).ready(function(){
		//opener = window.dialogArguments;
		//$('#divList').height(__GetHeight($('#divList')) - 30);
		lfSearch();
	});

	function lfSearch(){
		var data = {};

		/*
			data = {
				'SR'	:opener.SR
			,	'code'	:opener.code
			,	'year'	:opener.year
			,	'month'	:opener.month
			};
		*/
		data = {
			'SR'	:'<?=$SR;?>'
		,	'code'	:'<?=$code;?>'
		,	'fromDt':'<?=$fromDt;?>'
		,	'toDt'	:'<?=$toDt;?>'
		};
		
		$.ajax({
			type :'POST'
		,	url  :'./care_svc_use_stat_nametable_search.php'
		,	data :data
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#tbodyList').html(html);
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div id="title" class="title title_border">서비스이용현황</div><?
$colgroup = '
	<col width="40px">
	<col width="100px">
	<col width="70px">
	<col>';?>
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">성명</th>
			<th class="head">이용횟수</th>
			<th class="head">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="top" colspan="4">
				<div id="divList" style="width:100%; height:430px; overflow-x:hidden; overflow-y:scroll;">
					<table class="my_table" style="width:100%;">
						<colgroup><?=$colgroup;?></colgroup>
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