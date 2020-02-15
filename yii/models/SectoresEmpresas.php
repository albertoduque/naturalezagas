<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sectores_empresas".
 *
 * @property integer $id
 * @property string $nombre
 * @property string $created_at
 * @property string $modified_at
 * @property integer $deleted
 *
 * @property Empresas[] $empresas
 * @property Eventos[] $eventos
 */
class SectoresEmpresas extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sectores_empresas';
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
            [['nombre'], 'string', 'max' => 50],
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
            'deleted' => 'Estado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmpresas()
    {
        return $this->hasMany(Empresas::className(), ['id_sector_empresa' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventos()
    {
        return $this->hasMany(Eventos::className(), ['id_sector' => 'id']);
    }
}
