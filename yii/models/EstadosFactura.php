<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "estados_factura".
 *
 * @property integer $id
 * @property string $nombre
 * @property string $created_at
 * @property string $modified_at
 * @property integer $deleted
 *
 * @property DetalleFactura[] $detalleFacturas
 * @property Facturas[] $facturas
 */
class EstadosFactura extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'estados_factura';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre'], 'required'],
            [['created_at', 'modified_at'], 'safe'],
            [['deleted'], 'integer'],
            [['nombre'], 'string', 'max' => 255],
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
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
            'deleted' => 'Deleted',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetalleFacturas()
    {
        return $this->hasMany(DetalleFactura::className(), ['id_estado_factura' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFacturas()
    {
        return $this->hasMany(Facturas::className(), ['id_estado_factura' => 'id']);
    }
    
    public static function toList()
    {
        $model= EstadosFactura::find()->where(['=','deleted',0])->orderBy('id')->asArray()->all();
        return \yii\helpers\ArrayHelper::map($model, 'id', 'nombre');
    }
    
    public static function tolistFacturas(){
         return [1=>'FACTURADOS',0=>'NO FACTURADOS'];
    }
}
