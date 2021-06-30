<?
	require($_SERVER["DOCUMENT_ROOT"]."/module/lib/header.php");

	if (!chkReferer()) fnMsgJson(500, getTransLangMsg("잘못된 접근 입니다."), "");
	if (!isUser()) fnMsgJson(501, getTransLangMsg("로그인 회원만 이용 가능합니다."), "");

	$params['gubun'] = chkReqRpl("gubun", "", 50, "POST", "STR");

	if (!isStrpos("google,facebook,apple,wechat", $params['gubun'])) getTransLangMsg(502, getTransLangMsg("잘못된 요청 정보 입니다."), "");

    $cls_member = new CLS_MEMBER;

    $params['usr_idx'] = $MEM_USR['usr_idx'];
    $params['sns_id']  = '';
    if (!$cls_member->sns_conn_save($params)) fnMsgJson(503, getTransLangMsg("저장 처리중 문제가 발생했습니다.\n고객센터에 문의주세요."), "");
?>
{"result": 200, "message": "OK"}