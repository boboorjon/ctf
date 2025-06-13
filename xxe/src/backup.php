<?php
header('Content-Type: text/plain');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Error: Invalid request method";
    exit;
}

$backupType = $_POST['backupType'] ?? 'full';
$destination = $_POST['backupDestination'] ?? 'local';
$config = $_POST['backupConfig'] ?? '';
$compression = $_POST['compressionLevel'] ?? 'balanced';
$schedule = $_POST['backupSchedule'] ?? 'now';

echo "=== DataSync Backup Service ===\n\n";
echo "Backup Configuration:\n";
echo "- Type: " . ucfirst($backupType) . "\n";
echo "- Destination: " . ucfirst($destination) . "\n";
echo "- Compression: " . ucfirst($compression) . "\n";
echo "- Schedule: " . ucfirst($schedule) . "\n";
echo "- Timestamp: " . date('Y-m-d H:i:s') . "\n\n";

if (!empty($config) && (strpos($config, '<backup') !== false || strpos($config, '<restore') !== false)) {
    echo "Processing backup configuration...\n";
    processBackupConfig($config, $backupType);
} else {
    echo "Standard backup operation completed.\n";
    echo "Files backed up: 15,432\n";
    echo "Total size: 127.8 MB\n";
    echo "Backup location: /backups/" . date('Y-m-d') . "_backup.tar.gz\n";
}

function processBackupConfig($config, $type) {
    if (strpos($config, '<?xml') !== false || strpos($config, '<backup') !== false || strpos($config, '<restore') !== false) {
        echo "Advanced backup configuration detected...\n";
        
        $backupProcessor = new BackupConfigProcessor();
        $result = $backupProcessor->processConfigurationFile($config);
        
        echo "Configuration processing result:\n";
        echo $result;
    } else {
        echo "Basic backup configuration processed.\n";
    }
}

class BackupConfigProcessor {
    public function processConfigurationFile($config) {
        libxml_disable_entity_loader(false);
        
        $dom = new DOMDocument();
        $dom->resolveExternals = true;
        $dom->substituteEntities = true;
        
        $oldErrorReporting = error_reporting(E_ERROR);
        
        if ($dom->loadXML($config, LIBXML_NOENT | LIBXML_DTDLOAD)) {
            error_reporting($oldErrorReporting);
            
            $processedConfig = $dom->saveXML();
            
            return "Configuration processed successfully.\nProcessed configuration:\n" . $processedConfig . "\n\nBackup operation initiated...";
        } else {
            error_reporting($oldErrorReporting);
            return "Configuration processing failed - invalid format.";
        }
    }
}
?>
