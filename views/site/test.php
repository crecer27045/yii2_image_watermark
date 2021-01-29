<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Upload images';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
use yii\widgets\ActiveForm;
?>


<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

        <?= $form->field($model, 'imageFile')->fileInput() ?>

        <button>Submit</button>

    <?php ActiveForm::end() ?>

    <img src="<?php echo $image ?>" alt="">
</div>
