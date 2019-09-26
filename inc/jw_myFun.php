<?
	#이용리스트
	#실적관리(수급자내역, 요양사내역)
	#청구관리(영수증및명세서)

	#$code : 기관코드
	#$find_name : 직원명 & 고객명
	#$find_type : 부서 & 서비스
	#$gubun : 직원(요양사), 고객(수급자) 구분


	function _find_person($conn, $code, $find_name, $find_type , $gubun, $onclick){

		if($gubun == 'member'){	#직원명, 부서 찾기
			$html = '<table class=\'my_table\' style=\'width:100%;\'>
					<colgroup>
						<col width=\'45px;\'>
						<col width=\'100px;\'>
						<col width=\'45px;\'>
						<col width=\'100px;\'>
					</colgroup>
					<tr>
						<th class=\'center\'>요양사</th>
						<td>
							<input id=\'find_name\' name=\'find_name\' type=\'text\' value=\''.$find_name.'\' style=\'width:100%;\' onFocus=\'this.select();\'>
						</td>
						<th class=\'center\'>부서</th>
						<td>
						<select name=\'find_type\' style=\'width:auto;\' onchange=\'\'>
						<option value=\'\' >전체</option>';

						$sql = "select dept_cd, dept_nm
								  from dept
								 where org_no   = '$code'
								   and del_flag = 'N'
								 order by order_seq";

						$conn->query($sql);
						$conn->fetch();

						$row_count = $conn->row_count();

						for($i=0; $i<$row_count; $i++){
							$row = $conn->select_row($i);

							$html .=  '<option value=\''.$row['dept_cd'].'\' '.($find_type == $row['dept_cd'] ? 'selected' : '').'>'.$row['dept_nm'].'</option>';
						}

						$conn->row_free();

						$html .= '<option value=\'-\' '.($find_type == '-' ? 'selected' : '').'>미등록</option>
							</select>
						</td>
						<td class="last" style="padding-left:5px;">
							<span class=\'btn_pack m icon\'><span class=\'refresh\'></span><button type=\'button\' onclick=\''.$onclick.'\'>조회</button></span>
						</td>
					</tr>';
					$html .= '</table>';
		}else if($gubun == 'client'){	#고객명, 서비스 찾기

			$html = '<table class=\'my_table\' style=\'width:100%;\'>
					<colgroup>
						<col width=\'45px;\'>
						<col width=\'100px;\'>
						<col width=\'45px;\'>
						<col width=\'100px;\'>
					</colgroup>
					<tr>
						<th class=\'center\'>수급자</th>
						<td>
							<input id=\'find_name\' name=\'find_name\' type=\'text\' value=\''.$find_name.'\' style=\'width:100%;\' onFocus=\'this.select();\'>
						</td>
						<th class="center">서비스</th>
						<td>';

							$kind_list = $conn->kind_list($code, $gHostSvc['voucher']);
					
							$html .= '<select id=\'find_type\' name=\'find_type\' style=\'width:auto;\'>';
							$html .= '<option value=\'\'>전체</option>';

							if($kind_list){ 
								foreach($kind_list as $i => $k){
									$html .= '<option value=\''.$k['code'].'\' '.($find_type == $k['code'] ? 'selected' : '').'>'.$k['name'].'</option>';
								}
							}

							$html .= '</select></td>';

				$html .='<td class="last" style="padding-left:5px;">
							<span class=\'btn_pack m icon\'><span class=\'refresh\'></span><button type=\'button\' onclick=\''.$onclick.'\'>조회</button></span>
						</td>
					</tr>';
		$html .= '</table>';

		}

		return $html;
	}
?>