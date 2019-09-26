<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$orgNo = $_SESSION['userCenterCode'];
	$gbn = $_REQUEST['gbn2'] ? $_REQUEST['gbn2'] : $_REQUEST['gbn'];
	$rstFun = $_REQUEST['rstFun'];
	$idx = $_REQUEST['idx'];
?>
<script type="text/javascript" src="../js/postno.js"></script>
<script type="text/javascript">
	var postno = new ClsPostNo();

	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
		});
		$('#txtWrd').focus();
	});

	function lfPostSearch(page){
		if (!$('#txtWrd').val()){
			alert('검색할 명칭을 입력하여 주십시오.');
			$('#txtWrd').focus();
			return;
		}

		if (!page) page = 1;

		postno.currentPage = page;
		postno.srchwrd = $('#txtWrd').val();

		var data = postno.Xml2Obj(postno.GetPostNo());
		var html = '', str = '';;
		var totCnt, totPag, curPag;
		var row = data.split('?');

		if (row.length <= 1){
			return;
		}

		for(var i=0; i<row.length; i++){
			if (row[i]){
				var col = __parseVal(row[i]);

				if (i == 0){
					totCnt = __str2num(col['totCnt']);
					totPag = __str2num(col['totPag']);
					curPag = __str2num(col['curPag']);

					if (curPag < 10){
						for(var j=1; j<=(totPag > 10 ? 10 : totPag); j++){
							if (page == j){
								str += '[<span style="color:RED; font-weight:bold;">'+j+'</span>]';
							}else{
								str += '[<a href="#" onclick="lfPostSearch('+j+'); return false;">'+j+'</a>]';
							}
						}

						if (totPag > 10) str += '...[<a href="#" onclick="lfPostSearch('+totPag+'); return false;">'+totPag+'</a>]';
					}else if (totPag - curPag < 10){
						str += '[<a href="#" onclick="lfPostSearch(1); return false;">1</a>]...';

						for(var j=totPag-10; j<=totPag; j++){
							if (page == j){
								str += '[<span style="color:RED; font-weight:bold;">'+j+'</span>]';
							}else{
								str += '[<a href="#" onclick="lfPostSearch('+j+'); return false;">'+j+'</a>]';
							}
						}
					}else{
						str += '[<a href="#" onclick="lfPostSearch(1); return false;">1</a>]...';

						for(var j=curPag-5; j<curPag; j++){
							str += '[<a href="#" onclick="lfPostSearch('+j+'); return false;">'+j+'</a>]';
						}
						str += '[<span style="color:RED; font-weight:bold;">'+curPag+'</span>]';
						for(var j=curPag+1; j<=curPag+5; j++){
							str += '[<a href="#" onclick="lfPostSearch('+j+'); return false;">'+j+'</a>]';
						}

						str += '...[<a href="#" onclick="lfPostSearch('+totPag+'); return false;">'+totPag+'</a>]';
					}

					$('#ID_PAGE_LIST').html(str);
				}else{
					html += '<tr>'
						 +	'<td class="center" id="ID_POSTNO">'+col['zipNo']+'</td>'
						 +	'<td>'
						 +	'<div class="left" id="ID_LNADDR">'+col['lnmAdres']+'</div>'
						 +	'<div class="left" id="ID_RNADDR">'+col['rnAdres']+'</div>'
						 +	'</td>'
						 +	'</tr>';
				}
			}
		}

		$('tbody',$('#ID_POST_LIST')).html(html);
		$('tr',$('tbody',$('#ID_POST_LIST'))).css('cursor','pointer').unbind('mouseover').bind('mouseover',function(){
			$(this).css('background-color','#EAEAEA');
		}).unbind('mouseout').bind('mouseout',function(){
			$(this).css('background-color','');
		}).unbind('click').bind('click',function(){
			var postno = $('#ID_POSTNO',this).text();
			var lnaddr = $('#ID_LNADDR',this).text();
			var rnaddr = $('#ID_RNADDR',this).text();

			eval('<?=$rstFun;?>("<?=$gbn;?>","'+postno+'","'+lnaddr+'","'+rnaddr+'",{"IDX":"<?=$idx;?>"})');
		});
	}
</script>

<table class="my_table" style="width:100%; background-color:WHITE;">
	<colgroup>
		<col width="80%">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td>
				<input id="txtWrd" type="text" value="" style="width:100%;" onkeydown="if(window.event.keyCode==13){lfPostSearch();};">
			</td>
			<td class="center">
				<span class="btn_pack m"><button onclick="lfPostSearch();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;검&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;색&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button></span>
			</td>
		</tr>
		<tr>
			<td class="left bold" colspan="2">※도로명, 건물명, 지번, 동명에 대해통합검색이 가능합니다.</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%; background-color:WHITE;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">우편번호</th>
			<th class="head">주소</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="top" colspan="2">
				<div id="ID_POST_LIST" style="overflow-x:hidden; overflow-y:scroll; height:391px; background-color:WHITE;">
					<table class="my_table" style="width:100%; background-color:WHITE;">
						<colgroup>
							<col width="70px">
							<col>
						</colgroup>
						<tbody></tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="center" colspan="2" id="ID_PAGE_LIST"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once('../inc/_db_close.php');
?>