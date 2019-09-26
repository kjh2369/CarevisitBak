<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$memoType='1';
	$orgNo	= $_GET['orgNo'];

	$cgMemo = '<col width="40px"><col width="120px"><col width="120px"><col width="70px"><col width="150px"><col width="70px"><col>';
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfMemo('MemoList');
	});

	window.onunload = function(){
		var html = '작성일시 : '+$('td',$('tr:first',$('tbody',$('#ID_TBL_MEMO_LIST')))).eq(1).text()
				 + ' / 최종수정일시 : '+$('td',$('tr:first',$('tbody',$('#ID_TBL_MEMO_LIST')))).eq(2).text()
				 + ' / 제목 : '+$('td',$('tr:first',$('tbody',$('#ID_TBL_MEMO_LIST')))).eq(4).text()
				 + ' / 작성자 : '+$('td',$('tr:first',$('tbody',$('#ID_TBL_MEMO_LIST')))).eq(5).text()
				 + '<br>'+$('#ID_CELL_MEMO_0').html();

		$('#divMemo',opener.document).html(html);
	}

	function lfMemo(gbn,para){
		var url = '', data = {};

		if (gbn == 'MemoList'){
			url = 'center_memo_list.php';
		}else if (gbn == 'MemoReg'){
			url = 'center_memo_reg.php';
		}else if (gbn == 'MemoSave'){
			url = 'center_memo_save.php';
		}else if (gbn == 'MemoView'){
			url = 'center_memo_view.php';
		}else if (gbn == 'MemoDel'){
			url = 'center_memo_del.php';
		}

		if (!url) return;

		data['orgNo'] = '<?=$orgNo;?>';

		if (gbn == 'MemoSave'){
			data['subject'] = $('#txtMemoSubject').val();
			data['regNm'] = $('#cboMemoReg').val();
			data['contents'] = $('#txtMemoContents').val();

			if (!data['subject']){
				alert('제목을 입력하여 주십시오.');
				$('#txtMemoSubject').focus();
				return;
			}

			if (!data['contents']){
				alert('내용을 입력하여 주십시오.');
				$('#txtMemoContents').focus();
				return;
			}
		}

		if (gbn != 'MemoList'){
			try{
				data['seq'] = para['seq'] ? para['seq'] : '';
			}catch(e){
				data['seq'] = '';
			}

			if (!data['seq'] && gbn == 'MemoView') return;
		}

		$.ajax({
			type:'POST'
		,	url:url
		,	data:data
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				if (gbn == 'MemoList'){
					html = '<table class="my_table" style="width:100%;">'
						 + '<colgroup><?=$cgMemo;?></colgroup>'
						 + '<tbody>'+html+'</tbody>'
						 + '</table>';
				}else if (gbn == 'MemoSave' || gbn == 'MemoDel'){
					$('#tempLodingBar').remove();
					lfMemo('MemoList');
					return;
				}

				$('#ID_TBL_MEMO_LIST').html(html);
				$('input:text, textarea',$('#ID_TBL_MEMO_LIST')).each(function(){
					__init_object(this);
				});

				$('#ID_BODY_MEMO').show();
				$('#tempLodingBar').remove();

				if (gbn == 'MemoReg'){
					$('#txtMemoSubject').focus();
				}
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}
</script>
<div class="title title_border">메모관리</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="120px">
		<col width="120px">
		<col width="70px">
		<col width="150px">
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<thead>
			<tr>
				<th class="head">No</th>
				<th class="head">작성일시</th>
				<th class="head">최종수정일시</th>
				<th class="head">수정횟수</th>
				<th class="head">제목</th>
				<th class="head">작성자</th>
				<th class="head last">
					<div style="float:right; width:auto; margin-right:5px;"><span class="btn_pack small"><button onclick="lfMemo('MemoReg');">작성</button></span></div>
					<div style="float:center; width:auto; padding-top:1px;">비고</div>
				</th>
			</tr>
		</thead>
	</thead>
</table>
<div id="ID_TBL_MEMO_LIST" style=" position:absolute; overflow-x:hidden; overflow-y:scroll; height:526px;"></div>
<?
	include_once('../inc/_footer.php');
?>