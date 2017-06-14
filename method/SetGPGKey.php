<?php
/**
 * GPG Mail links here to finally save the GPG key.
 * @author gizmore
 * @since 3.0
 * @version 5.0
 * @see GWF_Mail
 */
final class Account_SetGPGKey extends GWF_Method
{
	public function isEnabled() { return Module_Account::instance()->cfgFeatureGPGEngine(); }
	
	public function execute()
	{
		$user = GWF_User::table()->find(Common::getGetString('userid'));
		$tmpfile = GWF_PATH . 'temp/gpg/' . $user->getID();
		$file_content = file_get_contents($tmpfile);
		unlink($tmpfile);

		if (!($fingerprint = GWF_PublicKey::grabFingerprint($file_content)))
		{
			return $this->error('err_gpg_fail_fingerprinting');
		}
		
		if (Common::getGetString('token') !== $fingerprint)
		{
			return $this->error('err_gpg_token');
		}
		
		GWF_PublicKey::updateKey($user->getID(), $file_content);
		
		return $this->message('msg_gpg_key_added');
	}
}
