<?
	require($_SERVER["DOCUMENT_ROOT"]."/module/lib/header.php");

	if (!chkReferer()) fnMsgJson(500, getTransLangMsg("잘못된 접근 입니다."), "");
	if (!isUser()) fnMsgJson(501, getTransLangMsg("로그인 회원만 이용 가능합니다."), "");

    $params['trans_up_file']   = $_FILES['up_file'];
    $params['fdel_flag']       = chkReqRpl("fdel_flag", "N", 1, "POST", "STR");
    $params['trans_lang']      = implode(",", chkReqRpl("service_lang", "", 100, "POST", "STR"));
    $params['trans_paypal_id'] = chkReqRpl("paypal_id", "", 50, "POST", "STR");
    $up_file_path              = "/upload/member/profile/";


    //if (chkBlank($params['trans_up_file'])) fnMsgJson(502, getTransLangMsg("프로필사진 값이 유효하지 않습니다."), "");
	if (chkBlank($params['trans_lang'])) fnMsgJson(503, getTransLangMsg("언어설정 값이 유효하지 않습니다."), "");
	if (chkBlank($params['trans_paypal_id'])) fnMsgJson(504, getTransLangMsg("페이팔 ID 값이 유효하지 않습니다."), "");

	$cls_member = new CLS_MEMBER;

    //사용자 정보 불러오기
    $user_view = $cls_member->user_view($MEM_USR['usr_idx']);
    if ($user_view == false) fnMsgJson(505, getTransLangMsg("사용자 데이터를 찾을 수 없습니다.\n고객센터로 문의해주세요."), "");

    //닉넥임 체크
    if (chkBlank($user_view['nick_name'])) fnMsgJson(506, getTransLangMsg("닉네임이 설정되지 않았습니다.\n닉네임을 설정해주세요."), "");

    //승인 대기 체크
    if ($user_view['trans_status']=='1') fnMsgJson(507, getTransLangMsg("승인 대기중 입니다."), "");

    //이전 데이터 불러오기
    $params['old_trans_up_file'] = $user_view['trans_up_file'];

    //프로필 사진 저장
    $upfile_change = false;
    if (!chkBlank($params['trans_up_file'])) {
        $fuArray                 = fileUpload("up_file", $up_file_path, 2, "IMG", "N");
        $params['trans_up_file'] = $fuArray[0]["file_info"];
        $upfile_change           = true;

        //썸네일 생성
        makeThumbnail($up_file_path, $fuArray[0]["file_name"], $up_file_path, MEMBER_THUMB_WIDTH, MEMBER_THUMB_HEIGHT, true);
    } else {
        if ($params['fdel_flag'] == 'Y') {
            $upfile_change     = true;
            $params['trans_up_file'] = "";
        } else {
            $params['trans_up_file'] = $params['old_trans_up_file'];
        }
    }


	//번역회원 신청 요청 저장
    $params['usr_idx']      = $user_view['usr_idx'];
    $params['trans_status'] = iif($user_view['trans_status']==2, 2, 1);
	$params['upt_ip']       = NOW_IP;
	$params['upt_id']       = $MEM_USR['usr_idx'];
	$params['reg_ip']       = NOW_IP;
	$params['reg_id']       = $MEM_USR['usr_idx'];
	if ($cls_member->trans_request_save($params) == false) {
        if ($upfile_change) fileDelete($up_file_path, getUpfileName($params['trans_up_file']));

        fnMsgJson(508, getTransLangMsg("저장 처리중 문제가 발생했습니다.\n고객센터에 문의주세요."), "");
    } else {
        if ($upfile_change) fileDelete($up_file_path, getUpfileName($params['old_trans_up_file']));
    }
?>
{"result": 200, "message": "OK"}