Toggle Icon Column for CGridView
==========================

Can be used to display toggle icon in
[CGridView](http://www.yiiframework.com/doc/api/CGridView) cell.

<img src="http://www.elisdn.ru/upload/images/blogs/d78330e4808678e6fa651eab24e512ec.jpg" alt="" />

Readme
------------

[README RUS](http://www.elisdn.ru/blog/15/dtogglecolumn-kolonka-pereklyuchatel-dlia-cgridview)

Installation
------------

Extract to `protected/components`.

Add following code to `config/main.php`:

~~~
[php]
'import'=>array(
	'application.components.DToggleColumn',
),
~~~

Usage example
-------------
~~~
[php]
$this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'date',
		'title', 
		// icon without link
		array(
			'class'=>'DToggleColumn',
			'name'=>'important',
			'onImageUrl'=>Yii::app()->request->baseUrl . '/images/important.png',
			'offImageUrl'=>Yii::app()->request->baseUrl . '/images/spacer.gif',
            // remove default link
            'linkUrl'=>false;
		),
		// icon with ajax degault toggle link to toggleAction($id, $attribute) action of current controller
		array(
			'class'=>'DToggleColumn',
			// model attribute
			'name'=>'public',
			// title of column
			'header'=>'P',
			// confirmation (if needle)
			'confirmation'=>'Do you really want to change a status?',
			// filter
			'filter'=>array(1=>'Only published', 0=>'Only not published'),
			// icon alt attribute values 
			'titles'=>array(1=>'Published', 0=>'Not published'),
			// style
			'htmlOptions'=>array('style'=>'width:30px'),
		),
        // icon with custom link
		array(
			'class'=>'DToggleColumn',
			'name'=>'inhome',
			'header'=>'H',
			'filter'=>array(1=>'Only in homepage', 0=>'Only not in homepage'),
			'linkUrl'=>'Yii::app()->controller->createUrl("customToggle", array("id"=>$data->id, "attribute"=>"inhome"))',
			'htmlOptions'=>array('style'=>'width:30px'),
		),
		array(
			'class'=>'CButtonColumn',
		),
	),
));
~~~

Controller:

~~~
[php]
class PostController extends Controller
{
	// ...
 
	public function actionAdmin()
	{
		$model = new Post('search');
		$model->unsetAttributes();
		if(isset($_GET['Post']))
			$model->attributes=$_GET['Post'];
 
		if (Yii::app()->request->isAjaxRequest) {
			$this->renderPartial('_grid', array(
				'model'=>$model,
			));
		} else {
			$this->render('admin', array(
				'model'=>$model,
			));
		}
	}
	 
	public function actionToggle($id, $attribute)
	{
		if(!Yii::app()->request->isPostRequest)
			throw new CHttpException(400, 'Bad request');
	 
		if (!in_array($attribute, array('public', 'inhome')))
			throw new CHttpException(400, 'Bad request');
	 
		$model = $this->loadModel($id);
		$model->$attribute = $model->$attribute ? 0 : 1;
		$model->save();
 
		if (!Yii::app()->request->isAjaxRequest)
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}
	 
	// ...  
	 
	public function loadModel($id)
	{
		// ...
	}
}
~~~
