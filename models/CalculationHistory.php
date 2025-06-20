<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "calculation_history".
 *
 * @property int $id
 * @property int $user_id
 * @property float $num1
 * @property float $num2
 * @property string $operation
 * @property float $result
 * @property string $created_at
 */
class CalculationHistory extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'calculation_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'num1', 'num2', 'operation', 'result'], 'required'],
            [['user_id'], 'integer'],
            [['num1', 'num2', 'result'], 'number'],
            [['operation'], 'string', 'max' => 20],
            [['operation'], 'in', 'range' => ['add', 'subtract', 'multiply', 'divide']],
            [['created_at'], 'safe'],
            // Removed exist validation since User model in basic template doesn't extend ActiveRecord
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'num1' => 'First Number',
            'num2' => 'Second Number',
            'operation' => 'Operation',
            'result' => 'Result',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for user relation.
     * Note: Removed because User model in basic template doesn't extend ActiveRecord
     *
     * @return \yii\db\ActiveQuery
     */
    // public function getUser()
    // {
    //     return $this->hasOne(User::class, ['id' => 'user_id']);
    // }

    /**
     * Get operation symbol for display
     *
     * @return string
     */
    public function getOperationSymbol()
    {
        $symbols = [
            'add' => '+',
            'subtract' => '−',
            'multiply' => '×',
            'divide' => '÷'
        ];
        
        return $symbols[$this->operation] ?? '';
    }

    /**
     * Get formatted calculation string
     *
     * @return string
     */
    public function getFormattedCalculation()
    {
        // Format numbers - remove unnecessary decimal places
        $num1 = rtrim(rtrim(number_format($this->num1, 8), '0'), '.');
        $num2 = rtrim(rtrim(number_format($this->num2, 8), '0'), '.');
        $result = rtrim(rtrim(number_format($this->result, 8), '0'), '.');
        
        return sprintf(
            '%s %s %s = %s',
            $num1,
            $this->getOperationSymbol(),
            $num2,
            $result
        );
    }

    /**
     * Get user's calculation history ordered by latest first
     *
     * @param int $userId
     * @param int $limit
     * @return CalculationHistory[]
     */
    public static function getUserHistory($userId, $limit = 10)
    {
        return static::find()
            ->where(['user_id' => $userId])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit($limit)
            ->all();
    }

    /**
     * Save a new calculation to history
     *
     * @param int $userId
     * @param float $num1
     * @param float $num2
     * @param string $operation
     * @param float $result
     * @return bool
     */
    public static function saveCalculation($userId, $num1, $num2, $operation, $result)
    {
        $history = new static();
        $history->user_id = $userId;
        $history->num1 = $num1;
        $history->num2 = $num2;
        $history->operation = $operation;
        $history->result = $result;
        
        return $history->save();
    }
}