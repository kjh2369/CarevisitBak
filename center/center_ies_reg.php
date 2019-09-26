<?
	include_once("../inc/_header.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');
?>
<base target='_self'>

<style type='text/css'>
.title_string{
	float:left;
	width:70px;
	height:30px;
	line-height:30px;
	padding-left:5px;
	font-weight:bold;
	background-color:#f7faff;
	border-right:1px solid #a6c0f3;
}

.text_string{
	float:left;
	width:auto;
	height:30px;
	line-height:30px;
}
</style>

<script type='text/javascript'>
<!--

var opener = null;

function search(){
	try{
		$.ajax({
			type: 'POST'
		,	url : './center_ies_list.php'
		,	data: {
				'code':opener.code
			,	'kind':opener.kind
			,	'seq':opener.seq
			}
		,	beforeSend: function (){
			}
		,	success: function (xmlHttp){
				$('#listBody').html(xmlHttp);
			}
		,	complete: function(){
				try{
					$('#strInsNm_'+opener.kind, opener.win.document).text($('#nowIesNm').attr('value'));
					$('#strInsDT_'+opener.kind, opener.win.document).text($('#nowIesFrom').attr('value').toString().split('-').join('.')+' ~ '+$('#nowIesTo').attr('value').toString().split('-').join('.'));
				}catch(e){
				}
				setData();
			}
		,	error: function (){
			}
		}).responseXML;
	}catch(e){
	}
}

function setData(){
	switch($(':radio[name="iesGbn"]:checked').attr('value')){
		case '1':
			$('#iesCD option[value=\'\']').attr('selected', true);
			$('#iesFrom').attr('value', '');
			$('#iesTo').attr('value', '');
			break;

		case '2':
			$('#iesCD option[value=\''+$('#nowIesCD').attr('value')+'\']').attr('selected', true);
			$('#iesFrom').attr('value', $('#nowIesFrom').attr('value'));
			$('#iesTo').attr('value', $('#nowIesTo').attr('value'));
			break;

		case '3':
			try{
				$('#iesCD option[value=\''+$('#nowIesCD').attr('value')+'\']').attr('selected', true);
				$('#iesFrom').attr('value', __getDate(parseInt(parseInt($('#nowIesFrom').attr('value').toString().split('-').join(''), 10) + 10000).toString()));
				$('#iesTo').attr('value', __getDate(parseInt(parseInt($('#nowIesTo').attr('value').toString().split('-').join(''), 10) + 10000).toString()));
			}catch(e){
			}
			break;
	}
}

function setItem(para){
	opener.para = para;
	self.close();
}

function save(){
	if ($('#iesCD').attr('value') == ''){
		alert('보험사명을 선택하여 주십시오.');
		$('#iesCD').focus();
		return;
	}

	if ($('#iesFrom').attr('value') == ''){
		alert('가입기간을 입력하여 주십시오.');
		$('#iesFrom').focus();
		return;
	}

	if ($('#iesTo').attr('value') == ''){
		alert('가입기간을 입력하여 주십시오.');
		$('#iesTo').focus();
		return;
	}

	if ($(':radio[name="iesGbn"]:checked').attr('value') == '2'){
		if (!confirm('배상책임가입기간을 변경여부....?')) return;
	}

	try{
		$.ajax({
			type: 'POST'
		,	url : './center_ies_reg_ok.php'
		,	data: {
				'code':opener.code
			,	'kind':opener.kind
			,	'seq':opener.seq
			,   'gbn':$(':radio[name="iesGbn"]:checked').attr('value')
			,	'ins':$('#iesCD').attr('value')
			,	'from':$('#iesFrom').attr('value')
			,	'to':$('#iesTo').attr('value')
			}
		,	beforeSend: function (){
			}
		,	success: function (xmlHttp){
				switch(xmlHttp){
					case 'error_1':
						alert('error_1');
						break;

					default:
						search();
				}
			}
		,	error: function (){
			}
		}).responseXML;
	}catch(e){
	}
}

$(document).ready(function(){
	var height = $(document).height();
	var top    = __getObjectTop(listBody);

	$('#listBody').height(height - top - 2);

	opener = window.dialogArguments;
	__init_form(document.f);

	search();
});

-->
</script>
<?
	echo '<form name=\'f\' method=\'post\'>
		  <div class=\'title title_border\'>책임보험</div>

		  <div style=\'margin-top:15px; margin-left:15px; margin-right:15px; border:2px solid 2px solid #0e69b0; border-bottom:none;\'>
			<div class=\'title_string\'>구분</div>
			<div class=\'text_string\'>
				<div style=\'float:left; width:auto; margin-top:4px; margin-left:4px;\'><input id=\'iesGbn\' name=\'iesGbn\' type=\'radio\' value=\'2\' class=\'radio\' onclick=\'setData();\' checked><label for=\'radio\'>수정</label></div>
				<div style=\'float:left; width:auto; margin-top:4px;\'><input id=\'iesGbn\' name=\'iesGbn\' type=\'radio\' value=\'1\' class=\'radio\' onclick=\'setData();\'><label for=\'radio\'>추가</label></div>
				<div style=\'float:left; width:auto; margin-top:4px;\'><input id=\'iesGbn\' name=\'iesGbn\' type=\'radio\' value=\'3\' class=\'radio\' onclick=\'setData();\'><label for=\'radio\'>연장</label></div>
			</div>
		  </div>

		  <div style=\'margin-left:15px; margin-right:15px; border:2px solid 2px solid #0e69b0; border-top:1px solid #a6c0f3; border-bottom:none;\'>
			<div class=\'title_string\'>보험사명</div>
			<div class=\'text_string\'>';

			$sql = 'select g01_code as cd
					,      g01_name as nm
					,      g01_use
					  from g01ins
					 order by g01_code';

			$conn->query($sql);
			$conn->fetch();
			$rowCount = $conn->row_count();

			echo '<select id=\'iesCD\' name=\'iesCD\' style=\'width:auto; margin-top:4px; margin-left:4px;\'>
					<option value=\'\'>--</option>';

			for($i=0; $i<$rowCount; $i++){
				$row  = $conn->select_row($i);

				echo '<option value=\''.$row['cd'].'\'>'.$row['nm'].'</option>';
			}
			$conn->row_free();

	echo '		</select>
			</div>
		  </div>

		  <div style=\'margin-left:15px; margin-right:15px; border:2px solid 2px solid #0e69b0; border-top:1px solid #a6c0f3;\'>
			<div class=\'title_string\'>가입기간</div>
			<div class=\'text_string\'>
				<input id=\'iesFrom\' name=\'iesFrom\' type=\'text\' value=\'\' class=\'date\' style=\'margin-top:5px; margin-left:4px;\'> ~
				<input id=\'iesTo\' name=\'iesTo\' type=\'text\' value=\'\' class=\'date\' style=\'margin-top:5px;\'>
			</div>
		  </div>
		  <div style=\'margin-top:15px; margin-left:15px; margin-right:15px; text-align:center;\'>
			<span class=\'btn_pack m\'><button type=\'button\' onclick=\'save();\'>저장</button></span>
			<span class=\'btn_pack m\'><button type=\'button\' onclick=\'self.close();\'>닫기</button></span>
		  </div>

		  </form>';



	echo '<div class=\'title title_border\'>변경내역</div>
		  <table class=\'my_table\' style=\'width:100%;\'>
			<colgroup>
				<col width=\'40px\'>
				<col width=\'200px\'>
				<col width=\'130px\'>
				<col>
			</colgroup>
			<thead>
				<tr>
					<th class=\'head\'>No</th>
					<th class=\'head\'>보험사명</th>
					<th class=\'head\'>가입기간</th>
					<th class=\'head last\'>비고</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan=\'4\'>
						<div id=\'listBody\' style=\'overflow-x:hidden; overflow-y:scroll; width:100%; height:100px;\'></div>
					</td>
				</tr>
			</tbody>
		  </table>';

	include_once("../inc/_footer.php");
?>