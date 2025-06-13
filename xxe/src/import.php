<?php
header('Content-Type: text/plain');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Error: Invalid request method";
    exit;
}

if (!isset($_POST['dataContent']) || empty($_POST['dataContent'])) {
    echo "Error: No data content provided";
    exit;
}

$dataContent = $_POST['dataContent'];
$source = $_POST['source'] ?? 'unknown';
$format = $_POST['format'] ?? 'unknown';
$validation = $_POST['validation'] ?? 'moderate';
$encoding = $_POST['encoding'] ?? 'utf8';

// Log import attempt
$logEntry = date('Y-m-d H:i:s') . " - Import attempt from source: $source, format: $format\n";
file_put_contents('/tmp/import.log', $logEntry, FILE_APPEND);

try {
    echo "=== DataSync Import Service ===\n\n";
    echo "Import Configuration:\n";
    echo "- Source System: " . ucfirst($source) . "\n";
    echo "- Data Format: " . strtoupper($format) . "\n";
    echo "- Validation Level: " . ucfirst($validation) . "\n";
    echo "- Character Encoding: " . strtoupper($encoding) . "\n";
    echo "- Timestamp: " . date('Y-m-d H:i:s') . "\n\n";
    
    // Determine processing method based on format
    switch ($format) {
        case 'json':
            processJsonData($dataContent, $validation);
            break;
            
        case 'csv':
            processCsvData($dataContent, $validation);
            break;
            
        case 'structured':
            processStructuredData($dataContent, $validation, $encoding);
            break;
            
        case 'api':
            processApiResponse($dataContent, $validation);
            break;
            
        default:
            echo "Error: Unsupported format specified\n";
            exit;
    }
    
} catch (Exception $e) {
    echo "Import Error: " . $e->getMessage() . "\n";
    echo "Please check your data format and try again.\n";
}

function processJsonData($data, $validation) {
    echo "Processing JSON Data...\n";
    echo "========================\n\n";
    
    $decoded = json_decode($data, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON format: " . json_last_error_msg());
    }
    
    echo "JSON Structure Analysis:\n";
    echo "- Root keys: " . implode(', ', array_keys($decoded)) . "\n";
    echo "- Data size: " . strlen($data) . " bytes\n";
    
    if (isset($decoded['employees'])) {
        echo "- Employee records found: " . count($decoded['employees']) . "\n";
    }
    
    echo "\nValidation: " . ($validation === 'strict' ? 'PASSED' : 'COMPLETED') . "\n";
    echo "Import Status: SUCCESS\n";
}

function processCsvData($data, $validation) {
    echo "Processing CSV Data...\n";
    echo "======================\n\n";
    
    $lines = explode("\n", trim($data));
    $headers = str_getcsv($lines[0]);
    
    echo "CSV Structure Analysis:\n";
    echo "- Column count: " . count($headers) . "\n";
    echo "- Headers: " . implode(', ', $headers) . "\n";
    echo "- Data rows: " . (count($lines) - 1) . "\n";
    echo "- Total size: " . strlen($data) . " bytes\n";
    
    echo "\nValidation: " . ($validation === 'strict' ? 'PASSED' : 'COMPLETED') . "\n";
    echo "Import Status: SUCCESS\n";
}

function processStructuredData($data, $validation, $encoding) {
    echo "Processing Structured Data...\n";
    echo "=============================\n\n";
    
    // This is where the XXE vulnerability exists - but it's much more complex
    // The vulnerability is hidden in a deep parsing chain with multiple steps
    
    // Step 1: Basic validation
    if (strpos($data, '<') === false) {
        throw new Exception("Invalid structured data format");
    }
    
    // Step 2: Encoding normalization
    if ($encoding !== 'utf8') {
        $data = mb_convert_encoding($data, 'UTF-8', strtoupper($encoding));
    }
    
    // Step 3: Pre-processing (this creates the vulnerability)
    $preprocessedData = preprocessStructuredData($data, $validation);
    
    // Step 4: Main parsing
    if (is_array($preprocessedData)) {
        $result = $preprocessedData;
    } else {
        $result = parseStructuredContent($preprocessedData);
    }
    
    echo "Structured Data Analysis:\n";
    echo "- Root element: " . ($result['root'] ?? 'unknown') . "\n";
    echo "- Element count: " . ($result['count'] ?? 0) . "\n";
    echo "- Processing method: Advanced parser\n";
    echo "- Encoding: " . $encoding . "\n";
    
    if (isset($result['employees'])) {
        echo "- Employee records: " . $result['employees'] . "\n";
    }
    
    echo "\nData Content Preview:\n";
    echo "---------------------\n";
    echo $result['preview'] ?? 'No preview available';
    echo "\n\n";
    
    echo "Validation: " . ($validation === 'strict' ? 'PASSED' : 'COMPLETED') . "\n";
    echo "Import Status: SUCCESS\n";
}

