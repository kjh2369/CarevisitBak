<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_body_header.php");

	$year = $_POST['year'] != '' ? $_POST['year'] : date('Y');
?>
<script type="text/javascript" src="../yoyangsa/mem.js"	></script>
<script type="text/javascript">
	$(document).ready(function(){
		setTimeout('lfSearch()',200);
		
		//레이어창 닫힌 후 새로고침
		$('#divPopupBody').focus(function(){
			location.reload();
		}); 
		
	});
	
	function moveYear(pos){
		$('#m_year').text(parseInt($('#m_year').text()) + pos);
		//showTaxView();
	}

	function showTaxView(obj, ssn){
		var pos = $(obj).parent();
		var w = 170;
		var h = 200;
		var t = pos.offset().top + pos.height();
		var l = pos.offset().left - 3;
		var limitY = document.body.offsetHeight + document.body.scrollTop;
		
		if ((t+h) > limitY)
			t = pos.offset().top - h;
		
		$.ajax({
			type: 'POST',
			url : './init_tax_view.php',
			data: {
				'year':$('#year').val()
			,	'ssn':ssn	
			},
			beforeSend: function (){
			},
			success: function (html){
				$('#divPopupLayer').html(html);
				$('#divPopupLayer').css('border','2px solid #13970f').css('left',l).css('top',t).css('width',w).css('height',h).css('margin','0').css('padding','0').show();
			},
			error: function (){
				
			}
		}).responseXML;
		
	}
	
	function hideTaxView(){
		$('#divPopupLayer').hide();
	}

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./mem_4insu_list.php'
		,	beforeSend: function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	data:{
			}
		,	success: function(data){
				var row = data.split(String.fromCharCode(11));
				var html = '';
				var no = 1;

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseStr(row[i]);

						html += '<tr>';
						html += '<td class="center">'+no+'</td>';
						
						if('<?=$debug?>'=='1'){
							//html += '<td class="center"><a href="#" onclick="showTaxView(this, \''+col['jumin']+'\'); return false;">'+col['name']+'</a></td>';
							html += '<td class="center">'+col['name']+'</td>';
						}else {
							html += '<td class="center">'+col['name']+'</td>';
						}
						html += '<td class="center">'+col['a']+'</td>';
						html += '<td class="center">'+col['h']+'</td>';
						html += '<td class="center">'+col['e']+'</td>';
						html += '<td class="center">'+col['s']+'</td>';
						html += '<td class="center">'+col['p']+'</td>';
						html += '<td class="right">'+__num2str(col['pay'])+'</td>';
						html += '<td class="center">'+__getDate(col['f'],'.')+'</td>';
						html += '<td class="center">'+__getDate(col['t'],'.')+'</td>';
						html += '<td class="left last">';
						html += '<span class="btn_pack m"><a href="#" onclick="salaryMemInsPopup(this, \''+col['jumin']+'\'); return false;">수정</a></span> ';
						html += '<span class="btn_pack m"><a href="#" onclick="lfDelete($(this).parent().parent().parent(),\''+col['jumin']+'\',\''+col['seq']+'\'); return false;">삭제</a></span>';
						html += '</td>';
						html += '</tr>';

						no ++;
					}
				}

				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();
			}
		,	error: function (){
			}
		}).responseXML;
	}

	function lfDelete(obj,jumin,seq){
		$.ajax({
			type:'POST'
		,	url:'./mem_4insu_delete.php'
		,	beforeSend: function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	data:{
				'jumin':jumin
			,	'seq':seq
			}
		,	success: function(result){
				if (result == 1){
					$(obj).remove();
				}else if (result == 9){
					alert('데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
				$('#tempLodingBar').remove();
			}
		,	error: function (){
			}
		}).responseXML;
	}


	/*********************************************************

		직원별 4대보험

	*********************************************************/
	function salaryMemInsPopup(obj, jumin){
		var body = $('#divPopupBody');
		var cont = $('#divPopupLayer');
		
		var w  = $(document).width();
		var h  = $(document).height();
		today = new Date();
		
		var target	= __getObject(obj);
		var x		= __getObjectLeft(target);
		var y		= __getObjectTop(target);
	
		$.ajax({
			type: 'POST'
		,	url : '../salaryNew/salary_edit_ins.php'
		,	beforeSend: function(){
				$('#loadingBody').after('<div id=\'tempLodingBar\' style=\'position:absolute; top:270px; left:170; text-align:center;\'><div style=\'width:250px; height:100px; padding-top:30px; border:2px solid #cccccc; background-color:#ffffff;\'>'+__get_loading()+'</div></div></center></div>');
			}
		,	data: {
				'code'     : '<?=$_SESSION[userCenterCode]?>'
			,	'year'     : today.getYear()
			,	'month'    : today.getMonth()<10?'0'+today.getMonth():today.getMonth()
			,	'jumin'    : jumin
			,	'func'	   : 'search'
			,	'salaryYN' : 'Y'
			}
		,	success: function(html){
				$('#tempLodingBar').remove();
				
				if (x + 213 >= 1024){
					var l = 1024 - 213;
				}else{
					var l = x + 3;
				}

				var t  = y + target.offsetHeight + 4;
				
				body.css('position', 'absolute').css('width', w).css('height', h).show();
				cont.css('width', 'auto')
					.css('margin', 0).css('padding', 0).css('border','none')
					.css('left', l).css('top', t )
					.css('cursor', 'default')
					.html(html)
					.show();
				
				$('#jumin').val(jumin);
			
				memInsuFind(jumin);
				memInsuMonthlyFind(jumin);
				
			}
		,	complete: function(html){
			}
		,	error: function (){
			}
		}).responseXML;
	}
	
	/*********************************************************
	 *	현재 보험 내역 조회
	 *********************************************************/
	function memInsuFind(jumin){
		$.ajax({
			type: 'POST'
		,	url : '../yoyangsa/mem_his_insu_list.php'
		,	beforeSend: function(){
			}
		,	data: {
				'jumin':jumin
			,	'seq':'MAX'
			}
		,	success: function(data){
				if (!data) return;
				var col = __parseStr(data);

				$('#lblInsuAnnuityYn').text(col['a']);
				$('#lblInsuHealthYn').text(col['h']);
				$('#lblInsuEmployYn').text(col['e']);
				$('#lblInsuSanjeYn').text(col['s']);
				$('#lblInsuPAYEYn').text(col['p']);
				$('#lblInsuFrom').text(__getDate(col['from'],'.'));
				$('#lblInsuTo').text(__getDate(col['to'],'.'));
			}
		,	error:function(request, status, error){
				alert('[ERROR No.03]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
				
				return false;
			}
		}).responseXML;
	}

	/*********************************************************
	 *	보수신고급여이력
	 *********************************************************/
	function memInsuHisMonthly(jumin){
		var objModal = new Object();
		var url = '../yoyangsa/mem_his_insu_monthly.php';
		var style = 'dialogWidth:300px; dialogHeight:400px; dialogHide:yes; scroll:no; status:no';
		
		if (!jumin){
			alert('기준근로시간/시급정보는 데이타 저장 후 변경 가능합니다.');
			return;
		}

		objModal.jumin = jumin;
		objModal.result = 9;
		
		window.showModalDialog(url, objModal, style);
		
		try{
			$('#lblInsuMonthly').text(objModal.pay);
			$('#lblInsuYYMM').text(objModal.yymm.split('-').join('.'));
		}catch(e){
		}
	}

	/*********************************************************
	 *	현재 보수신고급여 내역 조회
	 *********************************************************/
	function memInsuMonthlyFind(jumin){
		$.ajax({
			type: 'POST'
		,	url : '../yoyangsa/mem_his_insu_monthly_list.php'
		,	beforeSend: function(){
			}
		,	data: {
				'jumin':jumin
			,	'yymm':'NOW'
			}
		,	success: function(data){
				if (!data) return;
				var col = __parseStr(data);

				$('#lblInsuMonthly').text(col['pay']);
				$('#lblInsuYYMM').text(col['ym']);
			}
		,	error:function(request, status, error){
				alert('[ERROR No.03]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
				
				return false;
			}
		}).responseXML;
	}

</script>
<div class="title title_border">
	<div style="float:left; width:auto;">4대보험 가입내역</div>
	<div style="float:right; width:auto; color:red;">※4대보험가입 대상자가 아닌경우 삭제하여 주십시오.</div>
</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="50px" span="5">
		<col width="70px" span="3">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">성명</th>
			<th class="head">국민</th>
			<th class="head">건강</th>
			<th class="head">고용</th>
			<th class="head">산재</th>
			<th class="head">원천징수</th>
			<th class="head">월보수액</th>
			<th class="head">적용일</th>
			<th class="head">종료일</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="tbodyList"></tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
	<input type="hidden" id="year" name="year" value="<?=$year?>">
	<input type="hidden" id="ssn" name="ssn" value="">
	<input type="hidden" id="jumin"   name="jumin" value="">
</table>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>