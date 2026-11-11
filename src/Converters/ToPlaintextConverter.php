<?php

namespace Mkrawczyk\ConfigurableWysiwyg\Converters;

use Mkrawczyk\ConfigurableWysiwyg\Document;

class ToPlaintextConverter
{
    private static $converters = [];

    public function convert(Document $document): string
    {
        $source = $document->getCleanDom();
        $ret = [];
        foreach ($source->firstElementChild->childNodes as $child) {
            $ret[] = $this->convertNode($child);
        }
        return implode("\n", $ret);
    }

    public function convertNode($node)
    {
        if ($node instanceof \Dom\Element) {
            $ns = $node->namespaceURI;
            $tagName = $node->tagName;
            if (isset(self::$converters[$ns][$tagName])) {
                $converter = self::$converters[$ns][$tagName];
                return $converter($node, $this);
            } else {
                return '';
            }
        } elseif ($node instanceof \Dom\Text) {
            return $node->textContent;
        } else {
            return '';
        }
    }

    public static function register(string $ns, string $tagName, callable $converter)
    {
        self::$converters[$ns][$tagName] = $converter;
    }
}

ToPlaintextConverter::register('https://raw.githubusercontent.com/GreenCodeStudio/configurable-wysiwyg/refs/heads/master/schema.xsd', 'paragraph', function ($element, $converter) {
    $ret = '';
    foreach ($element->childNodes as $child) {
        $ret .= $converter->convertNode($child);
    }
    return $ret."\n";
});
