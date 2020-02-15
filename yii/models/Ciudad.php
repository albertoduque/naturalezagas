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
class Ciudad extends \yii\db\ActiveRecord
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
            [['nombre', 'status', 'id_pais','id_padre', 'codigo'], 'required'],
            [['status', 'id_pais','id_padre'], 'integer'],
            [['types'], 'safe'],
            [['codigo'], 'string', 'max' => 5],
            [['nombre'], 'string', 'max' => 200],
            ['nombre', 'unique', 'targetAttribute' => ['codigo','nombre', 'id_pais','id_padre'] ,'message' => 'La Ciudad ya fue utilizada para ese País'],
            [['nombre'], 'unique','targetAttribute' => ['codigo','nombre', 'id_pais','id_padre'], 'on'=>'update', 'when' => function($model){
                    return $model->isAttributeChanged('nombre');
                }
            ],
            [['id_pais'], 'exist', 'skipOnError' => true, 'targetClass' => Pais::className(), 'targetAttribute' => ['id_pais' => 'id']],
			[['id_padre'], 'exist', 'skipOnError' => true, 'targetClass' => Departamento::className(), 'targetAttribute' => ['id_padre' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codigo' => 'Codigo',
            'nombre' => 'Nombre',
            'status' => 'Status',
            'id_pais' => 'Id Pais',
            'id_padre' => 'Id Departamento',
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
    public function getIdDepartamento()
    {
        return $this->hasOne(Departamento::className(), ['id' => 'id_padre']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersonas()
    {
        return $this->hasMany(Persona::className(), ['cod_ciudad' => 'id']);
    }
    
    //public static function toList($pais){
    public static function toList($id_padre){
        //if($pais)
        if($id_padre)
        {
             //$model= Ciudad::find()->where(['=','status',1])->andWhere(['id_pais'=>$pais])->orderBy('nombre')->asArray()->all();
             $model= Ciudad::find()->where("id != 0 and id_padre != 0 and status = 1")->andWhere(['id_padre'=>$id_padre])->orderBy('nombre')->asArray()->all();
             return \yii\helpers\ArrayHelper::map($model, 'id', 'nombre');
        }
        else
        {
           return $model= array();
        }
   }
}
