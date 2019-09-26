<?
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');

	//본인부담율
	$sql = 'select seq
			,      kind
			,      rate
			,      date_format(from_dt, \'%Y%m%d\') as from_dt
			,      date_format(to_dt,   \'%Y%m%d\') as to_dt
			  from client_his_kind
			 where org_no = \''.$code.'\'
			   and jumin  = \''.$jumin.'\'
			   and date_format(from_dt, \'%Y%m\') <= \''.$year.$month.'\'
			   and date_format(to_dt,   \'%Y%m\') >= \''.$year.$month.'\'
			 order by jumin, seq';
	$laClientKind = $conn->_fetch_array($sql);

	//등급
	$sql = 'SELECT level
			,      date_format(from_dt, \'%Y%m%d\') as from_dt
			,      date_format(to_dt,   \'%Y%m%d\') as to_dt
			  FROM client_his_lvl
			 WHERE org_no = \''.$code.'\'
			   AND jumin  = \''.$jumin.'\'
			   AND svc_cd = \'0\'
			   AND DATE_FORMAT(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
			   AND DATE_FORMAT(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'
			 ORDER BY jumin, seq';
	$laClientLvel = $conn->_fetch_array($sql);

	$sql = 'select m03_ccode as code
			,      m03_jumin as jumin
			,      m03_name as name
			,      m03_tel as phone
			,      m03_hp as mobile
			,      m03_bath_add_yn as bath_yn
			,      m03_bipay1 as bipay1
			,      m03_bipay2 as bipay2
			,      m03_bipay3 as bipay3
			,      lvl.app_no
			,      ifnull(lvl.level, \'9\') as lvl
			,      ifnull(amt.amt, 0) as limit_amt
			,      case when ifnull(clm.amt, 0) > 0 then clm.amt else ifnull(amt.amt, 0) end as claim_amt
			,      CASE WHEN IFNULL(clm.amt_care, 0) > 0 THEN clm.amt_care ELSE 0 END AS claim_care
			,      CASE WHEN IFNULL(clm.amt_bath, 0) > 0 THEN clm.amt_bath ELSE 0 END AS claim_bath
			,      CASE WHEN IFNULL(clm.amt_nurse, 0) > 0 THEN clm.amt_nurse ELSE 0 END AS claim_nurse
			,      ifnull(kind.kind, \'1\') as kind
			,      ifnull(kind.rate, case when lvl.level is null then 100 else 15 end) as rate
			,      svc.from_dt as from_dt
			,      svc.to_dt as to_dt
			  from m03sugupja as mst
			 inner join (
				   select jumin
				   ,      date_format(min(from_dt), \'%Y%m%d\') as from_dt
				   ,      date_format(max(to_dt),   \'%Y%m%d\') as to_dt
				     from client_his_svc
				    where org_no = \''.$code.'\'
					  and svc_cd = \''.$svcCd.'\'
				      and date_format(from_dt, \'%Y%m\') <= \''.$year.$month.'\'
				      and date_format(to_dt,   \'%Y%m\') >= \''.$year.$month.'\'
				    group by jumin
				 ) as svc
			  on svc.jumin = mst.m03_jumin
		    left join (
				 select IFNULL(jumin,\'\') AS jumin
				 ,      app_no
				 ,      MIN(level) AS level
				   from client_his_lvl
				  where org_no = \''.$code.'\'
				    and svc_cd = \''.$svcCd.'\'
					and jumin  = \''.$jumin.'\'
					and date_format(from_dt, \'%Y%m\') <= \''.$year.$month.'\'
					and date_format(to_dt,   \'%Y%m\') >= \''.$year.$month.'\'
				  order by jumin, seq desc
				 ) as lvl
			  on lvl.jumin = mst.m03_jumin
			left join (
				 select m91_code as cd
				 ,      m91_kupyeo as amt
				   from m91maxkupyeo
				  where left(m91_sdate, 6) <= \''.$year.$month.'\'
					and left(m91_edate, 6) >= \''.$year.$month.'\'
				 ) as amt
			  on amt.cd = lvl.level
			left join (
				 select jumin
				 ,      seq
				 ,      kind
				 ,      rate
				   from client_his_kind
				  where org_no = \''.$code.'\'
				    and jumin  = \''.$jumin.'\'
					and date_format(from_dt, \'%Y%m\') <= \''.$year.$month.'\'
					and date_format(to_dt,   \'%Y%m\') >= \''.$year.$month.'\'
				  order by jumin, seq
				 ) as kind
			  on kind.jumin = mst.m03_jumin
			left join (
				 select jumin
				 ,      amt_care
				 ,      amt_bath
				 ,      amt_nurse
				 ,      amt
				   from client_his_limit
				  where org_no = \''.$code.'\'
				    and jumin  = \''.$jumin.'\'
					and date_format(from_dt, \'%Y%m\') <= \''.$year.$month.'\'
					and date_format(to_dt,   \'%Y%m\') >= \''.$year.$month.'\'
				  order by jumin, seq desc
				 ) as clm
			  on clm.jumin = mst.m03_jumin
			where m03_ccode = \''.$code.'\'
			  and m03_jumin = \''.$jumin.'\'
			group by m03_jumin';

	$laClientHis = $conn->get_array($sql);

	$laClientHis['claim_amt'] = ($laClientHis['claim_amt'] > 0 ? $laClientHis['claim_amt'] : $laClientHis['limit_amt']);

	//청구한도가 급여한도보다 많을 경우
	/*
		if ($laClientHis['claim_amt'] > $laClientHis['limit_amt']){
			$laClientHis['claim_amt'] = $laClientHis['limit_amt'];
		}
	 */
	if ($para['DayAndNight'] == 'Y'){
	}else{
		if ($laClientHis['claim_amt'] > $laClientHis['limit_amt']){
			$laClientHis['claim_amt'] = $laClientHis['limit_amt'];
		}
	}

	//청구한도 설정
	if ($para['DayAndNight'] == 'Y'){
		$sql = 'SELECT	yn
				FROM	dan_extra_charge
				WHERE	org_no	= \''.$code.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		yymm	= \''.$year.$month.'\'';

		$danExtraChargeYn = $conn->get_data($sql);
		if (!$danExtraChargeYn) $danExtraChargeYn = 'N';

		if ($danExtraChargeYn == 'Y'){
			$laClientHis['claim_amt'] = Floor($laClientHis['limit_amt'] * 1.5);
			$laClientHis['claim_amt'] = Round($laClientHis['claim_amt'] / 10) * 10;
		}
	}

	//한도오류 판단
	if ($laClientHis['claim_amt'] <= 0){
		//수급자등급 기간
		$sql = 'select count(*)
				  from client_his_lvl
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and svc_cd = \''.$svcCd.'\'
				   and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
				   and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'';
		$liLvlCnt = $conn->get_data($sql);

		//수급자구분 기간
		$sql = 'select count(*)
				  from client_his_kind
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and date_format(from_dt,\'%Y%m\') <= \''.$year.$month.'\'
				   and date_format(to_dt,  \'%Y%m\') >= \''.$year.$month.'\'';
		$liKindCnt = $conn->get_data($sql);
	}

	// 주간 목욕횟수 제한
	$liBathWeekCnt = 7;

	if ($year.$month >= '201107'){
		if ($laClientHis['bath_yn'] != 'Y'){
			$liBathWeekCnt = 1;
		}
	}?>
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="60px">
			<col width="80px">
			<col width="40px">
			<col>
		</colgroup>
		<tbody>
			<tr>
				<th class="center">고객명</th>
				<td class="center"><div id="lblCNm" class="left"><a href="#" onclick="return false;"><span class="bold"><?=$laClientHis['name'];?></span></a></div></td>
				<th class="center">등급</th>
				<td class="center last">
					<div style="float:right; width:auto;"><img id="btnClientLvlShow" src="./img/arrow2.gif" onclick="$('#tblLvlInfo').show(300);" onmouseover="this.src='../iljung/img/arrow1.gif';" onmouseout="this.src='../iljung/img/arrow2.gif';" style="margin-top:8px; margin-right:5px;"></div>
					<div class="left nowrap" style="float:left; width:auto; cursor:default;" onclick="$('#btnClientLvlShow').click();" onmouseover="$('#btnClientLvlShow').attr('src', '../iljung/img/arrow1.gif');" onmouseout="$('#btnClientLvlShow').attr('src', '../iljung/img/arrow2.gif');"><?=$myF->_lvlNm($laClientHis['lvl']);?></div>
				</td>
			</tr>
			<tr>
				<th class="center <?=($type == 'PLAN' ? 'bottom' : '');?>">인정번호</th>
				<td class="center <?=($type == 'PLAN' ? 'bottom' : '');?>"><div class="left" id="clientAppNo"><?=$laClientHis['app_no'];?></div></td>
				<th class="center <?=($type == 'PLAN' ? 'bottom' : '');?>">구분</th>
				<td class="center <?=($type == 'PLAN' ? 'bottom' : '');?> last">
					<div style="float:right; width:auto;"><img id="btnClientKindShow" src="./img/arrow2.gif" onclick="$('#tblKindInfo').show(300);" onmouseover="this.src='../iljung/img/arrow1.gif';" onmouseout="this.src='../iljung/img/arrow2.gif';" style="margin-top:8px; margin-right:5px;"></div>
					<div class="left nowrap" style="float:left; width:auto; cursor:default;" onclick="$('#btnClientKindShow').click();" onmouseover="$('#btnClientKindShow').attr('src', '../iljung/img/arrow1.gif');" onmouseout="$('#btnClientKindShow').attr('src', '../iljung/img/arrow2.gif');"><?=$myF->mid($myF->_kindNm($laClientHis['kind']),0,2);?></div>
				</td>
			</tr>
		</tbody>
	</table>
	<div id="infoClient" style="display:none;"
		limitAmt		="<?=$laClientHis['limit_amt'];?>"
		claimAmt		="<?=$laClientHis['claim_amt'];?>"
		claimCare		="<?=$laClientHis['claim_care'];?>"
		claimBath		="<?=$laClientHis['claim_bath'];?>"
		claimNurse		="<?=$laClientHis['claim_nurse'];?>"
		rate			="<?=$laClientHis['rate'];?>"
		bathWeekCnt		="<?=$liBathWeekCnt;?>"
		familyCareCnt	="<?=$liFamilyCareCnt;?>"
		lvlCnt			="<?=$liLvlCnt;?>"
		svcLvl			="<?=$laClientHis['lvl'];?>"
		kindCnt			="<?=$liKindCnt;?>"
		bipay200		="<?=$laClientHis['bipay1'];?>"
		bipay500		="<?=$laClientHis['bipay2'];?>"
		bipay800		="<?=$laClientHis['bipay3'];?>"<?
		for($i=1; $i<=31; $i++){
			if (is_array($laClientKind)){
				foreach($laClientKind as $laRow){
					if ($laRow['from_dt'] <= $year.$month.($i<10?'0':'').$i &&
						$laRow['to_dt']   >= $year.$month.($i<10?'0':'').$i){?>
						rate<?=$i;?>="<?=$laRow['rate'];?>" <?
						break;
					}
				}
			}else{?>
				rate<?=$i;?>="100" <?
			}
		}?>></div>
	<div id="tblKindInfo" class="my_border_blue" style="position:absolute; width:auto; top:90px; left:255px; background-color:#ffffff; display:none;">
		<table class="my_table" style="width:350px;">
			<colgroup>
				<col width="100px">
				<col width="100px">
				<col>
			</colgroup>
			<thead>
				<tr>
					<th class="head">구분</th>
					<th class="head">본인부담율</th>
					<th class="head last">
						<div style="float:right; width:auto;"><img src="../image/btn_close.gif" style="margin-right:3px;" onclick="$('#tblKindInfo').hide(300);"></div>
						<div style="float:center; width:auto;">기간</div>
					</th>
				</tr>
			</thead>
			<tbody><?
				if (is_array($laClientKind)){
					$lsLastDt = $myF->dateAdd('month', 1, $year.'-'.$month.'-01', 'Y-m-d');
					$lsLastDt = $myF->dateAdd('day', -1, $lsLastDt, 'Ymd');

					foreach($laClientKind as $laRow){
						if ($year.$month.'01' > $laRow['from_dt']){
							$lsFromDt = $year.'.'.$month.'.01';
						}else{
							$lsFromDt = $myF->dateStyle($laRow['from_dt'],'.');
						}

						if ($lsLastDt < $laRow['to_dt']){
							$lsToDt = $myF->dateStyle($lsLastDt,'.');
						}else{
							$lsToDt = $myF->dateStyle($laRow['to_dt'],'.');
						}?>
						<tr class="clsGbnList" gbn="<?=$laRow['kind'];?>" from="<?=Str_Replace('.','',$lsFromDt);?>" to="<?=Str_Replace('.','',$lsToDt);?>">
							<td class="left"><?=$myF->_kindNm($laRow['kind']);?></td>
							<td class="center"><?=$laRow['rate'];?></td>
							<td class="center last"><?=$lsFromDt.'~'.$lsToDt;?></td>
						</tr><?
					}
				}else{?>
					<tr>
						<td class="bottom center last" colspan="3">::검색된 데이타가 없습니다.::</td>
					</tr><?
				}?>
			</tbody>
		</table>
	</div>
	<div id="tblLvlInfo" rowCnt="<?=SizeOf($laClientLvel);?>" class="my_border_blue" style="position:absolute; width:auto; top:65px; left:255px; background-color:#ffffff; display:none;">
		<table class="my_table" style="width:190px;">
			<colgroup>
				<col width="50px">
				<col>
			</colgroup>
			<thead>
				<tr>
					<th class="head">등급</th>
					<th class="head last">
						<div style="float:right; width:auto;"><img src="../image/btn_close.gif" style="margin-right:3px;" onclick="$('#tblLvlInfo').hide(300);"></div>
						<div style="float:center; width:auto;">기간</div>
					</th>
				</tr>
			</thead>
			<tbody><?
				if (Is_Array($laClientLvel)){
					$lsLastDt = $myF->dateAdd('month', 1, $year.'-'.$month.'-01', 'Y-m-d');
					$lsLastDt = $myF->dateAdd('day', -1, $lsLastDt, 'Ymd');

					foreach($laClientLvel as $laRow){
						if ($year.$month.'01' > $laRow['from_dt']){
							$lsFromDt = $year.'.'.$month.'.01';
						}else{
							$lsFromDt = $myF->dateStyle($laRow['from_dt'],'.');
						}

						if ($lsLastDt < $laRow['to_dt']){
							$lsToDt = $myF->dateStyle($lsLastDt,'.');
						}else{
							$lsToDt = $myF->dateStyle($laRow['to_dt'],'.');
						}?>
						<tr class="clsLvlList" lvl="<?=$laRow['level'];?>" from="<?=Str_Replace('.','',$lsFromDt);?>" to="<?=Str_Replace('.','',$lsToDt);?>">
							<td class="center"><?=$myF->_lvlNm($laRow['level']);?></td>
							<td class="center last"><?=$lsFromDt.'~'.$lsToDt;?></td>
						</tr><?
					}
				}else{?>
					<tr>
						<td class="bottom center last" colspan="2">::검색된 데이타가 없습니다.::</td>
					</tr><?
				}?>
			</tbody>
		</table>
	</div>
	<script type="text/javascript">
	$(document).ready(function(){
		$('td',$('table tr:last', $('#tblKindInfo'))).css('border-bottom','none');
		_planSetLimitAmt();
	});
	</script><?
	unset($laClientHis);
	unset($laClientKind);
?>