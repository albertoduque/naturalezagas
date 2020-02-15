<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipo-nota".
 *
 * @property integer $id
 * @property string $identificador
 * @property integer $nombre
 * @property integer $descripcion
 *
 */
class TipoNota extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tipo_nota';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tipo', 'codigo', 'nombre',], 'required'],
            [['codigo'], 'string', 'max' => 2],
            [['nombre'], 'string', 'max' => 45],
            ['codigo', 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tipo' => 'Tipo Nota',
            'codigo' => 'Código',
            'nombre' => 'Nombre',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */    
    public static function toList()
    {
        //$model=  MedioPago::find()->where(['=','status',1])->orderBy('id')->asArray()->all();
        $model=  TipoNota::find()->where("id != 1")->orderBy('tipo, codigo')->asArray()->all();
        return \yii\helpers\ArrayHelper::map($model, 'id', 'nombre');
    }
	
    /**
     * @return \yii\db\ActiveQuery
     */    
    public static function toListDebito()
    {
        //$model=  MedioPago::find()->where(['=','status',1])->orderBy('id')->asArray()->all();
        $model=  TipoNota::find()->where("tipo = 'ND'")->orderBy('tipo, codigo')->asArray()->all();
        return \yii\helpers\ArrayHelper::map($model, 'id', 'nombre');
    }
	
    /**
     * @return \yii\db\ActiveQuery
     */    
    public static function toListCredito()
    {
        //$model=  MedioPago::find()->where(['=','status',1])->orderBy('id')->asArray()->all();
        $model=  TipoNota::find()->where("tipo = 'NC'")->orderBy('tipo, codigo')->asArray()->all();
        return \yii\helpers\ArrayHelper::map($model, 'id', 'nombre');
    }
}
