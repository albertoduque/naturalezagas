<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pais".
 *
 * @property integer $id
 * @property string $nombre
 * @property integer $status
 *
 * @property Ciudad[] $ciudads
 */
class Pais extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $types;
    public static function tableName()
    {
        return 'pais';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre', 'status'], 'required'],
            [['types'], 'safe'],
            [['status'], 'integer'],
            [['nombre','alias'], 'string', 'max' => 200],
            ['nombre', 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCiudads()
    {
        return $this->hasMany(Ciudad::className(), ['id_pais' => 'id']);
    }
    
    public static function toList()
    {
        //$model=  Pais::find()->where(['=','status',1])->orderBy('id')->asArray()->all();
        $model=  Pais::find()->where("id != 0 and status = 1")->orderBy('id')->asArray()->all();
        return \yii\helpers\ArrayHelper::map($model, 'id', 'nombre');
    }
}
