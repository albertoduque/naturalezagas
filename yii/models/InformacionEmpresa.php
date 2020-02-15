<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "informacion_empresa".
 *
 * @property int $id
 * @property string $nombre
 * @property string $direccion
 * @property string $telefono
 * @property string $pagina_web
 * @property string $created_at
 */
class InformacionEmpresa extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'informacion_empresa';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre'], 'required'],
            [['created_at'], 'safe'],
            [['nombre'], 'string', 'max' => 255],
            [['direccion'], 'string', 'max' => 450],
            [['telefono'], 'string', 'max' => 120],
            [['pagina_web'], 'string', 'max' => 200],
            [['ciudad'], 'string', 'max' => 200],
            [['numero_autorizacion_factura'], 'string', 'max' => 200],
            [['fecha_factura'], 'string', 'max' => 200],
            [['desde_factura'], 'string', 'max' => 200],
            [['hasta_factura'], 'string', 'max' => 200],
            [['numero_autorizacion_contingencia'], 'string', 'max' => 200],
            [['fecha_contingencia'], 'string', 'max' => 200],
            [['desde_contingencia'], 'string', 'max' => 200],
            [['hasta_contingencia'], 'string', 'max' => 200],
            [['periodo_renovacion'], 'integer', 'max' => 200],
            [['version_manual'], 'string', 'max' => 40],
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
            'direccion' => 'Dirección',
            'telefono' => 'Teléfono',
            'pagina_web' => 'Página Web',
            'created_at' => 'Created At',
            'fecha_factura'=>'Fecha de Resolución'
        ];
    }
}
