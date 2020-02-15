<?php

namespace app\models;
use  yii\web\Session;

use Yii;

/**
 * This is the model class for table "cargos".
 *
 * @property integer $id
 * @property string $nombre
 * @property string $created_at
 * @property string $modified_at
 * @property integer $deleted
 *
 * @property Personas[] $personas
 */
 
class Cargos extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $types;
    public static function tableName()
    {
        return 'cargos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre'], 'required'],
            [['created_at', 'modified_at'], 'safe'],
            [['deleted'], 'integer'],
            [['types'], 'safe'],
            ['nombre', 'unique', 'targetAttribute' => ['nombre', 'id_evento'] ,'message' => 'El nombre del cargo ya a sido utilizado para este evento'],
            [['id_evento'], 'integer'],
            [['nombre'], 'string', 'max' => 255],
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
            'created_at' => 'Fecha de Creación',
            'modified_at' => 'Modified At',
            'deleted' => 'Deleted',
            'id_evento' => 'Evento'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersonas()
    {
        return $this->hasMany(Personas::className(), ['id_cargo' => 'id']);
    }
    
    public static function toList()
    {
        $session = Yii::$app->session;
        $event_id = $session->get('event_id');
        $model= Cargos::find()->where(['=','deleted',0])->andWhere(['=','id_evento',$event_id])->orderBy('nombre')->asArray()->all();
        return \yii\helpers\ArrayHelper::map($model, 'id', 'nombre');
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->nombre=strtoupper($this->nombre);

        return true;
    }
}
