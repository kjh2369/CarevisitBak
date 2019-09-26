<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$jumin	= $ed->de($_POST['jumin']);
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$sr		= $_POST['sr'];

	//휴일리스트
	$sql = 'SELECT	CASAT(SUBSTRING(mdate,7) AS unsigned) AS day
			,		holiday_name AS nm
			FROM	tbl_holiday
			WHERE	LEFT(mdate,6) = \''.$year.$month.'\'';

	$loHolidayList = $conn->_fetch_array($sql,'day');

	//고객명
	$sql = 'SELECT	DISTINCT m03_jumin AS jumin
			,		m03_name AS name
			FROM	m03sugupja
			WHERE	m03_ccode = \''.$code.'\'';

	$client = $conn->_fetch_array($sql,'jumin');

	$sql = 'SELECT	iljung_dt
			,		iljung_seq
			,		iljung_jumin
			,		iljung_from
			,		iljung_to
			,		iljung_proc
			FROM	care_counsel_iljung
			WHERE	org_no = \''.$code.'\'
			AND		jumin = \''.$jumin.'\'
			AND		iljung_sr = \''.$sr.'\'
			AND		LEFT(iljung_dt,6) = \''.$year.$month.'\'
			AND		del_flag = \'N\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$day = IntVal(SubStr($row['iljung_dt'],6,2));
		$seq = $row['iljung_seq'];

		$data[$day][$seq] = Array(
				'jumin'=>$ed->en($row['iljung_jumin'])
			,	'name'=>$client[$row['iljung_jumin']]['name']
			,	'date'=>$row['iljung_dt']
			,	'from'=>$row['iljung_from']
			,	'to'=>$row['iljung_to']
			,	'proc'=>$row['iljung_proc']
		);
	}

	$conn->row_free();
