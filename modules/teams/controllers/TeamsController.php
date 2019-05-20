<?php
/**
 * Created by PhpStorm.
 * User: Pierre Köhler
 * Date: 27.03.2019
 * Time: 09:49
 */

namespace app\modules\teams\controllers;

use app\components\BaseController;
use app\modules\teams\models\MainTeam;
use app\modules\teams\models\SubTeam;
use app\modules\teams\models\SubTeamMember;

use app\modules\user\models\formModels\ProfilePicForm;

use app\modules\teams\models\formModels\SubTeamDetailsForm;
use app\modules\teams\models\formModels\TeamDetailsForm;

use app\widgets\Alert;

use DateTime;

use Yii;
use yii\web\UploadedFile;
use yii\filters\AccessControl;


class TeamsController extends BaseController
{
    /**
     * Access Controll
     * ka was es macht aber es blockt nicht eingeloggt user
     *
     * @inheritdoc
     */
    /*public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ]
            ]
        ];
    }*/

    /**
     * Main Team Details Page
     *
     * @param null $id
     * @return string
     * @throws \Exception
     */
    public function actionTeamDetails($id = null)
    {
        $teamDetails = MainTeam::findOne(['id' => $id]);
        $subTeams = $teamDetails->getSubTeamsGroupByTournamentMode();

        /** @var ProfilePicForm $profilePicModel */
        $profilePicModel = new ProfilePicForm(ProfilePicForm::SCENARIO_MAINTEAM);
        $profilePicModel->id = $id;

        if ($profilePicModel->load(Yii::$app->request->post())) {
            $profilePicModel->file = UploadedFile::getInstance($profilePicModel, 'file');
            if ($profilePicModel->validate()) {
                $profilePicModel->save();
            }
        }

        /* Get Register Date and Age */
        $memberDateTime = new DateTime('2019-03-01');

        /** @var $teamInfo array */
        $teamInfo = [
            'isOwner' => (Yii::$app->user->identity != null && Yii::$app->user->identity->getId() == $teamDetails->owner_id) ? true : false,
            //'memberSince' => DateTime::createFromFormat('d.m.y', $user->dt_created),
            'memberSince' => $memberDateTime->format('d.m.y'),
            'language' => $teamDetails->getHeadQuaterId(),
            //'nationality' => $teamDetails->getHeadQuarterId(),
            'nationalityImg' => Yii::getAlias("@web") . '/images/nationality/' . $teamDetails->getHeadQuaterId() . '.png',
            'teamImage' => Yii::getAlias("@web") . '/images/teams/mainTeams/' . $teamDetails->getId()
        ];

        /* Set Correct Image Path */
        if (!is_file($_SERVER['DOCUMENT_ROOT'] . $teamInfo['teamImage'] . '.webp')) {
            if (!is_file($_SERVER['DOCUMENT_ROOT'] . $teamInfo['teamImage'] . '.png')) {
                $teamInfo['teamImage'] = Yii::getAlias("@web") . '/images/userAvatar/default';
            }
        }

        return $this->render('teamDetails',
            [
                'profilePicModel' => $profilePicModel,
                'teamDetails' => $teamDetails,
                'teamInfo' => $teamInfo,
                'subTeams' => $subTeams,
            ]);
    }

    /**
     * Sub Team Details Page
     *
     * @param null $id
     * @return string
     * @throws \Exception
     */
    public function actionSubTeamDetails($id = null)
    {
        $teamDetails = SubTeam::findOne(['id' => $id]);

        /** @var ProfilePicForm $profilePicModel */
        $profilePicModel = new ProfilePicForm(ProfilePicForm::SCENARIO_SUBTEAM);
        $profilePicModel->id = $id;

        if ($profilePicModel->load(Yii::$app->request->post())) {
            $profilePicModel->file = UploadedFile::getInstance($profilePicModel, 'file');
            if ($profilePicModel->validate()) {
                $profilePicModel->save();
            }
        }

        /* Get Register Date and Age */
        $memberDateTime = new DateTime('2019-03-01');

        /** @var $teamInfo array */
        $teamInfo = [
            'isOwner' => (Yii::$app->user->identity != null && Yii::$app->user->identity->getId() == $teamDetails->captain_id) ? true : false,
            'isDeputy' => (Yii::$app->user->identity != null && Yii::$app->user->identity->getId() == $teamDetails->deputy_id) ? true : false,
            //'memberSince' => DateTime::createFromFormat('d.m.y', $user->dt_created),
            'memberSince' => $memberDateTime->format('d.m.y'),
            //'language' => $teamDetails->getHeadQuarterId(),
            //'nationality' => $teamDetails->getHeadQuarterId(),
            //'nationalityImg' => Yii::getAlias("@web") . '/images/nationality/' . $teamDetails->getHeadQuarterId() . '.png',
            'teamImage' => Yii::getAlias("@web") . '/images/teams/subTeams/' . $teamDetails->getId()
        ];

        /* Set Correct Image Path */
        if (!is_file($_SERVER['DOCUMENT_ROOT'] . $teamInfo['teamImage'] . '.webp')) {
            if (!is_file($_SERVER['DOCUMENT_ROOT'] . $teamInfo['teamImage'] . '.png')) {
                $teamInfo['teamImage'] = Yii::getAlias("@web") . '/images/userAvatar/default';
            }
        }


        return $this->render('subTeamDetails',
            [
                'profilePicModel' => $profilePicModel,
                'teamDetails' => $teamDetails,
                'teamInfo' => $teamInfo,
            ]);
    }



    public function actionEditDetails($id, $isSub = false)
    {
        $teamDetails = SubTeam::findOne(['id' => $id]);

        if (Yii::$app->user->isGuest || Yii::$app->user->identity == null) {
            return $this->goHome();
        }

        if (Yii::$app->user->identity->getId() != $teamDetails->captain_id && Yii::$app->user->identity->getId() != $teamDetails->deputy_id) {
            return $this->goHome();
        }

        $model = ($isSub == true) ? new SubTeamDetailsForm() : new TeamDetailsForm();

        return $this->render('editDetails',
            [
                'id' => $id,
                //'genderList' => $genderList,
                //'languageList' => $languageList,
                //'nationalityList' => $nationalityList,
                'model' => $model
            ]);
    }

    public function actionDeleteMember($subTeamId, $userId, $isSub = false)
    {
        $model = null;

        if($isSub)
        {
            $model = SubTeamMember::find()->where(['user_id' => $userId, 'sub_team_id' => $subTeamId])->one();
        }
        else
        {
            //$model = TeamMember::find()->where(['user_id' => $userId, 'sub_team_id' => $subTeamId])->one();
        }
        
        if($model != null)
            $model->delete();
        //$model = UserGames::find()->where(['game_id' => $gameId, 'platform_id' => $platformId, 'user_id' => Yii::$app->user->identity->getId()])->one();

        //$model->visible = !$model->visible;
        //$model->save();

        //$gameName = Games::find()->where(['id' => $gameId])->one()->getName();

        Alert::addSuccess('User ' . $model->getUser()->one()->getUsername() . ' deleted from ' . $model->getSubTeam()->one()->getTournamentMode()->one()->getName() . ' Sub Team ' . $model->getSubTeam()->one()->getTeamName());
        //Alert::addError("Pierre ist doof"); 
        //Alert::addInfo("Pierre ist doof"); 

        $this->redirect("sub-team-details?id=" . $subTeamId);
    }
}