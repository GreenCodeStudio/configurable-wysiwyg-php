<?php

namespace Mkrawczyk\ConfigurableWysiwyg\Converters;

use Mkrawczyk\ConfigurableWysiwyg\Document;

class ToPlaintextConverter
{
    public function convert(Document $document): string
    {
        $source = $document->getCleanDom();

        return $source->saveXml($source);//tmp
    }
}
