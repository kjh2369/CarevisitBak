<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_body_header.php');

	$orgNo = $_SESSION['userCenterCode'];
	$year = Date('Y');



	$fromDt = date('Y-m-d', strtotime('-30 days'));
	$toDt = date('Y-m-d');


?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();

		$('input[init="Y"]').each(function(){
			__init_object(this);
		});

	});
	
	function lfPrint(jumin, yymm, seq){

		var para = 'root=iljung'
				 + '&dir=N'
				 + '&fileName=sw_work_log'
				 + '&fileType=pdf'
				 + '&target=show.php'
				 + '&showForm=SW_WORK_LOG'
				 + '&jumin='+jumin
				 + '&yymm='+yymm
				 + '&seq='+seq
				 + '&cnt='+''
				 + '&param=';

		__printPDF(para);
	}

	function lfSearch(){

		$.ajax({
			type:'POST'
		,	url:'./sw_work_log_sign_search.php'
		,	data:{
			'fromDt':$('#fromDt').val()
		,	'toDt':$('#toDt').val()
		,	'orderByGbn':$('input:radio[name="orderByGbn"]:checked').val()
		,	'clientName':$('#clientName').val()
		,	'memName':$('#memName').val()
		}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){

				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();
			}
		});
	}

	function lfWokrLogReg(jumin, year, month, seq){
		var width = 650;
		var height = 700;
		var left = (screen.availWidth - width) / 2;
		var top = (screen.availHeight - height) / 2;

		var option = 'left='+left+', top='+top+', width='+width+',height='+height+',scrollbars=no,status=no,resizable=no';
		var url = './sw_work_log_sign_reg.php';
		var win = window.open('about:blank', 'WORK_LOG', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'type':'SIGN'
			,	'jumin':jumin
			,	'yymm':year+month
			,	'year':year
			,	'month':month
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

		form.setAttribute('target', 'WORK_LOG');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfPrintPopup(){
		var width = 300;
		var height = 260;
		var left = (screen.availWidth - width) / 2;
		var top = (screen.availHeight - height) / 2;

		var option = 'left='+left+', top='+top+', width='+width+',height='+height+',scrollbars=no,status=no,resizable=no';
		var url = './sw_work_log_sign_print.php';
		var win = window.open('about:blank', 'WORK_LOG_SIGN', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'fromDt':$('#fromDt').val()
			,	'toDt':$('#toDt').val()
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

		form.setAttribute('target', 'WORK_LOG_SIGN');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfLogBody(){
		$.ajax({
			type:'POST'
		,	url:'./sw_work_log_reg_body.php'
		,	data:{
				'jumin':$('#jumin').val()
			,	'yymm':$('#yymm').val()
			,	'seq':$('#seq').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#divLogBody').html(html);
				//$('table:first',$('#divWorkMst')).show();

				$('input[init="Y"]').each(function(){
					__init_object(this);
				});

				$('textarea').each(function(){
					__init_object(this);
				});
			}
		});
	}


</script>
<div class="title title_border">업무수행일지 결재</div>
<table class="my_table" style="width:100%; ">
	<colgroup>
		<col width="60px">
		<col width="210px">
		<col width="50px">
		<col width="200px">
		<col width="60px">
		<col width="70px">
		<col width="60px">
		<col width="70px">
		<col width="40px">
		<col width="*">
	</colgroup>
	<tbody>
		<tr>
			<th class="head">조회기간</th>
			<td>
				<input id='fromDt' name='fromDt' type='text' init="Y" value='<?=$fromDt;?>' class='date'> ~ <input id='toDt' name='toDt' type='text' init="Y" value='<?=$toDt;?>' class='date' style=" margin-right:10px;">
			</td>
			<th class="head">구분</th>
			<td>
				<input id='orderByGbn_1' name='orderByGbn' type='radio' value='1' class='radio' checked><label for='orderByGbn_1'>전체</label>
				<input id='orderByGbn_2' name='orderByGbn' type='radio' value='2' class='radio'><label for='orderByGbn_2'>결재</label>
				<input id='orderByGbn_3' name='orderByGbn' type='radio' value='3' class='radio'><label for='orderByGbn_2'>미결재</label>
			</td>
			<th class="center">고객명</th>
			<td>
				<input id="clientName" name="clientName" type="text" value="<?=$find_su_name;?>" style="width:70px;" onkeyup="if(event.keyCode==13){lfSearch();}" onFocus="this.select();">
			</td>
			<th class="center">복지사명</th>
			<td>
				<input id="memName" name="memName" type="text" value="<?=$find_su_name;?>" style="width:70px;" onkeyup="if(event.keyCode==13){lfSearch();}" onFocus="this.select();">
			</td>
			<td class="left last">
				<span class='btn_pack m'><button type='button' onclick='lfSearch();'>조회</button></span>
			</td>

			<td class="last right" style="text-align:right;">
				<span class='btn_pack m'><button type='button' onclick='lfPrintPopup();'>출력</button></span>
			</td>


		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%; ">
	<colgroup>
		<col width="110px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="220px">
		<col width="*">
		<col width="40px">
		<col width="50px">
		<col width="50px">
	</colgroup>
	<thead>
		<tr>
			<th class="head">방문일시</th>
			<th class="head">대상자</th>
			<th class="head">복지사</th>
			<th class="head">요양사</th>
			<th class="head">복지사총평</th>
			<th class="head">지시내용</th>
			<th class="head">결재</th>
			<th class="head">확인</th>
			<th class="head last">출력</th>
		</tr>
	</thead>
	<tbody id="tbodyList"></tbody>
</table>
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>