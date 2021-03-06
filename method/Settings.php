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
			if ($module->getUserSettings() || $module->getUserConfig())
			{
				$name = $module->getName();
				$href = href('Account', 'Settings', "&module=$name");
				$button = GDO_Link::make("link_$name")->rawlabel($name)->href($href)->icon('settings');
				$navbar->addField($button);
			}
		}
		return $navbar->render();
	}
	
	public function createForm(GWF_Form $form)
	{
	    $moduleName = $this->configModule->getName();
		$this->title('ft_account_settings', [$this->getSiteName(), $moduleName]);
		if ($settings = $this->configModule->getUserSettings())
		{
		    $form->addField(GDO_Divider::make()->label('div_user_settings', [$moduleName]));
		    foreach ($settings as $gdoType)
		    {
		        $value = GWF_UserSetting::get($gdoType->name)->getValue();
		        $form->addField($gdoType->value($value));
		    }
		}
		if ($settings = $this->configModule->getUserConfig())
		{
		    $form->addField(GDO_Divider::make()->label('div_variables', [$moduleName]));
			foreach ($settings as $gdoType)
			{
				$value = GWF_UserSetting::get($gdoType->writable(false)->name)->getValue();
				$form->addField($gdoType->value($value));
			}
		}
		$form->addField(GDO_AntiCSRF::make());
		$form->addField(GDO_Submit::make());
	}
	
	public function formValidated(GWF_Form $form)
	{
		$info = [];
		foreach ($this->configModule->getUserSettings() as $gdoType)
		{
			if ($gdoType->writable)
			{
				$key = $gdoType->name;
				$old = GWF_UserSetting::get($key)->getValue();
				$new = $form->getVar($key);
				if ($old !== $new)
				{
					GWF_UserSetting::set($key, $new);
					$old = $old === null ? '<i class="null">null</i>' : GWF_HTML::escape($old);
					$new = $new === null ? '<i class="null">null</i>' : GWF_HTML::escape($new);
					$info[] = t('msg_modulevar_changed', [$gdoType->displayLabel(), $old, $new]);
				}
			}
		}
		return $this->message('msg_settings_saved', [$this->configModule->getName(), implode('<br/>', $info)])->add($this->renderPage());
	}
}
