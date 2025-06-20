<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\CalculatorForm;

/* @var $this yii\web\View */
/* @var $model app\models\CalculatorForm */
/* @var $result float|null */
/* @var $history app\models\CalculationHistory[] */

$this->title = 'PHP Calculator - Yii2';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="calculator-index">
    <div class="row">
        <div class="col-lg-6">
            <h1><?= Html::encode($this->title) ?></h1>

            <p>Enter two numbers and select an operation to calculate the result.</p>

            <?php $form = ActiveForm::begin([
                'id' => 'calculator-form',
                'options' => ['class' => 'form-horizontal'],
                'fieldConfig' => [
                    'template' => "{label}\n<div class=\"col-lg-8\">{input}</div>\n<div class=\"col-lg-12\">{error}</div>",
                    'labelOptions' => ['class' => 'col-lg-4 control-label'],
                ],
            ]); ?>

                <?= $form->field($model, 'num1')->textInput([
                    'type' => 'number',
                    'step' => 'any',
                    'placeholder' => 'Enter first number'
                ]) ?>

                <?= $form->field($model, 'num2')->textInput([
                    'type' => 'number', 
                    'step' => 'any',
                    'placeholder' => 'Enter second number'
                ]) ?>

                <?= $form->field($model, 'operation')->dropDownList(
                    CalculatorForm::getOperationOptions(),
                    ['prompt' => 'Select operation...']
                ) ?>

                <div class="form-group">
                    <div class="col-lg-offset-4 col-lg-8">
                        <?= Html::submitButton('Calculate', [
                            'class' => 'btn btn-primary',
                            'name' => 'calculate-button'
                        ]) ?>
                        
                        <?= Html::a('Reset', ['calculator/index'], [
                            'class' => 'btn btn-default'
                        ]) ?>
                    </div>
                </div>

            <?php ActiveForm::end(); ?>

            <?php if ($result !== null): ?>
                <div class="alert alert-success">
                    <h3>Result</h3>
                    <p><strong>
                        <?= Html::encode($model->num1) ?> 
                        <?php
                        $symbols = [
                            'add' => '+',
                            'subtract' => '−',
                            'multiply' => '×',
                            'divide' => '÷'
                        ];
                        echo $symbols[$model->operation] ?? '';
                        ?>
                        <?= Html::encode($model->num2) ?> 
                        = 
                        <?= Html::encode($result) ?>
                    </strong></p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="col-lg-6">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Calculator Features</h3>
                </div>
                <div class="panel-body">
                    <ul>
                        <li><strong>Addition:</strong> Adds two numbers together</li>
                        <li><strong>Subtraction:</strong> Subtracts the second number from the first</li>
                        <li><strong>Multiplication:</strong> Multiplies two numbers</li>
                        <li><strong>Division:</strong> Divides the first number by the second</li>
                    </ul>
                    <p><strong>Note:</strong> Division by zero is automatically prevented with validation.</p>
                </div>
            </div>
            
            <!-- Calculation History Section -->
            <div class="panel panel-default" style="margin-top: 20px;">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="glyphicon glyphicon-time"></i> Calculation History
                        <?php if (!Yii::$app->user->isGuest && !empty($history)): ?>
                            <?= Html::a('Clear History', ['calculator/clear-history'], [
                                'class' => 'btn btn-xs btn-warning pull-right',
                                'data' => [
                                    'confirm' => 'Are you sure you want to clear your calculation history?',
                                    'method' => 'post',
                                ],
                            ]) ?>
                        <?php endif; ?>
                    </h3>
                </div>
                <div class="panel-body">
                    <?php if (Yii::$app->user->isGuest): ?>
                        <div class="alert alert-info">
                            <i class="glyphicon glyphicon-info-sign"></i>
                            Please <?= Html::a('log in', ['/site/login'], ['class' => 'alert-link']) ?> to enable history feature and see your past calculations.
                        </div>
                    <?php elseif (empty($history)): ?>
                        <div class="alert alert-warning">
                            <i class="glyphicon glyphicon-exclamation-sign"></i>
                            No calculation history yet. Start calculating to see your history here!
                        </div>
                    <?php else: ?>
                        <div class="history-list" style="max-height: 300px; overflow-y: auto;">
                            <?php foreach ($history as $index => $calc): ?>
                                <div class="history-item" style="border-bottom: 1px solid #eee; padding: 8px 0;">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <span class="calculation">
                                                <?= Html::encode($calc->getFormattedCalculation()) ?>
                                            </span>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <small class="text-muted">
                                                <?= Yii::$app->formatter->asRelativeTime($calc->created_at) ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div style="margin-top: 10px;">
                            <small class="text-muted">
                                <i class="glyphicon glyphicon-info-sign"></i>
                                Showing last <?= count($history) ?> calculations
                            </small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.history-item:last-child {
    border-bottom: none !important;
}

.history-item:hover {
    background-color: #f9f9f9;
}

.calculation {
    font-family: 'Courier New', monospace;
    font-weight: bold;
}

.history-list {
    background-color: #fafafa;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
}
</style>