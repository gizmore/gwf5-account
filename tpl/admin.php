<?php
echo Module_Account::instance()->renderAdminTabs();

$numWaitingActivation = 0;

echo GDO_Box::make()->content(t('box_content_account_admin', [$numWaitingActivation]))->render();
