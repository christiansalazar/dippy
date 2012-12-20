Dippy Widget
============

Manage dependent models (master-detail) in a visual way, using ajax and jquery.
-------------------------------------------------------------------------------

by:

Christian Salazar. christiansalazarh@gmail.com	@yiienespanol, dic. 2012.

![Screen Capture]
(https://bitbucket.org/christiansalazarh/dippy/downloads/screenshot.png "Screen Capture")

[http://opensource.org/licenses/bsd-license.php](http://opensource.org/licenses/bsd-license.php "http://opensource.org/licenses/bsd-license.php")

[Repository at Bit Bucket !](https://bitbucket.org/christiansalazarh/dippy/ "Repository at Bit Bucket !")

#Requirement: 

Yii  1.1.12
jQuery

#What it does ?

Dippy allows you to manage dependent values in a visual way, dippy will automatically handle your models (CComponent based, like CActiveRecord). no interface required !   no config setup required !

Let me explain using a sample data model, commonly used on ECommerce and Stock applications.

1. An Article.
2. An ArticleOpt , belongs to an 'Article'.
3. An OptVal,  belongs to an ArticleOpt.

You can use two (2) Dippy Widgets to manage ARTICLEOPT and OPTVAL. Easy.

	[ARTICLE]
	ARTICLEID	NAME
	-----------------------
	AAA123		BROWIES
	AAA124		CAKES

	[ARTICLEOPT]
	ARTOPTID	ARTICLEID	NAME
	-----------------------------------------
	00000001	ART123		FLAVOR
	00000002	ART123		PACKAGE

	[OPTVAL]
	OPTVALID	ARTOPTID	NAME
	-------------------------------
	10000000	00000001	CHOCO	(flavor)
	20000000	00000001	NUTS	(flavor)
	30000000	00000002	PLASTIC	(package)
	40000000	00000002	BAG	(package)


#For each model, manage it using a Dippy Widget.

This is a simple Dippy Widget, used to manage a single model:

![Simple Dippy]
(https://bitbucket.org/christiansalazarh/dippy/downloads/dippy.png "Simple Dippy")

You can concatenate two (2) or more Dippy Widgets, each one for every dependent model.

#Example Usage:

**AT FIRST PLACE**, if your widgets points to 'controllerName'=>'site' then put the following in 'site' controller:
~~~
public function actions() { 
	return array(
		'dippy'=>array('class'=>'ext.dippy.DippyAction')
	); 
}
~~~

**NEXT, IN YOUR VIEW:**

~~~
<h1>Dippy Demo</h1>

<label>Type an article code:</label>
<input type='text' id='Article_articleid' value='AAA123'>
<!-- 
please note: DippyWidget will bind a 'change event' using jQuery for 
this input element, so when this value changes dippy refresh automatically)
-->

<div style='width: 300px; padding: 10px; border: 2px solid #aaa;
border-radius: 5px;margin-top: 10px;'>

<?php
$this->widget('ext.dippy.DippyWidget',array(
	'title'=>'Article Options',
	'id'=>'dippy1',
	'controllerName'=>'site',	 	 // read note below
	'parent'=>'Article_articleid',	 //	parent key value
	'modelName'=>'ArticleOpt',		 //	your model name
	'parentKey'=>'articleid',		 // model belongs to (attribute name)
	'attribute'=>'name',			 // the text to edit and display
	//'logid'=>'logger',			 // ref. to: <div id='logid'></div>
));

/*
  Now insert a second Dippy Widget** (note the 'parent'=>'dippy1' value) 
  this 2nd dippy widget is listening for dippy1 selection change, when a 
  selection change occurs, it will refresh and display the dependent values 
  (the OptVal whos parent is the ArtOpt model)
*/

$this->widget('ext.dippy.DippyWidget',array(
	'title'=>'Selection Values:',
	'id'=>'dippy2',
	'controllerName'=>'site',
	'parent'=>'dippy1',	// IMPORTANT! now parent is 'dippy1'
	'modelName'=>'OptVal',
	'parentKey'=>'artoptid',
	'attribute'=>'name', // the text to edit and display
));
?>

<div id='logger'></div>
</div>
~~~

#jQuery UI and extra CSS.

You can associate the style passing the string 'jquery.ui' to the 'extraCss'
attribute, Dippy Widget will work togheter with jQuery UI.

Also, you can pass a single class name to the 'extraCss' attribute.


#onChange Event

Call a JS function whenever a Dippy selection changes. You can use the
same JS function for all dippy widgets, so look for the dippyId argument to
identify wich widget is making the call. The selectionId arguments is the
primary key passed when dippy makes a refresh.


~~~
<?php
$this->widget('ext.dippy.DippyWidget',array(
	'title'=>'Selection Values:',
	'id'=>'dippy2',
	'controllerName'=>'site',
	'parent'=>'dippy1',
	'modelName'=>'OptVal',
	'parentKey'=>'artoptid',
	'attribute'=>'name',
	'onChange' => "function(dippyId, selectionId){ ...yourcodehere..  }",
));
?>
~~~

Example:

in the same view were dippy widget is located, insert a text field:

	echo CHtml::textField('dippy2cur');

now add the onChange event handler, like this:

	'onChange' => "function(wid, id){ $('#dippy2cur').val(id);  }",

makes a selection on your Dippy widget and you will observe the text field
changes too.  This would be usefull when validation is required on your form.

#Your models:


models.ArticleOpt.php
~~~
class ArticleOpt extends CActiveRecord
{
	public function onBeforeValidate($event){
		$this->artoptid = sprintf("%08d",time());
		$this->name = '* New Item *';
	}
  ...
}
~~~


models.OptVal.php
~~~
class OptVal extends CActiveRecord
{

	public function onBeforeValidate($event){
		$this->optvalid = sprintf("%08d",time());
		$this->name = '* New Value *';
	}
  ...
}
~~~


Demo Files (only for demostration)
----------------------------------

~~~
CREATE TABLE IF NOT EXISTS `article` (
  `articleid` char(10) NOT NULL,
  `name` text NOT NULL,
  PRIMARY KEY (`articleid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `article` (`articleid`, `name`) VALUES
('AAA123', 'BROWNIE'),
('AAA124', 'CAKES');

CREATE TABLE IF NOT EXISTS `articleopt` (
  `artoptid` char(10) NOT NULL,
  `articleid` char(10) NOT NULL,
  `name` text NOT NULL,
  PRIMARY KEY (`artoptid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
INSERT INTO `articleopt` (`artoptid`, `articleid`, `name`) VALUES
('00000001', 'AAA123', 'Flavor'),
('00000002', 'AAA123', 'Package');

CREATE TABLE IF NOT EXISTS `optval` (
  `optvalid` char(10) NOT NULL,
  `artoptid` char(10) NOT NULL,
  `name` text NOT NULL,
  PRIMARY KEY (`optvalid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
INSERT INTO `optval` (`optvalid`, `artoptid`, `name`) VALUES
('10000000', '00000001', 'CHOCO'),
('20000000', '00000001', 'NUTS'),
('30000000', '00000002', 'PLASTIC'),
('40000000', '00000002', 'BAG');
~~~

