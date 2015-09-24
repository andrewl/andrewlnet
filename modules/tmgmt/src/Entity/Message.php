<?php

/*
 * @file
 * Contains Drupal\tmgmt\Plugin\Core\Entity\Message.
 */

namespace Drupal\tmgmt\Entity;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\tmgmt\MessageInterface;

/**
 * Entity class for the tmgmt_message entity.
 *
 * @ContentEntityType(
 *   id = "tmgmt_message",
 *   label = @Translation("Translation Message"),
 *   uri_callback = "tmgmt_message_uri",
 *  handlers = {
 *    "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *    "views_data" = "Drupal\tmgmt\Entity\ViewsData\MessageViewsData",
 *   },
 *   base_table = "tmgmt_message",
 *   entity_keys = {
 *     "id" = "mid",
 *     "uuid" = "uuid"
 *   }
 * )
 *
 * @ingroup tmgmt_job
 */
class Message extends ContentEntityBase implements MessageInterface {
  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['mid'] = BaseFieldDefinition::create('integer')
      ->setLabel('Message ID')
      ->setReadOnly(TRUE);;
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The node UUID.'))
      ->setReadOnly(TRUE);
    $fields['tjid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Job reference'))
      ->setSetting('target_type', 'tmgmt_job');
    $fields['tjiid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Job item reference'))
      ->setSetting('target_type', 'tmgmt_job_item');
    $fields['type'] = BaseFieldDefinition::create('string')
      ->setLabel('Message type')
      ->setDefaultValue('status');
    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Actor'))
      ->setDescription(t('The user who performed the action.'))
      ->setSettings(array(
        'target_type' => 'user',
      ))
      ->setDefaultValue(0);
    $fields['message'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Message'));
    $fields['variables'] = BaseFieldDefinition::create('map')
      ->setLabel(t('Variables'));
    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel('Created time');
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultLabel() {
    $created = format_date($this->created->value);
    switch ($this->type->value) {
      case 'error':
        return t('Error message from @time', array('@time' => $created));

      case 'status':
        return t('Status message from @time', array('@time' => $created));

      case 'warning':
        return t('Warning message from @time', array('@time' => $created));

      case 'debug':
        return t('Debug message from @time', array('@time' => $created));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getMessage() {
    $text = $this->message->value;
    if ($this->variables->first()->toArray()) {
      return t($text, $this->variables->first()->toArray());
    }
    else {
      return t($text);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getJob() {
    return $this->get('tjid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getJobItem() {
    return $this->get('tjid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return $this->get('type')->value;
  }

}
