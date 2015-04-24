<?php 
/** 
  * Copyright: dtbaker 2012
  * Licence: Please check CodeCanyon.net for licence details. 
  * More licence clarification available here:  http://codecanyon.net/wiki/support/legal-terms/licensing-terms/ 
  * Deploy: 6525 ae190c43422d2b2a7c6934e00e8b8686
  * Envato: 82b7b3b1-2e56-457f-aac2-ff4d410e1e54
  * Package Date: 2014-10-02 15:05:08 
  * IP Address: 203.125.187.204
  */

/**
 * Converts HTMLPurifier_ConfigSchema_Interchange to an XML format,
 * which can be further processed to generate documentation.
 */
class HTMLPurifier_ConfigSchema_Builder_Xml extends XMLWriter
{

    protected $interchange;
    private $namespace;

    protected function writeHTMLDiv($html) {
        $this->startElement('div');

        $purifier = HTMLPurifier::getInstance();
        $html = $purifier->purify($html);
        $this->writeAttribute('xmlns', 'http://www.w3.org/1999/xhtml');
        $this->writeRaw($html);

        $this->endElement(); // div
    }

    protected function export($var) {
        if ($var === array()) return 'array()';
        return var_export($var, true);
    }

    public function build($interchange) {
        // global access, only use as last resort
        $this->interchange = $interchange;

        $this->setIndent(true);
        $this->startDocument('1.0', 'UTF-8');
        $this->startElement('configdoc');
        $this->writeElement('title', $interchange->name);

        foreach ($interchange->directives as $directive) {
            $this->buildDirective($directive);
        }

        if ($this->namespace) $this->endElement(); // namespace

        $this->endElement(); // configdoc
        $this->flush();
    }

    public function buildDirective($directive) {

        // Kludge, although I suppose having a notion of a "root namespace"
        // certainly makes things look nicer when documentation is built.
        // Depends on things being sorted.
        if (!$this->namespace || $this->namespace !== $directive->id->getRootNamespace()) {
            if ($this->namespace) $this->endElement(); // namespace
            $this->namespace = $directive->id->getRootNamespace();
            $this->startElement('namespace');
            $this->writeAttribute('id', $this->namespace);
            $this->writeElement('name', $this->namespace);
        }

        $this->startElement('directive');
        $this->writeAttribute('id', $directive->id->toString());

        $this->writeElement('name', $directive->id->getDirective());

        $this->startElement('aliases');
            foreach ($directive->aliases as $alias) $this->writeElement('alias', $alias->toString());
        $this->endElement(); // aliases

        $this->startElement('constraints');
            if ($directive->version) $this->writeElement('version', $directive->version);
            $this->startElement('type');
                if ($directive->typeAllowsNull) $this->writeAttribute('allow-null', 'yes');
                $this->text($directive->type);
            $this->endElement(); // type
            if ($directive->allowed) {
                $this->startElement('allowed');
                    foreach ($directive->allowed as $value => $x) $this->writeElement('value', $value);
                $this->endElement(); // allowed
            }
            $this->writeElement('default', $this->export($directive->default));
            $this->writeAttribute('xml:space', 'preserve');
            if ($directive->external) {
                $this->startElement('external');
                    foreach ($directive->external as $project) $this->writeElement('project', $project);
                $this->endElement();
            }
        $this->endElement(); // constraints

        if ($directive->deprecatedVersion) {
            $this->startElement('deprecated');
                $this->writeElement('version', $directive->deprecatedVersion);
                $this->writeElement('use', $directive->deprecatedUse->toString());
            $this->endElement(); // deprecated
        }

        $this->startElement('description');
            $this->writeHTMLDiv($directive->description);
        $this->endElement(); // description

        $this->endElement(); // directive
    }

}

// vim: et sw=4 sts=4
