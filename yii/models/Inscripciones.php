<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "inscripciones".
 *
 * @property integer $id
 * @property integer $id_empresa
 * @property integer $id_producto
 * @property integer $id_tipo_asistente
 * @property integer $estado
 * @property integer $id_persona
 * @property string $created_at
 * @property string $modified_at
 * @property integer $deleted
 *
 * @property DetalleContacto[] $detalleContactos
 * @property Contactos[] $idContactos
 * @property DetalleFactura[] $detalleFacturas
 * @property TipoAsistentes $idTipoAsistente
 * @property Empresas $idEmpresa
 * @property Personas $idPersona
 * @property Productos $idProducto
 */
class Inscripciones extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $guardar,$id_cambio,$eventoId;
    public static function tableName()
    {
        return 'inscripciones';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_empresa', 'id_producto', 'id_tipo_asistente', 'estado', 'id_persona', 'deleted','is_facturado','id_estado_factura','is_presence','id_factura'], 'integer'],
            [['id_producto', 'id_persona'], 'required'],
            [['created_at', 'modified_at','guardar','id_cambio','is_facturado','observaciones','id_factura','eventoId'], 'safe'],
            [['id_tipo_asistente'], 'exist', 'skipOnError' => true, 'targetClass' => TipoAsistentes::className(), 'targetAttribute' => ['id_tipo_asistente' => 'id']],
            [['id_empresa'], 'exist', 'skipOnError' => true, 'targetClass' => Empresas::className(), 'targetAttribute' => ['id_empresa' => 'id']],
            [['id_persona'], 'exist', 'skipOnError' => true, 'targetClass' => Personas::className(), 'targetAttribute' => ['id_persona' => 'id']],
            [['id_producto'], 'exist', 'skipOnError' => true, 'targetClass' => Productos::className(), 'targetAttribute' => ['id_producto' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_empresa' => 'Id Empresa',
            'id_producto' => 'Id Producto',
            'id_tipo_asistente' => 'Id Tipo Asistente',
            'estado' => 'Estado',
            'id_persona' => 'Id Persona',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
            'deleted' => 'Deleted',
            'is_facturado'=>'Edo Factura',
            'is_presence'=>'Asistencia',
            'observaciones'=>'Observaciones',
            'id_estado_factura'=>'Estado Factura'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetalleContactos()
    {
        return $this->hasMany(DetalleContacto::className(), ['id_detalle_producto' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdContactos()
    {
        return $this->hasMany(Contactos::className(), ['id' => 'id_contacto'])->viaTable('detalle_contacto', ['id_detalle_producto' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetalleFacturas()
    {
        return $this->hasMany(DetalleFactura::className(), ['id_detalle_producto' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdTipoAsistente()
    {
        return $this->hasOne(TipoAsistentes::className(), ['id' => 'id_tipo_asistente']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdEmpresa()
    {
        return $this->hasOne(Empresas::className(), ['id' => 'id_empresa']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdPersona()
    {
        return $this->hasOne(Personas::className(), ['id' => 'id_persona']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdProducto()
    {
        return $this->hasOne(Productos::className(), ['id' => 'id_producto']);
    }
}
