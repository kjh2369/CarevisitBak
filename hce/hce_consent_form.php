<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*********************************************************
	 *	대상자 선정기준표
	 *********************************************************/

	$orgNo = $_SESSION['userCenterCode'];
	$orgNm = $_SESSION["userCenterName"];
	$userArea = $_SESSION['userArea'];

	//대상자명
	$sql = 'SELECT	m03_name
			FROM	m03sugupja
			WHERE	m03_ccode	= \''.$orgNo.'\'
			AND		m03_mkind	= \'6\'
			AND		m03_key		= \''.$hce->IPIN.'\'';
	$name = $conn->get_data($sql);

	$consentDt = Date('Y-m-d');

	//담당자
	$sql = 'SELECT	iver_nm
			,		iver_jumin
			FROM	hce_interview
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'';

	$row = $conn->get_array($sql);

	$perNm		= $row['iver_nm'];
	$perJumin	= $ed->en($row['iver_jumin']);

	Unset($row);
	
	//충남협회 20180313부터 사례회의록 서비스제공내역에서 서비스계획서 내용으로 변경
	$sql = 'SELECT	count(*)
			FROM	hce_consent_form
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND     (cont_dt <= \'20180312\'
			OR      REPLACE(update_dt,\'-\',\'\') <= \'20180312\')';
	
	$cnt = $conn -> get_data($sql);
	
?>
<script type="text/javascript">
	$(document).ready(function(){
		setTimeout('lfSearch()',100);
	});

	function lfAddRow(obj){
		var html = '';

		if (!obj){
			var obj = {};

			obj['svcNm'] = '';
			obj['cont']  = '';
			obj['other'] = '';
		}

		html += '<tr>';
		html += '<td><input id="txtSvcNm" name="txt" type="text" value="'+obj['svcNm']+'" style="width:100%;"></td>';
		html += '<td><input id="txtCont" name="txt" type="text" value="'+obj['cont']+'" style="width:100%;"></td>';
		html += '<td><input id="txtOther" name="txt" type="text" value="'+obj['other']+'" style="width:100%;"></td>';
		html += '<td class="last"><div style="padding-left:5px; padding-top:1px;"><span class="btn_pack m"><span class="delete"></span><a href="#">삭제</a></span></div></td>';
		html += '</tr>';

		$('#tbodyList tr:last-child').after(html);

		__init_form(document.f);
	}
	
	function lfMemFindResult(obj){
		var obj = __parseStr(obj);

		$('#txtPer').attr('jumin',obj['jumin']).val(obj['name']);
	}

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./hce_find.php'
		,	data:{
				'type':'<?=$type;?>'
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				if (!data) return;

				var col = __parseVal(data);

				$('#txtConsentDt').val(__getDate(col['contDt']));
				$('#txtPer').attr('jumin',col['perJumin']).val(col['perNm']);

				lfLoadList();
			}
		,	complite:function(result){
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfLoadList(){
		/*
			$.ajax({
				type:'POST'
			,	url:'./hce_find.php'
			,	data:{
					'type':'<?=$type;?>_LIST'
				}
			,	beforeSend:function(){
				}
			,	success:function(data){
					var row = data.split(String.fromCharCode(11));
					var html = '';

					for(var i=0; i<row.length; i++){
						if (row[i]){
							var col = __parseVal(row[i]);

							lfAddRow(col);
						}
					}
				}
			,	complite:function(result){
				}
			,	error:function(){
				}
			}).responseXML;
		*/
		$.ajax({
			type:'POST'
		,	url:'./hce_consent_form_list.php'
		,	data:{
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				var row = data.split(String.fromCharCode(11));
				
				for(var i=0; i<row.length; i++){
					if (row[i]){
						var col = __parseVal(row[i]);
						var obj = $('.clsData[code="'+col['svcCd']+'"]');
						
						$('#txtCont',obj).val(col['cont']);
						$('#txtOther',obj).val(col['other']);
					}
				}
			}
		,	complite:function(result){
			}
		,	error:function(){
			}
		}).responseXML;
	}

	//저장
	function lfSave(){
		if (!$('#txtConsentDt').val()){
			alert('작성일자를 입력하여 주십시오.');
			$('#txtPlanDt').focus();
			return;
		}

		if (!$('#txtPer').val()){
			alert('담당자를 입력하여 주십시오.');
			return;
		}

		var data = {};
		var cnt = 0;

		data['data'] = '';

		/*
			$('#tbodyList tr').each(function(){
				var svcNm	= $('#txtSvcNm',this).val();
				var content	= $('#txtCont',this).val();
				var other	= $('#txtOther',this).val();

				if (svcNm != undefined){
					if (!svcNm){
						alert('서비스명을 입력하여 주십시오.');
						$('#txtSvcNm',this).focus();
						cnt = 0;
						return false;
					}

					if (!content){
						alert('내용을 입력하여 주십시오.');
						$('#txtCont',this).focus();
						cnt = 0;
						return false;
					}

					data['data'] += ('svcNm='+svcNm+'&content='+content+'&other='+other+String.fromCharCode(11));

					cnt ++;
				}
			});
		*/

		$('.clsData').each(function(){
			var svcCd	= $(this).attr('code');
			var content	= $('#txtCont',this).val();
			var other	= $('#txtOther',this).val();

			data['data'] += ('svcNm='+svcCd+'&content='+content+'&other='+other+String.fromCharCode(11));

			cnt ++;
		});

		if (cnt == 0) return;

		data['perJumin']= $('#txtPer').attr('jumin');

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

<div class="title title_border">
	<div style="float:left; width:auto;">이용안내 및 동의서</div>
	<div style="float:right; width:auto; padding-top:10px;">
		<span style="color:red;">※동의서는 사례회의록이 작성이되있어야 저장이 가능합니다.</span>
		<span class="btn_pack m"><span class="save"></span><button type="button" class="bold" onclick="lfSave();">저장</button></span>
		<span class="btn_pack m"><span class="pdf"></span><button type="button" onclick="lfPDF('<?=$type;?>');">출력</button></span>
	</div>
</div>

<?
$colgroup	= '	<col width="200px">
				<col width="200px">
				<col width="200px">
				<col>';
?>

<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col width="120px">
		<col width="80px">
		<col width="80px">
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head">일자</th>
			<td><input id="txtConsentDt" name="txt" type="text" value="<?=$consentDt;?>" class="date"></td>
			<th class="head">담당자</th>
			<td class="" style="padding:1px 1px 0 2px;"><span class="btn_pack find" onclick="lfMemFind();">lfMemFind()</span></td>
			<td class="last"><input id="txtPer" name="txt" type="text" value="<?=$perNm;?>" jumin="<?=$perJumin;?>" alt="not" readonly></td>
			<td class="right last"> </td>
		</tr>
	</tbody>
</table>

<?
if($userArea == '05'){ 
	if($cnt>0){ ?>
<table class="my_table my_border_blue" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
		<tr>
			<th class="left bold last" colspan="4">1. 서비스 종류</th>
		</tr>
		<tr>
			<th class="head">서비스명</th>
			<th class="head">내용</th>
			<th class="head">비고</th>
			<th class="head last"><!--div style="text-align:left; padding-left:5px; padding-top:1px;"><span class="btn_pack m"><span class="add"></span><a href="#" onclick="lfAddRow();">추가</a></span></div--></th>
		</tr>
	<tbody><?
		$sql = 'SELECT	decision_svc
				FROM	hce_meeting
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'
				AND		del_flag= \'N\'
				ORDER	BY meet_seq DESC
				LIMIT	1';
		
		$tmpSvc = $conn->get_data($sql);
		$tmpSvc = Str_Replace('/','&',$tmpSvc);
		$tmpSvc = Str_Replace(':','=',$tmpSvc);

		Parse_Str($tmpSvc,$arrSvc);

		$sql = 'SELECT	DISTINCT
						care.suga_cd AS cd
				,		suga.nm1 AS mst_nm
				,		suga.nm2 AS pro_nm
				,		suga.nm3 AS svc_nm
				FROM	care_suga AS care
				INNER	JOIN	suga_care AS suga
						ON		suga.cd1 = SUBSTR(care.suga_cd,1,1)
						AND		suga.cd2 = SUBSTR(care.suga_cd,2,2)
						AND		suga.cd3 = SUBSTR(care.suga_cd,4,2)
				WHERE	care.org_no	= \''.$orgNo.'\'
				AND		care.suga_sr= \''.$hce->SR.'\'';
		
		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();
		$idx = 1;

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if ($arrSvc[$row['cd']] == 'Y'){?>
				<tr class="clsData" code="<?=$row['cd'];?>">
					<td><div class="nowrap" style="margin-left:5px; width:195px;"><?=$row['svc_nm'];?></div></td>
					<td><input id="txtCont" name="txt" type="text" value="" style="width:100%;"></td>
					<td><input id="txtOther" name="txt" type="text" value="" style="width:100%;"></td>
					<td class="last"></td>
				</tr><?
			}
		}

		$conn->row_free();?>
	</tbody>
</table> <?
	}else { 	
		
		?>
<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="200px">
		<col width="300px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head bold last" colspan="6"><div style="text-align:left; padding-left:5px;">- 서비스 종류</div></th>
		</tr>
		<tr>
			<th class="head">서비스명</th>
			<th class="head">내용</th>
			<th class="head">
				<div style="float:center; width:auto;">비고</div>
			</th>
		</tr>
	</thead>
	<tbody id="ID_LIST"><?
		$sql = 'SELECT	IFNULL(MAX(plan_seq),0)
				FROM	hce_plan_sheet
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'
				AND     del_flag= \'N\'';
				
				$planSeq = $conn->get_data($sql);
		
		$sql = 'SELECT	plan_idx
				,		contents
				,		period
				,		times
				,		method
				FROM	hce_plan_sheet_item
				WHERE	org_no	 = \''.$orgNo.'\'
				AND		org_type = \''.$hce->SR.'\'
				AND		IPIN	 = \''.$hce->IPIN.'\'
				AND		rcpt_seq = \''.$hce->rcpt.'\'
				AND     plan_seq = \''.$planSeq.'\'
				AND		del_flag = \'N\'';
		
		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();
		$no = 1;

		if ($rowCnt > 0){
			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);?>
				<tr class="clsData" code="<?=$row['plan_idx'];?>">
					<td><div class="nowrap" style="margin-left:5px; width:195px;"><?=StripSlashes($row['contents']);?></div></td>
					<td><input id="txtCont" name="txt" type="text" value="" style="width:100%;"></td>
					<td><input id="txtOther" name="txt" type="text" value="" style="width:100%;"></td>
				</tr><?

				$no ++;
			}
		}

		$conn->row_free();
	?>
	</tbody>
</table><?
	} 
}else { ?> 
<table class="my_table my_border_blue" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
		<tr>
			<th class="left bold last" colspan="4">1. 서비스 종류</th>
		</tr>
		<tr>
			<th class="head">서비스명</th>
			<th class="head">내용</th>
			<th class="head">비고</th>
			<th class="head last"><!--div style="text-align:left; padding-left:5px; padding-top:1px;"><span class="btn_pack m"><span class="add"></span><a href="#" onclick="lfAddRow();">추가</a></span></div--></th>
		</tr>
	<tbody><?
		$sql = 'SELECT	decision_svc
				FROM	hce_meeting
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'
				AND		del_flag= \'N\'
				ORDER	BY meet_seq DESC
				LIMIT	1';
		
		$tmpSvc = $conn->get_data($sql);
		$tmpSvc = Str_Replace('/','&',$tmpSvc);
		$tmpSvc = Str_Replace(':','=',$tmpSvc);

		Parse_Str($tmpSvc,$arrSvc);

		$sql = 'SELECT	DISTINCT
						care.suga_cd AS cd
				,		suga.nm1 AS mst_nm
				,		suga.nm2 AS pro_nm
				,		suga.nm3 AS svc_nm
				FROM	care_suga AS care
				INNER	JOIN	suga_care AS suga
						ON		suga.cd1 = SUBSTR(care.suga_cd,1,1)
						AND		suga.cd2 = SUBSTR(care.suga_cd,2,2)
						AND		suga.cd3 = SUBSTR(care.suga_cd,4,2)
				WHERE	care.org_no	= \''.$orgNo.'\'
				AND		care.suga_sr= \''.$hce->SR.'\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();
		$idx = 1;

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if ($arrSvc[$row['cd']] == 'Y'){?>
				<tr class="clsData" code="<?=$row['cd'];?>">
					<td><div class="nowrap" style="margin-left:5px; width:195px;"><?=$row['svc_nm'];?></div></td>
					<td><input id="txtCont" name="txt" type="text" value="" style="width:100%;"></td>
					<td><input id="txtOther" name="txt" type="text" value="" style="width:100%;"></td>
					<td class="last"></td>
				</tr><?
			}
		}

		$conn->row_free();?>
	</tbody>
</table><?
} ?>
<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="left bold last" colspan="2">2. 서비스 조정 및 중단</th>
		</tr>
		<tr>
			<th class="head">서비스<br>조정</th>
			<td class="head last">
				ㆍ서비스 이용자에게 적절하지 않거나 서비스 제공 목적에 어긋날 때<br>
				ㆍ서비스 이용자의 부적절한 서비스 요구가 있을 경우<br>
			</td>
		</tr>
		<tr>
			<th class="head">서비스<br>중단</th>
			<td class="head last">
				ㆍ서비스 이용자가 서비스를 중단의 의사가 있을 경우<br>
				ㆍ다른 지역으로 이주를 하였을 경우<br>
				ㆍ3개월 이상 연락이 끊겼을 경우<br>
				ㆍ타 기관과 서비스가 중복되었을 경우<br>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="left bold last">3. 서비스 이용 동의서</th>
		</tr>
		<tr>
			<td class="left last">
				&nbsp;&nbsp;서비스 이용자는 신상의 어려움이나 경제적인 변동이 있을 경우 기관에 알려야 하며, 어려움을 해결하기 위해 같이 노력하여야 한다.<br>
				서비스 이용자는 본 기관이 이용자와 상호 협의한 서비스를 실시하기 위하여 개인정보를 수집.활용하는 것에 동의한다.
			</td>
		</tr>
		<tr>
			<td class="left last">
				&nbsp;&nbsp;본 동의서는 "<?=$orgNm;?>"기관에서 제공되는 서비스에 대하여 본 기관과 (<?=$name;?>)님이 상호 협의한 내용이며, 서비스 제공에 있어 문제 및 어려움이 있을 경우 서비스 이용자와 기관과의 상호 협의를 통하여 조정이 가능하다.
			</td>
		</tr>
	</tbody>
</table>
<?

	include_once('../inc/_db_close.php');
?>