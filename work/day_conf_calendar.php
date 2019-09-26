<?
	include("../inc/_db_open.php");

	$mCode = $_POST["mCode"];
	$mKind = $_POST["mKind"];
	$mYear = $_POST["mYear"];
	$mMonth = $_POST["mMonth"];

	$sql = "select sum(case when substring(t01_sugup_date, 7, 8) = '01' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_01"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '02' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_02"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '03' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_03"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '04' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_04"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '05' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_05"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '06' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_06"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '07' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_07"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '08' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_08"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '09' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_09"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '10' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_10"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '11' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_11"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '12' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_12"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '13' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_13"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '14' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_14"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '15' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_15"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '16' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_16"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '17' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_17"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '18' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_18"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '19' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_19"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '20' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_20"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '21' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_21"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '22' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_22"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '23' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_23"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '24' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_24"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '25' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_25"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '26' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_26"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '27' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_27"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '28' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_28"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '29' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_29"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '30' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_30"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '31' and t01_svc_subcode = '200' and t01_status_gbn != 'C' then 1 else 0 end) as day_200_31"

		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '01' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_01"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '02' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_02"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '03' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_03"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '04' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_04"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '05' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_05"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '06' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_06"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '07' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_07"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '08' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_08"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '09' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_09"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '10' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_10"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '11' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_11"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '12' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_12"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '13' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_13"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '14' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_14"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '15' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_15"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '16' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_16"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '17' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_17"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '18' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_18"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '19' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_19"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '20' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_20"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '21' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_21"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '22' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_22"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '23' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_23"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '24' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_24"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '25' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_25"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '26' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_26"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '27' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_27"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '28' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_28"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '29' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_29"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '30' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_30"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '31' and t01_svc_subcode = '500' and t01_status_gbn != 'C' then 1 else 0 end) as day_500_31"

		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '01' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_01"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '02' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_02"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '03' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_03"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '04' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_04"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '05' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_05"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '06' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_06"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '07' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_07"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '08' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_08"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '09' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_09"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '10' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_10"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '11' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_11"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '12' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_12"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '13' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_13"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '14' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_14"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '15' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_15"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '16' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_16"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '17' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_17"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '18' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_18"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '19' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_19"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '20' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_20"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '21' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_21"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '22' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_22"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '23' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_23"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '24' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_24"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '25' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_25"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '26' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_26"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '27' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_27"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '28' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_28"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '29' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_29"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '30' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_30"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '31' and t01_svc_subcode = '800' and t01_status_gbn != 'C' then 1 else 0 end) as day_800_31"

		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '01' and t01_status_gbn = 'C' then 1 else 0 end) as day_cancel_01"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '02' and t01_status_gbn = 'C' then 1 else 0 end) as day_cancel_02"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '03' and t01_status_gbn = 'C' then 1 else 0 end) as day_cancel_03"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '04' and t01_status_gbn = 'C' then 1 else 0 end) as day_cancel_04"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '05' and t01_status_gbn = 'C' then 1 else 0 end) as day_cancel_05"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '06' and t01_status_gbn = 'C' then 1 else 0 end) as day_cancel_06"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '07' and t01_status_gbn = 'C' then 1 else 0 end) as day_cancel_07"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '08' and t01_status_gbn = 'C' then 1 else 0 end) as day_cancel_08"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '09' and t01_status_gbn = 'C' then 1 else 0 end) as day_cancel_09"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '10' and t01_status_gbn = 'C' then 1 else 0 end) as day_cancel_10"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '11' and t01_status_gbn = 'C' then 1 else 0 end) as day_cancel_11"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '12' and t01_status_gbn = 'C' then 1 else 0 end) as day_cancel_12"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '13' and t01_status_gbn = 'C' then 1 else 0 end) as day_cancel_13"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '14' and t01_status_gbn = 'C' then 1 else 0 end) as day_cancel_14"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '15' and t01_status_gbn = 'C' then 1 else 0 end) as day_cancel_15"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '16' and t01_status_gbn = 'C' then 1 else 0 end) as day_cancel_16"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '17' and t01_status_gbn = 'C' then 1 else 0 end) as day_cancel_17"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '18' and t01_status_gbn = 'C' then 1 else 0 end) as day_cancel_18"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '19' and t01_status_gbn = 'C' then 1 else 0 end) as day_cancel_19"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '20' and t01_status_gbn = 'C' then 1 else 0 end) as day_cancel_20"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '21' and t01_status_gbn = 'C' then 1 else 0 end) as day_cancel_21"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '22' and t01_status_gbn = 'C' then 1 else 0 end) as day_cancel_22"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '23' and t01_status_gbn = 'C' then 1 else 0 end) as day_cancel_23"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '24' and t01_status_gbn = 'C' then 1 else 0 end) as day_cancel_24"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '25' and t01_status_gbn = 'C' then 1 else 0 end) as day_cancel_25"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '26' and t01_status_gbn = 'C' then 1 else 0 end) as day_cancel_26"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '27' and t01_status_gbn = 'C' then 1 else 0 end) as day_cancel_27"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '28' and t01_status_gbn = 'C' then 1 else 0 end) as day_cancel_28"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '29' and t01_status_gbn = 'C' then 1 else 0 end) as day_cancel_29"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '30' and t01_status_gbn = 'C' then 1 else 0 end) as day_cancel_30"
		 . ",      sum(case when substring(t01_sugup_date, 7, 8) = '31' and t01_status_gbn = 'C' then 1 else 0 end) as day_cancel_31"

		 . "  from t01iljung"
		 . " where t01_ccode = '".$mCode
		 . "'  and t01_mkind = '".$mKind
		 . "'  and t01_sugup_date like '".$mYear.$mMonth
		 . "%' and t01_del_yn = 'N'";
	$conn->query($sql);
	$iljung = $conn->fetch();
	$conn->row_free();

	$setYear = $conn->get_iljung_year($mCode);
	$nowYear = date('Y', mkTime())+(date("m", mkTime())=="12"?1:0);
