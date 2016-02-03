<?php
/**
 * Questionnaire Model
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author AllCreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('QuestionnairesAppModel', 'Questionnaires.Model');

/**
 * Summary for Questionnaire Model
 */
class Questionnaire extends QuestionnairesAppModel {

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'NetCommons.OriginalKey',
		'Workflow.Workflow',
		'Workflow.WorkflowComment',
		'AuthorizationKeys.AuthorizationKey',
		'Questionnaires.QuestionnaireValidate',
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Block' => array(
			'className' => 'Blocks.Block',
			'foreignKey' => 'block_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'QuestionnairePage' => array(
			'className' => 'Questionnaires.QuestionnairePage',
			'foreignKey' => 'questionnaire_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => array('page_sequence' => 'ASC'),
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
	);

/**
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
	public function beforeValidate($options = array()) {
		$this->validate = Hash::merge($this->validate, array(
			'block_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => __d('net_commons', 'Invalid request.'),
					'on' => 'update', // Limit validation to 'create' or 'update' operations 新規の時はブロックIDがなかったりするから
				)
			),
			'title' => array(
					'rule' => 'notBlank',
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('questionnaires', 'Title')),
					'required' => true,
					'allowEmpty' => false,
					'required' => true,
			),
			'public_type' => array(
				'publicTypeCheck' => array(
					'rule' => array('inList', array(WorkflowBehavior::PUBLIC_TYPE_PUBLIC, WorkflowBehavior::PUBLIC_TYPE_LIMITED)),
					'message' => __d('net_commons', 'Invalid request.'),
				),
				'requireOtherFields' => array(
					'rule' => array('requireOtherFields', WorkflowBehavior::PUBLIC_TYPE_LIMITED, array('Questionnaire.publish_start', 'Questionnaire.publish_end'), 'OR'),
					'message' => __d('questionnaires', 'if you set the period, please set time.')
				)
			),
			'publish_start' => array(
				'checkDateTime' => array(
					'rule' => 'checkDateTime',
					'message' => __d('questionnaires', 'Invalid datetime format.')
				)
			),
			'publish_end' => array(
				'checkDateTime' => array(
					'rule' => 'checkDateTime',
					'message' => __d('questionnaires', 'Invalid datetime format.')
				),
				'checkDateComp' => array(
					'rule' => array('checkDateComp', '>=', 'publish_start'),
					'message' => __d('questionnaires', 'start period must be smaller than end period')
				)
			),
			'is_total_show' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'total_show_timing' => array(
				'inList' => array(
					'rule' => array('inList', array(QuestionnairesComponent::USES_USE, QuestionnairesComponent::USES_NOT_USE)),
					'message' => __d('net_commons', 'Invalid request.'),
				),
				'requireOtherFields' => array(
					'rule' => array('requireOtherFields', QuestionnairesComponent::USES_USE, array('Questionnaire.total_show_start_period'), 'AND'),
					'message' => __d('questionnaires', 'if you set the period, please set time.')
				)
			),
			'total_show_start_period' => array(
				'checkDateTime' => array(
					'rule' => 'checkDateTime',
					'message' => __d('questionnaires', 'Invalid datetime format.')
				)
			),
			'is_no_member_allow' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'is_anonymity' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'is_key_pass_use' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
				'requireOtherFieldsKey' => array(
					'rule' => array('requireOtherFields', QuestionnairesComponent::USES_USE, array('AuthorizationKey.authorization_key'), 'AND'),
					'message' => __d('questionnaires', 'if you set the use key phrase period, please set key phrase text.')
				),
				'authentication' => array(
					'rule' => array('requireOtherFields', QuestionnairesComponent::USES_USE, array('Questionnaire.is_image_authentication'), 'XOR'),
					'message' => __d('questionnaires', 'Authentication key setting , image authentication , either only one can not be selected.')
				)
			),
			'is_repeat_allow' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'is_image_authentication' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
				'authentication' => array(
					'rule' => array('requireOtherFields', QuestionnairesComponent::USES_USE, array('Questionnaire.is_key_pass_use'), 'XOR'),
					'message' => __d('questionnaires', 'Authentication key setting , image authentication , either only one can not be selected.')
				)
			),
			'is_answer_mail_send' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
		));

		parent::beforeValidate($options);
		// 最低でも１ページは存在しないとエラー
		if (! isset($this->data['QuestionnairePage'][0])) {
			$this->validationErrors['pickup_error'] = __d('questionnaires', 'please set at least one page.');
		} else {
			// ページデータが存在する場合
			// 配下のページについてバリデート
			$validationErrors = array();
			$this->QuestionnairePage = ClassRegistry::init('Questionnaires.QuestionnairePage', true);
			$maxPageIndex = count($this->data['QuestionnairePage']);
			$options['maxPageIndex'] = $maxPageIndex;
			foreach ($this->data['QuestionnairePage'] as $pageIndex => $page) {
				// それぞれのページのフィールド確認
				$this->QuestionnairePage->create();
				$this->QuestionnairePage->set($page);
				// ページシーケンス番号の正当性を確認するため、現在の配列インデックスを渡す
				$options['pageIndex'] = $pageIndex;
				if (! $this->QuestionnairePage->validates($options)) {
					$validationErrors['QuestionnairePage'][$pageIndex] = $this->QuestionnairePage->validationErrors;
				}
			}
			$this->validationErrors += $validationErrors;
		}
		// 引き続きアンケート本体のバリデートを実施してもらうためtrueを返す
		return true;
	}
/**
 * AfterFind Callback function
 *
 * @param array $results found data records
 * @param bool $primary indicates whether or not the current model was the model that the query originated on or whether or not this model was queried as an association
 * @return mixed
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function afterFind($results, $primary = false) {
		if ($this->recursive == -1) {
			return $results;
		}
		$this->QuestionnairePage = ClassRegistry::init('Questionnaires.QuestionnairePage', true);
		$this->QuestionnaireAnswerSummary = ClassRegistry::init('Questionnaires.QuestionnaireAnswerSummary', true);

		foreach ($results as &$val) {
			// この場合はcount
			if (! isset($val['Questionnaire']['id'])) {
				continue;
			}
			// この場合はdelete
			if (! isset($val['Questionnaire']['key'])) {
				continue;
			}

			$val['Questionnaire']['period_range_stat'] = $this->getPeriodStatus(
				isset($val['Questionnaire']['public_type']) ? $val['Questionnaire']['public_type'] : false,
				$val['Questionnaire']['publish_start'],
				$val['Questionnaire']['publish_end']);

			//
			// ページ配下の質問データも取り出す
			// かつ、ページ数、質問数もカウントする
			$val['Questionnaire']['page_count'] = 0;
			$val['Questionnaire']['question_count'] = 0;
			$this->QuestionnairePage->setPageToQuestionnaire($val);

			$val['Questionnaire']['all_answer_count'] = $this->QuestionnaireAnswerSummary->find('count', array(
				'conditions' => array(
					'questionnaire_key' => $val['Questionnaire']['key'],
					'answer_status' => QuestionnairesComponent::ACTION_ACT,
					'test_status' => QuestionnairesComponent::TEST_ANSWER_STATUS_PEFORM
				),
				'recursive' => -1
			));
		}
		return $results;
	}

/**
 * After frame save hook
 *
 * このルームにすでにアンケートブロックが存在した場合で、かつ、現在フレームにまだブロックが結びついてない場合、
 * すでに存在するブロックと現在フレームを結びつける
 *
 * @param array $data received post data
 * @return mixed On success Model::$data if its not empty or true, false on failure
 * @throws InternalErrorException
 */
	public function afterFrameSave($data) {
		// すでに結びついている場合は何もしないでよい
		if (!empty($data['Frame']['block_id'])) {
			return $data;
		}
		$frame = $data['Frame'];

		$this->begin();

		try {
			// ルームに存在するブロックを探す
			$block = $this->Block->find('first', array(
				'conditions' => array(
					'Block.room_id' => $frame['room_id'],
					'Block.plugin_key' => $frame['plugin_key'],
				)
			));
			// まだない場合
			if (empty($block)) {
				// 作成する
				$block = $this->Block->save(array(
					'room_id' => $frame['room_id'],
					'language_id' => $frame['language_id'],
					'plugin_key' => $frame['plugin_key'],
				));
				if (! $block) {
					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				}
				Current::$current['Block'] = $block['Block'];
			}

			$this->loadModels([
				'Frame' => 'Frames.Frame',
				'QuestionnaireSetting' => 'Questionnaires.QuestionnaireSetting',
			]);
			$data['Frame']['block_id'] = $block['Block']['id'];
			if (! $this->Frame->save($data)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			Current::$current['Frame']['block_id'] = $block['Block']['id'];

			$blockSetting = $this->QuestionnaireSetting->create();
			$blockSetting['QuestionnaireSetting']['block_key'] = $block['Block']['key'];
			$this->QuestionnaireSetting->saveQuestionnaireSetting($blockSetting);

			$this->commit();
		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback();
			//エラー出力
			CakeLog::error($ex);
			throw $ex;
		}
		return $data;
	}
/**
 * geQuestionnairesList
 * get questionnaires by specified block id and specified user id limited number
 *
 * @param array $conditions find condition
 * @param array $options 検索オプション
 * @return array
 */
	public function getQuestionnairesList($conditions, $options = array()) {
		//$limit = QuestionnairesComponent::QUESTIONNAIRE_DEFAULT_DISPLAY_NUM_PER_PAGE}, $offset = 0, $sort = 'modified DESC') {
		// 絞込条件
		$baseConditions = $this->getBaseCondition();
		$conditions = Hash::merge($baseConditions, $conditions);

		// 取得オプション
		$this->QuestionnaireFrameSetting = ClassRegistry::init('Questionnaires.QuestionnaireFrameSetting', true);
		$defaultOptions = $this->QuestionnaireFrameSetting->getQuestionnaireFrameSettingConditions(Current::read('Frame.key'));
		$options = Hash::merge($defaultOptions, $options);
		$list = $this->find('all', array(
			'recursive' => 0,
			'conditions' => $conditions,
			$options
		));
		return $list;
	}

/**
 * get index sql condition method
 *
 * @param array $addConditions 追加条件
 * @return array
 */
	public function getCondition($addConditions = array()) {
		// ベースとなる権限のほかに現在フレームに表示設定されているアンケートか見ている
		$conditions = $this->getBaseCondition($addConditions);

		$frameDisplay = ClassRegistry::init('Questionnaires.QuestionnaireFrameDisplayQuestionnaires');
		$keys = $frameDisplay->find(
			'list',
			array(
				'conditions' => array('QuestionnaireFrameDisplayQuestionnaires.frame_key' => Current::read('Frame.key')),
				'fields' => array('QuestionnaireFrameDisplayQuestionnaires.questionnaire_key'),
				'recursive' => -1
			)
		);
		$conditions['Questionnaire.key'] = $keys;

		if ($addConditions) {
			$conditions = array_merge($conditions, $addConditions);
		}
		return $conditions;
	}

/**
 * get index sql condition method
 *
 * @param array $addConditions 追加条件
 * @return array
 */
	public function getBaseCondition($addConditions = array()) {
		$conditions = $this->getWorkflowConditions(array(
			'block_id' => Current::read('Block.id'),
		));

		if (! Current::read('User.id')) {
			$conditions['is_no_member_allow'] = QuestionnairesComponent::PERMISSION_PERMIT;
		}

		if ($addConditions) {
			$conditions = array_merge($conditions, $addConditions);
		}
		return $conditions;
	}

/**
 * saveQuestionnaire
 * save Questionnaire data
 *
 * @param array &$questionnaire questionnaire
 * @throws InternalErrorException
 * @return bool
 */
	public function saveQuestionnaire(&$questionnaire) {
		$this->loadModels([
			'QuestionnairePage' => 'Questionnaires.QuestionnairePage',
			'QuestionnaireFrameDisplayQuestionnaire' => 'Questionnaires.QuestionnaireFrameDisplayQuestionnaire',
			'QuestionnaireAnswerSummary' => 'Questionnaires.QuestionnaireAnswerSummary',
		]);

		//トランザクションBegin
		$this->begin();

		try {
			$status = $questionnaire['Questionnaire']['status'];
			$this->create();
			// アンケートは履歴を取っていくタイプのコンテンツデータなのでSave前にはID項目はカット
			// （そうしないと既存レコードのUPDATEになってしまうから）
			// （ちなみにこのカット処理をbeforeSaveで共通でやってしまおうとしたが、
			//   beforeSaveでIDをカットしてもUPDATE動作になってしまっていたのでここに置くことにした)
			$questionnaire = Hash::remove($questionnaire, 'Questionnaire.id');

			$this->set($questionnaire);
			if (!$this->validates()) {
				return false;
			}

			$saveQuestionnaire = $this->save($questionnaire, false);
			if (! $saveQuestionnaire) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$questionnaireId = $this->id;

			// ページ以降のデータを登録
			$questionnaire = Hash::insert($questionnaire, 'QuestionnairePage.{n}.questionnaire_id', $questionnaireId);
			if (! $this->QuestionnairePage->saveQuestionnairePage($questionnaire['QuestionnairePage'])) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			// フレーム内表示対象アンケートに登録する
			if (! $this->QuestionnaireFrameDisplayQuestionnaire->saveDisplayQuestionnaire(array(
				'questionnaire_key' => $saveQuestionnaire['Questionnaire']['key'],
				'frame_key' => Current::read('Frame.key')
			))) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			// これまでのテスト回答データを消す
			$this->QuestionnaireAnswerSummary->deleteTestAnswerSummary($saveQuestionnaire['Questionnaire']['key'], $status);

			$this->commit();
		} catch (Exception $ex) {
			$this->rollback();
			CakeLog::error($ex);
			throw $ex;
		}
		return $questionnaire;
	}

/**
 * deleteQuestionnaire
 * Delete the questionnaire data set of specified ID
 *
 * @param array $data post data
 * @throws InternalErrorException
 * @return bool
 */
	public function deleteQuestionnaire($data) {
		$this->loadModels([
			'QuestionnaireFrameDisplayQuestionnaire' => 'Questionnaires.QuestionnaireFrameDisplayQuestionnaire',
			'QuestionnaireAnswerSummary' => 'Questionnaires.QuestionnaireAnswerSummary',
		]);
		$this->begin();
		try {
			// アンケート質問データ削除
			if (! $this->deleteAll(array(
					'Questionnaire.key' => $data['Questionnaire']['key']), true, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			//コメントの削除
			$this->deleteCommentsByContentKey($data['Questionnaire']['key']);

			// アンケート表示設定削除
			if (! $this->QuestionnaireFrameDisplayQuestionnaire->deleteAll(array(
				'questionnaire_key' => $data['Questionnaire']['key']), true, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			// アンケート回答削除
			if (! $this->QuestionnaireAnswerSummary->deleteAll(array(
				'questionnaire_key' => $data['Questionnaire']['key']), true, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$this->commit();
		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback();
			//エラー出力
			CakeLog::error($ex);
			throw $ex;
		}

		return true;
	}
/**
 * saveExportKey
 * update export key
 *
 * @param int $questionnaireId id of questionnaire
 * @param string $exportKey exported key ( finger print)
 * @throws InternalErrorException
 * @return bool
 */
	public function saveExportKey($questionnaireId, $exportKey) {
		$this->begin();
		try {
			$this->id = $questionnaireId;
			$this->saveField('export_key', $exportKey);
			$this->commit();
		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback();
			//エラー出力
			CakeLog::error($ex);
			throw $ex;
		}
		return true;
	}
/**
 * hasPublished method
 *
 * @param array $questionnaire questionnaire data
 * @return int
 */
	public function hasPublished($questionnaire) {
		if (isset($questionnaire['Questionnaire']['key'])) {
			$isPublished = $this->find('count', array(
				'recursive' => -1,
				'conditions' => array(
					'is_active' => true,
					'key' => $questionnaire['Questionnaire']['key']
				)
			));
		} else {
			$isPublished = 0;
		}
		return $isPublished;
	}

/**
 * clearQuestionnaireId アンケートデータからＩＤのみをクリアする
 *
 * @param array &$questionnaire アンケートデータ
 * @return void
 */
	public function clearQuestionnaireId(&$questionnaire) {
		foreach ($questionnaire as $qKey => $q) {
			if (is_array($q)) {
				$this->clearQuestionnaireId($questionnaire[$qKey]);
			} elseif (preg_match('/^id$/', $qKey) ||
				preg_match('/^key$/', $qKey) ||
				preg_match('/^created(.*?)/', $qKey) ||
				preg_match('/^modified(.*?)/', $qKey)) {
				unset($questionnaire[$qKey]);
			}
		}
	}
}
