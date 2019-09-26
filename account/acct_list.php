<?
	include_once("../inc/_db_open.php");
	include_once('../inc/_http_uri.php');
	include_once("../inc/_myFun.php");

	$code  = $_SESSION['userCenterCode'];
	$year  = Date('Y');
	$month = Date('m');

	if ($type == '2'){
		$title = '입금내역조회';
		$gbn = 'I';
	}else if ($type == '12'){
		$title = '지출내역조회';
		$gbn = 'E';
	}else{
		include('../inc/_http_home.php');
		exit;
	}
	
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSetMonth();
		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./acct_search.php'
		,	data :{
				'type':'<?=$type;?>'
			,	'year':$('#lblYear').text()
			,	'month':$('#txtMonth').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var html = '';
				var html2 = '';
				var list = data.split(String.fromCharCode(1));
				var totPay = 0;
				var totCharge = 0;

				for(var i=0; i<list.length; i++){
					if (list[i]){
						var val  = list[i].split(String.fromCharCode(2));

						html += '<tr'
								+ ' entDt="'+val[0]+'"'
								+ ' seq="'+val[1]+'"'
							 +  ' onmouseover="this.style.backgroundColor=\'#efefef\';" onmouseout="this.style.backgroundColor=\'#ffffff\';">'
							 +  '<td class="center">'+(i+1)+'</td>'
							 +  '<td class="center">'+val[2]+'</td>'
							 +  '<td class="center">'+val[3]+'</td>'
							 +  '<td class="left">'+val[5]+'</td>'
							 +  '<td class="right">'+__num2str(val[7])+'</td>'
							 +  '<td class="right">'+__num2str(val[8])+'</td>'
							 +  '<td class="left"><div class="nowrap" style="width:130px;">'+val[6]+'</div></td>'
							 +  '<td class="left last">'
								+ '<span class="btn_pack m"><button type="button" onclick="lfModify($(this).parent().parent().parent());">수정</button></span>&nbsp;'
								+ '<span class="btn_pack m"><button type="button" onclick="lfDelete($(this).parent().parent().parent());">삭제</button></span>&nbsp;'
								+ '<span class="btn_pack m" style="cursor:default;"><button type="button" onclick="lfPrint($(this).parent().parent().parent());" '+(!val[3] ? 'disabled="true"' : '')+'>출력</button></span>&nbsp;'
								+ '<span class="btn_pack m" style="cursor:default;"><button type="button" onclick="lfExcel($(this).parent().parent().parent());" '+(!val[3] ? 'disabled="true"' : '')+'>엑셀</button></span>'
								
							 +  '</td>'
							 +  '</tr>';

							totPay    += parseInt(val[7]);
							totCharge += parseInt(val[8]);
					}
				}
				
				if (html){	
					//합계
					html2 += '<tr style="background-color:#efefef;">'
								 +  '<td class="right" colspan="4">합 계</td>'
								 +  '<td class="right">'+__num2str(totPay)+'</td>'
								 +  '<td class="right">'+__num2str(totCharge)+'</td>'
								 +  '<td class="left" colspan="2"></td>'
								 +  '</tr>';
				}

				if (!html){
					 html = '<tr><td class="center last" colspan="20">::검색된 데이타가 없습니다.::</td></tr>';
				}
				
				

				$('#list').html(html);
				$('#tot_list').html(html2);
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfModify(obj){
		var objModal = new Object();
		var url      = './acct_pop.php?type=<?=$type;?>';
		var style    = 'dialogWidth:600px; dialogHeight:250px; dialogHide:yes; scroll:no; status:no';

		objModal.result = false;
		objModal.entDt  = $(obj).attr('entDt');
		objModal.seq    = $(obj).attr('seq');

		window.showModalDialog(url, objModal, style);

		if (!objModal.result) return;

		lfSearch();
	}

	function lfDelete(obj){
		if (!confirm('삭제후 복구가 볼가능합니다.\n정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type :'POST'
		,	url  :'./acct_delete.php'
		,	data :{
				'type' :'<?=$type;?>'
			,	'entDt':$(obj).attr('entDt')
			,	'seq'  :$(obj).attr('seq')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (result == 1){
					lfSearch();
				}else if (result == 9){
					alert('오류가 발생하였습니다. 잠시후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfPrint(obj){
		var arguments = 'root=account'
					  + '&dir=L'
					  + '&fileName=acct'
					  + '&fileType=pdf'
					  + '&target=show.php'
					  + '&gbn=<?=$gbn;?>'
					  + '&entDt='+$(obj).attr('entDt')
					  + '&seq='+$(obj).attr('seq')
					  + '&showForm='
					  + '&param=';

	__printPDF(arguments);
	}

	function lfExcel(obj){
	
		$('#entDt').val($(obj).attr('entDt'));
		$('#seq').val($(obj).attr('seq'));
		$('#gubun').val('<?=$gbn;?>');
		
		$('#f').attr('action', './acct_excel2.php');
		$('#f').attr('target', '_self');
		$('#f').submit();
	}

</script>

<div class="title title_border"><?=$title;?></div>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="85px">
		<col width="460px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년월</th>
			<td class="center">
				<div class="left" style="padding-top:2px;">
				<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="lfMoveYear(-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
				<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYear"><?=$year;?></div>
				<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="lfMoveYear(1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<td class="left last"><? echo $myF->_btn_month($month, 'lfMoveMonth(', ');', null, false);?></td>
			<td class="right last"></td>
		</tr>
	</tbody>
</table>
<input id="txtMonth" name="txt" type="hidden" value="<?=$month;?>">
<input id="entDt" name="entDt" type="hidden" value="">
<input id="seq" name="seq" type="hidden" value="">
<input id="gubun" name="gubun" type="hidden" value="">

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="90px">
		<col width="130px">
		<col width="70px">
		<col width="70px">
		<col width="150px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">일자</th>
			<th class="head">증빙서번호</th>
			<th class="head">항목</th>
			<th class="head">금액</th>
			<th class="head">부과세</th>
			<th class="head">적요</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="tot_list"></tbody>
	<tbody id="list"></tbody>
	<tfoot>
		<tr>
			<td class="center bottom last" colspan="20"><? include_once('../inc/_page_script.php');?></td>
		</tr>
	</tfoot>
</table>
<?
	include_once("../inc/_db_close.php");
?>