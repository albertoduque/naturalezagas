<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "usuario_evento".
 *
 * @property int $id
 * @property int $usuario id del usuario asociado
 * @property int $evento id del evento asociado
 * @property string $created_at
 * @property string $modified_at
 * @property int $deleted
 */
class UsuarioEvento extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usuario_evento';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['usuario', 'evento'], 'required'],
            [['usuario', 'evento', 'deleted'], 'integer'],
            [['created_at', 'modified_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'usuario' => 'Usuario',
            'evento' => 'Evento',
            'created_at' => 'Created At',
            'modified_at' => 'Modified At',
            'deleted' => 'Deleted',
        ];
    }
}
