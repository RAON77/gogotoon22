<?
	require($_SERVER["DOCUMENT_ROOT"]."/module/lib/header.php");

	if (!chkReferer()) fnMsgJson(500, getTransLangMsg("잘못된 접근 입니다."), "");
	if (!isUser()) fnMsgJson(501, getTransLangMsg("로그인 회원만 이용 가능합니다."), "");

	$params['out_reason'] = chkReqRpl("out_reason", "", 10, "POST", "STR");

	$cls_member = new CLS_MEMBER;

    //사용자 정보 불러오기
    $user_view = $cls_member->user_view($MEM_USR['usr_idx']);
    if ($user_view == false) fnMsgJson(510, getTransLangMsg("사용자 데이터를 찾을 수 없습니다.\n고객센터로 문의해주세요."), "");


    //회원탈퇴 요청 처리
    $params['usr_idx'] = $MEM_USR['usr_idx'];
    $params['upt_ip']  = NOW_IP;
    $params['upt_id']  = $MEM_USR['usr_idx'];
    if (!$cls_member->out_request_save($params)) fnMsgJson(512, getTransLangMsg("저장 처리중 문제가 발생했습니다.\n고객센터에 문의주세요."), "");
?>
{"result": 200, "message": "OK"}