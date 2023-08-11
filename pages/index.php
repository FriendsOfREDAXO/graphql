<?php

use RexGraphQL\Auth\AuthService;
use RexGraphQL\Auth\JwtService;
use RexGraphQL\RexGraphQL;

echo rex_view::title($this->getProperty('page')['title']);

if (rex_post('settings', 'array')) {
    $this->setConfig(rex_post('settings', [
        [AuthService::SHARED_SECRET_CONFIG_KEY, 'string'],
        [RexGraphQL::MODE_CONFIG_KEY, 'string'],
        [JwtService::JWT_SETTINGS_KEY, 'string']
    ]));
}

echo '<form action="' . rex_url::currentBackendPage() . '" method="post">';
$form = '';
$elements = [];
$elements['label'] = rex_i18n::msg('graphql.auth_shared_secret');
$textInput = new rex_input_text();
$textInput->setAttribute('name', 'settings[' . AuthService::SHARED_SECRET_CONFIG_KEY . ']');
$textInput->setAttribute('value', rex_config::get('graphql', AuthService::SHARED_SECRET_CONFIG_KEY));
$elements['field'] = $textInput->getHtml();
$formElements[] = $elements;

$elements = [];
$elements['label'] = rex_i18n::msg('graphql.jwt_secret');
$textInput = new rex_input_text();
$textInput->setAttribute('name', 'settings[' . JwtService::JWT_SETTINGS_KEY . ']');
$textInput->setAttribute('value', rex_config::get('graphql', JwtService::JWT_SETTINGS_KEY));
$elements['field'] = $textInput->getHtml();
$formElements[] = $elements;

$elements = [];
$elements['label'] = rex_i18n::msg('graphql.addon_mode');
$selectInput = new rex_select();
$selectInput->addArrayOptions([
    RexGraphQL::HEADLESS_MODE_KEY => rex_i18n::msg('graphql.headless_mode'),
    RexGraphQL::ENDPOINT_MODE_KEY => rex_i18n::msg('graphql.endpoint_mode')
]);
$selectInput->setName('settings['. RexGraphQL::MODE_CONFIG_KEY . ']');
$selectInput->setSelected(rex_config::get('graphql', RexGraphQL::MODE_CONFIG_KEY));
$elements['field'] = $selectInput->get();
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
