<?include("../inc/config.php")?>
<?
    setSession("RETURN_URL", "/mypage/mypage_info.php");

    if (!chkReferer()) fnMsgGo(500, getTransLangMsg("잘못된 접근 입니다."), "../member/logout.php", "");
	if (!isUser()) fnMsgGo(501, "", "/member/login.php?return_flag=Y", "");

    $params['gubun'] = chkReqRpl("gubun", "", 10, "", "STR");

    if (chkBlank($params['gubun']) || !isStrpos("init,change",$params['gubun'])) fnMsgGo(502, getTransLangMsg("요청정보 값이 유효하지 않습니다."), "RELOAD", "");
?>
<section class="popup">
    <header class="p_head">
        <h2 class="tit"><span><?=getTransLangMsg("비밀번호 설정")?></span></h2>
        <button type="button" class="btn_close b-close"><span><?=getTransLangMsg("닫기")?></span></button>
    </header>
    <div class="p_cont">
        <div class="box_info ta-c mb10">
            <p class="t1 c-red"><?=getTransLangMsg("비밀번호는 8~20자 사이로 입력해주세요.")?></p>
        </div>

        <form name="popPasswdChangeFrm" id="popPasswdChangeFrm" method="post">
        <input type="hidden" name="gubun" value="<?=$params['gubun']?>" />
        <?if ($params['gubun'] == "change") {?>
            <input type="password" name="curr_passwd" id="pop_curr_passwd" class="inp_txt w100p" maxlength="20" placeholder="<?=getTransLangMsg("현재 비밀번호")?>">
        <?}?>
        <input type="password" name="new_passwd" id="pop_new_passwd" class="inp_txt w100p" maxlength="20" placeholder="<?=getTransLangMsg("새로운 비밀번호")?>">
        <input type="password" name="chk_passwd" id="pop_chk_passwd" class="inp_txt w100p" maxlength="20" placeholder="<?=getTransLangMsg("새로운 비밀번호 재입력")?>">
        </form>
    </div>
    <div class="p_botm">
        <button type="button" class="btn-pk n red rv" onclick="changePasswdGo()"><span><?=getTransLangMsg("저장")?></span></button>
    </div>
</section>
<script>
    function changePasswdGo() {
		AJ.ajaxForm($("#popPasswdChangeFrm"), "popup_passwd_change_proc.php", function(data) {
			if (data.result == 200) {
				location.reload();
			} else {
				alert(data.message);
                $("#"+data.id).focus();
			}
		});
    }
</script>