<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');

	$orgNo = $_SESSION['userCenterCode'];
	$SR = $_GET['SR'];
	$gbn = $_GET['gbn'];
	$suga_cd = $_GET['suga_cd'];
	$seq = $_GET['seq'];
	$today = Date('Ymd');

	$sql = 'SELECT	reg_dt, att_cnt, attendee, contents, pic1, pic2
			FROM	care_rpt
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		org_sr	 = \''.$SR.'\'
			AND		suga_cd	 = \''.$suga_cd.'\'
			AND		seq		 = \''.$seq.'\'
			AND		del_flag = \'N\'
			';
	$R = $conn->get_array($sql);

	$sql = 'SELECT	suga_nm
			FROM	care_suga
			WHERE	org_no	= \''.$orgNo.'\'
			AND		suga_sr = \''.$SR.'\'
			AND		CONCAT(suga_cd, suga_sub) = \''.$suga_cd.'\'';

	$suga_nm = $conn->get_data($sql);
?>
<script type="text/javascript">
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;
		opener.result = false;

		var title = '리포트 작성';

		if (opener.SR == 'S'){
			title += '(재가지원)';
		}else if (opener.SR == 'R'){
			title += '(자원연계)';
		}else{
			self.close();
			return;
		}

		$('.title').text(title);

		__fileUploadInit($('#frm'), 'fileUploadCallback');

		$(':text, textarea').each(function(){
			__init_object(this);
		});

		if ('<?=$suga_cd;?>' == ''){
			$('#reg_dt').change();
		}else{
			lfCngCell();
		}

		lfCngSuga();
	});

	function lfCngSuga(){
		$.ajax({
			type :'POST'
		,	url  :'./care_rpt_suga.php'
		,	data :{
				'date':$('#reg_dt').val()
			,	'SR':opener.SR
			,	'suga_cd':'<?=$suga_cd;?>' != '' ? '<?=$suga_cd;?>' : opener.suga_cd
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				if (!data){
					$('#suga_cd').html('');
					return;
				}

				var obj = eval('['+data+']');
				var str = '';

				for(var i in obj){
					if (!obj[i]['code']) break;
					str += '<option value="'+obj[i]['code']+'">'+obj[i]['name']+'</option>';
				}

				$('#suga_cd').html(str);
				$('#suga_cd').change();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfCngCell(){
		var gbn = $('#suga_cd').val().substr(3, 2);
		var str = '';
		
		
		if (gbn == '01'){
			str = '참석자';
		}else if (gbn == '02'){
			str = '교육생';
		}else if (gbn == '03'){
			str = '실습생';
		}else if (gbn == '04'){
			str = '건';
		}

		$('#CELL_1').text(str+'수');
		$('#CELL_2').text(gbn == '04' ? '방법' : str);
	}

	function lfDel(){
		if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type :'POST'
		,	url  :'./care_rpt_del.php'
		,	data :{
				'SR':opener.SR
			,	'suga_cd':'<?=$suga_cd;?>'
			,	'seq':'<?=$seq;?>'
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (!result){
					alert('정상적으로 처리되었습니다.');
					opener.result = true;
					self.close();
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSave(){
		var gbn = $('#suga_cd').val().substr(3, 2);
		var str = '';
		var data = {'SR':opener.SR, 'seq':'<?=$seq;?>', 'reg_dt':$('#reg_dt').val().replace(/-/g, ''), 'suga_cd':$('#suga_cd').val(), 'contents':$('#contents').val()};

		if (!$('#reg_dt').val()){
			alert('작성일자를 입력하여 주십시오.');
			$('#reg_dt').focus();
			return;
		}

		if ('<?=$gbn;?>' == 'B99' || '<?=$gbn;?>' == 'B0301'){
			if ('<?=$gbn;?>' == 'B99'){
				if (gbn == '01'){
					str = '참석자';
				}else if (gbn == '02'){
					str = '교육생';
				}else if (gbn == '03'){
					str = '실습생';
				}else if (gbn == '04'){
					str = '홍보방법';
				}else{
					alert('리포트 구분을 선택하여 주십시오.');
					return;
				}
			}else{
				str = '참석자';
			}

			if (__str2num($('#att_cnt').val()) < 1){
				alert((gbn == '04' ? '홍보건수' : str+'수')+'를 입력하여 주십시오.');
				$('#att_cnt').focus();
				return;
			}

			if (!$('#attendee').val()){
				alert(str+'을 입력하여 주십시오.');
				$('#attendee').focus();
				return;
			}

			data['att_cnt'] = $('#att_cnt').val();
			data['attendee'] = $('#attendee').val();
		}else if ('<?=$gbn;?>' == 'A0602' || '<?=$gbn;?>' == 'A0603'){
			if ($(':checkbox[id="cust_cd"]:checked').length < 1){
				alert(('<?=$gbn;?>' == 'A0602' ? '후원자' : '봉사자')+'를 선택하여 주십시오.');
				return;
			}

			data['att_cnt'] = $(':checkbox[id="cust_cd"]:checked').length;
			data['attendee'] = '';

			$(':checkbox[id="cust_cd"]:checked').each(function(){
				data['attendee'] += '/'+$(this).val();
			});

			$('#attendee').val(data['attendee']);
			$('#att_cnt').val(data['att_cnt']);
		}


		var frm = $('#frm');
			frm.attr('action', './care_rpt_save.php?SR='+opener.SR+'&seq=<?=$seq;?>');
			frm.submit();
		/*
			$.ajax({
				type :'POST'
			,	url  :'./care_rpt_save.php'
			,	data :data
			,	beforeSend:function(){
				}
			,	success:function(result){
					if (!result){
						alert('정상적으로 처리되었습니다.');
						opener.result = true;
						self.close();
					}else{
						alert(result);
					}
				}
			,	error:function(){
				}
			}).responseXML;
		*/
	}

	function lfPdf(){
		var arguments	= 'root=care'
						+ '&dir=P'
						+ '&fileName=care_rpt'
						+ '&fileType=pdf'
						+ '&target=show.php'
						+ '&SR='+opener.SR
						+ '&suga_cd=<?=$suga_cd;?>'
						+ '&seq=<?=$seq;?>'
						;

		__printPDF(arguments);
	}

	function fileUploadCallback(result, state){
		if (!result){
			alert('정상적으로 처리되었습니다.');
			opener.result = true;
			self.close();
		}else{
			alert(result);
		}
	}
</script>
<div class="title title_border"></div>
<form id="frm" name="frm" method="post" enctype="multipart/form-data">
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>작성일자</th>
			<td>
				<input id="reg_dt" name="reg_dt" type="text" value="<?=$myF->dateStyle($R['reg_dt'] ? $R['reg_dt'] : $today);?>" class="date" onchange="lfCngSuga();">
			</td>
		</tr>
		<tr>
			<th>구분</th>
			<td><?
				if ($suga_cd){?>
					<select id="suga_cd" name="suga_cd" style="width:auto;">
						<option value="<?=$suga_cd;?>"><?=$suga_nm;?></option>
					</select><?
				}else{?>
					<select id="suga_cd" name="suga_cd" style="width:auto;" onchange="lfCngCell();"></select><?
				}?>
			</td>
		</tr><?
		if ($gbn == 'B99' || $gbn == 'B0301'){?>
			<tr>
				<th id="CELL_1"></th>
				<td><input id="att_cnt" name="att_cnt" type="text" value="<?=number_format($R['att_cnt']);?>" class="number" style="width:70px;"></td>
			</tr>
			<tr>
				<th id="CELL_2"></th>
				<td><textarea id="attendee" name="attendee" style="width:100%; height:35px;"><?=stripslashes($R['attendee']);?></textarea></td>
			</tr><?
		}else if ($gbn == 'A0602' || $gbn == 'A0603'){?>
			<tr>
				<th><?=$gbn == 'A0602' ? '후원자' : '봉사자';?></th>
				<td>
					<div style="width:100%; height:25px; border-bottom:1px solid #CCCCCC;">
						<label><input id="" type="checkbox" class="checkbox" onclick="$(':checkbox[id^=\'cust_\']').attr('checked', $(this).attr('checked'));">전체</label>
						<script type="text/javascript">
							function lfFindSupport(name){
								if (name){
									$(':checkbox[id^="cust_"]').parent().parent().hide();
									$(':checkbox[id^="cust_"]').each(function(){
										if ($(this).parent().text().indexOf(name) >= 0){
											$(this).parent().parent().show();
										}
									});
								}else{
									$(':checkbox[id^="cust_"]').parent().show();
								}
							}
						</script>
						<span style="padding-left:50px;"><?=$gbn == 'A0602' ? '후원자' : '봉사자';?>명:</span><input id="" type="text" style="width:70px;" onkeyup="lfFindSupport($(this).val());">
					</div>
					<div style="width:100%; height:75px; overflow-x:hidden; overflow-y:scroll;"><?
						$sql = 'SELECT	cust_cd, cust_nm
								FROM	care_cust
								WHERE	org_no		 = \''.$orgNo.'\'
								AND		del_flag	 = \'N\'
								AND		'.($gbn == 'A0602' ? 'supporter_yn' : 'worker_yn').' = \'Y\'
								AND		'.($SR == 'S' ? 'support_yn' : 'resource_yn').' = \'Y\'
								ORDER	BY cust_nm';

						$conn->query($sql);
						$conn->fetch();

						$rowCnt = $conn->row_count();

						for($i=0; $i<$rowCnt; $i++){
							$row = $conn->select_row($i);
							echo '<div style="float:left; width:20%;"><label><input id="cust_cd" name="cust_cd" type="checkbox" class="checkbox" value="'.$row['cust_cd'].'" '.(is_numeric(StrPos($R['attendee'], '/'.$row['cust_cd'])) ? 'checked' : '').'>'.$row['cust_nm'].'</label></div>';
						}

						$conn->row_free();?>
					</div>
					<input id="attendee" name="attendee" type="hidden" value="">
					<input id="att_cnt" name="att_cnt" type="hidden" value="">
				</td>
			</tr><?
		}?>
		<tr>
			<th>내용</th>
			<td><textarea id="contents" name="contents" style="width:100%; height:300px;"><?=stripslashes($R['contents']);?></textarea></td>
		</tr>
		<tr>
			<th>사진1</th>
			<td>
				<input type="file" name="filename1" id="filename1" style="width:250px;"><?
				if ($R['pic1']){
					$pic = Explode('/', $R['pic1']);
					$pic = $pic[count($pic)-1];
					echo '첨부파일 : '.$pic;
				}else{
					echo '* JPG, JPEG 파일만 등록하여 주십시오.';
				}?>
			</td>
		</tr>
		<tr>
			<td class="center bottom last" style="padding-top:10px;" colspan="2">
				<span class="btn_pack m"><button onclick="lfSave();">저장</button></span><?
				if (is_array($R)){?>
					<span class="btn_pack m"><button onclick="lfDel();" style="color:red;">삭제</button></span>
					<span class="btn_pack m"><button onclick="lfPdf();">출력</button></span><?
				}?>
				<span class="btn_pack m"><button onclick="self.close();">닫기</button></span>
			</td>
		</tr>
	</tbody>
</table>
</form>
<?
	include_once('../inc/_footer.php');
?>