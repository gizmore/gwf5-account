<?php
/**
 * Member Account Changes.
 * 
 * @author gizmore
 * @version 5.0
 * @since 1.0
 * 
 * @see GWF_User
 */
final class Module_Account extends GWF_Module
{
	##################
	### GWF_Module ###
	##################
	public function onLoadLanguage() { return $this->loadLanguage('lang/account'); }
	public function getClasses() { return ['GWF_AccountAccess', 'GWF_AccountChange', 'GWF_AccountDelete', 'GWF_AccountSetting']; }

	##############
	### Config ###
	##############
	public function getConfig()
	{
		return array(
			GDO_Int::make('adult_age')->unsigned()->min(12)->max(40)->initial('21'),
			GDO_Duration::make('account_changetime')->min(0)->initial(GWF_Time::ONE_MONTH * 3),
			GDO_Checkbox::make('allow_guest_settings')->initial('1'),
			GDO_Checkbox::make('allow_country_change')->initial('1'),
			GDO_Checkbox::make('allow_lang_change')->initial('1'),
			GDO_Checkbox::make('allow_birthday_change')->initial('1'),
			GDO_Checkbox::make('allow_gender_change')->initial('1'),
			GDO_Checkbox::make('allow_email_change')->initial('1'),
			GDO_Checkbox::make('allow_email_fmt_change')->initial('1'),
			GDO_Checkbox::make('allow_email_show_change')->initial('1'),
			GDO_Checkbox::make('allow_online_show_change')->initial('1'),
			GDO_Checkbox::make('allow_adult_show_change')->initial('1'),
			GDO_Checkbox::make('allow_birthday_show_change')->initial('1'),
			GDO_Checkbox::make('feature_access_history')->initial('1'),
			GDO_Checkbox::make('feature_account_deletion')->initial('1'),
			GDO_Checkbox::make('feature_gpg_engine')->initial('1'),
			GDO_Checkbox::make('feature_demographic_mail_confirm')->initial('1'),
		);
	}
	
	#############
	### Hooks ###
	#############
	public function hookUserAuthenticated(array $args=null)
	{
		GWF_AccountAccess::onAccess($this, $args[0]);
	}

	##################
	### Convinient ###
	##################
	public function cfgDemoMail() { return $this->getConfigValue('feature_demographic_mail_confirm'); }
	public function cfgAdultAge() { return $this->getConfigValue('adult_age'); }
	public function cfgChangeTime() { return $this->getConfigValue('account_changetime'); }
	public function cfgAllowGuests() { return $this->getConfigValue('allow_guest_settings'); }

	public function cfgAllowCountryChange() { return $this->getConfigValue('allow_country_change'); }
	public function cfgAllowLanguageChange() { return $this->getConfigValue('allow_lang_change'); }
	public function cfgAllowBirthdayChange() { return $this->getConfigValue('allow_birthday_change'); }
	public function cfgAllowGenderChange() { return $this->getConfigValue('allow_gender_change'); }
	public function cfgAllowEmailChange() { return $this->getConfigValue('allow_email_change'); }
	public function cfgAllowEmailFormatChange() { return $this->getConfigValue('allow_email_fmt_change'); }
	public function cfgAllowEmailVisibleChange() { return $this->getConfigValue('allow_email_show_change'); }
	public function cfgAllowOnlineVisibleChange() { return $this->getConfigValue('allow_online_show_change'); }
	public function cfgAllowAdultOptionsChange() { return $this->getConfigValue('allow_adult_show_change'); }
	public function cfgAllowBirthdayOptionsChange() { return $this->getConfigValue('allow_birthday_show_change'); }
	
	public function cfgFeatureAccess() { return $this->getConfigValue('feature_access_history'); }
	public function cfgFeatureDeletion() { return $this->getConfigValue('feature_account_deletion'); }
	public function cfgFeatureGPGEngine() { return $this->getConfigValue('feature_gpg_engine'); }
	
	##############
	### Navbar ###
	##############
	/**
	 * Add account link to right sidebar, if user can use it.
	 */
	public function onRenderFor(GWF_Navbar $navbar)
	{
		if ($navbar->isRight())
		{
			$user = GWF_User::current();
			if ( ($user->isMember()) || ($user->isGuest() && $this->cfgAllowGuests()) )
			{
				$navbar->addField(GDO_Button::make('btn_account')->href($this->getMethodHREF('Form')));
			}
		}
	}
	
	public function renderAccountTabs()
	{
		return $this->templatePHP('overview.php');
	}

}
