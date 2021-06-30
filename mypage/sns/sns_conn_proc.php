<?
	require($_SERVER["DOCUMENT_ROOT"]."/module/lib/header.php");

	if (!chkReferer()) fnMsgJson(500, getTransLangMsg("잘못된 접근 입니다."), "");
	if (!isUser()) fnMsgJson(501, getTransLangMsg("로그인 회원만 이용 가능합니다."), "");

	$params['gubun']     = chkReqRpl("sns_gubun", "", 50, "POST", "STR");
    $params['sns_name']  = getLoginSnsName($params['gubun']);
	$params['email']     = chkReqRpl("email", "", 50, "POST", "STR");
    $params['sns_id']    = chkReqRpl("uid", "N", 50, "POST", "STR");

	if (!isStrpos("google,facebook,apple", $params['sns_gubun'])) getTransLangMsg(502, getTransLangMsg("잘못된 요청 정보 입니다."), "");
	if (chkBlank($params['email'])) fnMsgJson(503, getTransLangMsg($params['sns_name'] ." 이메일 공유를 승인해주셔야 합니다."), "");
	if (chkBlank($params['sns_id'])) fnMsgJson(504, getTransLangMsg($params['sns_name']. " ID로 연결된 정보가 없습니다."), "");

	$cls_member = new CLS_MEMBER;

    //SNS 아이디 체크
    if ($cls_member->is_login_sns_check($params['sns_id'])) fnMsgJson(505, getTransLangMsg("다른 계정과 연결되어 있습니다."), "");

    //사용자 정보 불러오기
    $user_view = $cls_member->user_view($MEM_USR['usr_idx']);
    if ($user_view == false) fnMsgJson(506, getTransLangMsg($params['sns_name']." 로그인에 실패하였습니다."), "");


    $params['usr_idx'] = $MEM_USR['usr_idx'];
    if (!$cls_member->sns_conn_save($params)) fnMsgJson(507, getTransLangMsg("저장 처리중 문제가 발생했습니다.\n고객센터에 문의주세요."), "");

    //사용자 정보 세션
    $MEM_USR['sns_gubun'] = $params['gubun'];
    $MEM_USR['sns_date']  = date("Y-m-d H:i:s");
	setSession("user_view", $MEM_USR);
?>
{"result": 200, "message": "OK"}