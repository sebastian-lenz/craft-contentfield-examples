<?php

namespace examples\payload;

use craft\helpers\Json;
use examples\payload\workers\AssetWorker;
use examples\payload\workers\ContentEntryWorker;
use examples\payload\workers\WorkerInterface;
use Exception;

/**
 * Class Payload
 */
class Payload
{
  /**
   * @param string|null $fileName
   * @throws Exception
   */
  public function export(string $fileName = null) {
    if (is_null($fileName)) {
      $fileName = $this->getDefaultFileName();
    }

    $data = [];
    foreach ($this->getWorkers() as $key => $worker) {
      $workerData = $worker->export();
      if (!empty($workerData)) {
        $data[$key] = $workerData;
      }
    }

    file_put_contents(
      $fileName,
      Json::encode(
        $data,
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
      )
    );
  }

  /**
   * @return string
   */
  public function getDefaultFileName() {
    return implode(DIRECTORY_SEPARATOR, [__DIR__, 'payload.json']);
  }

  /**
   * @param string|null $fileName
   * @throws Exception
   */
  public function import(string $fileName = null) {
    if (is_null($fileName)) {
      $fileName = $this->getDefaultFileName();
    }

    if (!file_exists($fileName) || !is_readable($fileName)) {
      throw new Exception('Payload source file does not exist.');
    }

    $data = Json::decode(file_get_contents($fileName));
    foreach ($this->getWorkers() as $key => $worker) {
      if (array_key_exists($key, $data)) {
        $worker->import($data[$key]);
      }
    }
  }


  // Protected methods
  // -----------------

  /**
   * @return WorkerInterface[]
   * @throws Exception
   */
  protected function getWorkers() {
    return [
      'assets'  => new AssetWorker(),
      'entries' => new ContentEntryWorker(),
    ];
  }


  // Static methods
  // --------------

  /**
   * @return Payload
   */
  static public function create() {
    return new Payload();
  }
}
