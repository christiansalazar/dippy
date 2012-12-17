<?php
 /**
 * DippyAction class file.
 *
 *
 *	@example:
 *		public function actions() { 
 *			return array(
 				'dippy'=>array('class'=>'ext.dippy.DippyAction')
			); 
 *		}
 *
 * @author Christian Salazar <christiansalazarh@gmail.com>
 * @license http://opensource.org/licenses/bsd-license.php
 */
class DippyAction extends CAction {
	/**
	* this action invokes the appropiated handler referenced by a 'classname' url attribute.
	* the specified classname must implements: EYuiActionRunnable.php
	*/
	public function run(){
		//Yii::log('ACTION CALLED','info');
		include_once(dirname(__FILE__).'/DippyWidget.php');
		$inst = new DippyWidget();
		$inst->runAction($_GET['action']);
	}
 }

