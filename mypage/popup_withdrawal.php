<?include("../inc/config.php")?>
<?
    setSession("RETURN_URL", "/mypage/mypage_info.php");

    if (!chkReferer()) fnMsgGo(500, getTransLangMsg("잘못된 접근 입니다."), "../member/logout.php", "");
	if (!isUser()) fnMsgGo(501, "", "/member/login.php?return_flag=Y", "");

    $reason_list = getMemberOutCategoryList(SITE_SAVE_LANG);
?>
<section class="popup">
    <header class="p_head">
        <h2 class="tit"><span><?=getTransLangMsg("회원탈퇴 신청")?></span></h2>
        <button type="button" class="btn_close b-close"><span><?=getTransLangMsg("닫기")?></span></button>
    </header>
    <div class="p_cont">
        <form name="popWithdrawalFrm" id="popWithdrawalFrm" method="post">
        <div class="pr-mb2">
            <?for ($i=0; $i<count($reason_list); $i++) {?>
                <div class="mb">
                    <label class="inp_radio"><input type="radio" name="out_reason" id="pop_out_reason" value="<?=$reason_list[$i]['code']?>" class="out_reason"><span><?=$reason_list[$i]['name']?></span></label>
                </div>
            <?}?>
        </div>
        </form>
        <div class="hd_titbox1">
            <h2 class="title3"><strong><?=getTransLangMsg("탈퇴 주의사항")?></strong></h2>
        </div>
        <div class="box_info mb20">
            <ul class="lst_dot1">
                <li><?=getTransLangMsg("서비스 이용 중 등록하신 댓글, 별점은 유지됩니다.")?></li>
                <li><?=getTransLangMsg("탈퇴 이후에는 보유하신 G캐시, POINT, 대여/소장했던 콘텐츠 이용이 불가능하며, 복원 및 환불되지 않습니다.")?></li>
                <li><?=getTransLangMsg("사용자 정보는 개인정보처리방침 의거하여 탈퇴 처리 됩니다.")?></li>
            </ul>
        </div>
        <label class="inp_checkbox"><input type="checkbox" id="pop_agree"><span><?=getTransLangMsg("탈퇴 주의사항을 확인했으며, 탈퇴에 동의합니다.")?></span></label>
    </div>
    <div class="p_botm">
        <button type="button" class="btn-pk n red rv" onclick="withdrawalSaveGo()"><span><?=getTransLangMsg("탈퇴 신청하기")?></span></button>
    </div>
</section>
<script>
    function withdrawalSaveGo() {
        if ($(".out_reason").filter(":checked").length == 0) {
            alert("<?=getTransLangMsg("항목을 선택해주세요.")?>");
            return false;
        }

        if (!$("#pop_agree").is(":checked")) {
            alert("<?=getTransLangMsg("탈퇴 주의사항 확인에 동의해주세요.")?>");
            return false;
        }

        if (!confirm("<?=getTransLangMsg("탈퇴 주의사항은 확인하셨습니까?\\n탈퇴를 원하실 경우 '확인'을 눌러주세요.")?>")) return false;

		AJ.ajaxForm($("#popWithdrawalFrm"), "popup_withdrawal_proc.php", function(data) {
			if (data.result == 200) {
                alert("<?=getTransLangMsg("탈퇴 신청이 완료 되었습니다.\\n고고툰 서비스를 이용해주셔서 감사합니다.")?>");

                location.replace("/member/logout.php?tp=btn");
			} else {
				alert(data.message);
			}
		});
    }
</script>