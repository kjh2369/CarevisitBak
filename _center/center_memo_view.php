<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$memoType='1';
	$orgNo	= $_POST['orgNo'];
	$seq	= $_POST['seq'];

	if ($seq){
		$sql = 'SELECT	subject, reg_nm, contents
				FROM	cv_memo
				WHERE	memo_type=\''.$memoType.'\'
				AND		org_no	= \''.$orgNo.'\'
				AND		seq		= \''.$seq.'\'
				AND		del_flag= \'N\'';

		$R = $conn->get_array($sql);
	}
?>
<div style="padding:10px;">
<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">제목</th>
			<td class="left"><?=stripslashes($R['subject']);?></td>
		</tr>
		<tr>
			<th class="center">작성자</th>
			<td class="left"><?=$R['reg_nm'];?></td>
		</tr>
		<tr>
			<th class="center">내욕</th>
			<td class="top"><div style="overflow-x:hidden; overflow-y:auto; height:200px; padding:0 5px 0 5px;"><?=nl2br(stripslashes($R['contents']));?></div></td>
		</tr>
		<tr>
			<td class="center" colspan="2" style="padding:5px;">
				<span class="btn_pack m"><button onclick="lfMemo('MemoList');">닫기</button></span>
			</td>
		</tr>
	</tbody>
</table>
</div>
<?
	Unset($R);
	include_once('../inc/_db_close.php');
?>