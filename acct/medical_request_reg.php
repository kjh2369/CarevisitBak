<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');


	$orgNo = $_POST['orgNo'];
	$seq    = $_POST['seq'];
	
	
	$sql = ' select *
			   from medical_request
			  where org_no = \''.$orgNo.'\'
			    and seq    = \''.$seq.'\'';
	$mst = $conn -> get_array($sql);
	
	$counselDt = $mst['counsel_dt'] != '' ? $mst['counsel_dt'] : date('Ymd');

?>
<script type="text/javascript" src="../js/script.js"></script>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
		});

		var top = 0; //$('#divWorkLog').offset().top;
		var height = $(this).height();
		var h = 0; //height - top - 3;

		//$('#divWorkLog').height(h);

		top = $('#divLogBody').offset().top;
		h = height - top - $('#divBtnBody').height() - 3;

		$('#divLogBody').height(h);
	});

	//저장
	function lfSave(){
		
		
		var data = {};
		
		$('input:text').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			if (!val) val = '';
			data[id] = val;
		});
		
		$('input:hidden').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			if (!val) val = '';
			
			data[id] = val;
		});
		
		$('input:radio').each(function(){
			var name= $(this).attr('name');
			var val	= $('input:radio[name="'+name+'"]:checked').val();

			if (!val) val = '';

			data[name] = val;
			
		});
		
		$('textarea').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();
			
			data[id] = val;
		});

		

		$.ajax({
			type:'POST'
		,	url:'./medical_request_save.php'
		,	data:data
		,	beforeSend:function(){
			}
		,	success:function(result){
				var result = result.split('//');
		
				if (result[0] == 1){
					alert('정상적으로 처리되었습니다.');
					opener.lfSearch();
				}else if (result[0] == 9){
					alert('데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	complite:function(result){
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<input name="orgNo" id="orgNo" type="hidden" value="<?=$mst['org_no'];?>">
<input name="seq" id="seq" type="hidden"  value="<?=$mst['seq'];?>">

<div class="title title_border">의료기관 신청내역</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center" colspan="2">문의내용</th>
		</tr>
		<tr>
			<td colspan="2">
				<div style="width:100%; height:150px; overflow-x:hidden; overflow-y:auto;">
				<table class="my_table" style="width:100%;">
					<tbody>
						<tr>
							<td class="left"><?=nl2br($mst['request_cont']);?></td>
						</tr>
					</tbody>
				</table>
				</div>
			</td>
		</tr>
		<tr>
			<th class="center">상담일자</th>
			<td class="left"><input id="counselDt" name="counselDt" type="text" class="date" value="<?=$myF->dateStyle($counselDt);?>"></td>
		</tr>
		<tr>
			<th class="center">상담내용</th>
			<td class="left" ><textarea id="counselCont" name="counselCont" style="width:100%; height:80px;"><?=stripslashes($mst['counsel_cont']);?></textarea></td>
		</tr>
		<tr>
			<th class="center">완료여부</th>
			<td class="left">
				<label><input id="completeYn_1" name="completeYn" type="radio" class="radio" value="Y" <?=($mst['complete_yn'] == 'Y' ? 'checked' : '');?>>완료</label>&nbsp;&nbsp;
				<label><input id="completeYn_2" name="completeYn" type="radio" class="radio" value="N" <?=($mst['complete_yn'] == 'N' ? 'checked' : '');?>>미완료</label>&nbsp;&nbsp;
			</td>
		</tr>
		<!--tr>
			<th class="center">취소여부</th>
			<td class="left">
				<label><input id="cancelYn_1" name="cancelYn" type="radio" class="radio" value="Y" <?=($mst['cancel_yn'] == 'Y' ? 'checked' : '');?>>신청</label>&nbsp;&nbsp;
				<label><input id="cancelYn_2" name="cancelYn" type="radio" class="radio" value="N" <?=($mst['cancel_yn'] == 'N' ? 'checked' : '');?>>신청취소</label>&nbsp;&nbsp;
			</td>
		</tr-->
		
	</tbody>
</table>
<div id="divBtnBody" class="center" style="height:30px; margin-top:10px; ">
	<span class="btn_pack m"><button onclick="lfSave();">수정</button></span>
</div>
<?
	include_once('../inc/_footer.php');
?>