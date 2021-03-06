<?php

namespace app\modules\tournamenttrees\models;

use yii\db\ActiveRecord;

use app\modules\teams\models\SubTeam;
use app\modules\tournaments\models\Tournament;
use app\modules\user\models\User;

use yii\helpers\Html;

/**
 * Class Bracket
 * @package app\modules\tournamenttree\models
 *
 * @property int $id
 * @property int $encounter_id
 * @property int $tournament_id
 * @property int $best_of default:3
 * @property int $tournament_round
 * @property int $live_stream
 * @property boolean $is_winner_bracket default:true
 * @property SubTeam|NULL $team_1_id
 * @property SubTeam|NULL $team_2_id
 * @property User|NULL $user_1_id
 * @property User|NULL $user_2_id
 * @property Bracket|NULL $winner_bracket
 * @property Bracket|NULL $looser_bracket
 * @property winner int
 */
class Bracket extends ActiveRecord
{
	/**
     * @return string
     */
    public static function tableName()
    {
        return 'bracket'; // Tabellenname gegebenenfalls ändern??
    }

    /**
     * @return array
     */
    private function getLiveStreamClasses() {
    	return [
    		'',
    		'stream1',
    		'stream2',
    		'stream3',
    	];
    }

	/**
	 * @return int id
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return int id
	 */
	public function getEncounterId()
	{
		return $this->encounter_id;
	}

	/**
	 * @return ActiveQuery
	 */
	public function getTournament()
	{
		return $this->hasOne(Tournament::className(), ['id' => 'tournament_id']);
	}

	/**
	 * @return int best_of
	 */
	public function getBestOf()
	{
		return $this->best_of;
	}

	/**
	 * @return int tournament_round
	 */
	public function getTournamentRound()
	{
		return $this->tournament_round;
	}

	/**
	 * @return string
	 */
	public function getLiveStreamClass()
	{
		$classes = $this->getLiveStreamClasses();
		return $classes[$this->live_stream];
	}

	/**
	 * @return boolean is_winner_bracket
	 */
	public function getIsWinnerBracket()
	{
		return $this->is_winner_bracket;
	}

	/**
	 * @return ActiveQuery|NULL
	 */
	public function getTeam1()
	{
		return $this->hasOne(SubTeam::className(), ['id' => 'team_1_id']);
	}

	/**
	 * @return ActiveQuery|NULL
	 */
	public function getTeam2()
	{
		return $this->hasOne(SubTeam::className(), ['id' => 'team_2_id']);
	}

	/**
	 * @return bool
	 */
	public function isTeam2Manager($id)
	{
		$team = $this->hasOne(SubTeam::className(), ['id' => 'team_2_id']);
		
		//if($team->getTeamCaptainId() == $id || $team->getTeamDeputyId() == $id)
			return true;
	}

	/**
	 * @return ActiveQuery|NULL
	 */
	public function getPlayer1()
	{
		return $this->hasOne(User::className(), ['id' => 'user_1_id']);
	}

	/**
	 * @return ActiveQuery|NULL
	 */
	public function getPlayer2()
	{
		return $this->hasOne(User::className(), ['id' => 'user_2_id']);
	}

	/**
	 * @return ActiveQuery|NULL
	 */
	public function getWinnerBracket()
	{
		return $this->hasOne(Bracket::className(), ['id' => 'winner_bracket']);
	}

	/**
	 * @return ActiveQuery|NULL
	 */
	public function getLooserBracket()
	{
		return $this->hasOne(Bracket::className(), ['id' => 'looser_bracket']);
	}

	/**
	 * @return int
	 */
	public function getWinner()
	{
		return $this->winner;
	}

	/**
	 * @return array
	 */
	public function getBracketRefs()
	{
		$winner = $this->hasMany(Bracket::className(), ['winner_bracket' => 'id'])->all();
		$looser = $this->hasMany(Bracket::className(), ['looser_bracket' => 'id'])->all();

		$brackets = [];
		while ($tmp = array_shift($looser)) {
			$brackets[] = ['type' => 'looser', 'bracket' => $tmp];
		}
		while ($tmp = array_shift($winner)) {
			$brackets[] = ['type' => 'winner', 'bracket' => $tmp];
		}

		return $brackets;
	}

