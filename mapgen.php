<?php

class SiteMapGenerator
{
  public array $pages;
  public string $fileType;
  public string $filePath;

  public function __construct(array $pages, string $fileType, string $filePath)
  {
    $this->pages = $pages;
    $this->fileType = $fileType;
    if (!is_dir($filePath)) {
      mkdir($filePath, 0777, true);
    }
    $this->filePath = is_writable($filePath) ? $filePath . "/sitemap." . $fileType : throw new AccessClosed("Нет доступа к указанному пути: " . $filePath);
  }

  public function generateSitemap(): void
  {
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
        throw new UnsupportedTypeFile("Неподдеживаемый формат файла: " . $this->fileType);
    }
  }

  public function validateField(array $pagesArray): void
  {
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

      $changefreq = $xml->createElement('changefreq', $page['changefreq']);
      $url->appendChild($changefreq);

      $priority = $xml->createElement('priority', strval($page['priority']));
      $url->appendChild($priority);

      $urlset->appendChild($url);
    }

    $xml->save($this->filePath);
  }

  private function generateCsv(): void
  {
    $file = fopen($this->filePath, 'w');

    fputcsv($file, ['loc', 'lastmod', 'changefreq', 'priority'], ';');

    foreach ($this->pages as $page) {
      fputcsv($file, [
        $page['loc'],
        $page['lastmod'],
        $page['changefreq'] ?? '',
        $page['priority'] ?? ''
      ],';');
    }
    fclose($file);
  }

  private function generateJson(): void
  {
    $json = json_encode($this->pages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    file_put_contents($this->filePath, $json);
  }
}

class UnsupportedTypeFile extends Exception {};
class NotFoundField extends Exception {};
class AccessClosed extends Exception {};

try {
  $generator = new SiteMapGenerator($pagesArray, $format, $srcDirectory);
  $generator->validateField($pagesArray);
  echo 'Карта успешно создана';
} catch (UnsupportedTypeFile $e) {
  echo $e->getMessage();
} catch (NotFoundField $e) {
  echo $e->getMessage();
} catch (AccessClosed $e) {
  echo $e->getMessage();
}
