<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	require_once("../excel/PHPExcel.php");
	#require_once '../excel/PHPExcel/IOFactory.php';
	#include '../excel/PHPExcel/Writer/Excel2007.php';


	$orgNo	= $_SESSION['userCenterCode'];
	$type	= $_GET['type'];

	$sql = 'SELECT	DISTINCT m00_store_nm AS org_nm, m00_mname AS mg_nm, m00_caddr1 AS addr1, m00_caddr2 AS addr2
			FROM	m00center
			WHERE	m00_mcode = \''.$orgNo.'\'';

	$row = $conn->get_array($sql);

	$orgNm = $row['org_nm'];
	$mgNm = $row['mg_nm'];
	$addr = $row['addr1'].' '.$row['addr2'];

	Unset($row);


	$sql = 'SELECT	acct_bedt, cms_start_ym
			FROM	center_cont_info
			WHERE	org_no = \''.$orgNo.'\'';

	$row = $conn->get_array($sql);

	$acctBeDt = $row['acct_bedt'];
	$cmsStartYm = $row['cms_start_ym'];

	Unset($row);


	//header( "Content-type: application/vnd.ms-excel" );
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header( "Content-type: charset=utf-8" );
	header( "Content-Disposition: attachment; filename=".$myF->euckr("표준(신규계약)-장기요양 계약서").".xls" );
	header( "Content-Description: ".$myF->euckr("표준(신규계약)-장기요양 계약서"));
	header('Cache-Control: max-age=0');
	header( "Pragma: no-cache" );
	header( "Expires: 0" );


	$excel = new PHPExcel();
	$excel = PHPExcel_IOFactory::createReader('Excel2007');
	$excel = $excel->load('./doc/carevisit_contract'.($type == 're' ? '_re' : '').'.xlsx');
	$sheet = $excel->getActiveSheet();

	$sheet	->setCellValue('C4', $orgNm)
			->setCellValue('C5', $addr)
			->setCellValue('C6', $mgNm.'            (서명 및 도장)');


	if ($type == 're'){
	}else{
		$sheet	->setCellValue('L2', $myF->dateStyle($acctBeDt, 'KOR'))
				->setCellValue('C53', str_replace('201  년   월', $myF->_styleYYMM($cmsStartYm, 'KOR'), $sheet->getCell('C53')->getValue()));
	}

	$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	$objWriter->save("php://output");

	include_once('../inc/_db_close.php');
?>