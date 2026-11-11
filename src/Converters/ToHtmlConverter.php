<?php

namespace Mkrawczyk\ConfigurableWysiwyg\Converters;

use Mkrawczyk\ConfigurableWysiwyg\Document;

class ToHtmlConverter
{
    private static $converters = [];

    public function convert(Document $document): string
    {
        $source = $document->getCleanDom();
        $htmlDom = \Dom\HTMLDocument::createEmpty();
        $htmlElement = $htmlDom->createElement('html');
        $headElement = $htmlDom->createElement('head');
        $bodyElement = $htmlDom->createElement('body');
        $htmlElement->appendChild($headElement);
        $htmlElement->appendChild($bodyElement);
        $htmlDom->appendChild($htmlElement);
        foreach ($source->firstElementChild->childNodes as $child) {
            $convertedNodes = $this->convertNode($child, $htmlDom);
            foreach ($convertedNodes as $convertedNode) {
                $bodyElement->appendChild($convertedNode);
            }
        }
        $ret = [];
        foreach ($bodyElement->childNodes as $child) {
            $ret[] = $htmlDom->saveHtml($child);
        }
        return implode("\n", $ret);
    }

    private function convertNode($node, $htmlDocument)
    {
        if ($node instanceof \Dom\Element) {
            $ns = $node->namespaceURI;
            $tagName = $node->tagName;
            if (isset(self::$converters[$ns][$tagName])) {
                $converter = self::$converters[$ns][$tagName];
                return $converter($node, $htmlDocument);
            } else {
                return [];
            }
        } elseif ($node instanceof \Dom\Text) {
            return [$htmlDocument->createTextNode($node->textContent)];
        } else {
            return [];
        }
    }

    public static function register(string $ns, string $tagName, callable $converter)
    {
        self::$converters[$ns][$tagName] = $converter;
    }
}

ToHtmlConverter::register('https://raw.githubusercontent.com/GreenCodeStudio/configurable-wysiwyg/refs/heads/master/schema.xsd', 'paragraph', function ($element, $htmlDocument) {
    $p = $htmlDocument->createElement('p');
    foreach ($element->childNodes as $child) {
        $importedNode = $htmlDocument->importNode($child, true);
        $p->appendChild($importedNode);
    }
    return [$p];
});
