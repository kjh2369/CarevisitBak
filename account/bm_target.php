<?
	include_once("../inc/_db_open.php");
	include_once('../inc/_http_uri.php');
	include_once("../inc/_myFun.php");

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= Date('Y');
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
		});
		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./bm_target_search.php'
		,	data :{
				'year':$('#lblYYMM').text()
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				var col = __parseVal(data);

				for(var i=1; i<=12; i++){
					$('#txtAmt',$('#ID_ROW_'+i)).val(__num2str(col[i]));
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSave(){
		var para = '';

		$('tr[id^="ID_ROW_"]').each(function(){
			var month = $(this).attr('id').replace('ID_ROW_','');
			para += (para ? '&' : '');
			para += (month+'='+__str2num($('#txtAmt',this).val()));
		});

		$.ajax({
			type :'POST'
		,	url  :'./bm_target_save.php'
		,	data :{
				'year':$('#lblYYMM').text()
			,	'para':para
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (__resultMsg(result)){
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div class="title title_border">월별목표관리</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년도</th>
			<td class="last">
				<div class="left" style="float:left; width:auto; padding-top:2px;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),-1); lfSearch();" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYYMM" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),1); lfSearch();" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
				<div style="float:right; width:auto; padding-top:2px;">
					<span class="btn_pack m"><button onclick="lfSave();">저장</button></span>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col width="100px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">월</th>
			<th class="head">목표금액</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center sum">합계</td>
			<td class="center sum"><div id="ID_TOTAL_TARGET" class="right"></div></td>
			<td class="center sum last"></td>
		</tr><?
		for($i=1; $i<=12; $i++){?>
			<tr id="ID_ROW_<?=$i;?>">
				<td class="center"><?=$i;?>월</td>
				<td class="center"><input id="txtAmt" type="text" value="0" class="number" style="width:100%;"></td>
				<td class="center last"></td>
			</tr><?
		}?>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once("../inc/_db_close.php");
?>