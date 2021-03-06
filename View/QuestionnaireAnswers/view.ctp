<?php
/**
 * questionnaire page setting view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Allcreator <info@allcreator.net>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

$jsQuestionPage = NetCommonsAppController::camelizeKeyRecursive($questionPage);
$jsAnswers = NetCommonsAppController::camelizeKeyRecursive($answers);
?>
<?php echo $this->element('Questionnaires.scripts'); ?>

<article id="nc-questionnaires-answer-<?php echo Current::read('Frame.id'); ?>"
		ng-controller="QuestionnairesAnswer"
		 ng-init="initialize(
		 <?php echo h(json_encode($jsQuestionPage)); ?>,
		 <?php echo h(json_encode($jsAnswers)); ?>)">

	<?php echo $this->element('Questionnaires.Answers/answer_header'); ?>

	<?php echo $this->element('Questionnaires.Answers/answer_test_mode_header'); ?>

	<?php if ($questionPage['page_sequence'] > 0): ?>
		<?php $progress = round((($questionPage['page_sequence']) / $questionnaire['Questionnaire']['page_count']) * 100); ?>
		<div class="row">
			<div class="col-sm-8">
			</div>
			<div class="col-sm-4">
				<div class="progress">
					<uib-progressbar class="progress-striped" value="<?php echo $progress ?>" type="warning"><?php echo $progress ?>%</uib-progressbar>
				</div>
			</div>
		</div>
	<?php endif ?>

	<?php
		echo $this->NetCommonsForm->create('QuestionnaireAnswer', array(
			'url' => NetCommonsUrl::actionUrlAsArray(array(
							'controller' => 'questionnaire_answers',
							'action' => 'view',
							Current::read('Block.id'),
							$questionnaire['Questionnaire']['key'],
							'frame_id' => Current::read('Frame.id')
		))));
		echo $this->NetCommonsForm->hidden('Frame.id');
		echo $this->NetCommonsForm->hidden('Block.id');
		echo $this->NetCommonsForm->hidden('QuestionnairePage.page_sequence');
		echo $this->NetCommonsForm->hidden('QuestionnairePage.route_number');
	?>

		<?php foreach($questionPage['QuestionnaireQuestion'] as $index => $question): ?>
			<div class="form-group
							<?php if ($this->Form->isFieldError('QuestionnaireAnswer.' . $question['key'] . '.0.answer_value')): ?>
							has-error
							<?php endif ?>">

				<?php if ($question['is_require'] == QuestionnairesComponent::REQUIRES_REQUIRE): ?>
					<div class="pull-left">
						<?php echo $this->element('NetCommons.required'); ?>
					</div>
				<?php endif ?>

				<label class="control-label">
					<?php echo h($question['question_value']); ?>
				</label>

				<p class="help-block">
					<?php echo $question['description']; ?>
				</p>

				<?php echo $this->QuestionnaireAnswer->answer($question); ?>
			</div>
		<?php endforeach; ?>


	<div class="text-center">
		<?php echo $this->NetCommonsForm->button(
		__d('net_commons', 'NEXT') . ' <span class="glyphicon glyphicon-chevron-right"></span>',
		array(
		'class' => 'btn btn-primary',
		'name' => 'next_' . '',
		)) ?>
	</div>
	<?php echo $this->NetCommonsForm->end(); ?>

</article>
     