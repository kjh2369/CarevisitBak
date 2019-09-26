<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_body_header.php');
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	function lfMstAdd(obj,cd,parent,seq){
		var objModal = new Object();
		var url = './category_add.php';
		var style = 'dialogWidth:300px; dialogHeight:200px; dialogHide:yes; scroll:no; status:no';

		objModal.type = 'data';
		objModal.cd = (cd ? cd : '0');
		objModal.parent = (parent ? parent : '0');
		objModal.seq = (seq ? seq : '1');

		window.showModalDialog(url, objModal, style);

		if (objModal.result){
			/*
			if (objModal.parent > 0){
				obj = __GetTagObject(obj,'TR');

				var rowspan = __str2num($('td',obj).eq(0).attr('rowSpan'))+1;

				$('td',obj).eq(0).attr('rowSpan',rowspan);
				$('td',obj).eq(1).attr('rowSpan',rowspan);
				$(obj).after(objModal.html);
			}else{
				$('tr:first',$('#tbodyList')).before(objModal.html);
			}
			*/
			lfSearch();
		}
	}

	function lfSearch(){
		$.ajax({
			type:'POST',
			url:'./category_search.php',
			data:{
				'type':'data'
			},
			beforeSend: function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			},
			success:function(html){
				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();

				lfSearchSub();
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

	function lfSearchSub(){
		$('tr',$('#tbodyList')).each(function(){
			var cd = $(this).attr('cd');

			$.ajax({
				type:'POST',
				async:false,
				url:'./category_search.php',
				data:{
					'type':'data'
				,	'parent':cd
				},
				beforeSend: function (){
					$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
				},
				success:function(html){
					var cnt = html.split('</tr>').length;

					$('td',$('tr[cd="'+cd+'"]',$('#tbodyList'))).eq(0).attr('rowSpan',cnt);
					$('td',$('tr[cd="'+cd+'"]',$('#tbodyList'))).eq(1).attr('rowSpan',cnt);
					$('tr[cd="'+cd+'"]',$('#tbodyList')).after(html);
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
		});

		$('input:text').each(function(){
			__init_object(this);
		});
	}

	function lfSet(mode,cd,val){
		$.ajax({
			type:'POST',
			url:'./category_set.php',
			data:{
				'type':'data'
			,	'mode':mode
			,	'cd':cd
			,	'val':val
			},
			beforeSend: function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			},
			success:function(result){
				$('#tempLodingBar').remove();

				if (__resultMsg(result)){
				}
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
</script>
<div class="title title_border">
	<div style="float:left; width:auto;">자료실 관리</div>
	<div style="float:right; width:auto; margin-top:9px;"><span class="btn_pack m"><span class="add"></span><button onclick="lfMstAdd();">자료실생성</button></span></div>
</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="150px">
		<col width="150px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">분류명</th>
			<th class="head">게시판</th>
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
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>