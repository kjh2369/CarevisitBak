<?
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
?>
<table class="my_table my_border_blue" style="width:100%; border-left:1px solid;">
	<colgroup>
		<col width="60px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head last bold" colspan="2">이용 가능 서비스</th>
		</tr>
	</thead>
	<tbody><?
		$liAddCnt = 0;

		foreach($k_list as $laRow){
			$lbAdd = true;
			$lbSvc = false;
			$lsDt  = '';

			if (is_array($laUseSvc)){
				foreach($laUseSvc as $laSvc){
					if ($laRow['code'] == $laSvc['cd']){
						if ($today < $laSvc['from_dt']){
							$lbSvc = true;
							//$lsDt  = '<span style="font-weight:normal;">('.$myF->dateStyle($laSvc['from_dt'],'.').'~'.$myF->dateStyle($laSvc['to_dt'],'.').')</span>';
						}

						if ($today >= $laSvc['from_dt'] && $today <= $laSvc['to_dt'])
							$lbAdd = false;
						break;
					}
				}
			}

			if ($lbAdd){
				if ($laRow['code'] == '0')
					$lsGbn = '재가요양';
				else if ($laRow['code'] >= '1' && $laRow['code'] <= '4')
					$lsGbn = '바우처';
				else
					$lsGbn = '기타유료';

				if ($tmpGbn != $lsGbn){
					if (!empty($tmpUseSvc)){?>
						</td></tr><?
					}?>
					<tr>
						<th><?=$lsGbn;?></th>
						<td class="last"><?
					$tmpGbn = $lsGbn;
				}

				if ($lbSvc){
					$lsBold = 'bold';
					$lsChk  = '<span style="color:#ff0000; margin-right:2px;">√</span>';
				}else{
					$lsBold = '';
					$lsChk  = '';
				}

				$lsLink = '<a href="#" onclick="return lfSvcPop(\''.$laRow['code'].'\');">'.$conn->_svcNm($laRow['code']).'</a>';?>
				<div class="left <?=$lsBold;?>" style="float:left; width:auto; margin-right:7px;"><?=$lsChk.$lsLink.$lsDt;?></div><?
				$liAddCnt ++;
			}
		}
		if ($liAddCnt > 0){?>
			</td></tr><?
		}?>
	</tbody>
</table>