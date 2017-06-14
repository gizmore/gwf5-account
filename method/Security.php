<?php
/**
 * Toggle account security switches.
 * @author gizmore
 * @since 4.0
 * @version 5.0
 */
final class Account_Security extends GWF_MethodForm
{
	public function getUserType() { return GWF_User::MEMBER; }
	public function isEnabled() { return Module_Account::instance()->cfgFeatureAccess(); }
	
	/**
	 * @var GWF_User
	 */
	private $user;
	
	/**
	 * @var GWF_AccountSetting
	 */
	private $settings;
	
	/**
	 * Load user and settings used in method.
	 * Render Tabs first. Append this methods response to it.
	 * {@inheritDoc}
	 * @see GWF_MethodForm::execute()
	 */
	public function execute()
	{
		$this->user = GWF_User::current();
		$this->settings = GWF_AccountSetting::forUser($this->user);
		return Module_Account::instance()->renderAccountTabs()->add(parent::execute());
	}

	/**
	 * Take the checkboxes from GWF_AccountSetting class, which is a GDO. The columns are GDOType.
	 * Add a submit button and csrf. 
	 * {@inheritDoc}
	 * @see GWF_MethodForm::createForm()
	 */
	public function createForm(GWF_Form $form)
	{
		$form->addFields($this->settings->getGDOColumns(['accset_record_ip', 'accset_uawatch', 'accset_ipwatch', 'accset_ispwatch']));
		$form->addFields(array(
			GDO_Submit::make(),
			GDO_AntiCSRF::make(),
		));
	}
	
	/**
	 * On successful validation, save the new toggles.
	 * In case we turned IP recording off, send an error mail.
	 * {@inheritDoc}
	 * @see GWF_MethodForm::formValidated()
	 */
	public function formValidated(GWF_Form $form)
	{
		$beforeEnabeld = $this->setting->recordIPs();
		$this->settings->saveVars($form->values());
		if ( ($beforeEnabeld) && (!$this->setting->recordIPs()) )
		{
			GWF_AccountAccess::sendAlertMail($this->module, $this->user, 'record_disabled');
		}
	}
}
