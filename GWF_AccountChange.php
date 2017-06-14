<?php
/**
 * A table to store tokens associated with a userid type and serialized data.
 * Used to change demographic options on a bunch via mail,
 * and to change the email.
 * Could be used in more stuff, like Recovery.
 * 
 * @author gizmore
 * @version 5.0
 * @since 3.0
 */
final class GWF_AccountChange extends GDO
{
	###########
	### GDO ###
	###########
	public function gdoColumns()
	{
		return array(
			GDO_User::make('accchg_user')->primary(),
			GDO_Enum::make('accchg_type')->enumValues('email','demo', 'demo_lock')->primary(),
			GDO_Token::make('accchg_token')->notNull(),
			GDO_Serialize::make('accchg_data'),
			GDO_CreatedAt::make('accchg_time'),
		);
	}
	
	##############
	### Getter ###
	##############
	public function getUser() { return $this->getValue('accchg_user'); }
	public function getUserID() { return $this->getVar('accchg_user'); }
	public function getTimestamp() { return $this->getVar('accchg_time'); }
	public function getToken() { return $this->getVar('accchg_token'); }
	
	##############
	### Static ###
	##############
	/**
	 * @param string $userid
	 * @param string $type
	 * @param mixed $data
	 * @return GWF_AccountChange
	 */
	public static function addRow(string $userid, string $type, $data=null)
	{
		$row = self::blank(['accchg_user' => $userid, 'accchg_type' => $type]);
		$row->setValue('accchg_data', $data);
		return $row->replace();
	}
	
	/**
	 * @param string $userid
	 * @param string $type
	 * @param string $token
	 * @return self
	 */
	public static function getRow(string $userid, string $type, $token=true)
	{
		
		$condition = 'accchg_user=%s AND accchg_type=%s' . ($token===true?'':' AND accchg_token=%s');
		$condition = sprintf($condition, quote($userid), quote($type), quote($token));
		return self::table()->select()->where($condition)->exec()->fetchObject();
	}
}
