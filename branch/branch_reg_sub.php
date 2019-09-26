<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_referer.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	if ($_SESSION['userLevel'] == 'A'){
		$code = $_POST['code']; //지사코드
	}else{
		$code = $_SESSION['userBranchCode']; //지사코드
	}

	$type = $_POST['type']; //등록, 리스트
	$mode = $_POST['mode'];

	switch($mode){
		case _COM_:
			$com_yn = true;
			$mark_val = '';
			$barnck_gbn = _COM_NM_;
			$code = _COM_CD_;
			break;
		case _BRAN_:
			$com_yn = false;
			$mark_val = 'G';
			$barnck_gbn = _BRAN_NM_;
			break;
		case _STORE_:
			$com_yn = false;
			$mark_val = 'S';
			$barnck_gbn = _STORE_NM_;
			break;
	}

	if ($code == ''){
		// 지사코드가 없으면 다음 지사코드를 찾는다.
		if ($com_yn){
			//본사
			$branch['b00_code'] = _COM_CD_;
		}else{
			$sql = "select b00_code
					  from b00branch
					 order by b00_code";
			$conn->query($sql);
			$conn->fetch();
			$rowCount = $conn->row_count();

			$branch['b00_code'] = 0;

			for($i=0; $i<$rowCount; $i++){
				$row = $conn->select_row($i);

				$branch['b00_code'] = intVal(subStr($row[0], 1, 3));

				if ($i+1 < $branch['b00_code']){
					$branch['b00_code'] = $i;
					break;
				}
			}

			$branch['b00_code'] = '00'.($branch['b00_code'] + 1);
			$branch['b00_code'] = subStr($branch['b00_code'], strLen($branch['b00_code']) - 3, 3);

			$conn->row_free();
		}

		$branch['b00_pass'] = '1111';
	}else{
		// 지사정보를 조회한다.
		$sql = "select *
				  from b00branch
				 where b00_code = '$code'";
		$branch = $conn->get_array($sql);
	}
?>

