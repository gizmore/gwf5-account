<?php
# Create a horizontal navbar.
$bar = GDO_Bar::make();

# Add buttons to bar
$bar->addField(GDO_Link::make('link_account_admin')->href(href('Account', 'Admin'))->icon('admin'));
$bar->addField(GDO_Link::make('link_account_activations')->href(href('Account', 'Activations'))->icon('account_box'));
$bar->addField(GDO_Link::make('link_account_deletions')->href(href('Account', 'Deletions'))->icon('delete'));

# Render
echo $bar->renderCell();
