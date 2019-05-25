<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Welcome to Project eSport\'s';

$this->registerLinkTag(['rel' => 'canonical', 'href' => 'https://project-esport.gg' . Yii::$app->request->url]);

Yii::$app->MetaClass->writeMetaIndex($this, $this->title);

?>

<div class="site-index">
    <main>

        <div class="col-lg-9 welcome">
            Willkommen auf unserer Turnier Website.<br>
            Besuch uns doch bei Fragen auf unserem <a class="dclink" href="https://discord.gg/rk3qd9U">Discord</a> Server.
            <br>
            <br>
            Noch kein Team angelegt??? <br>
            Melde dich auf <a class="dclink" href="https://discord.gg/rk3qd9U">Discord</a> bei <b><i><u>Birnchen | Pierre#5366</u></i></b>.

            <div class="newsContainer">
                <div class="containerHeader">
                    <span class="headerKategorie"><img src="../images/gameLogos/1.webp">Rocket League</span>
                    <span class="headerTitle">Gerta Cup Season 3 1v1 Day 1</span>
                    <span class="headerDate">24.05.2019</span>
                </div>
                <div class="containerBody clearfix">
                    <div class="containerImage col-lg-3"><img src="https://pbs.twimg.com/media/D7Xrs37UYAAt_xi?format=jpg&name=small" alt="" style="width: 100%;     padding-top: 12px;"></div>
                    <div class="containerText col-lg-9">
                    Congratulations to <?= Html::a('xer02rl', ['/user/details', 'id' => '156']); ?> to this amazing win Yesterday in 1v1.
                    He beated up against 15 other participating Players on the first Day of the new Season.
                    <br><br>
                    Second Place goes to <?= Html::a('Dongiii', ['/user/details', 'id' => '162']); ?><br>
                    Third PLace goes to  <?= Html::a('NoAvian', ['/user/details', 'id' => '38']); ?><br>
                    </div>
                    <div class="containerAuthor">Birnchen</div>
                </div>
            </div>
        </div>



        <div class="col-lg-3">
            <?= Html::a('Tweets by project-eSport', 'https://twitter.com/' . 'esport_project' . '?ref_src=twsrc%5Etfw', ['class' => 'twitter-timeline', 'target' => '_blank', 'rel' =>'noopener', 'aria-label' => 'twitter-timeline', 'label' => 'twitter-timeline', 'data-lang' => 'en', 'data-height' => '400', 'data-theme' => 'light']); ?>
        </div>

    </main>
</div>