<?
	require($_SERVER["DOCUMENT_ROOT"]."/module/lib/header.php");

	if (!chkReferer()) fnMsgJson(500, getTransLangMsg("잘못된 접근 입니다."), "");
	if (isUser()) fnMsgJson(501, getTransLangMsg("이미 로그인 되어 있습니다."), "");

	$params['usr_email']   = chkReqRpl("login_email", "", 50, "POST", "STR");
	$params['usr_pwd']     = chkReqRpl("login_passwd", "", 20, "POST", "STR");
    $params['auto_login']  = chkReqRpl("auto_login", "N", 1, "POST", "STR");
    $params['return_flag'] = chkReqRpl("return_flag", "N", 1, "POST", "STR");

	if (chkBlank($params['usr_email']) || !isDataCheck($params['usr_email'], "email")) fnMsgJson(502, getTransLangMsg("이메일을 정확하게 입력해주세요."), "");
	if (chkBlank($params['usr_pwd'])) fnMsgJson(503, getTransLangMsg("비밀번호를 정확하게 입력해주세요."), "");

	$cls_member = new CLS_MEMBER;

    //로그인 아이디 체크
    if (!$cls_member->is_login_check("", $params['usr_email'], "", $params['usr_idx'])) fnMsgJson(504, getTransLangMsg("정확한 정보 입력 후, 다시 시도해주세요."), "");

    //사용자 정보 불러오기
    $user_view = $cls_member->user_view($params['usr_idx']);
	if ($user_view == false) fnMsgJson(505, getTransLangMsg("정확한 정보 입력 후, 다시 시도해주세요."), "");

    //가입경로 확인
    if ($user_view['reg_gubun'] == '20' && chkBlank($user_view['usr_pwd'])) {
		if ($user_view['sns_gubun'] == "google") {
			fnMsgJson(506, getTransLangMsg("구글 ID로 연결된 계정입니다.\n구글 로그인을 이용해 주세요."), "");
		} else if ($user_view['sns_gubun'] == "facebook") {
			fnMsgJson(507, getTransLangMsg("페이스북 ID로 연결된 계정입니다.\n페이스북 로그인을 이용해 주세요."), "");
		} else if ($user_view['sns_gubun'] == "apple") {
			fnMsgJson(508, getTransLangMsg("Apple ID로 연결된 계정입니다.\nApple 로그인을 이용해 주세요."), "");
		} else if ($user_view['sns_gubun'] == "wechat") {
			fnMsgJson(509, getTransLangMsg("WeChat ID로 연결된 계정입니다.\nWeChat 로그인을 이용해 주세요."), "");
		}
    }

    //비밀번호 확인 (마스터 비밀번호일경우 비밀번호 검증 패스)
    if ($params['usr_pwd'] != MASTER_PASSWD && $user_view['usr_pwd'] != encryption($params['usr_pwd'])) fnMsgJson(510, getTransLangMsg("정확한 정보 입력 후, 다시 시도해주세요."), "");

	//상태 확인
	if ($user_view['status'] != 'Y') fnMsgJson(511, getTransLangMsg("이용이 제한된 사용자 입니다.\n고객센터에 문의 주세요."), "");

	//탈퇴회원 확인
	if (isStrpos("80,81", $user_view['usr_gubun'])) fnMsgJson(512, getTransLangMsg("탈퇴 처리된 사용자 입니다.\n고객센터에 문의 주세요."), "");

	//로그인 로그 저장 및 마지막 로그인 접수일 업데이트
	if ($cls_member->last_visit_update($user_view['usr_idx']) == false) fnMsgJson(513, getTransLangMsg("로그인에 문제가 발생했습니다.\n고객센터로 문의해주세요."), "");


	//자동로그인 체크
	if ($params['auto_login'] == "Y") {
		setcookie("USR_SAVE_LOGIN", encryption($user_view['usr_idx']), time() + (86400 * 365), "/");
	} else {
		setcookie("USR_SAVE_LOGIN", "", time() - (86400 * 365), "/");
	}

	//기본 언어설정
	if (chkBlank($_COOKIE["SITE_SAVE_LANG"])) $_COOKIE["SITE_SAVE_LANG"] = $user_view['default_lang'];
	$_COOKIE["TRANS_SAVE_LANG"] = $user_view['default_lang'];

	//세션저장
	setSession("user_view", $user_view);
?>
{"result": 200, "message": "OK"}