<?include("../inc/config.php")?>
<?
	if (!chkReferer()) fnMsgGo(500, "잘못된 접근 입니다.", "/", "");

	$params['webtoon_idx'] = chkReqRpl("wt", null, "", "", "INT");
	$params['round_idx']   = chkReqRpl("ep", null, "", "", "INT");
	$params['page']        = chkReqRpl("page", 1, "", "", "INT");
	$params['list_size']   = 20;
	$params['block_size']  = 10;
	$params['sch_ordby']   = chkReqRpl("ordby", 1, "", "", "INT");

	$cls_wt = new CLS_WEBTOON;
    $cls_comment = new CLS_COMMENT;

    //웹툰 상세정보 불러오기
	$wt_view = $cls_wt->wt_view($params['webtoon_idx'], SITE_SAVE_LANG, 'Y');
	if ($wt_view == false) fnMsgGo(501, getTransLangMsg("일치하는 웹툰 데이터가 없습니다."), "/", "");

    //웹툰 회차 상세정보 불러오기
	$round_view = $cls_wt->round_view($params['round_idx'], SITE_SAVE_LANG, 'Y');
	if ($round_view == false) fnMsgGo(502, getTransLangMsg("일치하는 회차 데이터가 없습니다."), "/", "");

    //베스트 댓글 목록 불러오기
    $commen_best_list = $cls_comment->wt_comment_best_list($params);

    //댓글 목록 불러오기
    $params['comment_best'] = implode(',', array_column($commen_best_list, 'idx'));
    $comment_list = $cls_comment->wt_comment_list($params, $total_cnt, $total_page);
?>

