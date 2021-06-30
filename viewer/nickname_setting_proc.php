<?
	require($_SERVER["DOCUMENT_ROOT"]."/module/lib/header.php");

	if (!chkReferer()) fnMsgJson(500, getTransLangMsg("잘못된 접근 입니다."), "");
	if (!isUser()) fnMsgJson(501, getTransLangMsg("로그인 회원만 이용 가능합니다."), "");

	$params['nick_name'] = chkReqRpl("nick_name", "", 50, "POST", "STR");

    if (chkBlank($params['nick_name'])) fnMsgJson(502, getTransLangMsg("닉네임을 정확하게 입력해주세요."), array("id"=>"nick_name"));
    if (mb_strlen($params['nick_name'])<2 || mb_strlen($params['nick_name'])>20) fnMsgJson(503, getTransLangMsg("닉네임은 2~20자 사이로 입력해주세요."), array("id"=>"nick_name"));

	$cls_member = new CLS_MEMBER;

    //사용자 정보 불러오기
    $user_view = $cls_member->user_view($MEM_USR['usr_idx']);
    if ($user_view == false) fnMsgJson(504, getTransLangMsg("사용자 데이터를 찾을 수 없습니다.\n고객센터로 문의해주세요."), "");

    //닉네임 중복검사
    if ($cls_member->is_nick_check($params['nick_name'], $user_view['nick_name'])) fnMsgJson(505, getTransLangMsg("이미 가입된 닉네임 입니다. 닉네임을 다시 입력해 주세요."), array("id"=>"join_nick"));

    $params['usr_idx'] = $user_view['usr_idx'];
    $params['upt_ip']  = NOW_IP;
    $params['upt_id']  = $MEM_USR['usr_idx'];

    //회원정보 저장
    if (!$cls_member->user_nick_name_save($params, $usr_idx)) fnMsgJson(506, getTransLangMsg("저장 처리중 문제가 발생했습니다.\n고객센터에 문의주세요."), "");

    //사용자 정보 세션
    $MEM_USR['nick_name'] = $params['nick_name'];
	setSession("user_view", $MEM_USR);
?>
{"result": 200, "message": "OK"}