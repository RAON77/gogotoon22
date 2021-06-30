<?
	require($_SERVER["DOCUMENT_ROOT"]."/module/lib/header.php");

	if (!chkReferer()) fnMsgJson(500, getTransLangMsg("잘못된 접근 입니다."), "");
	if (!isUser()) fnMsgJson(501, getTransLangMsg("로그인 회원만 이용 가능합니다."), "");

	$params['webtoon_idx'] = chkReqRpl("wt", null, "", "POST", "INT");
	$params['round_idx']   = chkReqRpl("ep", null, "", "POST", "INT");
	$params['idx']         = chkReqRpl("idx", null, "", "POST", "INT");
	$params['parent_idx']  = chkReqRpl("parent_idx", null, "", "POST", "INT");
	$params['comment']     = chkReqRpl("comment", "", "max", "POST", "STR");

    if (chkBlank($params['comment'])) fnMsgJson(502, getTransLangMsg("댓글을 입력해주세요."), array("id"=>"comment"));
    if (mb_strlen($params['comment'])<2 || mb_strlen($params['comment'])>200) fnMsgJson(503, getTransLangMsg("댓글은 2~200자 사이로 입력해주세요."), array("id"=>"comment"));

	$cls_wt = new CLS_WEBTOON;
    $cls_comment = new CLS_COMMENT;

    //웹툰 상세정보 불러오기
	$wt_view = $cls_wt->wt_view($params['webtoon_idx'], SITE_SAVE_LANG, 'Y');
	if ($wt_view == false) fnMsgJson(504, getTransLangMsg("일치하는 웹툰 데이터가 없습니다."), "");

    //웹툰 회차 상세정보 불러오기
	$round_view = $cls_wt->round_view($params['round_idx'], SITE_SAVE_LANG, 'Y');
	if ($round_view == false) fnMsgJson(505, getTransLangMsg("일치하는 회차 데이터가 없습니다."), "");

    //웹툰 댓글 쓰기 금지체크
    if ($cls_comment->comment_is_disallow($MEM_USR['usr_idx'], $disallow_date)) {
        if (chkBlank($disallow_date)) {
            $disallow_msg = getTransLangMsg("로그인 회원만 이용 가능합니다.");
        } else {
            $disallow_msg = getTransLangMsg("댓글 쓰기가 운영정책에 의거해 일시적으로 금지 되었습니다.\n기간: {disallow_date} 까지");
            $disallow_msg = str_replace("{disallow_date}", formatDates($disallow_date, "Y.m.d"), $disallow_msg);
        }

        fnMsgJson(506, $disallow_msg, "");
    }

    //웹툰 댓글 저장
    $params['usr_idx']  = $MEM_USR['usr_idx'];
    $params['wt_title'] = $wt_view['title'] .' | '. $round_view['title'];
    $params['upt_ip']   = NOW_IP;
    $params['upt_id']   = $MEM_USR['usr_idx'];
    $params['reg_ip']   = NOW_IP;
    $params['reg_id']   = $MEM_USR['usr_idx'];

    if (!$cls_comment->wt_comment_save($params, $point_save)) fnMsgJson(507, getTransLangMsg("저장 처리중 문제가 발생했습니다.\n고객센터에 문의주세요."), "");

?>
{"result": 200, "message": "OK", "point_save": "<?=$point_save?>"}