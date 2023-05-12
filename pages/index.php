<?php

use RexGraphQL\Auth\SharedSecretAuthenticationService;

echo rex_view::title($this->getProperty('page')['title']);

if (rex_post('settings', 'array')) {
    $this->setConfig(rex_post('settings', [
        [SharedSecretAuthenticationService::SHARED_SECRET_CONFIG_KEY, 'string'],
    ]));
}

echo '<form action="' . rex_url::currentBackendPage() . '" method="post">';
$form = '';
$elements = [];
$elements['label'] = rex_i18n::msg('graphql.auth_shared_secret');

$textInput = new rex_input_text();
$textInput->setAttribute('name', 'settings[' . SharedSecretAuthenticationService::SHARED_SECRET_CONFIG_KEY . ']');
$textInput->setAttribute('value', rex_config::get('graphql', 'auth_shared_secret'));
$elements['field'] = $textInput->getHtml();

$formElements[] = $elements;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$form .= $fragment->parse('core/form/form.php');


$elements = [];
$formElements = [];
$elements['field'] = '
  <input type="submit" class="btn btn-save rex-form-aligned" name="config[submit]" value="' . rex_i18n::msg('mblock_config_save') . '" ' . rex::getAccesskey(rex_i18n::msg('mblock_config_save'), 'save') . ' />
';
$formElements[] = $elements;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$buttons = $fragment->parse('core/form/submit.php');


//////////////////////////////////////////////////////////
// parse form fragment
$fragment = new rex_fragment();
$fragment->setVar('class', 'edit', false);
$fragment->setVar('title', rex_i18n::msg('settings'));
$fragment->setVar('body', $form, false);
$fragment->setVar('buttons', $buttons, false);
echo $fragment->parse('core/page/section.php');
echo '</form>';
