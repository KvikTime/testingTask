<?php
class SiteMapGenerator
{
  private array $pages;
  private string $fileType;
  private string $filePath;

  public function __construct(array $pages, string $fileType, string $filePath)
  {
    try {
      $this->pages = $pages;
      $this->fileType = trim($fileType);
      if (!is_dir(trim($filePath))) {
        mkdir(trim($filePath), 0777, true);
      }
      $this->filePath = is_writable(trim($filePath)) ? trim($filePath) . "/sitemap." . trim($fileType) : throw new AccessClosed("Нет доступа к указанному пути: " . trim($filePath) . PHP_EOL);
    } catch (AccessClosed $e) {
      echo $e->getMessage();
    }
  }

  private function generateSitemap(): void
  {
    try {
      switch ($this->fileType) {
        case 'xml':
          $this->generateXml();
          break;
        case 'csv':
          $this->generateCsv();
          break;
        case 'json':
          $this->generateJson();
          break;
        default:
          throw new UnsupportedTypeFile('Неподдеживаемый формат файла: ' . $this->fileType . PHP_EOL);
      }
    } catch (UnsupportedTypeFile $e) {
      echo $e->getMessage();
    }
  }

  public function validateField(array $pagesArray): void
  {
    try {
      foreach ($pagesArray as $pageFieldsArray) {
        if (
          isset($pageFieldsArray['changefreq']) &&
          isset($pageFieldsArray['lastmod']) &&
          isset($pageFieldsArray['loc']) &&
          isset($pageFieldsArray['priority'])
        ) {
          continue;
        } else {
          throw new NotFoundField('Ошибка валидации полей' . PHP_EOL);
        }
      }
      $this->generateSitemap();
    } catch (NotFoundField $e) {
      echo $e->getMessage();
    }
  }

  private function generateXml(): void
  {
    $xml = new DOMDocument('1.0', 'UTF-8');
    $urlset = $xml->createElement('urlset');
    $urlset->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
    $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
    $urlset->setAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
    $xml->appendChild($urlset);

    foreach ($this->pages as $page) {
      $url = $xml->createElement('url');

      $loc = $xml->createElement('loc', $page['loc']);
      $url->appendChild($loc);

      $lastmod = $xml->createElement('lastmod', $page['lastmod']);
      $url->appendChild($lastmod);

      $priority = $xml->createElement('priority', strval($page['priority']));
      $url->appendChild($priority);

      $changefreq = $xml->createElement('changefreq', $page['changefreq']);
      $url->appendChild($changefreq);

      $urlset->appendChild($url);
    }

    $xml->save($this->filePath);
    echo 'Карта сайта в формате XML успешно создана' . PHP_EOL;
  }

  private function generateCsv(): void
  {
    $file = fopen($this->filePath, 'w');

    fputcsv($file, ['loc', 'lastmod', 'priority', 'changefreq'], ';');

    foreach ($this->pages as $page) {
      fputcsv($file, [
        $page['loc'],
        $page['lastmod'],
        $page['priority'] ?? '',
        $page['changefreq'] ?? ''
      ], ';');
    }
    fclose($file);
    echo 'Карта сайта в формате CSV успешно создана' . PHP_EOL;
  }

  private function generateJson(): void
  {
    $json = json_encode($this->pages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    file_put_contents($this->filePath, $json);
    echo 'Карта сайта в формате JSON успешно создана' . PHP_EOL;
  }
}

class UnsupportedTypeFile extends Exception{};
class NotFoundField extends Exception{};
class AccessClosed extends Exception{};
