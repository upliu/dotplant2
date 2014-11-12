<?php

use karik\helpers\Html;
use kartik\dynagrid\DynaGrid;
use kartik\icons\Icon;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Newsletter email list');
$this->params['breadcrumbs'][] = $this->title;

?>

<?= app\widgets\Alert::widget([
    'id' => 'alert',
]); ?>

<div class="row">
    <div class="col-md-8" id="jstree-more">
        <?php

        echo DynaGrid::widget(
            [
                'options' => [
                    'id' => 'subscribe_email_list-grid',
                ],
                'columns' => [
                    'name',
                    'email',
                    [
                        'class' => 'app\backend\columns\BooleanStatus',
                        'attribute' => 'is_active',
                    ],
                    [
                        'class' => 'app\components\ActionColumn',
                        'template' => '{update} {delete}',
                    ],
                ],
                'theme' => 'panel-default',
                'gridOptions' => [
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'panel' => [
                        'heading'=>'<h3 class="panel-title">'.$this->title.'</h3>',
                    ]
                ]
            ]
        );

        ?>
    </div>
</div>


