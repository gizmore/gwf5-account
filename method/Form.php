<?php
/**
 * Change account settings.
 * @author gizmore
 * @version 5.0
 * @since 2.0
 */
final class Account_Form extends GWF_MethodForm
{
	public function isUserRequired() { return true; }
	public function isGuestAllowed() { return Module_Account::instance()->cfgAllowGuests(); }
	
	public function execute()
	{
		$delay = GWF_Time::humanDuration(Module_Account::instance()->cfgChangeTime());
		return Module_Account::instance()->renderAccountTabs()->add(
				GDO_Box::make()->content(t('infobox_account_form', [$delay]))->render()->add(
						parent::execute()));
	}
	
	################
	### The Form ###
	################
	public function createForm(GWF_Form $form)
	{
		$m = Module_Account::instance();
		$user = GWF_User::current();
		
		# Section1
		$form->addField(GDO_Divider::make('div1')->label('section_login'));
		if ($user->isGuest()) :
		$form->addField($user->gdoColumn('user_guest_name')->writable(false));
		else :
		$form->addField($user->gdoColumn('user_name')->writable(false));
		$form->addField($user->gdoColumn('user_real_name')->writable(!$user->getRealName()));
		endif;
		
		# Section2
		$form->addField(GDO_Divider::make('div2')->label('section_email'));
		$form->addField($user->gdoColumn('user_email')->writable($m->cfgAllowEmailChange()));
		$form->addField($user->gdoColumn('user_email_fmt')->writable($m->cfgAllowEmailFormatChange()));
		if ($m->cfgAllowEmailVisibleChange()) $form->addField($user->gdoColumn('user_allow_email'));
		
		$form->addField(GDO_Divider::make('div3')->label('section_demographic'));
		$form->addField($user->gdoColumn('user_language')->writable($m->cfgAllowLanguageChange()));
		$form->addField($user->gdoColumn('user_country')->writable($m->cfgAllowCountryChange()));
		if ($m->cfgAllowGenderChange()) $form->addField($user->gdoColumn('user_gender'));
		if ($m->cfgAllowBirthdayChange()) $form->addField($user->gdoColumn('user_birthdate'));

		$form->addField(GDO_Divider::make('div4')->label('section_options'));
		if ($m->cfgAllowOnlineVisibleChange()) $form->addField($user->gdoColumn('user_hide_online'));
		if ($m->cfgAllowAdultOptionsChange()) $form->addField($user->gdoColumn('user_want_adult'));
		if ($m->cfgAllowBirthdayOptionsChange()) $form->addField($user->gdoColumn('user_show_birthdays'));
		
		$form->addField(GDO_Submit::make());
		$form->addField(GDO_AntiCSRF::make());
		
		$form->withGDOValuesFrom($user);
	}

	#######################
	### Change Settings ###
	#######################
	public function formValidated(GWF_Form $form)
	{
		$back = '';

		$m = Module_Account::instance();
		$user = GWF_User::current();
		$guest = $user->isGuest();
		
		if ($m->cfgAllowAdultOptionsChange() && $user->getAge() >= $m->cfgAdultAge())
		{
			$back .= $this->changeFlag($form, $user, 'user_want_adult');
		}
		if ($m->cfgAllowOnlineVisibleChange())
		{
			$back .= $this->changeFlag($form, $user, 'user_hide_online');
		}
		if ($m->cfgAllowBirthdayOptionsChange())
		{
			$back .= $this->changeFlag($form, $user, 'user_show_birthdays');
		}
		if ( (!$guest) && ($m->cfgAllowEmailVisibleChange()) )
		{
			$back .= $this->changeFlag($form, $user, 'user_allow_email');
		}
		
		# Real Name
		if ( (!$guest) && ($m->cfgAllowRealName()) )
		{
			if ($realname = $form->getVar('user_real_name'))
			{
				$user->setVar('user_real_name', $realname);
				$back .= t('msg_real_name_now', [$realname]);
			}
		}
		
		# Email Format
		if ( (!$guest) && $m->cfgAllowEmailFormatChange() )
		{
			$oldfmt = $user->getVar('user_email_fmt');
			$newfmt = $form->getVar('user_email_fmt');
			if ($newfmt !== $oldfmt)
			{
				$user->setVar('user_email_fmt', $newfmt);
				$back .= t('msg_email_fmt_now_'.$newfmt);
			}
		}
		
		# Change EMAIL
		if ( (!$guest) && ($m->cfgAllowEmailChange()) )
		{
			$oldmail = $user->getVar('user_email');
			$newmail = $form->getVar('user_email');
			if ($newmail !== $oldmail)
			{
				include 'ChangeEmail.php';
				$back .= Account_ChangeEmail::changeEmail($this->module, $user, $newmail);
			}
		}
		
		
		# Change Demo
		$demo_changed = false;

		$oldcid = $user->getVar('user_country');
		$newcid = $m->cfgAllowCountryChange() ? $form->getVar('user_country') : $oldcid;
		if ($oldcid !== $newcid) { $demo_changed = true; }
		$oldlid = $user->getVar('user_language');
		$newlid = $m->cfgAllowLanguageChange() ? $form->getVar('user_language') : $oldlid;
		if ($oldlid !== $newlid) { $demo_changed = true; }
		$oldgender = $user->getVar('user_gender');
		$newgender = $m->cfgAllowGenderChange() ? $form->getVar('user_gender') : $oldgender;
		if ($oldgender !== $newgender) { $demo_changed = true; }
		$oldbirthdate = $user->getVar('user_birthdate');
		$newbirthdate = $m->cfgAllowBirthdayChange() ? $form->getVar('user_birthdate') : $oldbirthdate;
		if ($oldbirthdate != $newbirthdate) { $demo_changed = true; }
		
		if ($demo_changed)
		{
			if ($guest)
			{
				$user->setVars(array(
					'user_country' => $newcid,
					'user_language' => $newlid,
					'user_gender' => $newgender,
					'user_birthdate' => $newbirthdate,
				));
				$back .= t('msg_demo_changed');
			}
			else
			{
				$data = array(
					'user_country' => $newcid,
					'user_language' => $newlid,
					'user_gender' => $newgender,
					'user_birthdate' => $newbirthdate,
				);
				require_once 'ChangeDemo.php';
				$back .= Account_ChangeDemo::requestChange($this->module, $user, $data);
			}
		}
		
		$user->save();
		
		return GWF_Message::make($back)->add($this->renderPage());
	}
	
	private function changeFlag(GWF_Form $form, GWF_User $user, $flagname)
	{
		$newFlag = $form->getVar($flagname);
		if ($newFlag !== $user->getVar($flagname))
		{
			$user->setVar($flagname, $newFlag);
			return t('msg_'.$flagname.($newFlag?'_on':'_off'));
		}
	}
	
}
