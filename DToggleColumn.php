<?php
/**
 * @author ElisDN <mail@elisdn.ru>
 * @link http://www.elisdn.ru
 */

Yii::import('zii.widgets.grid.CGridColumn');

class DToggleColumn extends CGridColumn
{
    /**
     * @var string the attribute name of the data model.
     */
    public $name;
    /**
     * @var boolean a PHP expression if needle.
     */
    public $value;
    /**
     * @var string or a PHP expression that will be evaluated url to toggle action.
     */
    public $linkUrl;
    /**
     * @var array captions for 'alt' and 'title' attributes of icon.
     */
    public $titles = array(1=>'Active', 0=>'Not active');
    /**
     * @var string icon address.
     */
    public $onImageUrl;
    /**
     * @var string icon address.
     */
    public $offImageUrl;
    /**
     * @var int icon width and icon height.
     */
    public $imageSize = 16;
    /**
     * @var string confirmation text if needle.
     */
    public $confirmation;
    /**
     * @var boolean whether the column is sortable.
     */
    public $sortable = true;
    /**
     * @var mixed the HTML code representing a filter input.
     */
    public $filter;

    /**
     * @var string stores CSS class name for link
     */
    protected $class;

    /**
     * @throws CException
     */
    public function init()
	{
		parent::init();

		if (empty($this->name))
			$this->sortable=false;

        if (empty($this->onImageUrl))
            $this->onImageUrl = Yii::app()->request->baseUrl . '/core/images/admin/yes.png';

        if (empty($this->offImageUrl))
            $this->offImageUrl = Yii::app()->request->baseUrl . '/core/images/admin/no.png';

		if (empty($this->name) && empty($this->value))
			throw new CException('Either "name" or "value" must be specified for DToggleColumn.');

        if (!empty($this->linkUrl))
            $this->registerClientScript();
	}

    /**
     * Renders the filter cell content.
     */
    protected function renderFilterCellContent()
    {
        if(is_string($this->filter))
            echo $this->filter;
        else if($this->filter!==false && $this->grid->filter!==null && $this->name!==null && strpos($this->name,'.')===false)
        {
            if(is_array($this->filter))
                echo CHtml::activeDropDownList($this->grid->filter, $this->name, $this->filter, array('id'=>false,'prompt'=>''));
            else if($this->filter===null)
                echo CHtml::activeTextField($this->grid->filter, $this->name, array('id'=>false));
        }
        else
            parent::renderFilterCellContent();
    }

    /**
     * Renders the header cell content.
     * This method will render a link that can trigger the sorting if the column is sortable.
     */
    protected function renderHeaderCellContent()
    {
        if($this->grid->enableSorting && $this->sortable && $this->name!==null)
            echo $this->grid->dataProvider->getSort()->link($this->name,$this->header,array('class'=>'sort-link'));
        else if($this->name!==null && $this->header===null)
        {
            if($this->grid->dataProvider instanceof CActiveDataProvider)
                echo CHtml::encode($this->grid->dataProvider->model->getAttributeLabel($this->name));
            else
                echo CHtml::encode($this->name);
        }
        else
            parent::renderHeaderCellContent();
    }

    /**
     * Registers the client scripts for the column.
     */
    protected function registerClientScript()
    {
        if(is_string($this->confirmation))
            $confirmation = "if(!confirm(".CJavaScript::encode($this->confirmation).")) return false;";
        else
            $confirmation = '';

        if(Yii::app()->request->enableCsrfValidation){
            $csrfTokenName = Yii::app()->request->csrfTokenName;
            $csrfToken = Yii::app()->request->csrfToken;
            $csrf = "\n\t\tdata:{ '$csrfTokenName':'$csrfToken' },";
        } else
            $csrf = '';

        $this->class = 'toggle_' . preg_replace('/\./', '_', $this->name);

        $js = "
$(document).on('click','#{$this->grid->id} a.{$this->class}', function(){
	$confirmation
    var th=this;
    $.fn.yiiGridView.update('{$this->grid->id}', {
        type:'POST',
        url:$(this).attr('href'),$csrf
        success:function(data) {
            $.fn.yiiGridView.update('{$this->grid->id}');
            afterAjaxUpdate(th,true,data);
        },
        error:function(XHR) {
            afterAjaxUpdate(th,false,XHR);
        }
    });
    return false;
});
        ";

        $script = CJavaScript::encode(new CJavaScriptExpression($js));
        Yii::app()->clientScript->registerScript(__CLASS__.'#'.$this->id, $script);
    }

    /**
     * Renders the data cell content.
     * @param integer $row the row number (zero-based)
     * @param mixed $data the data associated with the row
     */
    protected function renderDataCellContent($row,$data)
	{
		if(!empty($this->value))
			$value = $this->evaluateExpression($this->value,array('data'=>$data,'row'=>$row));
		elseif(!empty($this->name))
			$value = CHtml::value($data, $this->name);

        if(!empty($this->linkUrl))
            $url = $this->evaluateExpression($this->linkUrl,array('data'=>$data,'row'=>$row));
        else
            $url = '';

        $src = $value ?  $this->onImageUrl : $this->offImageUrl;
        $title = $this->titles[(int)$value];
        $iconStyle = 'width:' . $this->imageSize . 'px; height:' . $this->imageSize . 'px;';
        $image = CHtml::image($src, $title, array('title'=>$title, 'style'=>$iconStyle));

        if (!empty($url))
            echo CHtml::link($image, $url, array('class'=>'toggle ' . $this->class, 'title'=>$title));
        else
            echo $image;
	}
}
