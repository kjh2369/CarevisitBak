<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');

	$orgNo = $_SESSION['userCenterCode'];
	$reFun = $_GET['reFun'];
	$tgt_dt = $_GET['tgt_dt'];
	$gwan_cd = $_GET['gwan_cd'];
	$hang_cd = $_GET['hang_cd'];
	$mog_cd = $_GET['mog_cd'];
	

	//결제란설정
	$sql = 'SELECT	line_cnt, subject
			FROM	signline_set
			WHERE	org_no = \''.$orgNo.'\'';
	$row = $conn->get_array($sql);

	$signCnt = $row['line_cnt'];
	$signTxt = Explode('|',$row['subject']);


?>
<script type="text/javascript">
	var opener = null;
	
	$(document).ready(function(){
		
		opener = window.dialogArguments;

		__init_form(document.f);
		
		//SetMouseMove($('<?=$bodyid;?> .pop_title:first'));
		
	});
	
	function lfSelect(obj){
		
		opener.result = true;
	
		opener.ent_dt = $(obj).attr('ent_dt');
		opener.ent_seq = $(obj).attr('ent_seq');
		opener.sign_cd  = $(obj).attr('sign_cd');
		opener.wrt_dt  = $(obj).attr('wrt_dt');
		opener.obj =  obj;

		self.close();

	}


	/*
	function lfArSearch(rowno){
		if (!rowno) rowno = 1;
		$.ajax({
			type:'POST'
		,	url:'./find_fa_ar_search.php'
		,	data:{
				'tgt_dt':'<?=$tgt_dt;?>'
			,	'gwan_cd':'<?=$gwan_cd;?>'
			,	'hang_cd':'<?=$hang_cd;?>'
			,	'mog_cd':'<?=$mog_cd;?>'
			,	'rowno':rowno
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('<?=$bodyid;?> #TBL_ARLIST tbody').html(html);
				$('<?=$bodyid;?> #TBL_ARLIST a[id^="BTN_"]').unbind().bind({
					'click':function(){
						switch($(this).prop('id')){
							case 'BTN_SEL':
								var tr = __tagObject(this, 'TR');
								var ent_dt = $(tr).attr('ent_dt')
								,	ent_seq = $(tr).attr('ent_seq')
								,	sign_cd = $(tr).attr('sign_cd')
								,	wrt_dt = __date($(tr).attr('wrt_dt'), '/').substr(5);

								eval("<?=$reFun;?>({'ent_dt':ent_dt, 'ent_seq':ent_seq, 'sign_cd':sign_cd, 'wrt_dt':wrt_dt})");
								$('<?=$bodyid;?> #BTN_CLOSE').click();
								break;
						}
					}
				});
			}
		,	error:function(e){
				alert('ERROR\n'+e);
			}
		}).responseXML;
	}
	*/

</script>
<div class="title title_border">품의내역</div><?
$colgrp = '
	<col width="50px">
	<col width="70px">
	<col width="70px">
	<col width="90px">
	<col width="70px">
	<col width="400px">
	<col width="70px">
	<col>';?>
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgrp;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">품의일자</th>
			<th class="head">품의종류</th>
			<th class="head">품의금액</th>
			<th class="head">지출원</th>
			<th class="head">원인 및 용도</th>
			<th class="head">담당자</th>
			<th class="head last">결제</th>
		</tr>
	</thead>
</table>
<div style="overflow-x:hidden; overflow-y:scroll; height:575px;">
	<table id="TBL_ARLIST" class="my_table" style="width:100%; border-top:none;">
		<colgroup><?=$colgrp;?></colgroup>
		<tbody><?
		
			//if ($rowno < 0) $rowno = 0;
			$rowno = 0;

			$sql = 'SELECT	a.ent_dt, a.ent_seq, a.wrt_dt, b.gbn_name AS ar_type, a.ar_amt, a.exp_name, a.cause, a.sign_cd
					FROM	fa_apprq AS a
					INNER	JOIN	fa_apprq_gbn AS b
							ON		b.gbn_type	= \'T2\'
							AND		b.gbn_cd	= a.ar_type
							AND		b.del_flag	= \'N\'
					WHERE	a.org_no	 = \''.$orgNo.'\'
					AND		a.wrt_dt	<= \''.$tgt_dt.'\'
					AND		a.gwan_cd	 = \''.$gwan_cd.'\'
					AND		a.hang_cd	 = \''.$hang_cd.'\'
					AND		a.mog_cd	 = \''.$mog_cd.'\'
					AND		a.del_flag	 = \'N\'
					ORDER	BY wrt_dt DESC, a.ent_dt DESC, a.ent_seq DESC
					LIMIT	'.$rowno.', 10
					';
			//echo nl2br($sql); 
			$rows = $conn->_fetch_array($sql);

			for($i=0; $i<count($rows); $i++){
				$row = $rows[$i];
				
				$sign_cd = explode('/', $_POST['sign_cd']);
				
				$stat = '<span style="" class="btn_pack m" ><button id="BTN_SEL" onclick="lfSelect($(this).parent().parent().parent());" >선택</button></span>';

				for($i=0; $i<sizeof($sign_cd); $i++){

					if ($$signTxt[$i] != ''){
						
						$sql = 'SELECT m02_name
								FROM   m02yoyangsa
								WHERE  m02_ccode = \''.$orgNo.'\'
								AND    m02_key = \''.$ed->de($sign_cd[$i]).'\'';
					
						$yoyNm = $conn -> get_data($sql); 
						
						
						$stat = $signTxt[$i].'('.$yoyNm.') 대기중';
						break;
					}
				}
				
				?>

				<tr ent_dt="<?=$row['ent_dt'];?>" ent_seq="<?=$row['ent_seq'];?>" sign_cd="<?=$row['sign_cd'];?>" wrt_dt="<?=$row['wrt_dt'];?>">
				<td class="center"><?=$i+1;?></td>
				<td class="center"><?=$myF->dateStyle($row['wrt_dt'],'/');?></td>
				<td class="center"><?=$row['ar_type'];?></td>
				<td class="center"><?=number_format($row['ar_amt']);?></td>
				<td><?=$row['exp_name'];?></td>
				<td class="nowrap"><div class="left"><?=stripslashes($row['cause']);?></div></td>
				<td ><div class="left"><?=$per_name;?></div></td>
				<td class="left last"><?=$stat;?></td>
				</tr><?
			}

			unset($rows);
		?>
		</tbody>
	</table>
</div>

<?
	include_once('../inc/_footer.php');
?>