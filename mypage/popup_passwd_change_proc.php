<?
	require($_SERVER["DOCUMENT_ROOT"]."/module/lib/header.php");

	if (!chkReferer()) fnMsgJson(500, getTransLangMsg("잘못된 접근 입니다."), "");
	if (!isUser()) fnMsgJson(501, getTransLangMsg("로그인 회원만 이용 가능합니다."), "");

	$params['gubun']       = chkReqRpl("gubun", "", 10, "POST", "STR");
	$params['curr_passwd'] = chkReqRpl("curr_passwd", "", 20, "POST", "STR");
	$params['new_passwd']  = chkReqRpl("new_passwd", "", 20, "POST", "STR");
	$params['chk_passwd']  = chkReqRpl("chk_passwd", "", 20, "POST", "STR");

    if (chkBlank($params['gubun']) || !isStrpos("init,change",$params['gubun'])) fnMsgJson(502, getTransLangMsg("요청정보 값이 유효하지 않습니다."), "");

    if ($params['gubun'] == "change") {
        if (chkBlank($params['curr_passwd'])) fnMsgJson(503, getTransLangMsg("현재 비밀번호를 입력해주세요."), array("id"=>"pop_curr_passwd"));
        if (mb_strlen($params['curr_passwd'])<8 || mb_strlen($params['curr_passwd'])>20) fnMsgJson(504, getTransLangMsg("비밀번호는 8~20자 사이로 입력해주세요."), array("id"=>"pop_curr_passwd"));
    }

    if (chkBlank($params['new_passwd'])) fnMsgJson(505, getTransLangMsg("새로운 비밀번호를 입력해주세요."), array("id"=>"pop_new_passwd"));
    if (mb_strlen($params['new_passwd'])<8 || mb_strlen($params['new_passwd'])>20) fnMsgJson(506, getTransLangMsg("비밀번호는 8~20자 사이로 입력해주세요."), array("id"=>"pop_new_passwd"));

    if (chkBlank($params['chk_passwd'])) fnMsgJson(507, getTransLangMsg("새로운 비밀번호 재입력을 입력해주세요."), array("id"=>"pop_chk_passwd"));
    if (mb_strlen($params['chk_passwd'])<8 || mb_strlen($params['chk_passwd'])>20) fnMsgJson(508, getTransLangMsg("비밀번호는 8~20자 사이로 입력해주세요."), array("id"=>"pop_chk_passwd"));

    if ($params['new_passwd'] != $params['chk_passwd']) fnMsgJson(509, getTransLangMsg("새로운 비밀번호가 일치하지 않습니다."), array("id"=>"pop_new_passwd"));


	$cls_member = new CLS_MEMBER;

    //사용자 정보 불러오기
    $user_view = $cls_member->user_view($MEM_USR['usr_idx']);
    if ($user_view == false) fnMsgJson(510, getTransLangMsg("사용자 데이터를 찾을 수 없습니다.\n고객센터로 문의해주세요."), "");

    //현재 비밀번호 체크
    if ($user_view['usr_pwd'] != encryption($params['curr_passwd'])) fnMsgJson(511, getTransLangMsg("현재 비밀번호가 일치하지 않습니다."), "");

    //비밀번호 변경
    $params_passwd['usr_idx'] = $user_view['usr_idx'];
    $params_passwd['usr_pwd'] = encryption($params['new_passwd']);
    $params_passwd['upt_ip']  = NOW_IP;
    $params_passwd['upt_id']  = $MEM_USR['usr_idx'];
    if (!$cls_member->user_passwd_save($params_passwd)) fnMsgJson(512, getTransLangMsg("저장 처리중 문제가 발생했습니다.\n고객센터에 문의주세요."), "");

    //사용자 정보 세션
    $MEM_USR['usr_pwd'] = encryption($params['new_passwd']);
	setSession("user_view", $MEM_USR);
?>
{"result": 200, "message": "OK"}