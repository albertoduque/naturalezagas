<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "descripcion_productos".
 *
 * @property int $id
 * @property string $nombre
 * @property int $evento_id
 * @property string $create_at
 *
 * @property Eventos $evento
 * @property ProductosDescripciones[] $productosDescripciones
 */
class DescripcionProductos extends \yii\db\ActiveRecord
{
    public $producto_id;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'descripcion_productos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre'], 'string'],
            [['evento_id'], 'required'],
            [['evento_id'], 'integer'],
            [['create_at','producto_id'], 'safe'],
            [['evento_id'], 'exist', 'skipOnError' => true, 'targetClass' => Eventos::className(), 'targetAttribute' => ['evento_id' => 'id']],
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
            'evento_id' => 'Evento ID',
            'create_at' => 'Create At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvento()
    {
        return $this->hasOne(Eventos::className(), ['id' => 'evento_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductosDescripciones()
    {
        return $this->hasMany(ProductosDescripciones::className(), ['descripcion_id' => 'id']);
    }
}
