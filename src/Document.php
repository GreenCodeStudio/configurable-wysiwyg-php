<?php

namespace Mkrawczyk\ConfigurableWysiwyg;
class Document
{
    public function __construct(public \Dom\XMLDocument $domDocument, public Config $config = new Config())
    {

    }

    public static function loadXmlString($xmlString)
    {
        $dom = \Dom\XMLDocument::createFromString($xmlString);
        return new self($dom);
    }

    public function getCleanDom()
    {
        $cleanDom = \Dom\XMLDocument::createEmpty();
        $cleanDocument= $cleanDom->createElementNS('https://raw.githubusercontent.com/GreenCodeStudio/configurable-wysiwyg/refs/heads/master/schema.xsd', 'document');
        foreach ($this->domDocument->firstElementChild->childNodes as $child) {
            $cleanChildren = $this->cleanNode($cleanDom, $child);
            foreach ($cleanChildren as $cleanChild) {
                $cleanDocument->appendChild($cleanChild);
            }
        }
        $cleanDom->appendChild($cleanDocument);
        return $cleanDom;
    }

    private function cleanNode(\Dom\XMLDocument $targetDocument, \Dom\Node $sourceNode): array
    {
        if ($sourceNode instanceof \Dom\Element) {
            if ($this->config->isElementAllowed($sourceNode->namespaceURI, $sourceNode->tagName)) {
                $element = $targetDocument->createElementNS($sourceNode->namespaceURI, $sourceNode->tagName);
                foreach ($sourceNode->attributes as $attr) {
                    $element->setAttribute($attr->name, $attr->value);
                }
                foreach ($sourceNode->childNodes as $child) {
                    $cleanChildren = $this->cleanNode($targetDocument, $child);
                    foreach ($cleanChildren as $cleanChild) {
                        $element->appendChild($cleanChild);
                    }
                }
                return [$element];
            } else {
                $ret = [];
                foreach ($sourceNode->childNodes as $child) {
                    $cleanChildren = $this->cleanNode($targetDocument, $child);
                    foreach ($cleanChildren as $cleanChild) {
                        $ret[] = $cleanChild;
                    }
                }
                return $ret;
            }
        } elseif ($sourceNode instanceof \Dom\Text) {
            return [$targetDocument->createTextNode($sourceNode->textContent)];
        } else {
            return [];
        }
    }
}
