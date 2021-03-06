<?php
/**
 * QuestionnaireSetting::saveQuestionnaireSetting()のテスト
 *
 * @property QuestionnaireSetting $QuestionnaireSetting
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsSaveTest', 'NetCommons.TestSuite');
App::uses('QuestionnairesComponent', 'Questionnaires.Controller/Component');

/**
 * QuestionnaireSetting::saveQuestionnaireSetting()のテスト
 *
 * @author Allcreator <info@allcreator.net>
 * @package NetCommons\Questionnaires\Test\Case\Model\QuestionnaireSetting
 */
class QuestionnaireSettingSaveQuestionnaireSettingTest extends NetCommonsSaveTest {

/**
 * Plugin name
 *
 * @var array
 */
	public $plugin = 'questionnaires';

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.questionnaires.questionnaire',
		'plugin.questionnaires.block_setting_for_questionnaire',
		'plugin.questionnaires.questionnaire_frame_setting',
		'plugin.questionnaires.questionnaire_frame_display_questionnaire',
		'plugin.questionnaires.questionnaire_page',
		'plugin.questionnaires.questionnaire_question',
		'plugin.questionnaires.questionnaire_choice',
		'plugin.questionnaires.questionnaire_answer_summary',
		'plugin.authorization_keys.authorization_keys'
	);

/**
 * Model name
 *
 * @var array
 */
	protected $_modelName = 'QuestionnaireSetting';

/**
 * Method name
 *
 * @var array
 */
	protected $_methodName = 'saveQuestionnaireSetting';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Current::$current['Frame']['key'] = 'frame_3';
		Current::write('Plugin.key', $this->plugin);
	}

/**
 * テストDataの取得
 *
 * @param string $blockKey block key
 * @param bool $useWorkflow use work flow
 * @return array
 */
	protected function _getData($blockKey, $useWorkflow) {
		Current::write('Block.key', $blockKey);
		$data = array(
			'QuestionnaireSetting' => array(
				'use_workflow' => $useWorkflow,
			),
		);
		return $data;
	}
/**
 * Saveのテスト
 *
 * @param array $data 登録データ
 * @dataProvider dataProviderSave
 * @return void
 */
	public function testSave($data) {
		$model = $this->_modelName;
		$method = $this->_methodName;

		//テスト実行
		$result = $this->$model->$method($data);
		$this->assertTrue($result);
	}

/**
 * SaveのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *
 * @return array
 */
	public function dataProviderSave() {
		return array(
			array($this->_getData('block_1', 1)),
			array($this->_getData('block_2', 0)),
		);
	}
/**
 * SaveのExceptionErrorのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *  - mockMethod Mockのメソッド
 *
 * @return array
 */
	public function dataProviderSaveOnExceptionError() {
		return array(
			array($this->_getData('block_1', 1), 'Blocks.BlockSetting', 'saveMany'),
		);
	}
/**
 * SaveのValidationErrorのDataProvider
 *
 * ### 戻り値
 *  - data 登録データ
 *  - mockModel Mockのモデル
 *
 * @return array
 */
	public function dataProviderSaveOnValidationError() {
		return array(
			array($this->_getData('block_1', 1), 'Questionnaires.QuestionnaireSetting'),
		);
	}
}
