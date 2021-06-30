<?
	require($_SERVER["DOCUMENT_ROOT"]."/module/lib/header.php");

	if (!chkReferer()) fnMsgJson(500, getTransLangMsg("잘못된 접근 입니다."), "");
	if (isUser()) fnMsgJson(501, getTransLangMsg("이미 로그인 되어 있습니다."), "");

	$params['token']      = chkReqRpl("token", "", 500, "POST", "STR");
	$params['new_passwd'] = chkReqRpl("new_passwd", "", 20, "POST", "STR");
	$params['chk_passwd'] = chkReqRpl("chk_passwd", "", 20, "POST", "STR");

    if (chkBlank($params['token'])) fnMsgJson(502, getTransLangMsg("요청정보 값이 유효하지 않습니다."), "");

    if (chkBlank($params['new_passwd'])) fnMsgJson(503, getTransLangMsg("새로운 비밀번호를 입력해주세요."), array("id"=>"new_passwd"));
    if (mb_strlen($params['new_passwd'])<8 || mb_strlen($params['new_passwd'])>20) fnMsgJson(504, getTransLangMsg("비밀번호는 8~20자 사이로 입력해주세요."), array("id"=>"new_passwd"));

    if (chkBlank($params['chk_passwd'])) fnMsgJson(505, getTransLangMsg("새로운 비밀번호 재입력을 입력해주세요."), array("id"=>"chk_passwd"));
    if (mb_strlen($params['chk_passwd'])<8 || mb_strlen($params['chk_passwd'])>20) fnMsgJson(506, getTransLangMsg("비밀번호는 8~20자 사이로 입력해주세요."), array("id"=>"chk_passwd"));

    if ($params['new_passwd'] != $params['chk_passwd']) fnMsgJson(507, getTransLangMsg("새로운 비밀번호가 일치하지 않습니다."), array("id"=>"new_passwd"));

	$cls_member = new CLS_MEMBER;
	$cls_jwt = new CLS_JWT;

	$token_data = $cls_jwt->dehashing($token, $error_msg);
	if ($token_data == false) fnMsgJson(508, $error_msg, array("id"=>"new_passwd"));

	//이메일 아이디 체크
	if (!$cls_member->is_login_check("", $token_data['usr_email'], "", $params['usr_idx'])) fnMsgJson(508, getTransLangMsg("잘못된 요청 정보 입니다.\n고객센터로 문의해주세요."), array("id"=>"new_passwd"));

    //사용자 정보 불러오기
    $user_view = $cls_member->user_view($params['usr_idx']);
	if ($user_view == false) fnMsgJson(509, getTransLangMsg("일치하는 사용자 데이터가 없습니다."), array("id"=>"new_passwd"));

	//탈퇴회원 확인
	if (isStrpos("80,81", $user_view['usr_gubun'])) fnMsgJson(510, getTransLangMsg("탈퇴 처리된 사용자 입니다.\n고객센터에 문의 주세요."), array("id"=>"new_passwd"));

    //비밀번호 변경
    $params_passwd['usr_idx'] = $user_view['usr_idx'];
    $params_passwd['usr_pwd'] = encryption($params['new_passwd']);
    $params_passwd['upt_ip']  = NOW_IP;
    $params_passwd['upt_id']  = $user_view['usr_idx'];
    if (!$cls_member->user_passwd_save($params_passwd)) fnMsgJson(511, getTransLangMsg("저장 처리중 문제가 발생했습니다.\n고객센터에 문의주세요."), array("id"=>"new_passwd"));

    //토큰 데이터 사용 처리
    $cls_jwt->token_used_save($token);
?>
{"result": 200, "message": "OK"}