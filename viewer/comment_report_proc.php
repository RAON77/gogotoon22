<?
	require($_SERVER["DOCUMENT_ROOT"]."/module/lib/header.php");

	if (!chkReferer()) fnMsgJson(500, getTransLangMsg("잘못된 접근 입니다."), "");
	if (!isUser()) fnMsgJson(501, getTransLangMsg("로그인 회원만 이용 가능합니다."), "");

	$params['webtoon_idx'] = chkReqRpl("wt", null, "", "POST", "INT");
	$params['round_idx']   = chkReqRpl("ep", null, "", "POST", "INT");
	$params['comment_idx'] = chkReqRpl("cmt_idx", null, "", "POST", "INT");
    $params['category']    = chkReqRpl("report_cate", "", 10, "POST", "STR");

    if (chkBlank($params['category'])) fnMsgJson(502, getTransLangMsg("요청정보 값이 유효하지 않습니다."), "");
    if (array_search($params['category'], array_column(getCommentReportCategoryList(SITE_SAVE_LANG), 'code')) === false) fnMsgJson(503, getTransLangMsg("요청정보 값이 유효하지 않습니다."), "");

	$cls_wt = new CLS_WEBTOON;
    $cls_comment = new CLS_COMMENT;

    //웹툰 상세정보 불러오기
	$wt_view = $cls_wt->wt_view($params['webtoon_idx'], SITE_SAVE_LANG, 'Y');
	if ($wt_view == false) fnMsgJson(504, getTransLangMsg("일치하는 웹툰 데이터가 없습니다."), "");

    //웹툰 회차 목록 불러오기
    $round_list = $cls_wt->round_list($wt_view['idx'], SITE_SAVE_LANG);

    //웹툰 회차 상세정보 불러오기
	$round_view = $cls_wt->round_view($params['round_idx'], SITE_SAVE_LANG, 'Y');
	if ($round_view == false) fnMsgJson(505, getTransLangMsg("일치하는 회차 데이터가 없습니다."), "");

    //웹툰 댓글 상세정보 불러오기
    $cmt_view = $cls_comment->wt_comment_view($params['comment_idx']);
    if ($cmt_view == false) fnMsgJson(506, getTransLangMsg("일치하는 댓글 데이터가 없습니다."), "");

    //신고 체크
    if ($cmt_view['open_flag']=='N') fnMsgJson(507, getTransLangMsg("이미 신고된 댓글 입니다."), "");

    //삭제 체크
    if ($cmt_view['del_flag']=='Y') fnMsgJson(508, getTransLangMsg("이미 삭제된 데이터 입니다."), "");

    //본일글 체크
    if ($cmt_view['reg_id'] == $MEM_USR['usr_idx']) fnMsgJson(509, getTransLangMsg("본인 댓글은 신고가 불가능합니다."), "");

    //웹툰 신고 저장
    $params['title']            = $wt_view['title'] .' | '. $round_view['title'];
    $params['comment']          = $cmt_view['comment'];
    $params['writer_usr_idx']   = $cmt_view['reg_id'];
    $params['writer_nick_name'] = $cmt_view['nick_name'];
    $params['upt_ip']           = NOW_IP;
    $params['upt_id']           = $MEM_USR['usr_idx'];
    $params['reg_ip']           = NOW_IP;
    $params['reg_id']           = $MEM_USR['usr_idx'];
    if (!$cls_comment->wt_comment_report_save($params, $error_msg)) fnMsgJson(509, iif($error_msg!='', $error_msg, getTransLangMsg("저장 처리중 문제가 발생했습니다.\n고객센터에 문의주세요.")), "");
?>
{"result": 200, "message": "OK", "point_save": "<?=$point_save?>"}