function preprocessStructuredData($data, $validation) {
    // This function creates a complex vulnerability chain
    // The XXE happens here but it's buried in the preprocessing logic
    
    $tempFile = tempnam(sys_get_temp_dir(), 'import_');
    file_put_contents($tempFile, $data);
    
    // Complex validation logic that creates the vulnerability
    if ($validation === 'lenient') {
        // In lenient mode, we allow more flexible parsing
        // This is where the XXE vulnerability gets introduced
        return processWithAdvancedParser($tempFile);
    } else {
        // Standard processing
        return file_get_contents($tempFile);
    }
}

function processWithAdvancedParser($filePath) {
    // The actual XXE vulnerability is buried deep in this "advanced" parser
    // Multiple layers of indirection make it harder to spot
    
    $content = file_get_contents($filePath);
    
    // Create a complex parsing chain
    $processor = new AdvancedDataProcessor();
    return $processor->processWithExternalReferences($content);
}

function parseStructuredContent($data) {
    // Simple fallback parser for non-vulnerable paths
    if (is_array($data)) {
        return $data;
    }
    
    // Basic structure analysis for display purposes
    $result = [
        'root' => 'data',
        'count' => 1,
        'preview' => substr($data, 0, 500)
    ];
    
    // Try to extract some basic info
    if (strpos($data, '<employee') !== false) {
        $employeeCount = substr_count($data, '<employee');
        $result['employees'] = $employeeCount;
    }
    
    return $result;
}

class AdvancedDataProcessor {
    private $parserConfig;
    
    public function __construct() {
        $this->parserConfig = [
            'resolve_externals' => true,  // Hidden vulnerability setting
            'load_entities' => true,      // Another hidden vulnerability setting
            'validation_mode' => 'lenient'
        ];
    }
    
    public function processWithExternalReferences($content) {
        // This is where the actual XXE vulnerability exists
        // But it's hidden behind method names that don't mention XML
        
        return $this->parseWithEntityResolution($content);
    }
    
    private function parseWithEntityResolution($content) {
        // The actual vulnerable XML parsing code
        // Buried deep and disguised as "entity resolution"
        
        $oldValue = libxml_disable_entity_loader(false);
        
        try {
            $dom = new DOMDocument();
            $dom->resolveExternals = true;
            $dom->substituteEntities = true;
            
            // Suppress warnings to hide the XML parsing
            $oldErrorReporting = error_reporting(E_ERROR);
            
            if (!$dom->loadXML($content, LIBXML_NOENT | LIBXML_DTDLOAD)) {
                error_reporting($oldErrorReporting);
                libxml_disable_entity_loader($oldValue);
                throw new Exception("Failed to parse structured content");
            }
            
            error_reporting($oldErrorReporting);
            
            // Extract data but hide that it's XML processing
            $result = $this->extractStructureInfo($dom);
            
            libxml_disable_entity_loader($oldValue);
            
            return $result;
            
        } catch (Exception $e) {
            if (isset($oldValue)) {
                libxml_disable_entity_loader($oldValue);
            }
            throw $e;
        }
    }
    
    private function extractStructureInfo($dom) {
        $result = [];
        $result['root'] = $dom->documentElement->nodeName;
        $result['count'] = $dom->getElementsByTagName('*')->length;
        
        // Get the processed content (including resolved entities)
        $processedContent = $dom->saveXML();
        $result['preview'] = $this->createPreview($processedContent);
        
        // Count employees if present
        $employees = $dom->getElementsByTagName('employee');
        if ($employees->length > 0) {
            $result['employees'] = $employees->length;
        }
        
        return $result;
    }
    
    private function createPreview($content) {
        // Clean up for display
        $preview = preg_replace('/>\s+</', ">\n<", $content);
        
        // Limit preview length
        if (strlen($preview) > 1000) {
            $preview = substr($preview, 0, 1000) . "\n... (truncated)";
        }
        
        return $preview;
    }
}

function processApiResponse($data, $validation) {
    echo "Processing API Response...\n";
    echo "==========================\n\n";
    
    $decoded = json_decode($data, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid API response format: " . json_last_error_msg());
    }
    
    echo "API Response Analysis:\n";
    echo "- Status: " . ($decoded['status'] ?? 'unknown') . "\n";
    echo "- Response size: " . strlen($data) . " bytes\n";
    
    if (isset($decoded['data'])) {
        echo "- Data payload: Available\n";
    }
    
    if (isset($decoded['metadata'])) {
        echo "- Metadata: Available\n";
        if (isset($decoded['metadata']['totalRecords'])) {
            echo "- Total records: " . $decoded['metadata']['totalRecords'] . "\n";
        }
    }
    
    echo "\nValidation: " . ($validation === 'strict' ? 'PASSED' : 'COMPLETED') . "\n";
    echo "Import Status: SUCCESS\n";
}
?>
