<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$conn->fetch_type = 'assoc';

	$code	= $_SESSION['userCenterCode'];
	$sr		= $_POST['sr'];
	$suga	= $_POST['suga'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$from	= $_POST['from'];
	$to		= $_POST['to'];

	//휴일리스트
	$sql = 'select cast(substring(mdate,7) as unsigned) as day
			,      holiday_name as nm
			  from tbl_holiday
			 where left(mdate,6) = \''.$year.$month.'\'';
	$loHolidayList = $conn->_fetch_array($sql,'day');

	//자원리스트
	$sql = 'SELECT	cust_cd AS cd
			,		cust_nm AS nm
			FROM	care_cust
			WHERE	org_no = \''.$code.'\'';
	$arrCust = $conn->_fetch_array($sql,'cd');

	//서비스명
	$sql = 'SELECT	suga_nm
			FROM	care_suga
			WHERE	org_no  = \''.$code.'\'
			AND		suga_sr = \''.$sr.'\'
			AND		DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
			AND		DATE_FORMAT(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
			AND		CONCAT(suga_cd,suga_sub) = \''.$suga.'\'
			ORDER	BY from_dt DESC
			LIMIT	1';
	$lsSugaNm	= $conn->get_data($sql);

	//일정조회
	$sql = 'SELECT	t01_jumin			AS jumin
			,		m03_name			AS name
			,		t01_sugup_date		AS date
			,		t01_sugup_fmtime	AS time
			,		t01_svc_subcode		AS svc_cd
			,		t01_status_gbn		AS stat
			,		t01_yoyangsa_id1	AS resource_cd
			,		t01_yname1			AS resource_nm
			,		t01_yoyangsa_id2	AS mem_cd
			,		t01_yname2			AS mem_nm
			,		t01_suga			AS cost
			,		t01_sugup_seq		AS seq
			,		t01_request			AS rst
			,		t01_modify_pos		AS pos
			FROM	t01iljung
			INNER	JOIN	m03sugupja
					ON		m03_ccode = t01_ccode
					AND		m03_mkind = \'6\'
					AND		m03_jumin = t01_jumin
			WHERE	t01_ccode		= \''.$code.'\'
			AND		t01_mkind		= \''.$sr.'\'
			AND		t01_suga_code1	= \''.$suga.'\'
			AND		t01_del_yn		= \'N\'
			AND		t01_sugup_date >= \''.$year.$month.'01\'
			AND		t01_sugup_date <= \''.$year.$month.'31\'
			ORDER	BY time, resource_nm, mem_nm, name';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if (!$row['resource_nm']) continue;
		if (!$row['rst']) $row['rst'] = 'SERVICE';

		$liDay		= IntVal(SubStr($row['date'],6));
		$lsTime		= $row['time'].($row['rst'] != 'SERVICE' ? '_R' : '');
		$lsResource	= $row['resource_cd'];
		$lsMemCd	= $ed->en($row['mem_cd']);

		//$liIdx = SizeOf($laIljung[$liDay][$lsTime]);
		$liIdx = SizeOf($laIljung[$liDay][$lsTime]);

		if (is_array($laIljung[$liDay][$lsTime])){
			foreach($laIljung[$liDay][$lsTime] as $idx => $tmp){
				if ($tmp['resourceCd'] == $lsResource && $tmp['memCd'] == $lsMemCd){
					$liIdx = $idx;
					break;
				}
			}
		}

		if ($row['stat'] == '1' || $row['stat'] == '5'){
			$lsStat = '1';
		}else{
			$lsStat = '9';
		}

		if (!IsSet($laIljung[$liDay][$lsTime][$liIdx])){
			$laIljung[$liDay][$lsTime][$liIdx] = array(
				'day'		=>$liDay
			,	'cnt'		=>$lsTime.'_'.$liIdx
			,	'week'		=>$arrWeekGbn[$row['date']]
			,	'svcKind'	=>$row['svc_cd']
			,	'from'		=>$myF->timeStyle($row['time'])
			,	'resourceCd'=>$lsResource
			,	'resourceNm'=>$row['resource_nm']
			,	'memCd'		=>$ed->en($row['mem_cd'])
			,	'memNm'		=>$row['mem_nm']
			,	'sugaName'	=>$laSugaList[$lsSugaCd]['name']
			,	'sugaCd'	=>$suga
			,	'sugaNm'	=>$lsSugaNm
			,	'cost'		=>$row['cost']
			,	'ynAddRow'	=>'N'
			,	'ynSave'	=>'Y'
			,	'stat'		=>''
			,	'seq'		=>''
			,	'name'		=>''
			,	'showNm'	=>''
			,	'client'	=>''
			,	'birth'		=>''
			,	'gender'	=>''
			,	'clientCnt'	=>0
			,	'request'	=>$row['rst']
			);
		}


		#일정오버 시 이름표시
		if(strlen($row['name']) > 9){
			$showNm = substr($row['name'], 0,9).'...';
		}else {
			$showNm = $row['name'];
		}

		$laIljung[$liDay][$lsTime][$liIdx]['stat']		.= (($laIljung[$liDay][$lsTime][$liIdx]['stat'] ? chr(11) : '').$row['stat']);
		$laIljung[$liDay][$lsTime][$liIdx]['seq']		.= (($laIljung[$liDay][$lsTime][$liIdx]['seq'] ? chr(11) : '').$row['seq']);
		$laIljung[$liDay][$lsTime][$liIdx]['name']		.= (($laIljung[$liDay][$lsTime][$liIdx]['name'] ? chr(11) : '').$row['name']);
		$laIljung[$liDay][$lsTime][$liIdx]['client']	.= (($laIljung[$liDay][$lsTime][$liIdx]['client'] ? '_TAB_' : '').$ed->en($row['jumin']));
		//$laIljung[$liDay][$lsTime][$liIdx]['showNm']	.= (($laIljung[$liDay][$lsTime][$liIdx]['showNm'] ? chr(11) : '').$showNm);
		//$laIljung[$liDay][$lsTime][$liIdx]['birth']		.= (($laIljung[$liDay][$lsTime][$liIdx]['birth'] ? chr(11) : '').$myF->issToBirthday($row['jumin'],'.'));
		//$laIljung[$liDay][$lsTime][$liIdx]['gender']	.= (($laIljung[$liDay][$lsTime][$liIdx]['gender'] ? chr(11) : '').($myF->issToGender($row['jumin']) != '' ? $myF->issToGender($row['jumin']) : '　'));
		$laIljung[$liDay][$lsTime][$liIdx]['clientCnt'] ++;

	}

	$conn->row_free();
?>
<style>
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
	.divCalObj{
		clear:both;
		width:100%;
	}
</style>
<script type="text/javascript">
	function lfMouseOver(aoObj){
		$(aoObj).css('background-color','#dfe5f5');
	}

	function lfMouseOut(aoObj){
		$(aoObj).css('background-color','#ffffff');
	}

	//서비스 대상자 보여주기
	function lfCalInfoShow(obj){
		var objModal= new Object();
		var url		= './care_iljung_client_view.php';
		var style	= 'dialogWidth:800px; dialogHeight:400px; dialogHide:yes; scroll:no; status:no';

		objModal.win	= window;
		objModal.SR		= '<?=$sr;?>';
		objModal.date	= '<?=$year;?>-<?=$month;?>-'+($(obj).attr('day') < 10 ? '0' : '')+$(obj).attr('day');
		objModal.time	= $(obj).attr('from');
		objModal.sugaCd	= $(obj).attr('sugaCd');
		objModal.resCd	= $(obj).attr('resourceCd');
		objModal.memCd	= $(obj).attr('memCd');
		objModal.request= $(obj).attr('request');
		objModal.result	= false;

		window.showModalDialog(url, objModal, style);

		if (objModal.result){
			lfLoadIljung();
		}

		/*
		var w = 300;
		var h = 200;
		var t = (window.screen.height - h) / 2;
		var l = (window.screen.width  - w)  / 2;

		window.open('care_iljung_client_view.php?name='+nm+'&gender='+gd+'&birth='+birth+'&year='+year+'&month='+month+'&day='+day,'VIEW','top='+t+',left='+l+',width='+w+',height='+h+',scrollbars=no,resizable=no,location=no,toolbar=no,menubar=no,directories=no');
		*/
	}
</script>
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
			<td class="center top clsCalCol<?=$liWeekday;?> <?=$liWeekday == 6 ? 'last' : '';?>" style="height:50px; border-bottom:2px solid #afafaf; background-color:<?=$lsBackClr;?>;" >
				<div class="center bold divCalDay" style="color:<?=$lsFontClr;?>"><?=$i;?></div><?
				if ($liWeekday == 0)
					$ynHoliday = 'Y';
				else
					$ynHoliday = 'N';

				if (!empty($loHolidayList[$i]['nm'])){
					$ynHoliday = 'Y';?>
					<div class="left divCalTxt" style="margin-top:3px;"><?=$loHolidayList[$i]['nm'];?></div><?
				}?>
				<div id="loCal_<?=$i;?>" ynHoliday="<?=$ynHoliday;?>" week="<?=$liWeekly;?>" class="divCalObj"><?
				if (is_array($laIljung[$i])){
					if ($lbEdit){
						$evtClick	= 'lfShowCalendar(this,\'1\')';
					}else{
						$evtClick	= '';
					}

					$IsFirst = true;

					foreach($laIljung[$i] as $tmpArr){
						foreach($tmpArr as $row){
							if (!$IsFirst){
								$lsBorderTop = 'border-top:1px dotted #666666;';
							}else{
								$lsBorderTop = '';
							}

							$IsFirst = false;

							//$name = str_replace(chr(11), ' ', $row['showNm']);	//대상자명
							//$gender = str_replace(chr(11), ' ', $row['gender']);	//대상자성별
							//$birth = str_replace(chr(11), ' ', $row['birth']);	//대상자생년월일?>
							<div id="loCal_<?=$row['day'];?>_<?=$row['cnt'];?>" class="<?=$row['request'] == 'SERVICE' ? 'clsCal' : 'clsGrp';?>" onclick="lfCalInfoShow(this); return false;" style="clear:both; text-align:left; padding-left:3px;<?=$lsBorderTop;?>" onmouseover="lfMouseOver(this);" onmouseout="lfMouseOut(this);"
								day			="<?=$row['day'];?>"
								cnt			="<?=$row['cnt'];?>"
								week		="<?=$row['week'];?>"
								svcKind		="<?=$row['svcKind'];?>"
								from		="<?=$row['from'];?>"
								resourceCd	="<?=$row['resourceCd'];?>"
								resourceNm	="<?=$row['resourceNm'];?>"
								memCd		="<?=$row['memCd'];?>"
								memNm		="<?=$row['memNm'];?>"
								sugaName	="<?=$row['sugaName'];?>"
								sugaCd		="<?=$row['sugaCd'];?>"
								sugaNm		="<?=$row['sugaNm'];?>"
								cost		="<?=$row['cost'];?>"
								client		="<?=$row['client'];?>"
								ynAddRow	="<?=$row['ynAddRow'];?>"
								ynSave		="<?=$row['ynSave'];?>"
								stat		="<?=$row['stat'];?>"
								seq			="<?=$row['cnt'];?>"
								svcSeq		="<?=$row['seq'];?>"
								request		="<?=$row['request'];?>">
								<div class="divCalCont" style="font-weight:bold; cursor:default;">
									<div id="btnRemove" style="float:right; width:auto; margin-right:3px;" onclick="return false;"><?
										if ($row['request'] == 'PERSON'){?>
											<span style="font-size:11px; color:BLUE;">개별</span><?
										}else if ($row['request'] == 'SERVICE'){?>
											<img src="../image/btn_close.gif" onclick="lfCalRemove('<?=$row['day'];?>','<?=$row['cnt'];?>'); return false;" style="margin-top:3px;"><?
										}else if ($row['request']){?>
											<span style="font-size:11px; color:BLUE;">묶음</span><?
										}else{?>
											<img src="../image/btn_close.gif" onclick="lfCalRemove('<?=$row['day'];?>','<?=$row['cnt'];?>'); return false;" style="margin-top:3px;"><?
										}?>
									</div>
									<div id="lblTimeStr" style="float:left; width:auto; cursor:default;"><?=$row['from'];?></div>
								</div>
								<div class="divCalCont" style="cursor:default;">대상자 : <?=$row['clientCnt'];?>명</div><?
								if ($row['memCd']){?>
									<div id="lblSupplyStr" class="divCalCont" style="cursor:default;">담당직원:<?=$row['memNm'];?></div><?
								}?>
								<div id="lblSugaStr" class="divCalCont" style="cursor:default;">
									<div style="float:left; width:auto; margin-right:5px; margin-top:-1px;"><?=$row['resourceNm'];?></div>
								</div>
								<div class="divCalCont" style="cursor:default; display:none;"><span id="divErrorMsg" style="color:#ff0000; font-size:11px; font-weight:bold;"></span></div>
							</div><?
						}
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
<?
	include_once('../inc/_db_close.php');
?>