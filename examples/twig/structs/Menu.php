<?php

namespace examples\twig\structs;

use Craft;
use craft\base\ElementInterface;
use craft\elements\Entry;
use lenz\craft\utils\elementCache\ElementCache;

/**
 * Class Menu
 */
class Menu
{
  /**
   * @var MenuItem[]
   */
  protected $_items = [];

  /**
   * @var Menu
   */
  static protected $_instance;


  /**
   * Menu constructor.
   */
  public function __construct() {
    $siteId = Craft::$app
      ->getSites()
      ->getCurrentSite()
      ->id;

    $elements  = Entry::find()
      ->section('examples')
      ->siteId($siteId)
      ->all();

    $items = [];
    $stack = [];
    foreach ($elements as $element) {
      $item = new MenuItem($element);

      if ($element instanceof Entry) {
        $level = max(1, intval($element->level));
        while (count($stack) >= $level) {
          array_pop($stack);
        }

        $item->parentId = end($stack);
        $stack[] = $item->id;
      }

      $items[$item->id] = $item;
    }

    $this->_items = $items;
  }

  /**
   * @param ElementInterface|MenuItem|int $elementOrId
   * @param bool $includeSelf
   * @return MenuItem[]
   */
  public function getAncestors($elementOrId, $includeSelf = false) {
    $item   = $this->getById($elementOrId);
    $result = [];

    if (is_null($item)) {
      return $result;
    }

    if (!$includeSelf) {
      $item = $this->getById($item->parentId);
    }

    while ($item) {
      array_unshift($result, $item);
      $item = $this->getById($item->parentId);
    }

    return $result;
  }

  /**
   * @return MenuItem[]
   */
  public function getAll() {
    return $this->_items;
  }

  /**
   * @param ElementInterface|MenuItem|int $elementOrId
   * @return MenuItem|null
   */
  public function getById($elementOrId) {
    $id = static::normalizeId($elementOrId);
    return array_key_exists($id, $this->_items)
      ? $this->_items[$id]
      : null;
  }

  /**
   * @param ElementInterface|MenuItem|int $elementOrId
   * @return MenuItem[]
   */
  public function getChildren($elementOrId) {
    $id = static::normalizeId($elementOrId);

    return array_filter($this->_items, function(MenuItem $item) use ($id) {
      return $item->parentId === $id;
    });
  }

  /**
   * @return MenuItem[]
   */
  public function getRootItems() {
    return array_filter($this->_items, function(MenuItem $item) {
      return $item->parentId === false;
    });
  }

  /**
   * @param ElementInterface|MenuItem|int|null $elementOrId
   * @return MenuItem[]
   */
  public function getToc($elementOrId = null) {
    if (is_null($elementOrId)) {
      $id = Craft::$app->getUrlManager()->getMatchedElement()->id;
    } else {
      $id = static::normalizeId($elementOrId);
    }

    $item = $this->getById($id);
    while (!is_null($item) && $item->parentId !== false) {
      $item = $item->getParent();
    }

    return $item->getChildren();
  }

  /**
   * @param ElementInterface|MenuItem|int $elementOrId
   * @return $this
   */
  public function setActive($elementOrId) {
    $id   = static::normalizeId($elementOrId);
    $item = $this->getById($id);

    while (!is_null($item)) {
      $item->active = true;
      $item = $item->getParent();
    }

    return $this;
  }


  // Static methods
  // --------------

  /**
   * @return static
   */
  static function getInstance() {
    if (!isset(static::$_instance)) {
      static::$_instance = ElementCache::withLanguage(__METHOD__, function() {
        return new static();
      });

      static::$_instance->setActive(Craft::$app
        ->getUrlManager()
        ->getMatchedElement()
      );
    }

    return static::$_instance;
  }

  /**
   * @param ElementInterface|MenuItem|int $elementOrId
   * @return int
   */
  static function normalizeId($elementOrId) {
    if ($elementOrId instanceof ElementInterface) {
      return intval($elementOrId->getId());
    } elseif ($elementOrId instanceof MenuItem) {
      return $elementOrId->id;
    } else {
      return intval($elementOrId);
    }
  }
}
