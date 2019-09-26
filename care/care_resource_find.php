<?
	include_once("../inc/_header.php");
	include_once("../inc/_login.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$sugaCd = $_POST['sugaCd'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$sr		= $_POST['sr'];
	
	if (!$year) $year = Date('Y');
	if (!$month) $month = Date('m');

	$sql = 'SELECT	nm1
			,		nm2
			,		nm3
			FROM	suga_care
			WHERE	cd1 = \''.SubStr($sugaCd,0,1).'\'
			AND		cd2 = \''.SubStr($sugaCd,1,2).'\'
			AND		cd3 = \''.SubStr($sugaCd,3,2).'\'';

	$row = $conn->get_array($sql);

	$mstNm = Str_Replace('<br>','',$row['nm1']);
	$proNm = Str_Replace('<br>','',$row['nm2']);
	$svcNm = Str_Replace('<br>','',$row['nm3']);

	Unset($row);

	$sql = 'SELECT	DISTINCT suga_nm
			FROM	care_suga
			WHERE	org_no	= \''.$code.'\'
			AND		suga_sr = \''.$sr.'\'
			AND		suga_cd = \''.SubStr($sugaCd,0,5).'\'
			AND		suga_sub= \''.SubStr($sugaCd,5,2).'\'';

	$subNm = $conn->get_data($sql);
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfLoad();
		lfReSize();
		self.focus();
	});

	function lfReSize(){
		var t = $('#divList').offset().top;
		var h = $(document).height();
		var height = h - t -3;

		$('#divList').height(height);
	}

	function lfLoad(){
		$.ajax({
			type :'POST'
		,	url  :'./care_find.php'
		,	data :{
				'type':'RESOURCE_REG'
			,	'sr':'<?=$sr;?>'
			,	'year':'<?=$year;?>'
			,	'month':'<?=$month;?>'
			,	'sugaCd':'<?=$sugaCd;?>'
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(data){
				//var row = data.split(String.fromCharCode(11));
				var row = data.split('__TAP__');
				var html = '';
				var no = 1;

				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseStr(row[i]);

						if (col['gbn'] == '1'){
							col['gbn'] = '공공';
						}else if (col['gbn'] == '2'){
							col['gbn'] = '기업';
						}else if (col['gbn'] == '3'){
							col['gbn'] = '단체';
						}else{
							col['gbn'] = '개인';
						}

						html += '<tr style="cursor:default;">';
						html += '<td class="center">'+no+'</td>';
						html += '<td class="center">'+col['gbn']+'</td>';
						html += '<td class="center"><div class="left nowrap" style="width:150px;"><a href="#" onclick="lfSetResource(\''+col['cd']+'\',\''+col['cost']+'\',\''+col['name']+'\',\''+col['pernm']+'\'); return false;">'+col['name']+'</a></div></td>';
						html += '<td class="center">'+col['pernm']+'</td>';
						html += '<td class="center">'+__getPhoneNo(col['pertel']).split('-').join('.')+'</td>';

						if ('<?=$sr;?>' != 'S'){
							html += '<td class="center"><div class="right">'+__num2str(col['cost'])+'</div></td>';
						}

						if ('<?=$debug;?>' == '1'){
							html += '<td class="center last">'+col['cd']+'</td>';
						}else{
							html += '<td class="center last"></td>';
						}
						html += '</tr>';

						no ++;
					}
				}

				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSetResource(cd,cost,nm,per){
		opener.lfResourceFindResult(cd,cost,nm,per);
		self.close();
	}
</script>
<div class="title title_border">자원조회</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="150px">
		<col width="150px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">서비스코드</th>
			<th class="head">대분류(사업)</th>
			<th class="head">중분류(프로그램)</th>
			<th class="head last">소분류(서비스)</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center"><span id="sugaCd"><?=$sugaCd;?></span></td>
			<td class="left"><div id="mstNm" class="nowrap" style="width:150px;"><?=$mstNm;?></div></td>
			<td class="left"><div id="proNm" class="nowrap" style="width:150px;"><?=$proNm;?></div></td>
			<td class="left last"><div id="svcNm" class="nowrap"><?=$svcNm;?></div></td>
		</tr>
		<tr>
			<th class="head">서브명</th>
			<td class="left last" colspan="3"><?=$subNm;?></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="40px">
		<col width="150px">
		<col width="70px">
		<col width="90px"><?
		if ($sr != 'S'){?>
			<col width="70px"><?
		}?>
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">구분</th>
			<th class="head">명칭</th>
			<th class="head">담당자</th>
			<th class="head">연락처</th><?
			if ($sr != 'S'){?>
				<th class="head">단가</th><?
			}?>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center top last" colspan="10">
				<div id="divList" style="overflow-y:auto;width:100%;height:100px;">
					<table class="my_table" style="width:100%;">
						<colgroup>
							<col width="40px">
							<col width="40px">
							<col width="150px">
							<col width="70px">
							<col width="90px"><?
							if ($sr != 'S'){?>
								<col width="70px"><?
							}?>
							<col>
						</colgroup>
						<tbody id="tbodyList">
							<tr>
								<td class="center last" colspan="20">::검색된 데이타가 없습니다.::</td>
							</tr>
						</tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once("../inc/_footer.php");
?>