<div class="inr-c">
    <div class="comm1">
        <p class="h1"><?=getTransLangMsg("댓글")?> <span class="c-red"><?=formatNumbers($total_cnt)?></span></p>
        <div class="btn_comm">
            <form name="commentSaveFrm" id="commentSaveFrm" method="post">
            <input type="hidden" name="wt" value="<?=$wt_view['idx']?>" />
            <input type="hidden" name="ep" value="<?=$round_view['idx']?>" />
            <?if (chkBlank($MEM_USR['usr_idx'])) {?>
                <p class="non" onclick="popupNonLogin()"><?=getTransLangMsg("<span class=\"c-red\">로그인</span> 한 회원만 댓글 작성이 가능합니다.")?></p>
                <textarea name="comment" id="comment" class="textarea1" onclick="popupNonLogin()" maxlength="200"></textarea>
                <button type="button" class="btn-pk b red rv" onclick="popupNonLogin()"><span><?=getTransLangMsg("등록")?></span></button>
            <?} else if (chkBlank($MEM_USR['nick_name'])) {?>
                <p class="non" onclick="nicknameSetPopup()"><?=getTransLangMsg("회원님은 <span class=\"c-red\">닉네임</span> 설정되지 않았습니다.")?></p>
                <textarea name="comment" id="comment" class="textarea1" onclick="nicknameSetPopup()" maxlength="200"></textarea>
                <button type="button" class="btn-pk b red rv" onclick="nicknameSetPopup()"><span><?=getTransLangMsg("등록")?></span></button>
            <?} else {?>
                <textarea name="comment" id="comment" class="textarea1" maxlength="200"></textarea>
                <button type="button" class="btn-pk b red rv" onclick="commentSaveGo('')"><span><?=getTransLangMsg("등록")?></span></button>
            <?}?>
            </form>
        </div>
        <p class="ta-r"><span class="total-word">0</span>/200</p>
    </div>

    <div class="tab ty5">
        <ul>
            <li <?if ($params['sch_ordby']=='1') {?>class="on"<?}?>><a href="javascript:;" onclick="commentListGo(1,1)"><?=getTransLangMsg("최신순")?></a></li>
            <li <?if ($params['sch_ordby']=='2') {?>class="on"<?}?>><a href="javascript:;" onclick="commentListGo(1,2)"><?=getTransLangMsg("추천순")?></a></li>
            <li <?if ($params['sch_ordby']=='3') {?>class="on"<?}?>><a href="javascript:;" onclick="commentListGo(1,3)"><?=getTransLangMsg("댓글순")?></a></li>
        </ul>
    </div>

    <!-- 댓글 : S -->
    <div class="comment-box">
        <div class="lst_comm">
            <?if ((count($comment_list) + count($commen_best_list)) > 0) {?>
                <ul>
                    <?for ($i=0; $i<count($commen_best_list); $i++) {?>
                        <li>
                            <div class="col">
                                <div class="h">
                                    <p class="h1">
                                        <strong class="nickname">
                                            <?
                                                if ($commen_best_list[$i]['open_flag']=='N' || $commen_best_list[$i]['del_flag']=='Y') {
                                                    echo "-";
                                                } else {
                                                    echo $commen_best_list[$i]['nick_name'];
                                                }
                                            ?>
                                        </strong>
                                        <span><?=formatDates($commen_best_list[$i]['reg_date'], 'Y.m.d H:i:s')?></span>

                                        <?if ($commen_best_list[$i]['open_flag']=='Y' && $commen_best_list[$i]['del_flag']=='N') {?>
                                            <?if ($MEM_USR['usr_idx']!=$commen_best_list[$i]['reg_id']) {?>
                                                <a href="javascript:;" onclick="commentReportPopup(<?=$commen_best_list[$i]['idx']?>, '')"><?=getTransLangMsg("신고")?></a>
                                            <?}?>

                                            <?if ($MEM_USR['usr_idx']==$commen_best_list[$i]['reg_id']) {?>
                                                <a href="javascript:;" data-best="Y" onclick="commentDeleteGo(this, <?=$commen_best_list[$i]['idx']?>, '')"><?=getTransLangMsg("삭제")?></a>
                                            <?}?>
                                        <?}?>
                                    </p>
                                    <p class="t1">
                                        <span class="i-set i_best"><?=getTransLangMsg("베스트")?></span>
                                        <?
                                            if ($commen_best_list[$i]['del_flag']=='Y') {
                                                echo getTransLangMsg("댓글이 삭제 되었습니다.");
                                            } elseif ($commen_best_list[$i]['open_flag']=='N') {
                                                echo getTransLangMsg("신고로 인해 댓글이 삭제 되었습니다.");
                                            } else {
                                                echo textareaDecode($commen_best_list[$i]['comment']);
                                            }
                                        ?>
                                    </p>
                                    <div class="btn">
                                        <?
                                            //웹툰 댓글 좋아요/싫어요 참여 여부
                                            $is_like = false;
                                            $is_dislike = false;
                                            if (isUser()) {
                                                $cls_comment->wt_commend_is_check($commen_best_list[$i]['idx'], $MEM_USR['usr_idx'], $is_like, $is_dislike);
                                            }
                                        ?>
                                        <button type="button"><span class="i-aft i_best1 <?=iif($is_like, 'on', '')?>" onclick="commendSaveGo(this, <?=$commen_best_list[$i]['idx']?>, '', '10')">
                                            <?=formatNumbers($commen_best_list[$i]['total_like_cnt'])?></span>
                                        </button>
                                        <button type="button"><span class="i-aft i_best2 <?=iif($is_dislike, 'on', '')?>" onclick="commendSaveGo(this, <?=$commen_best_list[$i]['idx']?>, '', '20')">
                                            <?=formatNumbers($commen_best_list[$i]['total_bad_cnt'])?></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="botm reply-box" data-cmt="<?=$commen_best_list[$i]['idx']?>">
                                    <button type="button" class="b_cbotm" onclick="replyBtn(this)">
                                        <span><?=getTransLangMsg("답글")?> <?=formatNumbers($commen_best_list[$i]['total_reply_cnt'])?></span>
                                    </button>
                                </div>
                            </div>
                        </li>
                    <?}?>

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
                                                <a href="javascript:;" onclick="commentReportPopup(<?=$comment_list[$i]['idx']?>, '')"><?=getTransLangMsg("신고")?></a>
                                            <?}?>

                                            <?if ($MEM_USR['usr_idx']==$comment_list[$i]['reg_id']) {?>
                                                <a href="javascript:;" onclick="commentDeleteGo(this, <?=$comment_list[$i]['idx']?>, '')"><?=getTransLangMsg("삭제")?></a>
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
                                <div class="botm reply-box" data-cmt="<?=$comment_list[$i]['idx']?>">
                                    <button type="button" class="b_cbotm" onclick="replyBtn(this)">
                                        <span><?=getTransLangMsg("답글")?> <?=formatNumbers($comment_list[$i]['total_reply_cnt'])?></span>
                                    </button>
                                </div>
                            </div>
                        </li>
                    <?}?>
                </ul>
            <?} else {?>
                <div class="ta-c mt50"><?=getTransLangMsg("등록된 댓글이 없습니다.")?></div>
            <?}?>
        </div>

        <div class="pagenation">
            <? frontScriptPaging($total_page, $params['block_size'], $params['page'], "commentListGo({page}, ". $params['sch_ordby'] .")") ?>
        </div>
    </div>
    <!-- 댓글 : E -->
</div>

