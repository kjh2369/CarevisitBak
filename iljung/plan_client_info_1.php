<?
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');

	$sql = 'select min(m03_mkind) as kind
			,      IFNULL(jumin, m03_jumin) as jumin
			,      m03_name as name
			,      m03_bipay1 as bipay1
			  from m03sugupja
			  left join mst_jumin
					 on org_no = m03_ccode
					 and code = m03_jumin
					 and gbn = \'1\'
			 where m03_ccode  = \''.$code.'\'
			   and m03_jumin  = \''.$jumin.'\'
			   and m03_mkind  = \''.$svcCd.'\'
			   and m03_del_yn = \'N\'';

	$row   = $conn->get_array($sql);
	$lsCNm = $row['name'];
	$lsCCd = SubStr($row['jumin'].'0000000000000',0,13);
	$svcNm = $conn->_svcNm($svcCd);

	$liBipay1 = $row['bipay1'];

	unset($row);

	$sql = 'select voucher_gbn as val
			,      voucher_gbn2 as tm
			,      voucher_lvl as lvl
			,	   voucher_overtime+voucher_maketime as limit_cnt
			/*,      voucher_totaltime as limit_cnt*/
			,      voucher_totalpay as limit_amt
			,      voucher_suga_cd as suga_cd
			,      voucher_suga_cost as suga_cost
			  from voucher_make
			 where org_no        = \''.$code.'\'
			   and voucher_kind  = \''.$svcCd.'\'
			   and voucher_jumin = \''.$jumin.'\'
			   and voucher_yymm  = \''.$year.$month.'\'
			   and del_flag      = \'N\'';
	$row = $conn->get_array($sql);

	if ($row){
		$lsSvcVal = $row['val'];
		$lsSvcLvl = $row['lvl'];
	}else{
		if ($svcCd == '1'){
			$sql = 'select svc_val
					  from client_his_nurse
					 where org_no = \''.$code.'\'
					   and jumin  = \''.$jumin.'\'
					   and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
					   and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
					 order by seq desc
					 limit 1';

			$lsSvcVal = $conn->get_data($sql);
		}else if ($svcCd == '2'){
			$sql = 'select svc_val
					  from client_his_old
					 where org_no = \''.$code.'\'
					   and jumin  = \''.$jumin.'\'
					   and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
					   and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
					 order by seq desc
					 limit 1';

			$lsSvcVal = $conn->get_data($sql);

			if ($lsSvcVal == '1'){
				$lsSvcVal = 'V';
			}else if ($lsSvcVal == '2'){
				$lsSvcVal = 'D';
			}
		}else if ($svcCd == '3'){
			$sql = 'select svc_val
					  from client_his_baby
					 where org_no = \''.$code.'\'
					   and jumin  = \''.$jumin.'\'
					   and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
					   and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
					 order by seq desc
					 limit 1';

			$lsSvcVal = $conn->get_data($sql);
		}else if ($svcCd == '4'){
			$sql = 'select svc_val
					,      svc_lvl
					  from client_his_dis
					 where org_no = \''.$code.'\'
					   and jumin  = \''.$jumin.'\'
					   and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
					   and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
					 order by seq desc
					 limit 1';

			$laSvcIf  = $conn->get_array($sql);
			$lsSvcVal = $laSvcIf['svc_val'];
			$lsSvcLvl = $laSvcIf['svc_lvl'];

			if ($lsSvcVal == '1'){
				$lsSvcVal = 'A';
			}else if ($lsSvcVal == '2'){
				$lsSvcVal = 'C';
			}
		}
	}

	$lblLimitCnt = $row['limit_cnt']; //서비스 제한 시간 및 일수
	$liSvcCost = $row['suga_cost']; //서비스단가

	unset($row);?>
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="60px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="center">고객명</th>
				<td class="center last"><div id="lblCNm" class="left"><a href="#" onclick="retrun false;"><span class="bold"><?=$lsCNm;?></span></a></div></td>
			</tr>
			<tr>
				<th class="center <?=($type == 'PLAN' ? 'bottom' : '');?>">생년월일</th>
				<td class="center <?=($type == 'PLAN' ? 'bottom' : '');?> last"><div class="left"><?=$myF->issToBirthday($lsCCd,'.');?></div></td>
			</tr>
		</tbody>
	</table>
	<div id="infoClient" style="display:none;"
		limitAmt="<?=$lblLimitCnt;?>"
		claimAmt="<?=$lblLimitCnt;?>"
		svcCost="<?=$liSvcCost;?>"
		svcVal="<?=$lsSvcVal;?>"
		svcLvl="<?=$lsSvcLvl;?>"
		bipay="<?=$liBipay1;?>">Y</div>
	<script type="text/javascript">
	$(document).ready(function(){
		_planSetLimitAmt();
	});
	</script>