<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$year	= Date('Y');
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('#ID_LIST').height(315);
		lfSearch();
	});

	function lfMoveYear(pos){
		var year = __str2num($('#ID_YYMM').attr('year'));

		year += pos;

		$('#ID_YYMM').attr('year',year).text(year);

		lfSearch();
	}

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./center_reg_tax_his_search.php'
		,	data :{
				'year':$('#ID_YYMM').text()
			,	'orgNo':'<?=$orgNo;?>'
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('tbody',$('#ID_LIST')).html(html);
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년월</th>
			<td>
				<div>
					<div style="float:left; width:auto; margin-left:5px; margin-top:3px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="lfMoveYear(-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="ID_YYMM" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
					<div style="float:left; width:auto; margin-top:3px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="lfMoveYear(1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
		</tr>
	</tbody>
</table><?

$colgroup = '
	<col width="40px">
	<col width="70px">
	<col width="100px">
	<col>';?>
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">월</th>
			<th class="head">발행일자</th>
			<th class="head">청구/영수 구분</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center top bottom last" colspan="4">
				<div id="ID_LIST" style="overflow-x:hidden; overflow-y:scroll;">
					<table class="my_table" style="width:100%;">
						<colgroup><?=$colgroup;?></colgroup>
						<tbody></tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
</table>