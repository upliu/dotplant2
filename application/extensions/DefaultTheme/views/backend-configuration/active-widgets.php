<?php

use app\backend\widgets\BackendWidget;
use kartik\helpers\Html;
use kartik\icons\Icon;
use kartik\widgets\ActiveForm;
use yii\helpers\Url;

/** @var $this \yii\web\View */
/** @var \app\extensions\DefaultTheme\models\ThemeActiveWidgets[] $models */
/** @var array[] $allParts */
/** @var \app\extensions\DefaultTheme\models\ThemeVariation $variation */
/** @var array $availableWidgets */

$this->title = Yii::t('app', 'Theme variation active widgets');
$this->params['breadcrumbs'][] = ['url' => [Url::toRoute('index')], 'label' => Yii::t('app', 'Default theme configuration')];
$this->params['breadcrumbs'][] = $this->title;

?>

<?= app\widgets\Alert::widget([
    'id' => 'alert',
]); ?>

<section id="widget-grid">
    <div class="row">

        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">

            <?php BackendWidget::begin(['title'=> Yii::t('app', 'Active widgets'), 'icon'=>'pencil']); ?>

            <?php foreach ($allParts as $partsRow): ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <?= Html::encode($partsRow['name']) ?>
                    </h3>
                </div>
                <div class="panel-body">
                    <ul class="part-active-widgets sortable-list" data-part="<?=$partsRow['id']?>">
                    <?php foreach ($models as $activeWidget): ?>
                        <?php if ($activeWidget->part_id !== intval($partsRow['id'])) continue; ?>
                        <li class="well well-sm well-light" activewidget="<?=$activeWidget->id?>">
                            <?=
                            Html::a(
                                Icon::show('trash-o'),
                                ['delete-active-widget', 'id'=>$activeWidget->id],
                                [
                                    'class' => 'btn btn-danger btn-xs pull-right',
                                    'data-action' => 'post',
                                ]
                            )
                            ?>
                            <?= $activeWidget->widget->name ?>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                    <?php
                    $widgets = \yii\helpers\ArrayHelper::map(
                        array_filter(
                            $availableWidgets,
                            function(\app\extensions\DefaultTheme\models\ThemeWidgets $widget) use ($partsRow) {
                                $applying = $widget->applying;
                                foreach ($applying as $applyTo) {
                                    if ($applyTo->part_id === intval($partsRow['id'])) {
                                        return true;
                                    }
                                }
                                return false;
                            }
                        ),
                        'id',
                        'name'
                    )
                    ?>
                    <?=
                        Html::dropDownList(
                            'add-widget',
                            0,
                            [0=>Yii::t('app', 'Add widget')] + $widgets,
                            [
                                'class' => 'add-widget',
                                'data-part-id' => $partsRow['id'],
                            ]
                        )
                    ?>
                </div>
            </div>
            <?php endforeach; ?>

            <?php BackendWidget::end(); ?>

        </article>
        <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <h2><?= Yii::t('app', 'Variation:') ?>
                <small>
                    <?=Html::encode($variation->name)?>
                </small>
            </h2>
        </article>

    </div>
</section>

<?php
$saveSortedUrl = \yii\helpers\Json::encode(Url::to(['save-sorted']));
$js = <<<JS
var saveSortedIds = function(ids) {
    $.ajax({
        'url': $saveSortedUrl,
        'method': 'POST',
        'data': {
            'ids': ids
        }
    });
};
$(".part-active-widgets").each(function(){
    var that = $(this);
    that.sortable({
        update: function(event, ui) {
            var dragged = ui.item;

            var sortedIds = _.filter(
                that.sortable('toArray', {attribute:'activewidget'}),
                function (val) {
                    return val !== "";
                }
            );
            saveSortedIds(sortedIds, true);


        }
    }).disableSelection();
});

$(".add-widget").change(function(){
    var that = $(this),
        partId = that.data('partId'),
        val = that.val();
    if (parseInt(val) > 0) {
        var form = $('<form>')
                .attr('method', 'post'),
            csrf = $('<input type="hidden">')
                .attr('name', $('meta[name="csrf-param"]').attr('content'))
                .attr('value', $('meta[name="csrf-token"]').attr('content')),
            addWidgetHidden = $('<input type="hidden">')
                .attr('name', 'addWidget')
                .attr('value', val),
            partIdHidden = $('<input type="hidden">')
                .attr('name', 'partId')
                .attr('value', partId);
        form.append(csrf);
        form.append(addWidgetHidden);
        form.append(partIdHidden);
        $('document').append(form);
        form.submit();
    }
})
JS;
$this->registerJs($js);