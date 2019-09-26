<?
	#####################################
	#
	# 쿼리 리스트
	#
	#####################################

	class query_list{
		function query($index, $param){
			$sql = "";

			switch($index){
				case 'suga_info':
					$sql = "select service_gbn, service_lvl, service_cost
						  from suga_service
						 where org_no           = '".$param['code']."'
						   and service_kind     = '".$param['kind']."'
						   and service_code     = '".$param['suga']."'
						   and service_from_dt <= '".$param['date']."'
						   and service_to_dt   >= '".$param['date']."'";
			}

			return $sql;
		}
	}

	$query_list = new query_list();
?>