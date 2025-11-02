<?php
/**
 * Custom XOR-based encryption as a fallback when sodium/openssl are not available.
 * This is NOT cryptographically secure and should only be used for demo or internal testing.
 */

/**
 * Encrypts data using XOR against SHA-256 hashed key.
 * @param string $data  Plaintext to encrypt.
 * @param string $key   User-provided password/key.
 * @param string $nonce Unique salt/nonce value.
 * @return string Encrypted binary data.
 */
function custom_encrypt($data, $key, $nonce) {
    // Hash the key and nonce for uniform length and complexity
    $hashedKey = substr(hash('sha256', $key, true), 0, 32);   // 256-bit key
    $hashedNonce = substr(hash('sha256', $nonce, true), 0, 16); // 128-bit nonce

    $keyStream = $hashedKey . $hashedNonce; // Combine for stream
    $result = '';
    $dataLen = strlen($data);
    $keyLen = strlen($keyStream);

    for ($i = 0; $i < $dataLen; $i++) {
        $result .= $data[$i] ^ $keyStream[$i % $keyLen];
    }

    return $result;
}

/**
 * Decrypts data using the same XOR logic.
 * @param string $data  Encrypted binary data.
 * @param string $key   Original password/key.
 * @param string $nonce Original nonce value.
 * @return string Decrypted plaintext.
 */
function custom_decrypt($data, $key, $nonce) {
    return custom_encrypt($data, $key, $nonce); // XOR is reversible
}

/**
 * SECURITY NOTE:
 * This XOR method is insecure for real applications.
 * Use secure libs like:
 * - `sodium_crypto_secretbox()` from libsodium
 * - `openssl_encrypt()` with AES-256-GCM
 * - `defuse/php-encryption` library
 */
?>
