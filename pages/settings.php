<?php

$form   = rex_config_form::factory('headless');

$field = $form->addTextField('frontend_base_url', null, ['class' => 'form-control', 'placeholder' => 'https://example.com']);
$field->setLabel(rex_i18n::msg('headless_frontend_base_url'));
$formOutput = $form->get();

$fragment = new rex_fragment();
$fragment->setVar('class', 'edit', false);
$fragment->setVar('body', $formOutput, false);
echo $fragment->parse('core/page/section.php');
