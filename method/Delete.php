<?php
/**
 * Delete your account.
 * @author gizmore
 */
final class Account_Delete extends GWF_MethodForm
{
	public function getUserType() { return GWF_User::MEMBER; }
	public function isEnabled() { return Module_Account::instance()->cfgFeatureDeletion(); }
	
	public function execute()
	{
		return Module_Account::instance()->renderAccountTabs()->add(parent::execute());
	}
	
	public function createForm(GWF_Form $form)
	{
		$fields = array(
			GDO_Box::make('info')->content(t('box_info_deletion')),
			GDO_Message::make('accrm_note'),
			GDO_Submit::make(),
			GDO_AntiCSRF::make(),
		);
		$form->addFields($fields);
	}
	
	public function formValidated(GWF_Form $form)
	{
		$user = GWF_User::current();
		
		# Store note in database
		if ($note = $form->getVar('accrm_note'))
		{
			GWF_AccountDelete::insertNote($user, $note);
		}
		
		# Send note as email
		$this->onSendEmail($user, $note);			
		
		# Mark deleted
		$user->saveValue('user_deleted_at', time());
		GWF_Hook::call('UserQuit', [$user]);
		
		# Report and logout
		return $this->module->message('msg_account_marked_deleted')->add(method('Login', 'Logout')->execute());
	}
	
	private function onSendEmail(GWF_User $user, $note)
	{
		foreach (GWF_User::admins() as $admin)
		{
			$mail = new GWF_Mail();
			$mail->setSender(GWF_BOT_EMAIL);
			$mail->setSenderName(GWF_BOT_NAME);
			$mail->setSubject(tusr($admin, 'mail_subj_account_deleted', [$this->getSiteName(), $user->displayName()]));
			$mail->setBody(tusr($admin, 'mail_body_account_deleted', array($user->displayUsername(), htmlspecialchars($note))));
			$mail->sendToUser($admin);
		}
	}
	
}