<div style="width:48%; margin-left:10px; margin-top:10px; float:left;">
	<table class="my_table my_border_blue" style="width:100%;">
		<colGroup>
			<col width="20%">
			<col width="30%">
			<col width="20%">
			<col width="30%">
		</colGroup>
		<thead>
			<th class="head bold" colspan="4">지사</th>
		</thead>
		<tbody>
			<tr>
				<th>지사코드</th>
				<td>
				<?
					if ($com_yn){
						echo '<span class=\'left bold\'>'.$branch['b00_code'].'</span>';
						echo '<input name=\'mark\' type=\'hidden\' value=\'\'>';
						echo '<input name=\'code\' type=\'hidden\' value=\''.$branch['b00_code'].'\'>';
					}else{
						if ($code != ''){?>
							<span class="left bold"><?=$branch['b00_code'];?></span>
							<input name="mark" type="hidden" value="<?=subStr($branch['b00_code'], 0, 1);?>">
							<input name="code" type="hidden" value="<?=subStr($branch['b00_code'], 1, 3);?>"><?
						}else{?>
							<input name="mark" type="text" value="<?=$mark_val;?>" style="width:20px; text-align:center; background-color:#eee;" onFocus="document.f.code.focus();">
							<input name="code" type="text" value="<?=$branch['b00_code'];?>" tag="<?=$branch['b00_code'];?>" maxlength="3" class="no_string" style="width:30px; margin-left:-3px;" onChange="if(!_checkBranchCode(document.f.mark.value,this.value)){return false;}"><?
						}
					}
				?>
				</td>
				<th>지사구분</th>
				<td class="left"><?=$barnck_gbn;?></td>
			</tr>
			<tr>
				<th>지사명</th>
				<td><input name="name" type="text" value="<?=$branch['b00_name'];?>" style="width:100%;"></td>
				<th>지사상태</th>
				<td>
					<select name="stat" style="width:auto;">
						<option value="1" <? if ($branch['b00_stat'] == '1'){echo('selected');} ?>>서비스</option>
						<?
							if ($code != ''){
								echo '<option value=\'2\' '.($branch['b00_stat'] == '2' ? 'selected' : '').'>일시정지</option>';
								echo '<option value=\'3\' '.($branch['b00_stat'] == '3' ? 'selected' : '').'>보류</option>';
								echo '<option value=\'4\' '.($branch['b00_stat'] == '4' ? 'selected' : '').'>해지</option>';
								echo '<option value=\'9\' '.($branch['b00_stat'] == '9' ? 'selected' : '').'>기타</option>';
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th>가입일자</th>
				<td><input name="joinDate" type="text" value="<?=$myF->dateStyle($branch['b00_join_date']);?>" maxlength="8" class="date"></td>
				<th>해지일자</th>
				<td>
				<?
					if ($branch['b00_stat'] == '9'){
					?>
						<input name="quitDate" type="text" value="<?=$myF->dateStyle($branch['b00_quit_date']);?>" maxlength="8" class="date">
					<?
					}else{
					?>
						<input name="quitDate" type="text" value="<?=$myF->dateStyle($branch['b00_quit_date']);?>" maxlength="8" class="date" readOnly>
					<?
					}
				?>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<div style="width:48%; margin-left:10px; margin-top:10px; float:left;">
	<table class="my_table my_border_blue" style="width:100%;">
		<colGroup>
			<col width="20%">
			<col width="30%">
			<col width="20%">
			<col width="30%">
		</colGroup>
		<thead>
			<th class="head bold" colspan="4">대표자</th>
		</thead>
		<tbody>
			<tr>
				<th>대표자명</th>
				<td><input name="manager" type="text" value="<?=$branch['b00_manager'];?>"></td>
				<th></th>
				<td><input name="pass" type="hidden" value=""></td>
			</tr>
			<tr>
				<th>유선</th>
				<td><input name="phone" type="text" value="<?=$myF->phoneStyle($branch['b00_phone']);?>" class="phone"></td>
				<th>E-MAIL</th>
				<td><input name="email" type="text" value="<?=$branch['b00_email'];?>" style="width:100%;"></td>
			</tr>
			<tr>
				<th>무선</th>
				<td><input name="mobile" type="text" value="<?=$myF->phoneStyle($branch['b00_mobile']);?>" class="phone"></td>
				<th></th>
				<td></td>
			</tr>
		</tbody>
	</table>
</div>

<div style="width:48%; margin-left:10px; margin-top:10px; float:left;">
	<table class="my_table my_border_blue" style="width:100%;">
		<colGroup>
			<col width="20%">
			<col>
		</colGroup>
		<thead>
			<th class="head bold" colspan="4">소재지</th>
		</thead>
		<tbody>
			<tr>
				<th>우편번호</th>
				<td>
					<input name="postno1" type="text" value="<?=subStr($branch['b00_postno'],0,3);?>" class="date" maxlength="3" style="width:30px;" onKeyDown="__onlyNumber(this);"> -
					<input name="postno2" type="text" value="<?=subStr($branch['b00_postno'],3,3);?>" class="date" maxlength="3" style="width:30px;" onKeyDown="__onlyNumber(this);">
					<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onFocus="this.blur();" onClick="__helpAddress('postno1', 'postno2', 'addr1', 'addr2');">찾기</button></span>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input name="addr1" type="text" value="<?=$branch['b00_addr1'];?>" style="width:100%;">
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input name="addr2" type="text" value="<?=$branch['b00_addr2'];?>" style="width:100%;">
				</td>
			</tr>
		</tbody>
	</table>
</div>

<div style="width:23%; margin-left:10px; margin-top:10px; float:left;">
	<table class="my_table my_border_blue" style="width:100%;">
		<colGroup>
			<col width="40%">
			<col>
		</colGroup>
		<thead>
			<th class="head bold" colspan="4">사무실</th>
		</thead>
		<tbody>
			<tr>
				<th>전화번호</th>
				<td><input name="tel" type="text" value="<?=$myF->phoneStyle($branch['b00_tel']);?>" class="phone"></td>
			</tr>
			<tr>
				<th>FAX</th>
				<td><input name="fax" type="text" value="<?=$myF->phoneStyle($branch['b00_fax']);?>"class="phone"></td>
			</tr>
			<tr>
				<th>홈페이지</th>
				<td><input name="homepage" type="text" value="<?=$branch['b00_homepage'];?>" style="width:100%;"></td>
			</tr>
		</tbody>
	</table>
</div>

<div style="width:24%; margin-left:10px; margin-top:10px; float:left;">
	<table class="my_table my_border_blue" style="width:100%;">
		<colGroup>
			<col width="40%">
			<col>
		</colGroup>
		<thead>
			<th class="head bold" colspan="4">은행</th>
		</thead>
		<tbody>
			<tr>
				<th>은행명</th>
				<td><input name="banknm" type="text" value="<?=$branch['b00_banknm'];?>" style="width:100%;"></td>
			</tr>
			<tr>
				<th>계좌번호</th>
				<td><input name="bankno" type="text" value="<?=$branch['b00_bankno'];?>" style="width:100%;"></td>
			</tr>
			<tr>
				<th>예금주</th>
				<td><input name="bankacct" type="text" value="<?=$branch['b00_bankacct'];?>" style="width:100%;"></td>
			</tr>
		</tbody>
	</table>
</div>

<div style="width:33%; margin-left:10px; margin-top:10px; float:left;">
	<table class="my_table my_border_blue" style="width:100%;">
		<colGroup>
			<col width="30%">
			<col>
		</colGroup>
		<thead>
			<th class="head bold" colspan="4">사업자</th>
		</thead>
		<tbody>
			<tr>
				<th>사업자번호</th>
				<td><input name="regNo" type="text" value="<?=$myF->formatString($branch['b00_reg_no'], '###-##-#####');?>" style="width:85px;" onFocus="__replace(this, '-', '');" onBlur="__formatString(this, '###-##-#####');"></td>
			</tr>
			<tr>
				<th>업태</th>
				<td><input name="regoftype" type="text" value="<?=$branch['b00_reg_oftype'];?>" style="width:100%;"></td>
			</tr>
			<tr>
				<th>업종</th>
				<td><input name="regofitem" type="text" value="<?=$branch['b00_reg_ofitem'];?>" style="width:100%;"></td>
			</tr>
		</tbody>
	</table>
</div>

<div style="width:23%; margin-left:10px; margin-top:10px; float:left;">
	<table class="my_table my_border_blue" style="width:100%;">
		<colGroup>
			<col width="40%">
			<col>
		</colGroup>
		<thead>
			<th class="head bold" colspan="4">법인</th>
		</thead>
		<tbody>
			<tr>
				<th>법인번호</th>
				<td><input name="regnum" type="text" value="<?=$branch['b00_reg_num'];?>" style="width:85px;"></td>
			</tr>
			<tr>
				<th>등록일자</th>
				<td><input name="regdt" type="text" value="<?=$branch['b00_reg_dt'];?>" class="date"></td>
			</tr>
			<tr>
				<th>법인성격</th>
				<td>
					<select name="regchar" style="width:auto;">
						<option value="1" <? if($branch['b00_reg_char'] == '1'){echo 'selected';} ?>>공공</option>
						<option value="2" <? if($branch['b00_reg_char'] == '2'){echo 'selected';} ?>>비영리</option>
						<option value="3" <? if($branch['b00_reg_char'] == '3'){echo 'selected';} ?>>민간</option>
					</select>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<div style="width:39%; margin-left:10px; margin-top:10px; float:left;">
	<table class="my_table my_border_blue" style="width:100%;">
		<colGroup>
			<col>
		</colGroup>
		<thead>
			<th class="head bold" colspan="4">비고</th>
		</thead>
		<tbody>
			<tr>
				<td>
					<textarea name="other" style="width:100%; height:66px;"><?=stripSlashes($branch['b00_other']);?></textarea>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<div style="margin:10px; clear:both;">
	<table class="my_table my_border_blue" style="width:100%;">
		<colGroup>
			<col>
		</colGroup>
		<tbody>
			<tr>
				<th class="right">
				<?
					if ($type == 'reg'){
						echo '<a href=\'#\' onclick=\'_branchRegOk();\'>등록</a>';
					}else{
						echo '<a href=\'#\' onclick=\'_branchRegOk();\'>수정</a> | ';
						echo '<a href=\'#\' onclick=\'_branchList();\'>리스트</a>';
					}
				?>
				</th>
			</tr>
		</tbody>
	</table>
</div>

<input name="type" type="hidden" value="<?=$type;?>">
<input name="mode" type="hidden" value="<?=$mode;?>">
<input name="com_yn" type="hidden" value="<?=$com_yn?'Y':'N';?>">
<?
	include_once("../inc/_db_close.php");
?>