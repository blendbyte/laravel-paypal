<?php

namespace Srmklive\PayPal\Services;

use GuzzleHttp\Psr7\MimeType;

class VerifyDocuments
{
    /**
     * @var list<string>
     */
    protected static $dispute_evidence_types = [
        'application/pdf',
        'image/gif',
        'image/jpeg',
        'image/png',
    ];

    /**
     * @var int
     */
    protected static $dispute_evidence_file_size = 10;

    /**
     * @var int
     */
    protected static $dispute_evidences_size = 50;

    /**
     * Get Mime type from filename.
     *
     * @param  string  $file
     * @return string|null
     */
    public static function getMimeType($file): ?string
    {
        return MimeType::fromFilename($file);
    }

    /**
     * Check if the evidence file being submitted mime type is valid.
     *
     *
     * @param list<string> $files
     *
     * @return bool
     */
    public static function isValidEvidenceFile(array $files)
    {
        $validFile = true;
        $validSize = true;
        $total_size = 0;

        $basic = (1024 * 1024);
        $file_size = $basic * self::$dispute_evidence_file_size;
        $overall_size = $basic * self::$dispute_evidences_size;

        foreach ($files as $file) {
            $mime_type = self::getMimeType($file);

            if ($mime_type === null || ! in_array($mime_type, self::$dispute_evidence_types)) {
                $validFile = false;
                break;
            }

            $size = filesize($file);

            if ($size > $file_size) {
                $validSize = false;
                break;
            }

            $total_size += $size;
        }

        return (($validFile === false) || ($validSize === false)) || ($total_size > $overall_size) ? false : true;
    }
}
