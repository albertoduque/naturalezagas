<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rol".
 *
 * @property integer $idRol
 * @property string $nombre
 *
 * @property RolOperacion[] $rolOperacions
 * @property Usuario[] $usuarios
 */
class Rol extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rol';
    }
    
    public $permiso;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre'], 'string', 'max' => 45],
            [['permiso'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idRol' => 'Id',
            'nombre' => 'Nombre',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRolOperacions()
    {
        return $this->hasMany(RolOperacion::className(), ['rol_idRol' => 'idRol']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarios()
    {
        return $this->hasMany(Usuario::className(), ['rol_idRol' => 'idRol']);
    }
     public static function dropdown() {
        $models = static::find()->all();
        foreach ($models as $model) {
            $dropdown[$model->idRol] = $model->nombre;
        }
          return $dropdown;
    }
    public static function checkListPermision() {
        $models = AuthItem::find()->where(["type"=>2])->all();
        foreach ($models as $model) {
            $dropdown[$model->name] = $model->name;
        }
          return $dropdown;
    }
}
