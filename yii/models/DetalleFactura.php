<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "detalle_factura".
 *
 * @property integer $id
 * @property integer $id_factura
 * @property integer $id_inscripcion
 * @property integer $cantidad
 * @property double $valor
 * @property double $descuento
 * @property double $valorTotal
 * @property double $subtotal
 * @property double $iva
 * @property integer $id_moneda
 * @property integer $id_estado_factura
 * @property string $observacion
 * @property string $created_at
 * @property string $modified_at
 * @property integer $deleted
 *
 * @property Inscripciones $idInscripcion
 * @property EstadosFactura $idEstadoFactura
 * @property Facturas $idFactura
 * @property Monedas $idMoneda
 * @property DetalleRecibos[] $detalleRecibos
 * @property Recibos[] $idRecibos
 */
class DetalleFactura extends \yii\db\ActiveRecord
{
    public $producto,$descripcion;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'detalle_factura';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_factura', 'cantidad', 'valor', 'valorTotal','id_producto'], 'required'],
            [['id_factura', 'cantidad', 'id_moneda', 'id_estado_factura', 'deleted','nc_id'], 'integer'],
            //[['valor', 'descuento', 'valorTotal', 'subtotal', 'iva'], 'double'],
            [['created_at', 'modified_at','id_inscripcion','descripcion_id','producto','nc_id','descripcion'], 'safe'],
            [['observacion','producto'], 'string', 'max' => 255],
            [['id_producto'], 'exist', 'skipOnError' => true, 'targetClass' => Productos::className(), 'targetAttribute' => ['id_producto' => 'id']],
            [['id_inscripcion'], 'exist', 'skipOnError' => true, 'targetClass' => Inscripciones::className(), 'targetAttribute' => ['id_inscripcion' => 'id']],
            [['id_estado_factura'], 'exist', 'skipOnError' => true, 'targetClass' => EstadosFactura::className(), 'targetAttribute' => ['id_estado_factura' => 'id']],
            [['id_factura'], 'exist', 'skipOnError' => true, 'targetClass' => Facturas::className(), 'targetAttribute' => ['id_factura' => 'id']],
            [['id_moneda'], 'exist', 'skipOnError' => true, 'targetClass' => Monedas::className(), 'targetAttribute' => ['id_moneda' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_factura' => 'Id Factura',
            'id_inscripcion' => 'Id Inscripcion',
            'id_producto' => 'Id Producto',
            'cantidad' => 'Cantidad',
            'valor' => 'Valor',
            'descuento' => 'Descuento',
            'valorTotal' => 'Valor Total',
            'subtotal' => 'Subtotal',
            'iva' => 'Iva',
            'id_moneda' => 'Id Moneda',
            'id_estado_factura' => 'Id Estado Factura',
            'observacion' => 'Observacion',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
            'deleted' => 'Deleted',
            'nc_id'=>'NC ID',
            'producto'=>'producto'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdInscripcion()
    {
        return $this->hasOne(Inscripciones::className(), ['id' => 'id_inscripcion']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdEstadoFactura()
    {
        return $this->hasOne(EstadosFactura::className(), ['id' => 'id_estado_factura']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdFactura()
    {
        return $this->hasOne(Facturas::className(), ['id' => 'id_factura']);
    }
    
     public function getIdProducto()
    {
        return $this->hasOne(Productos::className(), ['id' => 'id_producto']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdMoneda()
    {
        return $this->hasOne(Monedas::className(), ['id' => 'id_moneda']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetalleRecibos()
    {
        return $this->hasMany(DetalleRecibos::className(), ['id_detalle_factura' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdRecibos()
    {
        return $this->hasMany(Recibos::className(), ['id' => 'id_recibo'])->viaTable('detalle_recibos', ['id_detalle_factura' => 'id']);
    }
}
