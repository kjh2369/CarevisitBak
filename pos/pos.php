<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_body_header.php');

	$type = $_GET['type'];
?>
<script type="text/javascript">
	$(document).ready(function(){
		__init_form(document.f);
		setTimeout('lfSearch()',10);
	});

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./pos_search.php'
		,	data:{
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var row		= data.split(String.fromCharCode(1));
				var idx		= 1;
				var html	= '';

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col	= row[i].split(String.fromCharCode(2));

						html	+= '<tr class="clsRemove">';
						html	+= '<th class="center">'+idx+'</th>';
						html	+= '<td class="center"><input id="txtPos_'+idx+'" name="txt" type="text" style="width:100%;" value="'+col[1]+'"></td>';
						html	+= '<td class="center"><input id="txtSeq_'+idx+'" name="txt" type="text" style="width:100%;" value="'+col[2]+'"></td>';
						html	+= '<td class="center"><span class="btn_pack small"><a href="#" onclick="lfAdd(\''+idx+'\'); return false;">수정</a></span></td>';
						html	+= '<td class="center"><span class="btn_pack small"><a href="#" onclick="lfDelete(\''+idx+'\'); return false;">삭제</a></span></td>';
						html	+= '<td class="center last">';
						html	+= '<input id="txtCd_'+idx+'" type="hidden" value="'+col[0]+'">';
						html	+= '</td>';
						html	+= '</tr>';

						idx ++;
					}
				}

				if (!html){
					//html	= '<tr><td class="center last" colspan="6">::검색된 데이타가 없습니다.::</td></tr>';
				}

				$('.clsRemove').remove();
				$('#bodyPos').html(html);
				$('#tempLodingBar').remove();
				__init_form(document.f);
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfAdd(idx){
		if (!idx){
			var cd	= '';
			var pos	= $('#txtPos').val();
			var seq	= $('#txtSeq').val();

			if (!$('#txtPos').val()){
				alert('직위명을 입력하여 주십시오.');
				$('#txtPos').focus();
				return;
			}
		}else{
			var cd	= $('#txtCd_'+idx).val();
			var pos	= $('#txtPos_'+idx).val();
			var seq	= $('#txtSeq_'+idx).val();

			if (!$('#txtPos_'+idx).val()){
				alert('직위명을 입력하여 주십시오.');
				$('#txtPos').focus();
				return;
			}
		}

		$.ajax({
			type :'POST'
		,	url  :'./pos_add.php'
		,	data:{
				'cd'	:cd
			,	'pos'	:pos
			,	'seq'	:seq
			}
		,	success:function(result){
				if (!idx){
					$('#txtPos').val('');
					$('#txtSeq').val('1');
					setTimeout('lfSearch()',1);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfDelete(idx){
		var cd	= $('#txtCd_'+idx).val();

		if (!cd){
			return;
		}

		$.ajax({
			type :'POST'
		,	url  :'./pos_delete.php'
		,	data:{
				'cd'	:cd
			}
		,	success:function(result){
				setTimeout('lfSearch()',1);
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>

<form id="f" name="f" method="post" enctype="multipart/form-data">

<div class="title">
<div style="width:auto; float:left;">직위관리</div>
<div style="width:100%; padding-top:8px; text-align:right;">예) 실장, 팀장, 과장, 차장, 대리 ...</div>
</div>
<table class="my_table my_border" width="100%">
	<colgroup>
		<col width="40px">
		<col width="150px">
		<col width="50px">
		<col width="70px" span="2">
		<col>
	</colgroup>
	<thead></thead>
	<tbody>
		<tr>
			<th class="head">No</th>
			<th class="head">직위명</th>
			<th class="head">순번</th>
			<th class="head">수정</th>
			<th class="head">삭제</th>
			<th class="head last">비고</th>
		</tr>
		<tr>
			<th class="center">추가</th>
			<td class="center"><input id="txtPos" name="txt" type="text" style="width:100%;" value=""></td>
			<td class="center"><input id="txtSeq" name="txt" type="text" style="width:100%;" value="1"></td>
			<td class="center"><span class="btn_pack small"><a href="#" onclick="lfAdd(); return false;">추가</a></span></td>
			<td class="center last" colspan="2"></td>
		</tr>
	</tbody>
	<tbody id="bodyPos"></tbody>
</table>

</form>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>