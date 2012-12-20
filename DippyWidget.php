<?php
/**
 * DippyWidget
 *
 *	Dippy allows you to manage dependent values in a visual way, 
 *	dippy will automatically handle your models (CComponent based, like
 *  CActiveRecord).
 *
 *	no interface required !   no config setup required !
 *
 *  EXAMPLE USE CASE:
 *
 *	at first place, suppose we have the following data model:
 *
 *	1) An Article
 *		2) An ArticleOpt , belongs to an 'Article'
 *			3) An OptVal,  belongs to an ArticleOpt
 *
 *	[ARTICLE]				[ARTICLEOPT]
 *	ARTICLEID	NAME		ARTOPTID	ARTICLEID	NAME
 *	-------------------		-------------------------------
 *	AAA123		BROWIES		00000001	ART123		FLAVOR
 *	AAA123		CAKES		00000002	ART123		PACKAGE
 *
 *							[OPTVAL]
 *							OPTVALID	ARTOPTID	NAME
 *							-------------------------------
 *							10000000	00000001	CHOCO		(flavor)
 *							20000000	00000001	NUTS		(flavor)
 *							...............................
 *							30000000	00000002	PLASTIC		(package)
 *							40000000	00000002	BAG			(package)
 *
 *	You can use two (2) Dippy Widgets to manage ARTICLEOPT and OPTVAL. Easy.
 *
 *	<label>Type an article code:</label>
 *	<input type='text' id='Article_articleid' value='AAA-123'>
 *		(please note: DippyWidget will bind a 'change event' using jQuery
 *		 for this input element, so when this value changes dippy refresh)
 *
 *	$this->widget('ext.dippy.DippyWidget',array(
 *		'title'=>'Article Options',
 *		'id'=>'dippy1',
 *		'controllerName'=>'article',	 // read note below
 *		'parent'=>'Article_articleid',	 //	parent key value
 *		'modelName'=>'ArticleOpt',		 //	your model name
 *		'parentKey'=>'articleid',		 // model belongs to (attribute name)
 *		'attribute'=>'name',			 // the text to edit and display
 *		//'logid'=>'logger',			 // ref. to: <div id='logid'></div>
 *	));
 *
 *	because the main usage for Dippy is for dependent values, now insert
 *	a second Dippy Widget, it is listen for dippy1 selection change, when a
 *	selection change occurs, it will display the Article Options child values.
 *
 *	$this->widget('ext.dippy.DippyWidget',array(
 *		'title'=>'Selection Values:',
 *		'id'=>'dippy2',
 *		'controllerName'=>'article',	 // read note below
 *		'parent'=>'dippy1',	 			 //	IMPORTANT !! parent is 'dippy1'
 *		'modelName'=>'OptVal',			 //	your model name
 *		'parentKey'=>'artoptid',		 // model belongs to (attribute name)
 *		'attribute'=>'name',			 // the text to edit and display
 *	));
 *
 *
 *	NOTE ABOUT: 'controllerName'
 *	in this controller referenced by its name you must add the following:
 *	
 *		public function actions() { 
 *			return array(
 * 				'dippy'=>array('class'=>'ext.dippy.DippyAction')
 *			); 
 *		}
 *
 * @uses CWidget
 * @author Christian Salazar <christiansalazarh@gmail.com> 
 * @license http://opensource.org/licenses/bsd-license.php
 */
class DippyWidget extends CWidget {

	public $id;
	public $title='';
	public $controllerName='site';
	public $actionName = 'dippy';
	public $parent = '';	// parent control ID, when it change dippy reply
	public $logid = '';
	public $modelName = '';	// example: 'Flavor'
	public $parentKey; 		// example: 'categoryid' (flavor belongs to catgid)
	public $attribute;		// example: 'flavor_name'
	public $extraCss;		// any css class name, or the alias: 'jquery.ui'

	public $newItemLabel = 'New Item';
	public $deleteConfirmation = 'Please confirm the item deletion.';
	public $enterSaveText = 'Press Enter for saving';
	public $validateErrorText = 'Please type a valid value';
	public $validateRegExp = '';

	public	$onChange; // function(dippyId, selectdId){   } 

	public $onSuccess;
	public $onError;
	private $_baseUrl;

	public function init(){
		parent::init();
		if($this->id == null)
			$this->id = 'dippy0';
		if($this->onChange == null)
			$this->onChange = "function(){}";
		if($this->onSuccess == null)
			$this->onSuccess = "function(){}";
		if($this->onError == null)
			$this->onError = "function(){}";
		if($this->extraCss==null)
			$this->extraCss='';
	}

