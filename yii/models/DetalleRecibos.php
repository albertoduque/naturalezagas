<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "detalle_recibos".
 *
 * @property integer $id
 * @property integer $id_factura
 * @property string $fecha_pago
 * @property double $valor
 * @property integer $id_forma_pago
 * @property string $tipo_pago
 * @property string $created_at
 * @property string $modified_at
 * @property integer $deleted
 *
 * @property Facturas $idFactura
 * @property FormasPago $idFormaPago
 */
class DetalleRecibos extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'detalle_recibos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['valor','fecha_pago'], 'required'],
            [['id_factura', 'id_forma_pago','id_inscripcion', 'deleted'], 'integer'],
            [[ 'created_at', 'modified_at'], 'safe'],
            [['valor'], 'number'],
            [['tipo_pago'], 'string', 'max' => 255],
            [['id_factura'], 'exist', 'skipOnError' => true, 'targetClass' => Facturas::className(), 'targetAttribute' => ['id_factura' => 'id']],
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
            'id_factura' => 'Factura',
            'id_inscripcion' => 'Inscripcion',
            'fecha_pago' => 'Fecha Pago',
            'valor' => 'Valor',
            'id_forma_pago' => 'Forma Pago',
            'tipo_pago' => 'Tipo Pago',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdFactura()
    {
        return $this->hasOne(Facturas::className(), ['id' => 'id_factura']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdFormaPago()
    {
        return $this->hasOne(FormasPago::className(), ['id' => 'id_forma_pago']);
    }
}
