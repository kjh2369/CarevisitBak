<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');


	/*********************************************************

		고객과 요양보호사의 가족관계를 정의한다.

	*********************************************************/


	$html  = '<div id=\'family_list\' style=\'display:none; margin:10px;\'>';


	$html .= '<table id=\'tblFamily\' class=\'my_table my_border_blue\' style=\'width:100%;\'>
				<colgroup>
					<col width=\'50px\'>
					<col width=\'150px\'>
					<col width=\'150px\'>
					<col>
				</colgroup>
				<tbody>
					<tr>
						<th class=\'head\'>No</th>
						<th class=\'head\'>요양보호사</th>
						<th class=\'head\'>관계</th>
						<th class=\'head\'>
							<div style=\'float:right; width:auto; padding-right:5px;\'><span class=\'btn_pack m\'><button type=\'button\' onclick=\'familyAddRow();\'>추가</button></span></div>
							<div style=\'float:center; width:auto;\'>비고</div>
						</th>
					</tr>
				</tbody>
			  </table>';

	$html .= '</div>';


	/*********************************************************

		가족요양보호사 조회

		*****************************************************/
		$sql = 'select cf_mem_cd as cd
				,      cf_mem_nm as nm
				,      cf_kind as kind
				  from client_family
				 where org_no   = \''.$code.'\'
				   and cf_jumin = \''.$jumin.'\'';

		$arrClientFamily = $conn->_fetch_array($sql, 'cd');
	/********************************************************/



	$html .= '<script type=\'text/javascript\'>
				function familyAddRow(memNM, memCD, memGbn){
					$rows = $("#tblFamily tr");
					$idx  = $rows.length;
					$id   = "tblFamilyRow_"+$idx;
					$code = $("#code").attr("value");

					if (!memNM) memNM = "";
					if (!memCD) memCD = "";
					if (!memGbn) memGbn = "";

					$("#tblFamily").append(
						 "<tr id=\'"+$id+"\'>"
							+"<td class=\'center\'>"+$idx+"</td>"
							+"<td class=\'left\'>"
								+"<div style=\'float:left; width:auto; height:100%;\'>"
									+"<span id=\'strFamilyNM_"+$idx+"\' style=\'font-weight:bold;\'>"+memNM+"</span>"
									+"<input id=\'objFamilyCD_"+$idx+"\' name=\'objFamilyCD[]\' type=\'hidden\' value=\'jumin="+memCD+"&name="+memNM+"\'>"
								+"</div>"
								+"<div style=\'float:right; width:auto; height:100%; padding-top:1px;\'><span class=\'btn_pack m find\' onclick=\'familySetMem(__find_member_if(\""+$code+"\"),\""+$idx+"\");\'></span></div>"
							+"</td>"
							+"<td class=\'center\'>"
								+"<select id=\'objFamilyGbn_"+$idx+"\' name=\'objFamilyGbn[]\' style=\'width:140px;\'>"
									+"<option value=\'\' selected></option>"
									+"<option value=\'S031\'"+(memGbn == "S031" ? " selected " : " ")+">처</option>"
									+"<option value=\'S032\'"+(memGbn == "S032" ? " selected " : " ")+">남편</option>"
									+"<option value=\'S033\'"+(memGbn == "S033" ? " selected " : " ")+">자</option>"
									+"<option value=\'S034\'"+(memGbn == "S034" ? " selected " : " ")+">자부</option>"
									+"<option value=\'S035\'"+(memGbn == "S035" ? " selected " : " ")+">사위</option>"
									+"<option value=\'S036\'"+(memGbn == "S036" ? " selected " : " ")+">형제자매</option>"
									+"<option value=\'S037\'"+(memGbn == "S037" ? " selected " : " ")+">손</option>"
									+"<option value=\'S038\'"+(memGbn == "S038" ? " selected " : " ")+">배우자의형제자매</option>"
									+"<option value=\'S039\'"+(memGbn == "S039" ? " selected " : " ")+">외손</option>"
									+"<option value=\'S040\'"+(memGbn == "S040" ? " selected " : " ")+">부모</option>"
									+"<option value=\'S041\'"+(memGbn == "S041" ? " selected " : " ")+">기타</option>"
								+"</select>"
							+"</td>"
							+"<td class=\'left\'><span class=\'btn_pack m\'><button type=\'button\' onclick=\'familyRemoveRow(\""+$id+"\");\'>삭제</button></span></td>"
						+"</tr>"
					);
				}

				function familyRemoveRow(id){
					$row = $("#"+id);
					$row.remove();
				}

				function familySetMem(memInfo, idx){
					$("#strFamilyNM_"+idx).text(memInfo["name"]);
					$("#objFamilyCD_"+idx).attr("value", "jumin="+memInfo["jumin"]+"&name="+memInfo["name"]);
				}';





				/*********************************************************
					가족요양보호사 리스트
					*****************************************************/
					if (is_array($arrClientFamily)){
						foreach($arrClientFamily as $memCD => $tmpArr){
							$html .= 'familyAddRow(\''.$tmpArr['nm'].'\',\''.$ed->en($tmpArr['cd']).'\',\''.$tmpArr['kind'].'\');';
						}
					}
				/********************************************************/

	$html .= '  </script>';

	echo $myF->_gabSplitHtml($html);
	//echo $html;
?>