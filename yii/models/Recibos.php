<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "recibos".
 *
 * @property integer $id
 * @property string $fecha_pago
 * @property integer $valor_descuento
 * @property integer $valor_retencion
 * @property integer $valor_pagado
 * @property string $tipo_pago
 * @property integer $valor_subtotal
 * @property integer $valor_iva
 * @property integer $id_forma_pago
 * @property string $created_at
 * @property string $modified_at
 * @property integer $deleted
 *
 * @property DetalleRecibos[] $detalleRecibos
 * @property DetalleFactura[] $idDetalleFacturas
 * @property FormasPago $idFormaPago
 */
class Recibos extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'recibos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fecha_pago', 'valor_descuento', 'valor_retencion', 'valor_pagado', 'tipo_pago', 'valor_subtotal', 'valor_iva'], 'required'],
            [['fecha_pago', 'created_at', 'modified_at'], 'safe'],
            [['valor_descuento', 'valor_retencion', 'valor_pagado', 'valor_subtotal', 'valor_iva', 'id_forma_pago', 'deleted'], 'integer'],
            [['tipo_pago'], 'string', 'max' => 1],
            [['id_forma_pago'], 'exist', 'skipOnError' => true, 'targetClass' => FormasPago::className(), 'targetAttribute' => ['id_forma_pago' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fecha_pago' => 'Fecha Pago',
            'valor_descuento' => 'Valor Descuento',
            'valor_retencion' => 'Valor Retencion',
            'valor_pagado' => 'Valor Pagado',
            'tipo_pago' => 'Tipo Pago',
            'valor_subtotal' => 'Valor Subtotal',
            'valor_iva' => 'Valor Iva',
            'id_forma_pago' => 'Id Forma Pago',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetalleRecibos()
    {
        return $this->hasMany(DetalleRecibos::className(), ['id_recibo' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdDetalleFacturas()
    {
        return $this->hasMany(DetalleFactura::className(), ['id' => 'id_detalle_factura'])->viaTable('detalle_recibos', ['id_recibo' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdFormaPago()
    {
        return $this->hasOne(FormasPago::className(), ['id' => 'id_forma_pago']);
    }
}
