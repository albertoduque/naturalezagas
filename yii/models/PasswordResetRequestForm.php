<?php
namespace app\models;

use Yii;
use app\models\User;
use yii\base\Model;
use yii\helpers\Html;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\app\models\User',
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => 'There is no user with such email.'
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);
       
        if ($user) {
            if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
               
                $user->generatePasswordResetToken();
            }
          
            if ($user->save()) {
                
                $resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
                
                 $txt= '<div class="password-reset">
                        <p>Hola '.Html::encode($user->username).',</p>

                        <p>Ingrese en el siguiente link para reiniciar su contraseña:</p>

                        <p>'.Html::a(Html::encode($resetLink), $resetLink).'</p>
                    </div>';
                $to = $this->email;
                $subject = 'Reiniciar contraseña de ' . \Yii::$app->name;
       
                $cabeceras  = 'MIME-Version: 1.0' . "\r\n";
                $cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

                // Cabeceras adicionales
                $cabeceras .= 'To: Alberto Duque <duque.alberto@gmail.com>' . "\r\n";
                $cabeceras .= 'From: '.\Yii::$app->name .' <'.\Yii::$app->params['supportEmail'].'>' . "\r\n";
    
                if( mail($to,$subject,$txt,$cabeceras))
                {
                     return true;
                }
                
                
               
//           return \Yii::$app->mailer->compose(['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'], ['user' => $user])
//                    ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
//                    ->setTo($this->email)
//                    ->setSubject('Reiniciar contraseña de ' . \Yii::$app->name)
//                    ->send();
//                
//            }
          
            }

        return false;
    }
}
}