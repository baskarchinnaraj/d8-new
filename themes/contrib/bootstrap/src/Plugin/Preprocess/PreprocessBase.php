<?php
/**
 * @file
 * Contains \Drupal\bootstrap\Plugin\Preprocess\PreprocessBase.
 */

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Plugin\PluginBase;
use Drupal\bootstrap\Utility\Element;
use Drupal\bootstrap\Utility\Variables;
use Drupal\Core\Template\Attribute;

/**
 * Base preprocess class used to build the necessary variables for templates.
 *
 * @ingroup plugins_preprocess
 */
class PreprocessBase extends PluginBase implements PreprocessInterface {

  /**
   * The theme hook invoked.
   *
   * @type string
   */
  protected $hook;

  /**
   * The theme hook info array from the theme registry.
   *
   * @type array
   */
  protected $info;

  /**
   * The Variables object.
   *
   * @type \Drupal\bootstrap\Utility\Variables
   */
  protected $variables;

  /**
   * {@inheritdoc}
   */
  public function preprocess(array &$variables, $hook, array $info) {
    $this->hook = $hook;
    $this->info = $info;
    $this->variables = Variables::create($variables);
    if ($this->variables->element) {
      $this->preprocessElement($this->variables->element, $this->variables);
    }
    $this->preprocessVariables($this->variables);
  }

  /**
   * Ensures all attributes have been converted to an Attribute object.
   */
  protected function preprocessAttributes() {
    foreach ($this->variables as $name => $value) {
      if (strpos($name, 'attributes') !== FALSE && is_array($value)) {
        $this->variables[$name] = new Attribute($value);
      }
    }
  }

  /**
   * Converts any set description variable into a traversable array.
   *
   * @see https://www.drupal.org/node/2324025
   */
  protected function preprocessDescription() {
    if ($this->variables->offsetGet('description')) {
      // Retrieve the description attributes.
      $description_attributes = $this->variables->offsetGet('description_attributes', []);

      // Remove standalone description attributes.
      $this->variables->offsetUnset('description_attributes');

      // Build the description attributes.
      if ($id = $this->variables->getAttribute('id')) {
        $this->variables->setAttribute('aria-describedby', "$id--description");
        $description_attributes['id'] = "$id--description";
      }

      // Replace the description variable.
      $this->variables->offsetSet('description', [
        'attributes' => new Attribute($description_attributes),
        'content' => $this->variables['description'],
        'position' => $this->variables->offsetGet('description_display', 'after'),
      ]);
    }
  }

  /**
   * Preprocess the variables array if an element is present.
   *
   * @param \Drupal\bootstrap\Utility\Element $element
   *   The Element object.
   * @param \Drupal\bootstrap\Utility\Variables $variables
   *   The Variables object.
   */
  protected function preprocessElement(Element $element, Variables $variables) {}

  /**
   * Preprocess the variables array.
   *
   * @param \Drupal\bootstrap\Utility\Variables $variables
   *   The Variables object.
   */
  protected function preprocessVariables(Variables $variables) {}

}
