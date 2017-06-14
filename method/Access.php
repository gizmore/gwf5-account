<?php
/**
 * Tabular overview of old logins.
 * @author gizmore
 */
final class Account_Access extends GWF_MethodTable
{
	public function getUserType() { return GWF_User::MEMBER; }
	public function isEnabled() { return Module_Account::instance()->cfgFeatureAccess(); }
	
	public function execute()
	{
		return Module_Account::instance()->renderAccountTabs()->add(parent::execute());
	}
	
	public function getHeaders()
	{
		$headers = array(
			GDO_Count::make(),
		);
		return array_merge($headers, GWF_AccountAccess::table()->getGDOColumns(['accacc_time', 'accacc_ip']));
	}
	
	public function getQuery()
	{
		$userid = GWF_User::current()->getID();
		return GWF_AccountAccess::table()->query()->from('gwf_account_access')->where('accacc_uid='.$userid);
	}

	public function getResult()
	{
		return $this->getQuery()->debug()->exec();
	}
	
	public function getResultCount()
	{
		return $this->getQuery()->select('COUNT(*)')->exec()->fetchValue();
	}
	
}
