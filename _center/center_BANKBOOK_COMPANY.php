<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	/*
	 *	본사계좌관리
	 */
?>
<script type="text/javascript">
	/*
	function lfReg(acctNo){
		var width = 250;
		var height = 330;
		var left = window.screenLeft + $('#left_box').width();
		var top = window.screenTop + $('#divTitle').offset().top;
		var target = '<?=$menuFile;?>_REG';
		var option = 'left='+left+'px, top='+top+'px, width='+width+'px,height='+height+'px,scrollbars=no,status=no,resizable=no';
		var url = './center_<?=$menuFile;?>_reg.php';
		var win = window.open('about:blank', target, option);
			win.opener = self;
			win.focus();

		if (!acctNo) acctNo = '';

		var parm = new Array();
			parm = {
				'acctNo':acctNo
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

		form.setAttribute('target', target);
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}
	*/

	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
		});

		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./center_<?=$menuId?>_search.php'
		,	data:{
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#ID_LIST').html(html);
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

	function lfSave(){
		if (!$('#txtBankNm').val()){
			alert('은행명을 입력하여 주십시오.');
			$('#txtBankNm').focus();
			return;
		}

		if (!$('#txtBankNo').val()){
			alert('계좌번호를 입력하여 주십시오.');
			$('#txtBankNo').focus();
			return;
		}

		if (!$('#txtBankAcct').val()){
			alert('예금주를 입력하여 주십시오.');
			$('#txtBankAcct').focus();
			return;
		}

		$.ajax({
			type:'POST'
		,	url:'./center_<?=$menuId?>_save.php'
		,	data:{
				'bankNm'	:$('#txtBankNm').val()
			,	'bankNo'	:$('#txtBankNo').val()
			,	'bankAcct'	:$('#txtBankAcct').val()
			,	'bankGbn'	:$('#cboBankGbn').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (!result){
					$('input:text').each(function(){
						$(this).val('');
					});
					$('#cboBankGbn').val('1');
					lfSearch();
				}else{
					alert(result);
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
		<col width="40px">
		<col width="100px">
		<col width="150px">
		<col width="100px">
		<col width="50px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">은행명</th>
			<th class="head">계좌번호</th>
			<th class="head">예금주</th>
			<th class="head">계좌구분</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center">-</td>
			<td><input id="txtBankNm" type="text" value="" style="width:100%;"></td>
			<td><input id="txtBankNo" type="text" value="" style="width:100%;"></td>
			<td><input id="txtBankAcct" type="text" value="" style="width:100%;"></td>
			<td>
				<select id="cboBankGbn" style="width:auto;">
					<option value="1">개인</option>
					<option value="2">법인</option>
				</select>
			</td>
			<td class="last">
				<div class="left"><span class="btn_pack small"><button onclick="lfSave();">저장</button></span></div>
			</td>
		</tr>
	</tbody>
	<tbody id="ID_LIST">
		<tr>
			<td class="center last" colspan="6">::검색된 데이타가 없습니다.::</td>
		</tr>
	</tbody>
</table>