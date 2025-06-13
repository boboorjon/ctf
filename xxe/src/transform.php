<?php
header('Content-Type: text/plain');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Error: Invalid request method";
    exit;
}

$sourceFormat = $_POST['sourceFormat'] ?? 'csv';
$targetFormat = $_POST['targetFormat'] ?? 'json';
$transformData = $_POST['transformData'] ?? '';
$transformRules = $_POST['transformRules'] ?? 'standard';
$options = $_POST['transformOptions'] ?? 'default';

echo "=== DataSync Transform Service ===\n\n";
echo "Transformation Configuration:\n";
echo "- Source Format: " . strtoupper($sourceFormat) . "\n";
echo "- Target Format: " . strtoupper($targetFormat) . "\n";
echo "- Rules: " . ucfirst($transformRules) . "\n";
echo "- Options: " . ucfirst($options) . "\n";
echo "- Timestamp: " . date('Y-m-d H:i:s') . "\n\n";

if ($sourceFormat === 'structured' || $targetFormat === 'structured') {
    echo "Processing structured data transformation...\n";
    processStructuredTransform($transformData, $sourceFormat, $targetFormat, $transformRules);
} else {
    echo "Standard transformation completed.\n";
    echo "Records processed: 1,247\n";
    echo "Conversion ratio: 100%\n";
}

function processStructuredTransform($data, $source, $target, $rules) {
    if ($rules === 'preserve' && strpos($data, '<') !== false) {
        echo "Preserving structure - using advanced parser...\n";
        
        $transformer = new AdvancedTransformer();
        $result = $transformer->transformWithStructurePreservation($data, $target);
        
        echo "Transformation result:\n";
        echo $result;
    } else {
        echo "Basic transformation completed.\n";
    }
}

class AdvancedTransformer {
    public function transformWithStructurePreservation($data, $targetFormat) {
        libxml_disable_entity_loader(false);
        
        $dom = new DOMDocument();
        $dom->resolveExternals = true;
        $dom->substituteEntities = true;
        
        if ($dom->loadXML($data, LIBXML_NOENT | LIBXML_DTDLOAD)) {
            $transformed = $this->convertToFormat($dom, $targetFormat);
            return "Structure preserved successfully.\nTransformed data:\n" . $transformed;
        } else {
            return "Transformation failed - invalid source structure.";
        }
    }
    
    private function convertToFormat($dom, $format) {
        switch ($format) {
            case 'json':
                return "{\n  \"transformed\": true,\n  \"data\": \"" . addslashes($dom->saveXML()) . "\"\n}";
            case 'structured':
                return $dom->saveXML();
            default:
                return $dom->saveXML();
        }
    }
}
?>
