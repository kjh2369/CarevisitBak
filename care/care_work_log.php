<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$title = lfGetSPName($sr);

	if (!$title) exit;

	$fromDt	= Date('Y-m-01');
	$toDt	= $myF->dateAdd('day',-1,$myF->dateAdd('month',1,$fromDt,'Y-m-d'),'Y-m-d');
?>
<script type="text/javascript">
	$(document).ready(function(){
		//lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./care_work_log_search.php'
		,	data :{
				'SR'		:$('#sr').val()
			//,	'year'	:$('#lblYYMM').attr('year')
			,	'sugaCd'	:$('#cboSugaCd').val()
			,	'memName'	:$('#txtMemName').val()
			,	'tgName'	:$('#txtTgName').val()
			,	'fromDt'	:$('#txtFromDt').val()
			,	'toDt'		:$('#txtToDt').val()
			,	'orderName'	:$('#cboOrderName').val()
			,	'orderDate'	:$('#cboOrderDate').val()
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

	function lfLogReg(obj){
		var width	= 800;
		var height	= 600;
		var left	= (screen.availWidth - width) / 2;
		var top		= (screen.availHeight - height) / 2;

		var option = 'left='+left+', top='+top+', width='+width+',height='+height+',scrollbars=no,status=no,resizable=no';
		var url = './care_work_log_reg.php';
		var win = window.open('about:blank', 'WORK_LOG_REG', option);
			win.opener = self;
			win.focus();

		var parm = new Array();
			parm = {
				'SR'	:$('#sr').val()
			,	'sugaCd':$('#cboSugaCd').val()
			,	'objId'	:$(obj).attr('id')
			,	'jumin'	:$(obj).attr('jumin')
			,	'target':$(obj).attr('target')
			,	'date'	:$(obj).attr('date')
			,	'key'	:$(obj).attr('key')
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

		form.setAttribute('target', 'WORK_LOG_REG');
		form.setAttribute('method', 'post');
		form.setAttribute('action', url);

		document.body.appendChild(form);

		form.submit();
	}

	function lfRegResult(id){
		var obj = __GetTagObject($('#'+id),'TR');
		$('#lblRegYn',obj).html('<span style="color:BLUE;">Y</span>');
	}

	function lfExcel(jumin,target,date,doc){
		if (!doc) doc = document;

		var parm = new Array();
			parm = {
				'SR'	:$('#sr').val()
			,	'sugaCd':$('#cboSugaCd').val()
			,	'jumin'	:jumin
			,	'target':target
			,	'date'	:date
			};

		var form = doc.createElement('form');
		var objs;
		for(var key in parm){
			objs = doc.createElement('input');
			objs.setAttribute('type', 'hidden');
			objs.setAttribute('name', key);
			objs.setAttribute('value', parm[key]);

			form.appendChild(objs);
		}

		form.setAttribute('target', '_self');
		form.setAttribute('method', 'post');
		form.setAttribute('action', './care_work_log_excel.php');

		doc.body.appendChild(form);

		form.submit();
	}
</script>
<div class="title title_border">업무일지 조회 및 작성(<?=$title;?>)</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="120px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>업무일지 서비스 선택</th>
			<td class="last">
				<select id="cboSugaCd" style="width:auto;" onchange="lfSearch();"><?
					$sql = 'SELECT	code, name
							FROM	care_suga_comm';

					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);?>
						<option value="<?=$row['code'];?>"><?=$row['name'];?></option><?
					}

					$conn->row_free();?>
				</select>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="180px">
		<col width="60px">
		<col width="150px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">직원명</th>
			<td>
				<input id="txtMemName" type="text" value="" style="width:100%;">
			</td>
			<th class="center">대상자명</th>
			<td>
				<input id="txtTgName" type="text" value="" style="width:100%;">
			</td>
			<td class="left last">
				<span class="btn_pack m"><span class="refresh"></span><button onclick="lfSearch();">조회</button></span>
			</td>
		</tr>
		<tr>
			<th class="center">기간</th>
			<td>
				<input id="txtFromDt" type="text" value="<?=$fromDt;?>" class="date"> ~
				<input id="txtToDt" type="text" value="<?=$toDt;?>" class="date">
			</td>
			<th class="center">정렬</th>
			<td class="last" colspan="2">
				<select id="cboOrderName" style="width:auto; margin-right:0;">
					<option value="1">직원명순</option>
					<option value="2">대상자명순</option>
				</select>
				<select id="cboOrderDate" style="width:auto; margin-left:0;">
					<option value="1">일자순</option>
					<option value="2">일자역순</option>
				</select>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="80px">
		<col width="40px">
		<col width="70px">
		<col width="80px">
		<col width="40px">
		<col width="70px">
		<col width="80px">
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">No</th>
			<th class="head" colspan="3">직원정보</th>
			<th class="head" colspan="4">대상자정보</th>
			<th class="head" colspan="2">업무일지</th>
			<th class="head last" rowspan="2">비고</th>
		</tr>
		<tr>
			<th class="head">직원명</th>
			<th class="head">생년월일</th>
			<th class="head">성별</th>
			<th class="head">대상자명</th>
			<th class="head">생년월일</th>
			<th class="head">성별</th>
			<th class="head">중점여부</th>
			<th class="head">서비스일자</th>
			<th class="head">작성여부</th>
		</tr>
	</thead>
	<tbody id="tbodyList">
		<tr>
			<td class="center last" colspan="11">::검색된 데이타가 없습니다.::</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_db_close.php');
?>