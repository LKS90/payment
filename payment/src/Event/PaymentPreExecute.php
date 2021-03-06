<?php

/**
 * @file
 * Contains \Drupal\payment\Event\PaymentPreExecute.
 */

namespace Drupal\payment\Event;

use Drupal\payment\Entity\PaymentInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Provides an event that is dispatched before a payment is executed.
 *
 * @see \Drupal\payment\Event\PaymentEvents::PAYMENT_PRE_EXECUTE
 */
class PaymentPreExecute extends Event {

  /**
   * The payment.
   *
   * @var \Drupal\payment\Entity\PaymentInterface
   */
  protected $payment;

  /**-
   * Constructs a new class instance.
   *
   * @param \Drupal\payment\Entity\PaymentInterface $payment
   *   The payment that will be executed.
   *
   * @param \Drupal\Core\Session\AccountInterface
   */
  public function __construct(PaymentInterface $payment) {
    $this->payment = $payment;
  }

  /**
   * Gets the payment that will be executed
   *
   * @return \Drupal\payment\Entity\PaymentInterface
   */
  public function getPayment() {
    return $this->payment;
  }

}
