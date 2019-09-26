<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_nhcs_db.php');

	$type  = 99;
	//$orgNo =  $_POST['orgNo'];
	$licenceNo    = $ed->de($_POST['licenceNo']);
	$mdOrgNo	  = $ed->de($_POST['mdOrgNo']);
	$fromDt		  = $_POST['fromDt'];
		
	$sql = '   SELECT	mo.org_no
			   ,		mo.org_nm
			   ,		doc.licence_no
			   ,		doc.doctor_nm
			   ,		md.cntrct_dt
			   ,		md.retire_dt
				FROM	medical_org_doctor AS md
				LEFT	JOIN (
							SELECT	DISTINCT
									doctor_licence_no AS licence_no
							,	    doctor_name As doctor_nm
							FROM	doctor
							WHERE   del_flag = \'N\'
						) AS doc
						ON	doc.licence_no = md.doctor_licence_no 
				LEFT	JOIN (
							SELECT	DISTINCT
									medical_org_no AS org_no
							,	    medical_org_name As org_nm
							FROM	medical_org 
							WHERE   del_flag = \'N\'
						) AS mo
						ON		mo.org_no = md.medical_org_no
			
			    WHERE   doctor_licence_no	= \''.$licenceNo.'\'
				AND     medical_org_no		= \''.$mdOrgNo.'\'
				AND     cntrct_dt			= \''.$fromDt.'\'
				AND     del_flag			= \'N\'';
	
	$mst = $conn -> get_array($sql);
	
	$fromDt = $mst['cntrct_dt'] != '' ? $myF->dateStyle($mst['cntrct_dt']) : $myF->dateStyle(date('Ymd', mktime()));
	$toDt = $mst['retire_dt'] != '' ? $myF->dateStyle($mst['retire_dt']) : $myF->dateStyle(date('Ymd', mktime()));
	
	
	if($mst['cntrct_dt']){
		$dis = 'disabled="true"';
	}else {
		$dis = '';
	}

	$colgroup = '<col width="40px"><col width="100px"><col width="100px"><col width="110px"><col>';
	$colgroup2 = '<col width="40px"><col width="150px"><col width="200px"><col>';

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

		
		setTimeout('lfOrgSearch()',200);
		setTimeout('lfMdOrgSearch()',200);
	});
	
	function lfDoctorSelect(obj,code,name){
		$('#tBodyList tr:not').css('font-weight', 'normal');
		
		$(obj).css('font-weight', 'bold');
		$('#lblCode').val(code);
		$('#lblName').text(name);
	}

	function lfMdOrgSelect(obj,code,name, arr){
		$('#tBodyMList tr:not').css('font-weight', 'normal');

		$(obj).css('font-weight', 'bold');
		$('#lblMdCode').val(code);
		$('#lblMdName').text(name);

	}

	function lfOrgSearch(){
		
		$.ajax({
			type :'POST'
		,	url  :'./doctor_search.php'
		,	data :{}
		,	beforeSend:function(){
				//$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				
				if(!'<?=$mst[cntrct_dt]?>'){
					var list = data.split(String.fromCharCode(1));
					var html   = '<table class="my_table" style="width:100%;"><colgroup><?=$colgroup;?></colgroup><tbody id=\'tBodyList\'>';
					
					for(var i=0; i<list.length; i++){
						if (list[i]){
							var val = list[i].split(String.fromCharCode(2));
							

							html += '<tr  id=\'rowId\' tag=\''+i+'\' onmouseover="$(this).css(\'background-color\', \'#efefef\'); $(this).css(\'cursor\', \'pointer\');" onmouseleave="$(this).css(\'background-color\', \'#ffffff\');" onclick="lfDoctorSelect(this,\''+val[1]+'\',\''+val[2]+'\');">'
								 +  '<td class="center">'+val[0]+'</td>'
								 +  '<td class="center"><div class="left">'+val[1]+'</div></td>'
								 +  '<td class="center"><div class="left">'+val[2]+'</div></td>'
								 +  '<td class="center "><div class="left">'+val[3]+'</div></td>'
								 +  '<td class="center "></td>'
								 +  '</tr>';
						}
					}

					html += '</tbody></table>';
				}else {
					html = '';
				}

				$('#tbodyOrgList').html(html);
				//$('#tempLodingBar').remove();

				
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfMdOrgSearch(){
		
		$.ajax({
			type :'POST'
		,	url  :'./medical_md_org_search.php'
		,	data :{}
		,	beforeSend:function(){
				//$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				var list = data.split(String.fromCharCode(1));
				var html   = '<table class="my_table" style="width:100%;"><colgroup><?=$colgroup2;?></colgroup><tbody id=\'tBodyMList\'>';

				for(var i=0; i<list.length; i++){
					if (list[i]){
						var val = list[i].split(String.fromCharCode(2));
						

						html += '<tr id=\'rowMId_'+i+'\' tag=\''+i+'\' onmouseover="$(this).css(\'background-color\', \'#efefef\'); $(this).css(\'cursor\', \'pointer\');" onmouseleave="$(this).css(\'background-color\', \'#ffffff\');" onclick="lfMdOrgSelect(this,\''+val[1]+'\',\''+val[2]+'\');">'
							 +  '<td class="center">'+val[0]+'</td>'
							 +  '<td class="center"><div class="left">'+val[1]+'</div></td>'
							 +  '<td class="center"><div class="left">'+val[2]+'</div></td>'
							 +  '<td class="center last"></td>'
							 +  '</tr>';
					}
				}

				html += '</tbody></table>';
			
				$('#tbodyMdOrgList').html(html);
				//$('#tempLodingBar').remove();

				
			}
		,	error:function(){
			}
		}).responseXML;
	}

	
	//저장
	function lfSave(){
		if(!'<?=$mst[from_dt]?>'){
			if(!$('#lblCode').val()){
				alert('의사를 선택해주십시오.');
				return;
			}
			
			if(!$('#lblMdCode').val()){
				alert('의료기관을 선택해주십시오.');
				return;
			}
			
			if(!$('#fromDt').val()){
				alert('시작일자를 등록해주십시오.');
				$('#fromDt').focus();
				return;
			}
		}

		if(!$('#toDt').val()){
			alert('종료일자를 등록해주십시오.');
			$('#toDt').focus();
			return;
		}
		
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
		
		
		data['lblMdName'] = $('#lblMdName').text();
		
		$.ajax({
			type:'POST'
		,	url:'./medical_doctor_connect_save.php'
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
<div class="title title_border">의료기관 신청연결</div>
<div style="float:left; width:50%;">
<table class="my_table" style="width:100%;">
<colgroup><?=$colgroup;?></colgroup>
<tbody>
<tr>
	<th class="center" colspan="5">재가요양기관</th>
</tr>
<tr>
	<th class="center" colspan="2">선택의사</th>
	<td class="left" colspan="3">
		<span id="lblName"><?=$mst['doctor_nm'];?></span>
		<input id="lblCode" type="hidden" value="<?=$mst['licence_no'];?>">
	</td>
</tr>
<tr>
	<th class="head">No</th>
	<th class="head">면허번호</th>
	<th class="head">의사명</th>
	<th class="head">전문과목</th>
	<th class="head">비고</th>
</tr>
<tr>
	<td class="top center" colspan="5">
		<div id="tbodyOrgList" style="overflow-x:hidden; overflow-y:scroll; width:100%; height:250px;"></div>
	</td>
</tr>
</tbody>
</table>
</div>
<div style="float:left; width:50%;">
<table class="my_table" style="width:100%;">
<colgroup><?=$colgroup2;?></colgroup>
<tbody>
<tr>
	<th class="head" colspan="4">의료기관</th>
</tr>
<tr>
	<th class="head" colspan="2">선택기관</th>
	<td class="left" colspan="2">
		<span id="lblMdName" ><?=$mst['org_nm'];?></span>
		<input id="lblMdCode" type="hidden" value="<?=$mst['org_no'];?>">	
	</td>
</tr>
<tr>
	<th class="head">No</th>
	<th class="head">기관기호</th>
	<th class="head">기관명</th>
	<th class="head last">비고</th>
</tr>
<tr>
	<td class="top center last" colspan="4">
		<div id="tbodyMdOrgList" style="overflow-x:hidden; overflow-y:scroll; width:100%; height:250px;"></div>
	</td>
</tr>
</tbody>
</table>
</div>
<div>
	<table class="my_table" style="width:100%;">
		<colgroup>
		<col width="140px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center" colspan="4">정보입력</th>
		</tr>
		<tr>
			<th class="center">계약기간</th>
			<td class="left" colspan="3">
				<input id="fromDt" name="fromDt" type="text" class="date" value="<?=$fromDt;?>"<?=$dis;?>> ~
				<input id="toDt" name="toDt" type="text" class="date" value="<?=$toDt;?>">
			</td>
		</tr>
	</tbody>
	</table>
</div>

<!--
	<colgroup>
		<col width="60px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">기관</th>
			<td class="left">
				<?
				if($mst['org_no'] == ''){ ?>
					<span class="btn_pack find" onclick="lfFindCenter();"></span><?
				} ?>
				<span id="lblName"><?=$mst['org_nm'];?></span>
				<input id="lblCode" type="hidden" value="">
			</td>
		</tr>
		<tr>
			<th class="center" >의료기관</th>
			<td class="left">
				<span class="btn_pack find" onclick="lfFindMdCenter();" style="vertical-align:middle; margin-right:2px;"></span>
				<span id="lblMdName" ><?=$mst['md_org_nm'];?></span>
				<input id="lblMdCode" type="hidden" value="">	
			</td>
		</tr>
		<tr>
			<th class="center">적용기간</th>
			<td class="left" >
				<input id="fromDt" name="fromDt" type="text" class="date" value="<?=$fromDt;?>"> ~
				<input id="toDt" name="toDt" type="text" class="date" value="<?=$toDt;?>">
			</td>
		</tr>
	</tbody>
-->
</table>
<div id="divBtnBody" class="center" style="height:30px; margin-top:20px; ">
	<span class="btn_pack m"><button onclick="lfSave();">저장</button></span>
</div>
<?
	include_once('../inc/_footer.php');
?>