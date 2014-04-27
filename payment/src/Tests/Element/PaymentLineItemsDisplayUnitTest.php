<?php

/**
 * @file
 * Contains \Drupal\payment\Tests\PaymentLineItemsDisplayUnitTest.
 */

namespace Drupal\payment\Tests\Element;

use Drupal\payment\Generate;
use Drupal\simpletest\DrupalUnitTestBase;

/**
 * Tests the payment_line_items_display element.
 */
class PaymentLineItemsDisplayUnitTest extends DrupalUnitTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = array('currency', 'field', 'payment', 'system', 'user');

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'description' => '',
      'name' => 'payment_line_items_display element unit test',
      'group' => 'Payment',
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installConfig(array('currency'));
  }

  /**
   * Tests the element.
   */
  protected function testElement() {
    $element = array(
      '#payment' => Generate::createPayment(2),
      '#type' => 'payment_line_items_display',
    );
    $output = drupal_render($element);
    $strings = array('<table', t('Total amount'), t('Quantity'), 'payment-line-item-name-foo', 'payment-line-item-plugin-payment_basic', 'NLG 9.90');
    foreach ($strings as $string) {
      $this->assertNotIdentical(strpos($output, $string), FALSE);
    }
  }
}
