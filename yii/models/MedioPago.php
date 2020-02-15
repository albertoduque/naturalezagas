<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipo-identificacion".
 *
 * @property integer $id
 * @property string $identificador
 * @property integer $nombre
 * @property integer $descripcion
 *
 */
class MedioPago extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'medios_pago';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['codigo', 'medio_pago',], 'required'],
            [['codigo'], 'string', 'max' => 2],
            [['medio_pago'], 'string', 'max' => 45],
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
            'medio_pago' => 'Medio Pago',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */    
    public static function toList()
    {
        //$model=  MedioPago::find()->where(['=','status',1])->orderBy('id')->asArray()->all();
        $model=  MedioPago::find()->orderBy('id')->asArray()->all();
        return \yii\helpers\ArrayHelper::map($model, 'id', 'medio_pago');
    }
}
