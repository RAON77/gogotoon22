<?
	require($_SERVER["DOCUMENT_ROOT"]."/module/lib/header.php");

	//접속통계 DB입력
	require($_SERVER["DOCUMENT_ROOT"]."/module/statistics/statistics_sava_proc.php");

	//지역정보 불러오기
	$cls_member = new CLS_MEMBER;

	if (!isUser()) {
		$save_login = $_COOKIE["USR_SAVE_LOGIN"];

		if ($save_id != "") $save_id = decryption($save_id);
		if ($save_login != "") {
			$save_login = decryption($save_login);

			//사용자 정보 불러오기
			$user_view = $cls_member->user_view($save_login);

			if ($user_view == false) {
				fnMsgGo(599, "", "/member/logout.php", "");
			} else {
				//사용자 상태 확인
				if ($user_view['status'] != 'Y') fnMsgGo(591, getTransLangMsg("이용이 제한된 사용자 입니다.\n고객센터에 문의 주세요."), "/member/logout.php", "");

				//로그인 로그 저장 및 마지막 로그인 접수일 업데이트
				if ($cls_member->last_visit_update($user_view['usr_idx']) == false) fnMsgGo(592, getTransLangMsg("일시적인 문제가 발생했습니다.\n고객센터에 문의주세요."), "/member/logout.php", "");

				//기본 언어설정
				if (chkBlank($_COOKIE["SITE_SAVE_LANG"])) $_COOKIE["SITE_SAVE_LANG"] = $user_view['default_lang'];
				$_COOKIE["TRANS_SAVE_LANG"] = $user_view['default_lang'];

				//세션저장
				setSession("user_view", $user_view);

				//fnMsgGo(593, "", "/", "");
			}
		}
	} else {
		//사용자 정보 불러오기
		$user_view = $cls_member->user_view($MEM_USR['usr_idx']);

		//세션저장
		setSession("user_view", $user_view);
	}

    //상단 언어설정 목록
    $TOP_LANG_LIST = getSiteLangList(SITE_SAVE_LANG);
?>