<?php
namespace Mkrawczyk\ConfigurableWysiwyg\Converters;

use Mkrawczyk\ConfigurableWysiwyg\Document;

class ToHtmlConverter
{
    public function convert(Document $document): string
    {
        $source=$document->getCleanDom();
        $htmlDom = \Dom\HTMLDocument::createEmpty();

        return $htmlDom->saveHtml($htmlDom);
    }
}
