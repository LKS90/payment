<?php

/**
 * Contains \Drupal\payment\Plugin\Payment\MethodConfiguration\PaymentMethodConfigurationInterface.
 */

namespace Drupal\payment\Plugin\Payment\MethodConfiguration;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Plugin\PluginFormInterface;

/**
 * A payment method configuration plugin.
 */
interface PaymentMethodConfigurationInterface extends PluginInspectionInterface, ConfigurablePluginInterface, PluginFormInterface {

  /**
   * Gets the plugin label.
   *
   * @return string
   */
  public function getPluginLabel();

  /**
   * Gets the plugin description.
   *
   * @return string
   */
  public function getPluginDescription();

}
