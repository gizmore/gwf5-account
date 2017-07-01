<?php
final class Account_Admin extends GWF_Method
{
	public function getPermission()
	{
		return 'staff';
	}
	
	public function execute()
	{
		return Module_Account::instance()->onRenderAdminTabs()->add($this->renderPage());
	}
	
	public function renderPage()
	{
		return $this->templatePHP('admin.php');
	}
	
}
