<?php
/**
 * Tabular overview of old logins.
 * @author gizmore
 */
final class Account_Access extends GWF_MethodQueryTable
{
	public function getGDO() { return GWF_AccountAccess::table(); }

	public function getUserType() { return GWF_User::MEMBER; }
	
	public function isEnabled() { return Module_Account::instance()->cfgFeatureAccess(); }
	
	public function execute()
	{
		return Module_Account::instance()->renderAccountTabs()->add(parent::execute());
	}
	
	public function getQuery()
	{
		return parent::getQuery()->where('accacc_uid='.GWF_User::current()->getID());
	}
	
	public function getHeaders()
	{
		$headers = array(
			GDO_Count::make(),
		);
		return array_merge($headers, GWF_AccountAccess::table()->getGDOColumns(['accacc_time', 'accacc_ip']));
	}
	
}
