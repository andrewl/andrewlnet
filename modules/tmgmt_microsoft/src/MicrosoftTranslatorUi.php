<?php
/**
 * @file
 * Contains \Drupal\tmgmt_microsoft\MicrosoftTranslatorUi.
 */

namespace Drupal\tmgmt_microsoft;

use Drupal\tmgmt\TranslatorPluginUiBase;
use Drupal\tmgmt\TranslatorInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Microsoft translator UI.
 */
class MicrosoftTranslatorUi extends TranslatorPluginUiBase {

  /**
   * {@inheritdoc}
   */
  public function pluginSettingsForm(array $form, FormStateInterface $form_state, TranslatorInterface $translator, $busy = FALSE) {
    $register_app = 'https://datamarket.azure.com/developer/applications/';
    $form['client_id'] = array(
      '#type' => 'textfield',
      '#title' => t('Client ID'),
      '#default_value' => $translator->getSetting('client_id'),
      '#description' => t('Please enter the Client ID, or follow this <a href="!link">link</a> to set it up.', array('!link' => $register_app)),
    );
    $generate_url = 'https://datamarket.azure.com/developer/applications/edit/' . $translator->getSetting('client_id');
    $form['client_secret'] = array(
      '#type' => 'textfield',
      '#title' => t('Client secret'),
      '#default_value' => $translator->getSetting('client_secret'),
      '#description' => t('Please enter the Client Secret, or follow this <a href="!link">link</a> to generate one.', array('!link' => $generate_url)),
    );

    return parent::pluginSettingsForm($form, $form_state, $translator);
  }

}
