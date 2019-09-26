<?
	define(_COM_,'company'); //본사구분
	define(_BRAN_,'branch'); //지사구분
	define(_STORE_,'store');  //가맹점구분

	define(_COM_NM_,'본사');
	define(_BRAN_NM_,'지사');
	define(_STORE_NM_,'가맹점');

	define(_CAREVISIT_,	'carevisit.net');
	define(_DWCARE_,	'dwcare.com');
	define(_KLCF_,		'klcf.kr');
	define(_KDOLBOM_,	'kdolbom.net');
	define(_DACARE_,	'thegoodjob.net');
	define(_KACOLD_,	'kacold.net');
	define(_DASOMI_,	'dasomi-m.net');
	define(_VAERP_,		'vaerp.com');
	define(_DOLVOIN_,	'dolvoin.net');
	define(_FORWEAK_,	'forweak.net');

	$tmpDomain = explode('.', $_SERVER['HTTP_HOST']);

	if (SizeOf($tmpDomain) == 2){
		$strDomain = $tmpDomain[0].'.'.$tmpDomain[1];
	}else{
		$strDomain = $tmpDomain[1].'.'.$tmpDomain[2];
	}

	switch($strDomain){
		case _CAREVISIT_:
			define(_COM_CD_, 'GE01');
			break;

		case _DWCARE_:
			define(_COM_CD_, 'ON01');
			break;

		case _KDOLBOM_:
			define(_COM_CD_, 'KD01');
			break;

		case _DACARE_:
			define(_COM_CD_, 'DA01');
			break;

		case _KACOLD_:
			define(_COM_CD_,'KC01');
			break;

		case _DASOMI_:
			define(_COM_CD_,'DS01');
			break;

		case _VAERP_:
			define(_COM_CD_,'VA01');
			break;

		case _DOLVOIN_:
			define(_COM_CD_,'UD01');
			break;

		case _FORWEAK_:
			define(_COM_CD_,'FW01');
			break;
	}
?>