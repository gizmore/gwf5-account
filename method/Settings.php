<?php
/**
 * Generic setting functionality.
 * Simply return GDOType[] in GWF_Module->getUserSettings() and you can configure stuff.
 * 
 * @author gizmore
 * @since 5.0
 */
final class Account_Settings extends GWF_MethodForm
{
	public function isUserRequired() { return true; }
	
	/**
	 * @var GWF_Module
	 */
	private $configModule;
	
	public function execute()
	{
		$tabs = Module_Account::instance()->renderAccountTabs();
		if ($this->configModule = GWF5::instance()->getModule(Common::getGetString('module')))
		{
			return $tabs->add($this->navModules())->add(parent::execute());
		}
		return $tabs->add($this->navModules())->add($this->infoBox());
	}
	
	public function infoBox()
	{
		return GDO_Box::make()->content(t('box_content_account_settings'))->render();
	}
	
	public function navModules()
	{
		$navbar = GWF_Navbar::create();
		foreach (GWF5::instance()->getActiveModules() as $module)
		{
			if ($settings = $module->getUserSettings())
			{
				$href = href('Account', 'Settings', '&module='.$module->getName());
				$button = GDO_Link::make()->rawlabel($module->getName())->href($href);
				$navbar->addField($button);
			}
		}
		return $navbar->render();
	}
	
	public function createForm(GWF_Form $form)
	{
		$this->title('ft_account_settings', [$this->getSiteName(), $this->configModule->getName()]);
		foreach ($this->configModule->getUserSettings() as $gdoType)
		{
			$value = GWF_UserSetting::get($gdoType->name)->getValue();
			$form->addField($gdoType->value($value));
		}
		$form->addField(GDO_AntiCSRF::make());
		$form->addField(GDO_Submit::make());
	}
	
	public function formValidated(GWF_Form $form)
	{
		$info = [];
		foreach ($this->configModule->getUserSettings() as $gdoType)
		{
			$key = $gdoType->name;
			$old = GWF_UserSetting::get($key)->getValue();
			$new = $form->getVar($key);
			if ($old !== $new)
			{
				GWF_UserSetting::set($key, $new);
				$info[] = t('msg_modulevar_changed', [$gdoType->displayLabel(), GWF_HTML::escape($old), GWF_HTML::escape($new)]);
			}
		}
		return $this->message('msg_settings_saved', [$this->configModule->getName(), implode('<br/>', $info)])->add($this->renderPage());
	}
}
