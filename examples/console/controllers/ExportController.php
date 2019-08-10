<?php

namespace examples\console\controllers;

use craft\console\Controller;
use examples\payload\Payload;
use Exception;

/**
 * Class ExportController
 */
class ExportController extends Controller
{
  /**
   * Exports the entries of the example site.
   *
   * @param string|null $fileName
   * @throws Exception
   */
  public function actionIndex(string $fileName = null) {
    Payload::create()->export($fileName);
  }
}
