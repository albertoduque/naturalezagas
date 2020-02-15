<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipo_evento".
 *
 * @property integer $id
 * @property string $nombre
 * @property string $created_at
 * @property integer $deleted
 */
class TipoEvento extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tipo_evento';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre'], 'required'],
            [['created_at'], 'safe'],
            [['deleted'], 'integer'],
            [['nombre'], 'string', 'max' => 120],
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
            'deleted' => 'Deleted',
        ];
    }
}
