<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_body_header.php");

	$orgNo = $_SESSION['userCenterCode'];
	$year = Date('Y');
	$month = Date('m');

	$sql = 'SELECT	val1
			FROM	cv_claim_set
			WHERE	gbn = \'01\'';

	$claimDt = $conn->get_data($sql);

	$sql = 'SELECT	val1, val2
			FROM	cv_claim_set
			WHERE	gbn = \'02\'';

	$row = $conn->get_array($sql);

	$bankDt = $row['val1'];
	$bankTime = $row['val2'];

	Unset($row);
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type:'POST',
			url:'./claim_list_search.php',
			data:{
				'year'	:$('#ID_YYMM').attr('year')
			},
			beforeSend: function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			},
			success:function(data){
				var row = data.split('?');
				var yymmstr = '';
				var sum = new Array;

				$('td[id^="ID_CELL_"]', $('#ID_CLAIM')).text('');
				$('span', $('td[id^="ID_BTN_"]', $('#ID_CLAIM'))).hide();
				$('div[id^="ID_CELL_MSG_"]').hide();
				$('div[id^="ID_CELL_DFTAMT_"]').hide();

				for(var i=1; i<=12; i++) sum[i] = 0;
				for(var i=0; i<row.length; i++){
					var col = __parseVal(row[i]);
					var y = $('#ID_YYMM').attr('year');
					var m = __str2num(col['month']) - 1;

					if (m < 1){
						y --;
						m = 12;
					}

					$('#ID_CELL_'+col['month']+'_1').text(__num2str(col['acctAmt']));
					$('#ID_CELL_'+col['month']+'_2').text(__num2str(col['useAmt']));
					$('#ID_CELL_'+col['month']+'_3').text(__num2str(col['disAmt']));

					$('#ID_CELL_'+col['month']+'_4').text(col['inAmt'] == '-' ? col['inAmt'] : __num2str(col['inAmt']));
					$('#ID_CELL_'+col['month']+'_5').text(col['dftAmt'] == '-' ? col['dftAmt'] : __num2str(col['dftAmt']));
					//	$('#ID_CELL_'+col['month']+'_4').text('-');
					//	$('#ID_CELL_'+col['month']+'_5').text('-');

					if (col['bill_kind'] == '1'){
						yymmstr = $('#ID_YYMM').attr('year')+'년 '+col['month']+'월 사용요금입니다.';
					}else{
						yymmstr = y+'년 '+m+'월 사용요금입니다.';
					}

					$('span', $('#ID_BTN_'+col['month'])).show();
					$('#ID_CELL_MSG_'+col['month']).text(yymmstr).show();

					//sum[1] += (col['acctAmt'] ? __str2num(col['acctAmt']) : 0);
					sum[1] += __str2num($('#ID_CELL_'+col['month']+'_1').text());
					sum[2] += __str2num($('#ID_CELL_'+col['month']+'_2').text());
					sum[3] += __str2num($('#ID_CELL_'+col['month']+'_3').text());
					sum[4] += __str2num($('#ID_CELL_'+col['month']+'_4').text());
					sum[5] += __str2num($('#ID_CELL_'+col['month']+'_5').text());

					if (__str2num($('#ID_CELL_'+col['month']+'_5').text()) > 0){
						$('#ID_CELL_DFTAMT_'+col['month']).show();
					}
				}

				$('#ID_SUM_1').text(__num2str(sum['1']));
				$('#ID_SUM_2').text(__num2str(sum['2']));
				$('#ID_SUM_3').text(__num2str(sum['3']));
				$('#ID_SUM_4').text(__num2str(sum['4']));
				$('#ID_SUM_5').text(__num2str(sum['5']));

				$('#tempLodingBar').remove();
			},
			error: function (request, status, error){
				$('#tempLodingBar').remove();

				alert('[ERROR]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		}).responseXML;
	}

	function lfDftHisShow(){
		var objModal = new Object();
		showModalDialog('../claim/claim_dftamt.php', objModal, 'dialogWidth:400px; dialogHeight:300px; dialogHide:yes; scroll:no; status:yes');
	}
</script>
<div class="title title_border">사용요금안내</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<!--col width="85px"-->
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년월</th>
			<td class="last">
				<div class="left" style="padding-top:2px;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="__moveYear($('#ID_YYMM'),-1,'lfSearch()');" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="ID_YYMM" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="__moveYear($('#ID_YYMM'),1,'lfSearch()');" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<!--td class="last"><?//$myF->_btn_month($month,'__moveMonth(',',$("#ID_YYMM"),"lfSearch()")');?></td-->
		</tr>
	</tbody>
</table>
<table id="ID_CLAIM" class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px" style="*width:75px;" span="5">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">청구<br>월</th>
			<th class="head" colspan="4">청구내역</th>
			<th class="head" rowspan="2">입금금액</th>
			<th class="head last" rowspan="2">
				<div style="float:left; width:auto; text-align:left; margin-left:5px; font-weight:bold;">※CMS(<?=$myF->dateStyle($claimDt, 'KOR');?>), 무통장(<?=$myF->dateStyle($bankDt, 'KOR').' '.$myF->timeStyle($bankTime);?>)의 입금분이 반영되었습니다.</div>
			</th>
		</tr>
		<tr>
			<th class="head">청구금액</th>
			<th class="head">사용금액</th>
			<th class="head">할인금액</th>
			<th class="head">미납금액</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th class="sum center">합계</th>
			<td class="sum right" id="ID_SUM_1"></td>
			<td class="sum right" id="ID_SUM_2"></td>
			<td class="sum right" id="ID_SUM_3"></td>
			<td class="sum right" id="ID_SUM_5"></td>
			<td class="sum right" id="ID_SUM_4"></td>
			<td class="sum left last"></td>
		</tr><?
		for($i=1; $i<=12; $i++){?>
			<tr>
			<th class="center"><?=$i;?>월</th>
			<td class="right" id="ID_CELL_<?=$i;?>_1"></td>
			<td class="right" id="ID_CELL_<?=$i;?>_2"></td>
			<td class="right" id="ID_CELL_<?=$i;?>_3"></td>
			<td class="right" id="ID_CELL_<?=$i;?>_5"></td>
			<td class="right" id="ID_CELL_<?=$i;?>_4"></td>
			<td class="left last" id="ID_BTN_<?=$i;?>">
				<div style="float:left; width:auto; margin-top:3px;">
					<span class="btn_pack small" style="display:none;"><button onclick="window.open('../popup/fee_msg/t.php?type=CLAIM&year='+$('#ID_YYMM').attr('year')+'&month=<?=$i;?>','CLAIM','width=700,height=850,Top=0,left=100,scrollbars=yes,resizable=no,location=no,toolbar=no,menubar=no');">청구서</button></span>
				</div>
				<div id="ID_CELL_MSG_<?=$i;?>" style="float:left; width:auto; margin-left:10px; display:none;"></div>
				<div id="ID_CELL_DFTAMT_<?=$i;?>" style="float:right; width:auto; margin-left:10px; margin-top:3px; display:none;">
					<span class="btn_pack small"><button onclick="lfDftHisShow();">미납내역보기</button></span>
				</div>
			</td>
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
	include("../inc/_body_footer.php");
	include("../inc/_footer.php");
?>