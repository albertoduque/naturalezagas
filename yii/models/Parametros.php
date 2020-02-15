<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "parametros".
 *
 * @property int $id
 * @property string $nombre
 * @property string $descripcion
 * @property int $deleted
 */
class Parametros extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'parametros';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre', 'descripcion','value'], 'required'],
            [['deleted'], 'integer'],
            [['nombre', 'descripcion','value'], 'string'],
            [['nombre'], 'unique'],
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
            'value' => 'Parametro',
            'descripcion' => 'Descripción',
            'deleted' => 'Estado',
        ];
    }
    
     public static function getParameterByName($name)
    {
        $connection = \Yii::$app->db;
        $connection->open();

        $command = $connection->createCommand(
            "SELECT value FROM parametros WHERE nombre = '" . $name . "';");

        $parameter = $command->queryOne();
        $connection->close();

        return $parameter;
    }
}
