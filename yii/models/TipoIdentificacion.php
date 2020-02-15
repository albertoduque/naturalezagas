<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipo-identificacion".
 *
 * @property integer $id
 * @property string $codigo
 * @property integer $cignificado
 *
 */
class TipoIdentificacion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tipo_identificacion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['codigo', 'significado'], 'required'],
            [['codigo','is_check_digit'], 'integer'],
            [['significado'], 'string', 'max' => 20],
            ['codigo', 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codigo' => 'Código',
            'is_check_digit'=> 'Verificación',
            'significado' => 'Significado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */    
    public static function toList()
    {
        $model=  TipoIdentificacion::find()->where(['=','status',1])->orderBy('id')->asArray()->all();
        return \yii\helpers\ArrayHelper::map($model, 'id', 'significado');
    }
}
