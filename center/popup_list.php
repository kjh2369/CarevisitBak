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

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./popup_list_search.php'
		,	data :{
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfModify(popId){
		var width = 1024;
		var height = 768;
		var left = (screen.availWidth - width) / 2;
		var top = (screen.availHeight - height) / 2;

		var option = 'left='+left+', top='+top+', width='+width+',height='+height+',scrollbars=no,status=no,resizable=no';
		var win = window.open('', 'POPUP_REG', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'popId':popId
			,	'popup':true
			};

		var form = document.createElement('form');
		var objs;
		for(var key in parm){
			objs = document.createElement('input');
			objs.setAttribute('type', 'hidden');
			objs.setAttribute('name', key);
			objs.setAttribute('value', parm[key]);

			form.appendChild(objs);
		}

		form.setAttribute('target', 'POPUP_REG');
		form.setAttribute('method', 'post');
		form.setAttribute('action', './popup.php');

		document.body.appendChild(form);

		form.submit();
	}

	function lfDelete(popId){
		if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type :'POST'
		,	url  :'./popup_list_delete.php'
		,	data :{
				'popId':popId
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (__resultMsg(result)){
					lfSearch();
				}
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div class="title title_border">팝업조회</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="60px">
		<col width="60px">
		<col width="75px">
		<col width="400px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">등록일</th>
			<th class="head">기관수</th>
			<th class="head">사용기간</th>
			<th class="head">내용</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="tbodyList"></tbody>
	<tfoot>
		<td class="bottom last"></td>
	</tfoot>
</table>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>