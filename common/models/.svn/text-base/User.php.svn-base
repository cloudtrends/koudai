<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\base\UserException;
use yii\db\Query;
use common\models\UserPassword;
use common\models\UserPayPassword;
use common\models\UserAccount;
use common\models\UserContact;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $id_card
 * @property integer $real_verify_status
 * @property integer $card_bind_status
 * @property integer $is_novice
 * @property integer $status
 * @property integer $created_at
 */
class User extends ActiveRecord implements IdentityInterface
{
	// 用户状态
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    
    // 实名认证状态
    const REAL_STATUS_NO = 0;
    const REAL_STATUS_YES = 1;
    
    // 绑卡状态
    const BINDCARD_STATUS_NO = 0;	// 未绑卡
    const BINDCARD_STATUS_P2P = 1;	// 绑了普通投资卡，后面可能扩充货币基金
    
    // 用户来源
    const SOURCE_NORMAL = 1; //普通注册
    const SOURCE_WZDAI = 2;
    
    // 性别
    const SEX_NOSET = 0;
    const SEX_MALE = 1;
    const SEX_FEMALE = 2;
    public static $sexes = array(
    	self::SEX_NOSET => '未知',
    	self::SEX_MALE => '男',
    	self::SEX_FEMALE => '女',
    );
    
    // 手机验证正则表达式
    const PHONE_PATTERN = '/^1[0-9]{10}$/';

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
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['real_verify_status', 'default', 'value' => self::REAL_STATUS_NO],
            ['card_bind_status', 'default', 'value' => self::BINDCARD_STATUS_NO],
            ['is_novice', 'default', 'value' => 1],
            
            ['source', 'default', 'value' => self::SOURCE_NORMAL],
            
            ['phone', 'required', 'message' => '手机号不能为空'],
            ['phone', 'match', 'pattern' => self::PHONE_PATTERN, 'message' => '手机号格式错误'],
            ['phone', 'unique', 'message' => '已经存在该号码'],
            ['username', 'required', 'message' => '用户名不能为空'],
            ['username', 'unique', 'message' => '已经存在该用户'],
            [['username'], 'string', 'max' => 18, 'message' => '用户名不能超过18位'],
            ['id_card',  'unique', 'message' => '已经存在该身份证号'],
            [['sex', 'realname', 'birthday', 'is_novice'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     * @see IdentityInterface
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     * @see IdentityInterface
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $userid = ( new \yii\db\Query())->select(['user_name'])->from(UserContact::tableName())->where(['contact_id'=>$token])->scalar();
        if (!$userid){
            return false;
        }
//        Yii::error($token);
//        Yii::error($userid);
//        Yii::error(var_export(static::findOne(['username' => $userid]),true));
    	return static::findOne(['username' => $userid]);
    }

    /**
     * @inheritdoc
     * @see IdentityInterface
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     * @see IdentityInterface
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     * @see IdentityInterface
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
    
    /**
     * Finds user by username
     *
     * @param string $username
     * @return User|null
     */
    public static function findByUsername($username)
    {
    	return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }
    
    /**
     * Finds user by phone
     *
     * @param string $phone
     * @return User|null
     */
    public static function findByPhone($phone)
    {
    	return static::findOne(['phone' => $phone, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
    	if ($this->userPassword) {
        	return Yii::$app->security->validatePassword($password, $this->userPassword->password);
    	} else {
    		return false;
    	}
    }
    
    /**
     * 验证交易密码
     * @param string $payPassword
     */
    public function validatePayPassword($payPassword)
    {
    	if ($this->userPayPassword) {
    		return Yii::$app->security->validatePassword($payPassword, $this->userPayPassword->password);
    	} else {
    		return false;
    	}
    }
    
    /**
     * 用户投资记录条数
     */
    public function getInvestCount()
    {
    	$kdbInvestCount = KdbInvest::find()->where(['user_id' => $this->id])->count();
    	$projectInvestCount = ProjectInvest::find()->where(['user_id' => $this->id])->count();
    	return $kdbInvestCount + $projectInvestCount;
    }
    
    /**
     * 用户已完结投资记录
     * TODO:缓存2小时，进入完结项目列表时会强制更新该缓存
     */
    public function getFinishedProjInvestCount($forceFlush = false)
    {
//     	$expression = $forceFlush ? 'time()' : 'false';
//     	$dependency = new \yii\caching\ExpressionDependency(['expression' => $expression]);
    	$db = Yii::$app->db;
    	$count = $db->cache(function($db){
			$sql = 'SELECT count(*)
					FROM ' . ProjectInvest::tableName() . '
					WHERE user_id = ' . $this->id . ' and status IN (' . implode(',', [
						ProjectInvest::STATUS_CANCELED,
						ProjectInvest::STATUS_FULLY_ASSIGNED,
						ProjectInvest::STATUS_REPAYED
					]) . ')';
    		return $db->createCommand($sql)->queryScalar();
    	}, 2 * 3600);
    	return $count;
    }
    
    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }
    
    /**
     * 判断是否是认证用户
     */
    public function getIsRealVerify()
    {
    	return $this->real_verify_status == self::REAL_STATUS_YES && $this->realname && $this->id_card;
    }
    
    /**
     * 判断是否绑银行卡
     */
    public function getIsBindCard()
    {
    	return $this->card_bind_status;
    }
    
    /**
     * 加交易锁
     */
    public function addTradeLock()
    {
    	if (Yii::$app->cache->get("userlock:{$this->id}") == '1') {
    		throw new UserException('您有其他资金交易尚未完成，请稍后再试');
    	}
    	// 30秒后自动过期
    	Yii::$app->cache->set("userlock:{$this->id}", '1', 30);
// 		if (UserRedis::HGET($this->id, 'trade_lock') == '1') {
// 			throw new UserException('操作太频繁，请稍后再试');
// 		}
// 		UserRedis::HSET($this->id, 'trade_lock', '1');
    }
    
    /**
     * 释放交易锁
     */
    public function releaseTradeLock()
    {
    	Yii::$app->cache->delete("userlock:{$this->id}");
//     	UserRedis::HSET($this->id, 'trade_lock', '0');
    }
    
    /**
     * 关联对象：密码表记录
     * @return UserPassword|null
     */
    public function getUserPassword()
    {
    	return $this->hasOne(UserPassword::className(), ['user_id' => 'id']);
    }
    
    /**
     * 关联对象：支付密码表记录
     * @return UserPayPassword|null
     */
    public function getUserPayPassword()
    {
    	return $this->hasOne(UserPayPassword::className(), ['user_id' => 'id']);
    }
    
    /**
     * 关联对象：用户资金信息
     * @return UserAccount|null
     */
    public function getAccount()
    {
    	return $this->hasOne(UserAccount::className(), ['user_id' => 'id']);
    }
    
    /**
     * 关联对象：用户资金信息
     * @return UserBankCard|null
     */
    public function getBankCards()
    {
    	
    }
}
