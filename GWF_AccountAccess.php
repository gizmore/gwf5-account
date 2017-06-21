<?php
/**
 * Table with user login history.
 * Alerts user on suspicous change of IP / InternetServiceProvider / UserAgent
 * 
 * @author gizmore
 * @version 5.0
 * @since 3.0
 * 
 * @see GWF_User
 * @see GWF_AccountSetting
 */
final class GWF_AccountAccess extends GDO
{
	public function gdoCached() { return false; }
	
	###########
	### GDO ###
	###########
	public function gdoColumns()
	{
		return array(
			GDO_AutoInc::make('accacc_id'),
			GDO_User::make('accacc_uid')->index(),
			GDO_MD5::make('accacc_ua')->notNull(),
			GDO_IP::make('accacc_ip')->notNull(),
			GDO_MD5::make('accacc_isp'),
			GDO_CreatedAt::make('accacc_time'),
		);
	}

	/**
	 * On authentication, check the old history against current data.
	 * Mail on suspicous activity.
	 * Add a new entry.
	 * @param Module_Account $module
	 * @param GWF_User $user
	 */
	public static function onAccess(Module_Account $module, GWF_User $user)
	{
		$setting = GWF_AccountSetting::forUser($user);
		
		$query = '';
		
		# Check UA
		$ua = self::uahash();
		if ($setting->alertOnUserAgent())
		{
			$query .= " AND ".self::hash_check('accacc_ua', $ua);
		}
		
		# Check exact IP
		$ip = GDO_IP::current();
		if ($setting->alertOnIPChange())
		{
			$query .= " AND accacc_ip=".GDO::quoteS($ip);
		}
		
		# Check ISP
		$isp = null;
		if ($setting->alertOnISPChange())
		{
			$isp = self::isphash();
			$query .= ' AND '.self::hash_check('accacc_isp', $isp);
		}
		
		# Query alert
		if (!empty($query))
		{
			if (0 != self::table()->select('COUNT(*)')->where("accacc_uid={$user->getID()}")->exec()->fetchValue())
			{
				if (!self::table()->select('1')->where("accacc_uid={$user->getID()} $query")->exec()->fetchValue())
				{
					self::sendAlertMail($module, $user);
				}
			}
		}
		
		if ($setting->recordIPs())
		{
			# New access insert
			self::blank(array(
				'accacc_uid' => $user->getID(),
				'accacc_ua' => $ua,
				'accacc_ip' => $ip,
				'accacc_isp' => $isp,
			))->insert();
		}
	}
	
	private static function isphash()
	{
		if (GDO_IP::current() === ($isp = @gethostbyaddr($_SERVER['REMOTE_ADDR'])))
		{
			$isp = null;
		}
		return self::hash($isp);
	}
	
	private static function uahash()
	{
		return self::hash(preg_replace('/\d/', '', $_SERVER['HTTP_USER_AGENT']));
	}
	
	private static function hash_check($field, $hash, $quote='"')
	{
		return $hash === null ? $field.' IS NULL' : $field.'='.quote($hash);
	}
	
	private static function hash($value)
	{
		return $value === null ? null : md5($value, true);
	}
	
	public static function sendAlertMail(Module_Account $module, GWF_User $user, string $append='')
	{
		if ($receive_mail = $user->getMail())
		{
			$mail = new GWF_Mail();
			$mail->setSender(GWF_BOT_EMAIL);
			$mail->setSenderName(GWF_BOT_NAME);
			$mail->setReceiver($receive_mail);
			$mail->setSubject(t("mail_subj_account_alert$append", [GWF5::instance()->getSiteName()]));
			$mail->setBody(t("mail_body_account_alert$append", array(
				$user->displayName(),
				GWF5::instance()->getSiteName(),
				GWF_HTML::escape($_SERVER['HTTP_USER_AGENT']),
				$_SERVER['REMOTE_ADDR'],
				gethostbyaddr($_SERVER['REMOTE_ADDR']),
				GWF_HTML::anchor(url('Account', 'Access')),
				GWF_HTML::anchor(url('Account', 'Form')),
			)));
			$mail->sendToUser($user);
		}
	}
	
}