?>
<table style="width:100%;">
<tr>
	<td class="title">일 실적 등록(수급자)</td>
</tr>
</table>
<table style="width:100%;">
<tr>
<td style="text-align:left; border:none;" colspan="14">
	<select name="mKind" style="width:150px;">
	<?
		for($r=0; $r<sizeOf($_SESSION["userCenterKind"]); $r++){
		?>
			<option value="<?=$_SESSION["userCenterKind"][$r];?>"<? if($_SESSION["userCenterKind"][$r] == $mKind){echo "selected";}?>><?=$_SESSION["userCenterKindName"][$r];?></option>
		<?
		}
	?>
	</select>
	<select name="mYear" style="width:65px;">
	<?
		for($i=$setYear[0]; $i<=$nowYear; $i++){
		?>
			<option value="<?=$i;?>" <? if($i == $mYear){echo 'selected';} ?>><?=$i;?>년</option>
		<?
		}
	?>
	</select>
	<select name="mMonth" style="width:55px;">
		<option value="01"<? if($mMonth == "01"){echo "selected";}?>>1월</option>
		<option value="02"<? if($mMonth == "02"){echo "selected";}?>>2월</option>
		<option value="03"<? if($mMonth == "03"){echo "selected";}?>>3월</option>
		<option value="04"<? if($mMonth == "04"){echo "selected";}?>>4월</option>
		<option value="05"<? if($mMonth == "05"){echo "selected";}?>>5월</option>
		<option value="06"<? if($mMonth == "06"){echo "selected";}?>>6월</option>
		<option value="07"<? if($mMonth == "07"){echo "selected";}?>>7월</option>
		<option value="08"<? if($mMonth == "08"){echo "selected";}?>>8월</option>
		<option value="09"<? if($mMonth == "09"){echo "selected";}?>>9월</option>
		<option value="10"<? if($mMonth == "10"){echo "selected";}?>>10월</option>
		<option value="11"<? if($mMonth == "11"){echo "selected";}?>>11월</option>
		<option value="12"<? if($mMonth == "12"){echo "selected";}?>>12월</option>
	</select>
	<input type="button" onClick="setDayConfCalendar(document.getElementById('myBody'), document.getElementById('mCode').value, document.getElementById('mKind').value, document.getElementById('mYear').value, document.getElementById('mMonth').value);" value="조회" class="btnSmall2" onFocus="this.blur();">
