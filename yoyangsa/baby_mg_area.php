<?
	if ($_SESSION['userCenterCode'] == 'DW-F-063-01'){?>
		<label><input name="cboBabyMgArea" type="radio" class="radio" value="" <?=$memOption['baby_mg_area'] == '' ? 'checked' : '';?>>관리지역없음</label><br>
		<label><input name="cboBabyMgArea" type="radio" class="radio" value="1" <?=$memOption['baby_mg_area'] == '1' ? 'checked' : '';?>>전주, 완주</label>
		<label><input name="cboBabyMgArea" type="radio" class="radio" value="2" <?=$memOption['baby_mg_area'] == '2' ? 'checked' : '';?>>익산, 군산</label>
		<label><input name="cboBabyMgArea" type="radio" class="radio" value="3" <?=$memOption['baby_mg_area'] == '3' ? 'checked' : '';?>>김제, 부안</label><br>
		<label><input name="cboBabyMgArea" type="radio" class="radio" value="4" <?=$memOption['baby_mg_area'] == '4' ? 'checked' : '';?>>무주, 진안, 장수</label>
		<label><input name="cboBabyMgArea" type="radio" class="radio" value="5" <?=$memOption['baby_mg_area'] == '5' ? 'checked' : '';?>>임실, 남원, 고창, 순창, 정읍</label><?
	}else if ($_SESSION['userCenterCode'] == 'VA201605001'){?>
		<label><input name="cboBabyMgArea" type="radio" class="radio" value="" <?=$memOption['baby_mg_area'] == '' ? 'checked' : '';?>>관리지역없음</label><br>
		<label><input name="cboBabyMgArea" type="radio" class="radio" value="1" <?=$memOption['baby_mg_area'] == '1' ? 'checked' : '';?>>광주전체</label>
		<label><input name="cboBabyMgArea" type="radio" class="radio" value="2" <?=$memOption['baby_mg_area'] == '2' ? 'checked' : '';?>>광주동구</label>
		<label><input name="cboBabyMgArea" type="radio" class="radio" value="3" <?=$memOption['baby_mg_area'] == '3' ? 'checked' : '';?>>광주서구</label>
		<label><input name="cboBabyMgArea" type="radio" class="radio" value="4" <?=$memOption['baby_mg_area'] == '4' ? 'checked' : '';?>>광주남구</label><br>
		<label><input name="cboBabyMgArea" type="radio" class="radio" value="5" <?=$memOption['baby_mg_area'] == '5' ? 'checked' : '';?>>광주북구</label>
		<label><input name="cboBabyMgArea" type="radio" class="radio" value="6" <?=$memOption['baby_mg_area'] == '6' ? 'checked' : '';?>>광주광산구</label>
		<label><input name="cboBabyMgArea" type="radio" class="radio" value="7" <?=$memOption['baby_mg_area'] == '7' ? 'checked' : '';?>>전남장성</label>
		<label><input name="cboBabyMgArea" type="radio" class="radio" value="8" <?=$memOption['baby_mg_area'] == '8' ? 'checked' : '';?>>전남담양</label>
		<label><input name="cboBabyMgArea" type="radio" class="radio" value="9" <?=$memOption['baby_mg_area'] == '9' ? 'checked' : '';?>>전남나주</label>
		<label><input name="cboBabyMgArea" type="radio" class="radio" value="10" <?=$memOption['baby_mg_area'] == '10' ? 'checked' : '';?>>기타</label><?
	}else{?>
		<label><input name="cboBabyMgArea" type="radio" class="radio" value="" <?=$memOption['baby_mg_area'] == '' ? 'checked' : '';?>>관리지역없음</label><?
	}
?>