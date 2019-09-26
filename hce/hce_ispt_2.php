<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	사례접수
	 *********************************************************/

	$orgNo = $_SESSION['userCenterCode'];

	if ($_POST['hcptSeq']) $tmpHcptSeq = $_POST['hcptSeq'];
	if (!$tmpHcptSeq) $tmpHcptSeq = $hce->rcpt;

	$familyMap = '../hce/map/'.$orgNo.'/'.$hce->SR.'/'.$hce->IPIN.'_'.$tmpHcptSeq.'.jpg';
	$ecomap = '../hce/eco/'.$orgNo.'/'.$hce->SR.'/'.$hce->IPIN.'_'.$tmpHcptSeq.'.jpg';

	//사정기록
	/*
	$sql = 'SELECT	ispt_seq
			FROM	hce_inspection
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			ORDER	BY ispt_seq
			LIMIT	1';

	$isptSeq = $conn->get_data($sql);
	*/
	$isptSeq = '1';

	$sql = 'SELECT	family_remark
			,		ecomap_remark
			,		remark
			,		family_path
			,		eco_path
			FROM	hce_map
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$tmpHcptSeq.'\'
			AND		ispt_seq= \''.$isptSeq.'\'';

	$row = $conn->get_array($sql);

	$familyRemark	= StripSlashes($row['family_remark']);
	$ecomapRemark	= StripSlashes($row['ecomap_remark']);
	$remark			= StripSlashes($row['remark']);

	if ($row['family_path']) $familyMap = $row['family_path'];
	if ($row['eco_path']) $ecomap = $row['eco_path'];

	Unset($row);

	if (!is_file($familyMap)) $familyMap = '';
	if (!is_file($ecomap)) $ecomap = '';