	/**
	 * @return array
	 */
	public function getParticipants()
	{
		$return = [];

		$refs = $this->getBracketRefs();

		$class = NULL;
		$vars = NULL;
		if ($this->getTeam1()->one() !== NULL) {
			$class = SubTeam::className();
			$vars = ['id' => 'team_1_id'];
		}
		if ($this->getPlayer1()->one() !== NULL) {
			$class = User::className();
			$vars = ['id' => 'user_1_id'];
		}

		if (NULL === $class) {

			if (count($refs) === 0) {
				$return[] = NULL;
			} else {
				$preBracket = array_shift($refs);
				$preText = ucfirst($preBracket['type']);
				$preRound = $preBracket['bracket']->getTournamentRound();
				$preRound = (998 == $preRound) ? 'Finale' : $preRound;
				$return[] = $preText . ' Runde ' . $preRound . ' Bracket ' . $preBracket['bracket']->getEncounterId();
			}

		} else {

			array_shift($refs);
			$slot = $this->hasOne($class, $vars)->one();
			if ($slot instanceof User) {

	            $imgPath = '/images/userAvatar/' . $slot->id;

	            if (!is_file($_SERVER['DOCUMENT_ROOT'] . '/' . $imgPath . '.webp')) {
	                if (!is_file($_SERVER['DOCUMENT_ROOT'] . '/' . $imgPath . '.png')) {
	                    $imgPath = '/images/userAvatar/default';
	                }
	            }

	            $img = Html::img($imgPath . '.webp', ['class' => 'bracketIcon', 'alt' => "profilePic", 'aria-label' => 'profilePic', 'onerror' =>'this.src="' . $imgPath . '.png"' ]);
				$return[] = $img . $slot->getUsername();
			} else if ($slot instanceof SubTeam) {

	            $imgPath = '/images/teams/subTeams/' . $slot->id;

	            if (!is_file($_SERVER['DOCUMENT_ROOT'] . '/' . $imgPath . '.webp')) {
	                if (!is_file($_SERVER['DOCUMENT_ROOT'] . '/' . $imgPath . '.png')) {
	                    $imgPath = '/images/userAvatar/default';
	                }
	            }

	            $img = Html::img($imgPath . '.webp', ['class' => 'bracketIcon', 'alt' => "profilePic", 'aria-label' => 'profilePic', 'onerror' =>'this.src="' . $imgPath . '.png"' ]);

				$show = $slot->getTeamShortCode() ;
				if (empty($show)) {

					$show = $slot->getName();
					$show = str_replace(' ', '', $show);
					$show = substr($show, 0, 6);
				}
				$return[] = $img . $show;
			} else {
				$return[] = '';
			}

		}


		$class = NULL;
		$vars = NULL;
		if ($this->getTeam2()->one() !== NULL) {
			$class = SubTeam::className();
			$vars = ['id' => 'team_2_id'];
		}
		if ($this->getPlayer2()->one() !== NULL) {
			$class = User::className();
			$vars = ['id' => 'user_2_id'];
		}

		if (NULL === $class) {

			if (count($refs) === 0) {
				$return[] = NULL;
			} else {
				$preBracket = array_shift($refs);
				$preText = ucfirst($preBracket['type']);
				$preRound = $preBracket['bracket']->getTournamentRound();
				$preRound = (998 == $preRound) ? 'Finale' : $preRound;
				$return[] = $preText . ' Runde ' . $preRound . ' Bracket ' . $preBracket['bracket']->getEncounterId();
			}

		} else {

			$slot = $this->hasOne($class, $vars)->one();
			if ($slot instanceof User) {

	            $imgPath = '/images/userAvatar/' . $slot->id;

	            if (!is_file($_SERVER['DOCUMENT_ROOT'] . '/' . $imgPath . '.webp')) {
	                if (!is_file($_SERVER['DOCUMENT_ROOT'] . '/' . $imgPath . '.png')) {
	                    $imgPath = '/images/userAvatar/default';
	                }
	            }

	            $img = Html::img($imgPath . '.webp', ['class' => 'bracketIcon', 'alt' => "profilePic", 'aria-label' => 'profilePic', 'onerror' =>'this.src="' . $imgPath . '.png"' ]);
				$return[] = $img . $slot->getUsername();
			} else if ($slot instanceof SubTeam) {

	            $imgPath = '/images/teams/subTeams/' . $slot->id;

	            if (!is_file($_SERVER['DOCUMENT_ROOT'] . '/' . $imgPath . '.webp')) {
	                if (!is_file($_SERVER['DOCUMENT_ROOT'] . '/' . $imgPath . '.png')) {
	                    $imgPath = '/images/userAvatar/default';
	                }
	            }

	            $img = Html::img($imgPath . '.webp', ['class' => 'bracketIcon', 'alt' => "profilePic", 'aria-label' => 'profilePic', 'onerror' =>'this.src="' . $imgPath . '.png"' ]);

				$show = $slot->getTeamShortCode();
				if (empty($show)) {

					$show = $slot->getName();
					$show = str_replace(' ', '', $show);
					$show = substr($show, 0, 6);
				}
				$return[] = $img . $show;
			} else {
				$return[] = '';
			}

		}

		return $return;
	}

