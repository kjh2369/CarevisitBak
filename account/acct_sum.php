<?
	include_once("../inc/_db_open.php");
	include_once('../inc/_http_uri.php');
	include_once("../inc/_myFun.php");

	$code  = $_SESSION['userCenterCode'];
	$year  = Date('Y');
	$month = Date('m');

	if ($type == '3'){
		$title = '입금내역집계';
		$gbn = 'I';
	}else if ($type == '13'){
		$title = '지출내역집계';
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
				'type' :'<?=$type;?>'
			,	'year' :$('#lblYear').text()
			,	'month':$('#txtMonth').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var html = '';
				var list = data.split(String.fromCharCode(1));
				var tot_pay =  0;
				var tot_over_pay = 0;
				var tot_t_pay =  0;
				
				for(var i=0; i<list.length; i++){
					if (list[i]){
						var val = list[i].split(String.fromCharCode(2));

						html += '<tr onmouseover="this.style.backgroundColor=\'#efefef\';" onmouseout="this.style.backgroundColor=\'#ffffff\';">'
							 +  '<td class="center">'+(i+1)+'</td>'
							 +  '<td class="center">'+val[0].split('-').join('.')+'</td>'
							 +  '<td class="center">'+(val[1] ? val[0].split('-').join('')+val[1] : '')+'</td>'
							 +  '<td class="left">'+val[2]+'</td>'
							 +  '<td class="right">'+__num2str(val[3])+'</td>'
							 +  '<td class="right">'+__num2str(val[4])+'</td>'
							 +  '<td class="right">'+__num2str(__str2num(val[3])+__str2num(val[4]))+'</td>'
							 +  '<td class="left"><span class="btn_pack m" style="cursor:default;"><button type="button" onclick="lfPrint($(this).parent().parent().parent());" '+(!val[1] ? 'disabled="true"' : '')+'>출력</button></span>&nbsp;<span class="btn_pack m" style="cursor:default;"><button type="button" onclick="lfExcel($(this).parent().parent().parent());" '+(!val[1] ? 'disabled="true"' : '')+'>엑셀</button></span></td>'
							 +  '</tr>';
							
							 tot_pay += Number(val[3]);
							 tot_over_pay +=  Number(val[4]);
							 tot_t_pay +=  Number(val[3])+Number(val[4]);
							
					}
				}
				
				html +=  '<tr>'
					 +  '<td class="head right bold" colspan="4">합계</td>'
					 +  '<td class="head right bold">'+__num2str(tot_pay)+'</td>'
					 +  '<td class="head right bold">'+__num2str(tot_over_pay)+'</td>'
					 +  '<td class="head right bold">'+__num2str(tot_t_pay)+'</td>'
					 +  '<td class="head center last"></td>'
					 +  '</tr>';
				
				
				$('#list').html(html);
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfPrint(obj){
		var docNo = $('td',obj).eq(2).text();

		var arguments = 'root=account'
					  + '&dir=L'
					  + '&fileName=acct'
					  + '&fileType=pdf'
					  + '&target=show.php'
					  + '&gbn=<?=$gbn;?>'
					  + '&docNo='+docNo
					  + '&showForm='
					  + '&param=';

		__printPDF(arguments);
	}

	function lfListPrint(){
		
		var arguments = 'root=account'
					  + '&dir=P'
					  + '&fileName=acct_list'
					  + '&fileType=pdf'
					  + '&target=show.php'
					  + '&gbn=<?=$gbn;?>'
					  + '&showForm='
					  + '&param=';

		__printPDF(arguments);
	}

	function lfExcel(obj){
		var docNo = $('td',obj).eq(2).text();

		$('#docNo').val(docNo);
		$('#gubun').val('<?=$gbn;?>');
		
		//if('<?=$debug?>'){ //신
			var url = './acct_excel2.php';
		//}else { //구
		//	var url = './acct_excel.php';
		//}

		$('#f').attr('action', url);
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
			<td class="right last">
			<?
			if($debug){ ?>
				<!--span class="btn_pack m" style="cursor:default;"><button type="button" onclick="lfListPrint();">출력</button></span--><?
			}
			?>
			</td>
		</tr>
	</tbody>
</table>
<input id="txtMonth" name="txt" type="hidden" value="<?=$month;?>">
<input id="docNo" name="docNo" type="hidden" value="">
<input id="gubun" name="gubun" type="hidden" value="">

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="90px">
		<col width="150px">
		<col width="80px">
		<col width="80px">
		<col width="80px">
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
			<th class="head">합계</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
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