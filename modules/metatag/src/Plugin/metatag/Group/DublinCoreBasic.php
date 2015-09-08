<?php
/**
 * @file
 * Contains \Drupal\metatag\Plugin\metatag\Group\DublinCoreBasic.
 */

namespace Drupal\metatag\Plugin\metatag\Group;

use Drupal\metatag\Plugin\metatag\Group\GroupBase;

/**
 * The Dublin Core Basic group.
 *
 * @MetatagGroup(
 *   id = "dublin_core_basic",
 *   label = @Translation("Dublin Core - Basic tags"),
 *   description = @Translation("The Dublin Core Metadata Element Set, aka 'Dublin Core meta tags', are a set of internationally standardized metadata tags used to describe content to make identification and classification of content easier; the standards are controlled by the <a href='http://dublincore.org/'>Dublin Core Metadata Initiative (DCMI)</a>."),
 *   weight = 7
 * )
 */
class DublinCoreBasic extends GroupBase {
  // Inherits everything from Base.
}
