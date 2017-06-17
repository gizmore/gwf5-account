<?php
/**
 * Demographic chance only once in a while.
 * 
 * @author gizmore
 * @version 5.0
 */
final class Account_ChangeDemo extends GWF_Method
{
	public function isTransactional() { return true; }
	
	public function execute()
	{
		if ($token = Common::getGetString('token'))
		{
			return $this->onChange($token);
		}
	}
	
	public static function requestChange(Module_Account $module, GWF_User $user, array $data)
	{
		if (true !== ($error = self::mayChange($module, $user)))
		{
			return $error;
		}
		
		if ($module->cfgDemoMail() && $user->hasMail())
		{
			return self::sendMail($module, $user, $data);
		}
		else
		{
			return self::change($module, $user, $data);
		}
	}
	
	private static function mayChange(Module_Account $module, GWF_User $user)
	{
		if ($row = GWF_AccountChange::getRow($user->getID(), 'demo_lock'))
		{
			$last = $row->getTimestamp();
			$elapsed = time() - $last;
			$min_wait = $module->cfgChangeTime();
			if ($elapsed < $min_wait)
			{
				$wait = $min_wait - $elapsed;
				return t('err_demo_wait', array(GWF_Time::humanDuration($wait)));
			}
		}
		return true;
	}
	
	public static function change(Module_Account $module, GWF_User $user, array $data)
	{
		$user->saveVars($data);
		GWF_AccountChange::addRow($user->getID(), 'demo_lock');
		return t('msg_demo_changed');
	}
	
	private static function sendMail(Module_Account $module, GWF_User $user, array $data)
	{
		$ac = GWF_AccountChange::addRow($user->getID(), 'demo', $data);
		
		$username = $user->displayName();
		$sitename = GWF5::instance()->getSiteName();
		$timeout = GWF_Time::humanDuration($module->cfgChangeTime());
		$gender = t('enum_'.$data['user_gender']);
		$country = GWF_Country::getByISOOrUnknown($data['user_country'])->displayName();
		$language = GWF_Language::getByISOOrUnknown($data['user_language'])->displayName();
		$birthdate = $data['user_birthdate'] > 0 ? GWF_Time::displayDate($data['user_birthdate'], 'day') : GWF_HTML::lang('unknown');
		$link = GWF_HTML::anchor(url('Account', 'ChangeDemo', sprintf("&userid=%d&token=%s", $user->getID(), $ac->getToken())));

		$args = [$username, $sitename, $timeout, $country, $language, $gender, $birthdate, $link];

		$mail = new GWF_Mail();
		$mail->setSender(GWF_BOT_EMAIL);
		$mail->setSenderName(GWF_BOT_NAME);
		$mail->setSubject(t('mail_subj_demochange', [$sitename]));
		$mail->setBody(t('mail_body_demochange', $args));
		$mail->sendToUser($user);
		return t('msg_mail_sent');
	}
	
	private function onChange($token)
	{
		$userid = Common::getGetString('userid');
		if (!($ac = GWF_AccountChange::getRow($userid, 'demo', $token)))
		{
			return $this->error('err_token');
		}
		if (!($user = GWF_User::getByID($userid)))
		{
			return $this->error('err_user');
		}
		
		$data = $ac->getData();
		$user->saveVars($data);
		$ac->delete();

		GWF_AccountChange::addRow($userid, 'demo_lock');
		
		return $this->message('msg_demo_changed');
	}
}
