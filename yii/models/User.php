<?php
namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use app\models\Rol;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $nombre
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;
    const ROLE_ADMIN = 1;
    public $eventos;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }
   
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required',"message"=>"Usuario no puede estar vacío"],
            ['username', 'unique', 'targetClass' => '\app\models\User', 'message' => 'Este nombre de usuario ya existe.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => 'La dirección de correo ya existe.'],

            ['password_hash', 'required',"message"=>"Password no puede estar vacío"],
            ['password_hash', 'string', 'min' => 6],
            ['nombre', 'required'],
            ['nombre', 'safe'],
            ['cedula', 'required'],
            ['cedula', 'safe'],
            ['cedula', 'number', 'min' => 10000],
            ['cedula', 'unique', 'targetClass' => '\app\models\User', 'message' => 'La cedula ya existe.'],
            ['telefono', 'required'],
            ['telefono', 'safe'],
            ['eventos', 'safe'],
            ['rol_id', 'required'],
            ['id_evento', 'integer'],
            ['is_all', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
       // if (!static::isPasswordResetTokenValid($token)) {
      //      return null;
       // }

        // Revisar el password reset token valid
        
        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
       // $expire = Yii::$app->params['user.passwordResetTokenExpire'];//password_reset_token
        //$expire = Yii::$app->params['user.password_reset_token'];
        $expire=1;
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
    
    public function getRol()
    {
        return $this->hasOne(Rol::className(), ['idRol' => 'rol_id']);
    }
    
    public function isAdmin() {
        return Self::ROLE_ADMIN === $this->rol_id;
    }
    
    public static function getRoleUsers($role_name)
    {
        $connection = \Yii::$app->db;
        $connection->open();

        $command = $connection->createCommand(
            "SELECT user_id FROM auth_assignment WHERE item_name = '" . $role_name . "';");

        $users = $command->queryAll();
        $connection->close();

        return $users;
    }
     
    public static function getRoluser($id){
        if($id>0)
            return Rol::find()->select('nombre')->where(['idRol'=>$id])->orderBy('id')->one();
        else
            return Rol::find()->orderBy('idRol')->asArray()->all();
    }

//    public function getRoles($id){
//        $rol=$this->getRoluser($id); 
//        return $rol['nombre'];
//    }
    public static function toList()
    {
        $model = User::getRoluser(0);
        
        return \yii\helpers\ArrayHelper::map($model, 'idRol', 'nombre');
    }
    
    public static function checkListEvents() {
        $models = Eventos::find()->where(["deleted"=>0])->all();
        foreach ($models as $model) {
            $dropdown[$model->id] = $model->nombre;
        }
        return $dropdown;
    }
    

}
