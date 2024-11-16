<?php

namespace Drupal\dropi\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 *
 */
class DropiSettingsForm extends ConfigFormBase {

  /**
   *
   */
  protected function getEditableConfigNames() {
    return ['dropi.settings'];
  }

  /**
   *
   */
  public function getFormId() {
    return 'dropi_settings_form';
  }

  /**
   *
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('dropi.settings');

    $countries = [
      'CL' => $this->t('Chile'),
      'CO' => $this->t('Colombia'),
      'EC' => $this->t('Ecuador'),
      'ES' => $this->t('España'),
      'MX' => $this->t('Mexico'),
      'PA' => $this->t('Panama'),
      'PE' => $this->t('Perú'),
      'PY' => $this->t('Paraguay'),
    ];

    foreach ($countries as $key => $value) {
      $form['dropi_integration_keys'][$key] = [
        '#type' => 'textarea',
        '#title' => $this->t('Dropi Integration Key') . ' - ' . $value,
        '#default_value' => $config->get('dropi_integration_keys.' . $key),
        // '#required' => TRUE,
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   *
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('dropi.settings')
      ->set('dropi_integration_keys.CL', $form_state->getValue('CL'))
      ->set('dropi_integration_keys.CO', $form_state->getValue('CO'))
      ->set('dropi_integration_keys.EC', $form_state->getValue('EC'))
      ->set('dropi_integration_keys.ES', $form_state->getValue('ES'))
      ->set('dropi_integration_keys.MX', $form_state->getValue('MX'))
      ->set('dropi_integration_keys.PA', $form_state->getValue('PA'))
      ->set('dropi_integration_keys.PE', $form_state->getValue('PE'))
      ->set('dropi_integration_keys.PY', $form_state->getValue('PY'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
