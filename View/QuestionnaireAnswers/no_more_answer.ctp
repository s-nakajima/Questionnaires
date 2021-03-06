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
?>
<article>
<?php echo $this->element('Questionnaires.Answers/answer_header'); ?>
<?php echo __d('questionnaires', 'you will not be able to answer this questionnaire.'); ?>
<?php echo $this->QuestionnaireUtil->getAggregateButtons($questionnaire, array(
	'title' => '&nbsp;' . __d('questionnaires', 'Aggregate'),
	'icon' => 'stats'
)); ?>
<?php if ($displayType == QuestionnairesComponent::DISPLAY_TYPE_LIST): ?>
	<div class="text-center">
        <?php echo $this->LinkButton->toList(); ?>
	</div>
<?php endif; ?>
</article>
