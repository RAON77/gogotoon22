<?
    require($_SERVER["DOCUMENT_ROOT"]."/module/lib/header.php");

    setSession("RETURN_URL", "/mypage/mypage_info.php");
	if (!isUser()) fnMsgGo(500, "", "/member/login.php?return_flag=Y", "");

	$sns_gubun   = chkReqRpl("sns", "", 10, "", "STR");

    if (!isStrpos("google,facebook,apple", $sns_gubun)) fnMsgGo(500, getTransLangMsg("잘못된 요청 정보 입니다."), "/mypage/mypage_info.php", "");
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<title><?=getTransLangMsg("고고툰")?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
<meta id="viewport" name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, target-densitydpi=medium-dpi, shrink-to-fit=no">
<meta name="format-detection" content="telephone=no" />
<meta http-equiv="Cache-Control" content="no-cache,no-store" />

<link href="/css/common.css" rel="stylesheet">
<link href="/css/mobile.css" rel="stylesheet">
<link href="/css/popup.css" rel="stylesheet">

<script src="/js/jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="/module/js/class.helper.js"></script>
<script type="text/javascript" src="/module/js/fn.user.define.js"></script>
<script src="https://cdn.polyfill.io/v2/polyfill.min.js?features=es6"></script>
<script src="https://www.gstatic.com/firebasejs/8.6.5/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.6.2/firebase-auth.js"></script>
<script src="https://unpkg.com/babel-standalone@6/babel.min.js"></script>
<script type="text/babel">
    var ajaxStatus = false;
    var sns_gubun = "<?=$sns_gubun?>";

    try {
        // Your web app's Firebase configuration
        var firebaseConfig = {
            apiKey: "AIzaSyC10yPbBMcOVTIJ6Hw0VKuNjQ9XJMx13zE",
            authDomain: "gogotoon-dev.firebaseapp.com",
            projectId: "gogotoon-dev",
            storageBucket: "gogotoon-dev.appspot.com",
            messagingSenderId: "672070548687",
            appId: "1:672070548687:web:702ecebe0e8ef563e84bba"
        };
        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);
        firebase.auth().languageCode = '<?=strtolower(SITE_SAVE_LANG)?>';

        <?if ($sns_gubun == 'google') {?>
            var provider = new firebase.auth.GoogleAuthProvider();
                provider.addScope('profile');
                provider.addScope('email');
        <?} else if ($sns_gubun == 'facebook') {?>
            var provider = new firebase.auth.FacebookAuthProvider();
                provider.addScope('email');
                provider.addScope('public_profile');
        <?} else {?>
            var provider = new firebase.auth.OAuthProvider('apple.com');
                provider.addScope('email');
                provider.addScope('name');
        <?}?>

        firebase.auth().onAuthStateChanged(function(user) {
            if (user) {
                var user = firebase.auth().currentUser;
                var providerId, email, uid;

                if (user != null) {
                    user.providerData.forEach(function (profile) {
                        providerId = profile.providerId;
                        uid        = profile.uid;
                        email      = profile.email;
                    });

                    if (email == null) {
                        alert("<?=getTransLangMsg(getLoginSnsName($sns_gubun) ." 이메일 공유를 승인해주셔야 합니다.")?>");
                        location.replace("/mypage/mypage_info.php");
                    }

                    if (providerId.indexOf(sns_gubun) > -1) {
                        AJ.callAjax("/mypage/sns/sns_conn_proc.php", {"sns_gubun": sns_gubun, "email": email, "uid": uid}, function(data){
                            if (data.result == 200) {
                                location.replace("/mypage/mypage_info.php");
                            } else {
                                alert(data.message);
                                location.replace("/mypage/mypage_info.php");
                            }
                        });
                    } else {
                        firebase.auth().signOut().then(() => {
                            firebase.auth().signInWithRedirect(provider);
                        }).catch((error) => {
                            alert("<?=getTransLangMsg(getLoginSnsName($sns_gubun) ." 로그인에 실패하였습니다.")?>");
                            location.replace("/mypage/mypage_info.php");
                        });
                    }
                } else {
                    firebase.auth().signOut().then(() => {
                        firebase.auth().signInWithRedirect(provider);
                    }).catch((error) => {
                        alert("<?=getTransLangMsg(getLoginSnsName($sns_gubun) ." 로그인에 실패하였습니다.")?>");
                        location.replace("/mypage/mypage_info.php");
                    });
                }
            } else {
                firebase.auth().signOut().then(() => {
                    firebase.auth().signInWithRedirect(provider);
                }).catch((error) => {
                    alert("<?=getTransLangMsg(getLoginSnsName($sns_gubun) ." 로그인에 실패하였습니다.")?>");
                    location.replace("/mypage/mypage_info.php");
                });
            }
        });
    } catch(e) {
        alert("<?=getTransLangMsg("지원하지 않는 브라우저 입니다. 다른 브라우저를 이용해주세요.")?>");
        location.replace("/mypage/mypage_info.php");
    }
</script>

</head>
<body>
<div id="wrap">
    <div id="container" class="container">
        <div class="inr-c area_member">
            <div class="pop_join pop_member">
                <section class="popup">
                    <header class="p_head ty2">
                        <h2 class="tit blind"><span><?=getTransLangMsg("SNS 계정 연결")?></span></h2>
                    </header>
                    <div class="p_cont">
                        <div class="img ta-c pr-mb2"><img src="/images/common/logo.png" alt="<?=getTransLangMsg("고고툰")?>"></div>
                        <div class="hd_titbox1">
                            <h2 class="title1 ta-c"><?=getTransLangMsg("SNS 계정 연결중 입니다. 잠시만 기다려주세요.")?></h2>
                        </div>
                        <div class="ta-c"><img src="/images/loading.gif" style="width:100px"></div>
                    </div>
                </section>
            </div>
        </div>
    </div><!--//container -->
</div>
    <script type="text/javascript" src="/module/js/jquery.form.js"></script>
    <script type="text/javascript" src="/module/js/fn.util.js"></script>
    <script type="text/javascript" src="/module/js/fn.check.field.js"></script>
</body>
</html>