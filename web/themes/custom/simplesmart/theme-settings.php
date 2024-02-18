<?php

/**
 * @file
 * Simplesmart theme settings.
 */

use Drupal\Core\Form\FormStateInterface;

/**
 * Font-family is selected.
 */
function simplesmart_form_system_theme_settings_alter(&$form, FormStateInterface $form_state) {

  $form['font_family'] = [
    '#type' => 'select',
    '#title' => t('Font Family'),
    '#options' => [
      'none' => t('N/A'),
      'pacifico' => t('Pacifico'),
    ],
    '#default_value' => theme_get_setting('font_family'),
  ];
}
