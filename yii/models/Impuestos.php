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
class Impuestos extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'impuestos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['identificador', 'nombre', 'descripcion'], 'required'],
            [['identificador'], 'string', 'max' => 2],
            [['nombre','descripcion'], 'string', 'max' => 45],
            ['identificador', 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'identificador' => 'Identificador',
            'nombre' => 'Nombre',
            'descripcion' => 'Descripción',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */    
    public static function toList()
    {
        $model=  Impuestos::find()->where("id != 1")->orderBy('id')->asArray()->all();
        return \yii\helpers\ArrayHelper::map($model, 'id', 'nombre');
    }
}
