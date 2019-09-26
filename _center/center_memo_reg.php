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
			<td><input id="txtMemoSubject" type="text" value="<?=stripslashes($R['subject']);?>" style="width:100%;"></td>
		</tr>
		<tr>
			<th class="center">작성자</th>
			<td>
				<select id="cboMemoReg" style="width:auto;">
					<option value="김종성" <?=$R['reg_nm'] == '김종성' ? 'selected' : '';?>>김종성</option>
					<option value="이양수" <?=$R['reg_nm'] == '이양수' ? 'selected' : '';?>>이양수</option>
					<option value="김재용" <?=$R['reg_nm'] == '김재용' ? 'selected' : '';?>>김재용</option>
					<option value="안상현" <?=$R['reg_nm'] == '안상현' ? 'selected' : '';?>>안상현</option>
					<option value="김주완" <?=$R['reg_nm'] == '김주완' ? 'selected' : '';?>>김주완</option>
				</select>
			</td>
		</tr>
		<tr>
			<th class="center">내욕</th>
			<td><textarea id="txtMemoContents" style="width:100%; height:200px;"><?=stripslashes($R['contents']);?></textarea></td>
		</tr>
		<tr>
			<td class="center" colspan="2" style="padding:5px;">
				<span class="btn_pack m"><button onclick="lfMemo('MemoSave',{'seq':'<?=$seq;?>'});">저장</button></span>
				<span class="btn_pack m"><button onclick="lfMemo('MemoList');">취소</button></span>
			</td>
		</tr>
	</tbody>
</table>
</div>
<?
	Unset($R);
	include_once('../inc/_db_close.php');
?>