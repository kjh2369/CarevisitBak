<?
	$html  = '';

	$html .= '<table id=\'tblIns\' class=\'my_table my_border_blue\'>
				<colgroup>
					<col width=\'70px\'>
					<col width=\'120px\'>
					<col>
				</colgroup>
				<thead>
					<tr>
						<th class=\'head bold\' colspan=\'3\'>';

	if ($salaryYN == 'Y'){
		$html .= '<div style=\'float:right; width:auto; margin-right:5px;\'><img src=\'../image/close.gif\' style=\'cussor:pointer;\' onclick=\'__popupHide();\'></div>';
	}

	$html .= '				<div style=\'float:center; width:auto;\'>급여공통항목</div>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>4대보험가입</th>
						<td>
							<input id=\'insY\' name=\'insYN\' type=\'radio\' class=\'radio\' value=\'Y\' onclick=\'lf_setInsAll();\' '.($insYN['ins'] == 'Y' ? 'checked' : '').'><label for=\'insY\'>가입</label>
							<input id=\'insN\' name=\'insYN\' type=\'radio\' class=\'radio\' value=\'N\' onclick=\'lf_setInsAll();\' '.($insYN['ins'] != 'Y' ? 'checked' : '').'><label for=\'insN\'>미가입</label>
						</td>
						<th class=\'center\'>신고월급여액</th>
					</tr>
					<tr>
						<th>국민연금</th>
						<td>
							<input id=\'annuityY\' name=\'annuityYN\' type=\'radio\' class=\'radio\' value=\'Y\' onclick=\'lf_setIns(this);\' '.($insYN['annuity'] == 'Y' ? 'checked' : '').'><label for=\'annuityY\'>가입</label>
							<input id=\'annuityN\' name=\'annuityYN\' type=\'radio\' class=\'radio\' value=\'N\' onclick=\'lf_setIns(this);\' '.($insYN['annuity'] != 'Y' ? 'checked' : '').'><label for=\'annuityN\'>미가입</label>
						</td>
						<td>
							<input id=\'annuityPay\' name=\'annuityPay\' type=\'text\' value=\''.number_format($insYN['annuity_pay']).'\' maxlength=\'10\' class=\'number\' style=\'ime-mode:disabled;\' onkeydown=\'__onlyNumber(this);\' onfocus=\'__commaUnset(this);\' onblur=\'__commaSet(this);\'>
						</td>
					</tr>
					<tr>
						<th>건강보험</th>
						<td>
							<input id=\'healthY\' name=\'healthYN\' type=\'radio\' class=\'radio\' value=\'Y\' onclick=\'lf_setIns(this);\' '.($insYN['health'] == 'Y' ? 'checked' : '').'><label for=\'healthY\'>가입</label>
							<input id=\'healthN\' name=\'healthYN\' type=\'radio\' class=\'radio\' value=\'N\' onclick=\'lf_setIns(this);\' '.($insYN['health'] != 'Y' ? 'checked' : '').'><label for=\'healthN\'>미가입</label>
						</td>
						<td class=\'center top\' rowspan=\'4\'>
							<span class=\'btn_pack small\' style=\'margin:5px;\'><button type=\'button\' onclick=\'_salaryMemInsSave();\' style=\'width:100%;\'>적용</button></span>
						</td>
					</tr>
					<tr>
						<th>고용보험</th>
						<td>
							<input id=\'employY\' name=\'employYN\' type=\'radio\' class=\'radio\' value=\'Y\' onclick=\'lf_setIns(this);\' '.($insYN['employ'] == 'Y' ? 'checked' : '').'><label for=\'employY\'>가입</label>
							<input id=\'employN\' name=\'employYN\' type=\'radio\' class=\'radio\' value=\'N\' onclick=\'lf_setIns(this);\' '.($insYN['employ'] != 'Y' ? 'checked' : '').'><label for=\'employN\'>미가입</label>
						</td>
						<td></td>
					</tr>
					<tr>
						<th>산재보험</th>
						<td>
							<input id=\'sanjeY\' name=\'sanjeYN\' type=\'radio\' class=\'radio\' value=\'Y\' onclick=\'lf_setIns(this);\' '.($insYN['sanje'] == 'Y' ? 'checked' : '').'><label for=\'sanjeY\'>가입</label>
							<input id=\'sanjeN\' name=\'sanjeYN\' type=\'radio\' class=\'radio\' value=\'N\' onclick=\'lf_setIns(this);\' '.($insYN['sanje'] != 'Y' ? 'checked' : '').'><label for=\'sanjeN\'>미가입</label>
						</td>
						<td></td>
					</tr>
					<tr>
						<th>원천징수</th>
						<td>
							<input id=\'payeY\' name=\'payeYN\' type=\'radio\' class=\'radio\' value=\'Y\' onclick=\'lf_setPAYE();\' '.($insYN['paye'] == 'Y' ? 'checked' : '').'><label for=\'payeY\'>예</label>
							<input id=\'payeN\' name=\'payeYN\' type=\'radio\' class=\'radio\' value=\'N\' onclick=\'lf_setPAYE();\' '.($insYN['paye'] != 'Y' ? 'checked' : '').'><label for=\'payeN\'>아니오</label>
						</td>
					</tr>
				</tbody>
			</table>';

	$html .= '<script type=\'text/javascript\'>
				function lf_setPAYE(){
					if ($(\'#annuityY\').attr(\'checked\') ||
						$(\'#healthY\').attr(\'checked\') ||
						$(\'#employY\').attr(\'checked\') ||
						$(\'#sanjeY\').attr(\'checked\')){

						if (!$(\'#payeY\').attr(\'checked\')) return;

						alert(\'4대보험에 가입된 대상자는 원천징수 대상자가 될 수 없습니다.!!\');

						$(\'#payeN\').attr(\'checked\', \'checked\');
					}
				}

				function lf_setIns(obj){
					if ($(\'#payeY\').attr(\'checked\')){
						alert(\'원천징수 대상자는 4대보험에 가입할 수 없습니다.!!\');
						$(\':radio[name="\'+$(obj).attr(\'name\')+\'"]:input[value="N"]\').attr(\'checked\', \'checked\');
						lf_setAnnuityPay();
						return;
					}
					lf_setAnnuityPay();
				}

				function lf_setAnnuityPay(){
					var yn = $(\':radio[name="annuityYN"]:checked\').attr(\'value\');

					if (yn == \'Y\'){
						$(\'#annuityPay\').attr(\'disabled\', false);
					}else{
						$(\'#annuityPay\').attr(\'disabled\', true);
					}
				}

				function lf_setInsAll(){
					var yn = $(\':radio[name="insYN"]:checked\').attr(\'value\');

					if ($(\'#payeY\').attr(\'checked\') && yn == \'Y\'){
						alert(\'원천징수 대상자는 4대보험에 가입할 수 없습니다.!!\');
						$(\'#insN\').attr(\'checked\', \'checked\');
						return;
					}

					$(\'#annuity\'+yn).attr(\'checked\', \'checked\');
					$(\'#health\'+yn).attr(\'checked\', \'checked\');
					$(\'#employ\'+yn).attr(\'checked\', \'checked\');
					$(\'#sanje\'+yn).attr(\'checked\', \'checked\');
				}

				lf_setAnnuityPay();
			  </script>';

	echo $myF->_gabSplitHtml($html);
?>