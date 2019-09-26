<?
	/******************************************
	 *
	 *	복지용구
	 *
	 ******************************************/
	class wmd{
		var $myF;

		/******************************************
		 *	amt		: 제품 월 대여금액
		 *	fromDt	: 대여시작일
		 *	toDt	: 대여종료일
		 *	rate	: 본인부담율
		 *
		 *	리턴값	: SUM 대여기간전체 내용, FIRST 첫달의 내용, LAST 마지막달의 내용
		 *			  USE 사용금액, EXP 본인부담금액, CNT 일수
		 *
		 ******************************************/
		function AmtInfo($amt, $fromDt, $toDt, $rate = 100){
			$fromDt = str_replace('-','',$this->myF->dateStyle($fromDt));
			$toDt	= str_replace('-','',$this->myF->dateStyle($toDt));

			$fromLastday = Date('t',StrToTime($fromDt));
			$toLastday	 = Date('t',StrToTime($toDt));


			$fromTDay = SubStr($fromDt,0,6).$fromLastday;
			$fromWDay = IntVal(Date('w',StrToTime($fromTDay))) * -1;
			$fromFDay = $this->myF->dateAdd('day', $fromWDay, $fromTDay, 'Ymd');

			$data['MSG'] = $fromDt.'/'.$fromFDay.'/'.$fromTDay;

			if ($fromDt >= $fromFDay && $fromDt <= $fromTDay){
				#if ($fromLastday > 30) $fromLastday = 30;
			}else{
			}

			if ($toLastday - IntVal(Date('d',StrToTime($toDt))) > 10){
				#if ($toLastday > 30) $toLastday = 30;
			}

			$fromYm = SubStr($fromDt, 0, 6);
			$toYm	= SubStr($toDt, 0, 6);

			//첫달, 마지막달의 일수
			if ($fromYm == $toYm){
				$fromDays	= $this->myF->dateDiff('d', $fromDt, $toDt) + 1;
				#$fromDays	= $this->myF->dateDiff('d', $fromDt, $toDt);
				$toDays		= 0;
			}else{
				$fromDays	= $this->myF->dateDiff('d', $fromDt, $fromYm.$fromLastday) + 1;
				#$fromDays	= $this->myF->dateDiff('d', $fromDt, $fromYm.$fromLastday);
				$toDays		= IntVal(Date('d',StrToTime($toDt)));
			}

			//개월수
			$termMons = Round((StrToTime($this->myF->dateAdd('date', -1, $toYm.'01', 'Ymd')) - StrToTime($this->myF->dateAdd('month', 1, $fromYm.'01', 'Ymd'))) / 60 / 60 / 24 / 30);
			if ($termMons < 0) $termMons = 0;


			$data['SUM']['USE'] = 0; //사용금액
			$data['SUM']['EXP'] = 0; //본인부담금액
			$data['SUM']['RST'] = 0; //나머지금액

			$data['FIRST']['CNT'] = 0; //시작달 일수
			$data['FIRST']['USE'] = 0; //시작달 사용금액
			$data['FIRST']['EXP'] = 0; //시작달 본인부담금액
			$data['FIRST']['RST'] = 0; //나머지금액

			$data['LAST']['CNT'] = 0; //마지막달 일수
			$data['LAST']['USE'] = 0; //마지막달 사용금액
			$data['LAST']['EXP'] = 0; //마지막달 본인부담금액
			$data['LAST']['RST'] = 0; //나머지금액

			$data['MONTH']['CNT'] = 0; //월수
			$data['MONTH']['USE'] = 0; //월 사용금액
			$data['MONTH']['EXP'] = 0; //월 본인부담금액
			$data['MONTH']['RST'] = 0; //나머지금액

			//첫달 본인부담금 및 사용금액
			if ($fromLastday == $fromDays){
				$data['FIRST']['USE'] = $amt;
				$data['FIRST']['EXP'] = Floor($amt * $rate / 1000) * 10;
			}else{
				$data['FIRST']['CNT'] = $fromDays;
				//$data['FIRST']['USE'] = Floor($amt / 30 * $fromDays / 10) * 10;
				$data['FIRST']['USE'] = Floor($amt / 30 * $fromDays);
				$data['FIRST']['EXP'] = Floor($amt / 30 * $fromDays * $rate / 1000) * 10;
			}

			$data['SUM']['USE'] += $data['FIRST']['USE'];
			$data['SUM']['EXP'] += $data['FIRST']['EXP'];


			//월별 본인부담금 및 사용금액
			$data['MONTH']['CNT'] = $termMons;
			$data['MONTH']['USE'] = $amt; // * $termMons;
			$data['MONTH']['EXP'] = Floor($amt * $rate / 1000) * 10; // * $termMons;

			$data['SUM']['USE'] += ($data['MONTH']['USE'] * $data['MONTH']['CNT']);
			$data['SUM']['EXP'] += ($data['MONTH']['EXP'] * $data['MONTH']['CNT']);

			//마지막달 본인부담금 및 사용금액
			if ($toLastday == $toDays){
				$data['LAST']['USE'] += $amt;
				$data['LAST']['EXP'] += Floor($amt * $rate / 1000) * 10;
			}else{
				$data['LAST']['CNT'] = $toDays;
				//$data['LAST']['USE'] = Floor($amt / 30 * $toDays / 10) * 10;
				$data['LAST']['USE'] = Floor($amt / 30 * $toDays);
				$data['LAST']['EXP'] = Floor($amt / 30 * $toDays * $rate / 1000) * 10;
			}

			$data['SUM']['USE'] += $data['LAST']['USE'];
			$data['SUM']['EXP'] += $data['LAST']['EXP'];

			$data['SUM']['USE'] = Floor($data['SUM']['USE'] / 10) * 10;
			$data['LAST']['RST'] = Floor($data['SUM']['USE'] * $rate / 1000) * 10 - $data['SUM']['EXP'];
			$data['SUM']['RST'] = $data['FIRST']['RST'] + $data['MONTH']['RST'] * $data['MONTH']['CNT'] + $data['LAST']['RST'];

			return $data;
		}
	}

	include_once('../inc/_myFun.php');

	$wmd = new wmd();
	$wmd->myF = $myF;
?>