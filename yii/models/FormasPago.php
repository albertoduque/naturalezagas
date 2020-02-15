<?php

namespace app\models;
use  yii\web\Session;

use Yii;

/**
 * This is the model class for table "formas_pago".
 *
 * @property integer $id
 * @property string $nombre
 * @property string $created_at
 * @property string $modified_at
 * @property integer $deleted
 *
 * @property Recibos[] $recibos
 */
class FormasPago extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'formas_pago';
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
            [['nombre'], 'string', 'max' => 50],
             [['id_evento'], 'integer'],
            ['nombre', 'unique', 'targetAttribute' => ['nombre', 'id_evento'] ,'message' => 'La forma de pago ya fue utilizada para este evento'],
             [['nombre'], 'unique','targetAttribute' => ['nombre', 'id_evento'], 'on'=>'update', 'when' => function($model){
                    return $model->isAttributeChanged('nombre');
                }
            ],
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
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecibos()
    {
        return $this->hasMany(Recibos::className(), ['id_forma_pago' => 'id']);
    }
    
    public static function toList()
    {
        $session = Yii::$app->session;
        $event_id = $session->get('event_id');
        $model= FormasPago::find()->where(['=','deleted',0])->andWhere(['=','id_evento',$event_id])->orderBy('id')->asArray()->all();
        return \yii\helpers\ArrayHelper::map($model, 'id', 'nombre');
    }
}
