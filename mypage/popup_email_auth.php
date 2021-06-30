<?include("../inc/config.php")?>
<?
    setSession("RETURN_URL", "/mypage/mypage_info.php");

    if (!chkReferer()) fnMsgGo(500, getTransLangMsg("잘못된 접근 입니다."), "../member/logout.php", "");
	if (!isUser()) fnMsgGo(501, "", "/member/login.php?return_flag=Y", "");

    $token = chkReqRpl("token", "", "max", "", "STR");

    if (chkBlank($token)) fnMsgGo(502, getTransLangMsg("요청정보 값이 유효하지 않습니다."), "RELOAD", "");

    $cls_jwt = new CLS_JWT;

	$token_data = $cls_jwt->dehashing($token, $error_msg);
	if ($token_data == false) fnMsgGo(503, getTransLangMsg("일치하는 요청정보 데이터가 없습니다."), "RELOAD", "");

    $email = $token_data['usr_email'];
?>
<section class="popup">
    <header class="p_head">
        <h2 class="tit"><span><?=getTransLangMsg("이메일 인증")?></span></h2>
        <button type="button" class="btn_close b-close"><span><?=getTransLangMsg("닫기")?></span></button>
    </header>
    <div class="p_cont">
        <form name="popEmailAuthFrm" id="popEmailAuthFrm" method="post">
        <input type="hidden" name="token" value="<?=$token?>" />
        <div class="box_info ta-c">
            <p class="t1">
                <?=str_replace("{{email}}", $email, getTransLangMsg("{{email}} 으로 인증번호가 발송되었습니다."))?>
                <?=getTransLangMsg("아래에 인증번호를 입력해 주세요.")?>
            </p>
        </div>
        <input type="number" name="auth_num" id="pop_auth_num" class="inp_txt w100p onlyNum" maxlength="6" placeholder="<?=getTransLangMsg("인증번호 6자리")?>">
        <p class="fz-s1 c-red"><?=getTransLangMsg("메일이 도착하지 않는다면, 스팸메일함을 확인해 주십시오.")?></p>
        </form>
    </div>
    <div class="p_botm">
        <button type="button" class="btn-pk n red rv" onclick="emailAuthNumGo()"><span><?=getTransLangMsg("확인")?></span></button>
    </div>
</section>
<script>
    //이메일 인증번호 확인
    function emailAuthNumGo() {
		AJ.ajaxForm($("#popEmailAuthFrm"), "popup_email_auth_proc.php", function(data) {
			if (data.result == 200) {
				location.reload();
			} else {
				alert(data.message);
                $("#"+data.id).focus();
			}
		});
    }
</script>