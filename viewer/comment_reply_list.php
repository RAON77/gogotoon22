<?include("../inc/config.php")?>
<?
	if (!chkReferer()) fnMsgGo(500, getTransLangMsg("잘못된 접근 입니다."), "/", "");

	$params['webtoon_idx'] = chkReqRpl("wt", null, "", "", "INT");
	$params['round_idx']   = chkReqRpl("ep", null, "", "", "INT");
    $params['page']        = 1;
	$params['list_size']   = 999999;
	$params['sch_parent']  = chkReqRpl("cmt", null, "", "", "INT");

	$cls_wt = new CLS_WEBTOON;
	$cls_comment = new CLS_COMMENT;

    //웹툰 상세정보 불러오기
	$wt_view = $cls_wt->wt_view($params['webtoon_idx'], SITE_SAVE_LANG, 'Y');
	if ($wt_view == false) fnMsgGo(502, getTransLangMsg("일치하는 웹툰 데이터가 없습니다."), "/", "");

    //웹툰 회차 상세정보 불러오기
	$round_view = $cls_wt->round_view($params['round_idx'], SITE_SAVE_LANG, 'Y');
	if ($round_view == false) fnMsgGo(503, getTransLangMsg("일치하는 회차 데이터가 없습니다."), "/", "");

    //웹툰 댓글 상세정보 불러오기
    $cmt_view = $cls_comment->wt_comment_view($params['sch_parent']);
    if ($cmt_view == false) fnMsgGo(504, getTransLangMsg("일치하는 댓글 데이터가 없습니다."), "/", "");

    //답변 댓글 목록 불러오기
    $comment_list = $cls_comment->wt_comment_list($params);
?>
<button type="button" class="b_cbotm" onclick="replyBtn(this)">
    <span><?=getTransLangMsg("답글")?> <?=formatNumbers(count($comment_list))?></span>
</button>
<ul>
    <?for ($i=0; $i<count($comment_list); $i++) {?>
        <li>
            <div class="col">
                <div class="h">
                    <p class="h1">
                        <strong class="nickname">
                            <?
                                if ($comment_list[$i]['open_flag']=='N' || $comment_list[$i]['del_flag']=='Y') {
                                    echo "-";
                                } else {
                                    echo $comment_list[$i]['nick_name'];
                                }
                            ?>
                        </strong>
                        <span><?=formatDates($comment_list[$i]['reg_date'], 'Y.m.d H:i:s')?></span>

                        <?if ($comment_list[$i]['open_flag']=='Y' && $comment_list[$i]['del_flag']=='N') {?>
                            <?if ($MEM_USR['usr_idx']!=$comment_list[$i]['reg_id']) {?>
                                <a href="javascript:;" onclick="commentReportPopup(<?=$comment_list[$i]['idx']?>, <?=$params['sch_parent']?>)"><?=getTransLangMsg("신고")?></a>
                            <?}?>

                            <?if ($MEM_USR['usr_idx']==$comment_list[$i]['reg_id']) {?>
                                <a href="javascript:;" onclick="commentDeleteGo(this, <?=$comment_list[$i]['idx']?>, <?=$params['sch_parent']?>)"><?=getTransLangMsg("삭제")?></a>
                            <?}?>
                        <?}?>
                    </p>
                    <p class="t1">
                        <?
                            if ($comment_list[$i]['del_flag']=='Y') {
                                echo getTransLangMsg("댓글이 삭제 되었습니다.");
                            } elseif ($comment_list[$i]['open_flag']=='N') {
                                echo getTransLangMsg("신고로 인해 댓글이 삭제 되었습니다.");
                            } else {
                                echo textareaDecode($comment_list[$i]['comment']);
                            }
                        ?>
                    </p>
                    <div class="btn">
                        <?
                            //웹툰 댓글 좋아요/싫어요 참여 여부
                            $is_like = false;
                            $is_dislike = false;
                            if (isUser()) {
                                $cls_comment->wt_commend_is_check($comment_list[$i]['idx'], $MEM_USR['usr_idx'], $is_like, $is_dislike);
                            }
                        ?>
                        <button type="button"><span class="i-aft i_best1 <?=iif($is_like, 'on', '')?>" onclick="commendSaveGo(this, <?=$comment_list[$i]['idx']?>, '', '10')">
                            <?=formatNumbers($comment_list[$i]['total_like_cnt'])?></span>
                        </button>
                        <button type="button"><span class="i-aft i_best2 <?=iif($is_dislike, 'on', '')?>" onclick="commendSaveGo(this, <?=$comment_list[$i]['idx']?>, '', '20')">
                            <?=formatNumbers($comment_list[$i]['total_bad_cnt'])?></span>
                        </button>
                    </div>
                </div>
            </div>
        </li>
    <?}?>
    <li>
        <div class="btn_comm">
            <?if (chkBlank($MEM_USR['usr_idx'])) {?>
                <p class="non" onclick="popupNonLogin()"><?=getTransLangMsg("<span class=\"c-red\">로그인</span> 한 회원만 댓글 작성이 가능합니다.")?></p>
                <textarea id="comment<?=$params['sch_parent']?>" class="textarea1" onclick="popupNonLogin()" maxlength="200"></textarea>
                <button type="button" class="btn-pk b red rv" onclick="popupNonLogin()"><span><?=getTransLangMsg("등록")?></span></button>
            <?} else if (chkBlank($MEM_USR['nick_name'])) {?>
                <p class="non" onclick="nicknameSetPopup()"><?=getTransLangMsg("회원님은 <span class=\"c-red\">닉네임</span> 설정되지 않았습니다.")?></p>
                <textarea id="comment<?=$params['sch_parent']?>" class="textarea1" onclick="nicknameSetPopup()" maxlength="200"></textarea>
                <button type="button" class="btn-pk b red rv" onclick="nicknameSetPopup()"><span><?=getTransLangMsg("등록")?></span></button>
            <?} else {?>
                <textarea id="comment<?=$params['sch_parent']?>" class="textarea1" maxlength="200"></textarea>
                <button type="button" class="btn-pk b red rv" onclick="commentSaveGo(<?=$params['sch_parent']?>)"><span><?=getTransLangMsg("등록")?></span></button>
            <?}?>
        </div>
    </li>
</ul>