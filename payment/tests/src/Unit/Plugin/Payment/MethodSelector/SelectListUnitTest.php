<?php

/**
 * @file
 * Contains
 * \Drupal\Tests\payment\Unit\Plugin\Payment\MethodSelector\SelectListUnitTest.
 */

namespace Drupal\Tests\payment\Unit\Plugin\Payment\MethodSelector {

use Drupal\payment\Plugin\Payment\MethodSelector\SelectList;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\payment\Plugin\Payment\MethodSelector\SelectList
 *
 * @group Payment
 */
class SelectListUnitTest extends UnitTestCase {

  /**
   * The current user used for testing.
   *
   * @var \Drupal\Core\Session\AccountInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $currentUser;

  /**
   * The payment method manager used for testing.
   *
   * @var \Drupal\payment\Plugin\Payment\Method\PaymentMethodManagerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $paymentMethodManager;

  /**
   * The payment method selector plugin under test.
   *
   * @var \Drupal\payment\Plugin\Payment\MethodSelector\SelectList
   */
  protected $paymentMethodSelector;

  /**
   * The ID of the payment method selector plugin under test.
   *
   * @var string
   */
  protected $paymentMethodSelectorPluginId;

  /**
   * The string translation service.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $stringTranslation;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->currentUser = $this->getMock('\Drupal\Core\Session\AccountInterface');

    $this->paymentMethodManager = $this->getMock('\Drupal\payment\Plugin\Payment\Method\PaymentMethodManagerInterface');

    $this->stringTranslation = $this->getMock('\Drupal\Core\StringTranslation\TranslationInterface');
    $this->stringTranslation->expects($this->any())
      ->method('translate')
      ->will($this->returnArgument(0));

    $this->paymentMethodSelectorPluginId = $this->randomMachineName();
    $this->paymentMethodSelector = new SelectList(array(), $this->paymentMethodSelectorPluginId, array(), $this->currentUser, $this->paymentMethodManager, $this->stringTranslation);
  }

  /**
   * @covers ::buildSelector
   */
  public function testBuildSelector() {
    $this->stringTranslation->expects($this->any())
      ->method('translate')
      ->will($this->returnArgument(0));

    $method = new \ReflectionMethod($this->paymentMethodSelector, 'buildSelector');
    $method->setAccessible(TRUE);
    $get_element_id_method = new \ReflectionMethod($this->paymentMethodSelector, 'getElementId');
    $get_element_id_method->setAccessible(TRUE);

    $payment_method_id = $this->randomMachineName();
    $payment_method_label = $this->randomMachineName();
    $payment_method = $this->getMock('\Drupal\payment\Plugin\Payment\Method\PaymentMethodInterface');
    $payment_method->expects($this->any())
      ->method('getPluginId')
      ->will($this->returnValue($payment_method_id));
    $payment_method->expects($this->any())
      ->method('getPluginLabel')
      ->will($this->returnValue($payment_method_label));

    $this->paymentMethodSelector->setPaymentMethod($payment_method);

    $element = array(
      '#parents' => array('foo', 'bar'),
    );
    $form_state = $this->getMock('\Drupal\Core\Form\FormStateInterface');
    $available_payment_methods = array($payment_method);

    $expected_build_payment_method_id = array(
      '#ajax' => array(
        'callback' => array('Drupal\payment\Plugin\Payment\MethodSelector\SelectList', 'ajaxSubmitConfigurationForm'),
        'effect' => 'fade',
        'event' => 'change',
        'trigger_as' => array(
          'name' => 'foo[bar][select][container][change]',
        ),
        'wrapper' => $get_element_id_method->invokeArgs($this->paymentMethodSelector, array($form_state)),
      ),
      '#default_value' => $payment_method_id,
      '#empty_value' => 'select',
      '#options' => array(
        $payment_method_id => $payment_method_label,
      ) ,
      '#required' => FALSE,
      '#title' => 'Payment method',
      '#type' => 'select',
    );
    $expected_build_change = array(
      '#ajax' => array(
        'callback' => array('Drupal\payment\Plugin\Payment\MethodSelector\AdvancedPaymentMethodSelectorBase', 'ajaxRebuildForm'),
      ),
      '#attributes' => array(
        'class' => array('js-hide')
      ),
      '#limit_validation_errors' => array(array('foo', 'bar', 'select', 'payment_method_id')),
      '#name' => 'foo[bar][select][container][change]',
      '#submit' => array(array($this->paymentMethodSelector, 'rebuildForm')),
      '#type' => 'submit',
      '#value' => 'Choose payment method',
    );
    $build = $method->invokeArgs($this->paymentMethodSelector, array($element, $form_state, $available_payment_methods));
    $this->assertEquals($expected_build_payment_method_id, $build['container']['payment_method_id']);
    $this->assertEquals($expected_build_change, $build['container']['change']);
    $this->assertSame('container', $build['container']['#type']);
  }

}

}

namespace {

  if (!function_exists('drupal_get_path')) {
    function drupal_get_path() {}
  }
  if (!function_exists('drupal_html_id')) {
    function drupal_html_id() {}
  }

}
