<?php

/**
 * Contains \Drupal\payment\PaymentProcessingInterface.
 */

namespace Drupal\payment;

use Drupal\payment\Plugin\Core\entity\PaymentInterface;

/**
 * Defines anything that can process payments.
 */
interface PaymentProcessingInterface {

  /**
   * Returns the supported currencies.
   *
   * @var array
   *   Keys are ISO 4217 currency codes. Values are associative arrays with
   *   keys "minimum" and "maximum", whose values are the minimum and maximum
   *   amount supported for the specified currency. Leave empty to allow all
   *   currencies.
   */
  public function currencies();

  /**
   * Returns the form elements to configure payments.
   *
   * $form_state['payment'] contains the payment that is added or edited. All
   * payment-specific information should be added to it during element
   * validation. The payment will be saved automatically.
   *
   * @param array $form
   * @param array $form_state
   *
   * @return array
   *   A render array.
   */
  public function paymentFormElements(array $form, array &$form_state);

  /**
   * Executes a payment.
   *
   * @param Payment $payment
   */
  public function executePayment(PaymentInterface $payment);

  /**
   * Validates a payment against a payment method and this controller. Don't
   * call directly. Use PaymentMethod::validate() instead.
   *
   * @see PaymentMethod::validate()
   *
   * @param Payment $payment
   * @param PaymentMethod $payment_method
   *
   * @throws PaymentValidationException
   */
  public function validatePayment(PaymentInterface $payment);
}