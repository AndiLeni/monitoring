<?php


$addon = rex_addon::get('monitoring');
$form = rex_config_form::factory($addon->name);

$form->addFieldset('Pushover');

$field = $form->addRadioField('pushover_enabled');
$field->setLabel('Pushover Benachrichtigungen');
$field->addOption('aktiviert', 1);
$field->addOption('deaktiviert', 0);

$field = $form->addInputField('password', 'pushover_user_key', null, ["class" => "form-control"]);
$field->setLabel('User Key');

$field = $form->addInputField('password', 'pushover_api_key', null, ["class" => "form-control"]);
$field->setLabel('Application API Token');



$form->addFieldset('E-Mail');

$field = $form->addRadioField('email_enabled');
$field->setLabel('E-Mail Benachrichtigungen');
$field->addOption('aktiviert', 1);
$field->addOption('deaktiviert', 0);

$field = $form->addInputField('email', 'email_receiver', null, ["class" => "form-control"]);
$field->setLabel('Empfänger');

$fragment = new rex_fragment();
$fragment->setVar('class', 'edit', false);
$fragment->setVar('title', "Einstellungen", false);
$fragment->setVar('body', $form->get(), false);
echo $fragment->parse('core/page/section.php');
