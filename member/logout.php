<?
	require($_SERVER["DOCUMENT_ROOT"]."/module/lib/header.php");

	$tp = chkReqRpl("tp", "", 20, "", "STR");

	delSession("user_view");

	$MEM_USR = array();

	//로그아웃 버튼 클릭시만 자동로그인 해제
	if ($tp == "btn") {
		setcookie("USR_SAVE_LOGIN", "", time() - (86400 * 365), "/");
	}

	header("Location: /");
?>