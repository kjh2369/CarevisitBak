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
?>
<script type="text/javascript">
	function lfReg(IPIN){
		var f = document.f;

		if (!IPIN) IPIN = '';

		f.IPIN.value = IPIN;
		f.action = '../care/care.php?sr=<?=$sr;?>&type=<?=$type;?>_REG';
		f.submit();
	}

	function lfDel(IPIN){
		if (!IPIN) return;
		if (!confirm('삭제 후 복구가 불가능합니다.\n정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type:'POST'
		,	url:'./care_actual_research_delete.php'
		,	data:{
				'SR':'<?=$sr;?>'
			,	'IPIN':IPIN
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (__resultMsg(result)){
					$('#rowId_'+IPIN).remove();
				}
			}
		,	error:function(error){
				alert('err');
			}
		}).responseXML;
	}
</script>
<div class="title title_border">
	<div style="float:left; width:auto;">지원대상 실태조사표(<?=$title;?>)</div>
	<div style="float:right; width:auto; padding-top:10px;">
		<span class="btn_pack m"><span class="add"></span><button type="button" class="bold" onclick="lfReg();">작성</button></span>
	</div>
</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="90px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">대상자명</th>
			<th class="head">생년월일</th>
			<th class="head">면접일</th>
			<th class="head">담당자</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody><?
		$sql = 'SELECT	a.IPIN
				,		a.iver_dt
				,		a.iver_cd
				,		a.iver_nm
				,		b.name
				,		b.jumin
				FROM	care_actual_research AS a
				INNER	JOIN	care_client_normal AS b
						ON		b.org_no	= a.org_no
						AND		b.normal_sr	= a.org_type
						AND		b.normal_seq= a.IPIN
				WHERE	a.org_no	= \''.$orgNo.'\'
				AND		a.org_type	= \''.$sr.'\'
				ORDER	BY name';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		$no = 1;

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);?>
			<tr id="rowId_<?=$row['IPIN'];?>">
				<td class="center"><?=$no;?></td>
				<td class="center"><?=$row['name'];?></td>
				<td class="center"><?=$myF->issToBirthDay($row['jumin'],'.');?></td>
				<td class="center"><?=$myF->dateStyle($row['iver_dt'],'.');?></td>
				<td class="center"><?=$row['iver_nm'];?></td>
				<td class="left last">
					<span class="btn_pack m"><button onclick="lfReg('<?=$row['IPIN'];?>');">수정</button></span><?
					if ($debug){?>
						<span class="btn_pack m"><button onclick="lfDel('<?=$row['IPIN'];?>');">삭제</button></span><?
					}?>
				</td>
			</tr><?
		}

		$conn->row_free();?>
	</tbody>
</table>
<input id="jumin" type="hidden" value="">
<input name="IPIN" type="hidden" value="">
<?
	include_once('../inc/_db_close.php');
?>