<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
?>
<script type="text/javascript">
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;
		opener.result = false;
		opener.html = '';

		if (opener.type == 'data'){
			$('#lblTitle').text('자료실생성');
		}else{
			self.close();
		}

		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type:'POST',
			url:'./category_add_search.php',
			data:{
				'type':opener.type
			,	'cd':opener.cd
			},
			beforeSend: function (){
			},
			success:function(html){
				$('#divBody').html(html);
				$('input:text').each(function(){
					__init_object(this);
				});
				$('#txtName').focus();
			},
			error: function (request, status, error){
				alert('[ERROR]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		}).responseXML;
	}

	function lfSave(){
		$.ajax({
			type:'POST',
			url:'./category_add_save.php',
			data:{
				'type':opener.type
			,	'cd':opener.cd
			,	'name':$('#txtName').val()
			,	'parent':opener.parent
			,	'seq':opener.seq
			},
			beforeSend: function (){
			},
			success:function(result){
				if (result > 0){
					opener.result = true;

					if (opener.parent > 0){
						opener.html = '	<tr>'
									+ '	<td class="center"><div class="left">'+$('#txtName').val()+'</div></td>'
									+ '	<td class="center last"></td>'
									+ '	</tr>';
					}else{
						opener.html = '	<tr>'
									+ '	<td class="center">-</td>'
									+ '	<td class="center"><div class="left">'+$('#txtName').val()+'</div></td>'
									+ '	<td class="center"><span class="btn_pack small"><button onclick="lfMstAdd(\'0\',\''+result+'\',\'1\')">게시판생성</button></span></td>'
									+ '	<td class="center last"></td>'
									+ '	</tr>';
					}
					lfClose();
				}else{
					alert('데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}
			},
			error: function (request, status, error){
				alert('[ERROR]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		}).responseXML;
	}

	function lfClose(){
		self.close();
	}
</script>
<div id="lblTitle" class="title title_border"></div>
<div id="divBody"></div>
<?
	include_once('../inc/_footer.php');
?>