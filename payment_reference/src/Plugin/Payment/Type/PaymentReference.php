<?php

/**
 * Contains \Drupal\payment_reference\Plugin\Payment\Type\PaymentReference.
 */

namespace Drupal\payment_reference\Plugin\Payment\Type;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\payment\Plugin\Payment\Type\PaymentTypeBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * The payment reference field payment type.
 *
 * @PaymentType(
 *   configuration_form = "\Drupal\payment_reference\Plugin\Payment\Type\PaymentReferenceConfigurationForm",
 *   id = "payment_reference",
 *   label = @Translation("Payment reference field")
 * )
 */
class PaymentReference extends PaymentTypeBase implements ContainerFactoryPluginInterface {

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * The URL generator.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface
   */
  protected $urlGenerator;

  /**
   * Constructs a new class instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   The event dispatcher.
   * @param \Drupal\Core\Routing\UrlGeneratorInterface $url_generator
   *   The URL generator.
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $string_translation
   *   The string translator.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, EventDispatcherInterface $event_dispatcher, UrlGeneratorInterface $url_generator, EntityManagerInterface $entity_manager, TranslationInterface $string_translation) {
    $configuration += $this->defaultConfiguration();
    parent::__construct($configuration, $plugin_id, $plugin_definition, $event_dispatcher);
    $this->urlGenerator = $url_generator;
    $this->entityManager = $entity_manager;
    $this->stringTranslation = $string_translation;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('event_dispatcher'),
      $container->get('url_generator'),
      $container->get('entity.manager'),
      $container->get('string_translation')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array(
      'bundle' => NULL,
      'entity_type_id' => NULL,
      'field_name' => NULL,
    ) + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  protected function doResumeContext() {
    // If the payment method does not interrupt the payment type context, the
    // payer is still in the original context and we do not need to redirect
    // them back.
    if ($this->getPayment()->getPaymentMethod()->isPaymentExecutionInterruptive()) {
      $url = $this->urlGenerator->generateFromRoute('payment_reference.resume_context', array(
        'payment' => $this->getPayment()->id(),
      ), array(
        'absolute' => TRUE,
      ));
      $response = new RedirectResponse($url);
      $listener = function(FilterResponseEvent $event) use ($response) {
        $event->setResponse($response);
      };
      $this->eventDispatcher->addListener(KernelEvents::RESPONSE, $listener, 999);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function resumeContextAccess(AccountInterface $account) {
    return $this->getPayment()->getOwnerId() == $account->id();
  }

  /**
   * {@inheritdoc}
   */
  public function paymentDescription($language_code = NULL) {
    $field_definitions = $this->entityManager->getFieldDefinitions($this->getEntityTypeId(), $this->getBundle());

    return isset($field_definitions[$this->getFieldName()]) ? $field_definitions[$this->getFieldName()]->getLabel() : $this->t('Unavailable');
  }

  /**
   * Sets the ID of the entity type the payment was made for.
   *
   * @param string $entity_type_id
   *
   * @return $this
   */
  public function setEntityTypeId($entity_type_id) {
    $this->configuration['entity_type_id'] = $entity_type_id;

    return $this;
  }

  /**
   * Gets the ID of the entity type the payment was made for.
   *
   * @return string
   */
  public function getEntityTypeId() {
    return $this->configuration['entity_type_id'];
  }

  /**
   * Sets the bundle of the entity the payment was made for.
   *
   * @param string $bundle
   *
   * @return $this
   */
  public function setBundle($bundle) {
    $this->configuration['bundle'] = $bundle;

    return $this;
  }

  /**
   * Gets the bundle of the entity the payment was made for.
   *
   * @return string
   */
  public function getBundle() {
    return $this->configuration['bundle'];
  }

  /**
   * Sets the name of the field the payment was made for.
   *
   * @param string $field_name
   *
   * @return $this
   */
  public function setFieldName($field_name) {
    $this->configuration['field_name'] = $field_name;

    return $this;
  }

  /**
   * Gets the name of the field the payment was made for.
   *
   * @return string
   */
  public function getFieldName() {
    return $this->configuration['field_name'];
  }

}