</td>
</tr>
<tr>
	<td style="width:15%; padding-left:5px; background-color:#eeeeee; font-weight:bold; color:#ff0000;" colspan="2">일</td>
	<td style="width:14%; padding-left:5px; background-color:#eeeeee; font-weight:bold;" colspan="2">월</td>
	<td style="width:14%; padding-left:5px; background-color:#eeeeee; font-weight:bold;" colspan="2">화</td>
	<td style="width:14%; padding-left:5px; background-color:#eeeeee; font-weight:bold;" colspan="2">수</td>
	<td style="width:14%; padding-left:5px; background-color:#eeeeee; font-weight:bold;" colspan="2">목</td>
	<td style="width:14%; padding-left:5px; background-color:#eeeeee; font-weight:bold;" colspan="2">금</td>
	<td style="width:15%; padding-left:5px; background-color:#eeeeee; font-weight:bold; color:#0000ff;" colspan="2">토</td>
</tr>
<?
	$calTime   = mkTime(0, 0, 1, $mMonth, 1, $mYear);
	$today     = date("Ymd", mktime());
	$lastDay   = date("t", $calTime); //총일수 구하기
	$startWeek = date("w", strtotime(date("Y-m", $calTime)."-01")); //시작요일 구하기
	$totalWeek = ceil(($lastDay + $startWeek) / 7); //총 몇 주인지 구하기
	$lastWeek  = date('w', strtotime(date("Y-m", $calTime)."-".$lastDay)); //마지막 요일 구하기
	$day = 1; //화면에 표시할 화면의 초기값을 1로 설정

	for($i=1; $i<=$lastDay; $i++){
		$dayIndex[$i] = 1;
	}

	// 총 주 수에 맞춰서 세로줄 만들기
	for($i=1; $i<=$totalWeek; $i++){
		echo "<tr>";
		// 총 가로칸 만들기
		for ($j=0; $j<7; $j++){
			echo "<td style='width:3%; text-align:right; vertical-align:top; padding-right:5px; line-height:1.5em; background-color:#f8f9e3;'>";
			if (!(($i == 1 && $j < $startWeek) || ($i == $totalWeek && $j > $lastWeek))){
				$index = $dayIndex[$day];
				$iljungDate = date("Ymd", mkTime(0, 0, 1, $mMonth, $day, $mYear));

				$sql = "select ifnull(holiday_name, '')"
					 . "  from tbl_holiday"
					 . " where mdate = '".$iljungDate
					 . "'";
				$conn->query($sql);
				$row = $conn->fetch();
				if ($row[0] != ''){
					$holidayName = $row[0];
					$holiday = 'Y';
				}else{
					$holidayName = '';
					$holiday = 'N';
				}
				$conn->row_free();

				if ($holidayName == ''){
					if($j == 0){
						echo "<font color='#FF0000'>".$day."</font>";
					}else if($j == 6){
						echo "<font color='#0000FF'>".$day."</font>";
					}else{
						echo "<font color='#000000'>".$day."</font>";
					}
				}else{
					echo "<font color='#FF0000' title='".$holidayName."'>".$day."</font>";
				}

				$content = "";
				$tempDay = (($day < 10) ? "0" : "").$day;

				if ($iljung["day_200_".$tempDay] > 0){
					$content .= "요양 : ".$iljung["day_200_".$tempDay]."건<br>";
				}
				if ($iljung["day_500_".$tempDay] > 0){
					$content .= "목욕 : ".$iljung["day_500_".$tempDay]."건<br>";
				}
				if ($iljung["day_800_".$tempDay] > 0){
					$content .= "간호 : ".$iljung["day_800_".$tempDay]."건<br>";
				}
				if ($iljung["day_cancel_".$tempDay] > 0){
					$content .= "<span style='color:#ff0000;'>에러 : ".$iljung["day_cancel_".$tempDay]."건</span><br>";
				}

				$day++;
			}else{
				$content = "";
			}
			echo "</td>";

			if ($content != ""){
				echo "<td style='text-align:left; vertical-align:top; padding-left:5px; line-height:1.5em; cursor:pointer;' onClick='getDayConfList(document.getElementById(\"myBody\"),\"".$mCode."\",\"".$mKind."\",\"".$mYear."\",\"".$mMonth."\",\"".$tempDay."\");'>".$content."</td>";
			}else{
				echo "<td></td>";
			}
		}
		echo "</tr>";
	}
?>
</table>
<?
	include("../inc/_db_close.php");
?>