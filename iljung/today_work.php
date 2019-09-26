<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_body_header.php');

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}
?>
<script type="text/javascript">
	$(document).ready(function(){
		setTimeout('lfSearch()',200);
	});

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./today_work_search.php'
		,	data:{
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();

				lfPublicCompany();
			}
		,	error:function(request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}

	function lfPublicCompany(){
		var today = '<?=Date("Ymd");?>';

		$('tr',$('#tbodyList')).each(function(){
			var appNo = $('td', this).eq(3).text();
			var objFrom = $('td', this).eq(9);
			var objTo = $('td', this).eq(10);
			var objTime = $('td', this).eq(11);
			var objRemark = $('td', this).eq(13);
			var subCd = $(this).attr('subCd');
			var seq = $(this).attr('seq');

			if (appNo){
				$.ajax({
					type:'POST',
					url:'http://dmd.longtermcare.or.kr/autodmd/nypk/nypk_autoDmdList.do?method=nypkRfidmodify',
					data:{
						'pageIndex':'1'
					,	'serviceKind':subCd
					,	'searchFrDt':today
					,	'searchToDt':today
					,   'searchGbn':'5'
					,	'searchValue':appNo
					,	'delYn':'N'
					},
					beforeSend: function (){
						$(objRemark).html('<span style="color:#4374D9;">Loading...</span>');
					},
					success: function (html){
						var time = lfGetHtml(html, seq);

						$(objFrom).text(time['fromTime']);
						$(objTo).text(time['toTime']);
						$(objTime).text(time['procTime']);
						$(objRemark).html('<span style="color:blue; font-weight:bold;">OK</span>');
					},
					error: function (){
						alert('error');
					}
				}).responseXML;
			}else{
				$(objRemark).html('<span style="color:red;">인정번호 오류</span>');
			}
		});
	}

	function lfGetHtml(html, seq){
		var tagTimeTable = $('table[@background="/autodmd/ny/img/common/table_nemo_bg.gif"]', html);
		var time = {};
		var no = 1;

		$('tr', tagTimeTable).each(function(){
			if (!isNaN($('td:nth-child(1)', $(this)).text())){
				if (no == seq){
					fromDT	= lfSplitDateTime($('td:nth-child(9)', $(this)).text());
					toDT	= lfSplitDateTime($('td:nth-child(10)', $(this)).text());

					time['fromTime'] = fromDT[1];

					if (toDT){
						time['toTime'] = toDT[1];
					}else{
						time['toTime'] = '';
					}

					time['procTime'] = $('td:nth-child(8)', $(this)).text().split('분').join('');
				}

				no ++;
			}
		});

		return time;
	}

	function lfSplitDateTime(html){
		var DateTime = html.split(' ');

		if (html){
			try{
				DateTime[1] = DateTime[1].substring(0,5);
			}catch(e){
			}

			return DateTime;
		}else{
			return '';
		}
	}
</script>
<div class="title title_border">당일일정(공단연동)</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="right last">
				<span class="btn_pack m"><button type="button" onclick="lfSearch();">새로고침</button></span>
				<span class="btn_pack m"><button type="button" onclick="lfPublicCompany();">공단내역 다시 가져오기</button></span>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px" span="2">
		<col width="60px">
		<col width="90px">
		<col width="100px">
		<col width="110px">
		<col width="40px" span="2">
		<col width="50px">
		<col width="40px" span="2">
		<col width="50px">
		<col width="50px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" colspan="2">계획시간</th>
			<th class="head" rowspan="2">수급자명</th>
			<th class="head" rowspan="2">인정번호</th>
			<th class="head" rowspan="2">서비스명</th>
			<th class="head" rowspan="2">요양사명</th>
			<th class="head" colspan="3">수행시간</th>
			<th class="head" colspan="3">공단시간</th>
			<th class="head" rowspan="2">상태</th>
			<th class="head last" rowspan="2">비고</th>
		</tr>
		<tr>
			<th class="head">시작</th>
			<th class="head">종료</th>
			<th class="head">시작</th>
			<th class="head">종료</th>
			<th class="head">시간</th>
			<th class="head">시작</th>
			<th class="head">종료</th>
			<th class="head">시간</th>
		</tr>
	</thead>
	<tbody id="tbodyList"></tbody>
	<tfoot>
		<tr>
			<td class="bottom last">&nbsp;</td>
		</tr>
	</tfoot>
</table>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>