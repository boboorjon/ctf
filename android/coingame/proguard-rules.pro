# Add project specific ProGuard rules here.

# Obfuscate everything except the main activity name
-keep public class com.ctfchallenge.coincollector.MainActivity {
    public <init>(...);
}

# Obfuscate method names and fields
-obfuscationdictionary proguard-dict.txt
-classobfuscationdictionary proguard-dict.txt
-packageobfuscationdictionary proguard-dict.txt

# Remove logging
-assumenosideeffects class android.util.Log {
    public static boolean isLoggable(java.lang.String, int);
    public static int v(...);
    public static int i(...);
    public static int w(...);
    public static int d(...);
    public static int e(...);
}

# Optimize aggressively
-optimizationpasses 5
-allowaccessmodification
-dontpreverify

# Keep encryption/decryption methods but obfuscate their names
-keepclassmembers class * {
    *** decrypt*(...);
    *** encrypt*(...);
}

# Add some fake methods to confuse reverse engineers
-keep,allowobfuscation class com.ctfchallenge.coincollector.** {
    void fakeDecrypt*(...);
    void checkLicense*(...);
    boolean verify*(...);
}