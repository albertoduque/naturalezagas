<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "productos_descripciones".
 *
 * @property int $id
 * @property int $producto_id
 * @property int $descripcion_id
 *
 * @property Productos $producto
 * @property DescripcionProductos $descripcion
 */
class ProductosDescripciones extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'productos_descripciones';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['producto_id', 'descripcion_id'], 'required'],
            [['producto_id', 'descripcion_id'], 'integer'],
            [['producto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Productos::className(), 'targetAttribute' => ['producto_id' => 'id']],
            [['descripcion_id'], 'exist', 'skipOnError' => true, 'targetClass' => DescripcionProductos::className(), 'targetAttribute' => ['descripcion_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'producto_id' => 'Producto ID',
            'descripcion_id' => 'Descripcion ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducto()
    {
        return $this->hasOne(Productos::className(), ['id' => 'producto_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDescripcion()
    {
        return $this->hasOne(DescripcionProductos::className(), ['id' => 'descripcion_id']);
    }
}
