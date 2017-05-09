<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */
namespace fecshop\app\apphtml5\modules\Customer\block\account;
use Yii;
use fec\helpers\CModule;
use fec\helpers\CRequest;
use yii\base\InvalidValueException;
use fecshop\app\apphtml5\helper\mailer\Email;


/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Login {
	
	
	public function getLastData($param = ''){
		$loginParam = \Yii::$app->getModule('customer')->params['login'];
		$loginPageCaptcha = isset($loginParam['loginPageCaptcha']) ? $loginParam['loginPageCaptcha'] : false;
		$email = isset($param['email']) ? $param['email'] : '';
		return [
			'loginPageCaptcha' => $loginPageCaptcha,
			'email' => $email,
			'googleLoginUrl' => Yii::$service->customer->google->getLoginUrl('customer/google/loginv'),
			'facebookLoginUrl' => Yii::$service->customer->facebook->getLoginUrl('customer/facebook/loginv'),
		];
	}
	
	public function login($param){
		$captcha = $param['captcha'];
		$loginParam = \Yii::$app->getModule('customer')->params['login'];
		$loginPageCaptcha = isset($loginParam['loginPageCaptcha']) ? $loginParam['loginPageCaptcha'] : false;
		if($loginPageCaptcha && !$captcha){
			Yii::$service->page->message->addError(['Captcha can not empty']);
			return;
		}else if($captcha && $loginPageCaptcha && !\Yii::$service->helper->captcha->validateCaptcha($captcha)){
			Yii::$service->page->message->addError(['Captcha is not right']);
			return;
		}
		if(is_array($param) && !empty($param)){
			if(Yii::$service->customer->login($param)){
				# 发送邮件
				if($param['email']){
					$this->sendLoginEmail($param);
				}
			}
		}
		Yii::$service->page->message->addByHelperErrors();
		if(!Yii::$app->user->isGuest){
			//Yii::$service->url->redirectByUrlKey('customer/account');
			Yii::$service->customer->loginSuccessRedirect('customer/account');
		}
		
	}
	/**
	 * 发送登录邮件
	 */
	public function sendLoginEmail($param){
		if($param){
			Yii::$service->email->customer->sendLoginEmail($param);
		}
	}
	
	
	
}