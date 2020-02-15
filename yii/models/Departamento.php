<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ciudad".
 *
 * @property integer $id
 * @property string $nombre
 * @property integer $status
 * @property integer $id_pais
 *
 * @property Pais $idPais
 * @property Persona[] $personas
 */
class Departamento extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $types;
    public static function tableName()
    {
        return 'ciudad';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['codigo','nombre', 'status', 'id_pais'], 'required'],
            [['status', 'id_pais'], 'integer'],
            [['types'], 'safe'],
            [['codigo'], 'string', 'max' => 2],
            [['nombre'], 'string', 'max' => 200],
            ['nombre', 'unique', 'targetAttribute' => ['nombre', 'id_pais'] ,'message' => 'El Departamento ya fue utilizada para ese País'],
            [['nombre'], 'unique','targetAttribute' => ['nombre', 'id_pais'], 'on'=>'update', 'when' => function($model){
                    return $model->isAttributeChanged('nombre');
                }
            ],
            [['id_pais'], 'exist', 'skipOnError' => true, 'targetClass' => Pais::className(), 'targetAttribute' => ['id_pais' => 'id']],
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
            'id_pais' => 'Id Pais',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdPais()
    {
        return $this->hasOne(Pais::className(), ['id' => 'id_pais']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersonas()
    {
        return $this->hasMany(Persona::className(), ['cod_ciudad' => 'id']);
    }
    
    public static function toList($pais){
        if($pais)
        {
             $model= Departamento::find()->where("id != 0 and id_padre = 0 and status = 1")->andWhere(['id_pais'=>$pais])->orderBy('nombre')->asArray()->all();
             return \yii\helpers\ArrayHelper::map($model, 'id', 'nombre');
        }
        else
        {
           return $model= array();
        }
   }
}
