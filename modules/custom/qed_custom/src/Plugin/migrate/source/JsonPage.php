<?php
namespace Drupal\qed_custom\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;
use Drupal\migrate\Row;

/**
* Source plugin to import data from JSON files
* @MigrateSource(
*   id = "json_page"
* )
*/
class JsonPage extends SourcePluginBase {

  public function prepareRow(Row $row) {
    $id = $row->getSourceProperty('_id');
    $city = $row->getSourceProperty('city');
    $location = $row->getSourceProperty('loc');
    $pop = $row->getSourceProperty('pop');
    $state = $row->getSourceProperty('state');

    if (strlen($city) > 255) {
      $row->setSourceProperty('city', substr($city,0,255));
    }
    $row->setSourceProperty('_id', $id);
    $row->setSourceProperty('loc', $location);
    $row->setSourceProperty('pop', $pop);
    $row->setSourceProperty('state', $state);

    return parent::prepareRow($row);
  }

  public function getIds() {
    $ids = [
      'json_filename' => [
        'type' => 'string'
      ]
    ];
    return $ids;
  }

  public function fields() {
    return array(
      'url' => $this->t('URL'),
      '_id' => $this->t('ID'),
      'city' => $this->t('City'),
      'loc' => $this->t('Location'),
      'pop' => $this->t('POP'),
      'state' => $this->t('State'),
      'json_filename' => $this-t("Source JSON filename")
    );
  }

  public function __toString() {
    return "json data";
  }

  /**
   * Initializes the iterator with the source data.
   * @return \Iterator
   *   An iterator containing the data for this source.
   */
  protected function initializeIterator() {

    // Loop through the source files and find anything with a .json extension
    $path = dirname(DRUPAL_ROOT) . "/source-data/cities.json";
    $filenames = glob($path);
    $rows = [];
    foreach ($filenames as $filename) {
      // Using second argument of TRUE here because migrate needs the data to be
      // associative arrays and not stdClass objects.
      $row = json_decode(file_get_contents($filename), TRUE);
      $row['json_filename'] = $filename;
      $row['date'] = time();

      // Append it to the array of rows we can import
      $rows[] = $row;
    }

    // Migrate needs an Iterator class, not just an array
    return new \ArrayIterator($rows);
  }
}
