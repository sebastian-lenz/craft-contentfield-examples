<?php

namespace examples\payload\workers;

use Craft;
use craft\base\Element;
use Throwable;
use yii\base\BaseObject;

/**
 * Class ElementWorker
 */
class ElementWorker extends BaseObject implements WorkerInterface
{
  /**
   * @var array|null
   */
  public $criteria = null;

  /**
   * @var Element|string
   */
  public $elementType;


  /**
   * @return array
   * @throws Throwable
   */
  public function export(): array {
    return array_map(function(Element $element) {
      return $this->exportElement($element);
    }, $this->getElements());
  }

  /**
   * @param array $data
   * @throws Throwable
   */
  public function import(array $data) {
    $elementType  = $this->elementType;
    $elements     = Craft::$app->getElements();
    $existingUids = array_map(function(Element $element) {
      return $element->uid;
    }, $this->getElements());

    foreach ($data as $elementData) {
      if (
        !is_array($elementData) ||
        !array_key_exists('uid', $elementData) ||
        in_array($elementData['uid'], $existingUids)
      ) {
        continue;
      }

      $element = new $elementType();
      $element->uid = $elementData['uid'];
      $this->importElement($element, $elementData);

      $elements->saveElement($element);
    }
  }


  // Protected methods
  // -----------------

  /**
   * @param Element $element
   * @return array
   * @throws Throwable
   */
  protected function exportElement(Element $element) {
    return [
      'uid' => $element->uid,
    ];
  }

  /**
   * @return Element[]
   */
  protected function getElements() {
    return $this->elementType::findAll($this->criteria);
  }

  /**
   * @param Element $element
   * @param array $data
   * @throws Throwable
   */
  protected function importElement(Element $element, array $data) { }
}
