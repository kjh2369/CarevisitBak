<?
	include_once("../inc/_db_open.php");
	include_once('../inc/_http_uri.php');
	include_once("../inc/_myFun.php");

	$orgNo	= $_SESSION['userCenterCode'];
	$power	= $_SESSION['userLevel'];
	$year	= Date('Y');
	$month	= Date('m');
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
		,	url  :'./bm_compay_search.php'
		,	data :{
				'year':$('#lblYYMM').attr('year')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var row = __parseVal(data);

				for(var i=1; i<=12; i++){
					$('#txtAmt_'+i).val(__num2str(row[i]));
				}

				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSave(){
		$.ajax({
			type :'POST'
		,	url  :'./bm_compay_save.php'
		,	data :{
				'year'	:$('#lblYYMM').attr('year')
			,	'1'		:$('#txtAmt_1').val()
			,	'2'		:$('#txtAmt_2').val()
			,	'3'		:$('#txtAmt_3').val()
			,	'4'		:$('#txtAmt_4').val()
			,	'5'		:$('#txtAmt_5').val()
			,	'6'		:$('#txtAmt_6').val()
			,	'7'		:$('#txtAmt_7').val()
			,	'8'		:$('#txtAmt_8').val()
			,	'9'		:$('#txtAmt_9').val()
			,	'10'	:$('#txtAmt_10').val()
			,	'11'	:$('#txtAmt_11').val()
			,	'12'	:$('#txtAmt_12').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (__resultMsg(result)){
				}
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div class="title title_border">정직임금.기타수입지출관리</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="90px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년도</th>
			<td class="last">
				<div class="left" style="padding-top:2px;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),-1); lfSearch();" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYYMM" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),1); lfSearch();" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<td class="right last">
				<span class="btn_pack m"><button onclick="lfSave();">저장</button></span>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="100px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">월</th>
			<th class="head">본사비용</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody><?
		for($i=1; $i<=12; $i++){?>
			<tr>
				<td class="center"><?=$i;?>월</td>
				<td><input id="txtAmt_<?=$i;?>" type="text" value="0" class="number" style="width:100%;"></td>
				<td class="last"></td>
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