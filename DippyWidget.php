<?php
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

	public $newItemLabel = 'New Item';
	public $deleteConfirmation = 'Please confirm the item deletion.';
	public $enterSaveText = 'Press Enter for saving';
	public $validateErrorText = 'Please type a valid value';
	public $validateRegExp = '';

	public $onSuccess;
	public $onError;
	private $_baseUrl;

	public function init(){
		parent::init();
		if($this->id == null)
			$this->id = 'dippy0';
		if($this->onSuccess == null)
			$this->onSuccess = "function(){}";
		if($this->onError == null)
			$this->onError = "function(){}";
	}

	public function run(){
		$this->_prepareAssets();
		$loading = $this->_baseUrl.'/loading.gif';
		$delete  = $this->_baseUrl.'/delete.png';
		$update  = $this->_baseUrl.'/update.png';
		
		echo 
"
<div id={$this->id} class='dippy'>
	<div class='dcontrol'>
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
			'parent'=>$this->parent,
			'deleteconfirmation'=>$this->deleteConfirmation,
			'enterSaveText'=>$this->enterSaveText,
			'validateErrorText'=>$this->validateErrorText,
			'validateRegExp'=>$this->validateRegExp,
			'data'=>serialize(array(
				'modelName'=>$this->modelName,
				'parentKey'=>$this->parentKey,
				'attribute'=>$this->attribute,
			)),
		));
		Yii::app()->getClientScript()->registerScript($this->id
				,"new Dippy({$options})");

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
			header('Content-Type: text/json');
			echo CJSON::encode($a);
		}

		if($action == 'newitem'){
			$parent = $_GET['parent'];
			$model = new $modelName;
			$model[$parentKey] = $parent;
			$model[$attribute] = 'new item';
			if($model->save()){ // using Save, to get validation
				header('Content-Type: text/json');
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
