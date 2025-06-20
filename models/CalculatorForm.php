<?php

namespace app\models;

use yii\base\Model;

class CalculatorForm extends Model
{
    public $num1;
    public $num2;
    public $operation;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['num1', 'num2', 'operation'], 'required'],
            [['num1', 'num2'], 'number'],
            [['operation'], 'in', 'range' => ['add', 'subtract', 'multiply', 'divide']],
            [['num2'], 'validateDivisionByZero'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'num1' => 'First Number',
            'num2' => 'Second Number',
            'operation' => 'Operation',
        ];
    }

    /**
     * Custom validation to prevent division by zero
     */
    public function validateDivisionByZero($attribute, $params)
    {
        if ($this->operation === 'divide' && $this->num2 == 0) {
            $this->addError($attribute, 'Division by zero is not allowed.');
        }
    }

    /**
     * Calculate the result based on the operation
     */
    public function calculate()
    {
        if (!$this->validate()) {
            return null;
        }

        switch ($this->operation) {
            case 'add':
                return $this->num1 + $this->num2;
            case 'subtract':
                return $this->num1 - $this->num2;
            case 'multiply':
                return $this->num1 * $this->num2;
            case 'divide':
                return $this->num1 / $this->num2;
            default:
                return null;
        }
    }

    /**
     * Get operation options for dropdown
     */
    public static function getOperationOptions()
    {
        return [
            'add' => 'Addition (+)',
            'subtract' => 'Subtraction (-)',
            'multiply' => 'Multiplication (×)',
            'divide' => 'Division (÷)',
        ];
    }
}