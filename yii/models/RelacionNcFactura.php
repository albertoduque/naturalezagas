<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "relacion_nc_factura".
 *
 * @property int $id
 * @property int $factura_id
 * @property int $nc_id
 * @property double $monto
 * @property string $created_date
 * @property int $deleted
 *
 * @property Facturas $factura
 * @property Facturas $nc
 */
class RelacionNcFactura extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'relacion_nc_factura';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['factura_id', 'nc_id', 'deleted'], 'integer'],
            [['monto', 'created_date','tipo'], 'required'],
            [['monto','tipo'], 'number'],
            [['created_date'], 'safe'],
            ['nc_id', 'unique', 'targetAttribute' => ['nc_id','factura_id']],
            [['factura_id'], 'exist', 'skipOnError' => true, 'targetClass' => Facturas::className(), 'targetAttribute' => ['factura_id' => 'id']],
            [['nc_id'], 'exist', 'skipOnError' => true, 'targetClass' => Facturas::className(), 'targetAttribute' => ['nc_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'factura_id' => 'Factura ID',
            'nc_id' => 'Nc ID',
            'monto' => 'Monto',
            'created_date' => 'Created Date',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFactura()
    {
        return $this->hasOne(Facturas::className(), ['id' => 'factura_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNc()
    {
        return $this->hasOne(Facturas::className(), ['id' => 'nc_id']);
    }
}
