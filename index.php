<?php declare(strict_types=1);

/**
 * @param SimpleXMLElement $xml
 * @return array
 */
function xmlToArray(SimpleXMLElement $xml): array
{
    $parser = function (SimpleXMLElement $xml, array $collection = []) use (&$parser) {
        $nodes = $xml->children();
        $attributes = $xml->attributes();

        if (0 !== count($attributes)) {
            foreach ($attributes as $attrName => $attrValue) {
                $collection['attributes'][$attrName] = strval($attrValue);
            }
        }

        if (0 === $nodes->count()) {
            $collection['value'] = strval($xml);
            return $collection;
        }

        foreach ($nodes as $nodeName => $nodeValue) {
            if (count($nodeValue->xpath('../' . $nodeName)) < 2) {
                $collection[$nodeName] = $parser($nodeValue);
                continue;
            }

            $collection[$nodeName][] = $parser($nodeValue);
        }

        return $collection;
    };

    return [
        $xml->getName() => $parser($xml)
    ];
}


// DEMO

$xml = <<<XML
<?xml version="1.0"?>
<logs>
    <file id="log_file_1">
        <name>file_name_1</name>
        <message>message body</message>
        <level>error</level>
        <context>
            <user>username_1</user>
        </context>
    </file>
    <file id="log_file_2">
        <name>file_name_2</name>
        <message>message body</message>
        <level>info</level>
        <context>
            <user>username_2</user>
        </context>
    </file>
</logs>
XML;

$res = xmlToArray(new SimpleXMLElement($xml));

print_r($res);
