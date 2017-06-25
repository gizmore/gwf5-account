<?php
/**
 * An account deletion note.
 * @author gizmore
 */
final class GWF_AccountDelete extends GDO
{
	public function gdoCached() { return false; }
	
	###########
	### GDO ###
	###########
	public function gdoColumns()
	{
		return array(
			GDO_Username::make('accrm_username')->primary(),
			GDO_Message::make('accrm_note')->notNull(),
		);
	}

	##############
	### Static ###
	##############
	public static function insertNote(GWF_User $user, string $note)
	{
		return self::blank(['accrm_username' => $user->getName(), 'accrm_note' => $note])->insert();
	}
}