	/**
	 * @param User | NULL
	 * @param SubTeam | NULL
	 * @param int
	 * @return bool
	 */
	public function isManageable($user, $player_one_two)
	{
		if (NULL === $user) {
			return false;
		}

		if ($this->getPlayer1()->one() !== NULL) {

			$p1 = $this->getPlayer1()->one();
			$p2 = $this->getPlayer2()->one();

			if (1 === $player_one_two && $p1 !== NULL && $p1->getId() == $user->getId()) {
				return true;
			}

			if (2 === $player_one_two && $p2 !== NULL && $p2->getId() == $user->getId()) {
				return true;
			}

			return false;
		}

		if ($this->getTeam1()->one() !== NULL) {

			$team1 = $this->getTeam1()->one();
			$team2 = $this->getTeam2()->one();

			if (1 === $player_one_two) {
				
				if ($team1 !== NULL && ($team1->getTeamCaptainId() == $user->getId() || $team1->getTeamDeputyId() == $user->getId())) {
					return true;
				}
				return false;

			}
			if (2 === $player_one_two) {

				if ($team2 !== NULL && ($team2->getTeamCaptainId() == $user->getId() || $team2->getTeamDeputyId() == $user->getId())) {
					return true;
				}
				return false;

			}

			return false;
		}

        return false;
	}

	/**
	 * @return bool
	 */
	public function checkIfBracketClosed()
	{
		return true;
	}

	/**
	 * @param User|SubTeam
	 */
	public function setSpieler($participant)
	{

		if ($participant instanceof User) {

			if (NULL === $this->user_1_id) {
				$this->user_1_id = $participant->getId();
			} else {
				$this->user_2_id = $participant->getId();
			}

		} elseif ($participant instanceof SubTeam) {

			if (NULL === $this->team_1_id) {
				$this->team_1_id = $participant->getId();
			} else {
				$this->team_2_id = $participant->getId();
			}

		}

	}

	/**
	 * @param int
	 * @param int
	 */
	public function setSpielerByBackRef($id, $type, $preBracketId) {
		
		$refs = $this->getBracketRefs();

		$bracket1 = reset($refs);
		if (false === $bracket1) {
			return;
		}
		$bracket2 = next($refs);

		$set1 = true;
		$set2 = true;
		if ($bracket1['bracket']->getId() === $bracket2['bracket']->getId()) {

			if ($this->user_1_id !== NULL || $this->team_1_id !== NULL) {
				$set1 = false;
			}

			if ($this->user_2_id !== NULL || $this->team_2_id !== NULL) {
				$set2 = false;
			}

		}

		if ($bracket1['bracket']->getId() === $preBracketId && true === $set1) {
			if ($type === 'user') {
				$this->user_1_id = $id;
			} else {
				$this->team_1_id = $id;
			}
		}

		if ($bracket2['bracket']->getId() === $preBracketId && true === $set2) {
			if ($type === 'user') {
				$this->user_2_id = $id;
			} else {
				$this->team_2_id = $id;
			}
		}
		
		$this->update();

	}

	/**
	 * @param int 
	 */
	public function movePlayersNextRound($winnerNumber) {

		if ($winnerNumber === 1) {
			$type = (NULL !== $this->user_1_id) ? 'user' : 'team';
		} else {
			$type = (NULL !== $this->user_2_id) ? 'user' : 'team';
		}

		if ($winnerNumber == 1) {
			$winnerField = ('user' === $type) ? 'user_1_id' : 'team_1_id';
			$looserField = ('user' === $type) ? 'user_2_id' : 'team_2_id';
		} else {
			$winnerField = ('user' === $type) ? 'user_2_id' : 'team_2_id';
			$looserField = ('user' === $type) ? 'user_1_id' : 'team_1_id';
		}

		$this->winner = $winnerNumber;
		$this->update();

		if ($this->tournament_round === 999) {
			return;
		}

		if ($this->tournament_round === 998 && $winnerNumber == 1) {
			return;
		}

		$winnerBracket = $this->getWinnerBracket()->one();
		$looserBracket = $this->getLooserBracket()->one();

		$winnerBracket->setSpielerByBackRef($this->$winnerField, $type, $this->getId());
		if (NULL !== $looserBracket) {
			$looserBracket->setSpielerByBackRef($this->$looserField, $type, $this->getId());
		}

	}

