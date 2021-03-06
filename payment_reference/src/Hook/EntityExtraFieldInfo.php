<?php

/**
 * @file
 * Contains \Drupal\payment_reference\Hook\EntityExtraFieldInfo.
 */

namespace Drupal\payment_reference\Hook;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;

/**
 * Implements hook_entity_extra_field_info().
 *
 * @see payment_reference_entity_extra_field_info()
 */
class EntityExtraFieldInfo {

  use StringTranslationTrait;

  /**
   * Constructs a new class instance.
   *
   * @param \Drupal\Core\StringTranslation\TranslationInterface $string_translation
   */
  public function __construct(TranslationInterface $string_translation) {
    $this->stringTranslation = $string_translation;
  }

  /**
   * Invokes the implementation.
   */
  public function invoke() {
    $fields['payment']['payment_reference']['form'] = array(
      'line_items' => array(
        'label' => $this->t('Line items'),
        'weight' => 0,
      ),
      'payment_method' => array(
        'label' => $this->t('Payment method selection and configuration'),
        'weight' => 1,
      ),
    );

    return $fields;
  }

}
