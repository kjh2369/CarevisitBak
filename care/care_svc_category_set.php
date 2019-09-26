<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
?>
<script type="text/javascript">
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;
		opener.count = 0;

		lfResize();
		lfCategoryFullname();
		lfCategoryList('A');
		lfCategoryList('B');
	});

	function lfResize(){
		var h = __GetHeight($('#ID_LIST_A'));

		$('#ID_LIST_A, #ID_LIST_B').height(h - 27);
	}

	function lfCategoryFullname(){
		$.ajax({
			type :'POST'
		,	url  :'./care_svc_category_fun.php'
		,	data :{
				'SR'	:opener.SR
			,	'gbn'	:'FULLNAME'
			,	'code'	:opener.code
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				$('#ID_CATEGORY_NAME').text(data);
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfCategoryList(gbn){
		$.ajax({
			type :'POST'
		,	url  :'./care_svc_category_fun.php'
		,	data :{
				'SR'	:opener.SR
			,	'gbn'	:'LIST_'+gbn
			,	'code'	:opener.code
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#ID_LIST_'+gbn).html(html);
				$('div[id^="ID_LIST_'+gbn+'_"]').unbind('mouseover').bind('mouseover',function(){
					if ($(this).attr('selYn') == 'Y') return;
					$(this).css('background-color','EAEAEA');
				}).unbind('mouseout').bind('mouseout',function(){
					if ($(this).attr('selYn') == 'Y') return;
					$(this).css('background-color','FFFFFF');
				}).unbind('click').bind('click',function(){
					if ($(this).attr('selYn') == 'Y'){
						$(this).attr('selYn','N').css('background-color','FFFFFF');
					}else{
						$(this).attr('selYn','Y').css('background-color','FAF4C0');
					}
				});

				opener.count = $('div[id^="ID_LIST_A_"]').length;
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfCategorySet(gbn){
		$('div[id^="ID_LIST_'+gbn+'_"][selYn="Y"]').each(function(){
			var This = this;
			$.ajax({
				type :'POST'
			,	async:false
			,	url  :'./care_svc_category_fun.php'
			,	data :{
					'SR'	:opener.SR
				,	'gbn'	:'SET_'+gbn
				,	'suga'	:$(this).attr('cd')
				,	'seq'	:$(this).attr('seq')
				,	'code'	:opener.code
				}
			,	beforeSend:function(){
				}
			,	success:function(result){
					$(This).remove();
				}
			,	error:function(){
				}
			}).responseXML;
		});

		if (gbn == 'A'){
			lfCategoryList('B');
		}else{
			lfCategoryList('A');
		}

		$('div[id^="ID_LIST_'+gbn+'_"]:first').css('border-top','none');
	}

	function lfCategoryAll(gbn){
		$('div[id^="ID_LIST_'+gbn+'_"]').attr('selYn','Y').css('background-color','FAF4C0');
	}
</script>
<div class="title title_border">카테고리 목록관리</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head">카테고리명</th>
			<td class="left" id="ID_CATEGORY_NAME"></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="50%" span="2">
	</colgroup>
	<thead>
		<tr>
			<th class="head">등록 리스트</th>
			<th class="head">미등록 리스트</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				<div id="ID_LIST_A" style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll; padding:0 5px 0 5px;"></div>
			</td>
			<td>
				<div id="ID_LIST_B" style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll; padding:0 5px 0 5px;"></div>
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td>
				<div style="float:right; width:auto; margin-right:10px;">
					<span class="btn_pack small"><button onclick="lfCategorySet('A');">미등록</button></span>
				</div>
				<div style="float:right; width:auto; margin-right:5px;">
					<span class="btn_pack small"><button onclick="lfCategoryAll('A');">전체선택</button></span>
				</div>
			</td>
			<td>
				<div style="float:left; width:auto; margin-left:10px;">
					<span class="btn_pack small"><button onclick="lfCategorySet('B');">등록</button></span>
				</div>
				<div style="float:left; width:auto; margin-left:5px;">
					<span class="btn_pack small"><button onclick="lfCategoryAll('B');">전체선택</button></span>
				</div>
			</td>
		</tr>
	</tfoot>
</table>
<?
	include_once('../inc/_footer.php');
?>