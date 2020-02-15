<?php

namespace app\models;

use Yii;
use yii\web\Session;
/**
 * This is the model class for table "productos".
 *
 * @property integer $id
 * @property string $nombre
 * @property integer $id_evento
 * @property string $descripcion
 * @property string $valor
 * @property integer $iva
 * @property string $imagen
 * @property string $activo
 * @property string $inscripciones
 * @property string $created_at
 * @property string $modified_at
 * @property integer $deleted
 *
 * @property DetalleProductos[] $detalleProductos
 * @property Eventos $idEvento
 */
class Productos extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'productos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre', 'id_evento', 'valor','tipo_impuesto'], 'required'],
            [['id_evento', 'deleted', 'tipo_impuesto'], 'integer'],
            [['descripcion'], 'string'],
            [['valor','iva'], 'number'],
            [['created_at', 'modified_at','activo'], 'safe'],
            [['nombre'], 'string', 'max' => 255],
            [['imagen'], 'string', 'max' => 50],
            [['activo', 'inscripciones'], 'string', 'max' => 1],
            [['id_evento'], 'exist', 'skipOnError' => true, 'targetClass' => Eventos::className(), 'targetAttribute' => ['id_evento' => 'id']],
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
            'id_evento' => 'Id Evento',
            'descripcion' => 'Descripción',
            'valor' => 'Valor',
            'iva' => 'IVA',
            'imagen' => 'Imagen',
            'activo' => 'Activo',
            'inscripciones' => 'Inscripciones',
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
        return $this->hasMany(DetalleProductos::className(), ['id_producto' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdEvento()
    {
        return $this->hasOne(Eventos::className(), ['id' => 'id_evento']);
    }

    public static function toList()
    {
        $session = Yii::$app->session;
        $event_id = $session->get('event_id');
        $model= Productos::find()->where(['=','deleted',0])->andWhere(['=','id_evento',$event_id])->orderBy('id')->asArray()->all();
        return \yii\helpers\ArrayHelper::map($model, 'id', 'nombre');
    }
}
