<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:radio[name="optOrder"]').unbind('click').bind('click',function(){
			lfSearch();
		});
		
		
		$('#cboDipoYn').change(function(){	
			lfSearch();
		});
		
		$('#cboUseYn').change(function(){	
			lfSearch();
		});


		setTimeout('lfSearch()', 200);
	});

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./report2014_request_search.php'
		,	data :{
				'orderBy':$('input:radio[name="optOrder"]:checked').val(),
				'dipoYn': $('#cboDipoYn option:selected').val(),
				'useYn': $('#cboUseYn option:selected').val(),
				'orgNo' : $('#txtOrgNo').val(),
				'orgNm' : $('#txtOrgNm').val()	
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#tbodyList').html(html);
				__init_form(document.f);

			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSave(){
		
		var data = {};
		var request = '';
		var no = 0;


		$('#tbodyList tr').each(function(){
			
			request += 'dipo_pay='+$('#txtDipopay_'+no).val();
			request += '&dipo_yn='+$('input:radio[name="optDipoyn_'+no+'"]:checked').val();
			request += '&use_yn='+$('input:radio[name="optUseyn_'+no+'"]:checked').val();
			request += '&code='+$('#code_'+no).val();
			request += '&seq='+$('#seq_'+no).val();
			request += String.fromCharCode(11);
			
			no ++;

		});
	
		data['request'] = request;

		$('input:hidden').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			if (!val) val = '';

			data[id] = val;
		});
		
	
		$.ajax({
			type :'POST'
		,	url  :'./report2014_request_ok.php'
		,	data :data
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
				}else if (result == 9){
					alert('데이타 처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function deposit_chk(obj,chk,idx){
			
		if(obj.checked == true){
			
				if(chk == 'Y'){
					if($('#txtDipopay_'+idx).val() == '0'){
						$('#txtDipopay_'+idx).val('110,000');	
						var pay	= '110000';
					}
				}else {
					$('#txtDipopay_'+idx).val('0');
					var pay	= '-110000';
				}
			
		
			$('#lblTotalPay').text(number_format(__str2num($('#lblTotalPay').text()) + __str2num(pay)));
			$('#txtTotalPay').val(number_format(__str2num($('#lblTotalPay').text()) + __str2num(pay)));
			$('#optUseyn_'+chk+'_'+idx).attr("checked", "checked");
		
		}
	}
	
	function deposit_tot(obj){
		
		obj.value = number_format(obj.value);

		$('#lblTotalPay').text(number_format((__str2num($('#lblTotalPay').text()) + __str2num(obj.value))));
	}
	
	//천 단위 표시
	function number_format(num){
		var num_str = num.toString();
		var result = "";

		for(var i=0; i<num_str.length; i++){
			var tmp = num_str.length - (i+1);

			if(((i%3)==00) && (i!=0)) result = ',' + result;
			result = num_str.charAt(tmp) + result;
		}

		return result;
	}
	
	function lfExcel(){
		$('#txtOrder').val($('input:radio[name="optOrder"]:checked').val());

		document.f.action = 'report2014_request_excel.php'; 
		document.f.submit();
	}

	
	

</script>
<form name="f" method="post">
<div class="title title_border">평가자료 신청내역</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col>
		<col width="250px">
	</colgroup>
	<tbody>
		<tr>
			<td class="bottom">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="60px">
						<col width="50px">
						<col width="50px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="center">기관코드</th>
							<td><input id="txtOrgNo" type="text"></td>
							<th class="center">기관명</th>
							<td><input id="txtOrgNm" type="text"></td>
						</tr>
				</table>
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="50px">
						<col width="200px">
						<col width="70px">
						<col width="60px">
						<col width="70px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="center">정렬</th>
							<td class="center">
								<label><input id="optOrder1" name="optOrder" type="radio" value="1" class="radio">최근 신청일자순</label>
								<label><input id="optOrder2" name="optOrder" type="radio" value="2" class="radio">기관명순</label>
							</td>
							<th class="center">입금구분</th>
							<td class="center">
								<select id="cboDipoYn" name="cboDipoYn" style="width:auto;">
									<option value="all" <?if($cboDipoYn == 'all'){?>selected<?}?>>전체</option>
									<option value="Y" <?if($cboDipoYn == 'Y'){?>selected<?}?>>입금</option>
									<option value="N" <?if($cboDipoYn == 'N'){?>selected<?}?>>미입금</option>
								</select>
							</td>
							<th class="center">사용구분</th>
							<td class="center">
								<select id="cboUseYn" name="cboUseYn" style="width:auto;">
									<option value="all" <?if($cboUseYn == 'all'){?>selected<?}?>>전체</option>
									<option value="Y" <?if($cboUseYn == 'Y'){?>selected<?}?>>사용</option>
									<option value="N" <?if($cboUseYn == 'N'){?>selected<?}?>>미사용</option>
								</select>
							</td>				
							<input name="txtOrder" id="txtOrder" type="hidden" value="">
						</tr>
					</tbody>
				</table>
			</td>
			<td class="left last">
				<span class="btn_pack m"><button onclick="lfSearch();">조회</button></span>
				<span id="btnSave" class="btn_pack m" ><span class="save"></span><button onclick="lfSave();">저장</button></span>
				<span id="btnExcel" class="btn_pack m" ><span class="excel"></span><button onclick="lfExcel();">엑셀</button></span>
			</td>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="120px">
		<col width="180px">
		<col >
		<col width="70px">
		<col width="120px">
		<col width="120px">
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">신청일시</th>
			<th class="head">기관명</th>
			<th class="head">주소</th>
			<th class="head">입금액</th>
			<th class="head">입금여부</th>
			<th class="head last">사용여부</th>
		</tr>
	</thead>
	<tbody id="tbodyList">
		<tr>
			<td class="center last" colspan="5">::검색된 데이타가 없습니다.::</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last">&nbsp;</td>
		</tr>
	</tfoot>
</table>
</form>
<?
	include_once('../inc/_db_close.php');
?>