	/**
	 * @return boolean|int
	 */
	public function checkisFinished()
	{
		$winner = $this->getWinner();
		if ($winner !== NULL) {
			return $winner;
		}

		$type = (NULL !== $this->user_1_id) ? 'user' : 'team';
		$winnerBracket = $this->getWinnerBracket()->one();
		if (false === $winnerBracket || NULL === $winnerBracket) {
			return false;
		}

		if ($type === 'user') {

			if ($winnerBracket->user_1_id != NULL && $this->user_1_id == $winnerBracket->user_1_id || $winnerBracket->user_2_id != NULL && $this->user_1_id == $winnerBracket->user_2_id) {
				return 1;
			} else if ($winnerBracket->user_1_id != NULL && $this->user_2_id == $winnerBracket->user_1_id || $winnerBracket->user_2_id != NULL && $this->user_2_id == $winnerBracket->user_2_id) {
				return 2;
			} 

		} else {

			if ($winnerBracket->team_1_id != NULL && $this->team_1_id == $winnerBracket->team_1_id || $winnerBracket->team_2_id != NULL && $this->team_1_id == $winnerBracket->team_2_id) {
				return 1;
			} else if ($winnerBracket->team_1_id != NULL && $this->team_2_id == $winnerBracket->team_1_id || $winnerBracket->team_2_id != NULL && $this->team_2_id == $winnerBracket->team_2_id) {
				return 2;
			} 

		}

		return false;
	}

	/**
	 */
	public function changeLiveStream()
	{
		$classes = $this->getLiveStreamClasses();
		$this->live_stream = ($this->live_stream + 1) % count($classes);
		$this->update();
	}

	/**
	 * @param int
	 * @return static|null
	 */
	public static function getById($bracketId)
	{
		return static::findOne(['id' => $bracketId]);
	}

	/**
	 * @param int
	 * @return static|null
	 */
	public static function getAllByTournament($tournament_id)
	{
		return static::findAll(['tournament_id' => $tournament_id]);
	}

	/**
	 * @param int
	 * @return array
	 */
	public static function getAllByTournamentFormatted($tournament_id)
	{
		$brackets = self::getAllByTournament($tournament_id);
		$out = [
			'winner' => [],
			'looser' => [],
		];

		$startTime = NULL;
		$firstBracket = reset($brackets);
		if (false !== $firstBracket) {
			$tournament = $firstBracket->getTournament()->one();
			$startTime = new \DateTime($tournament->getDtStartingTime());
		}

		foreach ($brackets as $key => $bracket) {

			$firstLevel = ($bracket->getIsWinnerBracket()) ? 'winner' : 'looser';
			$secondLevel = $bracket->getTournamentRound();
			$secondLevel = ($secondLevel === 998) ? 'Finale' : $secondLevel;
			$secondLevel = ($secondLevel === 999) ? 'Finale (optional)' : $secondLevel;

			if (!array_key_exists($secondLevel, $out[$firstLevel])) {
				$out[$firstLevel][$secondLevel] = ['startTime' => '', 'brackets' => []];
			}

			$out[$firstLevel][$secondLevel]['brackets'][] = $bracket;
		}

		if (count($out['winner']) == 0) {
			return $out;
		}

		$bracketMode = $tournament->getBracketMode()->one();

		$winnerKeys = array_keys($out['winner']);
		$looserKeys = array_keys($out['looser']);
		$maxLooserRound = ($bracketMode->getName() == 'Double Elimination') ? max($looserKeys) : max($winnerKeys);

		$allKeys = array_merge($winnerKeys, $looserKeys);

		foreach ($allKeys as $key => $keyVal) {

			if ($keyVal == 'Finale') {
				$keyRound = $maxLooserRound + 1;
			} else if ($keyVal == 'Finale (optional)') {
				$keyRound = $maxLooserRound + 2;
			} else {
				$keyRound = $keyVal;
			}

			$keyRound--;

			$min = $keyRound * 30;

			if ($keyVal == 'Finale') {
				$min+= 15;
			}
			if ($keyVal == 'Finale (optional)') {
				$min+= 30;
			}

			$interval = new \DateInterval('PT' . $min . 'M');
			$startTime->add($interval);
			if (array_key_exists($keyVal, $out['winner'])) {
				$out['winner'][$keyVal]['startTime'] = $startTime->format('H:i');
			}
			if (array_key_exists($keyVal, $out['looser'])) {
				$out['looser'][$keyVal]['startTime'] = $startTime->format('H:i');
			}
			$startTime->sub($interval);
		}

		return $out;
	}

	/**
	 * @param int
	 * @return static|null
	 */
	public static function getBracketByWinner($bracketId) {
		return static::findOne(['winner_bracket' => $bracketId]);
	}

	/**
	 * @param int
	 * @return static|null
	 */
	public static function getBracketByLooser($bracketId) {
		return static::findOne(['looser_bracket' => $bracketId]);
	}

	/**
	 * @param int
	 */
	public static function clearForTournament($tournament_id) {
		$brackets = self::getAllByTournament($tournament_id);
		foreach ($brackets as $key => $bracket) {
			$bracket->delete();
		}
	}

}