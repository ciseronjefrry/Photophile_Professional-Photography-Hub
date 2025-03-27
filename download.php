<?php
if (isset($_GET['folder'])) {
    $folderName = $_GET['folder'];
    $zipName = "uploads/$folderName.zip";
    $folderPath = "uploads/$folderName";

    $zip = new ZipArchive;
    if ($zip->open($zipName, ZipArchive::CREATE) === TRUE) {
        foreach (glob($folderPath . '/*') as $file) {
            $zip->addFile($file, basename($file));
        }
        $zip->close();
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . basename($zipName) . '"');
        readfile($zipName);
        unlink($zipName);
    } else {
        echo "Failed to create ZIP.";
    }
}
?>
