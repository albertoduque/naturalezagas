<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "detalle_contacto".
 *
 * @property integer $id_contacto
 * @property integer $id_detalle_producto
 * @property string $created_at
 * @property string $modified_at
 * @property integer $deleted
 *
 * @property Contactos $idContacto
 * @property Inscripciones $idDetalleProducto
 */
class DetalleContacto extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'detalle_contacto';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_contacto', 'id_detalle_producto'], 'required'],
            [['id_contacto', 'id_detalle_producto', 'deleted'], 'integer'],
            [['created_at', 'modified_at'], 'safe'],
            [['id_contacto'], 'exist', 'skipOnError' => true, 'targetClass' => Contactos::className(), 'targetAttribute' => ['id_contacto' => 'id']],
            [['id_detalle_producto'], 'exist', 'skipOnError' => true, 'targetClass' => Inscripciones::className(), 'targetAttribute' => ['id_detalle_producto' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_contacto' => 'Id Contacto',
            'id_detalle_producto' => 'Id Detalle Producto',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdContacto()
    {
        return $this->hasOne(Contactos::className(), ['id' => 'id_contacto']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdDetalleProducto()
    {
        return $this->hasOne(Inscripciones::className(), ['id' => 'id_detalle_producto']);
    }
}
