<?php
/**
 * Block Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('Block', 'Blocks.Model');

/**
 * Block Test Case
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Blocks\Test\Case\Model
 */
class BlockDeleteTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.blocks.block',
		'plugin.boxes.box',
		'plugin.frames.frame',
		'plugin.frames.plugin',
		'plugin.m17n.language',
		'plugin.rooms.room',
		'plugin.users.user',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Block = ClassRegistry::init('Blocks.Block');
		$this->Frame = ClassRegistry::init('Frames.Frame');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Block);
		unset($this->Frame);

		parent::tearDown();
	}

/**
 * Expect delete block
 *
 * @return  void
 */
	public function testDeleteBlock() {
		$blockKey = 'block_1';
		$blockIds = $this->Block->find('list', array(
				'recursive' => -1,
				'conditions' => array('key' => $blockKey)
			));

		//削除処理実行
		$this->Block->deleteBlock($blockKey);

		//チェック
		$this->assertEquals(0, $this->Block->find('count', array(
					'recursive' => -1,
					'conditions' => array('key' => $blockKey)
				))
			);
		$this->assertEquals(0, $this->Frame->find('count', array(
					'recursive' => -1,
					'conditions' => array('block_id' => array_keys($blockIds))
				))
			);
	}

/**
 * Expect Block->deleteBlock() fail on Block->deleteAll()
 * e.g.) connection error
 *
 * @return  void
 */
	public function testDeleteBlockFailOnDeleteAll() {
		$this->setExpectedException('InternalErrorException');

		$this->Block = $this->getMockForModel('Blocks.Block', array('deleteAll'));
		$this->Block->expects($this->any())
			->method('deleteAll')
			->will($this->returnValue(false));

		$blockKey = 'block_1';
		$this->Block->deleteBlock($blockKey);
	}

/**
 * Expect Block->deleteBlock() fail on Frame->updateAll()
 * e.g.) connection error
 *
 * @return  void
 */
	public function testDeleteBlockFailOnFrameUpdateAll() {
		$this->setExpectedException('InternalErrorException');

		$this->Frame = $this->getMockForModel('Frames.Frame', array('updateAll'));
		$this->Frame->expects($this->any())
			->method('updateAll')
			->will($this->returnValue(false));

		$blockKey = 'block_1';
		$this->Block->deleteBlock($blockKey);
	}
}
