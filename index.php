<?php
$plan = array();
$plan[1] = 'Aプラン';
$plan[2] = 'Bプラン';
$plan[3] = '芸能人差し入れプラン';
$plan[4] = '相談したい';

session_start();
$mode = 'input';
$errmessage = array();
if (isset($_POST['back']) && $_POST['back']) {
    // 何もしない
} else if (isset($_POST['confirm']) && $_POST['confirm']) {
    // 確認画面
    if (!$_POST['fullname']) {
        $errmessage[] = "名前を入力してください";
    } else if (mb_strlen($_POST['fullname']) > 50) {
        $errmessage[] = "名前は50文字以内にしてください";
    }
    $_SESSION['fullname'] = htmlspecialchars($_POST['fullname'], ENT_QUOTES);

    if (!$_POST['email']) {
        $errmessage[] = "メールアドレスを入力してください";
    } else if (mb_strlen($_POST['email']) > 200) {
        $errmessage[] = "メールアドレスは200文字以内にしてください";
    } else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errmessage[] = "メールアドレスが不正です";
    }
    $_SESSION['email'] = htmlspecialchars($_POST['email'], ENT_QUOTES);

    if ($_POST['tel']) {

        if (mb_strlen($_POST['tel']) > 13) {
            $errmessage[] = "電話番号は13文字以内にしてください";
        } else if (mb_strlen($_POST['tel']) < 10) {
            $errmessage[] = "電話番号は10文字以上にしてください";
        } else if (!preg_match('/^(0{1}\d{1,4}-{0,1}\d{1,4}-{0,1}\d{4})$/', $_POST['tel'])) {
            $errmessage[] = "正しい電話番号を入力してください";
        }
    }
    $_SESSION['tel'] = htmlspecialchars($_POST['tel'], ENT_QUOTES);

    if (!$_POST['talent']) {
        $errmessage[] = "差し入れしたいタレントの名前を入力してください";
    } else if (mb_strlen($_POST['talent']) > 50) {
        $errmessage[] = "差し入れしたいタレントの名前は50文字以内にしてください";
    }
    $_SESSION['talent'] = htmlspecialchars($_POST['talent'], ENT_QUOTES);

    if (!isset($_POST['plan']) || !$_POST['plan']) {
        $errmessage[] = "プランを選んでください";
    } else if ($_POST['plan'] <= 0 || $_POST['plan'] >= 5) {
        $errmessage[] = "プランが不正です";
    }

    if (isset($_POST['plan'])) {
        $_SESSION['plan'] = htmlspecialchars($_POST['plan'], ENT_QUOTES);
    }

    if ($_POST['massage']) {
        if (mb_strlen($_POST['massage']) > 500) {
            $errmessage[] = "備考欄は500文字以内にしてください";
        }
    }
    $_SESSION['massage'] = htmlspecialchars($_POST['massage'], ENT_QUOTES);

    if ($errmessage) {
        $mode = 'input';
    } else {
        $token = bin2hex(random_bytes(32));
        $_SESSION['token'] = $token;
        $mode = 'confirm';
    }
} else if (isset($_POST['send']) && $_POST['send']) {
    // 送信ボタンを押した時
    if (!$_POST["token"] || !$_SESSION['token'] || !$_SESSION['email']) {
        $errmessage[] = '不正な処理が行われました';
        $_SESSION = array();
        $mode = 'input';
    } else if ($_POST["token"] != $_SESSION['token']) {
        $errmessage[] = '不正な処理が行われました';
        $_SESSION = array();
        $mode = 'input';
    } else {
        $massage1 = "この度はお問い合わせありがとうございます。 \r\n"
            . "入力された内容は以下の通りです。 \r\n"
            . "\r\n"
            . "============" . "\r\n"
            . "お名前:" . $_SESSION['fullname'] . "\r\n"
            . "メールアドレス:" . $_SESSION['email'] . "\r\n"
            . "電話番号:" . $_SESSION['tel'] . "\r\n"
            . "差し入れしたいタレント:" . $_SESSION['talent'] . "\r\n"
            . "プラン:" . $plan[$_SESSION['plan']] . "\r\n"
            . "備考欄:\r\n"
            . preg_replace("/\r\n|\r|\n/", "\r\n", $_SESSION['massage']) . "\r\n"
            . "============" . "\r\n"
            . "\r\n";

        $massage2 = "ホームページから下記のお問い合わせがありました。 \r\n"
            . "\r\n"
            . "============" . "\r\n"
            . "お名前:" . $_SESSION['fullname'] . "\r\n"
            . "メールアドレス:" . $_SESSION['email'] . "\r\n"
            . "電話番号:" . $_SESSION['tel'] . "\r\n"
            . "差し入れしたいタレント:" . $_SESSION['talent'] . "\r\n"
            . "プラン:" . $plan[$_SESSION['plan']] . "\r\n"
            . "備考欄:\r\n"
            . preg_replace("/\r\n|\r|\n/", "\r\n", $_SESSION['massage']) . "\r\n"
            . "============" . "\r\n"
            . "\r\n";

        mail($_SESSION['email'], "Thank you for your e-mail", $massage1);
        mail("koshikibeam@gmail.com", "Email from HP", $massage2);
        $_SESSION['fullname'] = "";
        $_SESSION['email'] = "";
        $_SESSION['tel'] = "";
        $_SESSION['talent'] = "";
        $_SESSION['massage'] = "";
        $mode = 'send';
    }
} else {
    $_SESSION['fullname'] = "";
    $_SESSION['email'] = "";
    $_SESSION['tel'] = "";
    $_SESSION['talent'] = "";
    $_SESSION['plan'] = "";
    $_SESSION['massage'] = "";
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="韓国で話題のコーヒーカーが日本初上陸！推しのコンサートやイベント、ドラマの現場にファンの方がキッチンカーを差し入れをすることができます。">
    <meta property="og:title" content="差し入れコーヒーカー" />
    <meta property="og:description" content="韓国で話題のコーヒーカーが日本初上陸！推しのコンサートやイベント、ドラマの現場にファンの方がキッチンカーを差し入れをすることができます。" />
    <meta property="og:type" content="Webサイト" />
    <meta property="og:url" content="https://koshikibeam.website/coffeecar/" />
    <meta property="og:image" content="https://koshikibeam.website/coffeecar/img/sp/top-img@2x.png" />
    <meta property="og:site_name" content="差し入れコーヒーカー" />
    <meta property="og:locale" content="ja_JP" />
    <meta name="keywords" content="差し入れコーヒーカー">
    <meta name="twitter:card" content="Twitterカードの種類" />
    <meta name="twitter:site" content="@Twitterユーザー名" />
    <meta property="fb:app_id" content="FacebookアプリID" />
    <meta name="robots" content="noindex,nofollow">
    <title>差し入れコーヒーカー</title>
    <script>
        (function(d) {
            var config = {
                    kitId: 'guh3amn',
                    scriptTimeout: 3000,
                    async: true
                },
                h = d.documentElement,
                t = setTimeout(function() {
                    h.className = h.className.replace(/\bwf-loading\b/g, "") + " wf-inactive";
                }, config.scriptTimeout),
                tk = d.createElement("script"),
                f = false,
                s = d.getElementsByTagName("script")[0],
                a;
            h.className += " wf-loading";
            tk.src = 'https://use.typekit.net/' + config.kitId + '.js';
            tk.async = true;
            tk.onload = tk.onreadystatechange = function() {
                a = this.readyState;
                if (f || a && a != "complete" && a != "loaded") return;
                f = true;
                clearTimeout(t);
                try {
                    Typekit.load(config)
                } catch (e) {}
            };
            s.parentNode.insertBefore(tk, s)
        })(document);
    </script>
    <link rel="stylesheet" href="/coffeecar/css/style.css">
</head>

<body>
    <main>
        <?php if ($mode == 'input') { ?>

            <!-- top -->
            <div class="l-mv">
                <div class="l-mv__inner">
                    <div class="c-contact__btn"><a href="#contact">お問合わせはこちら</a></div>
                    <nav class="p-drawer">
                        <button id="menu-icon" class=" p-drawer__icon">
                            <span></span>
                            <span></span>
                            <span></span>
                        </button>
                        <div id="menu-panel" class="p-drawer__panel">
                            <ul class="p-drawer__panel-list">
                                <li><a href="#about">■差し入れコーヒーメーカーとは</a></li>
                                <li><a href="#situation">■使用するシチュエーション</a></li>
                                <li><a href="#plan">■料金プラン</a></li>
                                <li><a href="#qanda">■よくあるご質問</a></li>
                                <li><a href="#contact">■お見積り・お申込みフォーム</a></li>
                            </ul>
                        </div>
                    </nav>
                    <div class="p-top">
                        <img class="u-hidden-sp u-hidden-tab" src="/coffeecar/img/top-img@2x.png" alt="">
                        <img class="u-hidden-pc" src="/coffeecar/img/sp/top-img@2x.png" alt="">
                    </div>
                </div>
            </div>

            <!-- about -->
            <section id="about" class="l-about">
                <div class="l-about__inner fadein">
                    <div class="c-title__wrap">
                        <h1 class="c-title">ABOUT</h1>
                        <h2 class="c-subtitle">差し入れコーヒーカーとは</h2>
                    </div>
                    <div class="p-about">
                        <p>
                            <span class="u-hidden-sp u-hidden-tab">韓国で話題のコーヒーカーが日本初上陸！</span>
                            <span class="u-hidden-sp u-hidden-tab">推しのコンサートやイベント、ドラマの現場に</span>
                            <span class="u-hidden-sp u-hidden-tab">ファンの方がキッチンカーを差し入れをすることができます。</span>
                            <span class="u-hidden-sp u-hidden-tab">キッチンカーには推しの横断幕とのぼり旗を設置</span>
                            <span class="u-hidden-sp u-hidden-tab">致します。韓国では「コーヒーカー(커피차)」</span>
                            <span class="u-hidden-sp u-hidden-tab">「フードカー(밥차)」と呼ばれており、一般的なファン活動となります。</span>
                            <span class="u-hidden-pc">韓国で話題のコーヒーカーが日本初上陸！</span>
                            <span class="u-hidden-pc">推しのコンサートやイベント、ドラマの</span>
                            <span class="u-hidden-pc">現場にファンの方がキッチンカーを</span>
                            <span class="u-hidden-pc">差し入れをすることができます。</span>
                            <span class="u-hidden-pc">キッチンカーには推しの横断幕とのぼり旗</span>
                            <span class="u-hidden-pc">を設置致します。</span>
                            <span class="u-hidden-pc">韓国では「コーヒーカー(커피차)」</span>
                            <span class="u-hidden-pc">「フードカー(밥차)」と呼ばれており、</span>
                            <span class="u-hidden-pc">一般的なファン活動となります。</span>
                        </p>
                    </div>
                </div>
            </section>

            <!-- situation -->
            <section id="situation" class="l-situation">
                <div class="l-situation__inner fadein">
                    <div class="c-title__wrap">
                        <h1 class="c-title">SITUATION</h1>
                        <h2 class="c-subtitle">使用するシチュエーション</h2>
                    </div>
                    <div class="p-situation">
                        <div class="p-situation__item">
                            <p>
                                <span>イベントやコンサート、ドラマへの<br class="u-hidden-pc u-hidden-tab u-hidden-sp-small">差し入れ</span>
                                <span>お誕生日に事務所への差し入れ</span>
                                <span>芸能人の方が直接現場に差し入れ</span>
                                <span>その他の場面でもご使用いただけますので、<br class="u-hidden-tab u-hidden-sp-small">ご相談ください。</span>
                            </p>
                        </div>
                        <div class="p-situation__item"><img src="/coffeecar/img/situation-img@2x.png" alt=""></div>
                    </div>
                </div>
            </section>

            <!-- plan -->
            <section id="plan" class="l-plan">
                <div class="l-plan__inner fadein">
                    <div class="c-contact__btn"><a href="#contact">お問合わせはこちら</a></div>
                    <div class="c-title__wrap">
                        <h1 class="c-title">PLAN</h1>
                        <h2 class="c-subtitle">料金プラン</h2>
                    </div>
                    <div class="p-plan">
                        <div class="p-plan__item">
                            <div class="p-plan__item-title">Aプラン</div>
                            <div class="p-plan__item-body">
                                <div class="p-plan__item-text">稼働時間：12時間(差し入れする現場と相談)<br>
                                    事務所許諾連絡：お客様<br>
                                    カップホルダー300個無料(デザイン持ち込み)<br>
                                    横断幕印刷無料(デザイン持ち込み)<br>
                                    のぼり旗印刷無料(デザイン持ち込み)<br>
                                    出張費込</div>
                                <div class="p-plan__item-price">28万円<span>(税抜)</span></div>
                            </div>
                        </div>
                        <div class="p-plan__item">
                            <div class="p-plan__item-title">Bプラン</div>
                            <div class="p-plan__item-body">
                                <div class="p-plan__item-text">稼働時間：12時間(差し入れする現場と相談)<br>
                                    事務所許諾連絡：ラブレター<br>
                                    カップホルダー300個無料(デザイン持ち込み)<br>
                                    横断幕印刷無料(デザイン持ち込み)<br>
                                    のぼり旗印刷無料(デザイン持ち込み)<br>
                                    出張費込</div>
                                <div class="p-plan__item-price">35万円<span>(税抜)</span></div>
                            </div>
                        </div>
                        <div class="p-plan__item">
                            <div class="p-plan__item-title">芸能人差し入れプラン</div>
                            <div class="p-plan__item-body">
                                <div class="p-plan__item-text">稼働時間：12時間(差し入れする現場と相談)<br>
                                    カップホルダー300個無料(デザイン持ち込み)<br>
                                    横断幕印刷無料(デザイン持ち込み)<br>
                                    のぼり旗印刷無料(デザイン持ち込み)<br>
                                    出張費込</div>
                                <div class="p-plan__item-price">20万円<span>(税抜)</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- qanda -->
            <section id="qanda" class="l-qanda">
                <div class="l-qanda__inner fadein">
                    <div class="c-title__wrap">
                        <h1 class="c-title">Q&A</h1>
                        <h2 class="c-subtitle">よくあるご質問</h2>
                    </div>
                    <div class="p-qanda">
                        <div class="p-qanda__item">
                            <div class="p-qanda__q">日本全国出張可能ですか。</div>
                            <div class="p-qanda__a">東京都、神奈川県、千葉県、埼玉県が出張可能です。出張料金はプランに含まれております。
                            </div>
                        </div>
                        <div class="p-qanda__item">
                            <div class="p-qanda__q">事務所への許諾がないと依頼するのは難しいですか。</div>
                            <div class="p-qanda__a">事務所への許諾は必須となります。弊社でも許諾代行可能ですので、事務所との連絡を弊社で
                                <br>希望の場合は35万プランをお選びください。
                            </div>
                        </div>
                        <div class="p-qanda__item">
                            <div class="p-qanda__q">事務所への許諾に必要な項目を教えてください。</div>
                            <div class="p-qanda__a">差し入れを行うことが可能であるか、差し入れを行う場所にキッチンカーと停めるスペースを
                                <br>確保していただけるか、差し入れに行っても良い時間と滞在可能時間を必ずお聞きください。

                            </div>
                        </div>
                        <div class="p-qanda__item">
                            <div class="p-qanda__q">余ったカップホルダーをいただくことは可能ですか。</div>
                            <div class="p-qanda__a">可能です。送料着払いと箱代として500円(税込)がかかります。
                                <br>東京からゆうパックもしくはヤマト運輸からの発送となります。
                            </div>
                        </div>
                        <div class="p-qanda__item">
                            <div class="p-qanda__q">カップホルダーのデザインや横断幕のデザインを依頼することは可能ですか。</div>
                            <div class="p-qanda__a">1デザイン3万円(税込)で可能です。
                            </div>
                        </div>
                        <div class="p-qanda__item">
                            <div class="p-qanda__q">使った横断幕やのぼりをいただくことは可能ですか。</div>
                            <div class="p-qanda__a">のぼり旗と横断幕をお送りすることは可能です。スタンドなどはお送りすることが出来かねます。
                                <br>送料着払いと箱代として500円(税込)がかかります。東京からゆうパックもしくはヤマト運輸からの
                                <br class="u-hidden-sp u-hidden-tab">発送となります。カップホルダーとの同封はカップホルダー破損の恐れがございますので出来かねます。
                            </div>
                        </div>
                        <div class="p-qanda__item">
                            <div class="p-qanda__q">差し入れできるかまだわからないのですが、相談やお見積りをいただくことは可能ですか。</div>
                            <div class="p-qanda__a">もちろん可能です。フォームよりお気軽にご相談ください。
                            </div>
                        </div>
                        <div class="p-qanda__item">
                            <div class="p-qanda__q">プランに記載されている費用以外にかかる費用はございますか。</div>
                            <div class="p-qanda__a">ありません。デザイン制作など行う場合や、カップホルダーやのぼりを返送する場合は別途費用が
                                かかります。
                            </div>
                        </div>
                        <div class="p-qanda__item">
                            <div class="p-qanda__q">支払い方法を教えてください。</div>
                            <div class="p-qanda__a">銀行振込となります。ご依頼日の10日前までにお振り込みお願い致します。
                            </div>
                        </div>
                        <div class="p-qanda__item">
                            <div class="p-qanda__q">コーヒーカーと併せて応援広告を出すことはできますか。</div>
                            <div class="p-qanda__a">可能です。ご依頼の際にご相談ください。
                            </div>
                        </div>
                        <div class="p-qanda__item">
                            <div class="p-qanda__q">飲み物の種類は選べますか。何種類ありますか。</div>
                            <div class="p-qanda__a">選べます。10種類ほどご用意ございますがご希望のものがございましたらお気軽にご相談ください。
                            </div>
                        </div>
                        <div class="p-qanda__item">
                            <div class="p-qanda__q">飲み物以外に食べ物を差し入れすることも可能ですか。</div>
                            <div class="p-qanda__a">食べ物の種類によりますので、ご相談ください。
                            </div>
                        </div>
                        <div class="p-qanda__item">
                            <div class="p-qanda__q">事務所からの許諾が下りなかった場合返金は可能ですか。</div>
                            <div class="p-qanda__a">事務所からの許可が出てからお申し込みいただくようお願い致します。
                            </div>
                        </div>
                        <div class="p-qanda__item">
                            <div class="p-qanda__q">キャンセルはできますか。</div>
                            <div class="p-qanda__a">ご依頼の1ヶ月前までキャンセルが可能です。ただ事務所との日程調整なども行っているため、
                                できる限り正式なご依頼が決まったタイミングでお申し込みお願い致します。
                                またキャンセルの際に発生した返金の振込手数料などはお客様のご負担となります。
                            </div>
                        </div>
                        <div class="p-qanda__item">
                            <div class="p-qanda__q">コンサートやイベントがなくなった場合どうなりますか。</div>
                            <div class="p-qanda__a">別日への振替が可能です。5日前まではキャンセルも無料でお受け致します。
                                災害によるイベントの中止の場合はキャンセル料などは一切発生致しませんが、
                                タレントの不祥事によるイベントの中止は、5日前まではキャンセル無料でお受け致します。
                            </div>
                        </div>
                        <div class="p-qanda__item">
                            <div class="p-qanda__q">AプランとBプランの違いは何ですか。</div>
                            <div class="p-qanda__a">事務所への許可取りをお客様自身で行うか、弊社のほうで行うかの違いとなります。
                                その他は全て内容は同じです。

                            </div>
                        </div>
                        <div class="p-qanda__item">
                            <div class="p-qanda__q">エンタメ差し入れプランとは何ですか</div>
                            <div class="p-qanda__a">芸能人の方が自分の現場への差し入れ、お友達の現場への差し入れなどに使用していただけます。
                                芸能の業界人専用プランです。詳しくはフォームよりご相談ください。
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- contact -->
            <section id="contact" class="l-contact">
                <div class="l-contact__inner">
                    <div class="c-title__wrap">
                        <h1 class="c-title">CONTACT</h1>
                        <h2 class="c-subtitle">お見積り・お申込みフォーム</h2>
                    </div>
                    <div class="p-contact">
                        <!-- 入力画面 -->
                        <form class="p-contact__form" action="./index.php" method="post">
                            <?php
                            if ($errmessage) {
                                echo '<div class="err">';
                                echo implode('<br>', $errmessage);
                                echo '</div>';
                            }
                            ?>
                            <dl class="p-contact__list">
                                <div class="p-contact__item">
                                    <dt class="p-contact__item-title">
                                        お名前<span>(必須)</span>
                                    </dt>
                                    <dd class="p-contact__item-input">
                                        <input class="form-input" type="text" name="fullname" value="<?php echo $_SESSION['fullname'] ?>">
                                    </dd>
                                </div>
                                <div class="p-contact__item">
                                    <dt class="p-contact__item-title">
                                        メールアドレス<span>(必須)</span>
                                    </dt>
                                    <dd class="p-contact__item-input">
                                        <input class="form-input" type="email" name="email" value="<?php echo $_SESSION['email'] ?>">
                                    </dd>
                                </div>
                                <div class="p-contact__item">
                                    <dt class="p-contact__item-title">
                                        電話番号
                                    </dt>
                                    <dd class="p-contact__item-input">
                                        <input class="form-input" type="tel" name="tel" value="<?php echo $_SESSION['tel'] ?>">
                                    </dd>
                                </div>
                                <div class="p-contact__item">
                                    <dt class="p-contact__item-title">
                                        差し入れしたいタレント<span>(必須)</span>
                                    </dt>
                                    <dd class="p-contact__item-input">
                                        <input class="form-input" type="text" name="talent" value="<?php echo $_SESSION['talent'] ?>">
                                    </dd>
                                </div>
                                <div class="p-contact__item">
                                    <dt class="p-contact__item-title">
                                        プラン<span>(必須)</span>
                                    </dt>
                                    <dd class="p-contact__item-input flex">
                                        <?php foreach ($plan as $i => $v) { ?>
                                            <?php if ($_SESSION['plan'] == $i) { ?>
                                                <label><input type="radio" name="plan" value="<?php echo $i ?>" checked><?php echo $v ?></label>
                                            <?php } else { ?>
                                                <label><input type="radio" name="plan" value="<?php echo $i ?>"><?php echo $v ?></label>
                                            <?php } ?>
                                        <?php } ?>
                                    </dd>
                                </div>
                                <div class="p-contact__item">
                                    <dt class="p-contact__item-title">
                                        備考欄
                                    </dt>
                                    <dd class="p-contact__item-input">
                                        <textarea class="form-textarea" name="massage"><?php echo $_SESSION['massage'] ?></textarea>
                                    </dd>
                                </div>
                                <div class="p-contact__item-button">
                                    <input type="submit" name="confirm" value="確認する">
                                </div>
                            </dl>
                        </form>
                    </div>
                </div>
            </section>
        <?php } else if ($mode == 'confirm') { ?>

            <section id="contact" class="l-contact">
                <div class="l-contact__inner">
                    <div class="c-title__wrap">
                        <h1 class="c-title">CONTACT</h1>
                        <h2 class="c-subtitle">お見積り・お申込みフォーム</h2>
                    </div>
                    <div class="p-contact">

                        <!-- 確認画面 -->
                        <form action="./index.php" class="p-contact__form" method="post">
                            <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?>">
                            <dl class="p-contact__list">
                                <div class="p-contact__item">
                                    <dt class="p-contact__item-title">
                                        お名前<span>(必須)</span>
                                    </dt>
                                    <dd class="p-contact__item-input">
                                        <?php echo $_SESSION['fullname'] ?>
                                    </dd>
                                </div>
                                <div class="p-contact__item">
                                    <dt class="p-contact__item-title">
                                        メールアドレス<span>(必須)</span>
                                    </dt>
                                    <dd class="p-contact__item-input">
                                        <?php echo $_SESSION['email'] ?>
                                    </dd>
                                </div>
                                <div class="p-contact__item">
                                    <dt class="p-contact__item-title">
                                        電話番号
                                    </dt>
                                    <dd class="p-contact__item-input">
                                        <?php echo $_SESSION['tel'] ?>
                                    </dd>
                                </div>
                                <div class="p-contact__item">
                                    <dt class="p-contact__item-title">
                                        差し入れしたいタレント<span>(必須)</span>
                                    </dt>
                                    <dd class="p-contact__item-input">
                                        <?php echo $_SESSION['talent'] ?>
                                    </dd>
                                </div>
                                <div class="p-contact__item">
                                    <dt class="p-contact__item-title">
                                        プラン<span>(必須)</span>
                                    </dt>
                                    <dd class="p-contact__item-input">
                                        <?php echo $plan[$_SESSION['plan']] ?>
                                    </dd>
                                </div>
                                <div class="p-contact__item">
                                    <dt class="p-contact__item-title">
                                        備考欄
                                    </dt>
                                    <dd class="p-contact__item-input">
                                        <?php echo nl2br($_SESSION['massage']) ?>
                                    </dd>
                                </div>
                                <div class="p-contact__item-button">
                                    <input type="submit" name="back" value="戻る">
                                    <input type="submit" name="send" value="送信">
                                </div>
                            </dl>
                        </form>
                    </div>
                </div>
            </section>
        <?php } else { ?>

            <section id="contact" class="l-contact">
                <div class="l-contact__inner">
                    <div class="c-title__wrap">
                        <h1 class="c-title">CONTACT</h1>
                        <h2 class="c-subtitle">お見積り・お申込みフォーム</h2>
                    </div>
                    <div class="p-contact">

                        <!-- 完了画面 -->
                        <div class="p-contact__completion">
                            送信しました。お問い合わせありがとうございました。
                        </div>
                        <br><br>
                        <a href="./index.php">ホームに戻る</a>
                    </div>
                </div>
            </section>
        <?php } ?>

    </main>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="/coffeecar/js/script.js" defer></script>

</body>

</html>