	public function run(){
		$this->_prepareAssets();
		$loading = $this->_baseUrl.'/loading.gif';
		$delete  = $this->_baseUrl.'/delete.png';
		$update  = $this->_baseUrl.'/update.png';
		
		$css1 = $this->extraCss;
		$css2 = '';
		if($this->extraCss == 'jquery.ui'){
			$css1 = 'ui-widget-content';
			$css2 = 'ui-widget-header';
		}


		echo 
"
<div id={$this->id} class='dippy {$css1}'>
	<div class='dcontrol {$css2}'>
		<span>{$this->title}</span>
		<a class='newItem'>{$this->newItemLabel}<img src='{$loading}'></a>
	</div>
</div>
";

		$options = CJavaScript::encode(array(
			'id'=>$this->id,
			'action'=>CHtml::normalizeUrl(array(
				$this->controllerName.'/'.$this->actionName)),
			'imgDelete'=>$delete,
			'imgUpdate'=>$update,
			'imgWait' =>$loading,
			'logid'=>$this->logid,
			'onSuccess'=>new CJavaScriptExpression($this->onSuccess),
			'onError'=>new CJavaScriptExpression($this->onError),
			'onChange'=>new CJavaScriptExpression($this->onChange),
			'parent'=>$this->parent,
			'deleteconfirmation'=>$this->deleteConfirmation,
			'enterSaveText'=>$this->enterSaveText,
			'validateErrorText'=>$this->validateErrorText,
			'validateRegExp'=>$this->validateRegExp,
			'extraCss'=>$this->extraCss,
			'data'=>serialize(array(
				'modelName'=>$this->modelName,
				'parentKey'=>$this->parentKey,
				'attribute'=>$this->attribute,
			)),
		));
		Yii::app()->getClientScript()->registerScript($this->id,
				"new Dippy({$options})");

	}// end run()
	
	
	public function _prepareAssets(){
		$localAssetsDir = dirname(__FILE__) . '/assets';
		$this->_baseUrl = Yii::app()->getAssetManager()->publish(
				$localAssetsDir);
        $cs = Yii::app()->getClientScript();
        $cs->registerCoreScript('jquery');
		foreach(scandir($localAssetsDir) as $f){
			$_f = strtolower($f);
			if(strstr($_f,".swp"))
				continue;
			if(strstr($_f,".js"))
				$cs->registerScriptFile($this->_baseUrl."/".$_f);
			if(strstr($_f,".css"))
				$cs->registerCssFile($this->_baseUrl."/".$_f);
		}
	}


	/**
	 * runAction
	 *	invoked from DippyAction
	 * 
	 * @param mixed $action 
	 * @access public
	 * @return void
	 */
	public function runAction($action){
		$data = '';
		$modelName = '';
		if(isset($_GET['data'])){
			$data = unserialize($_GET['data']);
			$modelName = $data['modelName'];
			$parentKey = $data['parentKey'];
			$attribute = $data['attribute'];
		}

		if($action == 'refresh'){
			$parent = $_GET['parent'];
			$model = new $modelName;
			$a = array();
			foreach($model->findAllByAttributes(array(
				$parentKey=>$parent)) as $m)
					$a[$m->primarykey] = $m[$attribute];
			header("Content-type: application/json");
			echo CJSON::encode($a);
		}

		if($action == 'newitem'){
			$parent = $_GET['parent'];
			$model = new $modelName;
			$model[$parentKey] = $parent;
			$model[$attribute] = 'new item';
			if($model->save()){ // using Save, to get validation
			header("Content-type: application/json");
				echo CJSON::encode(array(
					'id'=>$model->primarykey, 
					'text'=>$model[$attribute]
				));
			}else
			throw new Exception(CHtml::errorSummary($model));
		}

		if($action == 'delete'){
			$id = $_GET['id'];
			$model = new $modelName;
			$inst = $model->model()->findByPk($id);
			if($inst != null)
				if($inst->delete()){
					echo "OK";
					return;
				}
			throw new Exception("cannot delete");
		}	

		if($action == 'update'){
			$id = $_GET['id'];
			$val = trim(file_get_contents('php://input'));
			$model = new $modelName;
			$inst = $model->model()->findByPk($id);
			if($inst != null){
				$inst[$attribute] = $val;
				if($inst->update()){
					echo $inst[$attribute];
					return;
				}
			}
			throw new Exception("cannot update");
		}	


	}
}
