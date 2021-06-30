<?include("../inc/config.php")?>
<?
    setSession("RETURN_URL", "/mypage/mypage_info.php");

    if (!chkReferer()) fnMsgGo(500, getTransLangMsg("잘못된 접근 입니다."), "../member/logout.php", "");
	if (!isUser()) fnMsgGo(501, "", "/member/login.php?return_flag=Y", "");
?>
<section class="popup">
    <header class="p_head">
        <h2 class="tit"><span><?=getTransLangMsg("이메일 계정 변경")?></span></h2>
        <button type="button" class="btn_close b-close"><span><?=getTransLangMsg("닫기")?></span></button>
    </header>
    <div class="p_cont">
        <div class="box_info ta-c mb10">
            <p class="t1 c-red"><?=getTransLangMsg("이메일 계정 변경시 최초 1회만 가능합니다.<br>변경 후 취소는 불가능합니다.")?></p>
        </div>

        <form name="popEmailChangeFrm" id="popEmailChangeFrm" method="post">
        <input type="text" name="new_email" id="pop_new_email" class="inp_txt w100p" maxlength="50" placeholder="<?=getTransLangMsg("이메일 아이디를 입력해주세요.")?>">
        <input type="password" name="curr_passwd" id="pop_curr_passwd" class="inp_txt w100p" maxlength="20" placeholder="<?=getTransLangMsg("현재 비밀번호")?>">
        </form>
    </div>
    <div class="p_botm">
        <button type="button" class="btn-pk n red rv" onclick="emailAuthSendGo()"><span><?=getTransLangMsg("인증메일 발송")?></span></button>
    </div>
</section>
<script>
    	//이메일 인증 발송
	function emailAuthSendGo() {
		AJ.ajaxForm($("#popEmailChangeFrm"), "popup_email_change_proc.php", function(data) {
			if (data.result == 200) {
                emailAuthPopup(data.token);
			} else {
				alert(data.message);
                $("#"+data.id).focus();
			}
		});
    }
</script>