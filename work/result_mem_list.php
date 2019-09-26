<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code   = $_SESSION['userCenterCode'];
	$year   = $_POST['year'];
	$svcCd  = $_POST['svcCd'];
	$deptCd = $_POST['deptCd'];
	$memNm  = $_POST['memNm'];

	//요양보호사
	$sql = 'select min(m02_mkind) as kind
			,      m02_yjumin as jumin
			,      m02_yname as name
			,      m02_dept_cd as dept_cd
			  from m02yoyangsa
			 where m02_ccode = \''.$code.'\'
			 group by m02_yjumin, m02_yname
			 order by m02_yjumin';
	$laMem = $conn->_fetch_array($sql,'jumin');

	//부서
	$sql = 'select dept_cd as code
			,      dept_nm as name
			  from dept
			 where org_no   = \''.$code.'\'
			   and del_flag = \'N\'
			 order by order_seq';
	$laDept = $conn->_fetch_array($sql,'code');

	//일정리스트
	$sql = 'select t01_mkind as kind
		    ,      t01_yoyangsa_id1 as mem_cd
		    ,      m02_yname as mem_nm
		    ,      substring(t01_sugup_date,5,2) as month
			  from t01iljung
			 inner join m02yoyangsa
			    on m02_ccode = t01_ccode
			   and m02_mkind = t01_mkind
			   and m02_yjumin = t01_yoyangsa_id1
			 where t01_ccode  = \''.$code.'\'
			   and t01_del_yn = \'N\'
			   and left(t01_sugup_date,4) = \''.$year.'\'';

	if ($svcCd != 'all'){
		$sql .= ' and t01_mkind = \''.$svcCd.'\'';
	}

	$sql .= ' union all
		     select t01_mkind as kind
		     ,      t01_yoyangsa_id2 as mem_cd
		     ,      m02_yname as mem_nm
		     ,      substring(t01_sugup_date,5,2) as month
			   from t01iljung
			  inner join m02yoyangsa
			     on m02_ccode = t01_ccode
			    and m02_mkind = t01_mkind
			    and m02_yjumin = t01_yoyangsa_id2
			  where t01_ccode  = \''.$code.'\'
			    and t01_del_yn = \'N\'
			    and t01_yoyangsa_id2 != \'\'
			    and left(t01_sugup_date,4) = \''.$year.'\'';

	if ($svcCd != 'all'){
		$sql .= ' and t01_mkind = \''.$svcCd.'\'';
	}

	$sql .= ' order by mem_nm, mem_cd, kind, month';
	
	
	
	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();
	$liIdx = 0;

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		if (($deptCd == 'all' || $deptCd == $laMem[$row['mem_cd']]['dept_cd']) &&
			($memNm == '' || $memNm <= $laMem[$row['mem_cd']]['name'])){
			$lbAddFlag = true;
		}else{
			$lbAddFlag = false;
		}

		if (empty($laMem[$row['mem_cd']]['name'])){
			$lbAddFlag = false;
		}

		if ($lbAddFlag){
			if ($lsMemCd != $row['mem_cd']){
				$lsMemCd  = $row['mem_cd'];
				$liIdx ++;

				$laData[$lsMemCd] = array(
					'no'=>$liIdx
				,	'jumin'=>$lsMemCd
				,	'name'=>$laMem[$row['mem_cd']]['name']
				,	'dept'=>$laDept[$laMem[$row['mem_cd']]['dept_cd']]['name']
				,	'rows'=>0
				);
			}

			if ($laData[$lsMemCd]['svcCd'] != $row['kind']){
				$laData[$lsMemCd]['svcCd']  = $row['kind'];
				$laData[$lsMemCd]['svc'][$row['kind']]['svcCd'] = $row['kind'];
				$laData[$lsMemCd]['svc'][$row['kind']]['svcNm'] = $conn->_svcNm($row['kind']);
				$laData[$lsMemCd]['rows'] ++;
			}

			$laData[$lsMemCd]['svc'][$row['kind']][$row['month']] ++;
			$laData[$lsMemCd]['total'][$row['month']] ++;
		}
	}

	$conn->row_free();

	unset($lsMemCd);

	if (is_array($laData)){
		foreach($laData as $laM){
			$liIdx = 0;
			foreach($laM['svc'] as $laS){?>
				<tr><?
				if ($lsMemCd != $laM['jumin']){
					$lsMemCd  = $laM['jumin'];?>
					<td class="center" rowspan="<?=$laM['rows'];?>"><?=$laM['no'];?></td>
					<td class="left" rowspan="<?=$laM['rows'];?>"><div class="nowrap" style="width:65px;"><?=$laM['name'];?></div></td>
					<td class="left" rowspan="<?=$laM['rows'];?>"><?=$laM['dept']?></td><?
				}?>
				<td class="left"><?=$laS['svcNm'];?></td>
				<td class="left last"><?
					for($i=1; $i<=12; $i++){
						$month = ($i<10?'0':'').$i;
						$class = 'my_month ';

						if ($laS[$month] > 0){
							$class .= 'my_month_y ';
							$color  = '#000000';
							$link   = '<a href="#" onclick="return showDetail(\''.$ed->en($laM['jumin']).'\',\''.$laM['name'].'\',\''.$laS['svcCd'].'\',\''.$year.'\',\''.$month.'\');">'.$i.'월</a>';
						}else{
							$class .= 'my_month_1 ';
							$color  = '#cccccc';
							$link   = '<span style="cursor:default;">'.$i.'월</span>';
						}?>
						<div class="<?=$class;?>" style="float:left; margin-right:2px; color:<?=$color;?>;"><?=$link;?></div><?
					}?>
				</td>
				</tr><?
				unset($laS);

				$liIdx ++;
			}

			if ($liIdx > 1){?>
				<tr>
					<td class="right sum" colspan="4">서비스별 합계</td>
					<td class="left sum last"><?
						for($i=1; $i<=12; $i++){
							$month = ($i<10?'0':'').$i;
							$class = 'my_month ';

							if ($laM['total'][$month] > 0){
								$class .= 'my_month_y ';
								$color  = '#000000';
								$link   = '<a href="#" onclick="return showDetail(\''.$ed->en($laM['jumin']).'\',\''.$laM['name'].'\',\'ALL\',\''.$year.'\',\''.$month.'\');;">'.$i.'월</a>';
							}else{
								$class .= 'my_month_1 ';
								$color  = '#cccccc';
								$link   = '<span style="cursor:default;">'.$i.'월</span>';
							}?>
							<div class="<?=$class;?>" style="float:left; margin-right:2px; color:<?=$color;?>;"><?=$link;?></div><?
						}?>
					</td>
				</tr><?
			}

			unset($laM);
		}
	}else{?>
		<tr>
			<td class="center last" colspan="5">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}

	#if ($debug){
	#	echo '<tr><td>'.$lsMsg.'</td></tr>';
	#}

	unset($laMem);
	unset($laDept);
	unset($laData);?>

	<script type="text/javascript">
		function showDetail(asJumin, asName, asSvcCd, asYear, asMonth){
			$.ajax({
				type: 'POST',
				url : './result_mem_dtl.php',
				data: {
					jumin : asJumin
				,	name  : asName
				,	svcCd : asSvcCd
				,	year  : asYear
				,	month : asMonth
				},
				beforeSend: function (){
					$('#loadingBody').before('<div id=\'tempLodingBar\' style=\'position:absolute; top:270px; left:170; text-align:center;\'><div style=\'width:250px; height:100px; padding-top:30px; border:2px solid #cccccc; background-color:#ffffff;\'>'+__get_loading()+'</div></div></center></div>');
				},
				success: function (result){
					$('#tempLodingBar').remove();
					$('#loMst').after(result).hide();
					//$('#loadingBody').html(result);
				},
				error: function (){
				}
			}).responseXML;
		}
	</script><?

	include_once('../inc/_db_close.php');
?>