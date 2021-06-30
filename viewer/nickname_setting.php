<?include("../inc/config.php")?>
<?
	if (!chkReferer()) fnMsgGo(500, getTransLangMsg("잘못된 접근 입니다."), "/", "");
	if (!isUser()) fnMsgGo(501, getTransLangMsg("로그인 회원만 이용 가능합니다."), "/", "");

	$params['webtoon_idx'] = chkReqRpl("wt", null, "", "POST", "INT");
	$params['round_idx']   = chkReqRpl("ep", null, "", "POST", "INT");

	$cls_wt = new CLS_WEBTOON;

    //웹툰 상세정보 불러오기
	$wt_view = $cls_wt->wt_view($params['webtoon_idx'], SITE_SAVE_LANG, 'Y');
	if ($wt_view == false) fnMsgGo(502, getTransLangMsg("일치하는 웹툰 데이터가 없습니다."), "/", "");

    //웹툰 회차 상세정보 불러오기
	$round_view = $cls_wt->round_view($params['round_idx'], SITE_SAVE_LANG, 'Y');
	if ($round_view == false) fnMsgGo(503, getTransLangMsg("일치하는 회차 데이터가 없습니다."), "/", "");
?>
<section class="popup">
    <header class="p_head">
        <h2 class="tit"><span><?=getTransLangMsg("닉네임 설정")?></span></h2>
        <button type="button" class="btn_close b-close"><span><?=getTransLangMsg("닫기")?></span></button>
    </header>
    <div class="p_cont">
        <form name="popSaveFrm" id="popSaveFrm" method="post">
        <div class="box">
            <p><?=getTransLangMsg("회원님은 닉네임 설정되지 않았습니다.<br>댓글 작성을 위해 닉네임을 설정해 주세요.<br>※ 회원 정보 수정에서도 닉네임 설정이 가능합니다.")?></p>
        </div>
        <div class="inp_btn">
            <input type="text" name="nick_name" id="pop_nick_name" data-nick-check="N" class="inp_txt w100p" maxlength="20">
            <button type="button" class="btn-pk n gray" onclick="nicknameCheckGo()"><span><?=getTransLangMsg("중복체크")?></span></button>
        </div>
        </form>
    </div>
    <div class="p_botm">
        <button type="button" class="btn-pk n red rv" onclick="nicknameSaveGo()"><span><?=getTransLangMsg("저장")?></span></button>
    </div>
</section>

<script>
    //닉네임 저장
    function nicknameSaveGo() {
        if ($("#pop_nick_name").data("nick-check") != "Y") {
            alert("<?=getTransLangMsg("닉네임 중복체크를 해주세요.")?>");
            return false;
        }

        var nick_name = $.trim($("#pop_nick_name").val());
            nick_name = nick_name.replace(/\s/gi, "");

        $("#pop_nick_name").val(nick_name);
		AJ.ajaxForm($("#popSaveFrm"), "nickname_setting_proc.php", function(data) {
			if (data.result == 200) {
				alert("<?=getTransLangMsg("저장 처리가 완료되었습니다.")?>");

				location.reload();
			} else {
				alert(data.message);
				$("#"+data.id).focus();
			}
		});
    }

    //중복체크
    function nicknameCheckGo() {
        var nick_name = $.trim($("#pop_nick_name").val());
            nick_name = nick_name.replace(/\s/gi, "");

        $("#pop_nick_name").val(nick_name);
        $("#pop_nick_name").data("nick-check", "N");
		AJ.callAjax("/module/member/nickname_check_proc.php", {"nick_name": nick_name}, function(data){
			if (data.result == 200) {
                alert("<?=getTransLangMsg("사용이 가능한 닉네임 입니다.")?>");
                $("#pop_nick_name").data("nick-check", "Y");
            } else {
                alert(data.message);
            }
		});
    }
</script>