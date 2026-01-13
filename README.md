# SQ-Tech Solver: Secure QR Code Generator System

## 📋 Table of Contents
- [System Overview](#system-overview)
- [Algorithms & Cryptographic Technologies](#algorithms--cryptographic-technologies)
- [Security Layers](#security-layers)
- [Key Features](#key-features)
- [Usage Procedure](#usage-procedure)
- [Why Use This System?](#why-use-this-system)
- [Technical Specifications](#technical-specifications)
- [Testing & Quality Assurance](#testing--quality-assurance)

---

## 🎯 System Overview

**SQ-Tech Solver Secure QR Code Generator** is a comprehensive, enterprise-grade platform designed to address critical security vulnerabilities in traditional QR code systems. This final year project was developed to combat the rising cases of QR code fraud and security breaches by providing a self-hosted, secure alternative that eliminates reliance on third-party services.

### What is This System?

This system is a **complete end-to-end secure QR code generation and management platform** that:

- **Generates encrypted QR codes** with military-grade cryptography
- **Scans for malware** before QR code creation using industry-leading threat detection
- **Enforces multi-layer access control** through passwords and one-time passcodes (OTP)
- **Provides granular permission management** with view-only and full-access modes
- **Enables secure sharing** via email or verified friend networks
- **Maintains comprehensive audit logs** of all access attempts and activities
- **Offers customizable QR design** with color customization options

Unlike traditional QR code generators that store data in plain text or rely on external services, this system ensures that **every piece of data is encrypted at rest and in transit**, with decryption keys derived from user-specific credentials that never leave the system.

---

## 🔐 Algorithms & Cryptographic Technologies

### 1. **ChaCha20-Poly1305 Encryption Algorithm**

**What it is:**
- ChaCha20 is a high-performance stream cipher designed by Daniel J. Bernstein
- Poly1305 is a cryptographic message authentication code (MAC)
- Together, they form an Authenticated Encryption with Associated Data (AEAD) cipher

**Why We Use It:**
- **Performance**: ChaCha20 is significantly faster than AES on software implementations, especially on devices without hardware acceleration
- **Security**: Provides 256-bit encryption strength with resistance to timing attacks
- **Authenticity**: Poly1305 ensures data integrity and authenticity, preventing tampering
- **Modern Standard**: Recommended by security experts and used in major protocols (TLS 1.3, WireGuard VPN)
- **No Patents**: Open-source and free from patent restrictions

**Implementation:**
- Each QR code content is encrypted with a unique 256-bit per-QR key
- Uses random nonces (96-bit) for each encryption operation to ensure uniqueness
- Provides authenticated encryption, meaning any tampering is immediately detected

### 2. **Argon2id Key Derivation Function (KDF)**

**What it is:**
- Argon2id is the winner of the Password Hashing Competition (2015)
- A memory-hard function designed to resist both GPU and ASIC attacks
- Combines features of Argon2i (side-channel resistant) and Argon2d (GPU resistant)

**Why We Use It:**
- **Memory-Hard**: Requires significant RAM, making brute-force attacks extremely expensive
- **Adaptive**: Can be tuned for different security/performance trade-offs
- **Future-Proof**: Resistant to advances in attack technology
- **Industry Standard**: Recommended by OWASP and NIST for password hashing

**Implementation:**
- Derives user-specific encryption keys from: `SHA-256(timestamp + email + user_phrase)`
- Uses moderate computational limits (`OPSLIMIT_MODERATE`, `MEMLIMIT_MODERATE`) for balanced security and performance
- Each user's key is unique and cannot be derived without their credentials

### 3. **SHA-256 Hash Function**

**What it is:**
- Secure Hash Algorithm 256-bit, part of the SHA-2 family
- Produces a 256-bit (32-byte) hash value

**Why We Use It:**
- **Cryptographically Secure**: One-way function, cannot be reversed
- **Collision Resistant**: Extremely difficult to find two inputs with the same hash
- **Industry Standard**: Used in Bitcoin, SSL/TLS certificates, and many security protocols
- **Fast & Reliable**: Efficient implementation available in all modern systems

**Implementation:**
- Used to create a deterministic input for Argon2id from user credentials
- Ensures consistent key derivation while maintaining security

### 4. **BCrypt Password Hashing**

**What it is:**
- Adaptive cryptographic hash function based on the Blowfish cipher
- Designed by Niels Provos and David Mazières

**Why We Use It:**
- **Adaptive Cost Factor**: Can be tuned to increase computational cost as hardware improves
- **Salt Integration**: Automatically generates and stores salts
- **Proven Security**: Battle-tested in production systems for decades
- **PHP Native**: Built into PHP's `password_hash()` function

**Implementation:**
- Used to hash user-provided passwords before storage
- Prevents rainbow table attacks and ensures passwords are never stored in plain text

### 5. **Random Number Generation**

**What it is:**
- Cryptographically secure random byte generation using `random_bytes()`

**Why We Use It:**
- **Cryptographically Secure**: Uses system's secure random number generator
- **Unpredictable**: Suitable for generating keys, salts, and nonces
- **Unique Values**: Ensures each QR code has distinct cryptographic parameters

**Implementation:**
- Generates unique salts (16 bytes) for each QR code
- Creates random nonces (12 bytes) for each encryption operation
- Generates per-QR encryption keys (32 bytes)

---

## 🛡️ Security Layers

The system implements **multiple layers of security** that work together to provide comprehensive protection:

### **Layer 1: Malware Detection & Prevention**
- **OPSWAT MetaDefender API Integration**
  - Scans all uploaded content (text, images, documents) before QR code generation
  - Uses multi-engine scanning with 30+ antivirus engines
  - Detects malware, viruses, trojans, and suspicious patterns
  - Blocks malicious content from entering the system
  - Session-based caching to prevent redundant scans

### **Layer 2: Data Encryption**
- **Double-Layer Encryption Architecture**
  - **User Key Layer**: Derived from user credentials (email + phrase + timestamp) using Argon2id
  - **Per-QR Key Layer**: Unique 256-bit key for each QR code, encrypted with user key
  - **Content Encryption**: QR content encrypted with ChaCha20-Poly1305 using per-QR key
  - **Key Isolation**: Each QR code's encryption key is independent, limiting breach impact

### **Layer 3: Access Control & Authentication**
- **Tri-Layer Verification Process**:
  1. **Token Validation**: QR token must exist in database and be valid
  2. **Password Authentication**: Optional password protection with BCrypt hashing
  3. **One-Time Passcode (OTP)**: Time-limited (50-60 seconds) 6-digit codes sent via email
- **Scan Status Control**:
  - `scan_status = 0`: Open access (no password required)
  - `scan_status = 1`: Protected access (password or OTP required)

### **Layer 4: Permission Management**
- **Two Access Permission Levels**:
  - **Level 1 (View Only)**: 
    - PDFs: Inline viewing only (no download)
    - Images: Display only (no download)
    - URLs: Proxy viewer (URL hidden from user)
  - **Level 2 (Full Access)**:
    - PDFs: Download allowed
    - Images: Download allowed
    - URLs: Direct redirect (full access)

### **Layer 5: Secure Sharing & Verification**
- **Email Verification System**:
  - Recipients must be verified by QR code owner before OTP access
  - Email addresses stored in `code` table with status flags
  - Prevents unauthorized OTP requests
- **Friend Network Sharing**:
  - Share QR codes with verified friends within the platform
  - Notification system alerts recipients
  - Activity logging for all sharing operations

### **Layer 6: Access Logging & Audit Trail**
- **Comprehensive Access Records**:
  - Logs every access attempt with timestamp
  - Records access type (Password, OTP, or No Password)
  - Captures IP addresses for security monitoring
  - Tracks QR code ID and user email
- **Activity Tracking**:
  - User activity logs for QR generation, sharing, and access
  - Timestamp-based audit trail
  - User-specific activity history

### **Layer 7: Session Security**
- **Secure Session Management**:
  - Session-based token caching for malware scan results
  - OTP codes stored in session with expiration timestamps
  - Session cleanup after access completion
  - Protection against session fixation attacks

### **Layer 8: Input Validation & SQL Injection Prevention**
- **Prepared Statements**: All database queries use parameterized statements
- **Input Sanitization**: Email validation, token validation, and data type checking
- **XSS Prevention**: HTML entity encoding for user-generated content
- **File Upload Security**: MIME type validation and file size limits

### **Layer 9: Database Security**
- **Encrypted Storage**: All sensitive content stored in encrypted form
- **Password Hashing**: Passwords never stored in plain text
- **Foreign Key Constraints**: Maintains referential integrity
- **Access Control**: User-specific data isolation

---

## ✨ Key Features

### 1. **Malware Scanning Integration**
- Automatic scanning of all uploaded content via OPSWAT MetaDefender API
- Multi-engine threat detection (30+ antivirus engines)
- Real-time malware detection before QR code generation
- Session-based caching to optimize performance

### 2. **Advanced Encryption Mechanism**
- ChaCha20-Poly1305 authenticated encryption
- Argon2id key derivation for user-specific keys
- Unique cryptographic parameters (salt, nonce, key) for each QR code
- Double-layer encryption architecture

### 3. **Flexible Access Control**
- Optional password protection
- One-Time Passcode (OTP) system with email delivery
- Time-limited OTP codes (50-60 seconds validity)
- Open access option for public QR codes

### 4. **Granular Permission Management**
- View-only mode for sensitive content
- Full access mode for trusted recipients
- Content-type specific handling (PDF, images, URLs, text)
- Proxy viewer for URL protection

### 5. **Secure Content Sharing**
- Email-based sharing with QR code attachments
- Friend network sharing within platform
- Email verification system for OTP access
- Notification system for shared QR codes

### 6. **Customizable QR Design**
- Color customization (foreground and background)
- Real-time preview of QR code design
- Design validation to ensure scannability
- Save design preferences

### 7. **Comprehensive Activity Tracking**
- User activity logs (generation, sharing, access)
- Access record logging with IP addresses
- Timestamp-based audit trail
- Activity dashboard for users

### 8. **User-Friendly Interface**
- Intuitive design with step-by-step guidance
- Real-time QR code preview
- Responsive design for mobile and desktop
- Animated feedback and loading indicators

### 9. **QR Code Management**
- Dashboard to view all generated QR codes
- Shared QR codes section
- QR code metadata (title, description, creation date)
- Quick access to scan and manage QR codes

### 10. **Security Monitoring**
- Access attempt logging
- IP address tracking
- Failed authentication tracking
- Security alerts for suspicious activity

---

## 📖 Usage Procedure

### **For QR Code Creators:**

#### Step 1: Account Setup
1. Register a new account or log in to existing account
2. Complete profile setup with email and security phrase
3. Verify email address (if required)

#### Step 2: Generate QR Code
1. Navigate to the QR Generator page (`generateqr.php`)
2. Upload or enter content (text, URL, image, PDF, etc.)
3. **Optional**: Customize QR code design
   - Select foreground color (darker recommended)
   - Select background color (lighter recommended)
   - Click "Save" to preview changes
4. Enter QR code metadata:
   - **Title** (Required): Brief title for the QR code
   - **Description** (Optional): Additional notes or context
5. Set security options:
   - **Password** (Optional): 
     - Leave blank for open access
     - Enter password (min 8 chars, uppercase, lowercase, number, symbol) for protected access
   - **Access Permission**:
     - **Level 1**: View only (recommended for sensitive content)
     - **Level 2**: Full access with download/edit capabilities
6. Click "Secure QR" to generate

#### Step 3: Review & Share
1. Review generated QR code in preview page
2. Download QR code image
3. **Optional**: Share QR code
   - **Via Email**: Enter recipient email, verify recipient (for OTP access)
   - **Via Friends**: Select from verified friend list
4. QR code is automatically saved to your dashboard

### **For QR Code Scanners/Recipients:**

#### Step 1: Receive QR Code
- Receive QR code via email attachment or friend sharing
- Or access from shared QR codes section in dashboard

#### Step 2: Scan QR Code
1. Navigate to Scan page (`scan.php`)
2. **Option A**: Upload QR code image file
3. **Option B**: Select from your QR codes table (click on QR code row)
4. System automatically decodes QR token

#### Step 3: Malware Scan (Automatic)
- System automatically scans QR token for malware
- If malicious content detected, access is blocked
- Safe content proceeds to access verification

#### Step 4: Access Verification
**For Open QR Codes (No Password):**
- Click "Access QR" button
- Content is immediately displayed

**For Protected QR Codes:**
- **Option A: Password Access**
  - Enter the password provided by QR code owner
  - Click "Access"
  
- **Option B: OTP Access**
  1. Click "Click Here for Request One Time Passcode"
  2. Enter your email address (must be verified by QR owner)
  3. Click "Send OTP"
  4. Check your email for 6-digit OTP code
  5. Enter OTP code (valid for 50-60 seconds)
  6. Click "Access"

#### Step 5: View Content
- Content is decrypted and displayed based on permission level:
  - **View Only**: Content displayed without download option
  - **Full Access**: Content can be downloaded or directly accessed

---

## 💡 Why Use This System?

### **1. Addresses Real-World Security Threats**
- **QR Code Fraud Prevention**: Protects against malicious QR codes that redirect to phishing sites or download malware
- **Data Breach Protection**: Even if database is compromised, encrypted content remains secure
- **Unauthorized Access Prevention**: Multi-layer authentication ensures only authorized users access content

### **2. Enterprise-Grade Security**
- **Military-Grade Encryption**: ChaCha20-Poly1305 provides 256-bit encryption strength
- **Industry-Standard Algorithms**: Uses algorithms recommended by security experts (Argon2id, ChaCha20)
- **No Third-Party Dependencies**: Self-hosted solution eliminates external service risks
- **Comprehensive Audit Trail**: Complete logging for compliance and security monitoring

### **3. Privacy & Data Protection**
- **Zero-Knowledge Architecture**: System cannot decrypt content without user credentials
- **User-Controlled Keys**: Encryption keys derived from user-specific data
- **No Data Leakage**: Content never exposed in URLs or logs
- **GDPR Compliant**: Secure data handling and user control

### **4. Flexibility & Control**
- **Customizable Security Levels**: Choose between open access or password protection
- **Granular Permissions**: Control whether recipients can download or only view
- **Flexible Sharing**: Share via email or friend networks
- **Design Customization**: Brand QR codes with custom colors

### **5. User Experience**
- **Simple Workflow**: Intuitive interface for both creators and scanners
- **Real-Time Feedback**: Immediate malware scan results and access status
- **Mobile Friendly**: Responsive design works on all devices
- **Fast Performance**: Optimized encryption and scanning processes

### **6. Business & Professional Use Cases**
- **Secure Document Sharing**: Share sensitive documents (contracts, reports) via QR codes
- **Event Management**: Distribute event materials with controlled access
- **Educational Content**: Share course materials with view-only permissions
- **Healthcare**: Securely share patient information or medical records
- **Financial Services**: Distribute financial documents with audit trails

### **7. Cost-Effective Solution**
- **Self-Hosted**: No subscription fees or per-use charges
- **Open Source Foundation**: Built on open-source technologies
- **Scalable**: Can handle enterprise-level usage
- **Maintenance Control**: Full control over system updates and security patches

### **8. Compliance & Audit Requirements**
- **Access Logging**: Complete audit trail for compliance requirements
- **IP Tracking**: Monitor access from different locations
- **Activity Reports**: Generate reports for security audits
- **Data Retention Control**: Full control over data storage and retention

### **9. Protection Against Common Attacks**
- **SQL Injection**: Prevented through prepared statements
- **XSS Attacks**: Input sanitization and output encoding
- **Brute Force**: Argon2id makes password cracking computationally expensive
- **Man-in-the-Middle**: Encrypted content prevents interception
- **Replay Attacks**: Nonces ensure each encryption is unique

### **10. Trust & Reliability**
- **Transparent Security**: Open-source algorithms with proven security
- **No Vendor Lock-in**: Self-hosted solution with full data control
- **Community Trust**: Built following industry best practices
- **Continuous Improvement**: SDLC methodology ensures ongoing security updates

---

## 🔧 Technical Specifications

### **Technology Stack**
- **Backend**: PHP 8.0+
- **Database**: MySQL/MariaDB
- **Cryptography**: libsodium (ChaCha20-Poly1305, Argon2id)
- **QR Code Generation**: PHP QR Code library
- **Email**: PHPMailer with SMTP
- **Malware Scanning**: OPSWAT MetaDefender API
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla + Tailwind CSS)

### **System Requirements**
- PHP 8.0 or higher
- MySQL 5.7+ or MariaDB 10.3+
- libsodium extension enabled
- cURL extension for API calls
- GD or Imagick extension for image processing
- SMTP server access for email functionality

### **Database Schema**
- `users`: User accounts and credentials
- `qr_security`: QR code metadata and password hashes
- `qr_secondlayer`: Encrypted content and cryptographic parameters
- `qr_shares`: QR code sharing records
- `code`: Email verification and OTP access records
- `accessrecord`: Access logs and audit trail
- `activity`: User activity logs
- `notification`: System notifications

### **Security Parameters**
- **Encryption Key Size**: 256 bits (32 bytes)
- **Salt Size**: 128 bits (16 bytes)
- **Nonce Size**: 96 bits (12 bytes)
- **Argon2id Parameters**: Moderate operations and memory limits
- **OTP Validity**: 50-60 seconds
- **Password Requirements**: Min 8 characters, uppercase, lowercase, number, symbol

---

## 📝 License & Credits

This project was developed as a Final Year Project to address QR code security vulnerabilities in modern digital systems.

**Developed by**: SQ-Tech Solver Team  
**Year**: 2025  
**Purpose**: Academic Research & Security Solution

---

## 🔗 Additional Resources

For technical documentation, API references, or security advisories, please refer to:
- libsodium documentation: https://libsodium.gitbook.io/
- OPSWAT MetaDefender API: https://docs.metadefender.com/
- ChaCha20-Poly1305 RFC: https://tools.ietf.org/html/rfc8439
- Argon2 specification: https://github.com/P-H-C/phc-winner-argon2

---

**⚠️ Security Notice**: This system is designed for secure data handling. Always keep your passwords and security phrases confidential. Never share your encryption keys or OTP codes with unauthorized parties.

## PART 1 (Explanation)

[![Watch the video](https://img.youtube.com/vi/mbXsyDUpzS0/0.jpg)](https://youtu.be/mbXsyDUpzS0)

## PART 2 (System Demo)

[![Watch on YouTube](https://img.youtube.com/vi/AAsuHRUb18s/0.jpg)](https://youtu.be/AAsuHRUb18s)
