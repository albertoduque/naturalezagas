<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "proveedor_tecnologico".
 *
 * @property int $id
 * @property string $nit
 * @property string $nombre
 * @property string $created
 */
class ProveedorTecnologico extends \yii\db\ActiveRecord
{
    public $verificacion;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'proveedor_tecnologico';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nit', 'nombre'], 'required'],
            [['id'], 'integer'],
            [['created','verificacion'], 'safe'],
            [['nit'], 'string', 'max' => 50],
            [['nombre'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nit' => 'Nit',
            'nombre' => 'Nombre',
            'created' => 'Created',
        ];
    }

    public static function toList()
    {
        $model= ProveedorTecnologico::find()->orderBy('id')->asArray()->all();
        return \yii\helpers\ArrayHelper::map($model, 'id', 'nombre');
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $this->nombre=strtoupper($this->nombre);

        return true;
    }
}
