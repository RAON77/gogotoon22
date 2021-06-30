<?
	require($_SERVER["DOCUMENT_ROOT"]."/module/lib/header.php");

	if (!chkReferer()) fnMsgJson(500, getTransLangMsg("잘못된 접근 입니다."), "");
	if (!isUser()) fnMsgJson(501, getTransLangMsg("로그인 회원만 이용 가능합니다."), "");

	$params['webtoon_idx'] = chkReqRpl("chk_wt", null, "", "POST", "INT");

    if (chkBlank($params['webtoon_idx'])) fnMsgJson(502, getTransLangMsg("요청정보 값이 유효하지 않습니다."), "");

    $cls_wt = new CLS_WEBTOON;

    //웹툰 좋아요 체크 삭제
    $params['usr_idx'] = $MEM_USR['usr_idx'];
    if ($cls_wt->favorit_delete($params, $error_msg) == false) fnMsgJson(503, iif($error_msg!='', $error_msg, getTransLangMsg("삭제 처리중 문제가 발생했습니다.\n고객센터에 문의주세요.")), "");
?>
{"result": 200, "message": "OK"}