<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$fromDt = $myF->dateStyle(date("Ymd",strtotime("-1 month", time())));
	$toDt = $myF->dateStyle(date('Ymd', mktime()));

?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:radio[name="optOrder"]').unbind('click').bind('click',function(){
			lfSearch();
		});
		
		$('input:text').each(function(){
			__init_object(this);
		});

		setTimeout('lfSearch()', 200);
	});
	
	
	function lfSearch(){
		
		$.ajax({
			type :'POST'
		,	url  :'./medical_request_search.php'
		,	data :{
				'orderBy':$('input:radio[name="optOrder"]:checked').val(),
				'CompleteGbn': $('#cboCompleteGbn option:selected').val(), 
				'CancelGbn': $('#cboCancelGbn option:selected').val(), 
				'orgNo' : $('#txtOrgNo').val(),
				'fromDt':$('#fromDt').val(),
				'toDt':$('#toDt').val()
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
	
	function lfReg(org_no,seq){
		
		var url = './medical_request_reg.php';

		var width = 450;
		var height = 420;
		var top  = (window.screen.height - height) / 2;
		var left = (window.screen.width  - width)  / 2;

		var option = 'left='+left+'px, top='+top+'px, width='+width+'px,height='+height+'px,scrollbars=no,status=no,resizable=no';
		var win = window.open('about:blank', 'MEDICAL_REQUEST_REG', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'orgNo':org_no
			,	'seq':seq
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

		form.setAttribute('target', 'MEDICAL_REQUEST_REG');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}
	
	function lfExcel(){
		//$('#txtOrder').val($('input:radio[name="optOrder"]:checked').val());	

		//document.f.action = 'medical_request_excel.php'; 
		//document.f.submit();
	}

	function lfDel(code,seq){
		
		if (!confirm('삭제된 데이타는 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type :'POST'
		,	url  :'./medical_request_delete.php'
		,	data :{
				'code' : code 
		,		'seq'  : seq	
		}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					lfSearch();
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

</script>
<div class="title title_border">의료기관신청 신청내역</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="bottom">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="60px">
						<col width="205px">
						<col width="60px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<!--th class="center">기관코드</th>
							<td><input name="txtOrgNo" id="txtOrgNo" type="text"></td-->
							<th class="center">조회기간</th>
							<td class="left">
								<input name="fromDt" id="fromDt" type="text" class="date" value="<?=$fromDt;?>"> ~ <input name="toDt" id="toDt" type="text" class="date" value="<?=$toDt;?>">
							</td>
							<th class="center">기관명</th>
							<td class="left last"><input name="txtOrgNm" id="txtOrgNm" type="text"></td>
						</tr>
				</table>
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="60px">
						<col width="205px">
						<col width="60px">
						<col width="60px">
						<col width="60px">
						<col >
					</colgroup>
					<tbody>
						<tr>
							<th class="center">정렬</th>
							<td class="left">
								<label><input id="optOrder1" name="optOrder" type="radio" value="1" class="radio" checked>최근 신청일자순</label>
								<label><input id="optOrder2" name="optOrder" type="radio" value="2" class="radio">기관명순</label>
							</td>
							<th class="center">완료 구분</th>
							<td class="left">
								<select id="cboCompleteGbn" name="cboCompleteGbn" style="width:auto;">
									<option value="all" <?if($cboCompleteGbn == 'all'){?>selected<?}?>>전체</option>
									<option value="Y" <?if($cboCompleteGbn == 'Y'){?>selected<?}?>>완료</option>
									<option value="N" <?if($cboCompleteGbn == 'N'){?>selected<?}?>>미완료</option>
								</select>
							</td>	
							<th class="center">취소 구분</th>
							<td class="left last">
								<select id="cboCancelGbn" name="cboCancelGbn" style="width:auto;">
									<option value="N" <?if($cboCancelGbn == 'N'){?>selected<?}?>>신청</option>
									<option value="Y" <?if($cboCancelGbn == 'Y'){?>selected<?}?>>취소</option>
								</select>
							</td>	
							<input name="txtOrder" id="txtOrder" type="hidden" value="">
						</tr>
					</tbody>
				</table>
			</td>
			<td class="left last">
				<span class="btn_pack m"><button onclick="lfSearch();">조회</button></span>
				<!--span id="btnExcel" class="btn_pack m" ><span class="excel"></span><button onclick="lfExcel();">엑셀</button></span-->
			</td>
			<input name="txtOrder" id="txtOrder" type="hidden" value="">
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="35px">
		<col width="80px">
		<col width="150px">
		<col width="100px">
		<col width="80px">
		<col width="80px">
		<col width="60px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">신청일자</th>
			<th class="head">기관명</th>
			<th class="head">신청지역</th>
			<th class="head">사무실번호</th>
			<th class="head">대표자번호</th>
			<th class="head">완료여부</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="tbodyList"></tbody>
	<tfoot>
		<tr>
			<td id="ID_ROW_PAGELIST" class="center bottom last" colspan="8">PAGE LIST</td>
		</tr>
	</tfoot>
</table>
<?
	include_once('../inc/_db_close.php');
?>