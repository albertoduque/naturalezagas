<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contactos".
 *
 * @property integer $id
 * @property string $nombre
 * @property string $telefono
 * @property string $telefono_extension
 * @property string $movil
 * @property string $correo
 * @property integer $id_cargo
 * @property string $created_at
 * @property string $modified_at
 * @property integer $deleted
 *
 * @property DetalleContacto[] $detalleContactos
 * @property Inscripciones[] $idDetalleProductos
 */
class Contactos extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contactos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre', 'telefono', 'movil', 'correo'], 'required'],
            [['id_cargo', 'deleted'], 'integer'],
            [['created_at', 'modified_at'], 'safe'],
            [['correo'], 'email'],
            [['nombre', 'telefono', 'movil', 'correo'], 'string', 'max' => 255],
            [['telefono_extension'], 'string', 'max' => 20],
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
            'telefono' => 'Telefono',
            'telefono_extension' => 'Telefono Extension',
            'movil' => 'Movil',
            'correo' => 'Correo',
            'id_cargo' => 'Id Cargo',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetalleContactos()
    {
        return $this->hasMany(DetalleContacto::className(), ['id_contacto' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdDetalleProductos()
    {
        return $this->hasMany(Inscripciones::className(), ['id' => 'id_detalle_producto'])->viaTable('detalle_contacto', ['id_contacto' => 'id']);
    }
}
