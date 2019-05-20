<?php

namespace app\modules\teams\models\formModels;

use app\components\FormModel;

use app\widgets\Alert;

use app\modules\user\models\User;
use app\modules\user\models\Language;
use app\modules\user\models\Nationality;

use Yii;
use yii\db\Expression;
use yii\db\Exception;

/**
 * TeamDetailForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class TeamDetailsForm extends FormModel
{

}