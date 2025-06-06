package com.ctfchallenge.coincollector

import android.util.Base64
import java.nio.charset.StandardCharsets
import java.security.MessageDigest
import javax.crypto.Cipher
import javax.crypto.spec.IvParameterSpec
import javax.crypto.spec.SecretKeySpec

/**
 * Utility class to encrypt flags for the CTF challenge
 * Run this separately to generate encrypted flags
 */
class FlagEncryptor {
    companion object {
        private const val TARGET_SCORE = 99999
        
        fun encryptFlag(plainFlag: String, deviceModel: String = "Generic"): String {
            // Generate key using same logic as decryption
            val keySource = "$TARGET_SCORE$deviceModel$TARGET_SCORE"
            val keyHash = MessageDigest.getInstance("SHA-256").digest(keySource.toByteArray())
            val key = keyHash.sliceArray(0..15)
            
            // Encrypt using AES
            val cipher = Cipher.getInstance("AES/CBC/PKCS5Padding")
            val keySpec = SecretKeySpec(key, "AES")
            val ivSpec = IvParameterSpec(ByteArray(16) { 0x42 })
            
            cipher.init(Cipher.ENCRYPT_MODE, keySpec, ivSpec)
            
            val encrypted = cipher.doFinal(plainFlag.toByteArray(StandardCharsets.UTF_8))
            return Base64.encodeToString(encrypted, Base64.DEFAULT)
        }
        
        fun generateObfuscatedConstants(flag: String): String {
            // Generate various obfuscated representations
            val bytes = flag.toByteArray()
            val xorKey = 0x42
            
            val output = StringBuilder()
            output.appendLine("// Obfuscated flag constants")
            output.appendLine("private val flagLen = ${bytes.size}")
            
            // XOR encoded
            output.append("private val xorFlag = byteArrayOf(")
            bytes.forEachIndexed { i, b ->
                if (i > 0) output.append(", ")
                output.append("0x${(b.toInt() xor xorKey).toString(16)}")
            }
            output.appendLine(")")
            
            // Split into parts
            val part1 = flag.substring(0, flag.length / 2)
            val part2 = flag.substring(flag.length / 2)
            output.appendLine("private val p1 = \"${Base64.encodeToString(part1.toByteArray(), Base64.NO_WRAP)}\"")
            output.appendLine("private val p2 = \"${Base64.encodeToString(part2.toByteArray(), Base64.NO_WRAP)}\"")
            
            return output.toString()
        }
    }
}

// Example usage:
fun main() {
    val flag = "CTF{4ndr01d_r3v3rs3_m4st3r}"
    val encrypted = FlagEncryptor.encryptFlag(flag)
    println("Encrypted flag: $encrypted")
    
    println("\nObfuscation constants:")
    println(FlagEncryptor.generateObfuscatedConstants(flag))
}