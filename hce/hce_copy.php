<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	$orgNo		= $_SESSION['userCenterCode'];
	
	$IPIN	= $hce->IPIN;
	$rcpt	= $hce->rcpt;
	$meet	= $_POST['seq'];
	$SR     = $hce->SR;
	$type   = $_GET['type'];

?>
<base target='_self'>

<script language='javascript'>
<!--

var opener = null;

function search(){

	try{
		$.ajax({
			type: 'POST',
			url : './hce_copy_list.php',
			data: {
			    'orgNo':'<?=$orgNo?>'
			,	'IPIN':'<?=$IPIN?>'
		  	,	'SR':'<?=$SR;?>'
			,	'type':opener.type
			},
			beforeSend: function (){
			},
			success: function (xmlHttp){
				$('#listBody').html(xmlHttp);
				$('#tblList tr:even').css('background-color', '#ffffff');
				$('#tblList tr:odd').css('background-color', '#f9f9f9');
				$('#tblList tr').mouseover(function(){
					$(this).css('background-color', '#f2f5ff');
				});
				$('#tblList tr').mouseout(function(){
					if ($('#tblList tr').index($(this)) % 2 == 1){
						$(this).css('background-color', '#f9f9f9');
					}else{
						$(this).css('background-color', '#ffffff');
					}
				});
			},
			error: function (){
			}
		}).responseXML;
	}catch(e){
	}
}


function lfCopyReg(seq){
	
	self.close();
}


function setItem(para){
	opener.para = para;
	self.close();
}


$(document).ready(function(){
	var height = $(document).height();
	var top    = __getObjectTop(listBody);

	$('#listBody').height(height - top - 2);

	opener = window.dialogArguments;

	search();
});




-->
</script>


<?



if($type == '52'){ ?>
	<table id='tblList' class='my_table' style='width:100%;'>
		<colgroup>
			<col width="50px">
			<col width="70px">
			<col width="70px">
			<col width="70px">
			<col width="70px">		
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head">No</th>
				<th class="head">회의일자</th>	
				<th class="head">판정구분</th>
				<th class="head">조사자</th>
				<th class="head">참석자</th>
				<!--<th class="head">제공여부</th>
				<th class="head">판정일자</th>-->
				<th class="head last">비고</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class='center last' colspan='6'>
					<div id='listBody' style='overflow-x:hidden; overflow-y:scroll; width:100%; height:100px;'>
					</div>
				</td>
			</tr>
		</tbody>
	</table><?
}else if($type == '102'){ ?>
	<table id='tblList' class='my_table' style='width:100%;'>
		<colgroup>
			<col width="50px">
			<col width="70px">
			<col width="70px">
			<col width="70px">
			<col width="70px">		
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head">No</th>
				<th class="head">작성일자</th>	
				<th class="head">작성구분</th>
				<th class="head">담당자</th>
				<th class="head">조사자</th>
				<th class="head last">비고</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class='center last' colspan='6'>
					<div id='listBody' style='overflow-x:hidden; overflow-y:scroll; width:100%; height:100px;'>
					</div>
				</td>
			</tr>
		</tbody>
	</table><?
}else if($type == '142'){ ?>
	<table id='tblList' class='my_table' style='width:100%;'>
		<colgroup>
			<col width="50px">
			<col width="70px">	
			<col width="100px">	
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head">No</th>
				<th class="head">평가일자</th>
				<th class="head">사례관리자</th>
				<th class="head last">비고</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class='center last' colspan='4'>
					<div id='listBody' style='overflow-x:hidden; overflow-y:scroll; width:100%; height:100px;'>
					</div>
				</td>
			</tr>
		</tbody>
	</table><?
}else { ?>
	<table id='tblList' class='my_table' style='width:100%;'>
		<colgroup>
			<col width="50px">
			<col width="70px">	
			<col width="70px">	
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head">No</th>
				<th class="head">일자</th>
				<th class="head">작성자</th>
				<th class="head last">비고</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class='center last' colspan='4'>
					<div id='listBody' style='overflow-x:hidden; overflow-y:scroll; width:100%; height:100px;'>
					</div>
				</td>
			</tr>
		</tbody>
	</table><?
} ?>

<?
	include_once('../inc/_footer.php');
?>