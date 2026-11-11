<?php

namespace Mkrawczyk\ConfigurableWysiwyg;
class Config
{
    private $allowedElements = [];
    public function addAllowedElement($element)
    {
        $this->allowedElements[] = $element;
    }

    public function isElementAllowed(?string $namespaceURI, string $tagName)
    {
        foreach ($this->allowedElements as $element) {
            if ($element->ns === $namespaceURI && $element->tagName === $tagName) {
                return true;
            }
        }
        return false;
    }

}
