<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();

		$('input:radio[name^="optYn"]').unbind('click').bind('click',function(){
			$.ajax({
				type:'POST'
			,	url:'./center_<?=$menuId?>_set.php'
			,	data:{
					'year':$('#lblYYMM').attr('year')
				,	'month':$(this).attr('name').replace('optYn','')
				,	'yn':$(this).val()
				}
			,	beforeSend:function(){
				}
			,	success:function(result){
					if (result) alert(result);
				}
			,	error: function (request, status, error){
				}
			});
		});
	});

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./center_<?=$menuId?>_search.php'
		,	data:{
				'year':$('#lblYYMM').attr('year')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var val = __parseVal(data);

				for(var i in val){
					$('input:radio[name="optYn'+i+'"][value="'+val[i]+'"]').attr('checked',true);
				}

				$('#tempLodingBar').remove();
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

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="100px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">청구년월 기준</th>
			<td class="last">
				<div class="left" style="padding-top:2px;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),-1); lfSearch();" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYYMM" year="<?=$year;?>"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),1); lfSearch();" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<div class="my_border_blue" style="margin:10px;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="7%">
			<col width="26%">
			<col width="7%">
			<col width="26%">
			<col width="7%">
			<col width="26%">
		</colgroup>
		<tbody><?
			for($i=1; $i<=12; $i++){
				if ($i % 3 == 1){?>
					<tr><?
				}?>
				<th class="center bold"><?=$i;?>월</th>
				<td>
					<label><input name="optYn<?=$i;?>" type="radio" class="radio" value="Y">마감설정</label>
					<label><input name="optYn<?=$i;?>" type="radio" class="radio" value="N">마감해제</label>
				</td><?
				if ($i % 3 == 0){?>
					</tr><?
				}
			}?>
		</tbody>
	</table>
</div>