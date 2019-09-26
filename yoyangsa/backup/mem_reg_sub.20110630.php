<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="400px">
		<col width="430px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<td class="left top bottom" style="padding:0;">
				<table class="my_table" style="width:100%; border-bottom:0px;">
					<colgroup>
						<col width="50px">
						<col width="130px">
						<col>
					</colgroup>
					<tbody>
						<!--
						<div id="pictureView" style="width:90px; height:120px;"><img src="../image/no_img_bg.gif">
						<span style="width:100px; height:18px; background:url(../image/btn_find_file.gif) no-repeat left 50%; margin-bottom:5px;">
							<input type="file" name="file" id="file" style="padding-left:5px; width:0px; height:18px; filter:alpha(opacity=0); cursor:hand;" onchange="__showLocalImage(this,'pictureView');">
						</span>
						-->
						<tr>
							<th colspan="2">주민번호</th>
							<td class="last">
							<?
								if ($mode == 1){?>
									<input name="yJumin1" type="text" value="" maxlength="6" class="phone" style="width:50px;" readonly> -
									<input name="yJumin2" type="text" value="" maxlength="7" class="phone" style="width:55px; margin-right:0;" readonly>
									<input name="jumin" type="hidden" value="">
									[<a href="#" onclick="find_counsel();">찾기</a>]<?
								}else if ($mode == 2){?>
									<div class="left"><?=$myF->issStyle($jumin);?></div>
									<input name="jumin" type="hidden" value="<?=$ed->en($jumin);?>"><?
								}else{?>
									<div class="left"><?=$myF->issStyle($jumin);?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th colspan="2">성명</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="yName" type="text" value="<?=$mst[$basic_kind]["m02_yname"];?>" maxlength="10" onKeyDown="__enterFocus();" onFocus="this.select();" tag="성명을 입력하여 주십시오."><?
								}else{?>
									<div class="left"><?=$mst[$basic_kind]["m02_yname"];?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th class="center" rowspan="3">소속</th>
							<th>부서</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<select name="dept" style="width:auto;">
										<option value="">부서를 선택하여 주십시오.</option><?
										$sql = "select dept_cd, dept_nm
												  from dept
												 where org_no   = '$code'
												   and del_flag = 'N'
												 order by order_seq";

										$conn->query($sql);
										$conn->fetch();

										$row_count = $conn->row_count();

										for($i=0; $i<$row_count; $i++){
											$row = $conn->select_row($i);?>
											<option value="<?=$row['dept_cd'];?>" <? if($mst[$basic_kind]['m02_dept_cd'] == $row['dept_cd']){?>selected<?} ?>><?=$row['dept_nm'];?></option><?
										}

										$conn->row_free();
									?>
									</select><?
								}else{
									$sql = "select dept_nm
											  from dept
											 where org_no   = '$code'
											   and dept_cd  = '".$mst[$basic_kind]['m02_dept_cd']."'
											   and del_flag = 'N'";

									$dept_nm = $conn->get_data($sql);?>
									<div class="left"><?=$dept_nm;?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th>직종</th>
							<td class="last">
							<?
								$sql = "select distinct mst_cd, mst_nm
										  from job_kind";

								$conn->query($sql);
								$conn->fetch();

								$row_count = $conn->row_count();

								for($i=0; $i<$row_count; $i++){
									$row = $conn->select_row($i);

								}

								$conn->row_free();
							?>
							</td>
						</tr>
						<tr>
							<th>호봉</th>
							<td class="last">
								<input name=
							</td>
						</tr>
						<tr>
							<th class="center" rowspan="3">연락처</th>
							<th>
								<div class="help_left">핸드폰</div>
								<div class="help" onmouseover="_show_help(this, '이 핸드폰으로 모바일용 일정관리 업무를 사용할 수 있습니다.(스마트폰일 경우에만 사용가능)');" onmouseout="_hidden_help();"></div>
							</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="yTel" type="text" value="<?=getPhoneStyle($mst[$basic_kind]["m02_ytel"]);?>" maxlength="11" class="phone" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getPhoneNo(this);" onChange="checkHPno();" tag="핸드폰번호를 입력하여 주십시오."><?
								}else{?>
									<div class="left"><?=getPhoneStyle($mst[$basic_kind]["m02_ytel"]);?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th>유선</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="yTel2" type="text" value="<?=$mst[$basic_kind]["m02_ytel2"];?>" maxlength="11" class="phone" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getPhoneNo(this);"><?
								}else{?>
									<div class="left"><?=$mst[$basic_kind]["m02_ytel2"];?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th>e-mail</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="email" type="text" value="<?=$mst[$basic_kind]["m02_email"];?>" style="width:100%;"><?
								}else{?>
									<div class="left"><?=$mst[$basic_kind]["m02_email"];?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th class="center" rowspan="3">소재</th>
							<th>우편번호</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="yPostNo1" type="text" value="<?=substr($mst[$basic_kind]["m02_ypostno"],0,3);?>" maxlength="3" class="phone" style="width:30px;" onKeyDown="__onlyNumber(this)" onFocus="this.select();"> -
									<input name="yPostNo2" type="text" value="<?=substr($mst[$basic_kind]["m02_ypostno"],3,6);?>" maxlength="3" class="phone" style="width:30px;" onKeyDown="__onlyNumber(this)" onFocus="this.select();">
									<span class="btn_pack small"><button type="button" onClick="__helpAddress(document.f.yPostNo1, document.f.yPostNo2, document.f.yJuso1, document.f.yJuso2);">찾기</button></span><?
								}else{?>
									<div class="left"><?=substr($mst[$basic_kind]["m02_ypostno"],0,3);?>-<?=substr($mst[$basic_kind]["m02_ypostno"],3,6);?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th rowspan="2">주소</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="yJuso1" type="text" value="<?=$mst[$basic_kind]["m02_yjuso1"];?>" maxlength="20" style="width:100%;" onKeyDown="__enterFocus();" onFocus="this.select();"><?
								}else{?>
									<div class="left"><?=$mst[$basic_kind]["m02_yjuso1"];?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="yJuso2" type="text" value="<?=$mst[$basic_kind]["m02_yjuso2"];?>" maxlength="20" style="width:100%;" onKeyDown="__enterFocus();" onFocus="this.select();"><?
								}else{?>
									<div class="left"><?=$mst[$basic_kind]["m02_yjuso2"];?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th class="center" rowspan="3">자격증</th>
							<th>자격종류</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<select name="yJakukKind" style="width:auto;" onKeyDown="__enterFocus();">
									<?
										$sql = "select m99_code, m99_name
												  from m99license
												 order by m99_seq";
										$conn->query($sql);
										$row2 = $conn->fetch();
										$row_count = $conn->row_count();

										for($i=0; $i<$row_count; $i++){
											$row2 = $conn->select_row($i);
										?>
											<option value="<?=$row2[0];?>"<? if($mst[$basic_kind]["m02_yjakuk_kind"] == $row2[0]){echo "selected";}?>><?=$row2[1];?></option>
										<?
										}

										$conn->row_free();
									?>
									</select><?
								}else{
									$sql = "select m99_name
											  from m99license
											 where m99_code = '".$mst[$basic_kind]["m02_yjakuk_kind"]."'";
									$license = $conn->get_data($sql);?>
									<div class="left"><?=$license;?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th>자격증번호</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="yJagyukNo" type="text" value="<?=$mst[$basic_kind]["m02_yjagyuk_no"];?>" onFocus="this.select();"><?
								}else{?>
									<div class="left"><?=$mst[$basic_kind]["m02_yjagyuk_no"];?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th>발급일자</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="yJakukDate" type="text" value="<?=$myF->dateStyle($mst[$basic_kind]["m02_yjakuk_date"]);?>" maxlength="11" class="date" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);"><?
								}else{?>
									<div class="left"><?=$myF->dateStyle($mst[$basic_kind]["m02_yjakuk_date"]);?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th colspan="2">직책</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<select name="yJikJong" style="width:auto;" onKeyDown="__enterFocus();">
									<?
										$sql = $conn->get_query("98");
										$conn->query($sql);
										$row2 = $conn->fetch();
										$row_count = $conn->row_count();

										for($i=0; $i<$row_count; $i++){
											$row2 = $conn->select_row($i);
										?>
											<option value="<?=$row2[0];?>" tag="<?=$row2[2];?>" <? if($mst[$basic_kind]["m02_yjikjong"] == $row2[0]){echo "selected";}?>><?=$row2[1];?></option>
										<?
										}

										$conn->row_free();
									?>
									</select><?
								}else{
									$sql = "select m98_name
											  from m98job
											 where m98_code = '".$mst[$basic_kind]["m02_yjikjong"]."'";

									$job_nm = $conn->get_data($sql);?>
									<div class="left"><?=$job_nm;?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th class="center" rowspan="11">급여<br>공통<br>항목</th>
							<th>급여지급은행</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<select name="yBankName" style="width:auto;">
									<?
										$bankList= $definition->GetBankList();
										$bankListCount = sizeOf($bankList);

										for($i=0; $i<$bankListCount; $i++){
										?>
											<option value="<?=$bankList[$i]['code'];?>" title="<?=$bankList[$i]['name'];?>" <? if($mst[$basic_kind]['m02_ybank_name'] == $bankList[$i]['code']){?>selected<?} ?>><?=$bankList[$i]['name'];?></option>
										<?
										}
									?>
									</select><?
								}else{?>
									<div class="left"><?=$definition->GetBankName($mst[$basic_kind]['m02_ybank_name']);?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th>계좌번호</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="yGyeojaNo" type="text" value="<?=$mst[$basic_kind]["m02_ygyeoja_no"];?>" class="no_string" style="width:100%;" onKeyDown="__onlyNumber(this, '189 109');" onFocus="this.select();"><?
								}else{?>
									<div class="left"><?=$mst[$basic_kind]["m02_ygyeoja_no"];?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th>공제대상 가족수</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="yGongJeJaNo" type="text" value="<?=$mst[$basic_kind]["m02_ygongjeja_no"];?>" maxlength="2" class="number" onKeyDown="__onlyNumber(this);" onBlur="if(this.value == ''){this.value = '0';}"><?
								}else{?>
									<div class="left"><?=$mst[$basic_kind]["m02_ygongjeja_no"];?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th>20세이하 자녀수</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="yGongJeJayeNo" type="text" value="<?=$mst[$basic_kind]["m02_ygongjejaye_no"];?>" maxlength="2" class="number" onKeyDown="__onlyNumber(this);" onBlur="if(this.value == ''){this.value = '0';}"><?
								}else{?>
									<div class="left"><?=$mst[$basic_kind]["m02_ygongjejaye_no"];?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th>4대보험 가입유무</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="y4BohumUmu" type="radio" class="radio" value="Y" <? if($mst[$basic_kind]['m02_y4bohum_umu'] == 'Y'){?>checked<?} ?> onclick="set_4ins_yn(this.value);">유
									<input name="y4BohumUmu" type="radio" class="radio" value="N" <? if($mst[$basic_kind]['m02_y4bohum_umu'] != 'Y'){?>checked<?} ?> onclick="set_4ins_yn(this.value);">무<?
								}else{?>
									<div class="left"><?=$mst[$basic_kind]['m02_y4bohum_umu'] == 'Y' ? '유' : '무';?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th>국민연금 신고 월급여액</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="yKuksinMpay" type="text" value="<?=number_format($mst[$basic_kind]["m02_ykuksin_mpay"]);?>" maxlength="10" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="__commaSet(this);"><?
								}else{?>
									<div class="left"><?=number_format($mst[$basic_kind]["m02_ykuksin_mpay"]);?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th>건강보험 신고 월급여액</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="yHealthMpay" type="text" value="<?=number_format($mst[$basic_kind]["m02_health_mpay"]);?>" maxlength="10" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="__commaSet(this);"><?
								}else{?>
									<div class="left"><?=number_format($mst[$basic_kind]["m02_health_mpay"]);?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th>고용보험 신고 월급여액</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="yEmployMpay" type="text" value="<?=number_format($mst[$basic_kind]["m02_employ_mpay"]);?>" maxlength="10" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="__commaSet(this);"><?
								}else{?>
									<div class="left"><?=number_format($mst[$basic_kind]["m02_employ_mpay"]);?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th>산제보험 신고 월급여액</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="ySanjeMpay" type="text" value="<?=number_format($mst[$basic_kind]["m02_sanje_mpay"]);?>" maxlength="10" class="number" onKeyDown="__onlyNumber(this);" onFocus="__commaUnset(this);" onBlur="__commaSet(this);"><?
								}else{?>
									<div class="left"><?=number_format($mst[$basic_kind]["m02_sanje_mpay"]);?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th>연장특별수당</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="addPayRate" type="text" value="<?=number_format($mst[$basic_kind]["m02_add_payrate"]);?>" maxlength="3" class="number" onKeyDown="__onlyNumber(this);" onchange="if(this.value==''){this.value='0.0';}" style="width:50px;" alt="not">%<?
								}else{?>
									<div class="left"><?=number_format($mst[$basic_kind]["m02_add_payrate"]);?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th>직급수당</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="rank_pay" type="text" value="<?=number_format($mst[$basic_kind]["m02_rank_pay"]);?>" maxlength="10" class="number" onKeyDown="__onlyNumber(this);" onchange="this.value=cutOff(this.value);"><?
								}else{?>
									<div class="left"><?=number_format($mst[$basic_kind]["m02_rank_pay"]);?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th colspan="2">
								<div class="help_left">스마트폰 업무 구분</div>
								<div class="help" onmouseover="_show_help(this, '핸드폰(스마트폰)으로 사용할 업무를 선택합니다. (관리자용인지 요양보호사용인지 혹은 양쪽 다 사용하는 지를 선택) 생략하면 스마트폰으로의 업무는 사용할 수가 없습니다.');" onmouseout="_hidden_help();"></div>
							</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="jikwonGbnM" type="checkbox" class="checkbox" value="Y" <? if($smart_gbn['M'] == 'Y'){?>checked<?} ?>>관리자
									<input name="jikwonGbnY" type="checkbox" class="checkbox" value="Y" <? if($smart_gbn['Y'] == 'Y'){?>checked<?} ?>>요양보호사<?
								}else{
									if ($smart_gbn['M'] == 'Y') $smart_string = '관리자';
									if ($smart_gbn['Y'] == 'Y') $smart_string .= ($smart_string != '' ? ', ' : '').'요양보호사';?>
									<div class="left"><?=$smart_string;?></div><?
								}
							?>
							</td>
						</tr>
						<tbody>
							<tr>
								<td class="last bottom" colspan="3"></td>
							</tr>
						</tbody>
					</tbody>
				</table>
			</td>

			<td class="left top bottom" style="padding:0;">
				<table class="my_table" style="width:100%; border-bottom:none;">
					<colgroup>
						<col width="20px">
						<col width="20px">
						<col width="75px">
						<col width="100px">
						<col width="90px">
						<col width="105px">
						<col>
					</colgroup>
					<tbody id="kind_0" style="display:<? if($mst[0]['m02_mkind'] != '0'){echo 'none';} ?>;">
						<tr>
							<th class="center" rowspan="<?=$row_span_count;?>">재<br>가</th>
							<th colspan="2">고용형태</th>
							<td class="last" colspan="3">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="yGoyongKind" type="radio" class="radio" value="1" <? if($mst[0]["m02_ygoyong_kind"] == "1"){echo "checked";}?>><span style="margin-left:-5px;" title="월급제근로자">정규직</span>
									<input name="yGoyongKind" type="radio" class="radio" value="2" style="margin-left:3px;" <? if($mst[0]["m02_ygoyong_kind"] == "2"){echo "checked";}?>><span style="margin-left:-5px;" title="월60시간미만근로자">계약직</span>
									<input name="yGoyongKind" type="radio" class="radio" value="3" style="margin-left:3px;" <? if($mst[0]["m02_ygoyong_kind"] == "3"){echo "checked";}?>><span style="margin-left:-5px;" title="월60시간이상근로자">시간직</span>
									<input name="yGoyongKind" type="radio" class="radio" value="4" style="margin-left:3px;" <? if($mst[0]["m02_ygoyong_kind"] == "4"){echo "checked";}?>><span style="margin-left:-5px;">기타</span>
									<input name="yGoyongKind" type="radio" class="radio" value="5" style="margin-left:3px;" <? if($mst[0]["m02_ygoyong_kind"] == "5"){echo "checked";}?>><span style="margin-left:-5px;">특수근로</span><?
								}else{
									switch($mst[0]["m02_ygoyong_kind"]){
										case '1': $goyong_kind = '정규직'; break;
										case '2': $goyong_kind = '계약직'; break;
										case '3': $goyong_kind = '시간직'; break;
										case '4': $goyong_kind = '기타'; break;
										case '5': $goyong_kind = '특수근로'; break;
									}?>
									<div class="left"><?=$goyong_kind;?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th colspan="2">고용상태</th>
							<td class="last" colspan="3">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="yGoyongStat" type="radio" class="radio" value="1" onclick="set_out_date(this.value);" <? if($mst[0]["m02_ygoyong_stat"] == "1"){echo "checked";}?>><span style="margin-left:-5px;">재직</span>
									<input name="yGoyongStat" type="radio" class="radio" value="2" onclick="set_out_date(this.value);" <? if($mst[0]["m02_ygoyong_stat"] == "2"){echo "checked";}?>><span style="margin-left:-5px;">휴직</span>
									<input name="yGoyongStat" type="radio" class="radio" value="9" onclick="set_out_date(this.value);" <? if($mst[0]["m02_ygoyong_stat"] == "9"){echo "checked";}?>><span style="margin-left:-5px;">퇴사</span><?
								}else{
									switch($mst[0]["m02_ygoyong_stat"]){
										case '1': $goyong_stat = '활동'; break;
										case '2': $goyong_stat = '휴직'; break;
										case '9': $goyong_stat = '퇴사'; break;
									}?>
									<div class="left"><?=$goyong_stat;?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th colspan="2">입사일자</th>
							<td>
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="yIpsail" type="text" value="<?=$myF->dateStyle($mst[0]["m02_yipsail"]);?>" maxlength="8" class="date" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);" tag="입사일자를 입력하여 주십시오."><?
								}else{?>
									<div class="left"><?=$myF->dateStyle($mst[0]["m02_yipsail"]);?></div><?
								}
							?>
							</td>
							<th>퇴사일자</th>
							<td class="last">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="yToisail" type="text" value="<?=$myF->dateStyle($mst[0]["m02_ytoisail"]);?>" maxlength="8" class="date" tag="<?=$myF->dateStyle($mst[0]["m02_ytoisail"]);?>" onKeyDown="__onlyNumber(this);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);"><?
								}else{?>
									<div class="left"><?=$myF->dateStyle($mst[0]["m02_ytoisail"]);?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th colspan="2">근무가능요일</th>
							<td class="last" colspan="3">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="yGunmuMon" type="checkbox" value="Y" class="checkbox" onKeyDown="__enterFocus();"<? if($mst[0]["m02_ygunmu_mon"] == "Y" || $mode == 1){echo "checked";}?>><font style="font-weight:bold;">월</font>
									<input name="yGunmuTue" type="checkbox" value="Y" class="checkbox" onKeyDown="__enterFocus();"<? if($mst[0]["m02_ygunmu_tue"] == "Y" || $mode == 1){echo "checked";}?>><font style="font-weight:bold;">화</font>
									<input name="yGunmuWed" type="checkbox" value="Y" class="checkbox" onKeyDown="__enterFocus();"<? if($mst[0]["m02_ygunmu_wed"] == "Y" || $mode == 1){echo "checked";}?>><font style="font-weight:bold;">수</font>
									<input name="yGunmuThu" type="checkbox" value="Y" class="checkbox" onKeyDown="__enterFocus();"<? if($mst[0]["m02_ygunmu_thu"] == "Y" || $mode == 1){echo "checked";}?>><font style="font-weight:bold;">목</font>
									<input name="yGunmuFri" type="checkbox" value="Y" class="checkbox" onKeyDown="__enterFocus();"<? if($mst[0]["m02_ygunmu_fri"] == "Y" || $mode == 1){echo "checked";}?>><font style="font-weight:bold;">금</font>
									<input name="yGunmuSat" type="checkbox" value="Y" class="checkbox" onKeyDown="__enterFocus();"<? if($mst[0]["m02_ygunmu_sat"] == "Y" || $mode == 1){echo "checked";}?>><font style="font-weight:bold; color:#0000ff;">토</font>
									<input name="yGunmuSun" type="checkbox" value="Y" class="checkbox" onKeyDown="__enterFocus();"<? if($mst[0]["m02_ygunmu_sun"] == "Y" || $mode == 1){echo "checked";}?>><font style="font-weight:bold; color:#ff0000;">일</font><?
								}else{
									$week_day = '';

									if ($mst[0]["m02_ygunmu_mon"] == 'Y') $week_day .= ($week_day != '' ? ', ' : '').'월';
									if ($mst[0]["m02_ygunmu_tue"] == 'Y') $week_day .= ($week_day != '' ? ', ' : '').'화';
									if ($mst[0]["m02_ygunmu_wed"] == 'Y') $week_day .= ($week_day != '' ? ', ' : '').'수';
									if ($mst[0]["m02_ygunmu_thu"] == 'Y') $week_day .= ($week_day != '' ? ', ' : '').'목';
									if ($mst[0]["m02_ygunmu_fri"] == 'Y') $week_day .= ($week_day != '' ? ', ' : '').'금';
									if ($mst[0]["m02_ygunmu_sat"] == 'Y') $week_day .= ($week_day != '' ? ', ' : '').'<font color="#0000ff">토</font>';
									if ($mst[0]["m02_ygunmu_sun"] == 'Y') $week_day .= ($week_day != '' ? ', ' : '').'<font color="#ff0000">일</font>';?>
									<div class="left"><?=$week_day;?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th colspan="2">주휴희망요일</th>
							<td class="last" colspan="3">
							<?
								if ($mode == 1 || $mode == 2){?>
									<input name="weeklyHoliday" type="radio" value="1" class="radio" onKeyDown="__enterFocus();"<? if($mst[0]["m02_weekly_holiday"] == "1" || $mode == 1){echo "checked";}?>><font style="font-weight:bold;">월</font>
									<input name="weeklyHoliday" type="radio" value="2" class="radio" onKeyDown="__enterFocus();"<? if($mst[0]["m02_weekly_holiday"] == "2" || $mode == 1){echo "checked";}?>><font style="font-weight:bold;">화</font>
									<input name="weeklyHoliday" type="radio" value="3" class="radio" onKeyDown="__enterFocus();"<? if($mst[0]["m02_weekly_holiday"] == "3" || $mode == 1){echo "checked";}?>><font style="font-weight:bold;">수</font>
									<input name="weeklyHoliday" type="radio" value="4" class="radio" onKeyDown="__enterFocus();"<? if($mst[0]["m02_weekly_holiday"] == "4" || $mode == 1){echo "checked";}?>><font style="font-weight:bold;">목</font>
									<input name="weeklyHoliday" type="radio" value="5" class="radio" onKeyDown="__enterFocus();"<? if($mst[0]["m02_weekly_holiday"] == "5" || $mode == 1){echo "checked";}?>><font style="font-weight:bold;">금</font>
									<input name="weeklyHoliday" type="radio" value="6" class="radio" onKeyDown="__enterFocus();"<? if($mst[0]["m02_weekly_holiday"] == "6" || $mode == 1){echo "checked";}?>><font style="font-weight:bold; color:#0000ff;">토</font>
									<input name="weeklyHoliday" type="radio" value="0" class="radio" onKeyDown="__enterFocus();"<? if($mst[0]["m02_weekly_holiday"] == "0" || $mode == 1){echo "checked";}?>><font style="font-weight:bold; color:#ff0000;">일</font><?
								}else{
									switch($row[0]['m02_weekly_holiday']){
										case '1':
											$week_day = '월';
											break;
										case '2':
											$week_day = '화';
											break;
										case '3':
											$week_day = '수';
											break;
										case '4':
											$week_day = '목';
											break;
										case '5':
											$week_day = '금';
											break;
										case '6':
											$week_day = '토';
											break;
										case '0':
											$week_day = '일';
											break;
									}?>
									<div class="left"><?=$week_day;?></div><?
								}
							?>
							</td>
						</tr>
						<tr>
							<th colspan="2">기준근로시간</th>
							<td>
								<input name="stndWorkTime" type="text" class="number" style="width:50px;" value="<?=$mst[0]["m02_stnd_work_time"];?>" maxlength="3" onkeydown="__onlyNumber(this, '.')">
							</td>
							<th>기준시급</th>
							<td class="last">
								<input name="stndWorkPay" type="text" class="number" style="width:100%;" value="<?=$mst[0]["m02_stnd_work_pay"];?>" maxlength="5" onkeydown="__onlyNumber(this)">
							</td>
						</tr>
						<tr>
							<td class="last" colspan="5">
							<?
								$voucher_id = 0;
								include('mem_reg_voucher.php');
							?>
							</td>
						</tr>
						<tr>
							<td class="last" colspan="5">
							<?
								include('mem_reg_family.php');
							?>
							</td>
						</tr>
						<?
							if ($subject_count > 0){?>
								<tr>
									<th rowspan="">각종수당<br>/공제처리</th>
									<td class="last" colspan="3">
										<table class="my_table" style="width:100%;">
											<colgroup>
												<col width="25%" span="4">
											</colgroup>
											<tbody>
											<?
												$tr = false;

												if ($subject_count > 0){
													for($i=0; $i<$subject_count; $i++){
														$pay = $my_subject[$i];

														if ($i == $subject_count - ($i % 2 == 0 ? 2 : 1)){
															$class_bottom = 'bottom';
														}else{
															$class_bottom = '';
														}

														if ($i % 2 == 0){
															if ($tr == true){
																echo '</tr>';
															}
															echo '<tr>';
															$tr = true;
															$class_last = '';
														}else{
															$class_last = 'last';
														}

														/*
														$payString = "";

														if ($pay["t20_kind1"] == "1"){
															if ($pay["t20_kind2"] == "1"){
																$payString = "(과세)";
															}else if ($pay["t20_kind2"] == "2"){
																$payString = "(비과세)";
															}
														}else{
															$payString = "(공제)";
														}
														*/

														$my_fix_amount = 0;
														for($j=0; $j<sizeOf($my_fix_pay); $j++){
															if ($my_fix_pay[$j]['code'] == $pay["t20_kind1"].'_'.$pay["t20_kind2"].'_'.$pay["t20_code"]){
																$my_fix_amount = number_format($my_fix_pay[$j]['amount']);
																break;
															}
														}

														echo '
															<th class="'.$class_bottom.'">'.$pay["t20_name"].$payString.'</th>
															<td class="'.$class_bottom.' '.$class_last.'"><input name="pay_'.$pay["t20_kind1"].'_'.$pay["t20_kind2"].'_'.$pay["t20_code"].'" type="text" value="'.$my_fix_amount.'" maxlength="8" class="number" style="width:100%;" onKeyDown="__onlyNumber(this);" onBlur="if(this.value == \'\'){this.value = \'0\';}"></td>
															 ';
													}
													if ($subject_count % 2 != 0){
														echo '
															<th class="bottom"></th>
															<td class="bottom last"></td>
															 ';
													}
													echo '</tr>';
												}
											?>
											</tbody>
										</table>
									</td>
								</tr><?
							}

							if ($center_ins_code > 0){ ?>
								<tr>
									<th colspan="2" rowspan="2">배상책임보험</th>
									<th>가입여부</th>
									<td class="last" colspan="2">
									<?
										if ($mode == 1 || $mode == 2){?>
											<input name="insYN" type="radio" class="radio" value="Y" onclick="_ins_join_yn(this.value, '<?=$insYN;?>', 'insFromDate', 'insToDate');" <? if($insYN == "Y"){echo "checked";}?>><span style="margin-left:-5px;">유</span>
											<input name="insYN" type="radio" class="radio" value="N" onclick="_ins_join_yn(this.value, '<?=$insYN;?>', 'insFromDate', 'insToDate');" <? if($insYN == "N"){echo "checked";}?>><span style="margin-left:-5px;">무</span><?
										}else{?>
											<div class="left"><?=$insYN == 'Y' ? '유' : '무';?></div><?
										}
									?>
									</td>
								</tr>
								<tr>
									<th>
										<div class="help_left">가입기간</div>
										<div class="help" onmouseover="_show_help(this, '만료일자는 센터에서 가입한 보험만료일자가 부여됩니다.');" onmouseout="_hidden_help();"></div>
									</th>
									<td class="last" colspan="2">
									<?
										if ($mode == 1 || $mode == 2){?>
											<input name="insFromDate" type="text" value="<? if($insYN == 'Y'){echo $myF->dateStyle($insFromDate);} ?>" tag="<?=$myF->dateStyle($insFromDate);?>" alt="_checkInsLimitDate" maxlength="8" class="date" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);" onChange="_checkInsLimitDate();"> ~
											<input name="insToDate"	  type="text" value="<? if($insYN == 'Y'){echo $myF->dateStyle($insToDate);}   ?>" tag="<?=$myF->dateStyle($insToDate);?>"   alt="_checkInsLimitDate" maxlength="8" class="date" onKeyDown="__onlyNumber(this)" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);" onChange="_checkInsLimitDate();"><?
										}else{?>
											<div class="left"><?=$myF->dateStyle($insFromDate);?>~<?=$myF->dateStyle($insToDate);?></div><?
										}
									?>
									</td>
								</tr><?
							}else{?>
								<input type="hidden" name="insYN"       value="N">
								<input type="hidden" name="insFromDate" value="">
								<input type="hidden" name="insToDate"   value=""><?
							}
						?>
					</tbody>

					<tbody>
						<tr id="kind_1">
							<th id="voucher_row" class="head" rowspan="4">바<br>우<br>처</th>
							<td class="last" colspan="5">
							<?
								$voucher_id = 1;
								include('mem_reg_voucher.php');
							?>
							</td>
						</tr>

						<tr id="kind_2">
							<td class="last" colspan="5">
							<?
								$voucher_id = 2;
								include('mem_reg_voucher.php');
							?>
							</td>
						</tr>

						<tr id="kind_3">
							<td class="last" colspan="5">
							<?
								$voucher_id = 3;
								include('mem_reg_voucher.php');
							?>
							</td>
						</tr>

						<tr id="kind_4">
							<td class="last" colspan="5">
							<?
								$voucher_id = 4;
								include('mem_reg_voucher.php');
							?>
							</td>
						</tr>
					</tbody>

					<tbody id="kind_5" style="display:<? if($mst[5]['m02_mkind'] != '5'){echo 'none';} ?>;">
						<tr>
							<th colspan="2">시설</th>
							<td class="last" colspan="3">준비중입니다.</td>
						</tr>
					</tbody>

					<tbody>
						<tr>
							<td class="last bottom" colspan="5"></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>