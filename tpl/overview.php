<?php
$module = Module_Account::instance();

# Create a horizontal navbar.
$bar = GWF_Navbar::create(10, 'row');

# Add buttons to bar
$bar->addField(GDO_Button::make('link_account_form')->href(href('Account', 'Form')));
if ($module->cfgFeatureGPGEngine()) :
	$bar->addField(GDO_Button::make('link_account_encryption')->href(href('Account', 'Encryption')));
endif;
if ($module->cfgFeatureAccess()) : 
	$bar->addField(GDO_Button::make('link_account_security')->href(href('Account', 'Security')));
	$bar->addField(GDO_Button::make('link_account_access')->href(href('Account', 'Access')));
endif;
if ($module->cfgFeatureDeletion()) :
	$bar->addField(GDO_Button::make('link_account_delete')->href(href('Account', 'Delete')));
endif;

# Render
echo $bar->render();
