<?php
/**
 * Method only is triggered by Form.
 * Consists of two mail sending, old and new.
 * 
 * @author gizmore
 *
 */
final class Account_ChangeEmail extends GWF_Method
{
	public function getUserType() { return GWF_User::MEMBER; }
	public function isEnabled() { return Module_Account::instance()->cfgAllowEmailChange(); }
	
	public function execute()
	{
		if (Common::getPostString('changemail'))
		{
			return $this->onRequestB();
		}
		if ($token = Common::getGetString('token'))
		{
			return $this->onChangeA($token);
		}
		if ($token = Common::getGet('change'))
		{
			return $this->onChangeB($token);
		}
	}
	
	public static function changeEmail(Module_Account $module, GWF_User $user, $newMail)
	{
		if ($module->cfgUseEmail() && $user->getMail())
		{
			return self::sendEmail($module, $user, $newMail);
		}
		else
		{
			return self::sendEmailB($module, $user->getID(), $newMail);
		}
	}
	
	private static function sendEmail(Module_Account $module, GWF_User $user, $newMail)
	{
		$mail = new GWF_Mail();
		$mail->setReceiver($user->getMail());
		$mail->setSender(GWF_BOT_EMAIL);
		$mail->setSenderName(GWF_BOT_NAME);
		$mail->setSubject(t('mail_subj_chmail_a', [$this->getSiteName()]));
		$newmail = trim(Common::getPost('email'));
		$link = self::createLink($module, $user, $newMail);
		$mail->setBody(t('mail_body_chmail_a', [$user->displayName(), $this->getSiteName(), $link]));
		$mail->sendToUser($user);
	}
	
	private static function createLink(Module_Account $module, GWF_User $user, $newMail)
	{
		$userid = $user->getID();
		$row = GWF_AccountChange::addRow($userid, 'email', $newMail);
		$token = $row->getToken();
		return GWF_HTML::anchor(url('Account', 'ChangeEmail', "&userid=$userid&token=$token"));
	}
	
	private function onChangeA($token)
	{
		if (!($row = GWF_AccountChange::getRow($userid, 'email', $token)))
		{
			return $this->error('err_token_chmaila');
		}
		return $this->templateChangeMailB($row);
	}
	
	private function getChangeMailForm(GWF_AccountChange $ac)
	{
		$form = new GWF_Form();
		$form->title('ft_change_mail', [$this->getSiteName()]);
		$form->addFields(array(
			GDO_Email::make('email')->required(),
			GDO_Email::make('email_re')->required()->label('retype'),
			GDO_Hidden::make('token')->value($ac->getToken()),
			GDO_Hidden::make('userid')->value($ac->getUserID()),
			GDO_Submit::make(),
			GDO_AntiCSRF::make(),
		));
		$data = array(
			'email' => array(GWF_Form::STRING, '', $this->module->lang('th_email_new')),
			'email_re' => array(GWF_Form::STRING, '', $this->module->lang('th_email_re')),
			'changemail' => array(GWF_Form::SUBMIT, $this->module->lang('btn_changemail')),
			'token' => array(GWF_Form::HIDDEN, $ac->getVar('token')),
			'' => array(GWF_Form::HIDDEN, $ac->getVar('userid')),
		);
		return new GWF_Form('GWF_User', $data);	
	}
	
	private function templateChangeMailB(GWF_AccountChange $ac)
	{
		$form = $this->getChangeMailForm($ac);
		
		$tVars = array(
			'form' => $form->templateY($this->module->lang('chmail_title')),
		);
		return $this->module->template('changemail.tpl', $tVars);
	}
	
	private function onRequestB()
	{
		$token = Common::getPost('token');
		$userid = (int) Common::getPost('userid');
		if (false === ($row = GWF_AccountChange::checkToken($userid, $token, 'email'))) {
			return $this->module->error('err_token');
		}
		
		$email1 = Common::getPost('email');
		$email2 = Common::getPost('email_re');
		if (!GWF_Validator::isValidEmail($email1)) {
			return $this->module->error('err_email_invalid').$this->templateChangeMailB($row);
		}
		
		if ($email1 !== $email2) {
			return $this->module->error('err_email_retype').$this->templateChangeMailB($row);
		}
		
		if (GWF_User::getByEmail($email1) !== false) {
			return $this->module->error('err_email_taken');
		}
		
		if (false === $row->delete()) {
			return GWF_HTML::err('ERR_DATABASE', array( __FILE__, __LINE__));
		}
		
		return self::sendEmailB($this->module, $userid, $email1);
	}
	
	private static function sendEmailB(Module_Account $module, $userid, $email)
	{
		$token = GWF_AccountChange::createToken($userid, 'email2', $email);
		
		$mail = new GWF_Mail();
		$mail->setSender($module->cfgMailSender());
		$mail->setReceiver($email);
		$mail->setSubject($module->lang('chmailb_subj'));
		
		if (false === ($user = GWF_User::getByID($userid))) {
			return GWF_HTML::err('ERR_UNKNOWN_USER');
		}
		
		$link = self::getLinkB($token, $userid);
		$body = $module->lang('chmailb_body', array( $user->display('user_name'), $link));
		$mail->setBody($body);

		if (!$mail->sendToUser($user)) {
			return GWF_HTML::err('ERR_MAIL_SENT');
		}
		
		return $module->message('msg_mail_sent', array(htmlspecialchars($email)));
	}
	
	private static function getLinkB($token, $userid)
	{
		$url = Common::getAbsoluteURL(sprintf('index.php?mo=Account&me=ChangeEmail&userid=%s&change=%s', $userid, $token));
		return sprintf('<a href="%s">%s</a>', $url, $url);
	}

	private function onChangeB($token)
	{
		$userid = (int) Common::getGet('userid');
		if (false === ($ac = GWF_AccountChange::checkToken($userid, $token, 'email2')))
		{
			return $this->module->error('err_token');
		}
		
		if (false === ($user = $ac->getUser()))
		{
			return GWF_HTML::err('ERR_UNKNOWN_USER');
		}
		
		if (false === $ac->delete())
		{
			return GWF_HTML::err('ERR_DATABASE', array( __FILE__, __LINE__));
		}
		
		$oldmail = $user->getValidMail();
		$newmail = $ac->getVar('data');
		
		if (false === GWF_Hook::call(GWF_Hook::CHANGE_MAIL, $user, array($oldmail, $newmail)))
		{
			return GWF_HTML::err('ERR_DATABASE', array( __FILE__, __LINE__));
		}
		
		if (false === $user->saveVar('user_email', $newmail))
		{
			return GWF_HTML::err('ERR_DATABASE', array( __FILE__, __LINE__));
		}
		
		if (false === $user->saveOption(GWF_User::MAIL_APPROVED, true))
		{
			return GWF_HTML::err('ERR_DATABASE', array( __FILE__, __LINE__));
		}
		
		return $this->module->message('msg_mail_changed', array(htmlspecialchars($newmail)));
	}
}

?>
