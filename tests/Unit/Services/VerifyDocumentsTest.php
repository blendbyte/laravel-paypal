<?php

use Srmklive\PayPal\Services\VerifyDocuments;

// ---------------------------------------------------------------------------
// getMimeType — extension-to-MIME mapping (delegates to Guzzle MimeType)
// ---------------------------------------------------------------------------

it('returns image/jpeg for .jpg files', function () {
    expect(VerifyDocuments::getMimeType('photo.jpg'))->toBe('image/jpeg');
});

it('returns image/jpeg for .jpeg files', function () {
    expect(VerifyDocuments::getMimeType('photo.jpeg'))->toBe('image/jpeg');
});

it('returns application/pdf for .pdf files', function () {
    expect(VerifyDocuments::getMimeType('document.pdf'))->toBe('application/pdf');
});

it('returns image/gif for .gif files', function () {
    expect(VerifyDocuments::getMimeType('animation.gif'))->toBe('image/gif');
});

it('returns image/png for .png files', function () {
    expect(VerifyDocuments::getMimeType('screenshot.png'))->toBe('image/png');
});

it('returns null for a completely unknown extension', function () {
    // Use an extension that is definitively not in any MIME database.
    expect(VerifyDocuments::getMimeType('file.paypaltest999ext'))->toBeNull();
});

// ---------------------------------------------------------------------------
// isValidEvidenceFile — MIME type validation (no real files needed for rejects)
// ---------------------------------------------------------------------------

it('returns true for an empty file list', function () {
    expect(VerifyDocuments::isValidEvidenceFile([]))->toBeTrue();
});

it('returns false for a file with an unsupported extension', function () {
    expect(VerifyDocuments::isValidEvidenceFile(['malware.exe']))->toBeFalse();
});

it('returns false for a Word document', function () {
    expect(VerifyDocuments::isValidEvidenceFile(['evidence.doc']))->toBeFalse();
    expect(VerifyDocuments::isValidEvidenceFile(['evidence.docx']))->toBeFalse();
});

it('returns false for a bitmap image', function () {
    expect(VerifyDocuments::isValidEvidenceFile(['screenshot.bmp']))->toBeFalse();
});

it('returns false for a file with unknown extension', function () {
    expect(VerifyDocuments::isValidEvidenceFile(['file.paypaltest999ext']))->toBeFalse();
});

it('returns false when any file in the list has an invalid mime type', function () {
    // Create a real temp file so filesize() does not emit a warning for the valid entry.
    $valid = sys_get_temp_dir().'/paypal_test_'.uniqid().'.jpg';
    file_put_contents($valid, str_repeat('x', 512));

    $result = VerifyDocuments::isValidEvidenceFile([$valid, 'invalid.exe']);

    @unlink($valid);

    expect($result)->toBeFalse();
});

// ---------------------------------------------------------------------------
// isValidEvidenceFile — valid files (requires small real temp files)
// ---------------------------------------------------------------------------

it('returns true for small files with accepted mime types', function () {
    $paths = [];

    foreach (['jpg', 'pdf', 'png', 'gif'] as $ext) {
        $path = sys_get_temp_dir().'/paypal_test_'.uniqid().'.'.$ext;
        file_put_contents($path, str_repeat('x', 1024)); // 1 KB
        $paths[] = $path;
    }

    $result = VerifyDocuments::isValidEvidenceFile($paths);

    foreach ($paths as $p) {
        @unlink($p);
    }

    expect($result)->toBeTrue();
});

// ---------------------------------------------------------------------------
// isValidEvidenceFile — size limits
// Note: the class uses self:: binding, so limit overrides require reflection.
// ---------------------------------------------------------------------------

it('returns false when a single file exceeds the per-file size limit', function () {
    $path = sys_get_temp_dir().'/paypal_test_'.uniqid().'.jpg';
    file_put_contents($path, str_repeat('x', 512 * 1024)); // 512 KB

    // Temporarily lower the per-file cap to 0 MB (= 0 bytes) via reflection.
    $prop = new ReflectionProperty(VerifyDocuments::class, 'dispute_evidence_file_size');
    $original = $prop->getValue();
    $prop->setValue(null, 0);

    $result = VerifyDocuments::isValidEvidenceFile([$path]);

    $prop->setValue(null, $original);
    @unlink($path);

    expect($result)->toBeFalse();
});

it('returns false when the total size of all files exceeds the overall limit', function () {
    $paths = [];

    foreach (['a.jpg', 'b.jpg'] as $name) {
        $path = sys_get_temp_dir().'/paypal_test_'.uniqid().'.jpg';
        file_put_contents($path, str_repeat('x', 600 * 1024)); // 600 KB each
        $paths[] = $path;
    }

    // Lower the total cap to 1 MB via reflection so we don't need 50 MB of files.
    $prop = new ReflectionProperty(VerifyDocuments::class, 'dispute_evidences_size');
    $original = $prop->getValue();
    $prop->setValue(null, 1);

    $result = VerifyDocuments::isValidEvidenceFile($paths);

    $prop->setValue(null, $original);
    foreach ($paths as $p) {
        @unlink($p);
    }

    expect($result)->toBeFalse();
});
