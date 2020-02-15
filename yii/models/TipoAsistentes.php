<?php

namespace app\models;
use  yii\web\Session;

use Yii;

/**
 * This is the model class for table "tipo_asistentes".
 *
 * @property integer $id
 * @property string $nombre
 * @property string $facturable
 * @property string $created_at
 * @property string $modified_at
 * @property integer $deleted
 *
 * @property DetalleProductos[] $detalleProductos
 */
class TipoAsistentes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tipo_asistentes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre', 'facturable'], 'required'],
            [['created_at', 'modified_at'], 'safe'],
            [['deleted'], 'integer'],
            [['nombre'], 'string', 'max' => 255],
             [['id_evento'], 'integer'],
            ['nombre', 'unique', 'targetAttribute' => ['nombre', 'id_evento'] ,'message' => 'El tipo de asistente ya a sido utilizado para este evento'],
             [['nombre'], 'unique','targetAttribute' => ['nombre', 'id_evento'], 'on'=>'update', 'when' => function($model){
                    return $model->isAttributeChanged('nombre');
                }
            ],
            [['facturable'], 'string', 'max' => 2],
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
            'facturable' => 'Facturable',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetalleProductos()
    {
        return $this->hasMany(DetalleProductos::className(), ['id_tipo_asistente' => 'id']);
    }
    public static function toList($eventId=0)
    {
        if(!$eventId) {
            $session = Yii::$app->session;
            $event_id = $session->get('event_id');
        }
        else{
            $event_id=$eventId;
        }
        $model= TipoAsistentes::find()->where(['=','deleted',0])->andWhere(['=','id_evento',$event_id])->orderBy('nombre')->asArray()->all();
        return \yii\helpers\ArrayHelper::map($model, 'id', 'nombre');
    }
    public function getPersonas()
   {
        $session = Yii::$app->session;
        $event_id = $session->get('event_id');
       $model= TipoAsistentes::find()->where(['=','deleted',0])->andWhere(['=','id_evento',$event_id])->orderBy('nombre')->asArray()->all();
       return \yii\helpers\ArrayHelper::map($model, 'id', 'nombre');
   }
}
