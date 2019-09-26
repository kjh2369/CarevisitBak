<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*
	 * CMS
	 * ap.efnc.co.kr
	 * id : eos21994
	 * pw : geos0120
	 */

	$year  = Date('Y');
	$month = IntVal(Date('m'));
?>
<script type="text/javascript">
	$(document).ready(function(){
		__fileUploadInit($('#f'), 'lfFileUploadCallback');
		lfSearch();
		lfDetail();
	});

	function lfFileUpload(){
		if (!$('#filename').val()){
			alert('');
			$('#filename').focus();
			return;
		}

		var exp = $('#filename').val().split('.');

		if (exp[exp.length-1].toLowerCase() != 'xls'){
			alert('EXCEL 파일을 선택하여 주십시오.');
			return;
		}

		var frm = $('#f');
			frm.attr('action', './acct_deposit_excel_upload.php');
			frm.submit();

		$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
	}

	function lfFileUploadCallback(data, state){
		if (__fileUploadCallback(data, state)){
			alert('정상적으로 처리되었습니다.');
			lfSearch();
		}else{
			alert('저장중 오류가 발생하였습니다.\n관리자에게 문의하여 주십시오.');
		}
	}

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./search.php'
		,	data :{
				'mode':'<?=$type;?>'
			,	'year':$('#lblYear').text()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var val = data.split(String.fromCharCode(2));
				var tot = 0;

				for(var i=1; i<=12; i++){
					tot += __str2num(val[i-1]);

					var link = '<a href="#" onclick="lfDetail(\''+i+'\'); return false;">'+__num2str(val[i-1])+'</a>';

					$('#lblMon_'+i).html(link);
				}

				$('#lblMonTot').text(__num2str(tot));
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfDetail(asMonth){
		var mon = __str2num(asMonth);
			mon = (mon < 10 ? '0' : '')+mon;

		$.ajax({
			type :'POST'
		,	url  :'./search.php'
		,	data :{
				'mode':'<?=$type;?>_1'
			,	'year':$('#lblYear').text()
			,	'month':mon
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var html = '';
				var cnt  = 0;
				var list = data.split(String.fromCharCode(1));

				for(var i=0; i<list.length; i++){
					if (list[i]){
						var val = list[i].split(String.fromCharCode(2));

						html += '<tr>'
							 +  '<td class="center">'+val[0]+'</td>'
							 +  '<td class="left" style="'+(val[6] != 'Y' ? 'color:red;' : '')+'">'+val[1]+'</td>'
							 +  '<td class="left">'+val[2]+'</td>'
							 +  '<td class="right">'+__num2str(val[3])+'</td>'
							 +  '<td class="center">'+val[4]+'</td>'
							 +  '<td class="left last">'+val[5]+'</td>'
							 +  '</tr>';
						cnt ++;
					}
				}

				if (!html){
					 html = '<tr><td class="center last" colspan="6">::검색된 데이타가 없습니다.::</td></tr>';
				}

				$('#list').html(html);
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>

<div class="title title_border">입금등록(엑셀)</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col width="300px">
		<col>
	</colgroup>
	<tbody>
		<th>엑셀파일선택</th>
		<td style="padding:0 5px 0 5px;"><input type="file" name="filename" id="filename" style="width:100%; margin:0;"></td>
		<td class="left last"><span class="btn_pack m"><button type="button" onclick="lfFileUpload();">업로드</button></span></td>
	</tbody>
</table>

<div class="title title_border">년월별입금액</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="85px">
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
			<td class="right last"></td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px" span="12">
	</colgroup>
	<thead>
		<tr>
			<th class="head">1월</th>
			<th class="head">2월</th>
			<th class="head">3월</th>
			<th class="head">4월</th>
			<th class="head">5월</th>
			<th class="head">6월</th>
			<th class="head">7월</th>
			<th class="head">8월</th>
			<th class="head">9월</th>
			<th class="head">10월</th>
			<th class="head">11월</th>
			<th class="head last">12월</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td id="lblMon_1" class="right">0</td>
			<td id="lblMon_2" class="right">0</td>
			<td id="lblMon_3" class="right">0</td>
			<td id="lblMon_4" class="right">0</td>
			<td id="lblMon_5" class="right">0</td>
			<td id="lblMon_6" class="right">0</td>
			<td id="lblMon_7" class="right">0</td>
			<td id="lblMon_8" class="right">0</td>
			<td id="lblMon_9" class="right">0</td>
			<td id="lblMon_10" class="right">0</td>
			<td id="lblMon_11" class="right">0</td>
			<td id="lblMon_12" class="right last">0</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="left last bold" colspan="12">
				<span>합계 : </span>
				<span id="lblMonTot">0</span>
			</td>
		</tr>
	</tfoot>
</table>

<div class="title title_border">월별상세내역</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="90px">
		<col width="200px">
		<col width="70px">
		<col width="50px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">입금일자</th>
			<th class="head">기관기호</th>
			<th class="head">기관명</th>
			<th class="head">금액</th>
			<th class="head">구분</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="list"></tbody>
	<tfoot>
		<tr>
			<td class="center bottom last" colspan="6"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once('../inc/_db_close.php');
?>