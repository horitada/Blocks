<?php
/**
 * BlockSetting Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Mitsuru Mutaguchi <mutaguchi@opensource-workshop.jp>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('ModelBehavior', 'Model');

/**
 * BlockSetting Behavior
 *
 * @author Mitsuru Mutaguchi <mutaguchi@opensource-workshop.jp>
 * @package NetCommons\Blocks\Model\Behavior
 */
class BlockSettingBehavior extends ModelBehavior {

/**
 * setup
 *
 * #### サンプルコード
 * ##### Model
 * ```php
 * public $actsAs = array(
 *	'Blocks.BlockSetting' => array(
 *		'fields' => array(
 *			'use_workflow',
 *			'use_comment',
 *			'use_comment_approval',
 *			'use_like',
 *			'use_unlike',
 *			'auto_play',
 *		),
 *	),
 * ),
 * ```
 *
 * @param Model $model モデル
 * @param array $config Configuration settings for $model
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		$this->settings = Hash::merge($this->settings, $config);
		$this->settings['fields'] = Hash::get($this->settings, 'fields', array());

		//$model->Block = ClassRegistry::init('Blocks.Block', true);
		$model->BlockSetting = ClassRegistry::init('Blocks.BlockSetting', true);
	}

/**
 * BlockSettingデータ新規作成
 *
 * @param Model $model モデル
 * @return array
 */
	public function createBlockSetting(Model $model) {
		//public function createBlockSetting(Model $model, $isRow = 1) {
		//* @param int $isRow 列持ち（横持ち）にするか
		$pluginKey = Current::read('Plugin.key');

		// room_idなし, block_keyなし
		$conditions = array(
			'plugin_key' => $pluginKey,
			'room_id' => null,
			'block_key' => null,
			//'field_name' => array_keys($this->settings['fields']),
			'field_name' => $this->settings['fields'],
		);
		$blockSettings = $model->BlockSetting->find('all', array(
			'recursive' => -1,
			'conditions' => $conditions,
		));
		//		if (!$isRow) {
			// 縦持ち
			// 新規登録時に不要な部分を除外
			$blockSettings = Hash::remove($blockSettings, '{n}.{s}.id');
			$blockSettings = Hash::remove($blockSettings, '{n}.{s}.created');
			$blockSettings = Hash::remove($blockSettings, '{n}.{s}.created_user');
			$blockSettings = Hash::remove($blockSettings, '{n}.{s}.modified');
			$blockSettings = Hash::remove($blockSettings, '{n}.{s}.modified_user');
			return $blockSettings;
		//		}
		//
		//		// 列持ち（横持ち）に変換
		//		$result['BlockSetting'] = Hash::combine($blockSettings,
		//			'{n}.BlockSetting.field_name',
		//			'{n}.BlockSetting.value');
		//
		//		return $result;
	}

/**
 * BlockSettingデータ取得
 *
 * @param Model $model モデル
 * @param int $isRow 列持ち（横持ち）にするか
 * @param int $roomId ルームID
 * @param string $blockKey ブロックキー
 * @return array
 */
	public function getBlockSetting(Model $model, $isRow = 1, $roomId = null, $blockKey = null) {
		if (is_null($roomId)) {
			$roomId = Current::read('Room.id');
		}
		if (is_null($blockKey)) {
			$blockKey = Current::read('Block.key');
		}
		$pluginKey = Current::read('Plugin.key');

		// room_idあり, block_keyあり
		$conditions = array(
			'plugin_key' => $pluginKey,
			'room_id' => $roomId,
			'block_key' => $blockKey,
			'field_name' => $this->settings['fields'],
		);
		$blockSettings = $model->BlockSetting->find('all', array(
			'recursive' => -1,
			'conditions' => $conditions,
		));
		if (!$blockSettings) {
			// 縦持ちで取得
			//$blockSettings = $this->createBlockSetting($model, 0);
			$blockSettings = $this->createBlockSetting($model);
		}
		if ($isRow) {
			// 横持ちに変換
			$result['BlockSetting'] = Hash::combine($blockSettings,
				'{n}.{s}.field_name',
				'{n}.{s}.value');
			return $result;
		}

		// 縦持ちでindexをfield_nameに変更
		$result['BlockSetting'] = Hash::combine($blockSettings, '{n}.{s}.field_name', '{n}.{s}');
		return $result;
		//			return $blockSettings;

		//		if (!$blockSettings) {
		//			return $blockSettings;
		//		}

		//		// 列持ち（横持ち）に変換
		//		$result['BlockSetting'] = Hash::combine($blockSettings,
		//			'{n}.BlockSetting.field_name',
		//			'{n}.BlockSetting.value');

		//		$conditions = array(
		//			$model->Block->alias . '.key' => Current::read('Block.key'),
		//		);
		//
		//		$block = $model->Block->find('first', array(
		//			'recursive' => -1,
		//			//'recursive' => 0,
		//			'conditions' => $conditions,
		//			//'order' => $model->Block->alias . '.id DESC'
		//		));
		//		$blockSetting = array();
		//		$result = Hash::merge($block, $blockSetting);
		//		return $result;
	}

/**
 * BlockSettingデータ保存
 *
 * ### 注意事項
 * この引数$dataは、リクエストの中身そのまま。
 * ```
 * //(例)各プラグインのBlockSettingControllerからの登録処理
 * array(
 * 	'BlockSetting' => array(
 * 		'use_comment' => array(
 * 			'id' => '1',
 * 			'plugin_key' => 'videos',
 * 			'room_id' => '2',
 * 			'block_key' => '2e86eb72e9cbd0ffa87ea23c81d4e3b7',
 * 			'field_name' => 'use_comment',
 * 			'value' => '1',
 * 			'type' => 'boolean',
 * 		),
 * 		'use_like' => array(
 * 			'id' => '1',
 * 			'plugin_key' => 'videos',
 * 			'room_id' => '2',
 * 			'block_key' => '2e86eb72e9cbd0ffa87ea23c81d4e3b7',
 * 			'field_name' => 'use_like',
 * 			'value' => '1',
 * 			'type' => 'boolean',
 * 		),
 * 		'use_unlike' => array(
 * 			'id' => '1',
 * 			'plugin_key' => 'videos',
 * 			'room_id' => '2',
 * 			'block_key' => '2e86eb72e9cbd0ffa87ea23c81d4e3b7',
 * 			'field_name' => 'use_unlike',
 * 			'value' => '0',
 * 			'type' => 'boolean',
 * 		),
 * 		'is_auto_play' => array(
 * 			'id' => '1',
 * 			'plugin_key' => 'videos',
 * 			'room_id' => '2',
 * 			'block_key' => '2e86eb72e9cbd0ffa87ea23c81d4e3b7',
 * 			'field_name' => 'auto_play',
 * 			'value' => '0',
 * 			'type' => 'numeric',
 * 		),
 * 	)
 * )
 * ```
 *
 * @param Model $model モデル
 * @param array $data received post data
 * @return mixed On success Model::$data if its not empty or true, false on failure
 * @throws InternalErrorException
 */
	public function saveBlockSetting(Model $model, $data) {
		//トランザクションBegin
		$model->BlockSetting->begin();

		//バリデーション
		$result = $this->_validateBlock($model, $data);
		if (! $result) {
			return false;
		}
		$data = $result;

		try {
			$saveData = Hash::extract($data, 'BlockSetting.{s}');

			if ($saveData && ! $model->BlockSetting->saveMany($saveData, ['validate' => false])) {
				throw new InternalErrorException('Failed - BlockSetting ' . __METHOD__);
			}

			//トランザクションCommit
			$model->BlockSetting->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$model->BlockSetting->rollback($ex);
		}
		return true;
	}

/**
 * ブロック設定のValidate処理
 *
 * @param Model $model モデル
 * @param array $data リクエストデータ配列
 * @return bool|array 正常な場合、登録不要なデータを削除して戻す。validateionErrorが空でない場合は、falseを返す。
 */
	protected function _validateBlock(Model $model, $data) {
		//$this->prepare();
		//array_keys($this->settings['fields']);

		foreach ($data['BlockSetting'] as $blockSetting) {
			if ($blockSetting['type'] === 'boolean') {
				if (! in_array($blockSetting['value'], ['0', '1'], true)) {
					$fieldName = $blockSetting['field_name'];
					$model->validationErrors['BlockSetting'][$fieldName]['value']
						= array(__d('net_commons', 'Invalid request.'));
				}

			} elseif ($blockSetting['type'] === 'numeric') {
				if (! is_numeric($blockSetting['value'])) {
					$fieldName = $blockSetting['field_name'];
					$model->validationErrors['BlockSetting'][$fieldName]['value']
						= array(__d('net_commons', 'Invalid request.'));
				}

			}
		}

		if (! $model->validationErrors) {
			return $data;
		} else {
			return false;
		}
	}

}
