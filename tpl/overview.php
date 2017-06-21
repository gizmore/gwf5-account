<?php
$module = Module_Account::instance();

# Create a horizontal navbar.
$bar = GWF_Navbar::create(10, 'row');

# Add buttons to bar
$bar->addField(GDO_Link::make('link_account_form')->href(href('Account', 'Form'))->icon('account_box'));
$bar->addField(GDO_Link::make('link_settings')->href(href('Account', 'Settings'))->icon('settings'));
if ($module->cfgFeatureGPGEngine()) :
	$bar->addField(GDO_Link::make('link_account_encryption')->href(href('Account', 'Encryption'))->icon('enhanced_encryption'));
endif;
if ($module->cfgFeatureAccess()) : 
	$bar->addField(GDO_Link::make('link_account_security')->href(href('Account', 'Security'))->icon('alarm_on'));
	$bar->addField(GDO_Link::make('link_account_access')->href(href('Account', 'Access'))->icon('date_range'));
endif;
if ($module->cfgFeatureDeletion()) :
	$bar->addField(GDO_Link::make('link_account_delete')->href(href('Account', 'Delete'))->icon('delete_sweep'));
endif;

# Render
echo $bar->render();
