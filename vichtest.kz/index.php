<?php
require_once("./classes/testUI.php");
$lang=1;
if ($_GET["lang"]=="kz") { $lang=2; } else { $lang=1; }
$testUI = new testUI($lang);
?>
<!doctype html>
<html class="no-js" lang="ru">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Aman Bol</title>
        <meta content="" name="description">
        <meta content="" name="keywords">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="format-detection" content="telephone=no">
        <!-- This make sence for mobile browsers. It means, that content has been optimized for mobile browsers -->
        <meta name="HandheldFriendly" content="true">

        <!-- Stylesheet -->
        <link href="static/css/main.min.css" rel="stylesheet" type="text/css">
        <link href="static/css/separate-css/custom.css" rel="stylesheet" type="text/css">

        <!--  Open Graph Tags -->
        <meta property="og:title" content="" />
        <meta property="og:url" content="" />
        <meta property="og:description" content="" />
        <meta property="og:image" content="" />
        <meta property="og:image:type" content="image/jpeg" />
        <meta property="og:image:width" content="500" />
        <meta property="og:image:height" content="300" />
        <meta property="twitter:description" content="" />
        <link rel="image_src" href="" />

        <!-- Favicons -->
        <link rel="icon" type="image/png" href="favicon.png">

        <script>
            (function(H){H.className=H.className.replace(/\bno-js\b/,'js')})(document.documentElement)
        </script>
        <script src="/js/script.js"></script>
        <script>
            let la="<?=$lang ?>";
            let resultbal=0;

            let Xchanger=0;
            let XidQ=0;
            let XidA=0;
            let XReqQuestion="";
            let Xball=0;


            let q1=0;
            let q2=0;
            let q3=0;
            let q4=0;
            let q5=0;
            let q6=0;

            let b1=0;
            let b2=0;
            let b3=0;
            let b4=0;
            let b5=0;
            let b6=0;
            let bitog=0;

            let status='';
            let st1='<?=$testUI->getContentById(11) ?>';
            let st2='<?=$testUI->getContentById(12) ?>';
            let st3='<?=$testUI->getContentById(13) ?>';
            let st4='<?=$testUI->getContentById(14) ?>';

            let statdescr="";

            function flx() {
                if (la=="1") { document.title = "Русский";  } else { document.title = "Казахский"; }
            }
            function fx() {
                if (la=="1") { window.location.assign("/index.php?lang=kz"); } else { window.location.assign("/index.php"); }
            }

            function langChange(id) {
                la=id;
                fx();
                flx();
            }
        </script>
        <!--[if lt IE 9 ]>
    <script src="static/js/separate-js/html5shiv-3.7.2.min.js" type="text/javascript"></script>
    <script src="static/js/separate-js/respond.min.js" type="text/javascript"></script>
    <meta content="no" http-equiv="imagetoolbar">
    <![endif]-->
    </head>

    <body class="layout">

        <!-- Header Begin -->
        <header class="header">
            <div class="container header__container">
                <div class="header__left">
                    <img src="static/img/general/logo.png" class="header__logo" alt="Aman Bol">
                    <ul class="lang header__lang">
                        <li class="lang__item">
                            <a href="#" onclick="langChange(2)" class="lang__link">RU</a>
                        </li>
                        <li class="lang__item">
                            <a href="#" onclick="langChange(1)" class="lang__link">KZ</a>
                        </li>
                    </ul>
                </div>
                <div class="header__phone">
                    <div class="header__phone-title"><?=$testUI->getContentById(1) ?>:</div>
                    <a href="tel:<?=$testUI->getContentById(2) ?>" class="header__phone-number">
                        <?=$testUI->getContentById(2) ?>
                </a>
                </div>
            </div>
        </header>
        <!--/. Header End -->

        <!-- Entry Begin -->
        <div class="entry">
            <div class="container entry__container">
                <div class="entry__content">
                    <h1 class="entry__title"><?=$testUI->getContentById(3) ?></h1>
                    <div class="entry__subtitle"><?=$testUI->getContentById(8) ?></div>
                    <div class="entry__items">
                        <div class="entry__item">
                            <img src="static/img/general/detective.svg" class="entry__item-icon" alt="">
                            <div class="entry__item-desc"><?=$testUI->getContentById(4) ?></span>
                            </div>
                        </div>
                        <div class="entry__item">
                            <img src="static/img/general/cooperation.svg" class="entry__item-icon" alt="">
                            <div class="entry__item-desc"><?=$testUI->getContentById(5) ?></div>
                        </div>
                    </div>
                    <a href="#" class="button -orange -lg" data-target="#modal-quiz-1" data-toggle="modal"><?=$testUI->getContentById(7) ?></a>
                </div>
                <img src="static/img/general/man.png" class="entry__man" alt="">
            </div>

        </div>
        <!--/. Entry End -->

        <!-- Footer Begin -->
        <footer class="footer">
            <div class="container footer__container">
                <div class="footer__row">
                    <div class="footer__desc">
                        <?=$testUI->getContentById(9) ?>
                    </div>
                    <div class="footer__desc">
                        <?=$testUI->getContentById(6) ?>
                    </div>
                    <div class="social footer__social">
                        <a href="https://www.youtube.com/channel/UCnVJtd0v4muYTVDZGb98KWg" class="social__link -youtube" target="_blank">Youtube</a>
                        <a href="https://ok.ru/group/56142233993406" class="social__link -ok" target="_blank">Ok</a>
                        <a href="https://t.me/s/neolyudi" class="social__link -telegram" target="_blank">Telegram</a>
                        <a href="https://vk.com/neolyudi" class="social__link -vk" target="_blank">VK</a>
                        <a href="https://www.facebook.com/Neolyudi-100302404730879/" class="social__link -fb" target="_blank">FB</a>
                        <a href="https://www.instagram.com/neolyudi/" class="social__link -instagram" target="_blank">Instagram</a>
                    </div>
                </div>
                <img src="static/img/general/man.png" class="footer__man" alt="">
            </div>
        </footer>
        <!--/. Footer End -->

        <!-------------------------------- CONTENT ENDS HERE -------------------------------->

        <!-- Modals Begin -->
        <noindex>

            <!-- Modal Quiz Begin -->
            <div id="modal-quiz-1" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="quiz">
                            <div class="quiz__header"><?=$testUI->getContentById(10) ?></div>
                            <div class="quiz__progress">
                                <div class="quiz__progress-fill" style="width: 16%"></div>
                            </div>
                            <div class="quiz__paginator"><?=$testUI->getContentById(20) ?></div>
                            <div class="quiz__title"><?=$testUI->getContentById(26) ?></div>
                            <div class="quiz__content">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <p>
                                            <input class="with-gap" name="group1" onclick="butactivator('but1', 1, 1);" type="radio" id="radio1" />
                                            <label for="radio1"><?=$testUI->getContentById(27) ?></label>
                                        </p>
                                        <p>
                                            <input class="with-gap" name="group1" onclick="butactivator('but1', 1, 2);" type="radio" id="radio2" />
                                            <label for="radio2"><?=$testUI->getContentById(28) ?></label>
                                        </p>
                                    </div>
                                    <div class="col-md-6 text-center">
                                        <img src="static/img/content/quiz-1.png" alt="">
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="button" id="but1"  class="button -lg -orange quiz__button" data-dismiss="modal" data-target="#modal-quiz-2" data-toggle="modal" disabled><?=$testUI->getContentById(43) ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/. Modal Quiz End -->

            <!-- Modal Quiz Begin -->
            <div id="modal-quiz-2" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="quiz">
                            <div class="quiz__header"><?=$testUI->getContentById(10) ?></div>
                            <div class="quiz__progress">
                                <div class="quiz__progress-fill" style="width: 32%"></div>
                            </div>
                            <div class="quiz__paginator"><?=$testUI->getContentById(21) ?></div>
                            <div class="quiz__title"><?=$testUI->getContentById(30) ?></div>
                            <div class="quiz__content">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="row" data-gutter="10">
                                            <div class="col-6">
                                                <p>
                                                    <input class="with-gap" onclick="butactivator('but2', 2, 1);" name="group1" type="radio" id="radio3" />
                                                    <label for="radio3">0 - 14<?=$testUI->getContentById(29) ?></label>
                                                </p>
                                                <p>
                                                    <input class="with-gap" onclick="butactivator('but2', 2, 2);" name="group1" type="radio" id="radio4" />
                                                    <label for="radio4">15 - 19<?=$testUI->getContentById(29) ?></label>
                                                </p>
                                                <p>
                                                    <input class="with-gap" onclick="butactivator('but2', 2, 3);" name="group1" type="radio" id="radio5" />
                                                    <label for="radio5">20 - 29<?=$testUI->getContentById(29) ?></label>
                                                </p>
                                                <p>
                                                    <input class="with-gap" onclick="butactivator('but2', 2, 4);" name="group1" type="radio" id="radio6" />
                                                    <label for="radio6">30 - 39<?=$testUI->getContentById(29) ?></label>
                                                </p>
                                            </div>
                                            <div class="col-6">
                                                <p>
                                                    <input class="with-gap" onclick="butactivator('but2', 2, 5);" name="group1" type="radio" id="radio7" />
                                                    <label for="radio7">40 - 49<?=$testUI->getContentById(29) ?></label>
                                                </p>
                                                <p>
                                                    <input class="with-gap" onclick="butactivator('but2', 2, 6);" name="group1" type="radio" id="radio8" />
                                                    <label for="radio8">50 - 59<?=$testUI->getContentById(29) ?></label>
                                                </p>
                                                <p>
                                                    <input class="with-gap" onclick="butactivator('but2', 2, 7);" name="group1" type="radio" id="radio9" />
                                                    <label for="radio9">> 60<?=$testUI->getContentById(29) ?></label>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-center">
                                        <img src="static/img/content/quiz-2.png" alt="">
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="button" id="but2" class="button -lg -orange quiz__button" data-dismiss="modal" data-target="#modal-quiz-3" data-toggle="modal" disabled><?=$testUI->getContentById(43) ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/. Modal Quiz End -->

            <!-- Modal Quiz Begin -->
            <div id="modal-quiz-3" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="quiz">
                            <div class="quiz__header"><?=$testUI->getContentById(10) ?></div>
                            <div class="quiz__progress">
                                <div class="quiz__progress-fill" style="width: 50%"></div>
                            </div>
                            <div class="quiz__paginator"><?=$testUI->getContentById(22) ?></div>
                            <div class="quiz__title"><?=$testUI->getContentById(31) ?></div>
                            <div class="quiz__content">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <p>
                                            <input type="checkbox" onclick="butactivator('but3', 3, 1);" class="filled-in" id="check1">
                                            <label for="check1"><?=$testUI->getContentById(32) ?></label>
                                        </p>
                                        <p>
                                            <input type="checkbox" onclick="butactivator('but3', 3, 2);" class="filled-in" id="check2">
                                            <label for="check2"><?=$testUI->getContentById(33) ?></label>
                                        </p>
                                    </div>
                                    <div class="col-md-8 text-center">
                                        <img src="static/img/content/quiz-3.png" alt="">
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="button" class="button -lg -orange quiz__button" id="but3" data-dismiss="modal" data-target="#modal-quiz-4" data-toggle="modal" disabled><?=$testUI->getContentById(43) ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/. Modal Quiz End -->

            <!-- Modal Quiz Begin -->
            <div id="modal-quiz-4" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="quiz">
                            <div class="quiz__header"><?=$testUI->getContentById(10) ?></div>
                            <div class="quiz__progress">
                                <div class="quiz__progress-fill" style="width: 66%"></div>
                            </div>
                            <div class="quiz__paginator"><?=$testUI->getContentById(23) ?></div>
                            <div class="quiz__title"><?=$testUI->getContentById(34) ?></div>
                            <div class="quiz__content">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <p>
                                            <input class="with-gap" name="group1" onclick="butactivator('but4', 4, 1);" type="radio" id="radio10" />
                                            <label for="radio10"><?=$testUI->getContentById(35) ?></label>
                                        </p>
                                        <p>
                                            <input class="with-gap" name="group1" onclick="butactivator('but4', 4, 2);" type="radio" id="radio11" />
                                            <label for="radio11"><?=$testUI->getContentById(36) ?></label>
                                        </p>
                                    </div>
                                    <div class="col-md-8 text-center">
                                        <img src="static/img/content/quiz-4.png" alt="">
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="button" class="button -lg -orange quiz__button" id="but4" data-dismiss="modal" data-target="#modal-quiz-5" data-toggle="modal" disabled><?=$testUI->getContentById(43) ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/. Modal Quiz End -->

            <!-- Modal Quiz Begin -->
            <div id="modal-quiz-5" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="quiz">
                            <div class="quiz__header"><?=$testUI->getContentById(10) ?></div>
                            <div class="quiz__progress">
                                <div class="quiz__progress-fill" style="width: 66%"></div>
                            </div>
                            <div class="quiz__paginator"><?=$testUI->getContentById(24) ?></div>
                            <div class="quiz__title"><?=$testUI->getContentById(37) ?></div>
                            <div class="quiz__content">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <p>
                                            <input class="with-gap" onclick="butactivator('but5', 5, 1);" name="group1" type="radio" id="radio12" />
                                            <label for="radio12"><?=$testUI->getContentById(38) ?></label>
                                        </p>
                                        <p>
                                            <input class="with-gap" onclick="butactivator('but5', 5, 2);" name="group1" type="radio" id="radio13" />
                                            <label for="radio13"><?=$testUI->getContentById(39) ?></label>
                                        </p>
                                        <p>
                                            <input class="with-gap" onclick="butactivator('but5', 5, 3);" name="group1" type="radio" id="radio14" />
                                            <label for="radio14"><?=$testUI->getContentById(40) ?></label>
                                        </p>
                                        <p>
                                            <input class="with-gap" onclick="butactivator('but5', 5, 4);" name="group1" type="radio" id="radio15" />
                                            <label for="radio15"><?=$testUI->getContentById(41) ?></label>
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <img src="static/img/content/quiz-5.png" alt="">
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="button" class="button -lg -orange quiz__button" id="but5" data-dismiss="modal" data-target="#modal-quiz-6" data-toggle="modal" disabled><?=$testUI->getContentById(43) ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/. Modal Quiz End -->

            <!-- Modal Quiz Begin -->
            <div id="modal-quiz-6" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="quiz">
                            <div class="quiz__header"><?=$testUI->getContentById(10) ?></div>
                            <div class="quiz__progress">
                                <div class="quiz__progress-fill" style="width: 100%"></div>
                            </div>
                            <div class="quiz__paginator"><?=$testUI->getContentById(25) ?></div>
                            <div class="quiz__title"><?=$testUI->getContentById(42) ?></div>
                            <div class="quiz__content">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <p>
                                            <input class="with-gap" onclick="butactivator('but6', 6, 1);" name="group1" type="radio" id="radio16" />
                                            <label for="radio16"><?=$testUI->getContentById(35) ?></label>
                                        </p>
                                        <p>
                                            <input class="with-gap" onclick="butactivator('but6', 6, 2);" name="group1" type="radio" id="radio17" />
                                            <label for="radio17"><?=$testUI->getContentById(36) ?></label>
                                        </p>
                                    </div>
                                    <div class="col-md-6 text-center">
                                        <img src="static/img/content/quiz-6.png" alt="">
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="button" class="button -lg -orange quiz__button" id="but6" onclick="resultSend();" data-dismiss="modal" data-target="#modal-quiz-complete" data-toggle="modal" disabled><?=$testUI->getContentById(43) ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/. Modal Quiz End -->

            <!-- Modal Quiz Begin -->
            <div id="modal-quiz-complete" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="quiz">
                            <div class="quiz__header"><?=$testUI->getContentById(19) ?></div>
                            <div class="quiz__results" id="quizresults">1 - НИЗКИЙ</div>
                            <div class="quiz__title" id="quizresultsdesc">Твои риски получения ВИЧ достаточно низкие, а значит ты всё делаешь правильно, чтобы позаботиться о своем сексуальном здоровье. Тем не менее, мы рекомендуем тебе сдавать тесты на ИППП, включая ВИЧ, каждые полгода, чтобы отслеживать
                                свой статус, ты так же можешь задать все дополнительные вопросы по телефону доверия:</div>
                            <a href="tel:<?=$testUI->getContentById(2) ?>" class="quiz__phone"><?=$testUI->getContentById(2) ?></a>
                            <div class="quiz__title" id="quizblue"></div>
                            <div class="quiz__bottom">
                                <div class="quiz__desc"><?=$testUI->getContentById(6) ?></div>
                                <div class="social quiz__social">
                                    <a href="https://www.youtube.com/channel/UCnVJtd0v4muYTVDZGb98KWg" class="social__link -youtube" target="_blank">Youtube</a>
                                    <a href="https://ok.ru/group/56142233993406" class="social__link -ok" target="_blank">Ok</a>
                                    <a href="https://t.me/s/neolyudi" class="social__link -telegram" target="_blank">Telegram</a>
                                    <a href="https://vk.com/neolyudi" class="social__link -vk" target="_blank">VK</a>
                                    <a href="https://www.facebook.com/Neolyudi-100302404730879/" class="social__link -fb" target="_blank">FB</a>
                                    <a href="https://www.instagram.com/neolyudi/" class="social__link -instagram" target="_blank">Instagram</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: none">
                <div id="stdesc1"><?=$testUI->getContentById(15) ?></div>
                <div id="stdesc2"><?=$testUI->getContentById(16) ?></div>
                <div id="stdesc3"><?=$testUI->getContentById(17) ?></div>
                <div id="stdesc4"><?=$testUI->getContentById(18) ?></div>
                <div id="qb"><?=$testUI->getContentById(44) ?></div>
            </div>
            <!--/. Modal Quiz End -->

        </noindex>
        <!--/. Modals End -->

        <!-- Main scripts. You can replace it, but I recommend you to leave it here -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="static/js/main.min.js"></script>
        <script src="static/js/separate-js/scripts.js"></script>
        <script>
            function butactivator(id, qid, answerid)
            {
                document.getElementById(id).disabled = false;
                switch (qid) {
                    case 1: q1=answerid; break;
                    case 2: q2=answerid; break;
                    case 3:
                    let check1=document.getElementById('check1').checked;
                    let check2=document.getElementById('check2').checked;
                    let counterCheck=0;
                    if ((check1==true) && (check2==false)) counterCheck=1;
                    if ((check1==false) && (check2==true)) counterCheck=2;
                    if ((check1==true) && (check2==true)) counterCheck=3;
                    q3=counterCheck;
                    if ((check1==false) && (check2==false)) document.getElementById(id).disabled = true;
                        break;
                    case 4: q4=answerid; break;
                    case 5: q5=answerid; break;
                    case 6: q6=answerid; break;
                }
            }

            function resultSend() {
                switch (q1) {
                    case 1: b1=6; break;
                    case 2: b1=4; break;
                }
                switch (q2) {
                    case 1: b2=0; break;
                    case 2: b2=0; break;
                    case 3: b2=2; break;
                    case 4: b2=4; break;
                    case 5: b2=3; break;
                    case 6: b2=1; break;
                    case 7: b2=0; break;
                }
                switch (q3) {
                    case 1: if (q1==1) b3=7; else b3=7; break;
                    case 2: if (q1==1) b3=3; else b3=3; break;
                    case 3: b3=10; break;
                }
                switch (q4) {
                    case 1: b4=10; break;
                    case 2: b4=0; break;
                }
                switch (q5) {
                    case 1: b5=0; break;
                    case 2: b5=2; break;
                    case 3: b5=3; break;
                    case 4: b5=5; break;
                }
                switch (q6) {
                    case 1: b6=10; break;
                    case 2: b6=0; break;
                }
                bitog=(b1+b2+b3+b4+b5+b6)/6;
                let itogo=0;
                if (bitog<10) itogo=4;
                if (bitog<6) itogo=3;
                if (bitog<4) itogo=2;
                if (bitog<2) itogo=1;
                switch (itogo) {
                    case 1: status=st1; statdescr=document.getElementById("stdesc1").innerText; break;
                    case 2: status=st2; statdescr=document.getElementById("stdesc2").innerText; break;
                    case 3: status=st3; statdescr=document.getElementById("stdesc3").innerText; break;
                    case 4: status=st4; statdescr=document.getElementById("stdesc4").innerText; break;
                }
                if ((q1==1) && (q3==1 || q3==3)) document.getElementById("quizblue").innerHTML=document.getElementById("qb").innerHTML;
                document.getElementById('quizresults').innerText=status;
                document.getElementById('quizresultsdesc').innerText=statdescr;
                document.getElementById('but1').disabled = true;
                document.getElementById('but2').disabled = true;
                document.getElementById('but3').disabled = true;
                document.getElementById('but4').disabled = true;
                document.getElementById('but5').disabled = true;
                document.getElementById('check1').checked=false;
                document.getElementById('check2').checked=false;
                ajaxSender(q1, q2, q3, q4, q5, q6, bitog, la);
            }
            
            function ajaxSender(q1, q2, q3, q4, q5, q6, res, lang) {
                $.ajax({
                    type:'POST',
                    url:'zapros.php',
                    data:{
                        'q1':q1,
                        'q2':q2,
                        'q3':q3,
                        'q4':q4,
                        'q5':q5,
                        'q6':q6,
                        'res':res,
                        'lang':lang
                    },
                    success:function(html){
                    //$('#masters').html(html);
                    },
                    error:function(html){
                        $('body').css('cursor','default');
                        alert('Ошибка подключения!');
                    },
                });
            }
        </script>
    </body>

</html>
