<?php
/**
 * ブロック設定のElement
 *  - $model: モデル名
 *  - $action: アクションURL
 *  - $callback: コールバックelement
 *  - $callbackOptions: コールバックオプション
 *  - $cancelUrl: キャンセルURL
 *  - $options: Form->create()のオプション
 *  - `displayModified` 更新情報を表示するかどうか
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

if (! isset($options)) {
	$options = array();
}
if (isset($action)) {
	$options['url'] = $action;
}
if (! isset($cancelUrl)) {
	$cancelUrl = null;
}
if (! isset($callbackOptions)) {
	$callbackOptions = array();
}
if (! isset($displayModified)) {
	$displayModified = false;
}
?>

<?php echo $this->NetCommonsForm->create($model, Hash::merge(array(), $options)); ?>
	<div class="panel panel-default">
		<div class="panel-body">
			<?php echo $this->element('Blocks.form_hidden'); ?>
			<?php echo $this->element($callback, $callbackOptions); ?>
			<?php echo $this->element('Blocks.modifed_info', array('displayModified' => $displayModified)); ?>
		</div>

		<div class="panel-footer text-center">
			<?php echo $this->Button->cancelAndSave(__d('net_commons', 'Cancel'), __d('net_commons', 'OK'), $cancelUrl); ?>
		</div>
	</div>
<?php echo $this->NetCommonsForm->end();
