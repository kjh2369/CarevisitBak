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
		,	url  :'./doctor_list_search.php'
		,	data :{
				'doctorNm'  : $('#txtDoctor').val(),
				'licenceNo'  : $('#txtLicenceNo').val()		
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
	
	function lfReg(doctor_no){
		
		var url = './doctor_reg.php';

		var width = 450;
		var height = 300;
		var top  = (window.screen.height - height) / 2;
		var left = (window.screen.width  - width)  / 2;

		var option = 'left='+left+'px, top='+top+'px, width='+width+'px,height='+height+'px,scrollbars=no,status=no,resizable=no';
		var win = window.open('about:blank', 'DOCTOR_REG', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'doctor_licence_no':doctor_no
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

		form.setAttribute('target', 'DOCTOR_REG');
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
		,	url  :'./doctor_delete.php'
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
<div class="title title_border">의사 조회</div>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col width="100px">
		<col width="80px">
		<col width="100px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">의사명</th>
			<td class="left"><input name="txtDoctor" id="txtDoctor" type="text"></td>
			<th class="center">면허번호</th>
			<td class="left"><input name="txtLicenceNo" id="txtLicenceNo" type="text"></td>
			<td class="left last">
				<span class="btn_pack m" ><button onclick="lfSearch();">조회</button></span>
				<span class="btn_pack m" ><button onclick="lfReg();">등록</button></span>
			</td>
		</tr>
</table>
			
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="35px">
		<col width="85px">
		<col width="85px">
		<col width="85px">
		<col width="85px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">의사명</th>
			<th class="head">면허번호</th>
			<th class="head">전화번호</th>
			<th class="head">휴대폰번호</th>
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