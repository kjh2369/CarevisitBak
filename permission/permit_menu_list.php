<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$sql = 'SELECT	id,name,seq,url,permit,debug,use_yn
			FROM	menu_top
			WHERE	use_yn	= \'Y\' '.(!$debug ? ' AND debug = \'N\'' : '').'
			ORDER	BY seq,id';

	$menuTop = $conn->_fetch_array($sql);

	$first = true;
	$border = 'border-top:1px solid #d9d9d9;';

	/*
	($row['permit'] == 'S' && $gHostSvc['careSupport']) ||
	($row['permit'] == 'R' && $gHostSvc['careResource']) ||
	($row['permit'] == 'C' && $gHostSvc['homecare']) ||
	($row['permit'] == 'V' && $gHostSvc['voucher']) ||
	($row['permit'] == $insuCd) ||
	($row['permit'] == 'D' && $gDayAndNight) ||
	($row['permit'] == 'W' && $gWMD) ||
	($row['permit'] == 'A')
	 */

	if (Is_Array($menuTop)){
		foreach($menuTop as $top){
			if (($top['permit'] == 'S' && $gHostSvc['careSupport']) ||
				($top['permit'] == 'R' && $gHostSvc['careResource']) ||
				($top['permit'] == 'C' && $gHostSvc['homecare']) ||
				($top['permit'] == 'V' && $gHostSvc['voucher']) ||
				($top['permit'] == $insuCd) ||
				($top['permit'] == 'D' && $gDayAndNight) ||
				($top['permit'] == 'W' && $gWMD) ||
				($top['permit'] == 'A') ||
				($top['permit'] == 'B')
				){?>
				<tr>
					<th class="bold bottom last" style="<?=(!$first ? $border : '');?>" colspan="2"><label><input id="chk<?=$top['id'];?>" name="chk" type="checkbox" class="checkbox" value=""><?=$top['name'];?></label></th>
				</tr><?

				$first = false;

				$sql = 'SELECT	id,name,seq,url,permit,debug,use_yn
						FROM	menu_left
						WHERE	m_top	= \''.$top['id'].'\'
						AND		use_yn	= \'Y\' '.(!$debug ? ' AND debug = \'N\'' : '').'
						ORDER	BY seq,id';

				$menuLeft = $conn->_fetch_array($sql);

				if (Is_Array($menuLeft)){
					foreach($menuLeft as $left){
						if ($topId != $top['name']){
							$topId  = $top['name'];
							$topBd	= $border;
						}else{
							$topBd	= '';
						}?>
						<tr>
							<td class="bottom" style="<?=$topBd;?>"></td>
							<th class="bottom last" style="border-top:1px solid #d9d9d9;"><label><input id="chk<?=$top['id'].$left['id'];?>" name="chk" type="checkbox" class="checkbox" value=""><?=$left['name'];?></label></th>
						</tr>
						<tr>
							<td class="bottom" style=""></td>
							<td class="bottom last" style="border-top:1px solid #d9d9d9; padding-left:25px;"><?
								$sql = 'SELECT	id,name,seq,url,permit,debug,use_yn
										FROM	menu_list
										WHERE	m_top	= \''.$top['id'].'\'
										AND		m_left	= \''.$left['id'].'\'
										AND		use_yn	= \'Y\' '.(!$debug ? ' AND debug = \'N\'' : '').'
										ORDER	BY seq,id';

								$conn->query($sql);
								$conn->fetch();

								$rowCnt = $conn->row_count();

								for($i=0; $i<$rowCnt; $i++){
									$row = $conn->select_row($i);

									if (($row['permit'] == 'S' && $gHostSvc['careSupport']) ||
										($row['permit'] == 'R' && $gHostSvc['careResource']) ||
										($row['permit'] == 'C' && $gHostSvc['homecare']) ||
										($row['permit'] == 'V' && $gHostSvc['voucher']) ||
										($row['permit'] == $insuCd) ||
										($row['permit'] == 'D' && $gDayAndNight) ||
										($row['permit'] == 'W' && $gWMD) ||
										($row['permit'] == 'A') ||
										($row['permit'] == 'B') ){?>
										<div style="float:left; width:30%;"><label><input id="chk<?=$top['id'].$left['id'].$row['id'];?>" name="chk" type="checkbox" class="checkbox clsMenu" value=""><?=$row['name'];?></label></div><?
									}
								}

								$conn->row_free();?>
							</td>
						</tr><?
					}
				}
			}
		}
	}

	include_once('../inc/_db_close.php');
?>