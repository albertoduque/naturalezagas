<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "monedas".
 *
 * @property integer $id
 * @property string $nombre
 * @property string $simbolo
 * @property string $created_at
 * @property string $modified_at
 * @property integer $deleted
 *
 * @property DetalleFactura[] $detalleFacturas
 * @property Facturas[] $facturas
 */
class Monedas extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'monedas';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['codigo', 'numero', 'divisa', 'id_pais'], 'required'],
            [['created_at', 'modified_at'], 'safe'],
            [['deleted','id_pais'], 'integer'],
            [['codigo', 'numero'], 'string', 'max' => 3],
            [['divisa'], 'string', 'max' => 50],
			[['id_pais'], 'exist', 'skipOnError' => true, 'targetClass' => Pais::className(), 'targetAttribute' => ['id_pais' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codigo' => 'Codigo',
            'numero' => 'Número',
            'divisa' => 'Divisa',
            'id_pais' => 'Pais',
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
        return $this->hasMany(DetalleFactura::className(), ['id_moneda' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFacturas()
    {
        return $this->hasMany(Facturas::className(), ['id_moneda' => 'id']);
    }
	
	/**
     * @return \yii\db\ActiveQuery
     */
    public function getIdPais()
    {
        return $this->hasOne(Pais::className(), ['id' => 'id_pais']);
    }
    
    public static function toList()
    {
    $model= Monedas::find()->where(['=','deleted',0])->orderBy('id')->asArray()->all();
        //return \yii\helpers\ArrayHelper::map($model, 'id', 'nombre');
        return \yii\helpers\ArrayHelper::map($model, 'id', 'divisa');
    }
}
