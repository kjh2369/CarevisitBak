<?
	include_once('../inc/_header.php');
	include_once('../inc/_myFun.php');

	$orgNo = $_GET['orgNo'];
	$date = $_GET['date'];
	$seq = $_GET['seq'];

	$sql = 'SELECT	remark
			FROM	cv_pay_in
			WHERE	org_no		= \''.$orgNo.'\'
			AND		issue_dt	= \''.$date.'\'
			AND		issue_seq	= \''.$seq.'\'
			AND		del_flag	= \'N\'';

	$remark = $conn->get_data($sql);
?>
<div class="title title_border">입금내역 비고 조회 및 수정</div>
<div style="padding:5px;"><textarea name="txtRemark" style="width:100%; height:180px;"><?=$remark;?></textarea></div>
<div style="text-align:center; margin-top:15px;">
	<span class="btn_pack m"><button onclick="lfSave();">저장</button></span>
	<span class="btn_pack m"><button onclick="self.close();">닫기</button></span>
</div>
<script type="text/javascript">
	var opener = window.dialogArguments;

	$('textarea').each(function(){
		__init_object(this);
	});

	function lfSave(){
		$.ajax({
			type:'POST'
		,	url:'./payin_remark_seve.php'
		,	data:{
				'orgNo':'<?=$orgNo;?>'
			,	'date':'<?=$date;?>'
			,	'seq':'<?=$seq;?>'
			,	'remark':$('textarea[name="txtRemark"]').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (!result){
					opener.result = true;
					opener.remark = $('textarea[name="txtRemark"]').val();
					self.close();
				}else{
					alert(result);
				}
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}
</script>
<?
	include_once('../inc/_footer.php');
?>