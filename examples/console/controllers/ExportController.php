<?php

namespace examples\console\controllers;

use craft\console\Controller;
use craft\elements\Asset;
use craft\elements\Entry;
use craft\helpers\Json;

/**
 * Class ExportController
 */
class ExportController extends Controller
{
  /**
   * Exports the entries of the example site.
   */
  public function actionExport() {
    $fileName = implode(DIRECTORY_SEPARATOR, [
      dirname(__DIR__, 3),
      'payload.json'
    ]);

    file_put_contents($fileName, Json::encode([
      'assets'  => $this->exportAssets(),
      'entries' => $this->exportEntries(),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
  }


  // Private methods
  // ---------------

  /**
   * @return array
   */
  private function exportAssets() {
    $assets = Asset::find()->all();
    return array_map(function(Asset $asset) {
      return $asset->getAttributes();
    }, $assets);
  }

  /**
   * @return array
   */
  private function exportEntries() {
    $entries = Entry::find()->all();
    return array_map(function(Entry $entry) {
      $parent = $entry->getParent();
      $result = [
        'parent'  => is_null($parent) ? null : $parent->uid,
        'section' => $entry->getSection()->handle,
        'site'    => $entry->getSite()->handle,
        'title'   => $entry->title,
        'type'    => $entry->getType()->handle,
        'uid'     => $entry->uid,
      ];

      $content = $entry->pageContent;
      if (!is_null($content)) {
        $result['pageContent'] = $content->getModel()->getSerializedValue();
      }

      return $result;
    }, $entries);
  }
}
