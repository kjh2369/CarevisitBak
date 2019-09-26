<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$title = lfGetSPName($sr);
	$type = $_GET['type'];
	$year = Date('Y');
	$month = Date('n');

	if ($type == 'RPT'){ //기타
		$suga_cd = 'B99';
	}else if ($type == 'RSLS'){ //자원연계서비스
		$suga_cd = 'A0602';
	}else if ($type == 'RSPL'){ //자원봉사자연결
		$suga_cd = 'A0603';
	}else if ($type == 'LHCT'){ //지역재가협의체구성
		$suga_cd = 'B0301';
	}
?>
<style>
	.rpt1{border:1px solid #FFC19E; background:#FAE0D4; line-height:1.5em; padding-top:2px; margin:2px;}
	.rpt2{border:1px solid #B7F0B1; background:#CEFBC9; line-height:1.5em; padding-top:2px; margin:2px;}
	.rpt3{border:1px solid #B5B2FF; background:#DAD9FF; line-height:1.5em; padding-top:2px; margin:2px;}
	.rpt4{border:1px solid #BDBDBD; background:#EAEAEA; line-height:1.5em; padding-top:2px; margin:2px;}
</style>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	function lfMoveYear(pos){
		$('#yymm').text(parseInt($('#yymm').attr('year')) + pos);
		$('#yymm').attr('year', parseInt($('#yymm').attr('year')) + pos);

		lfSearch();
	}

	function lfMoveMonth(month){
		$(document).find('.my_month').each(function(){
			if ($(this).attr('id').toString().substr($(this).attr('id').toString().length - month.toString().length - 1, $(this).attr('id').toString().length) == '_'+month.toString()){
				$(this).removeClass('my_month_1').addClass('my_month_y');
			}else{
				$(this).removeClass('my_month_y').addClass('my_month_1');
			}
		});
		$('#yymm').attr('month',month);

		lfSearch();
	}

	function lfRptReg(suga_cd, seq){
		if (!suga_cd) suga_cd = '';
		if (!seq) seq = 0;

		var objModal = new Object();
		var url = './care_rpt_reg.php?SR=<?=$sr;?>&suga_cd='+suga_cd+'&seq='+seq+'&gbn=<?=$suga_cd;?>';
		var style = 'dialogWidth:800px; dialogHeight:600px; dialogHide:yes; scroll:no; status:no';

		objModal.win = window;
		objModal.SR = '<?=$sr;?>';
		objModal.suga_cd = '<?=$suga_cd;?>';

		window.showModalDialog(url, objModal, style);

		if (objModal.result) lfSearch();
	}

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./care_rpt_search.php'
		,	data :{
				'year':$('#yymm').attr('year')
			,	'month':$('#yymm').attr('month')
			,	'SR':'<?=$sr;?>'
			,	'suga_cd':'<?=$suga_cd;?>'
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#TBL_LIST').html(html);
				$('#TBL_LIST td[id^="CELL_"]').css('cursor', 'default');
				$('#tempLodingBar').remove();

				lfSummary();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSummary(){
		$.ajax({
			type :'POST'
		,	url  :'./care_rpt_summary.php'
		,	data :{
				'year':$('#yymm').attr('year')
			,	'month':$('#yymm').attr('month')
			,	'SR':'<?=$sr;?>'
			,	'suga_cd':'<?=$suga_cd;?>'
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#DIV_SUMMARY').html(html);
				$('#DIV_SUMMARY tr').each(function(){
					$('td:last', this).css('border-right', 'none');
					$('.head:last', this).css('border-right', 'none');
				});
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script><?
	if ($type == 'RPT'){ //기타
		$subject = '기타';
	}else if ($type == 'RSLS'){ //자원연계서비스
		$subject = '자원연계서비스';
	}else if ($type == 'RSPL'){ //자원봉사자연결
		$subject = '자원봉사자연결';
	}else if ($type == 'LHCT'){ //지역재가협의체구성
		$subject = '지역재가협의체구성';
	}
?>
<div class="title title_border"><?=$subject;?>(<?=$title;?>)</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년월</th>
			<td class="left last" colspan="6">
				<div style="float:left; width:auto;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="lfMoveYear(-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="yymm" year="<?=$year?>" month="<?=$month?>"><?=$year?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="lfMoveYear(1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px;"><?=$myF->_btn_month($month, 'lfMoveMonth(', ');', null, false);?></div>
				</div>
				<div style="float:right; width:auto;">
					<span class="btn_pack m"><button onclick="lfRptReg();">등록</button></span>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="<?=Round(100 / 7);?>%" span="7">
	</colgroup>
	<thead>
		<tr><?
			for($i=0;  $i<=6; $i++){
				$dow = $myF->dowidx2name($i, true);
				echo '<th class="head '.($i == 6 ? 'last' : '').'">'.$dow.'</th>';
			}?>
		</tr>
	</thead>
	<tbody id="TBL_LIST"></tbody>
</table>
<div id="DIV_SUMMARY" style="margin-bottom:25px;"></div><?

	include_once('../inc/_db_close.php');
?>