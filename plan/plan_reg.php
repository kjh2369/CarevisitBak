<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$org_no = $_SESSION['userCenterCode'];
	$SR = $_POST['SR'];
	$bodyid = $_POST['bodyid'];
	$year = $_POST['year'];
	$month = $_POST['month'];
	$yymm = $year.($month < 10 ? '0' : '').$month;

	if (SubStr($bodyid, 0, 1) != '#') $bodyid = '#'.$bodyid;

	$sql = 'SELECT	m00_store_nm
			FROM	m00center
			WHERE	m00_mcode = \''.$org_no.'\'
			';
	$org_name = $conn->get_data($sql);
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('<?=$bodyid;?> a[id^="BTN_"]').on('click', function(){
			switch($(this).prop('id')){
				case 'BTN_CLOSE':
					$('<?=$bodyid;?>').html('').parent().hide();
					break;

				default:
					alert($(this).prop('id'));
			}
		});

		SetMouseMove($('<?=$bodyid;?> .pop_title:first'));
	});
</script>
<div class="pop_title">
	<h3><i class="fa fa-check-square Col_b" aria-hidden="true"></i> 계획등록</h3>
</div>
<table class="my_table">
	<colgroup>
		<col width="100px" span="2">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="center" style="border-top:none;" colspan="2"><?=$org_name;?></td>
			<th>년월</th>
			<th>대상자</th>
			<th>성별</th>
			<th>생년월일</th>
			<th>나이</th>
			<th>분류</th>
			<th>주소</th>
		</tr>
		<tr>
			<td class="center"><?=$_SESSION['userName'];?></td>
			<td class="center">직위</td>
			<td><?=$year;?>년 <?=$month;?>월</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
	</tbody>
</table>
<table class="my_table">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>제공서비스</th>
			<th>자원</th>
			<th>시간</th>
			<th>담당생활관리사</th>
			<th rowspan="2">소요<br>시간</th>
			<th>1주차</th>
			<th>2주차</th>
			<th>3주차</th>
			<th>4주차</th>
			<th>5주차</th>
			<th>서비스(종류)</th>
			<th>메모관리</th>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
	</tbody>
</table>
<table class="my_table">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td>특정일 없는 주간 서비스</td>
		</tr>
	</tbody>
</table>
<table class="my_table">
	<colgroup>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td style="border-top:none;">취소</td>
			<td style="border-top:none;"><?=$myF->yymmStyle($yymm, '/');?></td>
			<td style="border-top:none;">배정</td>
			<th>제공자</th>
			<th>직책</th>
			<th>제공서비스명</th>
			<th>총제공시간</th>
			<th>횟수</th>
			<th rowspan="2">메<br>모<br>관<br>리</th>
			<th>No</th>
			<th>작성일</th>
			<th>작성자</th>
			<th>내용</th>
		</tr>
		<tr>
			<td style="padding:0; vertical-align:top;" colspan="3">
				<table class="my_table" style="border:none; margin-right:10px;">
					<colgroup>
						<col>
					</colgroup>
					<tbody>
						<tr>
							<th>주</th>
							<th style="color:red;">일</th>
							<th>월</th>
							<th>화</th>
							<th>수</th>
							<th>목</th>
							<th>금</th>
							<th style="color:blue; border-right:0;">토</th>
						</tr><?
						$dow = $myF->dowidx($yymm.'01');
						$lastday = $myF->lastday($year, $month);
						$day = 0;

						for($i=1; $i<=5; $i++){?>
							<tr>
							<td class="center" style="<?=$i == 5 ? 'border-bottom:none;' : '';?>"><?=$i;?>주</td><?
							for($j=0; $j<7; $j++){
								if ($i == 1 && $dow == $j){
									$day ++;
								}

								if ($j == 0){
									$color = 'red';
								}else if ($j == 6){
									$color = 'blue';
								}else{
									$color = '';
								}?>
								<td class="center" style="color:<?=$color;?>; <?=$i == 5 ? 'border-bottom:none;' : '';?>"><?
									if ($day > 0){
										echo $day;
										$day ++;
									}else{
										echo '&nbsp;';
									}?>
								</td><?

								if ($day >= $lastday) $day = 0;
							}?>
							</tr><?
						}?>
					</tbody>
				</table>
			</td>
			<td style="padding:0; vertical-align:top;" colspan="5">
				<div style="overflow-x:hidden; overflow-y:scroll; height:100px;">
					<table class="my_table" style="border:none; margin-right:10px;">
						<colgroup>
							<col>
						</colgroup>
						<tbody>
							<tr>
								<td>&nbsp;</td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
						</tbody>
					</table>
				</div>
			</td>
			<td style="padding:0; vertical-align:top;" colspan="4">
				<div style="overflow-x:hidden; overflow-y:scroll; height:100px;">
					<table class="my_table" style="border:none; margin-right:10px;">
						<colgroup>
							<col>
						</colgroup>
						<tbody>
							<tr>
								<td>&nbsp;</td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
						</tbody>
					</table>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table">
	<colgroup>
		<col>
	</colgroup>
	<thead>
		<tr>
			<th colspan="8"><?=$year;?>년 <?=$month;?>월 일정표</th>
		</tr>
		<tr>
			<th>특정일없는 주간서비스</th>
			<th style="color:red;">일</th>
			<th>월</th>
			<th>화</th>
			<th>수</th>
			<th>목</th>
			<th>금</th>
			<th style="color:blue;">토</th>
		</tr>
	</thead>
</table>
<div style="overflow-x:hidden; overflow-y:scroll; height:100px;">
	<table class="my_table">
		<colgroup>
			<col>
		</colgroup>
		<tbody>
			<tr>
				<td>&nbsp;</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</tbody>
	</table>
</div>
<div class="icon_btn pop_close"><a href="#" id="BTN_CLOSE"><span class="icon_s3 font_s3_2 Col_b"><i class="fa fa-times" aria-hidden="true"></i></span></a></div>
<?
	include_once('../inc/_db_close.php');
?>