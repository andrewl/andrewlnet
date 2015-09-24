<?php

/**
 * @file
 * Contains \Drupal\tmgmt_language_combination\Plugin\field\formatter\LanguageCombinationDefaultFormatter.
 */

namespace Drupal\tmgmt_language_combination\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'tmgmt_language_combination_table' formatter.
 *
 * @FieldFormatter(
 *   id = "tmgmt_language_combination_table",
 *   label = @Translation("Table"),
 *   field_types = {
 *     "tmgmt_language_combination",
 *   }
 * )
 */
class LanguageCombinationTableFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items) {
    $rows = array();

    foreach ($items as $item) {
      $to = tmgmt_language_combination_language_label($item->language_to);
      $from = tmgmt_language_combination_language_label($item->language_from);
      $row[] = array(
        'data' => $from,
        'class' => array('from-language', drupal_html_class('language-' . $from)),
      );

      $row[] = array(
        'data' => $to,
        'class' => array('to-language', drupal_html_class('language-' . $to)),
      );

      $rows[] = array(
        'data' => $row,
        'class' => array(drupal_html_class($from . '-' . $to)),
      );
    }

    return array(
      '#theme' => 'table',
      '#header' => array(t('From'), t('To')),
      '#rows' => $rows,
    );
  }

}
