Toggle Icon Column for CGridView
==========================

Can be used to display toggle icon in
[CGridView](http://www.yiiframework.com/doc/api/CGridView) cell.

<img src="http://www.elisdn.ru/upload/images/blogs/d78330e4808678e6fa651eab24e512ec.jpg" alt="" />

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
		// icon
		array(
			'class'=>'DToggleColumn',
			'name'=>'important',
			'onImageUrl' => Yii::app()->request->baseUrl . '/images/important.png',
			'offImageUrl' => Yii::app()->request->baseUrl . '/images/spacer.gif',
		),
		// icon with ajax toggle link
		array(
			'class'=>'DToggleColumn',
			// model attribute
			'name'=>'public',
			// title of column
			'header'=>'P',
			// confirmation (if needle)
			'confirmation'=>'Изменить статус публикации?',
			// filter
			'filter'=>array(1=>'Only published', 0=>'Only not published'),
			// icon alt attribute values 
			'titles'=>array(1=>'Published', 0=>'Not published'),
			// link to toggle action
			'linkUrl'=>'Yii::app()->createUrl("toggle", array("id"=>$data->id, "param"=>"public"))',
			'htmlOptions'=>array('style'=>'width:30px;text-align:center'),
		),	 
		array(
			'class'=>'DToggleColumn',
			'name'=>'inhome',
			'header'=>'H',
			'filter'=>array(1=>'Only in homepage', 0=>'Only not in homepage'),
			'linkUrl'=>'Yii::app()->createUrl("toggle", array("id"=>$data->id, "param"=>"inhome"))',
			'htmlOptions'=>array('style'=>'width:30px;text-align:center'),
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
		$model->$attribute = !$model->$attribute;
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
