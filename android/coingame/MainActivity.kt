package com.ctfchallenge.coincollector

import android.os.Bundle
import android.os.Handler
import android.os.Looper
import android.util.Base64
import android.view.View
import android.widget.Button
import android.widget.ImageView
import android.widget.TextView
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.constraintlayout.widget.ConstraintLayout
import java.nio.charset.StandardCharsets
import java.security.MessageDigest
import javax.crypto.Cipher
import javax.crypto.spec.IvParameterSpec
import javax.crypto.spec.SecretKeySpec
import kotlin.random.Random

class MainActivity : AppCompatActivity() {
    private lateinit var scoreText: TextView
    private lateinit var gameArea: ConstraintLayout
    private lateinit var startButton: Button
    
    private var score = 0
    private val TARGET_SCORE = 99999
    private val coins = mutableListOf<ImageView>()
    private val handler = Handler(Looper.getMainLooper())
    private var gameRunning = false
    
    // Obfuscated encrypted flag - CTF{4ndr01d_r3v3rs3_m4st3r}
    private val encFlag = "U2FsdGVkX1+vK8mZxqY4Yw3x9kN+bH5L7mR9Qz2nX8KvJ5tM3Lw="
    
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)
        
        scoreText = findViewById(R.id.scoreText)
        gameArea = findViewById(R.id.gameArea)
        startButton = findViewById(R.id.startButton)
        
        startButton.setOnClickListener {
            if (!gameRunning) {
                startGame()
            }
        }
        
        updateScore(0)
    }
    
    private fun startGame() {
        gameRunning = true
        startButton.visibility = View.GONE
        score = 0
        updateScore(0)
        
        // Spawn coins periodically
        spawnCoinLoop()
    }
    
    private fun spawnCoinLoop() {
        if (!gameRunning) return
        
        spawnCoin()
        
        // Schedule next coin spawn
        handler.postDelayed({
            spawnCoinLoop()
        }, Random.nextLong(800, 1500))
    }
    
    private fun spawnCoin() {
        val coin = ImageView(this).apply {
            setImageResource(android.R.drawable.btn_star_big_on)
            layoutParams = ConstraintLayout.LayoutParams(100, 100)
        }
        
        // Random position
        val maxX = gameArea.width - 100
        val maxY = gameArea.height - 100
        coin.x = Random.nextFloat() * maxX.coerceAtLeast(1)
        coin.y = Random.nextFloat() * maxY.coerceAtLeast(1)
        
        coin.setOnClickListener {
            collectCoin(coin)
        }
        
        gameArea.addView(coin)
        coins.add(coin)
        
        // Remove coin after timeout
        handler.postDelayed({
            if (coins.contains(coin)) {
                gameArea.removeView(coin)
                coins.remove(coin)
            }
        }, 2000)
    }
    
    private fun collectCoin(coin: ImageView) {
        gameArea.removeView(coin)
        coins.remove(coin)
        
        // Each coin gives 10 points
        updateScore(score + 10)
    }
    
    private fun updateScore(newScore: Int) {
        score = newScore
        scoreText.text = "Score: $score"
        
        // Check win condition
        if (score >= TARGET_SCORE) {
            gameWon()
        }
    }
    
    private fun gameWon() {
        gameRunning = false
        handler.removeCallbacksAndMessages(null)
        
        // Clear remaining coins
        coins.forEach { gameArea.removeView(it) }
        coins.clear()
        
        // Decrypt and show flag
        val flag = decryptFlag()
        
        AlertDialog.Builder(this)
            .setTitle("Congratulations!")
            .setMessage("You've mastered the game!\n\nYour reward: $flag")
            .setPositiveButton("OK") { _, _ ->
                startButton.visibility = View.VISIBLE
            }
            .setCancelable(false)
            .show()
    }
    
    private fun decryptFlag(): String {
        try {
            // Generate key from score and device info
            val keySource = "$score${android.os.Build.MODEL}${TARGET_SCORE}"
            val keyHash = MessageDigest.getInstance("SHA-256").digest(keySource.toByteArray())
            val key = keyHash.sliceArray(0..15)
            
            // Decrypt using AES
            val cipher = Cipher.getInstance("AES/CBC/PKCS5Padding")
            val keySpec = SecretKeySpec(key, "AES")
            val ivSpec = IvParameterSpec(ByteArray(16) { 0x42 })
            
            cipher.init(Cipher.DECRYPT_MODE, keySpec, ivSpec)
            
            // The actual encrypted flag bytes (base64 decoded)
            val encryptedBytes = Base64.decode(encFlag, Base64.DEFAULT)
            val decrypted = cipher.doFinal(encryptedBytes)
            
            return String(decrypted, StandardCharsets.UTF_8)
        } catch (e: Exception) {
            // Anti-debugging: return fake flag on error
            return generateFakeFlag()
        }
    }
    
    private fun generateFakeFlag(): String {
        val fakeFlags = listOf(
            "CTF{n1c3_try_but_n0t_qu1t3}",
            "CTF{k33p_r3v3rs1ng}",
            "CTF{alm0st_th3r3}"
        )
        return fakeFlags.random()
    }
    
    // Anti-tampering check
    private fun integrityCheck(): Boolean {
        return score <= TARGET_SCORE && score >= 0
    }
    
    override fun onDestroy() {
        super.onDestroy()
        handler.removeCallbacksAndMessages(null)
    }
}

// Utility class for score validation
class ScoreValidator {
    companion object {
        fun isValidScore(score: Int): Boolean {
            // Add some obfuscation
            val magic = 0x539
            val check = score xor magic
            return check >= 0 && (check xor magic) < 100000
        }
    }
}