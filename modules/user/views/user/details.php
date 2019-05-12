<?php

/* @var $this yii\web\View *
 * @var $profilePicModel \app\modules\core\models\ProfilePicForm
 * @var $model app\modules\core\models\User
 * @var $userInfo array
 * @var $games array
 * @var $mainTeams array
 * @var $subTeams array
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

use app\modules\miscellaneous\MetaClass;
use app\modules\miscellaneous\HelperClass;

\app\modules\user\assets\UserAsset::register($this);

/** Browser Title */
$this->title = $model->username . '\'s Player profile';

/** Site Canonicals */
$this->registerLinkTag(['rel' => 'canonical', 'href' => 'https://project-esport.gg' . Yii::$app->request->url]);

/** twitter/facebook/google Metatag */
MetaClass::writeMetaUser($this, $model, $userInfo['nationality']);

/** $synonym = ($gender_id == 1) ? 'synonym_m' : ($gender_id == 2) ? 'synonym_w' : 'synonym_d'; */

//$timestamp = time();
//$datum = date("d.m.Y - H:i", $timestamp);
//echo $datum;

?>

<div class="site-account">
	<!-- Avatar Panel -->
	<div class="col-lg-3 avatarPanel">
        <img class="avatar-logo" src="<?= $userInfo['playerImage']; ?>.webp" alt="" onerror="this.src='<?= $userInfo['playerImage']; ?>.png'">
        <?php if ($userInfo['isMySelfe']) : ?>
            <?php $form = ActiveForm::begin([
                'id' => 'profile-pic-form',
                // 'layout' => 'horizontal',
                'options' => ['enctype' => 'multipart/form-data'],
                'fieldConfig' => [
                    'template' => "<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-7\">{error}</div>"
                ],
            ]); ?>
            <?= $form->field($profilePicModel, 'id')->hiddenInput()->label(false); ?>
            <?= $form->field($profilePicModel, 'file')->fileInput() ?>
            <?= Html::submitButton(Yii::t('app', 'upload')); ?>
            <?php ActiveForm::end(); ?>
        <?php endif; ?>
    </div>

    <!-- Personal User Informations -->
    <div class="col-lg-7 playerPanel">
    	<!-- Header with User id and Nationality -->
    	<div class="header">
    		<img class="nationality-logo" src="<?= $userInfo['nationalityImg']; ?>.webp" alt="" onerror="this.src='<?= $userInfo['nationalityImg']; ?>.png'">
    		<span class="username"><?= $model->username; ?></span>
    		<span class="userid">ID:<?= $model->id ?></span>
    	</div>

    	<!-- Personal user Informations -->
    	<div class="userBody">
			<div class="entry clearfix">
				<div class="col-xs-5 col-sm-3 col-lg"><?= \app\modules\user\Module::t('account', 'name', $userInfo['language']->locale) ?>:</div>
				<div class="col-xs-7 col-sm-9 col-lg-9 context"><?= $model->pre_name . ' ' . $model->last_name; ?></div>
			</div>
			<div class="entry clearfix">
                <div class="col-xs-5 col-sm-3 col-lg-3"><?= \app\modules\user\Module::t('account', 'alias', $userInfo['language']->locale) ?>:</div>
                <div class="col-xs-7 col-sm-9 col-lg-9 context"><?= $model->username; ?></div>
            </div> 
            <div class="entry clearfix">
                <div class="col-xs-5 col-sm-3 col-lg-3"><?= \app\modules\user\Module::t('account', 'member_since', $userInfo['language']->locale) ?>:</div>
                <div class="col-xs-7 col-sm-9 col-lg-9 context"><?= $userInfo['memberSince']; ?></div>
            </div>
            <div class="entry clearfix">
                <div class="col-xs-5 col-sm-3 col-lg-3"><?= \app\modules\user\Module::t('account', 'age_gender', $userInfo['language']->locale) ?>:</div>
                <div class="col-xs-7 col-sm-9 col-lg-9 context"><?= $userInfo['age'] . " / " . $userInfo['gender']->getName(); ?></div>
            </div>
            <div class="entry clearfix">
                <div class="col-xs-5 col-sm-3 col-lg-3"><?= \app\modules\user\Module::t('account', 'nationality', $userInfo['language']->locale) ?>:</div>
                <div class="col-xs-7 col-sm-9 col-lg-9 context">
                	<img class="nationality-logo" src="<?= $userInfo['nationalityImg']; ?>.webp" alt="" onerror="this.src='<?= $userInfo['nationalityImg']; ?>.png'">
                    <?= $userInfo['nationality']->getName(); ?>                	
            	</div>
        	</div>
            <div class="entry clearfix">
                <div class="col-xs-5 col-sm-3 col-lg-3"><?= \app\modules\user\Module::t('account', 'city', $userInfo['language']->locale) ?>:</div>
                <div class="col-xs-7 col-sm-9 col-lg-9 context"><?= $model->city; ?></div>
            </div>
            <!-- User games/Platforms and id's -->
            <div class="clearfix">
            	<div class="col-lg-12 gameHeader"><?= \app\modules\user\Module::t('account', 'games', $userInfo['language']->locale) ?></div>
            </div>

            <div class="gameBody clearfix">
	            <div class="entry clearfix">
	            	<?php foreach ($games as $key => $game) : ?>
                            <div class="col-xs-5 col-sm-3 col-lg-3">
                                <img class="game-logo" src="<?= $game['gameImg']; ?>.webp" alt="" onerror="this.src='<?= $game['gameImg']; ?>.png'">
                                <img class="platform-logo" src="<?= $game['platform']; ?>.webp" alt="" onerror="this.src='<?= $game['platform']; ?>.png'">
                            </div>
	        			    <?php if($game['visible']) : ?>
                                <div class="col-xs-7 col-sm-9 col-lg-9 context"><?= $game['playerId']; ?></div>
                            <?php endif;
                        //endif;
	            	endforeach; ?>
	                <div class="col-xs-7 col-sm-9 col-lg-9 context"></div>
	            </div>
            </div>

			<!-- Teams where the user is member -->
			<div class="clearfix">
	            <div class="col-lg-12 teamHeader">My Team & Sub-Teams</div>
	        </div>
             <div class="teamBody clearfix">
            <div class="col-xs-12 col-sm-6">
                <div class="mainTeam">Main Team:</div>
                <?php foreach ($mainTeams as $key => $mainTeam): ?>
                    <div class="teamEntry clearfix">
                        <div class="col-lg-12">
                            <?= Html::a($mainTeam['team']->getName(), ['/teams/team-details', 'id' => $mainTeam['team']->getId()]); ?>
                            (<?= ($mainTeam['owner']) ? 'owner' : 'member'; ?>)
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="col-xs-12 col-sm-6">
                <div class="mainTeam">Sub Teams:</div>
                <?php foreach ($subTeams as $key => $subTeam): ?>
                    <div class="teamEntry clearfix">
                        <div class="col-lg-12"><?= Html::a($subTeam['team']->getTeamName(), ['/teams/sub-team-details', 'id' => $subTeam['team']->getId()]) . " (" . $subTeam['team']->getTournamentMode()->one()->getName() . ")"; ?>
                            (<?= ($subTeam['owner']) ? 'Captain' : (($subTeam['isSub']) ? 'substitute' : 'player'); ?>)
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            
        </div>
            
    	</div>
    </div>
</div>