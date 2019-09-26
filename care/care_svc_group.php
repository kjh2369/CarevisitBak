<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$title = lfGetSPName($sr);

	if (!$title) exit;

	$orgNo = $_SESSION['userCenterCode'];

	$year = date('Y');

?>
<script type="text/javascript">
	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./care_svc_group_search.php'
		,	data:{
				'SR':'<?=$sr;?>'
			,	'category':$('#ID_CATEGORY').attr('category')
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#ID_LIST').html(html);
				$('#tempLodingBar').remove();
			}
		,	error:function(error){
				alert('err');
			}
		}).responseXML;
	}

	function lfReg(suga,seq){
		if (!suga) suga = '';
		if (!seq) seq = '';
		location.href = './care.php?sr=<?=$sr;?>&type=SVC_GROUP_REG&suga='+suga+'&seq='+seq;
	}

	function lfDel(suga,seq){
		if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type:'POST'
		,	url:'./care_svc_group_delete.php'
		,	data:{
				'SR':'<?=$sr;?>'
			,	'suga':suga
			,	'seq':seq
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					$('#rowId_'+suga+'_'+seq).remove();
					alert('정상적으로 처리되었습니다.');
				}else if (result == 9){
					alert('처리중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	error:function(error){
				alert('err');
			}
		}).responseXML;
	}

	function lfCategoryFind(){
		var objModal = new Object();
		var url = './care_svc_category_find.php';
		var style = 'dialogWidth:800px; dialogHeight:600px; dialogHide:yes; scroll:no; status:no';

		objModal.win = window;
		objModal.SR	 = $('#sr').val();
		objModal.code= '';
		objModal.name= '';

		window.showModalDialog(url, objModal, style);

		if (objModal.code){
			$('#ID_CATEGORY').attr('category',objModal.code).text(objModal.name);
			lfSearch();
		}
	}
</script>
<div class="title title_border">
	<div style="float:left; width:auto;">서비스 묶음조회(<?=$title;?>)</div>
	<div style="float:right; width:auto; padding-top:10px;">
		<span class="btn_pack m"><span class="save"></span><button type="button" class="bold" onclick="lfReg();">등록</button></span>
	</div>
</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="head">카테고리 선택</th>
			<td id="ID_CATEGORY" class="left last" onmouseover="this.style.backgroundColor='#EAEAEA';" onmouseout="this.style.backgroundColor='#FFFFFF';" onclick="lfCategoryFind();" category=""></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="150px">
		<col width="150px">
		<col width="150px">
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">그룹명</th>
			<th class="head">자원명</th>
			<th class="head">서비스</th>
			<th class="head">대상자수</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="ID_LIST"><?
		$sql = 'SELECT	grp.suga_cd
				,		suga.suga_nm
				,		grp.seq
				,		cust.cust_nm
				,		grp.group_nm
				,		LENGTH(grp.target) - LENGTH(REPLACE(grp.target,\'/\',\'\')) + 1 AS cnt
				FROM	care_svc_group AS grp';

		if ($IsCareYoyAddon){
			//공통수가
			$sql .= '
				INNER	JOIN (
							SELECT	org_no, suga_sr, suga_cd, suga_sub, suga_nm
							FROM	care_suga
							WHERE	org_no	= \''.$orgNo.'\'
							AND		suga_sr	= \''.$sr.'\'';
				
				if($_SESSION['userArea'] == '05' || $_SESSION['userArea'] == '14' || $_SESSION['userArea'] == '03'  || $_SESSION['userArea'] == '04' || $_SESSION['userArea'] == '08'){
					$sql .= 'AND		LEFT(from_dt,4) <= \''.$year.'\'
							 AND		LEFT(to_dt,  4) >= \''.$year.'\'';
				}
			
				$sql .=	'	UNION	ALL
							SELECT	\''.$orgNo.'\' AS org_no, \''.$sr.'\' AS suga_sr, LEFT(code,5) AS suga_cd, MID(code,6) AS suga_sub, name
							FROM	care_suga_comm
						) AS suga
						ON		suga.org_no	= grp.org_no
						AND		suga.suga_sr= grp.org_type
						AND		CONCAT(suga.suga_cd,suga.suga_sub) = grp.suga_cd';
		}else{
			$sql .= '
				INNER	JOIN	care_suga AS suga
						ON		suga.org_no	= grp.org_no
						AND		suga.suga_sr= grp.org_type
						AND		CONCAT(suga.suga_cd,suga.suga_sub) = grp.suga_cd';
		}

		$sql .= '
				INNER	JOIN	care_cust AS cust
						ON		cust.org_no	= grp.org_no
						AND		cust.cust_cd= grp.res_cd
				WHERE	grp.org_no	= \''.$orgNo.'\'
				AND		grp.org_type= \''.$sr.'\'
				AND		grp.del_flag= \'N\'
				GROUP   BY suga_cd, seq
				ORDER	BY group_nm';
		
		//if($debug) echo nl2br($sql); 
		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);?>
			<tr id="rowId_<?=$row['suga_cd'];?>_<?=$row['seq'];?>">
				<td class="center"><?=$i+1;?></td>
				<td class="left"><?=$row['group_nm'];?></td>
				<td class="left"><?=$row['cust_nm'];?></td>
				<td class="left"><?=$row['suga_nm'];?></td>
				<td class="right"><?=$row['cnt'];?></td>
				<td class="left last">
					<span class="btn_pack m"><button onclick="lfReg('<?=$row['suga_cd'];?>','<?=$row['seq'];?>');" style="color:BLUE;">수정</button></span>
					<span class="btn_pack m"><button onclick="lfDel('<?=$row['suga_cd'];?>','<?=$row['seq'];?>');" style="color:RED;">삭제</button></span>
				</td>
			</tr><?
		}

		$conn->row_free();?>
	</tbody>
</table>
<?
	include_once('../inc/_db_close.php');
?>