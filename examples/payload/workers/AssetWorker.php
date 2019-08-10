<?php

namespace examples\payload\workers;

use Craft;
use craft\base\Element;
use craft\base\Volume;
use craft\elements\Asset;
use DateTime;
use Exception;

/**
 * Class AssetWorker
 */
class AssetWorker extends ElementWorker
{
  /**
   * AssetWorker constructor.
   *
   * @param array $config
   */
  public function __construct($config = []) {
    parent::__construct($config + [
      'elementType' => Asset::class,
    ]);
  }


  // Protected methods
  // -----------------

  /**
   * @inheritDoc
   */
  protected function exportElement(Element $element) {
    if (!($element instanceof Asset)) {
      throw new Exception('Invalid element type');
    }

    $volume = $element->volume;
    if (!($volume instanceof Volume)) {
      throw new Exception('Invalid asset volume');
    }

    return parent::exportElement($element) + [
      'volumeUid' => $volume->uid,
      'filename'  => $element->filename,
      'kind'      => $element->kind,
      'width'     => $element->width,
      'height'    => $element->height,
      'size'      => $element->size,
    ];
  }

  /**
   * @inheritDoc
   */
  protected function importElement(Element $element, array $data) {
    $volumes = Craft::$app->getVolumes();
    $volume  = $volumes->getVolumeByUid($data['volumeUid']);

    if (!($element instanceof Asset)) {
      throw new Exception('Invalid element type');
    }

    if (!($volume instanceof Volume)) {
      throw new Exception('Invalid asset volume');
    }

    $element->volumeId     = $volume->id;
    $element->folderId     = $volumes->ensureTopFolder($volume);
    $element->dateModified = new DateTime();
    $element->filename     = $data['filename'];
    $element->kind         = $data['kind'];
    $element->width        = $data['width'];
    $element->height       = $data['height'];
    $element->size         = $data['size'];
  }
}
