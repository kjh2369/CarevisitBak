<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*********************************************************
	 *	서비스계획서
	 *********************************************************/

	$orgNo = $_SESSION['userCenterCode'];
	$orgType = '40';
	
?>
<script type="text/javascript">
	$(document).ready(function(){
		setTimeout('lfSearch()',100);
		lfResize();
	});

	function lfResize(){
		var top = $('#divBody').offset().top;
		var height = $(document).height();

		var h = height - top - 10;

		$('#divBody').height(h);
	}

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./hce_find.php'
		,	data:{
				'type':'<?=$type;?>'
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				var row = data.split(String.fromCharCode(11));
				var html = '';
				var no = row.length - 1;

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseVal(row[i]);
						
						
						html += '<tr style="cursor:default;" onmouseover="this.style.backgroundColor=\'#efefef\';" onmouseout="this.style.backgroundColor=\'#ffffff\';">';
						html += '<td class="center"><a href="../hce/hce_body.php?sr=<?=$sr;?>&type=62&seq='+col['seq']+'">'+no+'회차</a></td>';
						html += '<td class="center">'+__getDate(col['dt'],'.')+'</td>';
						html += '<td class="center">'+col['er']+'</td>';
						html += '<td class="left last"><span class="btn_pack small"><button onclick="location.href=\'../hce/hce_body.php?sr=<?=$sr;?>&type=62&seq='+col['seq']+'&copyYn=Y\';">복사</button></span></td>';
						//html += '<td class="last"></td>';
						html += '</tr>';

						no --;
					}
				}

				$('#tbodyList').html(html);
			}
		,	complite:function(result){
			}
		,	error:function(){
			}
		}).responseXML;
	}



</script>
<?
	$colgroup	= '	<col width="100px">
					<col width="70px">
					<col width="70px">
					<col>';
?>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="right bottom last">
				<span class="btn_pack m"><span class="add"></span><a href="../hce/hce_body.php?sr=<?=$sr;?>&type=62" target="frmBody">신규</a></span>
				<span class="btn_pack m"><span class="pdf"></span><button type="button" onclick="lfPDF('<?=$type;?>','ALL');">전체출력</button></span>
				<span class="btn_pack m"><span class="pdf"></span><button type="button" onclick="lfPDF('<?=$type;?>','LIST');">이력출력</button></span>
			</td>
		</tr>
	</tbody>
</table>
<div class="my_border_blue" style="border-bottom:none;">
	<table class="my_table" style="width:100%;">
		<colgroup><?=$colgroup;?></colgroup>
		<thead>
			<tr>
				<th class="head">회차</th>
				<th class="head">작성일자</th>
				<th class="head">작성자</th>
				<th class="head last">비고</th>
			</tr>
		</thead>
	</table>
</div>

<div id="divBody" class="my_border_blue" style="border-top:none; height:200px; overflow-x:hidden; overflow-y:scroll;">
	<table class="my_table" style="width:100%;">
		<colgroup><?=$colgroup;?></colgroup>
		<tbody id="tbodyList"></tbody>
	</table>
</div>
<?
	include_once('../inc/_db_close.php');
?>