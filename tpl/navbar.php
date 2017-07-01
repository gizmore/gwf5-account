<?php $navbar instanceof GWF_Navbar;
if ($navbar->isRight())
{
	$user = GWF_User::current();
	if ( ($user->isMember()) ||
		 ($user->isGuest() && Module_Account::instance()->cfgAllowGuests()) )
	{
		$navbar->addField(GDO_Link::make('btn_account')->href(href('Account', 'Form')));
	}
}