<script>
    $(function(){
        $("#comment").keyup(function(){
			var max_length = 200;
			var this_length = this.value.length;

			if (this_length >= max_length) {
				this.value = this.value.left(max_length);
			}
			$(".total-word").html( this.value.length.addComma() );
        })
    })

    //닉네임 설정
    function nicknameSetPopup() {
		AJ.callAjax("nickname_setting.php", {"wt": "<?=$params['webtoon_idx']?>", "ep": "<?=$params['round_idx']?>"}, function(data){
			$("#popNick1").html(data);
			openLayerPopup('popNick1');
		}, "html");
    }

    //댓글 등록
    function commentSaveGo(cmt_idx) {
        var params = {
                "wt": "<?=$params['webtoon_idx']?>",
                "ep": "<?=$params['round_idx']?>",
                "parent_idx": cmt_idx,
                "comment": $("#comment"+cmt_idx).val()
            }

        AJ.callAjax("comment_save_proc.php", params, function(data){
			if (data.result == 200) {
                if (data.point_save == 'Y') {
                    popupPointActiveComplete();
                }

                if (cmt_idx == "") {
                    commentListGo(1, <?=$params['sch_ordby']?>);
                } else {
                    replyListGo(cmt_idx, 'reg');
                }

			} else {
				alert(data.message);
                $("#comment"+cmt_idx).focus();
			}
        });
    }

    //답변 확인
    function replyBtn(obj) {
        var $target = $(obj).closest(".reply-box");
        var $btn    = $target.find(".b_cbotm");

        if ($btn.next('ul').length > 0) {
            if ($btn.next('ul').is(":visible")) {
                $btn.next('ul').slideUp('fast');
                return false;
            }
        }

        replyListGo($target.data("cmt"), 'view');
    }

	//댓글 답변 목록
	function replyListGo(cmt, mode) {
        var $target = $(".reply-box").filter("[data-cmt="+ cmt +"]");

		$target.load("comment_reply_list.php?wt=<?=$params['webtoon_idx']?>&ep=<?=$params['round_idx']?>&cmt="+ cmt, function(){
            var $btn = $target.find(".b_cbotm");

            if (mode=='view') {
                $btn.next('ul').slideDown('fast');
            } else {
                $btn.next('ul').show();
            }
        });
	}

    //댓글 삭제
    function commentDeleteGo(obj, cmt_idx, parent_idx) {
        if (!confirm("<?=getTransLangMsg("댓글을 삭제하시겠습니까?")?>")) return false;

		AJ.callAjax("comment_delete_proc.php", {"wt": "<?=$params['webtoon_idx']?>", "ep": "<?=$params['round_idx']?>", "cmt_idx": cmt_idx, "parent_idx": parent_idx}, function(data){
			if (data.result == 200) {
                if ($(obj).data("best") == "Y") {
                    commentListGo(1, <?=$params['sch_ordby']?>);
                } else {
                    $(obj).closest(".col").find("strong").text("-");
                    $(obj).closest(".col").find(".t1").text("<?=getTransLangMsg("댓글이 삭제 되었습니다.")?>");
                    $(obj).remove();
                }
            } else {
                alert(data.message);
            }
		});
    }

    //댓글 신고 팝업
    function commentReportPopup(cmt_idx, parent_idx) {
		<?if (!isUser()) {?>
			popupNonLogin();
		<?} else {?>
			AJ.callAjax("comment_report.php", {"wt": "<?=$params['webtoon_idx']?>", "ep": "<?=$params['round_idx']?>", "cmt_idx": cmt_idx, "parent_idx": parent_idx}, function(data){
				$("#popReport").html(data);
				openLayerPopup('popReport');
			}, "html");
		<?}?>
    }

    //댓글 신고 저장
    function reportSaveGo(cmt_idx, parent_idx) {
        if ($(":radio[name=report_cate]:checked").length == 0) {
            alert("<?=getTransLangMsg("항목을 선택해주세요.")?>");
            return false;
        }

        if (!confirm("<?=getTransLangMsg("선택한 항목을 저장 하시겠습니까?")?>")) return false;

		AJ.callAjax("comment_report_proc.php", {"wt": "<?=$params['webtoon_idx']?>", "ep": "<?=$params['round_idx']?>", "cmt_idx": cmt_idx, "report_cate": $(":radio[name=report_cate]:checked").val()}, function(data){
			if (data.result == 200) {
                alert("신고가 접수 되었습니다.\n신고해주신 내용은 운영정책에 의거해 처리됩니다.");

                if (parent_idx == '') {
                    commentListGo(1, <?=$params['sch_ordby']?>);
                } else {
                    replyListGo(parent_idx, 'report');
                }

                $("#popReport").find(".b-close, .popup_dim").trigger("click");
            } else {
                alert(data.message);
            }
		});
    }

    //댓글 추천,비부천 저장
    function commendSaveGo(obj, cmt_idx, parent_idx, check_type) {
        var params = {
                "wt": "<?=$params['webtoon_idx']?>",
                "ep": "<?=$params['round_idx']?>",
                "cmt_idx": cmt_idx,
                "parent_idx": parent_idx,
                "check_type": check_type
            }

        AJ.callAjax("comment_commend_proc.php", params, function(data){
			if (data.result == 200) {
                if (data.point_save == 'Y') {
                    popupPointActiveComplete();
                }

                var this_val = parseInt($(obj).text());
                if ($(obj).hasClass("on")) {
                    $(obj).removeClass("on").text( this_val-=1 );
                } else {
                    $(obj).addClass("on").text( this_val+=1 );
                }
			} else {
				alert(data.message);
                $("#comment"+cmt_idx).focus();
			}
        });
    }
</script>