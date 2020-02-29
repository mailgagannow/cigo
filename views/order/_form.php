<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use app\models\Order;
use app\assets\AppAsset;
/* @var $this yii\web\View */
/* @var $model app\models\Order */
/* @var $form yii\widgets\ActiveForm */
?>

<?php

$form = ActiveForm::begin([
    'layout' => 'horizontal',
    'action' => [
        'create'
    ],
    'method' => 'post',
    'fieldConfig' => [
        'horizontalCssClasses' => [
            'label' => 'col-sm-2',
            'offset' => 'col-sm-offset-2',
            'wrapper' => 'col-sm-4'
        ]
    ]
]);
?>
<div class="row">

	<div class="col-md-6">
            <?= $form->field($model, 'first_name') ?>
            <?= $form->field($model, 'email') ?>
            
       </div>
	<div class="col-md-6">
            <?= $form->field($model, 'last_name') ?>
            <?= $form->field($model, 'contact_no') ?>
           
       </div>
	<div class="col-md-6">
	<?=$form->field($model, 'order_type')->dropDownList(Order::getType());?>
                 <?=$form->field($model, 'order_date')->widget(DatePicker::classname(),['readonly'=>true, 'pluginOptions' => ['format' => 'yyyy-mm-dd','autoclose' => true  , 'startDate' => date("Y-m-d"),'todayHighlight' => true]])?>
           
           
       </div>
	<div class="col-md-6">
            <?= $form->field($model, 'amount')->textInput() ?>
           
       </div>
	<div class="col-md-9">
            <?= $form->field($model, 'street_address') ?>
           
       </div>
	<div class="col-md-6">
            <?= $form->field($model, 'city') ?>
            <?= $form->field($model, 'zipcode') ?>
            
           
       </div>

	<div class="col-md-6">
            <?= $form->field($model, 'state') ?>
            <?= $form->field($model, 'country')->dropDownList(['Canada'=>'Canada','USA'=>'USA','Mexico'=>'Mexico']) ?>
            
           
       </div>
       
</div>
<div class="form-group">
        <?= Html::button(Yii::t('app', 'Preview'), ['class' => 'btn btn-success','id'=>'preview']) ?>
    </div>
 <div class="form-group" style="text-align: right; margin-right: 250px">
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success']) ?>
        <?= Html::resetButton('Cancel', ['id'=>'reset','class' => 'btn btn-success red']) ?>
    </div>
<?php ActiveForm::end() ?>
 <div id="mapid"></div>
 

<div>
     <?=$this->render('index', ['dataProvider' => $dataProvider])?>

</div>



