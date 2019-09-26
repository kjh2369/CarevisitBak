<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*
	 * 게시판 카테고리 생성
	 */

	$type	= $_POST['type'];
	$cd		= $_POST['cd'];

	if ($cd > 0){
	}else{
		//마스터?>
		<table class="my_table" style="width:100%;">
			<colgroup>
				<col width="50px">
				<col>
			</colgroup>
			<tbody>
				<tr>
					<th class="center">명칭</th>
					<td><input id="txtName" type="text" value="" style="width:100%;"></td>
				</tr>
			</tbody>
		</table><?
	}?>
	<div class="center" style="margin-top:15px;">
		<span class="btn_pack m"><button onclick="lfSave();">저장</button></span>
		<span class="btn_pack m"><button onclick="lfClose();">취소</button></span>
	</div><?

	include_once('../inc/_db_close.php');
?>