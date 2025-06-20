<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\CalculatorForm;
use app\models\CalculationHistory;

class CalculatorController extends Controller
{
    public function actionIndex()
    {
        $model = new CalculatorForm();
        $result = null;
        $history = [];

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $result = $model->calculate();
            
            // Save to history if user is logged in
            if (!Yii::$app->user->isGuest && $result !== null) {
                CalculationHistory::saveCalculation(
                    Yii::$app->user->id,
                    $model->num1,
                    $model->num2,
                    $model->operation,
                    $result
                );
            }
        }

        // Get user's calculation history if logged in
        if (!Yii::$app->user->isGuest) {
            $history = CalculationHistory::getUserHistory(Yii::$app->user->id, 15);
        }

        return $this->render('index', [
            'model' => $model,
            'result' => $result,
            'history' => $history,
        ]);
    }

    /**
     * Clear user's calculation history
     */
    public function actionClearHistory()
    {
        if (!Yii::$app->user->isGuest) {
            CalculationHistory::deleteAll(['user_id' => Yii::$app->user->id]);
            Yii::$app->session->setFlash('success', 'Calculation history has been cleared.');
        }
        
        return $this->redirect(['index']);
    }
}