?>
<script type="text/javascript">
	$(document).ready(function(){
		__init_form(document.f);
		__fileUploadInit($('form[name="f"]'), 'fileUploadCallback');
	});

	function fileUploadCallback(data, state){
		if (data == 'FAMILYMAP_ERROR'){
			alert('가계도 이미지 업로드 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
		}else if (data == 'ECOMAP_ERROR'){
			alert('생태도 이미지 업로드 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
		}else if (data == 'ERROR'){
			alert('데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
		}else{
			data = data.split('amp;').join('');
			var row = data.split('?');

			for(var i=0; i<row.length; i++){
				if (row[i]){
					var col = __parseVal(row[i]);

					if (col['gbn'] == 'FAMILYMAP'){
						$('#imgFamilyMap').attr('src',col['src']+'?timestamp=' + new Date().getTime());
					}else if (col['gbn'] == 'ECOMAP'){
						$('#imgEcomap').attr('src',col['src']+'?timestamp=' + new Date().getTime());
					}
				}
			}
		}
	}

	function lfUploadMap(){
		if ($('#familymap').val() || $('#ecomap').val()){
			var frm = $('form[name="f"]');
				frm.attr('action', './hce_ispt_2_mapupload.php');
				frm.submit();
		}
	}

	function lfMakeFamilyMap(){
		$.ajax({
			type: 'POST'
		,	url : './hce_map.php'
		,	data: {
				'type':'FAMILY_MAP'
			,	'sr':$('#sr').val()
			}
		,	beforeSend: function (){
			}
		,	success: function(result){
				if ('<?=$debug;?>' == '1'){
					$('#txtFamiyRemark').val(result);
				}
				$('#imgFamilyMap').attr('src',result+'?timestamp=' + new Date().getTime());
			}
		,	complite: function(result){
			}
		,	error: function (){
			}
		}).responseXML;
	}

	function lfDeleteFamilyMap(){
		$.ajax({
			type: 'POST'
		,	url : './hce_map_delete.php'
		,	data: {
				'type':'FAMILY_MAP'
			,	'sr':$('#sr').val()
			}
		,	beforeSend: function (){
			}
		,	success: function(result){
				$('#imgFamilyMap').attr('src','timestamp=' + new Date().getTime());
			}
		,	complite: function(result){
			}
		,	error: function (){
			}
		}).responseXML;
	}

	function lfMakeEcomap(){
		$.ajax({
			type: 'POST'
		,	url : './hce_map_eco.php'
		,	data: {
				'type':'ECO_MAP'
			,	'sr':$('#sr').val()
			}
		,	beforeSend: function (){
			}
		,	success: function(result){
				$('#imgEcomap').attr('src',result+'?timestamp=' + new Date().getTime());
			}
		,	complite: function(result){
			}
		,	error: function (){
			}
		}).responseXML;
	}

	function lfDeleteEcomap(){
		$.ajax({
			type: 'POST'
		,	url : './hce_map_delete.php'
		,	data: {
				'type':'ECO_MAP'
			,	'sr':$('#sr').val()
			}
		,	beforeSend: function (){
			}
		,	success: function(result){
				if (result) alert(result);
				$('#imgEcomap').attr('src','timestamp=' + new Date().getTime());
			}
		,	complite: function(result){
			}
		,	error: function (){
			}
		}).responseXML;
	}

	//저장
	function lfSaveSub(){
		var data = {};

		$('input:hidden').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			if (!val) val = '';

			data[id] = val;
		});

		$('textarea').each(function(){
			var id	= $(this).attr('id');
			var val	= $(this).val();

			data[id] = val;
		});

		$.ajax({
			type:'POST'
		,	url:'./hce_apply.php'
		,	data:data
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
					top.frames['frmTop'].lfTarget();

					lfUploadMap();
				}else if (result == 9){
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
<table class="my_table" style="width:100%;">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="bold last">- 가계도</th>
		</tr>
		<tr>
			<td class="left last">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col>
						<col width="80px">
						<col width="50px">
					</colgroup>
					<tbody>
						<tr>
							<td class="bottom">
								<span class="btn_pack small"><button type="button" onclick="lfMakeFamilyMap();">생성</button></span>
								<span class="btn_pack small"><button type="button" onclick="lfDeleteFamilyMap();">삭제</button></span>
							</td>
							<th class="center bottom">이미지업로드</th>
							<td class="bottom">
								<div style="float:left; width:100px; margin-left:5px; margin-top:2px; background:url(../image/find_file.gif) no-repeat left 50%;">
									<input type="file" name="familymap" id="familymap" style="width:18px; height:18px; filter:alpha(opacity=0); cursor:hand; margin-left:-7px;" onchange="">
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td class="center last">
				<img id="imgFamilyMap" src="<?=$familyMap;?>" style="margin:10px;" width="600" height="300" border="0">
			</td>
		</tr>
		<tr>
			<td class="center last">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="50px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="center bottom">비고</th>
							<td class="bottom last"><textarea id="txtFamiyRemark" style="width:100%; height:50px;"><?=$familyRemark;?></textarea></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<th class="bold last">- 생태도(지역사회 연계도)</th>
		</tr>
		<tr>
			<td class="left last">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col>
						<col width="80px">
						<col width="50px">
					</colgroup>
					<tbody>
						<tr>
							<td class="bottom">
								<span class="btn_pack small"><button type="button" onclick="lfMakeEcomap();">생성</button></span>
								<span class="btn_pack small"><button type="button" onclick="lfDeleteEcomap();">삭제</button></span>
							</td>
							<th class="center bottom">이미지업로드</th>
							<td class="bottom">
								<div style="float:left; width:100px; margin-left:5px; margin-top:2px; background:url(../image/find_file.gif) no-repeat left 50%;">
									<input type="file" name="ecomap" id="ecomap" style="width:18px; height:18px; filter:alpha(opacity=0); cursor:hand; margin-left:-7px;" onchange="">
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td class="center last">
				<img id="imgEcomap" src="<?=$ecomap;?>" style="margin:10px;" width="600" height="300" border="0">
			</td>
		</tr>
		<tr>
			<td class="center last">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="50px">
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th class="center bottom">비고</th>
							<td class="bottom last"><textarea id="txtEcoRemark" style="width:100%; height:50px;"><?=$ecomapRemark;?></textarea></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<th class="bold last">- 비고</th>
		</tr>
		<tr>
			<td class="last">
				<textarea id="txtMapText" name="multi" style="width:100%; height:150px;"><?=$remark;?></textarea>
			</td>
		</tr>
	</tbody>
</table>
<input id="bodyIdx" type="hidden" value="2">
<input id="isptSeq" type="hidden" value="<?=$isptSeq;?>">
<?
	include_once('../inc/_db_close.php');
?>