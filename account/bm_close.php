<?
	include_once("../inc/_db_open.php");
	include_once('../inc/_http_uri.php');
	include_once("../inc/_myFun.php");

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= Date('Y');
	$month	= Date('m');
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./bm_close_search.php'
		,	data :{
				'year':$('#lblYYMM').text()
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				if (data){
					var row = data.split('?');

					for(var i=0; i<row.length; i++){
						if (row[i]){
							var col = __parseVal(row[i]);

							$('#ID_CELL_CLOSE_YN_'+col['month']).text(col['close']);
							$('#ID_CELL_CLOSE_DT_'+col['month']).text(__getDate(col['closeDt'],'.'));
							$('#ID_CELL_ACCT_DT_'+col['month']).text(__getDate(col['acctDt'],'.'));
						}
					}
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSet(month, close){
		$.ajax({
			type :'POST'
		,	url  :'./bm_close_exec.php'
		,	data :{
				'year'	:$('#lblYYMM').text()
			,	'month'	:month
			,	'close'	:close
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				lfSearch();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfAcct(month){
		$.ajax({
			type :'POST'
		,	url  :'./bm_close_acct.php'
		,	data :{
				'year'	:$('#lblYYMM').text()
			,	'month'	:month
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (!result){
					alert('정상적으로 처리되었습니다.');
					lfSearch();
				}else{
					//alert(result);
					document.write(result);
				}
				$('#tempLodingBar').hide();
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div class="title title_border">월별데이타마감</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="84px">
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
			<td class="right last"></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col width="70px">
		<col width="90px">
		<col width="90px">
		<col width="230px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">월</th>
			<th class="head">마감여부</th>
			<th class="head">마감일자</th>
			<th class="head">정산일자</th>
			<th class="head">정산 및 마감</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody><?
		for($i=1; $i<=12; $i++){?>
			<tr>
				<td class="center"><?=$i;?>월</td>
				<td class="center" id="ID_CELL_CLOSE_YN_<?=$i;?>"></td>
				<td class="center" id="ID_CELL_CLOSE_DT_<?=$i;?>"></td>
				<td class="center" id="ID_CELL_ACCT_DT_<?=$i;?>"></td>
				<td class="left">
					<span id="ID_BTN_ACCT_<?=$i;?>" class="btn_pack small" style="display:;"><button onclick="lfAcct('<?=$i;?>');">정산실행</button></span>
					<span id="ID_BTN_CLOSE_<?=$i;?>" class="btn_pack small" style="display:;"><button onclick="lfSet('<?=$i;?>','Y');">마감설정</button></span>
					<span id="ID_BTN_CANCEL_<?=$i;?>" class="btn_pack small" style="display:;"><button onclick="lfSet('<?=$i;?>','N');">마감설정해제</button></span>
				</td><?
				if ($i == 1){?>
					<td class="left top last" rowspan="12">
						※정산실행 - 현재의 데이타를 통계에서 볼 수 있도록 정산합니다.<br>
						※마감 - 마감을 설정하면 "수입 및 지출"의 등록 및 수정을 불가능하도록 설정할 수 있습니다.
					</td><?
				}?>
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