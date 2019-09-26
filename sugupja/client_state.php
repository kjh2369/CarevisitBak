<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}

	$code	= $_SESSION['userCenterCode'];
	$year	= Date('Y');
	$month	= intval(Date('m'));
?>
<script type="text/javascript">
	$(document).ready(function(){
		setTimeout('lfSearch()',200);
	});

	function lfSearch(){
		$.ajax({
			type : 'POST'
		,	url  : './client_state_search.php'
		,	data : {
				'year':$('#lblYYMM').attr('year')
			,	'month':$('#lblYYMM').attr('month')
			}
		,	beforeSend: function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success: function(data){
				var row = data.split(String.fromCharCode(11));
				var html = '';
				var no = 1;

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseVal(row[i]);

						if (col['kind'] == '3'){
							col['kind'] = '기초';
						}else if (col['kind'] == '2'){
							col['kind'] = '의료';
						}else if (col['kind'] == '4'){
							col['kind'] = '경감';
						}else if (col['kind'] == '1'){
							col['kind'] = '일반';
						}else{
							col['kind'] = '';
						}

						if (col['gender'] == '1'){
							col['gender'] = '남';
						}else{
							col['gender'] = '여';
						}

						html += '<tr style="cursor:default;" onmouseover="this.style.backgroundColor=\'#efefef\';" onmouseout="this.style.backgroundColor=\'#ffffff\';">';
						html += '<td class="center">'+no+'</td>';
						html += '<td class="center"><div class="left nowrap" style="width:70px;">'+col['name']+'</div></td>';
						html += '<td class="center">'+col['jumin']+'</td>';
						html += '<td class="center">'+col['appNo']+'</td>';
						html += '<td class="center">'+col['kind']+'</td>';
						html += '<td class="center">'+col['gender']+'</td>';
						html += '<td class="center">'+__getDate(col['from'],'.')+'~'+__getDate(col['to'],'.')+'</td>';
						html += '<td class="center"><div class="left nowrap" style="width:100px;">'+col['addr']+'</div></td>';
						html += '<td class="center">'+__getPhoneNo(col['telno'],'.')+'</td>';
						html += '<td class="center"><div class="left nowrap" style="width:60px;">'+col['grdNm']+'</div></td>';
						html += '<td class="center last"></td>';
						html += '</tr>';

						no ++;
					}
				}

				if (!html){
					 html = '<tr><td class="center last" colspan="20">::검색된 데이타가 없습니다.::</td></tr>';
				}

				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();
			}
		});
	}

	function lfExcel(){
		var year	= $('#lblYYMM').attr('year');
		var month	= $('#lblYYMM').attr('month');
	
		location.href = './client_state_excel.php?year='+year+'&month='+month;
	}

	function lfShowPDF(){
		
		var year	= $('#lblYYMM').attr('year');
		var month	= $('#lblYYMM').attr('month');
		
		var para = 'root=sugupja'
				 + '&dir=L'
				 + '&fileName=client_state'
				 + '&fileType=pdf'
				 + '&target=show.php'
				 + '&showForm=CLIENT_STATE'
				 + '&year='+year
				 + '&month='+month
				 + '&param=';

		__printPDF(para);
	}

</script>
<div class="title title_border">수급자현황(재가요양)</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="80px">
		<col width="500px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head">년월</th>
			<td class="left">
				<div style="padding-top:2px;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYYMM" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<td class="left last"><? echo $myF->_btn_month($month,'__moveMonth(',',$("#lblYYMM")); lfSearch();');?></td>
			<td class="right last">
				<span class="btn_pack m"><span class="pdf"></span><button type="button" onclick="lfShowPDF();">출력</button></span>
				<span class="btn_pack m"><span class="excel"></span><button type="button" onclick="lfExcel();">출력</button></span>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="95px">
		<col width="75px">
		<col width="35px">
		<col width="35px">
		<col width="130px">
		<col width="100px">
		<col width="80px">
		<col width="60px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">성명</th>
			<th class="head">주민번호</th>
			<th class="head">인정번호</th>
			<th class="head">구분</th>
			<th class="head">성별</th>
			<th class="head">계약기간</th>
			<th class="head">주소</th>
			<th class="head">연락처</th>
			<th class="head">보호자</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="tbodyList"></tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>