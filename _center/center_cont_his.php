<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$fromDt	= $_POST['fromDt'];

	/*
	$tmpRs = Array('3'=>'신규','1'=>'서비스','2'=>'일시중지','4'=>'해지','9'=>'기타');
	$tmpDtlRs['3'] = Array('01'=>'신규연결','02'=>'기간연장','03'=>'재연결');
	$tmpDtlRs['1'] = Array('01'=>'신규계약','02'=>'재계약','03'=>'기간연장');
	$tmpDtlRs['2'] = Array('01'=>'사용료미납','02'=>'기관요청','03'=>'장기미사용');
	$tmpDtlRs['4'] = Array('01'=>'계약기간만기','02'=>'기관요청','03'=>'사용료미납','04'=>'미계약','05'=>'장기미사용','06'=>'무료기간연장');
	$tmpDtlRs['9'] = Array('99'=>'기타');
	*/

	include_once('./center_rs_set.php');
	$tmpRs = $setRs;
	$tmpDtlRs = $setDtlRs;

	$sql = 'SELECT	cont_dt, from_dt, to_dt, rs_cd, rs_dtl_cd
			FROM	cv_reg_info
			WHERE	org_no = \''.$orgNo.'\'
			ORDER	BY from_dt, to_dt';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<div class="CLS_HIS_ROW" fromDt="<?=$row['from_dt'];?>" stat="<?=$row['rs_cd'];?>_<?=$row['rs_dtl_cd'];?>" newCont="<?=$row['rs_cd'] == '3' ? 'Y' : 'N'?>" selYn="<?=$fromDt == $row['from_dt'] || $fromDt == $row['cont_dt'] ? 'Y' : 'N';?>" delYn="N" style="cursor:default; border-top:<?=$i > 0 ? 'solid 1px #CCCCCC;' : 'none';?>; font-weight:<?=$fromDt == $row['from_dt'] || $fromDt == $row['cont_dt'] ? 'bold' : 'normal';?>; padding:5px;">
			<div class="nowrap" style="float:left; width:120px;"><?=$tmpRs[$row['rs_cd']];?> - <?=$tmpDtlRs[$row['rs_cd']][$row['rs_dtl_cd']];?></div>
			<div style="float:left; width:170px;"><?=$myF->dateStyle($row['from_dt'],'.').($row['to_dt'] ? ' ~ '.$myF->dateStyle($row['to_dt'],'.') : '');?></div><?
			if ($i == $rowCnt - 1){?>
				<div style="float:left; width:auto;"><img src="../image/btn_close.gif" style="cursor:pointer;" onclick="lfContDel('<?=$row['from_dt'];?>','<?=$row['rs_cd'];?>_<?=$row['rs_dtl_cd'];?>');"></div><?
			}?>
		</div><?
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
<script type="text/javascript">
	function lfContDel(fromDt, stat){
		/*if (stat == '3_01'){
			alert('신규연결은 삭제할 수  없습니다.');
			return;
		}*/

		/*if (stat == '1_01'){
			alert('신규계약은 삭제할 수  없습니다.');
			return;
		}*/

		if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

		$('.CLS_HIS_ROW[fromDt="'+fromDt+'"]').attr('delYn','Y');

		$.ajax({
			type :'POST'
		,	url  :'./center_cont_delete.php'
		,	data :{
				'orgNo':'<?=$orgNo;?>'
			,	'fromDt':fromDt
			}
		,	beforeSend:function(){
			}
		,	success:function(posDt){
				lfContMove(0, posDt);
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>