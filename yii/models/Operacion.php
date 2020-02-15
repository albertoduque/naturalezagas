<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "operacion".
 *
 * @property integer $idOperacion
 * @property string $nombre
 *
 * @property RolOperacion[] $rolOperacions
 */
class Operacion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'operacion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idOperacion' => 'id',
            'nombre' => 'Nombre',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRolOperacions()
    {
        return $this->hasMany(RolOperacion::className(), ['operacion_idOperacion' => 'idOperacion']);
    }
}