?>
	<style>
		.thStyle{
			border-bottom:2px solid #a6c0f3;
		}
		.divCalDay{
			float:left;
			width:25px;
			background-color:#efefef;
			border-right:1px solid #cccccc;
			border-bottom:1px solid #cccccc;
		}
		.divCalTxt{
			float:left;
			width:auto;
			color:#ff0000;
			font-size:11px;
			height:15px;
			line-height:15px;
		}
		.divCalBtn{
			float:right;
			width:auto;
			font-size:11px;
			height:15px;
			line-height:15px;
		}
		.divCalObj{
			clear:both;
			width:100%;
		}
	</style>
	<table id="tblCalBody" ynLoad="N" class="my_table" style="width:100%; border-bottom:none;">
		<colgroup>
			<col width="15%">
			<col width="14%">
			<col width="14%">
			<col width="14%">
			<col width="14%">
			<col width="14%">
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head bold thStyle clsCalCol0"><div style="cursor:default; color:ff0000;">일</div></th>
				<th class="head bold thStyle clsCalCol1"><div style="cursor:default; color:000000;">월</div></th>
				<th class="head bold thStyle clsCalCol2"><div style="cursor:default; color:000000;">화</div></th>
				<th class="head bold thStyle clsCalCol3"><div style="cursor:default; color:000000;">수</div></th>
				<th class="head bold thStyle clsCalCol4"><div style="cursor:default; color:000000;">목</div></th>
				<th class="head bold thStyle clsCalCol5"><div style="cursor:default; color:000000;">금</div></th>
				<th class="head bold thStyle clsCalCol6 last"><div style="cursor:default; color:0000ff;">토</div></th>
			</tr>
		</thead>
		<tbody><?
			$liFirstWeekly = date('w', strtotime($year.$month.'01'));
			$liLastDay = intval($myF->dateAdd('day', -1, $myF->dateAdd('month', 1, $year.$month.'01', 'Y-m-d'), 'd'));
			$liChkWeek = ceil(($liLastDay + $liFirstWeekly) / 7);
			$liWeekday = 0;
			$liWeekly  = 0;
			$lbLastWeek= false;

			for($i=0; $i<$liFirstWeekly; $i++){
				if ($liWeekday % 7 == 0){?>
					<tr class="clsCalRow" week="<?=$liWeekly;?>"><?
				}?>
				<td class="center clsCalCol<?=$liWeekday;?>" style="border-bottom:2px solid #afafaf;">
					<div week="<?=$liWeekly;?>" class="divCalObj"></div>
				</td><?
				$liWeekday ++;
			}

			if ($liWeekday > 0)
				$liWeekly = 1;
			else
				$liWeekly = 0;

			for($i=1; $i<=$liLastDay; $i++){
				if ($liWeekday % 7 == 0){
					$liWeekday = 0;
					$liWeekly ++;

					if ($liChkWeek <= $liWeekly) $lbLastWeek = true;
					if ($liFirstWeekly != 0){?>
						</tr><?
					}?>
					<tr class="clsCalRow" week="<?=$liWeekly;?>"><?
				}

				switch($liWeekday){
					case 0: $lsFontClr = '#ff0000'; break;
					case 6: $lsFontClr = '#0000ff'; break;
					default: $lsFontClr = '#000000';
				}

				if (!empty($loHolidayList[$i]['nm'])){
					if ($loHolidayList[$i]['holiday'] != 'N'){
						$lsFontClr = '#ff0000';
					}
				}

				if ($liWeekly % 2 == 0){
					$lsBackClr = '#f9fcff';
				}else{
					$lsBackClr = '#ffffff';
				}?>
				<td class="center top clsCalCol<?=$liWeekday;?> <?=$liWeekday == 6 ? 'last' : '';?>" style="height:50px; border-bottom:2px solid #afafaf; background-color:<?=$lsBackClr;?>;">
					<div class="center bold divCalDay" style="color:<?=$lsFontClr;?>"><?=$i;?></div><?
					if ($liWeekday == 0)
						$ynHoliday = 'Y';
					else
						$ynHoliday = 'N';

					if (!empty($loHolidayList[$i]['nm'])){
						$ynHoliday = 'Y';?>
						<div class="left divCalTxt" style="margin-top:3px;"><?=$loHolidayList[$i]['nm'];?></div><?
					}?>
					<div id="btnAdd_<?=$i;?>" class="right divCalBtn" style="margin-top:3px; display:none;">추가</div>
					<div id="loCal_<?=$i;?>" ynHoliday="<?=$ynHoliday;?>" week="<?=$liWeekly;?>" class="divCalObj"><?
						if (Is_Array($data[$i])){
							foreach($data[$i] as $seq => $cal){?>
								<div id="loCal_<?=$i;?>_'<?=$seq;?>'" class="clsCal" onmouseover="_planMouseOver(this);" onmouseout="_planMouseOut(this);"
									day="<?=$i;?>"
									cnt="<?=$seq;?>"
									jumin="<?=$cal['jumin'];?>"
									from=<?=$cal['from'];?>""
									to="<?=$cal['to'];?>"
									proc=<?=$cal['proc'];?>""
									duplicate="1"
									seq="<?=$seq;?>"
									stat="1">
									<div class="divCalCont" style="font-weight:bold; cursor:default; line-height:1.2em;">
										<div id="btnRemove" style="float:right; width:auto; margin-right:3px;"><img src="../image/btn_close.gif" onclick="return lfCalRemove($(this).parent().parent().parent());" style="margin-top:3px;"></div>
										<div id="lblTimeStr" style="float:left; width:auto; cursor:default;"><?=$myF->timeStyle($cal['from']).'~'.$myF->timeStyle($cal['to']);?></div>
									</div>
									<div id="lblMemStr" class="divCalCont" style="cursor:default; line-height:1.2em; text-align:left;"><?=$cal['name'];?></div>
									<div id="lblSugaStr" class="divCalCont" style="cursor:default; line-height:1.2em;"><div style="float:left; width:auto; margin-right:5px; margin-top:-1px;">상담지원</div></div>
									<div class="divCalCont" style=""><span id="divErrorMsg" style="color:#ff0000; font-size:11px; font-weight:bold; cursor:default; display:none;">일정중복</span></div>
								</div><?
							}
						}?>
					</div>
				</td><?
				$liWeekday ++;
			}

			if ($liWeekday % 7 == 0){?>
				</tr><?
			}else{
				for($i=$liWeekday+1; $i<=7; $i++){?>
					<td class="center clsCalCol<?=$liWeekday;?> <?=$liWeekday == 6 ? 'last' : '';?>" style="border-bottom:2px solid #afafaf;">
						<div week="<?=$liWeekly;?>" class="divCalObj"></div>
					</td><?
					$liWeekday ++;
				}?>
				</tr><?
			}?>
		</tbody>
	</table>
	<div id="calCont" style="clear:both; width:100%;"></div>
<?
	include_once('../inc/_db_close.php');
?>
